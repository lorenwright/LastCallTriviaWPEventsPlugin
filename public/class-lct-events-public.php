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

		wp_enqueue_style( 'tribe_pro_styles', plugins_url() . '/events-calendar-pro/src/resources/css/views-full.min.css');
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

		add_action('wp_footer', function () { ?>

			<style>
				.tribe-venue-map-container {
					display: flex;
					flex-wrap: wrap;
					width: 100%;
					max-width: 100%;
				}

				#tribe-venue-map {
					width: 100%;
					height: 720px;
				}

				#tribe-venue-map .tribe-events-pro-map__event-tooltip {
					padding: 8px;
					width: 200px;
				}

				.tribe-venue-map-events {
					padding: 16px;
				}

				.map-key {
					display: flex;
					justify-content: space-around;
				}

				.key-group {
					display: flex;
					padding: 0 1rem 0 0;
				}

				.key-group .square {
					width: 24px;
					height: 24px;
					background: grey;
					margin-right: 8px;
					border-radius: 3px;
				}

				.square.blue {
					background: #00558c;
				}

				.square.orange {
					background: #c05131;
				}

				.square.purple {
					background: #72246c;
				}

				.square.teal {
					background: #004f59;
				}

				.filter-bar {
					display: flex
				}

				.filter-bar .control {
					margin-right: 1rem;
				}

				.filter-bar .control select {
					padding: 0rem 1rem;
					border: none;
					box-shadow: 0 0 4px rgb(0 0 0 / 30%);
					height: 35px;
					line-height: 35px;
					display: block;
					box-sizing: border-box;
				}

				.filter-bar .control .button {
					padding: 0 1rem;
					border: none;
					box-shadow: 0 0 4px rgb(0 0 0 / 30%);
					text-decoration: none;
					font-size: 1rem;
					height: 35px;
					line-height: 35px;
					display: block;
					box-sizing: border-box;
				}

				.tribe-events-pro-map__event-tooltip .lct-pill {
					margin-right: 1rem;
					margin-top: .5rem;
				}
			</style>

			<script>
			function venue_map() {
				var map = new google.maps.Map(document.getElementById('tribe-venue-map'), {
					center: {lat: 39.8097343, lng: -98.5556199},
					zoom: 4,
					streetViewControl: false
				});

				var markerBlue = '<?php echo WP_PLUGIN_URL . '/lct-events/public/images/map-marker-blue.png'; ?>';
				var markerOrange = '<?php echo WP_PLUGIN_URL . '/lct-events/public/images/map-marker-orange.png'; ?>';
				var markerPurple = '<?php echo WP_PLUGIN_URL . '/lct-events/public/images/map-marker-purple.png'; ?>';
				var markerTeal = '<?php echo WP_PLUGIN_URL . '/lct-events/public/images/map-marker-teal.png'; ?>';

				<?php

				if (isset($_GET['game'])) {
					$args = [
						'post_type' => Tribe__Events__Main::VENUE_POST_TYPE,
						'posts_per_page' => -1,
						'meta_key' => 'VENUE_GAME_TYPE',
						'meta_value' => $_GET['game'],
					];
				} else {
					$args = [
						'post_type' => Tribe__Events__Main::VENUE_POST_TYPE,
						'posts_per_page' => -1,
					];
				}

				$venues = get_posts( $args );

				foreach ( $venues as $venue ) {

					$game_type = get_post_meta($venue->ID, 'VENUE_GAME_TYPE', true);

					if ($game_type === 'trivia' || $game_type === 'theme_trivia') { ?>
						var icon = markerBlue
					<?php } else if ($game_type === 'feud') { ?>
						var icon = markerOrange
					<?php } else if ($game_type === 'bingo' || $game_type === 'one_hour_bingo' || $game_type === 'two_hour_bingo') { ?>
						var icon = markerPurple
					<?php } else { ?>
						var icon = markerTeal
					<?php }

					// Get first event for venue
					$event_args = [
						'post_type' => Tribe__Events__Main::POSTTYPE,
						'posts_per_page' => 1,
						'meta_key' => '_EventVenueID',
						'meta_value' => $venue->ID,
					];

					$first_event = get_posts($event_args);
					$additional_fields = [];
					if ( $first_event ) {
						$additional_fields = tribe_get_custom_fields($first_event[0]->ID);
						$event = tribe_get_event($first_event[0]);
						$start_date = $event->start_date;
						$start_timestamp = strtotime($start_date);
						$day_of_week = date('l', $start_timestamp);

						$start_time = tribe_get_start_time($first_event[0]->ID);
					}

					$coordinates = tribe_get_coordinates ( $venue->ID );
					
					if ( $coordinates['lat'] != 0 && $coordinates['lng'] != 0 ) { ?>

						window['marker_' + <?php echo $venue->ID; ?>] = new google.maps.Marker({
							position: {lat: <?php echo $coordinates['lat']; ?>, lng: <?php echo $coordinates['lng']; ?>},
							map: map,
							title: "<?php echo $venue->post_title; ?>",
							icon: {
								url: icon,
								scaledSize: new google.maps.Size(40, 40)
							}
						});

						var content = '<div class="tribe-events-pro-map__event-tooltip tribe-events-pro-map__event-tooltip--has-slider">'
						content += '<div class="tribe-events-pro-map__event-tooltip-datetime-wrapper tribe-common-b2 tribe-common-b3--min-medium">'
						content += '<?php echo $day_of_week; ?>s @ <?php echo $start_time; ?>'
						content += '</div>'
						content += '<h3 class="tribe-events-pro-map__event-tooltip-title tribe-common-h7">'
						content += '<a class="tribe-events-pro-map__event-tooltip-title-link tribe-common-anchor-thin" href="<?php echo the_permalink($venue->ID); ?>"><?php echo htmlspecialchars($venue->post_title, ENT_QUOTES); ?></a>'
						content += '</h3>'
						content += '<address class="tribe-events-pro-map__event-tooltip-venue tribe-common-b2 tribe-common-b3--min-medium" style="margin-top: 8px">'
						content += '<span class="tribe-events-pro-map__event-tooltip-venue-address">'
						content += '<?php echo tribe_get_venue_single_line_address($venue->ID); ?>'
						content += '</span>'
						content += '</address>'
						content += '<?php echo isset($additional_fields['Game']) ? '<div class="lct-pill ' . strtolower($additional_fields['Game']) . '">' . $additional_fields['Game'] . '</div>' : ''; ?>'
						content += '<?php echo isset($additional_fields['Region']) ? '<div class="lct-pill">' . $additional_fields['Region'] . '</div>' : ''; ?>'
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

		$venues = get_posts( array( 'post_type' => Tribe__Events__Main::VENUE_POST_TYPE, 'posts_per_page' => -1) );
		$game_type_options = [];
		foreach ( $venues as $venue ) {
			$game_type = get_post_meta($venue->ID, 'VENUE_GAME_TYPE', true);
			if ($game_type) {
				$game_type_options[] = $game_type;
			}
		}
		$unique_game_types = array_unique($game_type_options);

		$game_lookup = [
			'trivia' => 'Trivia',
			'theme_trivia' => 'Theme Trivia',
			'feud' => 'Feud',
			'bingo' => 'Bingo',
			'one_hour_bingo' => 'One Hour Bingo',
			'two_hour_bingo' => 'Two Hour Bingo',
		];

		$html = '<form class="filter-bar" method="GET">';
			$html .= '<div class="control">';
				$html .= '<select name="game" onchange="this.form.submit()">';
					if (!isset($_GET['game'])) {
						$none_selected = 'selected';
					} else {
						$none_selected = '';
					}
					$html .= '<option value="" disabled ' . $none_selected . '>Game</option>';
					foreach ($unique_game_types as $game_type) {
						if (isset($_GET['game'])) {
							$selected = $_GET['game'] === $game_type ? 'selected' : '';
						} else {
							$selected = '';
						}
						$html .= '<option value="' . $game_type . '" ' . $selected . '>' . $game_lookup[$game_type] . '</option>';
					}
				$html .= '</select>';
			$html .= '</div>';
			$html .= '<div class="control">';
				$html .= '<a class="button" href="' . strtok($_SERVER["REQUEST_URI"], '?') . '">Clear Filters</a>';
			$html .= '</div>';
		$html .= '</form>';
		$html .= '<div class="map-key">';
			$html .= '<div class="key-group"><div class="square blue"></div><span>Trivia</span></div>';
			$html .= '<div class="key-group"><div class="square orange"></div><span>Feud</span></div>';
			$html .= '<div class="key-group"><div class="square purple"></div><span>Bingo</span></div>';
			$html .= '<div class="key-group"><div class="square teal"></div><span>Other</span></div>';
		$html .= '</div>';
		$html .= '<div class="tribe-venue-map-container tribe-common tribe-events-pro"><div id="tribe-venue-map"></div></div>';

		return $html;
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
