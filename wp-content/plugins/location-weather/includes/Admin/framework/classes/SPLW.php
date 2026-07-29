<?php
/**
 *  Framework SPLW file.
 *
 * @package    Location_weather
 * @subpackage Location_weather/framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

if ( ! class_exists( 'SPLW' ) ) {
	/**
	 *
	 * Setup Class
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class SPLW {

		/**
		 * Premium
		 *
		 * @var string
		 */
		public static $premium = true;
		/**
		 * Version
		 *
		 * @var string
		 */
		public static $version = '2.1.8';
		/**
		 * Dir
		 *
		 * @var string
		 */
		public static $dir = '';
		/**
		 * Url
		 *
		 * @var string
		 */
		public static $url = '';
		/**
		 * Css
		 *
		 * @var string
		 */
		public static $css = '';
		/**
		 * Webfonts
		 *
		 * @var array
		 */
		public static $webfonts = array();
		/**
		 * Subsets
		 *
		 * @var array
		 */
		public static $subsets = array();
		/**
		 * Inited
		 *
		 * @var array
		 */
		public static $inited = array();
		/**
		 * Fields
		 *
		 * @var array
		 */
		public static $fields = array();
		/**
		 * Args
		 *
		 * @var array
		 */
		public static $args = array(
			'admin_options'   => array(),
			'metabox_options' => array(),
		);

		/**
		 * Shortcode instances
		 *
		 * @var array
		 */
		public static $shortcode_instances = array();

		/**
		 * Initialize
		 *
		 * @return void
		 */
		public static function init() {

			// Init action.
			do_action( 'splwt_init' );

			// Set directory constants.
			self::constants();

			// Include files.
			self::includes();

			add_action( 'after_setup_theme', array( 'SPLW', 'setup' ) );
			add_action( 'init', array( 'SPLW', 'setup' ) );
			add_action( 'switch_theme', array( 'SPLW', 'setup' ) );
			add_action( 'admin_enqueue_scripts', array( 'SPLW', 'add_admin_enqueue_scripts' ) );
			add_action( 'wp_head', array( 'SPLW', 'add_custom_css' ), 80 );
			add_filter( 'admin_body_class', array( 'SPLW', 'add_admin_body_class' ) );
			// Init translation in framework.
		}

		/**
		 * Setup frameworks
		 *
		 * @return void
		 */
		public static function setup() {
			// Setup admin option framework.
			$params = array();
			if ( ! empty( self::$args['admin_options'] ) ) {
				foreach ( self::$args['admin_options'] as $key => $value ) {
					if ( ! empty( self::$args['sections'][ $key ] ) && ! isset( self::$inited[ $key ] ) ) {

						$params['args']       = $value;
						$params['sections']   = self::$args['sections'][ $key ];
						self::$inited[ $key ] = true;

						SPLWT_Options::instance( $key, $params );
					}
				}
			}

			// Setup metabox option framework.
			$params = array();
			if ( ! empty( self::$args['metabox_options'] ) ) {
				foreach ( self::$args['metabox_options'] as $key => $value ) {
					if ( ! empty( self::$args['sections'][ $key ] ) && ! isset( self::$inited[ $key ] ) ) {

						$params['args']       = $value;
						$params['sections']   = self::$args['sections'][ $key ];
						self::$inited[ $key ] = true;

						SPLWT_Metabox::instance( $key, $params );

					}
				}
			}

			do_action( 'splwt_loaded' );
		}

		/**
		 * Create options
		 *
		 * @param int   $id id.
		 * @param array $args args.
		 * @return void
		 */
		public static function createOptions( $id, $args = array() ) {
			self::$args['admin_options'][ $id ] = $args;
		}

		/**
		 * Create metabox options.
		 *
		 * @param int   $id metabox id.
		 * @param array $args metabox args.
		 * @return void
		 */
		public static function createMetabox( $id, $args = array() ) {
			self::$args['metabox_options'][ $id ] = $args;
		}

		/**
		 * Create section.
		 *
		 * @param int   $id metabox id.
		 * @param array $sections metabox section.
		 * @return void
		 */
		public static function createSection( $id, $sections ) {
			self::$args['sections'][ $id ][] = $sections;
			self::set_used_fields( $sections );
		}

		/**
		 * Set directory constants.
		 *
		 * @return void
		 */
		public static function constants() {

			// We need this path-finder code for set URL of framework.
			$dirname        = wp_normalize_path( dirname( __DIR__ ) );
			$theme_dir      = wp_normalize_path( get_parent_theme_file_path() );
			$plugin_dir     = wp_normalize_path( WP_PLUGIN_DIR );
			$located_plugin = ( preg_match( '#' . self::sanitize_dirname( $plugin_dir ) . '#', self::sanitize_dirname( $dirname ) ) ) ? true : false;
			$directory      = ( $located_plugin ) ? $plugin_dir : $theme_dir;
			$directory_uri  = ( $located_plugin ) ? WP_PLUGIN_URL : get_parent_theme_file_uri();
			$foldername     = str_replace( $directory, '', $dirname );
			$protocol_uri   = ( is_ssl() ) ? 'https' : 'http';
			$directory_uri  = set_url_scheme( $directory_uri, $protocol_uri );

			self::$dir = $dirname;
			self::$url = $directory_uri . $foldername;
		}

		/**
		 * Include file helper
		 *
		 * @param string  $file file include.
		 * @param boolean $load file load.
		 * @return string
		 */
		public static function include_plugin_file( $file, $load = true ) {

			$path     = '';
			$file     = ltrim( $file, '/' );
			$override = apply_filters( 'splwt_override', 'splwt-lite-override' );

			if ( file_exists( get_parent_theme_file_path( $override . '/' . $file ) ) ) {
				$path = get_parent_theme_file_path( $override . '/' . $file );
			} elseif ( file_exists( get_theme_file_path( $override . '/' . $file ) ) ) {
				$path = get_theme_file_path( $override . '/' . $file );
			} elseif ( file_exists( self::$dir . '/' . $override . '/' . $file ) ) {
				$path = self::$dir . '/' . $override . '/' . $file;
			} elseif ( file_exists( self::$dir . '/' . $file ) ) {
				$path = self::$dir . '/' . $file;
			}

			if ( ! empty( $path ) && ! empty( $file ) && $load ) {

				global $wp_query;

				if ( is_object( $wp_query ) && function_exists( 'load_template' ) ) {

					load_template( $path, true );

				} else {

					require_once $path;

				}
			} else {

				return self::$dir . '/' . $file;

			}
		}

		/**
		 * Is active plugin helper
		 *
		 * @param string $file plugin file.
		 * @return boolean
		 */
		public static function is_active_plugin( $file = '' ) {
			return in_array( $file, (array) get_option( 'active_plugins', array() ), true );
		}

		/**
		 * Sanitize dirname
		 *
		 * @param array $dirname directory name.
		 * @return array
		 */
		public static function sanitize_dirname( $dirname ) {
			return preg_replace( '/[^A-Za-z]/', '', $dirname );
		}

		/**
		 * Set url constant
		 *
		 * @param string $file file url.
		 * @return string
		 */
		public static function include_plugin_url( $file ) {
			return esc_url( LOCATION_WEATHER_URL . '/includes/Admin/framework' ) . '/' . ltrim( $file, '/' );
		}

		/**
		 * Include files
		 *
		 * @return void
		 */
		public static function includes() {

			// Helpers.
			self::include_plugin_file( 'functions/actions.php' );
			self::include_plugin_file( 'functions/helpers.php' );
			self::include_plugin_file( 'functions/sanitize.php' );
			self::include_plugin_file( 'functions/validate.php' );

			// Includes free version classes.
			self::include_plugin_file( 'classes/abstract.class.php' );
			self::include_plugin_file( 'classes/fields.class.php' );
			self::include_plugin_file( 'classes/admin-options.class.php' );

			// Includes premium version classes.
			self::include_plugin_file( 'classes/metabox-options.class.php' );
		}

		/**
		 * Include framework configuration files.
		 *
		 * This function includes various configuration files related to
		 * layout, weather, display, style, typography, global settings,
		 * and tool options for the SPLW framework.
		 *
		 * @return void
		 */
		public function splw_framework_config() {
			self::include_plugin_file( 'classes/Generator_Config/LayoutSetup.php' );
			self::include_plugin_file( 'classes/Generator_Config/WeatherSetup.php' );
			self::include_plugin_file( 'classes/Generator_Config/DisplaySetup.php' );
			self::include_plugin_file( 'classes/Generator_Config/MapSetup.php' );
			self::include_plugin_file( 'classes/Generator_Config/StyleSetup.php' );
			self::include_plugin_file( 'classes/Generator_Config/TypographySetup.php' );
			self::include_plugin_file( 'classes/Tools_Config/Tools_Options.php' );
		}

		/**
		 * Maybe include a field class
		 *
		 * @param string $type include field type.
		 * @return void
		 */
		public static function maybe_include_field( $type = '' ) {
			if ( ! class_exists( 'SPLWT_Field_' . $type ) && class_exists( 'SPLWT_Fields' ) ) {
				self::include_plugin_file( 'fields/' . $type . '/' . $type . '.php' );
			}
		}

		/**
		 * Set all of used fields
		 *
		 * @param array $sections fields section.
		 * @return void
		 */
		public static function set_used_fields( $sections ) {

			if ( ! empty( $sections['fields'] ) ) {

				foreach ( $sections['fields'] as $field ) {

					if ( ! empty( $field['fields'] ) ) {
						self::set_used_fields( $field );
					}

					if ( ! empty( $field['tabs'] ) ) {
						self::set_used_fields( array( 'fields' => $field['tabs'] ) );
					}

					if ( ! empty( $field['accordions'] ) ) {
						self::set_used_fields( array( 'fields' => $field['accordions'] ) );
					}

					if ( ! empty( $field['type'] ) ) {
						self::$fields[ $field['type'] ] = $field;
					}
				}
			}
		}

		/**
		 * Enqueue admin and fields styles and scripts
		 *
		 * @return void
		 */
		public static function add_admin_enqueue_scripts() {

			// Loads scripts and styles only when needed.
			$enqueue  = false;
			$wpscreen = get_current_screen();

			if ( ! empty( self::$args['admin_options'] ) ) {
				foreach ( self::$args['admin_options'] as $argument ) {
					if ( substr( $wpscreen->id, -strlen( $argument['menu_slug'] ) ) === $argument['menu_slug'] ) {
						$enqueue = true;
					}
				}
			}

			if ( ! empty( self::$args['metabox_options'] ) ) {
				foreach ( self::$args['metabox_options'] as $argument ) {
					if ( in_array( $wpscreen->post_type, (array) $argument['post_type'], true ) || 'location_weather_page_splw_admin_dashboard' === $wpscreen->id ) {
						$enqueue = true;
					}
				}
			}

			if ( ! $enqueue ) {
				return;
			}

			// Check for developer mode.
			$min = ( self::$premium && SCRIPT_DEBUG ) ? '' : '.min';

			// Admin utilities.
			wp_enqueue_media();

			// Wp color picker.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			// Font awesome ( 5 ) Icons.
			wp_enqueue_style( 'splwt-fontawesome-icon', self::include_plugin_url( 'assets/css/font-awesome' . $min . '.css' ), array(), LOCATION_WEATHER_VERSION, 'all' );

			// Main style.
			wp_enqueue_style( 'splwt-lite', self::include_plugin_url( 'assets/css/style' . $min . '.css' ), array(), LOCATION_WEATHER_VERSION, 'all' );

			// Main RTL styles.
			if ( is_rtl() ) {
				wp_enqueue_style( 'splwt-lite-rtl', self::include_plugin_url( 'assets/css/style-rtl' . $min . '.css' ), array(), LOCATION_WEATHER_VERSION, 'all' );
			}

			// Add footer text.
			add_filter( 'admin_footer_text', array( 'SPLW', 'add_admin_footer_text' ) );
			add_filter( 'update_footer', array( 'SPLW', 'footer_version_text' ) );

			// Main scripts.
			wp_enqueue_script( 'splwt-lite-plugins', self::include_plugin_url( 'assets/js/plugins' . $min . '.js' ), array(), LOCATION_WEATHER_VERSION, true );
			wp_enqueue_script( 'splwt-lite', self::include_plugin_url( 'assets/js/main' . $min . '.js' ), array( 'splwt-lite-plugins' ), LOCATION_WEATHER_VERSION, true );

			// Main variables.
			wp_localize_script(
				'splwt-lite',
				'splwt_vars',
				array(
					'previewJS'     => esc_url( LOCATION_WEATHER_ASSETS . '/js/lw-scripts' . $min . '.js' ),
					'color_palette' => apply_filters( 'splwt_color_palette', array() ),
					'i18n'          => array(
						'confirm'         => esc_html__( 'Are you sure?', 'location-weather' ),
						/* translators: %1$s is replaced with "string" */
						'typing_text'     => esc_html__( 'Please enter %s or more characters', 'location-weather' ),
						'searching_text'  => esc_html__( 'Searching...', 'location-weather' ),
						'no_results_text' => esc_html__( 'No results found.', 'location-weather' ),
					),
					'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				)
			);

			// Enqueue fields scripts and styles.
			$enqueued = array();

			if ( ! empty( self::$fields ) ) {
				foreach ( self::$fields as $field ) {
					if ( ! empty( $field['type'] ) ) {
						$classname = 'SPLWT_Field_' . $field['type'];
						self::maybe_include_field( $field['type'] );
						if ( class_exists( $classname ) && method_exists( $classname, 'enqueue' ) ) {
							$instance = new $classname( $field );
							if ( method_exists( $classname, 'enqueue' ) ) {
								$instance->enqueue();
							}
							unset( $instance );
						}
					}
				}
			}

			do_action( 'splwt_enqueue' );
		}

		/**
		 * Footer version
		 *
		 * @return void
		 */
		public static function footer_version_text() {
			$default = sprintf( 'Enjoyed <b>Location Weather?</b> <a class="sp-lw-footer-text" href="https://wordpress.org/support/plugin/location-weather/reviews/" target="_blank"> Rate us! ★★★★★ </a>' );
			echo wp_kses_post( $default );
		}

		/**
		 * Footer text function
		 *
		 * @return void
		 */
		public static function add_admin_footer_text() {
			$heart_icon = '<svg class="splw-footer-heart" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#E25555"/>
			</svg>';

			$social_icons = array(
				'linkedin'  => array(
					'url'  => 'https://www.linkedin.com/company/shapedplugin',
					'icon' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 0c.563 0 1 .469 1 1v12c0 .563-.437 1-1 1H1c-.562 0-1-.437-1-1V1c0-.531.438-1 1-1zM4.219 12h.031V5.313H2.156V12zM3.187 2C2.532 2 2 2.531 2 3.219c0 .656.531 1.187 1.188 1.187s1.218-.531 1.218-1.187A1.22 1.22 0 0 0 3.188 2M12 12V8.344c0-1.813-.375-3.188-2.469-3.188-1.031 0-1.687.563-1.969 1.063h-.03v-.907h-2V12h2.093V8.688c0-.876.156-1.72 1.219-1.72 1.062 0 1.094 1 1.094 1.782V12z" fill="#757575"/></svg>',
				),
				'twitter'   => array(
					'url'  => 'https://www.x.com/shapedplugin/',
					'icon' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 0h10c1.094 0 2 .906 2 2v10c0 1.094-.906 2-2 2H2c-1.094 0-2-.906-2-2V2C0 .906.906 0 2 0m9.281 2.625H9.812L7.345 5.438 5.25 2.625H2.188l3.656 4.781-3.469 3.969h1.469L6.53 8.313l2.344 3.062h2.969L8.03 6.344zM10.094 10.5H9.28L3.906 3.469h.875z" fill="#757575"/></svg>',
				),
				'wordpress' => array(
					'url'  => 'https://profiles.wordpress.org/shapedplugin/#content-plugins',
					'icon' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.688 5.031a6.6 6.6 0 0 0-.594 2.719 6.62 6.62 0 0 0 3.75 5.969zM12.25 7.406c0 .594-.219 1.25-.5 2.157l-.687 2.218-2.376-7.156c.376 0 .75-.062.75-.062.344-.032.313-.563-.062-.532 0 0-1.062.063-1.75.063-.656 0-1.75-.063-1.75-.063-.375-.031-.406.532-.062.532 0 0 .343.062.718.062l1.032 2.844-1.47 4.375-2.405-7.219c.406 0 .75-.062.75-.062.343-.032.312-.563-.032-.532 0 0-1.093.063-1.781.063h-.437a6.65 6.65 0 0 1 5.562-3c1.719 0 3.313.656 4.5 1.75h-.094c-.656 0-1.125.562-1.125 1.187 0 .532.313 1 .656 1.563.25.437.563 1 .563 1.812m-4.375.938 2.031 5.594c.031.03.031.062.063.093a6.5 6.5 0 0 1-2.219.375 6.2 6.2 0 0 1-1.875-.281zm5.719-3.781c.5.937.812 2.03.812 3.187 0 2.469-1.344 4.594-3.312 5.75l2.031-5.875c.375-.937.5-1.719.5-2.375 0-.25 0-.469-.031-.687M0 7.75a7.75 7.75 0 0 0 7.75 7.75 7.75 7.75 0 0 0 7.75-7.75A7.75 7.75 0 0 0 7.75 0 7.75 7.75 0 0 0 0 7.75m15.156 0a7.4 7.4 0 0 1-7.406 7.406A7.4 7.4 0 0 1 .344 7.75 7.4 7.4 0 0 1 7.75.344a7.4 7.4 0 0 1 7.406 7.406" fill="#757575"/></svg>',
				),
				'facebook'  => array(
					'url'  => 'https://www.facebook.com/shapedplugin/',
					'icon' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 0h10c1.094 0 2 .906 2 2v10c0 1.094-.906 2-2 2H7.969V9.438h2.156L10.594 7H7.969v-.875c0-1.281.5-1.781 1.812-1.781.406 0 .75 0 .938.031V2.156c-.375-.094-1.25-.187-1.75-.187-2.656 0-3.906 1.25-3.906 3.968V7H3.406v2.438h1.656V14H2c-1.094 0-2-.906-2-2V2C0 .906.906 0 2 0" fill="#757575"/></svg>',
				),
				'youtube'   => array(
					'url'  => 'https://www.youtube.com/@shapedplugin',
					'icon' => '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m8.813 7-2.97 1.688V5.312zM12 0c1.094 0 2 .906 2 2v10c0 1.094-.906 2-2 2H2c-1.094 0-2-.906-2-2V2C0 .906.906 0 2 0zm.438 4.25c-.126-.5-.5-.875-1-1C10.562 3 7 3 7 3s-3.562 0-4.437.25c-.5.125-.876.5-1 1-.25.906-.25 2.75-.25 2.75s0 1.875.25 2.781c.125.469.5.844 1 .969C3.437 11 7 11 7 11s3.563 0 4.438-.25c.5-.125.874-.5 1-1 .25-.875.25-2.75.25-2.75s0-1.844-.25-2.75" fill="#757575"/></svg>',
				),
			);

			$social_html = '';
			foreach ( $social_icons as $platform => $data ) {
				$social_html .= sprintf(
					'<a href="%s" target="_blank" rel="noopener noreferrer" class="splw-footer-social-link" title="%s">%s</a>',
					esc_url( $data['url'] ),
					esc_attr( ucfirst( $platform ) ),
					$data['icon']
				);
			}

			$default = sprintf(
				'<div class="splw-footer-container">
					<div class="splw-footer-left">
						<span class="splw-footer-made-with">%s</span>
						%s
						<span class="splw-footer-by">%s</span>
						<a href="https://shapedplugin.com/about-us/" target="_blank" rel="noopener noreferrer" class="splw-footer-team-link">ShapedPlugin LLC Team</a>
					</div>
					<div class="splw-footer-social">Get Connected with %s</div>
				</div>',
				esc_html__( 'Made with', 'location-weather' ),
				$heart_icon,
				esc_html__( 'by the', 'location-weather' ),
				$social_html
			);

			echo $default; //phpcs:ignore -- No user input.
		}

		/**
		 * Add admin body class
		 *
		 * @param string $classes admin body class.
		 * @return string
		 */
		public static function add_admin_body_class( $classes ) {

			if ( apply_filters( 'splwt_fa4', false ) ) {
				$classes .= 'splwt-lite-fa5-shims';
			}

			return $classes;
		}

		/**
		 * Add custom css to front page
		 *
		 * @return void
		 */
		public static function add_custom_css() {

			if ( ! empty( self::$css ) ) {
				echo '<style type="text/css">' . wp_strip_all_tags( self::$css ) . '</style>'; // phpcs:ignore
			}
		}

		/**
		 * Add a new framework field
		 *
		 * @param array  $field framework field.
		 * @param string $value framework field value.
		 * @param string $unique framework field unique id.
		 * @param string $where framework field where.
		 * @param string $parent framework field parent.
		 * @return void
		 */
		public static function field( $field = array(), $value = '', $unique = '', $where = '', $parent = '' ) {

			// Check for unallow fields.
			if ( ! empty( $field['_notice'] ) ) {

				$field_type = $field['type'];

				$field            = array();
				$field['content'] = esc_html__( 'Oops! Not allowed.', 'location-weather' ) . ' <strong>(' . $field_type . ')</strong>';
				$field['type']    = 'notice';
				$field['style']   = 'danger';

			}

			$depend     = '';
			$visible    = '';
			$unique     = ( ! empty( $unique ) ) ? $unique : '';
			$class      = ( ! empty( $field['class'] ) ) ? ' ' . esc_attr( $field['class'] ) : '';
			$is_pseudo  = ( ! empty( $field['pseudo'] ) ) ? ' splwt-lite-pseudo-field' : '';
			$field_type = ( ! empty( $field['type'] ) ) ? esc_attr( $field['type'] ) : '';

			if ( ! empty( $field['dependency'] ) ) {

				$dependency      = $field['dependency'];
				$depend_visible  = '';
				$data_controller = '';
				$data_condition  = '';
				$data_value      = '';
				$data_global     = '';

				if ( is_array( $dependency[0] ) ) {
					$data_controller = implode( '|', array_column( $dependency, 0 ) );
					$data_condition  = implode( '|', array_column( $dependency, 1 ) );
					$data_value      = implode( '|', array_column( $dependency, 2 ) );
					$data_global     = implode( '|', array_column( $dependency, 3 ) );
					$depend_visible  = implode( '|', array_column( $dependency, 4 ) );
				} else {
					$data_controller = ( ! empty( $dependency[0] ) ) ? $dependency[0] : '';
					$data_condition  = ( ! empty( $dependency[1] ) ) ? $dependency[1] : '';
					$data_value      = ( ! empty( $dependency[2] ) ) ? $dependency[2] : '';
					$data_global     = ( ! empty( $dependency[3] ) ) ? $dependency[3] : '';
					$depend_visible  = ( ! empty( $dependency[4] ) ) ? $dependency[4] : '';
				}

				$depend .= ' data-controller="' . esc_attr( $data_controller ) . '"';
				$depend .= ' data-condition="' . esc_attr( $data_condition ) . '"';
				$depend .= ' data-value="' . esc_attr( $data_value ) . '"';
				$depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';

				$visible = ( ! empty( $depend_visible ) ) ? ' splwt-lite-depend-visible' : ' splwt-lite-depend-hidden';

			}

			if ( ! empty( $field_type ) ) {

				// These attributes has been sanitized above.
				echo '<div class="splwt-lite-field splwt-lite-field-' . esc_attr( $field_type . $is_pseudo . $class . $visible ) . '"' . wp_kses_post( $depend ) . '>';

				if ( ! empty( $field['fancy_title'] ) ) {
					echo '<div class="splwt-lite-fancy-title">' . wp_kses_post( $field['fancy_title'] ) . '</div>';
				}

				if ( ! empty( $field['title'] ) ) {
					$title_info = ( ! empty( $field['title_info'] ) ) ? '<span class="splwt-lite-help title-info"><div class="splwt-lite-help-text">' . wp_kses_post( $field['title_info'] ) . '</div><img src="' . self::include_plugin_url( 'assets/images/info.svg' ) . '"></span>' : '';
					echo '<div class="splwt-lite-title">';
					echo '<h4>' . wp_kses_post( $field['title'] . $title_info ) . '</h4>';
					echo ( ! empty( $field['subtitle'] ) ) ? '<div class="splwt-lite-subtitle-text">' . wp_kses_post( $field['subtitle'] ) . '</div>' : '';
					echo '</div>';
				}

				echo ( ! empty( $field['title'] ) || ! empty( $field['fancy_title'] ) ) ? '<div class="splwt-lite-fieldset">' : '';

				$value = ( ! isset( $value ) && isset( $field['default'] ) ) ? $field['default'] : $value;
				$value = ( isset( $field['value'] ) ) ? $field['value'] : $value;

				self::maybe_include_field( $field_type );

				$classname = 'SPLWT_Field_' . $field_type;

				if ( class_exists( $classname ) ) {
					$instance = new $classname( $field, $value, $unique, $where, $parent );
					$instance->render();
				} else {
					echo '<p>' . esc_html__( 'Field not found!', 'location-weather' ) . '</p>';
				}
			} else {
				echo '<p>' . esc_html__( 'Field not found!', 'location-weather' ) . '</p>';
			}

			echo ( ! empty( $field['title'] ) || ! empty( $field['fancy_title'] ) ) ? '</div>' : '';
			echo '<div class="clear"></div>';
			echo '</div>';
		}
	}

	SPLW::init();
}
