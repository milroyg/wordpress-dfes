<?php
/**
 * WPBakery Integration
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\WPBakery;

use ShapedPlugin\Weather\Admin\PageBuilders\Base\Base_Page_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WPBakery integration for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class WPBakery_Builder {

	/**
	 * Initialize the integration.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'vc_before_init', array( __CLASS__, 'register_shortcode' ) );
		add_shortcode( 'vc_location_weather', array( __CLASS__, 'render' ) );
	}

	/**
	 * Register the WPBakery element.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function register_shortcode() {
		if ( ! function_exists( 'vc_map' ) ) {
			return;
		}

		$helper = new WPBakery_Helper();
		vc_map( array(
			'name'     => esc_html__( 'Location Weather', 'location-weather' ),
			'base'     => 'vc_location_weather',
			'category' => esc_html__( 'ShapedPlugin', 'location-weather' ),
			'icon'     => 'icon-wpb-layer-shape-text',
			'params'   => array(
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Saved Template', 'location-weather' ),
					'param_name'  => 'template_id',
					'value'       => $helper->get_saved_templates_list(),
					'admin_label' => true,
					'description' => esc_html__( 'Select a saved weather template', 'location-weather' ),
				),
			),
		) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @since 3.2.0
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Rendered content.
	 */
	public static function render( $atts ) {
		$atts = shortcode_atts( array(
			'template_id' => '0',
		), $atts );

		$template_id = (int) $atts['template_id'];
		$is_editor   = function_exists( 'vc_is_frontend_editor' ) && vc_is_frontend_editor();

		$helper = new WPBakery_Helper();
		return $helper->render_template( $template_id, $is_editor );
	}
}

/**
 * Helper class for WPBakery builder.
 *
 * @since 3.2.0
 */
class WPBakery_Helper {
	use Base_Page_Builder;
}