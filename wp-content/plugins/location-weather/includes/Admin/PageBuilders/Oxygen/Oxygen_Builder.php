<?php
/**
 * Oxygen Builder Integration
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Oxygen;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Oxygen Builder integration for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class Oxygen_Builder {

	/**
	 * Initialize the integration.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'oxygen_vsb_register_element', array( __CLASS__, 'register_element' ), 15, 1 );
	}

	/**
	 * Register the Oxygen element.
	 *
	 * @since 3.2.0
	 *
	 * @param array $class Existing Oxygen element classes.
	 * @return array Updated classes.
	 */
	public static function register_element( $class ) {
		$class['SP_Location_Weather_Oxygen'] = dirname( __FILE__ ) . '/Oxygen_Element.php';
		return $class;
	}
}