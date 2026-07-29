<?php
/**
 * Main file for Location Weather Blocks.
 *
 * @package Location_Weather_Pro/Blocks
 */

namespace ShapedPlugin\Weather\Blocks;

use ShapedPlugin\Weather\Blocks\Includes\Weather_Block_Register;
use ShapedPlugin\Weather\Blocks\Includes\Render_Blocks_Templates;
use ShapedPlugin\Weather\Blocks\Includes\Manage_Dynamic_CSS;
use ShapedPlugin\Weather\Blocks\Includes\Weather_Premade_Patterns;

/**
* Exit if accessed directly.
*/
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Main location weather blocks Class.
 */
class Blocks {
	/**
	 * This plugin's instance.
	 *
	 * @var Blocks
	 */
	private static $instance;

	/**
	 * CSS directory URL
	 *
	 * @var string
	 */
	private $css_url;

	/**
	 * Main Blocks Instance.
	 *
	 * Insures that only one instance of Blocks exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @static
	 * @return object|Blocks The one true Blocks
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Blocks ) ) {
			self::$instance = new Blocks();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Load actions
	 *
	 * @return void
	 */
	private function init() {
		add_action( 'plugins_loaded', array( $this, 'lw_blocks_plugins_loaded' ), 99 );
	}

	/**
	 * Loads the plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function lw_blocks_plugins_loaded() {
		/**
		 * Register gutenberg block function hook.
		 */

		add_action( 'init', array( new Weather_Block_Register(), 'spl_weather_blocks_init' ) );

		add_action( 'enqueue_block_assets', array( $this, 'splw_scripts_enqueue' ) );

		add_filter( 'rest_post_collection_params', array( $this, 'add_rand_orderby_rest_post_collection_params' ) );

		remove_filter( 'admin_head', 'wp_check_widget_editor_deps' );

		// Create Block category.
		if ( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
			add_filter( 'block_categories', array( $this, 'lw_blocks_register_block_category' ), 10, 2 );
		} else {
			add_filter( 'block_categories_all', array( $this, 'lw_blocks_register_block_category' ), 10, 2 );
		}

