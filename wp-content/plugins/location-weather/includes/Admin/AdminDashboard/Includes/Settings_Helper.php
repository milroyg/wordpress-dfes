<?php
/**
 * Block settings helper functions.
 *
 * @package location-weather-pro
 */

namespace ShapedPlugin\Weather\Admin\AdminDashboard\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Settings_Helper
 */
class Settings_Helper {
	/**
	 * Our blocks names
	 *
	 * @var array
	 */
	public static $our_blocks_list = array(
		array(
			'name' => 'sp-location-weather-pro/vertical-card',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/horizontal',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/aqi-minimal',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/tabs',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/table',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/accordion',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/grid',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/combined',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/map',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/section-heading',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/windy-map',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/aqi-detailed',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/historical-weather',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/sun-moon',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/historical-aqi',
			'show' => true,
		),
		array(
			'name' => 'sp-location-weather-pro/shortcode',
			'show' => true,
		),
	);

	/**
	 * Method get_our_blocks_list
	 *
	 * @return array $block_list.
	 */
	public static function get_our_blocks_list() {
		$our_blocks_list = self::$our_blocks_list;
		$block_list      = array();
		foreach ( $our_blocks_list as $block ) {
			$block_list[] = $block['name'];
		}
		return $block_list;
	}
}
