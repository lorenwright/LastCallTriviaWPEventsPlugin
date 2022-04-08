<?php

class Lct_Events_Cron {

	/**
	 * Create a scheduled event (if it does not exist already)
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	function lct_events_cron_activation() {
		// https://wpguru.co.uk/2014/01/how-to-create-a-cron-job-in-wordpress-teach-your-plugin-to-do-something-automatically/
		if ( !wp_next_scheduled( 'lct_daily_cron' ) ) {  
			wp_schedule_event( time(), 'daily', 'lct_daily_cron' );  
		}
	}

	/**
	 * Deactivate the cron
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	function lct_events_cron_deactivation() {
		// find out when the last event was scheduled
		$timestamp = wp_next_scheduled ('lct_daily_cron');
		// unschedule previous event if any
		wp_unschedule_event ($timestamp, 'lct_daily_cron');
	}

	/**
	 * This is the actual function that gets fired off on a cron
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	function lct_events_cron_run() {
		// Pull events from API
		$url = get_option('lct_events_general_options')['api_endpoint'];
		$request = wp_remote_get( $url );

		if( is_wp_error( $request ) ) {
			return false; // Bail early
		}

		$body = wp_remote_retrieve_body( $request );
		$data = json_decode( $body );

		if( ! empty( $data ) ) {
			// We need to delete all previously added events, so that we can add them in again fresh
			$this->delete_events_added_from_api();
			foreach ($data->results as $venue) {
				// Update or create venue
				$wp_venue_id = $this->update_or_create_venue($venue);

				// Start Adding Events
				$this->add_events_from_api($venue, $wp_venue_id);
			}
		}
	}

	/**
	 * This updates or creates a venue inside of WordPress.
	 *
	 * @since     1.0.0
	 * @param     array	$venue			The Object we want to create the venue with
	 * @return    int	$wp_venue_id	The ID of the WordPress venue
	 */
	function update_or_create_venue( $venue ) {
		$venue_args = [
			'Venue' => $venue->name,
			'Country' => 'United States',
			'Address' => $venue->address,
			'City' => $venue->city,
			'State' => $venue->state,
			'Zip' => $venue->zip,
			'Phone' => $venue->phone,
			'URL' => $venue->website,
		];

		// Find if we already have added the venue to WP
		$query = new WP_Query( array(
			'post_type' => 'tribe_venue',
			'meta_key' => 'VENUE_ID',
			'meta_value' => $venue->id
		) );

		// If we have already added the venue to WP, update that venue
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$wp_venue_id = get_the_ID();
				tribe_update_venue( $wp_venue_id, $venue_args );
			endwhile;
		// Otherwise, create a new venue
		} else {
			$wp_venue_id = tribe_create_venue ( $venue_args );
		}

		// Add the meta key for the VENUE_ID. This is how we relate WP Venues to Symfony Venues
		add_post_meta($wp_venue_id, 'VENUE_ID', $venue->id, true);
		wp_reset_postdata();
		return $wp_venue_id;
	}

	/**
	 * This deletes all events with the meta_key `FROM_API` with a value of `true`
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	function delete_events_added_from_api() {
		$query = new WP_Query( array(
			'post_type' => 'tribe_events',
			'posts_per_page' => -1,
			'meta_key' => 'FROM_API',
			'meta_value' => true
		) );

		foreach ($query->posts as $post) {
			wp_delete_post($post->ID, true);
		}
		wp_reset_postdata();
	}

	/**
	 * This is where we add events from the API
	 *
	 * @since     1.0.0
	 * @param     array	$venue	The venue object to create an event with
	 * @param     int	$wp_venue_id	The WordPress venue to associate the event with
	 * @return    void
	 */
	function add_events_from_api( $venue, $wp_venue_id ) {
		$start_minute = explode(' ', $venue->startTime)[0];
		$start_minute = explode(':', $start_minute)[1];

		$end_minute = explode(' ', $venue->endTime)[0];
		$end_minute = explode(':', $end_minute)[1];

		$game = $this->getOptionDisplay($this->typeOptions, $venue->type);
		$region = ucwords($venue->region);
		$theme = $venue->theme;
		$features = $this->getOptionDisplay($this->featureOptions, $venue->features);


		// see https://docs.theeventscalendar.com/reference/functions/tribe_create_event/ for full arguments
		$new_event = [
			'post_title' => $venue->name,
			'post_content' => $venue->recurrenceRule,
			'post_status' => 'publish',
			'EventStartDate' => date('Y-m-d'), // TODO
			'EventEndDate' => date('Y-m-d'), // TODO
			'EventStartHour' => explode(':', $venue->startTime)[0],
			'EventStartMinute' => $start_minute,
			'EventStartMeridian' => explode(' ', $venue->startTime)[1],
			'EventEndHour' => explode(':', $venue->endTime)[0],
			'EventEndMinute' => $end_minute,
			'EventEndMeridian' => explode(' ', $venue->endTime)[1],
			'EventShowMapLink' => true,
			'EventShowMap' => true,
			// 'EventCost' => $event->EventCost,
			'EventURL' => $venue->website,
			'Venue' => [
				'VenueID' => $wp_venue_id,
			],
			'_ecp_custom_2' => $game, // Game
			'_ecp_custom_3' => $region, // Region
			// '_ecp_custom_4' => 'Thursday', // Day
			'_ecp_custom_5' => $features, // Features
			'_ecp_custom_6' => $theme, // Themes
		];
		
		// Create a new event
		$event_id = tribe_create_event( $new_event );
		add_post_meta($event_id, 'FROM_API', true, true);

		// if ($event->EventImage) {
		// 	$this->generate_featured_image($event->EventImage, $event_id);
		// }
	}

	/**
	* Downloads an image from the specified URL and attaches it to a post as a post thumbnail.
	*
	* @param string $image_url    The URL of the image to download.
	* @param int $post_id	The post ID to associate the image with
	*/
	function generate_featured_image( $image_url, $post_id ){
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents($image_url);
		$filename = basename($image_url);
		if(wp_mkdir_p($upload_dir['path']))
		$file = $upload_dir['path'] . '/' . $filename;
		else
		$file = $upload_dir['basedir'] . '/' . $filename;
		file_put_contents($file, $image_data);

		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
		$res2= set_post_thumbnail( $post_id, $attach_id );
	}

    /**
     * @param  string $optionKey
     * @return string
     */
    public function getOptionDisplay($options, $optionKey)
    {
        if (!isset($options[$optionKey])) {
            return "Unknown option ($optionKey)";
        }

        return $options[$optionKey];
    }

	public $typeOptions = [
        'trivia'           => 'Trivia',
        'feud'             => 'Feud',
        'theme_trivia'     => 'Theme Trivia',
        'private_event'    => 'Private Event',
        'one_hour_bingo'   => '1 Hour Bingo',
        'two_hour_bingo'   => '2 Hour Bingo'
    ];

	public $featureOptions = [
        'new'           => 'New Show',
        'returning'     => 'Returning Show',
        'theme'     	=> 'Theme Show',
    ];

}