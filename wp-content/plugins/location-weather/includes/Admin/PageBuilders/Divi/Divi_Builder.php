<?php
/**
 * Divi Builder Integration
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Divi;

use ShapedPlugin\Weather\Admin\PageBuilders\Base\Base_Page_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cannot access directly.
}

/**
 * Register Location Weather module with Divi Builder.
 *
 * @since 3.2.0
 *
 * @return void
 */
function splw_template_divi_modules() {

	// Prevent duplicate registration.
	static $registered = false;
	if ( $registered ) {
		return;
	}

	// Check if Divi Builder is active.
	if ( ! class_exists( 'ET_Builder_Module' ) ) {
		return;
	}

	// Check if integration is enabled in dashboard settings.
	$settings     = get_option( 'splw_page_builder_settings', array() );
	$divi_enabled = isset( $settings['divi_enabled'] ) ? (bool) $settings['divi_enabled'] : true;

	if ( ! $divi_enabled ) {
		return;
	}

	// Check if module class already exists.
	if ( class_exists( 'ET_Builder_Module_Location_Weather', false ) ) {
		return;
	}

	/**
	 * Divi Module class
	 */
	class ET_Builder_Module_Location_Weather extends \ET_Builder_Module {

		private $helper;

		/**
		 * Module slug.
		 *
		 * @var string
		 */
		public $slug = 'et_pb_location_weather';

		/**
		 * Visual Builder support.
		 *
		 * @var string
		 */
		public $vb_support = 'partial';

		/**
		 * Module credits.
		 *
		 * @var array
		 */
		protected $module_credits = array(
			'module_uri' => 'https://shapedplugin.com',
			'author'     => 'ShapedPlugin',
		);

		/**
		 * Initialize the module.
		 *
		 * @since 3.2.0
		 *
		 * @return void
		 */
		public function init() {
			$this->name      = esc_html__( 'Location Weather', 'location-weather' );
			$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
			$this->helper    = new Divi_Helper();

			// Enqueue scripts for page builder.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_page_builder_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_page_builder_scripts' ) );
		}

		/**
		 * Get fields.
		 *
		 * @since 3.2.0
		 *
		 * @return array
		 */
		public function get_fields() {
			$template_list = $this->splw_get_template_list();

			return array(
				'template_id' => array(
					'label'           => esc_html__( 'Saved Template', 'location-weather' ),
					'type'            => 'select',
					'option_category' => 'basic_option',
					'options'         => $template_list,
					'default'         => 'none',
					'description'     => esc_html__( 'Select a saved weather template', 'location-weather' ),
					'toggle_slug'     => 'main_content',
				),
			);
		}

		/**
		 * Render module.
		 *
		 * @since 3.2.0
		 *
		 * @param array  $attrs       Module attributes.
		 * @param string $content     Module content.
		 * @param string $render_slug Module render slug.
		 * @return string Rendered content.
		 */
		public function render( $attrs, $content = null, $render_slug = '' ) {
			// Check attrs first, fallback to props.
			$template_id = isset( $attrs['template_id'] ) ? $attrs['template_id'] : '0';
			if ( empty( $template_id ) && isset( $this->props['template_id'] ) ) {
				$template_id = $this->props['template_id'];
			}

			// Show placeholder if no template selected.
			if ( 'none' === $template_id || empty( $template_id ) ) {
				return '<div style="
					text-align: center;
					padding: 20px;
					border: 2px dashed #ccc;
					color: #999;
					font-size: 14px;
				">
					' . esc_html__( 'Please Select a Saved Template', 'location-weather' ) . '
				</div>';
			}

			// Get template post.
			$template_post = get_post( $template_id );
			if ( ! $template_post || 'publish' !== $template_post->post_status ) {
				return '<div style="
					text-align: center;
					padding: 20px;
					border: 2px dashed #ccc;
					color: #999;
					font-size: 14px;
				">
					' . esc_html__( 'Template not found or not published.', 'location-weather' ) . '
				</div>';
			}

			$template_content = $template_post->post_content;
			if ( empty( $template_content ) ) {
				return '<div style="
					text-align: center;
					padding: 20px;
					border: 2px dashed #ccc;
					color: #999;
					font-size: 14px;
				">
					' . esc_html__( 'Template content is empty.', 'location-weather' ) . '
				</div>';
			}

			// Check if in any Divi editor mode (Frontend, Backend, or AJAX rendering).
			$is_editor = splw_template_divi_is_builder_editor();

			// Capture and echo dynamic CSS for this template in editor (like Elementor).
			$css_output = '';
			if ( $is_editor ) {
				ob_start();
				$this->enqueue_template_dynamic_css( $template_id );
				$css_output = ob_get_clean();
			}

			// Render the shortcode.
			$output = do_shortcode( '[location_weather id="' . absint( $template_id ) . '"]' );

			// Wrap output with builder-specific class.
			return $css_output . '<div class="splw-divi-weather-wrapper" data-builder-template-id="' . esc_attr( $template_id ) . '">' .
				$output .
			'</div>';
		}

		/**
		 * Enqueue CSS for template in Divi builder.
		 *
		 * @since 3.2.0
		 *
		 * @param int $template_id Template post ID.
		 * @return void
		 */
		protected function enqueue_template_dynamic_css( $template_id ) {
			// Prevent duplicate CSS generation for the same template in current request.
			static $generated_templates = array();
			if ( isset( $generated_templates[ $template_id ] ) ) {
				return;
			}

			// Mark this template as processed.
			$generated_templates[ $template_id ] = true;

			$upload_dir = wp_upload_dir();
			$css_file   = trailingslashit( $upload_dir['basedir'] ) . 'spl-weather-css/spl-weather-' . $template_id . '.css';
			$css_url    = trailingslashit( $upload_dir['baseurl'] ) . 'spl-weather-css/spl-weather-' . $template_id . '.css';

			if ( file_exists( $css_file ) ) {
				$sp_rand = get_post_meta( $template_id, '_sp_splw_unique_version', true );
				$sp_rand = ! empty( $sp_rand ) ? $sp_rand : LOCATION_WEATHER_VERSION;
				// Echo CSS link directly for Divi editor (like Elementor).
				echo '<link rel="stylesheet" id="splw-css-' . esc_attr( $template_id ) . '" href="' . esc_url( $css_url . '?v=' . $sp_rand ) . '" media="all">'; // phpcs:ignore

				$font_lists = get_post_meta( $template_id, '_spl_weather_fonts', true );
			} else {
				// Fallback: CSS from post meta.
				$css = get_post_meta( $template_id, '_spl_weather_css', true );
				if ( ! empty( $css ) ) {
					// Echo inline style directly for Divi editor (like Elementor).
					echo '<style id="splw-divi-dynamic-css-' . esc_attr( $template_id ) . '">' . $css . '</style>'; // phpcs:ignore
				}
				$font_lists = get_post_meta( $template_id, '_spl_weather_fonts', true );
			}

			// Echo Google Fonts directly.
			if ( ! empty( $font_lists ) && is_array( $font_lists ) ) {
				$font_lists = array_unique( $font_lists );
				foreach ( $font_lists as $font ) {
					if ( ! empty( $font ) ) {
						echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=' . esc_attr( $font ) . '">'; // phpcs:ignore
					}
				}
			}
		}

		/**
		 * Enqueue page builder scripts.
		 *
		 * @since 3.2.0
		 *
		 * @return void
		 */
		public function enqueue_page_builder_scripts() {
			// Only load in Divi builder editor, not the regular frontend.
			if ( ! splw_template_divi_is_builder_editor() ) {
				return;
			}

			splw_template_divi_enqueue_builder_assets();
			// Add inline script for Divi builder change events.
			wp_add_inline_script(
				'splw-blocks-builder-script',
				'jQuery(document).ready(function($) {
					// Re-initialize scripts when Divi builder updates elements
					function reinitLocationWeatherScripts() {
					console.log(typeof window.locationInit === "function");
						if (typeof window.locationInit === "function") {
							window.locationInit();
						}
					}

					// Listen for Divi Visual Builder events
					$(document).on("divi:module:updated", function(e, moduleId) {
						reinitLocationWeatherScripts();
					});

					// Listen for Divi AJAX render events
					$(document).on("divi:ajax:render:success", function() {
						reinitLocationWeatherScripts();
					});

					// Listen for Divi builder save events
					$(document).on("divi:builder:save", function() {
						reinitLocationWeatherScripts();
					});

					// Also observe DOM changes as fallback - watch for .splw-divi-weather-wrapper
					var observer = new MutationObserver(function(mutations) {
						mutations.forEach(function(mutation) {
							if (mutation.addedNodes.length) {
								for (var i = 0; i < mutation.addedNodes.length; i++) {
									var node = mutation.addedNodes[i];
									if (node.nodeType === 1) {
										if ($(node).hasClass("splw-divi-weather-wrapper") || $(node).find(".splw-divi-weather-wrapper").length) {
											reinitLocationWeatherScripts();
											break;
										}
									}
								}
							}
						});
					});

					// Start observing the document for changes
					if (document.body) {
						observer.observe(document.body, { childList: true, subtree: true });
					}
				});'
			);
		}

		/**
		 * Retrieve all published Location Weather templates.
		 *
		 * @since 3.2.0
		 *
		 * @return array Template list.
		 */
		private function splw_get_template_list() {
			$list = array(
				'none' => esc_html__( '- Select Template -', 'location-weather' ),
			);

			$query = new \WP_Query(
				// phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- High limit needed for template selection
				array(
					'post_type'      => 'spl_weather_template',
					'post_status'    => 'publish',
					'posts_per_page' => 10000,
				)
			);

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post ) {
					$title = $post->post_title;
					if ( empty( $title ) ) {
						$title = '#' . $post->ID;
					}
					$list[ $post->ID ] = $title;
				}
			}

			return $list;
		}
	}

	new ET_Builder_Module_Location_Weather();

	// Mark as registered to prevent duplicates.
	$registered = true;
}

