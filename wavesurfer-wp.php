<?php

/**
 * @package Wavesurfer
 * @version 1.0
 */

/**
 * Plugin Name: WaveSurfer-WP
 * Plugin URI: http://www.extremraym.com/
 * Description: HTML5 Audio controler with waveform preview (mixed or split channels), using WordPress native audio shortcode.
 * Author: X-Raym
 * Version: 1.0
 * Author URI: http://www.extremraym.com/
 * License: GNU AGPLv3
 * License URI: http://www.gnu.org/licenses/agpl-3.0.html
 * Date: 2015-10-28
 * Text Domain: wavesurfer
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) exit ( 'restricted access' );

/**
 * Our main plugin instantiation class
 *
 * This contains important things that our relevant to
 * our add-on running correctly. Things like registering
 * custom post types, taxonomies, posts-to-posts
 * relationships, and the like.
 *
 * @since 1.0.0
 */
class WaveSurfer {

	//public static $load_front_ressources = false;

	/**
	 * Get everything running.
	 *
	 * @since 1.0.0
	 **/
	public function __construct() {

		// Define plugin constants
		$this->basename			 = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url	= plugins_url( dirname( $this->basename ) );

		// Load translations
		load_plugin_textdomain( 'wavesurfer', false, dirname( $this->basename ) . '/languages' );

		// Run our activation and deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// Include our other plugin files
		add_action( 'init', array( $this, 'includes' ) );

	} /* __construct() */


	/**
	 * Activation hook for the plugin.
	 *
	 * @since 1.0.0
	 **/
	public function activate() {
		// Do some activation things
		if ( false === get_option('wavesurfer_settings') ) {
			$arg = array(
				'wave_color'	 			=> '#EE82EE',
				'progress_color'		=> '#800080',
				'front_theme'		=> 'wavesurfer_default'
			);
		update_option( 'wavesurfer_settings', $arg, '', 'yes' );
		}
	} /* activate() */


