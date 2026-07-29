<?php
/**
 * Weather Block Register Class.
 *
 * @package    Location_Weather
 * @subpackage Blocks
 * @since      3.2.0
 * @version    1.0.0
 */

namespace ShapedPlugin\Weather\Blocks\Includes;

use ShapedPlugin\Weather\Blocks\Includes\Render_Blocks_Templates;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Register_Weather_Blocks
 */
class Weather_Block_Register {
	/**
	 * Convert object to array recursively.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Data to convert.
	 * @return array Converted array.
	 */
	public function object_to_array( $data ) {
		$result = array();
		foreach ( $data as $key => $value ) {
			$result[ $key ] = ( is_array( $value ) || is_object( $value ) ) ? $this->object_to_array( $value ) : $value;
		}
		return $result;
	}

	/**
	 * Method add_block_options_on_settings
	 *
	 * @param array $our_blocks .
	 *
	 * @return void
	 */
	private function add_block_settings_option( $our_blocks ) {
		$updated_options   = array_map(
			function ( $block ) {
				return array(
					'name' => $block['name'],
					'show' => true,
				);
			},
			$our_blocks
		);
		$updated_options[] = array(
			'name' => 'sp-location-weather-pro/shortcode',
			'show' => true,
		);

		// Fetch existing saved options.
		$existing_options = get_option( 'splw_blocks_visibility_options', array() );
		// insert if not exist on db.
		if ( empty( $existing_options ) ) {
			update_option( 'splw_blocks_visibility_options', $updated_options );
			return;
		}
		// if not changed anything return.
		if ( count( $existing_options ) === count( $updated_options ) ) {
			return;
		}
		// final options.
		$final_options = array();
		foreach ( $updated_options as $block ) {
			$existing_block  = wp_list_filter( $existing_options, array( 'name' => $block['name'] ) );
			$existing_block  = reset( $existing_block );
			$final_options[] = $existing_block ? $existing_block : $block;
		}
		update_option( 'splw_blocks_visibility_options', $final_options );
	}

	/**
	 * Register only active blocks.
	 *
	 * @param array $blocks Prepared blocks.
	 * @return void
	 */
	private function register_active_blocks( $blocks ) {
		$block_options = get_option( 'splw_blocks_visibility_options', array() );
		$block_options = $this->object_to_array( $block_options );
		$active_blocks = wp_list_pluck( $block_options, 'show', 'name' );

		foreach ( $blocks as $block ) {
			if ( ! empty( $active_blocks[ $block['name'] ] ) ) {
				register_block_type( $block['name'], $block['block_options'] );
			}
		}
	}

	/**
	 * Blocks register function.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	public function spl_weather_blocks_init() {
		$block_renderer = new Render_Blocks_Templates();
		include LOCATION_WEATHER_TEMPLATE_PATH . '/Blocks/Includes/block-attributes.php';

		// Block default options.
		$shared_options = array(
			'editor_script'   => array( 'spl_weather_editor_js' ),
			'editor_style'    => array( 'splw_index_editor_style' ),
			'style'           => array( 'splw_index_style' ),
			'script'          => array( 'spl-weather-block-script' ),
			'render_callback' => array( $block_renderer, 'render_weather_block_template' ),
		);

		// All blocks with attributes.
		$our_blocks = array(
			array(
				'name'       => 'sp-location-weather-pro/vertical-card',
				'attributes' => $vertical_block_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/horizontal',
				'attributes' => $horizontal_block_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/aqi-minimal',
				'attributes' => $aqi_minimal_card_block_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/grid',
				'attributes' => $grid_block_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/tabs',
				'attributes' => $tabs_block_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/table',
				'attributes' => $table_block_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/windy-map',
				'attributes' => $windy_map_block_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/combined',
				'attributes' => $block_required_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/aqi-detailed',
				'attributes' => $block_required_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/accordion',
				'attributes' => $block_required_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/map',
				'attributes' => $block_required_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/historical-weather',
				'attributes' => $block_required_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/historical-aqi',
				'attributes' => $block_required_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/sun-moon',
				'attributes' => $block_required_attributes,
			),
			array(
				'name'       => 'sp-location-weather-pro/section-heading',
				'attributes' => null,
			),
		);

		// Prepare block list for settings option.
		$prepared_blocks = array();
		foreach ( $our_blocks as $block ) {

			$block_options = $shared_options;

			if ( 'sp-location-weather-pro/section-heading' === $block['name'] ) {
				$block_options['attributes']      = null;
				$block_options['render_callback'] = null;
				$block_options['script']          = null;
				$block_options['style']           = null;
			} else {
				$block_options['attributes'] = $block['attributes'];
			}

			$prepared_blocks[] = array(
				'name'          => $block['name'],
				'block_options' => $block_options,
			);
		}

		// add registered block on settings option.
		$this->add_block_settings_option( $prepared_blocks );
		// register location weather blocks.
		$this->register_active_blocks( $prepared_blocks );
	}
}
