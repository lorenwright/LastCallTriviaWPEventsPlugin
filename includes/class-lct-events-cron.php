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
		$url = 'https://api.jsonbin.io/v3/b/62333170a703bb67492e6cdc/1';
		$key = '$2b$10$YZ7/yh6bC817K6wuc.a0lupxXfof//DyvexXfUy/j1Mp.RbYBSfem';
		$request = wp_remote_get( $url, array(
			'headers' => array(
				'X-Master-Key' => $key
			)
		) );

		if( is_wp_error( $request ) ) {
			return false; // Bail early
		}

		$body = wp_remote_retrieve_body( $request );
		$data = json_decode( $body );

		if( ! empty( $data ) ) {
			foreach ($data->record->events as $event) {
				if ($event->ShouldAddEvent) {
					// see https://docs.theeventscalendar.com/reference/functions/tribe_create_event/ for full arguments
					$new_event = [
						'post_title' => $event->EventName,
						'post_content' => $event->EventDescription,
						'post_status' => 'publish',
						'EventStartDate' => $event->EventStartDate,
						'EventEndDate' => $event->EventEndDate,
						'EventStartHour' => $event->EventStartHour,
						'EventStartMinute' => $event->EventStartMinute,
						'EventStartMeridian' => $event->EventStartMeridian,
						'EventEndHour' => $event->EventEndHour,
						'EventEndMinute' => $event->EventEndMinute,
						'EventEndMeridian' => $event->EventEndMeridian,
						'EventShowMapLink' => $event->EventShowMapLink,
						'EventShowMap' => $event->EventShowMap,
						'EventCost' => $event->EventCost,
						'EventURL' => $event->EventURL,
						'Venue' => [
							'VenueID' => $event->VenueID,
							// 'Venue' => $event->VenueName,
							// 'Address' => $event->VenueAddress,
							// 'City' => $event->VenueCity,
							// 'State' => $event->VenueState,
							// 'Zip' => $event->VenueZip,
							// 'Phone' => $event->VenuePhone
						],
					];
					
					// Insert the post into the database
					$event_id = tribe_create_event( $new_event );

					if ($event->EventImage) {
						$this->generate_featured_image($event->EventImage, $event_id);
					}
				}
			}
		}
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

}