	/**
	 * Include our plugin dependencies
	 *
	 * @since 1.0.0
	 **/
	public function includes() {

			// Add Menu
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_links' ) );

			// Construct Page
			add_action( 'admin_menu', array( $this, 'add_admin_pages' ), 999 );

			// Register Settings
			add_action( 'admin_init', array( $this, 'wavesurfer_settings_init' ) );

			// Add Color Pickers Scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'load_color_picker' ) );

			//
			if ( !is_admin() ) {
				add_filter( 'wp_audio_shortcode_override' , array( $this, 'wp_audio_shortcode_override' ), 10, 2 );
			}

			// Load Front End Ressources
			add_action( 'wp_enqueue_scripts',  array( $this, 'wavesurfer_register_ressources' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wavesurfer_load_front_ressources' ) );


	} /* includes() */


	/**
	 * Register Scripts and Styles in FrondEnd
	 */
	public function wavesurfer_register_ressources() {

		if ( !is_admin() ) {

			wp_register_script( 'wavesurfer', plugin_dir_url( __FILE__ ) . '/js/wavesurfer.min.js', array( 'jquery' ), '1.8.0', true );
			wp_register_script('wavesurfer_scripts', plugin_dir_url( __FILE__ ) . '/js/wavesurfer-wp.js', array( 'jquery' ), '1.8.0', true );

			wp_register_style( 'wavesurfer_default', plugin_dir_url( __FILE__ ) . '/css/wavesurfer-wp_default.css' );
		}
	}

	/**
	 * load scripts in Front End
	 */
	public function wavesurfer_load_front_ressources() {
		if ( !is_admin() ) {
			//wp_enqueue_script('jquery');
	  	wp_enqueue_script( 'wavesurfer' );
	   	wp_enqueue_script( 'wavesurfer_scripts' );

			//wp_enqueue_style( 'dashicons' );

			$options = get_option( 'wavesurfer_settings' );
			if ( isset( $options['front_theme'] ) ) {
				if ( $options['front_theme'] !== 'wavesurfer_none' )
					wp_enqueue_style( $options['front_theme'] );
			} else {
				wp_enqueue_style( 'wavesurfer_default' );
			}
		}
	}

	/**
	 * Load color picker scripts for Admin settings page
	 */
	public function load_color_picker( $hook ) {
  	// first check that $hook_suffix is appropriate for your admin page
		if ( 'settings_page_wavesurfer-wp' != $hook ) {
		 return;
		}
  	wp_enqueue_style( 'wp-color-picker' );
  	wp_enqueue_script( 'my-script-handle', plugin_dir_url( __FILE__ ) . 'js/admin-color-picker.js', array( 'wp-color-picker' ), false, true );
	}


	/**
	 * Link on plugin page
	 *
	 * @since 1.0
	 **/
	public function add_action_links ( $links ) {
		$mylinks = array(
			'<a href="' . admin_url('options-general.php?page=wavesurfer-wp.php' ) . '">' . __( 'Settings', 'wavesurfer' ) . '</a>',
		);
		return array_merge( $links, $mylinks );
	} /* add_action_links() */


	/**
	 * Add administration menus
	 *
	 * @since 1.0
	 **/
	public function add_admin_pages() {

		add_options_page(
			'WaveSurfer-WP',
			'WaveSurfer-WP',
			'manage_options',
			'wavesurfer-wp.php',
			array( $this, 'users_page' )
		);

	}

	/**
	 * Content of the settings page
	 *
	 * @since 1.0
	 **/
	public function wavesurfer_settings_init(	) {

			// Register Settings
			register_setting(
					'wavesurfer', // Option group
					'wavesurfer_settings' // Option name
			);


			// Register Section
			add_settings_section(
					'colors_section', // Id
					__( 'Appearance', 'wavesurfer' ), // Title
					array( $this, 'render_colors_section' ), // Callback
					'wavesurfer' // Page
			);


			// Add Fields
			// Wave Color
			add_settings_field( // 0
					'wave_color', // Id
					__( 'Wave Color', 'wavesurfer' ), // Title
					array( $this, 'render_wave_color_field' ), // Callback
					'wavesurfer', // Page
					'colors_section' // Section
			);

			// Progress Color
			add_settings_field( // 1
					'progress_color',
					__( 'Progress Color', 'wavesurfer' ),
					array( $this, 'render_progress_color_field' ),
					'wavesurfer',
					'colors_section'
			);

			// Progress Color
			add_settings_field( // 1
					'front_theme',
					__( 'Front Theme', 'wavesurfer' ),
					array( $this, 'render_theme_field' ),
					'wavesurfer',
					'colors_section'
			);

	}

	/**
	 * Echo form sections descriptions.
	 *
	 * @since 1.0
	 **/
	public function render_colors_section(	) { // 0
			echo __( 'Global style of the wavesurfer visualization and buttons control.', 'wavesurfer' );
	}

	/**
	 * Render form fields
	 *
	 * @since 1.0
	 **/
	public function render_wave_color_field(	) { // 0

			$options = get_option( 'wavesurfer_settings' );
			$val = ( isset( $options['wave_color'] ) ) ? $options['wave_color'] : '';

			echo '<input type="text" name="wavesurfer_settings[wave_color]" value="' . $val .'" class="my-color-field" >';
			echo '<p>' . __( 'This setting can be locally overridden with the <code>wave_color="#123456"</code> [audio] shortcode attribute', 'wavesurfer' ) .'.</p>';

	}


	public function render_progress_color_field(	) { // 1

		$options = get_option( 'wavesurfer_settings' );
		$val = ( isset( $options['progress_color'] ) ) ? $options['progress_color'] : '';

		echo '<input type="text" name="wavesurfer_settings[progress_color]" value="' . $val .'" class="my-color-field" >';
		echo '<p>' . __( 'This setting can be locally overridden with the <code>progress_color="#123456"</code> [audio] shortcode attribute', 'wavesurfer' ) .'.</p>';

	}

	public function render_theme_field(	) { // 2

		$options = get_option( 'wavesurfer_settings' );
		//$val = ( isset( $options['front_theme'] ) ) ? $options['front_theme'] : '';

		?>
		<select name='wavesurfer_settings[front_theme]'>
			<option value='wavesurfer_default' <?php selected( $options['front_theme'], 'wavesurfer_default' ); ?>><?php _e('Default', 'wavesurfer'); ?></option>
			<option value='wavesurfer_none' <?php selected( $options['front_theme'], 'wavesurfer_none' ); ?>><?php _e('None', 'wavesurfer'); ?></option>
		</select>
		<p><?php _e( 'Style of the buttons. Default theme requires Font-Awesome 1.0.', 'wavesurfer' ) ?></p>
		<?php

	}


	/**
	 * Content of the settings page
	 *
	 * @since 1.0.0
	 **/
	public function users_page() {

		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'wavesurfer' ) );

?>

<div class="wrap wavesurfer">

	<!-- HEADER -->
	<!-- Header: Title	-->
	<h1><?php _e( 'WaveSurfer-WP', 'wavesurfer' ); ?></h1>

	<!-- Header: Infos	-->
	<p><?php _e( 'A WordPress Integration of <a href="https://github.com/katspaugh">katspaugh</a>\'s <a href="http://wavesurfer-js.org/">wavesurfer.js</a> by <a href="http://extremraym.com" target="_blank">X-Raym</a>.', 'wavesurfer' ); ?></p>

	<h2>
		<?php _e('Settings', 'wavesurfer'); ?>
	</h2>

	<div id="tab_container">

	<form method="post" action="options.php"> <!-- options.php is important -->
		<?php

			settings_fields( 'wavesurfer' );
			do_settings_sections( 'wavesurfer' );

			submit_button();

		?>
	</form>
</div>
</div>
<?php
	}

