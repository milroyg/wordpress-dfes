<?php
/**
 * Oxygen Element for Location Weather
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Oxygen;

use ShapedPlugin\Weather\Admin\PageBuilders\Base\Base_Page_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'OxyEl' ) ) {
	return;
}

/**
 * Oxygen element for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class SP_Location_Weather_Oxygen extends \OxyEl {

	/**
	 * Element name.
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	public function name() {
		return esc_html__( 'Location Weather', 'location-weather' );
	}

	/**
	 * Element slug.
	 *
	 * @since 3.2.0
	 *
	 * @return string
	 */
	public function slug() {
		return 'sp-location-weather';
	}

	/**
	 * Element controls.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public function controls() {
		$base = new Base_Helper();
		$this->addOptionControl(
			array(
				'type'    => 'dropdown',
				'name'    => esc_html__( 'Saved Template', 'location-weather' ),
				'slug'    => 'template_id',
				'value'   => $base->get_saved_templates_list(),
				'default' => '0',
			)
		);
	}

	/**
	 * Render element.
	 *
	 * @since 3.2.0
	 *
	 * @param array $options   Element options.
	 * @param array $defaults  Default values.
	 * @param array $content   Element content.
	 * @return void
	 */
	public function render( $options, $defaults, $content ) {
		$template_id = isset( $options['template_id'] ) ? (int) $options['template_id'] : 0;
		$is_editor   = defined( 'SHOW_CT_BUILDER' ) && SHOW_CT_BUILDER;

		$base   = new Base_Helper();
		$result = $base->render_template( $template_id, $is_editor );
		echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Helper class for Oxygen element.
 *
 * @since 3.2.0
 */
class Base_Helper {
	use Base_Page_Builder;
}
