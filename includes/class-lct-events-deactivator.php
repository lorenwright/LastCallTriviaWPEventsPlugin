<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://lorenwright.io
 * @since      1.0.0
 *
 * @package    Lct_Events
 * @subpackage Lct_Events/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Lct_Events
 * @subpackage Lct_Events/includes
 * @author     Loren Wright <loren.wright.dev@gmail.com>
 */
class Lct_Events_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		register_deactivation_hook (plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lct-events-cron.php', 'lct_events_cron_deactivation');
	}

}
