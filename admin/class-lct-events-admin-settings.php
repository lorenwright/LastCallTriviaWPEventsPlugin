<?php
/**
 * The settings of the plugin.
 *
 * @link       https://lorenwright.io
 * @since      1.0.0
 *
 * @package    Lct_Events
 * @subpackage Lct_Events/admin
 */
/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Lct_Events_Admin_Settings {
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
   * This function introduces the theme options into the 'Appearance' menu and into a top-level
   * 'WPPB Demo' menu.
   */
  public function setup_plugin_options_menu() {
    //Add the menu to the Plugins set of menu items
    add_menu_page(
      'LCT Events Options',           // The title to be displayed in the browser window for this page.
      'LCT Events Options',          // The text to be displayed for this menu item
      'manage_options',          // Which type of users can see this menu item
      'lct_events_options',      // The unique ID - that is, the slug - for this menu item
      array( $this, 'render_settings_page_content'),      // The name of the function to call when rendering this menu's page
      'dashicons-calendar-alt'
    );
  }
  /**
   * Provides default values for the General Options.
   *
   * @return array
   */
  public function default_general_options() {
    $defaults = array(
      'api_endpoint'    =>  '/api/v1/venues/',
    );
    return $defaults;
  }
  
  /**
   * Renders a simple page to display settings for the menu defined above.
   */
  public function render_settings_page_content( $active_tab = '' ) {
    ?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
      <h2><?php _e( 'LCT Events Options', 'lct-events-plugin' ); ?></h2>
      <?php settings_errors(); ?>
      <?php $active_tab = 'general_options'; ?>
      <h2 class="nav-tab-wrapper">
        <a href="?page=lct_events_options&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Options', 'lct-events-plugin' ); ?></a>
      </h2>
      <form method="post" action="options.php">
        <?php
        if( $active_tab == 'general_options' ) {
          settings_fields( 'lct_events_general_options' );
          do_settings_sections( 'lct_events_general_options' );
        }
        submit_button();
        ?>
      </form>
    </div><!-- /.wrap -->
  <?php
  }

  /**
   * This function provides a simple description for the General Options page.
   *
   * It's called from the 'wppb-demo_initialize_theme_options' function by being passed as a parameter
   * in the add_settings_section function.
   */
  public function general_options_callback() {
    $options = get_option('lct_events_general_options');
  } // end general_options_callback

  /**
   * Initializes the theme's general options page by registering the Sections,
   * Fields, and Settings.
   *
   * This function is registered with the 'admin_init' hook.
   */
  public function initialize_general_options() {
    // If the theme options don't exist, create them.
    if( false == get_option( 'lct_events_general_options' ) ) {
      $default_array = $this->default_general_options();
      add_option( 'lct_events_general_options', $default_array );
    }
    
    add_settings_section(
      'general_settings_section',                  // ID used to identify this section and with which to register options
      __( 'Options', 'lct-events-plugin' ),            // Title to be displayed on the administration page
      array( $this, 'general_options_callback'),      // Callback used to render the description of the section
      'lct_events_general_options'                    // Page on which to add this section of options
    );

    // Next, we'll introduce the fields for toggling the visibility of content elements.
    add_settings_field(
      'api_endpoint',                    // ID used to identify the field throughout the theme
      __( 'API Endpoint', 'lct-events-plugin' ),          // The label to the left of the option interface element
      array( $this, 'api_endpoint_callback'),  // The name of the function responsible for rendering the option interface
      'lct_events_general_options',              // The page on which this option will be displayed
      'general_settings_section',              // The name of the section to which this field belongs
      array(                        // The array of arguments to pass to the callback. In this case, just a description.
        __( 'The API endpoint to hit to gather events', 'lct-events-plugin' ),
      )
    );
    
    // Finally, we register the fields with WordPress
    register_setting(
      'lct_events_general_options',
      'lct_events_general_options',
      array( $this, 'validate_general_options')
    );
  } // end wppb-demo_initialize_theme_options

  public function api_endpoint_callback() {
    $options = get_option( 'lct_events_general_options' );
    // Render the output
    echo '<input type="text" id="api_endpoint" name="lct_events_general_options[api_endpoint]" value="' . $options['api_endpoint'] . '" />';
  } // end input_element_callback
  
 
  public function validate_general_options( $input ) {
    // Create our array for storing the validated options
    $output = array();
    // Loop through each of the incoming options
    foreach( $input as $key => $value ) {
      // Check to see if the current option has a value. If so, process it.
      if( isset( $input[$key] ) ) {
        // Strip all HTML and PHP tags and properly handle quoted strings
        $output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
      } // end if
    } // end foreach
    // Return the array processing any additional functions filtered by this action
    return apply_filters( 'validate_general_options', $output, $input );
  } // end validate_general_options
}