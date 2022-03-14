<?php

class Lct_Events_Cron {

	/**
	 * Create a scheduled event (if it does not exist already)
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
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
	 * @return    string    The version number of the plugin.
	 */
	function lct_events_cron_deactivation() {
		// find out when the last event was scheduled
		$timestamp = wp_next_scheduled ('lct_daily_cron');
		// unschedule previous event if any
		wp_unschedule_event ($timestamp, 'lct_daily_cron');
	}

}