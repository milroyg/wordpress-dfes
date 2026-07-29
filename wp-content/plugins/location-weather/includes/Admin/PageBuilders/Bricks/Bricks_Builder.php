<?php
/**
 * Bricks Builder Integration
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Bricks;

use ShapedPlugin\Weather\Admin\PageBuilders\Base\Base_Page_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bricks Builder integration for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class Bricks_Builder {

	/**
	 * Initialize the integration.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'bricks/elements', array( __CLASS__, 'register_element' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Register the Location Weather element in Bricks.
	 *
	 * @since 3.2.0
	 *
	 * @param array $elements Existing elements.
	 * @return array Updated elements.
	 */
	public static function register_element( $elements ) {
		$helper = new Bricks_Helper();
		$elements['location-weather'] = array(
			'name'        => 'location-weather',
			'label'       => esc_html__( 'Location Weather', 'location-weather' ),
			'icon'        => 'ti-alarm-clock',
			'controls'    => array(
				'template_id' => array(
					'label'       => esc_html__( 'Saved Template', 'location-weather' ),
					'type'        => 'select',
					'options'     => $helper->get_saved_templates_list(),
					'default'     => '0',
					'description' => esc_html__( 'Select a saved weather template to display', 'location-weather' ),
				),
			),
			'render_callback' => array( __CLASS__, 'render' ),
		);

		return $elements;
	}

	/**
	 * Render the element.
	 *
	 * @since 3.2.0
	 *
	 * @param array $props Element properties.
	 * @return void
	 */
	public static function render( $props ) {
		$template_id = isset( $props['template_id'] ) ? (int) $props['template_id'] : 0;
		$is_editor   = function_exists( 'bricks_is_builder_iframe' ) && bricks_is_builder_iframe();

		$helper = new Bricks_Helper();
		$result = $helper->render_template( $template_id, $is_editor );
		echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( 'splw-styles' );
		wp_enqueue_script( 'splw-scripts' );
	}
}

/**
 * Helper class for Bricks element.
 *
 * @since 3.2.0
 */
class Bricks_Helper {
	use Base_Page_Builder;
}