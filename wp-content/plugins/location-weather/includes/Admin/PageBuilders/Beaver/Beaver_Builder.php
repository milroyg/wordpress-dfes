<?php
/**
 * Beaver Builder Integration
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

/**
 * Beaver Builder integration for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class Beaver_Builder {

	/**
	 * Initialize the integration.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_module' ) );
	}

	/**
	 * Register the FLBuilderModule.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function register_module() {
		if ( ! class_exists( 'FLBuilder' ) ) {
			return;
		}

		// Include the module class file.
		require_once __DIR__ . '/Beaver_Module.php';

		$helper = new Beaver_Helper();
		FLBuilder::register_module( 'SP_Location_Weather_Beaver', array(
			'name'        => esc_html__( 'Location Weather', 'location-weather' ),
			'description' => esc_html__( 'Display weather from saved templates', 'location-weather' ),
			'category'    => 'sp-plugins',
			'dir'         => LOCATION_WEATHER_PATH . 'includes/Admin/PageBuilders/',
			'url'         => LOCATION_WEATHER_URL . 'includes/Admin/PageBuilders/',
			'icon'        => 'location.svg',
			'settings'    => array(
				'template_id' => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Saved Template', 'location-weather' ),
					'options' => $helper->get_saved_templates_list(),
					'default' => '0',
				),
			),
		) );
	}
}

/**
 * Helper class for Beaver builder.
 *
 * @since 3.2.0
 */
class Beaver_Helper {
	use Base_Page_Builder;
}