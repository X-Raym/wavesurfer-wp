<?php

/**
 * @package WaveSurfer-WP
 * @version 2.5
 */

/**
 * Plugin Name: WaveSurfer-WP
 * Plugin URI: http://www.extremraym.com/
 * Description: Customizable HTML5 Audio controller with waveform preview (mixed or split channels), using WordPress native audio and playlist shortcode.
 * Author: X-Raym
 * Version: 2.5
 * Author URI: http://www.extremraym.com/
 * License: GNU AGPLv3
 * License URI: http://www.gnu.org/licenses/agpl-3.0.html
 * Date: 2016-10-08
 * Text Domain: wavesurfer-wp
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) exit ( 'restricted access' );

/**
 * Our main plugin instantiation class
 *
 * @since 1.0.0
 */

class WaveSurfer_WP {

	/* Singleton style */
	/* https://code.tutsplus.com/articles/design-patterns-in-wordpress-the-singleton-pattern--wp-31621 */

    /** Refers to a single instance of this class. */
    private static $instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return A single instance of this class.
	 * @since 2.5
	 */
	public static function get_instance() {
	 if ( null == self::$instance ) {
	     self::$instance = new self;
	 }
	 return self::$instance;
	}

	/**
	 * Get everything running.
	 *
	 * @since 1.0.0
	 **/
	private function __construct() {

		// Define plugin constants
		$this->basename			 = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url	= plugins_url( dirname( $this->basename ) );

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

		// Add Options
		if ( false === get_option('wavesurfer_settings') ) {
			$arg = array(
				'wave_color'	 		=> '#EE82EE',
				'progress_color'	=> '#800080',
				'cursor_color'		=> '#333333',
				'front_theme'			=> 'wavesurfer_default',
				'height'					=> '128'
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

		// Load translations
		load_plugin_textdomain( 'wavesurfer-wp', false, dirname( $this->basename ) . '/languages' );

		// Add Menu
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_links' ) );

		// Construct Page
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ), 999 );

		// Register Settings
		add_action( 'admin_init', array( $this, 'wavesurfer_settings_init' ) );

		// Add Color Pickers Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_color_picker' ) );

		// Add Premium Page Text
		add_action( 'display_premium_page', array( $this, 'render_premium_page_free') );

		// Add Donation Tag Line
		add_action( 'display_donation_tagline', array( $this, 'render_donation_tagline') );

		// Shortcode Override Functions
		if ( !is_admin() ) {
			add_filter( 'wp_audio_shortcode_override' , array( $this, 'wp_audio_shortcode_override' ), 10, 2 );
			add_filter( 'post_playlist' , array( $this, 'wp_playlist_shortcode_override' ), 10, 3 );
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

			wp_register_script( 'wavesurfer', plugin_dir_url( __FILE__ ) . 'js/wavesurfer.js', array( 'jquery' ), '1.8.0', true );
			wp_register_script( 'wavesurfer-wp_init', plugin_dir_url( __FILE__ ) . 'js/wavesurfer-wp.js', array( 'jquery' ), '1.8.0', true );
			wp_register_script( 'download-js', plugin_dir_url( __FILE__ ) . 'js/download.min.js', array( 'jquery' ), '1.8.0', true );

			wp_register_style( 'wavesurfer_default', plugin_dir_url( __FILE__ ) . 'css/wavesurfer-wp_default.css' );
			wp_register_style( 'wavesurfer_flat-icons', plugin_dir_url( __FILE__ ) . 'css/wavesurfer-wp_flat-icons.css' );
		}

		wp_localize_script( 'wavesurfer-wp_init', 'wavesurfer_localize', $this->get_player_translation_strings() );
	}

	/**
	 * Get Player Translation Strings
	 *
	 */
	public static function get_player_translation_strings() {
		// Localize Scripts Strings
	 	$localize_strings = array(
	 		'play' => __('Play', 'wavesurfer'),
	 		'pause' => __('Pause', 'wavesurfer'),
	 		'resume' => __('Resume', 'wavesurfer'),
	 		'stop' => __('Stop', 'wavesurfer'),
	 		'loop' => __('Loop', 'wavesurfer'),
	 		'unloop' => __('Unloop', 'wavesurfer'),
	 		'mute' => __('Mute', 'wavesurfer'),
	 		'unmute' => __('Unmute', 'wavesurfer')
	 	);

		return $localize_strings;
	}

