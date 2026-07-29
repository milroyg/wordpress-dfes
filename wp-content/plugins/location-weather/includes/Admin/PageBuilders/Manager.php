<?php
/**
 * Page Builder Integration Manager
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Page builder integrations manager.
 *
 * Conditionally loads integrations based on:
 * 1. Whether the page builder is active
 * 2. User's integration settings
 *
 * @since 3.2.0
 */
class Manager {

	/**
	 * Get available integrations.
	 *
	 * @since 3.2.0
	 *
	 * @return array Integration key => class name.
	 */
	private static function get_integrations() {
		return array(
			'elementor' => Elementor\Elementor_Builder::class,
			'bricks'    => Bricks\Bricks_Builder::class,
			'beaver'    => Beaver\Beaver_Builder::class,
			'divi'      => Divi\Divi_Builder::class,
			'oxygen'    => Oxygen\Oxygen_Builder::class,
			'wpbakery'  => WPBakery\WPBakery_Builder::class,
		);
	}

	/**
	 * Check if a page builder is active.
	 *
	 * @since 3.2.0
	 *
	 * @param string $builder Builder key.
	 * @return bool
	 */
	private static function is_builder_active( $builder ) {
		switch ( $builder ) {
			case 'elementor':
				return defined( 'ELEMENTOR_VERSION' );
			case 'divi':
				return defined( 'ET_BUILDER_VERSION' );
			// case 'bricks':
			// return class_exists( 'Bricks\Frontend' );
			// case 'beaver':
			// return class_exists( 'FLBuilder' );
			// case 'oxygen':
			// return defined( 'CT_VERSION' );
			// case 'wpbakery':
			// return defined( 'WPB_VC_VERSION' );
			default:
				return false;
		}
	}

	/**
	 * Check if an integration is enabled in settings.
	 *
	 * @since 3.2.0
	 *
	 * @param string $builder Builder key.
	 * @return bool
	 */
	private static function is_integration_enabled( $builder ) {
		$options = get_option( 'splw_integrations_options', array() );

		// Default to enabled if no setting exists.
		if ( empty( $options ) ) {
			return true;
		}

		// Find the integration by id.
		foreach ( $options as $option ) {
			if ( isset( $option['id'] ) && $option['id'] === $builder ) {
				return isset( $option['enabled'] ) ? (bool) $option['enabled'] : true;
			}
		}

		// Default to enabled if integration not found in options.
		return true;
	}

	/**
	 * Initialize all integrations.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function init() {
		$integrations = self::get_integrations();

		foreach ( $integrations as $key => $class ) {
			// For Divi, delay check to init hook since Divi loads late.
			if ( 'divi' === $key ) {
				add_action( 'init', array( __CLASS__, 'check_and_load_divi' ), 20 );
				continue;
			}

			// Check if page builder is active.
			if ( ! self::is_builder_active( $key ) ) {
				continue;
			}

			// Check if integration is enabled in settings.
			if ( ! self::is_integration_enabled( $key ) ) {
				continue;
			}

			// Load the integration.
			if ( class_exists( $class ) ) {
				$class::init();
			}
		}
	}

	/**
	 * Check and load Divi integration on init hook.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public static function check_and_load_divi() {
		$class = Divi\Divi_Builder::class;

		// Check if integration is enabled in settings.
		if ( ! self::is_integration_enabled( 'divi' ) ) {
			return;
		}

		// Check if Divi is active on init hook.
		if ( ! defined( 'ET_BUILDER_VERSION' ) && ! class_exists( 'ET_Builder_Module' ) ) {
			return;
		}

		// Load the integration.
		if ( class_exists( $class ) ) {
			$class::init();
		}
	}
}
