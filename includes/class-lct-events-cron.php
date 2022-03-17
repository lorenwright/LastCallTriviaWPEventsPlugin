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
		$url = 'https://api.jsonbin.io/b/62333170a703bb67492e6cdc/1';  
		$result = wp_remote_get( $url );

		// see https://docs.theeventscalendar.com/reference/functions/tribe_create_event/ for full arguments
		$event = [
			'post_title' => 'My post',
			'post_content' => 'This is my post.',
			'post_status' => 'publish',
			'EventStartDate' => '2022-03-22',
			'EventEndDate' => '2022-03-22',
			'EventStartHour' => '2',
			'EventStartMinute' => '29',
			'EventStartMeridian' => 'pm',
			'EventEndHour' => '4',
			'EventEndMinute' => '20',
			'EventEndMeridian' => 'pm',
			'EventShowMapLink' => true,
			'EventShowMap' => true,
			'EventCost' => '15.00',
			'EventURL' => 'https://google.com',
			'Venue' => [
				'VenueID' => 20
			],
			// 'Organizer' => array(
			// 	'Organizer' => 'Organizer Name',
			// 	'Email' => 'me@me.com'	
			// )
		];
		 
		 // Insert the post into the database
		 $event_id = tribe_create_event( $event );

		 $this->generate_featured_image('https://cdn1.parksmedia.wdprapps.disney.com/resize/mwImage/1/1600/900/75/dam/disneyland/destinations/disneyland/tomorrowland/disneyland-pizza-port-pepperoni-16x9.jpg', $event_id);
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