	/**
	 * Enqueue script for ajax
	 */
	public function my_enqueue_script( $script ) {
		wp_enqueue_script( $script );

		$wavesurfer_nonce = wp_create_nonce( 'wavesurfer_nonce' );

		wp_localize_script( $script, 'my_ajax_obj', array(
		   'ajax_url' => admin_url( 'admin-ajax.php' ),
		   'nonce'    => $wavesurfer_nonce,
		) );
	}

	/**
	 * load scripts in Front End
	 */
	public function wavesurfer_load_front_ressources() {
		if ( !is_admin() ) {

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
		if ( 'settings_page_wavesurfer-wp' != $hook )
			return;

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
			'<a href="' . admin_url('options-general.php?page=wavesurfer-wp.php' ) . '">' . __( 'Settings', 'wavesurfer-wp' ) . '</a>',
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
	public function wavesurfer_settings_init() {

		// Register Settings
		register_setting(
				'wavesurfer', // Option group
				'wavesurfer_settings' // Option name
		);


		// Register Section
		add_settings_section(
				'colors_section', // Id
				__( 'Appearance', 'wavesurfer-wp' ), // Title
				array( $this, 'render_colors_section' ), // Callback
				'wavesurfer' // Page
		);


		// Add Fields
		// Wave Color
		add_settings_field( // 0
				'wave_color', // Id
				__( 'Wave Color', 'wavesurfer-wp' ), // Title
				array( $this, 'render_wave_color_field' ), // Callback
				'wavesurfer', // Page
				'colors_section' // Section
		);

		// Progress Color
		add_settings_field( // 1
				'progress_color',
				__( 'Progress Color', 'wavesurfer-wp' ),
				array( $this, 'render_progress_color_field' ),
				'wavesurfer',
				'colors_section'
		);

		// Cursor Color
		add_settings_field( // 1
				'cursor_color',
				__( 'Cursor Color', 'wavesurfer-wp' ),
				array( $this, 'render_cursor_color_field' ),
				'wavesurfer',
				'colors_section'
		);

		// Theme
		add_settings_field( // 1
				'front_theme',
				__( 'Front Theme', 'wavesurfer-wp' ),
				array( $this, 'render_theme_field' ),
				'wavesurfer',
				'colors_section'
		);

		// Height
		add_settings_field( // 1
				'height',
				__( 'Height', 'wavesurfer-wp' ),
				array( $this, 'render_height_field' ),
				'wavesurfer',
				'colors_section'
		);

	}

	/**
	 * Echo form sections descriptions.
	 *
	 * @since 1.0
	 **/
	public function render_colors_section() { // 0
		echo __( 'Global style of the wavesurfer visualization and buttons control.', 'wavesurfer-wp' );
	}

	/**
	 * Render form fields
	 *
	 * @since 1.0
	 **/
	public function render_wave_color_field() { // 0

		$options = get_option( 'wavesurfer_settings' );
		$val = ( isset( $options['wave_color'] ) ) ? $options['wave_color'] : '';

		echo '<input type="text" name="wavesurfer_settings[wave_color]" value="' . $val .'" class="my-color-field" >';
		echo '<p>' . __( 'This setting can be locally overridden with the <code>wave_color="#123456"</code> [audio] shortcode attribute', 'wavesurfer-wp' ) .'.</p>';

	}


	public function render_progress_color_field() { // 1

		$options = get_option( 'wavesurfer_settings' );
		$val = ( isset( $options['progress_color'] ) ) ? $options['progress_color'] : '';

		echo '<input type="text" name="wavesurfer_settings[progress_color]" value="' . $val .'" class="my-color-field" >';
		echo '<p>' . __( 'This setting can be locally overridden with the <code>progress_color="#123456"</code> [audio] shortcode attribute', 'wavesurfer-wp' ) .'.</p>';

	}

	public function render_cursor_color_field() { // 1

		$options = get_option( 'wavesurfer_settings' );
		$val = ( isset( $options['cursor_color'] ) ) ? $options['cursor_color'] : '';

		echo '<input type="text" name="wavesurfer_settings[cursor_color]" value="' . $val .'" class="my-color-field" >';
		echo '<p>' . __( 'This setting can be locally overridden with the <code>cursor_color="#123456"</code> [audio] shortcode attribute', 'wavesurfer-wp' ) .'.</p>';

	}

	public function render_theme_field() { // 3

		$options = get_option( 'wavesurfer_settings' );
		//$val = ( isset( $options['front_theme'] ) ) ? $options['front_theme'] : '';

		?>
		<select name='wavesurfer_settings[front_theme]'>
			<option value='wavesurfer_default' <?php selected( $options['front_theme'], 'wavesurfer_default' ); ?>><?php _e('Default', 'wavesurfer'); ?></option>
			<option value='wavesurfer_flat-icons' <?php selected( $options['front_theme'], 'wavesurfer_flat-icons' ); ?>><?php _e('Flat Icons', 'wavesurfer'); ?></option>
			<option value='wavesurfer_none' <?php selected( $options['front_theme'], 'wavesurfer_none' ); ?>><?php _e('None', 'wavesurfer'); ?></option>
		</select>
		<p><?php _e( 'Style of the buttons. Default theme requires Font-Awesome 1.0.', 'wavesurfer-wp' ) ?></p>
		<?php

	}

	public function render_height_field() { // 4

		$options = get_option( 'wavesurfer_settings' );
		$val = ( isset( $options['height'] ) ) ? $options['height'] : '128';

		echo '<input type="number" name="wavesurfer_settings[height]" min="0" max="2048" value="' . $val .'" class="height" >';
		echo '<p>' . __( 'This setting can be locally overridden with the <code>height="128"</code> [audio] shortcode attribute', 'wavesurfer-wp' ) .'.</p>';

	}


	/**
	 * Content of the settings page
	 *
	 * @since 1.0.0
	 **/
	public function users_page() {

		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'wavesurfer-wp' ) );

?>

<div class="wrap wavesurfer">

	<!-- HEADER -->
	<!-- Header: Title	-->
	<h1><?php _e( 'WaveSurfer-WP', 'wavesurfer-wp' ); ?></h1>

	<!-- Header: Infos	-->
	<p><?php _e( 'A WordPress Integration of <a href="https://github.com/katspaugh">katspaugh</a>\'s <a href="http://wavesurfer-js.org/">wavesurfer.js</a> by <a href="http://www.extremraym.com/en/wavesurfer-wp/" target="_blank">X-Raym</a>.', 'wavesurfer-wp' ); ?></p>

	<?php do_action( 'display_donation_tagline', array( $this, 'render_donation_header') );
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general'; ?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=wavesurfer-wp.php&tab=general" class="nav-tab actions-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General', 'wavesurfer-wp' ); ?></a>
		<a href="?page=wavesurfer-wp.php&tab=premium" class="nav-tab license-tab <?php echo $active_tab == 'premium' ? 'nav-tab-active' : ''; ?>"><?php _e('Premium', 'wavesurfer'); ?></a>
	</h2>

