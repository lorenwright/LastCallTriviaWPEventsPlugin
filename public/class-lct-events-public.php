<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://lorenwright.io
 * @since      1.0.0
 *
 * @package    Lct_Events
 * @subpackage Lct_Events/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Lct_Events
 * @subpackage Lct_Events/public
 * @author     Loren Wright <loren.wright.dev@gmail.com>
 */
class Lct_Events_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lct-events-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lct-events-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the custom shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'lct_events_venue_map', array( $this, 'lct_events_venue_map_shortcode') );
	  }

	/**
	 * Register the [lct_events_venue_map] shortcode
	 *
	 * @since    1.0.0
	 */
	public function lct_events_venue_map_shortcode( $atts ) {
		$url = apply_filters( 'tribe_events_google_maps_api', 'https://maps.google.com/maps/api/js' );
		$url = $url . '&callback=venue_map';

		wp_enqueue_script( 'lct_events_google_maps_api', $url, array(), false, true );
		wp_enqueue_script( 'jquery' );

		var_dump(tribe_events_template_var('map_provider', 'test'));

		add_action('wp_footer', function () { ?>

			<style>
				.tribe-venue-map-container {
					display: flex;
					flex-wrap: wrap;
					width: 100%;
					max-width: 100%;
				}

				#tribe-venue-map {
					width: 60%;
					height: 720px;
				}

				#tribe-venue-map .tribe-events-pro-map__event-tooltip {
					padding: 8px;
					width: 200px;
				}

				.tribe-venue-map-events {
					padding: 16px;
				}
			</style>

			<script>
			function venue_map() {
				var map = new google.maps.Map(document.getElementById('tribe-venue-map'), {
					center: {lat: 39.8097343, lng: -98.5556199},
					zoom: 4,
					streetViewControl: false
				});

				<?php

				$venues = get_posts( array( 'post_type' => Tribe__Events__Main::VENUE_POST_TYPE, 'posts_per_page' => -1) );

				foreach ( $venues as $venue ) {

					$coordinates = tribe_get_coordinates ( $venue->ID );
					
					if ( $coordinates['lat'] != 0 && $coordinates['lng'] != 0 ) { ?>

						window['marker_' + <?php echo $venue->ID; ?>] = new google.maps.Marker({
						position: {lat: <?php echo $coordinates['lat']; ?>, lng: <?php echo $coordinates['lng']; ?>},
						map: map,
						title: "<?php echo $venue->post_title; ?>"
						});

						var content = '<div class="tribe-events-pro-map__event-tooltip tribe-events-pro-map__event-tooltip--has-slider">'
						// content += '<div class="tribe-events-pro-map__event-tooltip-datetime-wrapper tribe-common-b2 tribe-common-b3--min-medium">'
						// content 
						// content += '</div>'
						content += '<h3 class="tribe-events-pro-map__event-tooltip-title tribe-common-h7">'
						content += '<a class="tribe-events-pro-map__event-tooltip-title-link tribe-common-anchor-thin" href="<?php echo the_permalink($venue->ID); ?>"><?php echo htmlspecialchars($venue->post_title, ENT_QUOTES); ?></a>'
						content += '</h3>'
						content += '<address class="tribe-events-pro-map__event-tooltip-venue tribe-common-b2 tribe-common-b3--min-medium">'
						content += '<span class="tribe-events-pro-map__event-tooltip-venue-address">1234 Main st...</span>'
						content += '</address>'
						content += '</div>'


						window['info_' + <?php echo $venue->ID; ?>] = new google.maps.InfoWindow({
						content: content
						});

						window['marker_' + <?php echo $venue->ID; ?>].addListener('click', function() {
						window['info_' + <?php echo $venue->ID; ?>].open(map, window['marker_' + <?php echo $venue->ID; ?>]);
						});

						<?php
					}
				} ?>
			}

			</script>

		<?php }); // wp_footer

		return '<div class="filter-bar">Do we need filters here?</div><div class="tribe-venue-map-container tribe-common"><div id="tribe-venue-map"></div><div class="tribe-venue-map-events">Events go here. Or do venues go here?</div></div>';
	}

	// function lct_events_modify_pin_markers( array $response_data ) {
	// 	var_dump($response_data);
	// 	die('DIE DIE DIE');
	// 	// No markers? No need to modify
	// 	if ( ! isset( $response_data['markers'] ) ) return $response_data;
	
	// 	// Otherwise, loop through each and add your extra info
	// 	foreach ( $response_data['markers'] as &$marker ) {
	// 		$event_id = $marker['event_id']; // Use this to obtain more information
	// 		$marker['custom'] = 'https://lastcalltrivia.com/wp-content/uploads/2022/03/CIN_CatchAFire.png'; // Add your extra information here
	// 	}
	
	// 	// Return it!
	// 	return $response_data;
	// }

}
