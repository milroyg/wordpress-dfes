<?php

namespace MasterAddons\Inc\Classes;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor Backward Compatibility
 *
 * Registers stub classes for deprecated Elementor Scheme_Color and Scheme_Typography
 * that were removed in Elementor 3.15+. Prevents fatal errors from third-party
 * themes/plugins (e.g. Brooklyn Lite) that still reference them.
 */
class Elementor_Compat {

	/**
	 * Register deprecated Elementor class stubs if missing.
	 */
	public static function init() {
		// Scheme_Color
		if ( ! class_exists( '\Elementor\Scheme_Color' ) ) {
			if ( class_exists( '\Elementor\Core\Schemes\Color' ) ) {
				class_alias( '\Elementor\Core\Schemes\Color', '\Elementor\Scheme_Color' );
			} else {
				class_alias( __NAMESPACE__ . '\Elementor_Scheme_Color_Stub', '\Elementor\Scheme_Color' );
			}
		}

		// Scheme_Typography
		if ( ! class_exists( '\Elementor\Scheme_Typography' ) ) {
			if ( class_exists( '\Elementor\Core\Schemes\Typography' ) ) {
				class_alias( '\Elementor\Core\Schemes\Typography', '\Elementor\Scheme_Typography' );
			} else {
				class_alias( __NAMESPACE__ . '\Elementor_Scheme_Typography_Stub', '\Elementor\Scheme_Typography' );
			}
		}
	}
}

/**
 * Minimal stub for Elementor\Scheme_Color (removed in Elementor 3.15+).
 *
 * @internal
 */
class Elementor_Scheme_Color_Stub {
	const COLOR_1 = '1';
	const COLOR_2 = '2';
	const COLOR_3 = '3';
	const COLOR_4 = '4';

	public static function get_type() {
		return 'color';
	}
}

/**
 * Minimal stub for Elementor\Scheme_Typography (removed in Elementor 3.15+).
 *
 * @internal
 */
class Elementor_Scheme_Typography_Stub {
	const TYPOGRAPHY_1 = '1';
	const TYPOGRAPHY_2 = '2';
	const TYPOGRAPHY_3 = '3';
	const TYPOGRAPHY_4 = '4';

	public static function get_type() {
		return 'typography';
	}
}
