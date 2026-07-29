<?php
/**
 * Weather Block Template Renderer Class.
 *
 * This template displays all weather layout for a specific location.
 *
 * @package    Location_Weather
 * @subpackage Blocks
 * @since      3.2.0
 * @version    3.2.0
 */

namespace ShapedPlugin\Weather\Blocks\Includes;

use ShapedPlugin\Weather\License;
use ShapedPlugin\Weather\Frontend\Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Templates class for weather blocks.
 */
class Render_Blocks_Templates {
	/**
	 * Gets the icon folder name based on the type.
	 *
	 * @param string $weather_icon The icon set type.
	 * @param string $icon_type The folder name for the icon set.
	 * @return string $weather_image_url.
	 */
	public function forecast_icon_url( $weather_icon, $icon_type ) {
		$folder_names      = array(
			'forecast_icon_set_one'   => 'weather-icons',
			'forecast_icon_set_two'   => 'weather-static-icons',
			'forecast_icon_set_three' => 'light-line',
			'forecast_icon_set_four'  => 'fill-icon',
			'forecast_icon_set_five'  => 'weather-glassmorphism',
			'forecast_icon_set_six'   => 'animated-line',
			'forecast_icon_set_seven' => 'animated',
			'forecast_icon_set_eight' => 'medium-line',
		);
		$folder_name       = $folder_names[ $icon_type ] ?? 'weather-icons';
		$weather_image_url = LOCATION_WEATHER_URL . '/assets/images/icons/' . $folder_name . '/' . $weather_icon . '.svg';
		return $weather_image_url;
	}

	/**
	 * Block template renderer.
	 *
	 * @since 3.2.0
	 *
	 * @param string  $template Template name to render.
	 * @param boolean $is_main_template check the template is main template.
	 * @return string Template path if found, empty string otherwise.
	 */
	public function block_template_renderer( $template, $is_main_template = false ) {
		$main_url          = LOCATION_WEATHER_TEMPLATE_PATH . '/Blocks/Includes/templates/';
		$template_folder   = $is_main_template ? '' : 'template-parts/';
		$selected_template = $main_url . $template_folder . $template;

		if ( $template && file_exists( $selected_template ) ) {
			return $selected_template;
		}
		return '';
	}


	/**
	 * Renders the weather block template.
	 *
	 * @since 3.2.0
	 * @param array  $attributes {
	 *      Block attributes.
	 *
	 *     @type string $blockName Block name.
	 *     @type string $uniqueId  Unique identifier.
	 *     @type string $align     Block alignment.
	 *     @type string $$content  Inner blocks.
	 * }
	 * @param string $content is block markup.
	 * @return string Block content.
	 */
	public function render_weather_block_template( $attributes, $content ) {
		$block_name           = $attributes['blockName'];
		$template             = $attributes['template'];
		$unique_id            = $attributes['uniqueId'];
		$align                = $attributes['align'];
		$custom_class         = $attributes['customClassName'] ?? '';
		$location_auto_detect = false;
		// include api data file.
		include LOCATION_WEATHER_TEMPLATE_PATH . 'Blocks/Includes/weather-api-data.php';

		// weather based image .
		$wrapper_image = '';
		$error_message = $error_message ?? false;

		// $weather_based_image     = '';
		// $bg_img_supported_blocks = array( 'vertical', 'horizontal', 'grid', 'accordion', 'combined' );
		// if ( in_array( $block_name, $bg_img_supported_blocks, true ) ) {
		// $bg_color_type       = $attributes['bgColorType'];
		// $bg_image_type       = $attributes['imageType'];
		// $weather_based_image = 'image' === $bg_color_type && 'weather-based' === $bg_image_type
		// ? ' weather-status-' . $weather_data['icon'] : '';
		// $wrapper_image       = in_array( $block_name, array( 'vertical', 'horizontal' ), true ) ? $weather_based_image : '';
		// }

		if ( $error_message ) {
			$api_error_message = sprintf(
				'<div id="%s" class="%s">%s</div>',
				esc_attr( $unique_id ),
				esc_attr( 'spl-weather-' . $block_name . '-card sp-location-weather-block-wrapper spl-weather-api-error align' . $align ),
				$error_message
			);
			return $api_error_message;
		}
		ob_start();
		if ( $block_name ) {
			?>
		<div id="<?php echo esc_attr( $unique_id ); ?>" class="spl-weather-<?php echo esc_attr( $block_name . '-card sp-location-weather-block-wrapper align' . $align . $wrapper_image ); ?> <?php echo esc_attr( $custom_class ); ?>">
			<?php
			require self::block_template_renderer( 'preloader.php' );
			?>
			<?php require self::block_template_renderer( $block_name . '/render.php', true ); ?>
		</div>
			<?php
		}
		return ob_get_clean();
	}
}