		// Editor-only endpoint (uses the editor nonce splw_block_api_nonce); no nopriv registration.
		add_action( 'wp_ajax_splw_ajax_block_data', array( $this, 'splw_ajax_block_data' ) );
		// block settings.
		add_action( 'wp_ajax_splw_block_color_settings_ajax', array( $this, 'splw_block_color_settings_ajax' ) );
		new Manage_Dynamic_CSS();
		new Weather_Premade_Patterns();
	}


	/**
	 * Splw_scripts_enqueue load enqueue file.
	 */
	public function splw_scripts_enqueue() {
		$script_suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
		/**
		 * Register only admin styles and scripts.
		 */
		if ( is_admin() ) {
			// editor script file.
			wp_register_script(
				'spl_weather_editor_js',
				plugin_dir_url( __FILE__ ) . 'build/index.js',
				array(),
				LOCATION_WEATHER_VERSION,
				true
			);
			// editor css file.
			wp_register_style(
				'splw_index_editor_style',
				plugin_dir_url( __FILE__ ) . 'build/index.css',
				array(),
				LOCATION_WEATHER_VERSION,
				'all'
			);
		}
		/**
		 * Register block styles.
		 */
		// frontend and editor main css file.
		wp_register_style(
			'splw_index_style',
			plugin_dir_url( __FILE__ ) . 'build/style-index.css',
			array(),
			LOCATION_WEATHER_VERSION,
			'all'
		);

		// block main script file.
		wp_register_script(
			'spl-weather-block-script',
			LOCATION_WEATHER_URL . '/includes/Blocks/assets/js/script' . $script_suffix . '.js',
			array(),
			LOCATION_WEATHER_VERSION,
			true
		);

		// localize data .
		$splw_option      = get_option( 'location_weather_settings', true );
		$open_api_key     = isset( $splw_option['open-api-key'] ) ? $splw_option['open-api-key'] : '';
		$weather_api_key  = isset( $splw_option['weather-api-key'] ) ? $splw_option['weather-api-key'] : '';
		$has_api_key      = ! empty( $open_api_key ) || ! empty( $weather_api_key );
		$weather_api_info = array(
			'lw_api_type'             => $splw_option['lw_api_source_type'] ?? 'openweather_api',
			'lw_openweather_api_type' => $splw_option['lw_openweather_api_type'] ?? 'free',
			'has_api_key'             => $has_api_key,
		);
		wp_localize_script(
			'spl_weather_editor_js',
			'splWeatherBlockLocalize',
			array(
				'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
				'blockApiNonce'     => wp_create_nonce( 'splw_block_api_nonce' ),
				'adminUrl'          => admin_url(),
				'pluginUrl'         => LOCATION_WEATHER_URL,
				'blockOptions'      => get_option( 'splw_blocks_visibility_options' ),
				'weather_api_info'  => $weather_api_info,
				'savedTemplatesUrl' => admin_url( 'edit.php?post_type=location_weather&page=splw_admin_dashboard#saved_templates' ),
			)
		);
		wp_localize_script(
			'spl-weather-block-script',
			'splWeatherBlockFrontendLocalize',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'post_id'       => get_the_ID(),
				'blockAPiNonce' => wp_create_nonce( 'splw_block_frontend_nonce' ),
			)
		);
	}

	/**
	 * Method splw_block_color_settings_ajax
	 *
	 * @return void
	 */
	public function splw_block_color_settings_ajax() {
		// Check user capabilities, current_user_can() is called internally.
		location_weather_verify_capability();
		$nonce = isset( $_POST['splwBlockApiNonce'] ) ? sanitize_text_field( wp_unslash( $_POST['splwBlockApiNonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'splw_block_api_nonce' ) ) {
			return;
		}

		$query_data = isset( $_POST['colorSettingsData'] ) ? sanitize_text_field( wp_unslash( $_POST['colorSettingsData'] ) ) : array();
		$query_data = json_decode( $query_data );

		if ( isset( $query_data ) ) {
			update_option( 'splw_blocks_custom_colors_options', $query_data );
		}

		// theme colors.
		$global_settings  = wp_get_global_settings();
		$theme_colors     = isset( $global_settings['color']['palette']['theme'] ) ? $global_settings['color']['palette']['theme'] : array();
		$custom_colors    = isset( $global_settings['color']['palette']['custom'] ) ? $global_settings['color']['palette']['custom'] : array();
		$theme_all_colors = array_merge( $theme_colors, $custom_colors );

		// send response.
		$response = array(
			'theme_colors'  => $theme_all_colors,
			'custom_colors' => get_option( 'splw_blocks_custom_colors_options', array() ),
		);
		wp_send_json_success( $response );
		wp_die();
	}
	/**
	 * Gets the icon folder name based on the type.
	 * Provides compatibility for calling `self::forecast_icon_url()` within this class.
	 *
	 * @param string $weather_icon The icon set type.
	 * @param string $icon_type The folder name for the icon set.
	 * @return string $weather_image_url.
	 */
	public function forecast_icon_url( $weather_icon, $icon_type ) {
		$renderer = new Render_Blocks_Templates();
		return $renderer->forecast_icon_url( $weather_icon, $icon_type );
	}

	/**
	 * Block template renderer.
	 * Provides compatibility for calling `self::block_template_renderer()` within this class.
	 *
	 * @since 3.2.0
	 *
	 * @param string  $template Template name to render.
	 * @param boolean $is_main_template check the template is main template.
	 */
	public function block_template_renderer( $template, $is_main_template = false ) {
		$renderer = new Render_Blocks_Templates();
		return $renderer->block_template_renderer( $template, $is_main_template );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function splw_ajax_block_data() {

		$nonce = isset( $_POST['splwBlockApiNonce'] ) ? sanitize_text_field( wp_unslash( $_POST['splwBlockApiNonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'splw_block_api_nonce' ) ) {
			return;
		}

		// This is a block-editor preview endpoint; require edit capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized', 'location-weather' ) ), 403 );
		}

		$query_data = isset( $_POST['weatherFormData'] ) ? sanitize_text_field( wp_unslash( $_POST['weatherFormData'] ) ) : '';
		$query_data = json_decode( $query_data );

		$is_editor  = true;
		$attributes = (array) $query_data;
		$block_name = $attributes['blockName'] ?? 'vertical';
		$skip_cache = true;
		include LOCATION_WEATHER_TEMPLATE_PATH . 'Blocks/Includes/weather-api-data.php';

		$response = array(
			'weather_data'  => $weather_data ?? null,
			'forecast_data' => $forecast_data ?? null,
			'aqi_data'      => $aqi_data ?? null,
			'unit'          => $measurement_units['weather_unit'] ?? null,
			'error_message' => $error_message,
		);
		wp_send_json_success( $response );
		wp_die();
	}

	/**
	 * Register category function.
	 *
	 * @param array  $categories Categories list.
	 * @param object $post Post.
	 * @return array
	 */
	public function lw_blocks_register_block_category( $categories, $post ) {
		return array_merge(
			array(
				array(
					'slug'  => 'location-weather',
					'title' => __( 'Location Weather', 'location-weather' ),
				),
				array(
					'slug'  => 'location-weather-pro-blocks',
					'title' => __( 'Location Weather Pro Blocks', 'location-weather' ),
				),
			),
			$categories
		);
	}

	/**
	 * Add `rand` as an option for orderby param in REST API.
	 * Hook to `rest_{$this->post_type}_collection_params` filter.
	 *
	 * @param array $query_params Accepted parameters.
	 * @return array
	 */
	public function add_rand_orderby_rest_post_collection_params( $query_params ) {
		$query_params['orderby']['enum'] = array_merge( $query_params['orderby']['enum'], array( 'rand', 'menu_order' ) );
		return $query_params;
	}
}
