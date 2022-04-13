<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://lorenwright.io
 * @since      1.0.0
 *
 * @package    Lct_Events
 * @subpackage Lct_Events/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lct_Events
 * @subpackage Lct_Events/admin
 * @author     Loren Wright <loren.wright.dev@gmail.com>
 */
class Lct_Events_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lct_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lct_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lct-events-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lct_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lct_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lct-events-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Enable custom field support for venue posts.
	 * 
	 * @param  array $args
	 * @return array
	 */
	public function lct_events_venues_custom_field_support( $args ) {
		$args['supports'][] = 'custom-fields';
		return $args;
	}

	/**
	 * Enable custom field filtering on admin site.
	 * 
	 * @return void
	 */
	public function lct_events_custom_field_filtering() {
		$type = 'tribe_events'; // change to custom post name.
		if (isset($_GET['tribe_events'])) {
			$type = $_GET['tribe_events'];
		}

		//only add filter to post type `tribe_events`
		if ('tribe_events' == $type){
			//change this to the list of values you want to show
			//in 'label' => 'value' format
			$values = array(
				'FROM_API' => true
			);
			?>
			<select name="FROM_API">
			<option value=""><?php _e('Filter By ', 'lct_events_posts_filter'); ?></option>
			<?php
				$current_v = isset($_GET['FROM_API'])? $_GET['FROM_API']:'';
				foreach ($values as $label => $value) {
					printf
						(
							'<option value="%s"%s>%s</option>',
							$value,
							$value == $current_v? ' selected="selected"':'',
							$label
						);
					}
			?>
			</select>
			<?php
		}
	}

	/**
	 * If submitted filter by post meta
	 * 
	 * @param  array $args
	 * @return array
	 */
	public function lct_events_posts_filter( $query ) {
		global $pagenow;
		$type = 'tribe_events';
		if (isset($_GET['tribe_events'])) {
			$type = $_GET['tribe_events'];
		}
		if ( 'tribe_events' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['FROM_API']) && $_GET['FROM_API'] != '') {
			$query->query_vars['meta_key'] = 'FROM_API';
			$query->query_vars['meta_value'] = $_GET['FROM_API']; 
		}
	}

}