/**
 * Check if the current request is for Divi Builder or Visual Builder.
 *
 * Detects Frontend Builder (et_fb), Backend Builder (et_bfb), and AJAX rendering modes.
 *
 * @since 3.2.0
 *
 * @return bool True if in Divi builder context, false otherwise.
 */
function splw_template_divi_is_builder_editor() {
	// 1. Check Divi Frontend Builder (Visual Builder) via URL parameter.
	if ( isset( $_GET['et_fb'] ) ) {
		return true;
	}

	// 2. Check Divi Backend Builder (New Experience) via URL parameter.
	if ( isset( $_GET['et_bfb'] ) && is_admin() ) {
		return true;
	}

	// 3. Check Divi preview mode.
	if ( isset( $_GET['et_pb_preview'] ) ) {
		return true;
	}

	// 4. Check global variable set during Frontend Builder.
	if ( isset( $GLOBALS['et_fb'] ) && $GLOBALS['et_fb'] ) {
		return true;
	}

	// 5. Check Divi AJAX requests (for module rendering).
	if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 0 === strpos( sanitize_key( wp_unslash( $_REQUEST['action'] ) ), 'et_' ) ) {
		return true;
	}

	// 6. Check if Divi JSON request (used by Visual Builder for module rendering).
	if ( wp_is_json_request() && function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
		return true;
	}

	// 7. Check if builder is loaded (works for Backend Builder in admin context).
	if ( function_exists( 'et_builder_is_loaded' ) && et_builder_is_loaded() ) {
		// Additional check: only return true in admin context for Backend Builder reliability.
		if ( is_admin() ) {
			return true;
		}
		// For Frontend Builder, verify with et_core_is_fb_enabled().
		if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
			return true;
		}
	}

	// 8. Final fallback: check if Frontend Builder is enabled.
	if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
		return true;
	}

	return false;
}