	/**
	 * Shortcode output
	 */
	public function wp_audio_shortcode_override( $html, $attr ) {

			//self::$load_front_ressources = true;
			$html = ''; // Value for not overring render

			// Check if shortcode render must be override or not
			if ( isset( $attr['player'] ) ) {
				if ( $attr['player'] === 'default' )
					return $html;
			}

			// Check audio type to determine the link
			$options = get_option( 'wavesurfer_settings' );
			if ( isset( $attr['wav'] ) ) { $link = $attr['wav']; }
			if ( isset( $attr['mp3'] ) ) { $link = $attr['mp3']; }
			if ( isset( $attr['m4a'] ) ) { $link = $attr['m4a']; }
			if ( isset( $attr['ogg'] ) ) { $link = $attr['ogg']; }

			// Begin render
			$html .= '<div class="wavesurfer-block">';
			$html .= '<wavesurfer ';

			// Split channels
			if ( isset( $attr['split_channels'] ) ) {
				if( $attr['split_channels'] == true )
				$html .= 'data-split-channels="true" ';
			}

			// Wave color
			if ( isset( $attr['wave_color'] ) ) {
				$wave_color = esc_attr( $attr['wave_color'] );
			} else {
				$wave_color = ( isset( $options['wave_color'] ) ) ? $options['wave_color'] : 'violet'; // Get color value from Settings
			}
			$html .= 'data-wave-color="' . $wave_color . '" ';

			// Progress color
			if ( isset( $attr['progress_color'] ) ) {
				$progress_color = esc_attr( $attr['progress_color'] );
			} else {
				$progress_color = ( isset( $options['progress_color'] ) ) ? $options['progress_color'] : 'purple'; // Get color value from Settings
			}
			$html .= 'data-progress-color="' . $progress_color . '" ';

			// Buttons
			$html .= 'data-url="' . $link . '"';
			$html .= '></wavesurfer>';
			$html .= '<div class="wavesurfer-buttons_set">';
			$html .= '<button type="button" class="wavesurfer-play"><span>' . __('Play', 'wavesurfer') . '</span></button>';
			$html .= '<button type="button" class="wavesurfer-pause"><span>' . __('Pause', 'wavesurfer') . '</span></button>';
			$html .= '<button type="button" class="wavesurfer-stop"><span>' . __('Stop', 'wavesurfer') . '</span></button>';
			$html .= '<div class="wavesurfer-time"></div>';
			$html .= '<div class="wavesurfer-duration"></div>';
			$html .= '</div>';
			$html .= '</div>';

			// Output
			return $html;

	}


} // End class declaration

// Create Object
new WaveSurfer();
