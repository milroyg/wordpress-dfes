<?php
/**
 * Enhanced Elementor Integration
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Enhanced Elementor integration for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class Elementor_Builder {

	/**
	 * Initialize the integration.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widget' ) );
		add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'splw_block_enqueue_style' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( __CLASS__, 'splw_block_enqueue_script' ) );
	}

	/**
	 * Register the Location Weather widget in Elementor.
	 *
	 * @since 3.2.0
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 * @return void
	 */
	public static function register_widget( $widgets_manager ) {
		require_once __DIR__ . '/Elementor_Widget.php';
		$widgets_manager->register( new Elementor_Widget() );
	}

	/**
	 * Enqueue styles for Elementor preview.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function splw_block_enqueue_style() {
		// Check if already registered (might be registered by Blocks class).
		if ( ! wp_style_is( 'splw_index_style', 'registered' ) ) {
			wp_register_style(
				'splw_index_style',
				LOCATION_WEATHER_URL . '/includes/Blocks/build/style-index.css',
				array(),
				LOCATION_WEATHER_VERSION,
				'all'
			);
		}
		wp_enqueue_style( 'splw_index_style' );
	}

	/**
	 * Enqueue scripts for Elementor preview.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function splw_block_enqueue_script() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Main scripts from assets/js.
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

		// Swiper scripts.
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

		// Block scripts (for block-specific functionality).
		if ( ! wp_script_is( 'spl-weather-block-script', 'registered' ) ) {
			wp_register_script(
				'spl-weather-block-script',
				LOCATION_WEATHER_URL . '/includes/Blocks/assets/js/script' . $suffix . '.js',
				array(),
				LOCATION_WEATHER_VERSION,
				true
			);
		}
		wp_enqueue_script( 'spl-weather-block-script' );
	}
}
