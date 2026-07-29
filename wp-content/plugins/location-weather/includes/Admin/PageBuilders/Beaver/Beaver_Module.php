<?php
/**
 * Beaver Builder Module for Location Weather
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Beaver;

use ShapedPlugin\Weather\Admin\PageBuilders\Base\Base_Page_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'FLBuilderModule' ) ) {
	return;
}

/**
 * Beaver Builder module for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class SP_Location_Weather_Beaver extends \FLBuilderModule {

	/**
	 * Constructor.
	 *
	 * @since 3.2.0
	 */
	public function __construct() {
		parent::__construct( array(
			'name'        => esc_html__( 'Location Weather', 'location-weather' ),
			'description' => esc_html__( 'Display weather from saved templates', 'location-weather' ),
			'category'    => 'sp-plugins',
			'dir'         => LOCATION_WEATHER_PATH . 'includes/Admin/PageBuilders/',
			'url'         => LOCATION_WEATHER_URL . 'includes/Admin/PageBuilders/',
		) );
	}

	/**
	 * Render the module.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public function render() {
		$template_id = isset( $this->settings->template_id ) ? (int) $this->settings->template_id : 0;
		$is_editor   = method_exists( 'FLBuilderModel', 'is_builder_active' ) && \FLBuilderModel::is_builder_active();

		$base   = new Beaver_Helper();
		$result = $base->render_template( $template_id, $is_editor );
		echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Helper class for Beaver module.
 *
 * @since 3.2.0
 */
class Beaver_Helper {
	use Base_Page_Builder;
}