/**
 * Register and enqueue block frontend assets needed by saved templates in Divi.
 *
 * @since 3.2.0
 *
 * @return void
 */
function splw_template_divi_enqueue_builder_assets() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Enqueue main location weather scripts.
	if ( ! wp_script_is( 'splw-scripts', 'registered' ) ) {
		wp_register_script(
			'splw-scripts',
			LOCATION_WEATHER_ASSETS . '/js/lw-scripts' . $suffix . '.js',
			array( 'jquery' ),
			LOCATION_WEATHER_VERSION,
			true
		);
	}
	wp_enqueue_script( 'splw-scripts' );

	// Enqueue swiper if needed.
	if ( ! wp_script_is( 'splw-swiper-scripts', 'registered' ) ) {
		wp_register_script(
			'splw-swiper-scripts',
			LOCATION_WEATHER_ASSETS . '/js/swiper' . $suffix . '.js',
			array( 'jquery' ),
			LOCATION_WEATHER_VERSION,
			true
		);
	}
	wp_enqueue_script( 'splw-swiper-scripts' );

	// Enqueue Blocks builder script.
	if ( ! wp_script_is( 'splw-blocks-builder-script', 'registered' ) ) {
		wp_register_script(
			'splw-blocks-builder-script',
			LOCATION_WEATHER_URL . '/includes/Blocks/assets/js/script' . $suffix . '.js',
			array( 'jquery' ),
			LOCATION_WEATHER_VERSION,
			true
		);
	}
	wp_enqueue_script( 'splw-blocks-builder-script' );
}

/**
 * Helper class for Divi module.
 *
 * @since 3.2.0
 */
class Divi_Helper {
	use Base_Page_Builder;
}

/**
 * Divi Builder class for initialization.
 *
 * @since 3.2.0
 */
class Divi_Builder {

	/**
	 * Initialize the integration.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function init() {
		// Hook to et_builder_ready to register module.
		add_action( 'et_builder_ready', 'ShapedPlugin\Weather\Admin\PageBuilders\Divi\splw_template_divi_modules' );
	}
}
