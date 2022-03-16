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
		// https://gist.github.com/ethanclevenger91/98f1101ca0da176de5cbb3a08bf3c05a
		$event_id = tribe_create_event( [
			'post_title' => 'Future Event',
			'post_status' => 'publish',
			'EventStartDate' => '2022-03-17',
			'EventsEndDate' => '2022-03-23',
			'EventStartHour' => '05',
			'EventStartMinute' => '00',
			'EventStartMeridian' => 'pm',
			'EventEndHour' => '09',
			'EventEndMinute' => '30',
			'EventEndMeridian' => 'pm',
			'EventShowMapLink' => true,
			'EventShowMap' => true,
			'EventCost' => '15.00',
			'EventURL' => 'https://google.com',

			// see https://docs.theeventscalendar.com/reference/functions/tribe_create_event/ for full arguments
		] );
		
		// $provider = \Tribe__Tickets__Tickets::get_event_ticket_provider();
		
		// $ticket_id = $provider::get_instance()->ticket_add( $event_id, [
		// 	'ticket_name'     => 'Test ticket ' . uniqid(),
		// 	'ticket_provider' => $provider,
		// 	'ticket_price'    => '100',
		// 	'tribe-ticket'    => [
		// 		'mode'           => 'global',
		// 		'event_capacity' => '100',
		// 		'capacity'       => ''
		// 	],
		// 	'ticket_description'      => 'Wine and cheese night',
		// 	'ticket_show_description' => '1',
		// 	'ticket_start_date'       => '',
		// 	'ticket_start_time'       => '',
		// 	'ticket_end_date'         => '',
		// 	'ticket_end_time'         => '',
		// 	'ticket_sku'              => uniqid(),
		// 	'ticket_id'               => '',
		// ] );
		
		
		// // Optionally, you can also add attendee meta when using TEC PRO
		// // You can use an existing template by passing the ID to an existing ticket-meta-fieldset post
		// // $ticket_fields = get_post_meta( 10734, \Tribe__Tickets_Plus__Meta__Fieldset::META_KEY, true );
		
		// // Or you can define your own programatically
		// $ticket_fields = [
		// 	[
		// 		"type" => "text",
		// 		"required" => "on",
		// 		"label" => "First Name",
		// 		"slug" => "first-name",
		// 		"extra" => [],
		// 	],
		// 	[
		// 		"type" => "email",
		// 		"required" => "on",
		// 		"label" => "Email",
		// 		"slug" => "email",
		// 		"extra" => [],
		// 	],
		// 	[
		// 		"type" => "select",
		// 		"required" => "on",
		// 		"label" => "State",
		// 		"slug" => "state",
		// 		"extra" => [
		// 			"options" => [
		// 				"Alabama",
		// 				"Alaska",
		// 				"Arizona",
		// 				// ...
		// 				"Wisconsin",
		// 				"Wyoming",
		// 			],
		// 		],
		// 	],
		// 	[
		// 		"type" => "telephone",
		// 		"required" => "on",
		// 		"label" => "Mobile Phone",
		// 		"slug" => "mobile-phone",
		// 		"extra" => [],
		// 	],
		// ];
		
		// update_post_meta( $ticket_id, \Tribe__Tickets_Plus__Meta::META_KEY, $ticket_fields );
		// update_post_meta( $ticket_id, \Tribe__Tickets_Plus__Meta::ENABLE_META_KEY, 'yes' );
	}

}