	<div id="tab_container">

	<?php if( $active_tab == 'general' ) { ?>
		<form method="post" action="options.php"> <!-- options.php is important -->
			<?php

				settings_fields( 'wavesurfer' );
				do_settings_sections( 'wavesurfer' );

				submit_button();

			?>
		</form>

		<h3><?php _e( 'Other Shortcodes Attributes', 'wavesurfer-wp'); ?></h3>
		<dl>
			<dt><code>mute_button="true"</code></dt>
			<dd><?php _e( 'Add a mute button.', 'wavesurfer-wp'); ?></dd>
			<dt><code>loop_button="true"</code></dt>
			<dd><?php _e( 'Add a loop button', 'wavesurfer-wp'); ?></dd>
			<dt><code>download_button="true"</code></dt>
			<dd><?php _e( 'Add a download audio file button.', 'wavesurfer-wp'); ?></dd>
			<dt><code>split_channels="true"</code></dt>
			<dd><?php _e( 'If audio is multi-channels, split them for drawing the waveform.', 'wavesurfer-wp'); ?></dd>
			<dt><code>player="default"</code></dt>
			<dd><?php _e( 'Use the standard WordPress player.', 'wavesurfer-wp'); ?></dd>
		</dl>

		<h3><?php _e( 'Documentaion', 'wavesurfer-wp'); ?></h3>
		<p><?php _e( 'More infos on the <a href="https://wordpress.org/plugins/wavesurfer-wp/">WordPress.org</a> Plugin page.', 'wavesurfer-wp'); ?></p>

	</div>

	<?php } else { do_action( 'display_premium_page' ); }; ?>

</div>
<?php
	}

	/**
	 * Render Donation Tagline for free users
	 *
	 * @since 2.5
	 */
	public function render_donation_tagline() {
		ob_start(); ?>
		<p><?php _e( 'If you enjoy this free plugin, please consider making a <a href="https://www.extremraym.com/en/donation">donation</a>, contributing to its <a href="https://translate.wordpress.org/projects/wp-plugins/wavesurfer-wp">translations</a> or <a href="https://github.com/X-Raym/wavesurfer-wp">source code</a>, promoting it, or a buying it\'s <a href="https://www.extremraym.com/en/downloads/wavesurfer-wp-premium">premium add-on</a>. Thanks for your consideration!' , 'wavesufer-wp' ); ?></p>
		<?php echo ob_get_clean();
	}

	/**
	 * Render Premium page for free users
	 *
	 * @since 2.5
	 */
	public function render_premium_page_free() {
		ob_start(); ?>

		<p><?php _e( 'WaveSurfer-WP Premium Add-On is now avaible!', 'wavesurfer-wp'); ?></p>
		<h3><?php _e( 'Features', 'wavesurfer-wp'); ?></h3>
		<h4><?php _e( 'Cache Peaks File', 'wavesurfer-wp'); ?></h4>
		<p><?php _e( 'This add-on creates and loads peaks from small files, containing peaks values. No need to wait for the full audio to be decoded to display its waveform.', 'wavesurfer-wp'); ?></p>
		<h4><?php _e( 'Plug and Play', 'wavesurfer-wp'); ?></h4>
		<p><?php _e( 'These extra features are packed as an add-on. No need to delete and replace the original plugin.', 'wavesurfer-wp'); ?><br/><?php _e( 'You will still be able to benefit from translations made by the community. Also, the core is still open source, to allow contribution.', 'wavesurfer-wp'); ?></p>
		<h3><?php _e( 'Documentation', 'wavesurfer-wp'); ?></h3>
		<p><?php _e( 'All infos about this add-on are avaible on it\'s <a href="https://www.extremraym.com/en/downloads/wavesurfer-wp-premium">official product page</a>.', 'wavesurfer-wp'); ?></p>

		<?php echo ob_get_clean();
	}

	/**
	 * Audio Shortcode output
	 */
	public function wp_audio_shortcode_override( $html, $attr ) {

		//self::$load_front_ressources = true;
		$html = ''; // Value for not overring render

		// Check if shortcode render must be override or not
		if ( ! empty( $attr['player'] ) && $attr['player'] === 'default' ) {
				return $html;
		}

		// Enqueue Scripts
		wp_enqueue_script( 'wavesurfer' );
		$this->my_enqueue_script( 'wavesurfer-wp_init' );

		// Check audio type to determine the link
		if ( isset( $attr['wav'] ) ) { $link = $attr['wav']; }
		if ( isset( $attr['mp3'] ) ) { $link = $attr['mp3']; }
		if ( isset( $attr['m4a'] ) ) { $link = $attr['m4a']; }
		if ( isset( $attr['ogg'] ) ) { $link = $attr['ogg']; }
		if ( isset( $attr['src'] ) ) { $link = $attr['src']; }

		// Get Hash from URL
		$hash = hash( 'md5', $link );

		// Begin render
		$html .= '<div class="wavesurfer-block wavesurfer-audio">';
		$html .= '<div class="wavesurfer-player" ';

		// Split channels
		if ( isset( $attr['split_channels'] ) ) {
			if( $attr['split_channels'] == true )
			$html .= 'data-split-channels="true" ';
		}

		// Get Options
		$options = get_option( 'wavesurfer_settings' );

		// Wave color
		if ( isset( $attr['wave_color'] ) ) {
			$wave_color = esc_attr( $attr['wave_color'] );
		} else {
			$wave_color = ( isset( $options['wave_color'] ) ) ? $options['wave_color'] : 'violet'; // Get color value from Settings
		}
		$html .= 'data-wave-color="' . $wave_color . '" ';

		// Cursor color
		if ( isset( $attr['cursor_color'] ) ) {
			$cursor_color = esc_attr( $attr['cursor_color'] );
		} else {
			$cursor_color = ( isset( $options['cursor_color'] ) ) ? $options['cursor_color'] : '#333333'; // Get color value from Settings
		}
		$html .= 'data-cursor-color="' . $cursor_color . '" ';

		// Progress color
		if ( isset( $attr['progress_color'] ) ) {
			$progress_color = esc_attr( $attr['progress_color'] );
		} else {
			$progress_color = ( isset( $options['progress_color'] ) ) ? $options['progress_color'] : 'purple'; // Get color value from Settings
		}
		$html .= 'data-progress-color="' . $progress_color . '" ';

		// Height
		if ( isset( $attr['height'] ) ) {
			$height = esc_attr( $attr['height'] );
		} else {
			$height = ( isset( $options['height'] ) ) ? $options['height'] : '128'; // Get color value from Settings
		}
		$html .= 'data-height="' . $height . '" ';

		// File URL
		$html .= 'data-url="' . $link . '"';

		// Data hash
		$html .= 'data-hash="' . $hash . '"';

		// End div
		$html .= '></div>';

		// Progress Bar
		$html .= '<div class="wavesurfer-progress"><progress class="wavesurfer-loading" value="0" max="100"></progress></div>';

		// Buttons
		$html .= '<div class="wavesurfer-buttons_set">';
		$html .= '<button type="button" class="wavesurfer-play"><span>' . __('Play', 'wavesurfer') . '</span></button>';
		$html .= '<button type="button" class="wavesurfer-stop"><span>' . __('Stop', 'wavesurfer') . '</span></button>';

		// Mute button
		if ( isset( $attr['mute_button'] ) ) {
			if( $attr['mute_button'] == true )
			$html .= '<button type="button" class="wavesurfer-mute"><span>' . __('Mute', 'wavesurfer') . '</span></button>';
		}

		// Loop button channels
		if ( isset( $attr['loop_button'] ) ) {
			if( $attr['loop_button'] == true )
			$html .= '<button type="button" class="wavesurfer-loop"><span>' . __('Loop', 'wavesurfer') . '</span></button>';
		}

		// Download button
		if ( isset( $attr['download_button'] ) ) {
			if( $attr['download_button'] == true )
			$html .= '<button type="button" class="wavesurfer-download"><span>' . __('Download', 'wavesurfer') . '</span></button>';
			wp_enqueue_script( 'download-js' );
		}

		// Time buttons
		$html .= '<div class="wavesurfer-time"></div>';
		$html .= '<div class="wavesurfer-duration"></div>';
		$html .= '</div>';

		// End WaveSurfer Block
		$html .= '</div>';

		// Output
		return $html;

	}


	/**
	 * Audio Shortcode output
	 *
	 * https://github.com/WordPress/WordPress/blob/master/wp-includes/media.php#L1892
	 * https://developer.wordpress.org/reference/hooks/post_playlist/
	 */
	public function wp_playlist_shortcode_override( $html, $attr, $instance ) {

		//self::$load_front_ressources = true;
		$html = ''; // Value for not overring render

		// Check if shortcode render must be override or not - Check for Video Playlist
		if ( (! empty( $attr['player'] ) && $attr['player'] === 'default' ) || ( ! empty( $attr['type'] ) && $atts['type'] !== 'audio' ) || ( empty( $attr['ids'] ) ) ) {
				return $html;
		}

		// Enqueue Scripts
		wp_enqueue_script( 'wavesurfer' );
		$this->my_enqueue_script( 'wavesurfer-wp_init' );

		// Parse IDs
		if ( ! empty( $attr['ids'] ) ) {
			$ids = explode( ',', $attr['ids'] );
			$attachments = array();
			foreach ( $ids as $id ) {
				array_push( $attachments, get_post( $include = $id ) );
			};
		};

		// Check audio type to determine the link
		$link = $attachments[0]->guid;

		// Get Hash from URL
		$hash = hash( 'md5', $link );

		// Begin render
		$html .= '<div class="wavesurfer-block wavesurfer-playlist">';
		$html .= '<div class="wavesurfer-player" ';

		// Split channels
		if ( isset( $attr['split_channels'] ) ) {
			if( $attr['split_channels'] == true )
			$html .= 'data-split-channels="true" ';
		}

		// Get Options
		$options = get_option( 'wavesurfer_settings' );

		// Wave color
		if ( isset( $attr['wave_color'] ) ) {
			$wave_color = esc_attr( $attr['wave_color'] );
		} else {
			$wave_color = ( isset( $options['wave_color'] ) ) ? $options['wave_color'] : 'violet'; // Get color value from Settings
		}
		$html .= 'data-wave-color="' . $wave_color . '" ';

		// Cursor color
		if ( isset( $attr['cursor_color'] ) ) {
			$cursor_color = esc_attr( $attr['cursor_color'] );
		} else {
			$cursor_color = ( isset( $options['cursor_color'] ) ) ? $options['cursor_color'] : '#333333'; // Get color value from Settings
		}
		$html .= 'data-cursor-color="' . $cursor_color . '" ';

		// Progress color
		if ( isset( $attr['progress_color'] ) ) {
			$progress_color = esc_attr( $attr['progress_color'] );
		} else {
			$progress_color = ( isset( $options['progress_color'] ) ) ? $options['progress_color'] : 'purple'; // Get color value from Settings
		}
		$html .= 'data-progress-color="' . $progress_color . '" ';

		// Height
		if ( isset( $attr['height'] ) ) {
			$height = esc_attr( $attr['height'] );
		} else {
			$height = ( isset( $options['height'] ) ) ? $options['height'] : '128'; // Get color value from Settings
		}
		$html .= 'data-height="' . $height . '" ';

		// File URL
		$html .= 'data-url="' . $link . '"';

		// Data hash
		$html .= 'data-hash="' . $hash . '"';

		$html .= '></div>';

		// Progress Bar
		$html .= '<div class="wavesurfer-progress"><progress class="wavesurfer-loading" value="0" max="100"></progress></div>';

		// Buttons
		$html .= '<div class="wavesurfer-buttons_set">';
		$html .= '<button type="button" class="wavesurfer-play"><span>' . __('Play', 'wavesurfer') . '</span></button>';
		$html .= '<button type="button" class="wavesurfer-stop"><span>' . __('Stop', 'wavesurfer') . '</span></button>';

		// Mute button
		if ( isset( $attr['mute_button'] ) ) {
			if( $attr['mute_button'] == true )
			$html .= '<button type="button" class="wavesurfer-mute"><span>' . __('Mute', 'wavesurfer') . '</span></button>';
		}

		// Loop button channels
		if ( isset( $attr['loop_button'] ) ) {
			if( $attr['loop_button'] == true )
			$html .= '<button type="button" class="wavesurfer-loop"><span>' . __('Loop', 'wavesurfer') . '</span></button>';
		}

		// Download button
		if ( isset( $attr['download_button'] ) ) {
			if( $attr['download_button'] == true )
			$html .= '<button type="button" class="wavesurfer-download"><span>' . __('Download', 'wavesurfer') . '</span></button>';
			wp_enqueue_script( 'download-js' );
		}

		// Time buttons
		$html .= '<div class="wavesurfer-time"></div>';
		$html .= '<div class="wavesurfer-duration"></div>';
		$html .= '</div>';

		// Playlist
		$html .= '<ol class="wavesurfer-list-group">';
		foreach ( $attachments as $attachment ) {
			// Get Hash from URL
			$hash = hash( 'md5', $attachment->guid );
	 		$html .= '<li data-url="' . $attachment->guid . '" data-hash="' . $hash . '" class="list-group-item">' . $attachment->post_title . '</li>';
		};
		$html .= '</ol>';

		// End WaveSurfer Block
		$html .= '</div>';

		// Output
		return $html;

	}

} // End class declaration

// Create Object
WaveSurfer_WP::get_instance();
