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

use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Templates class for weather blocks.
 */
class Template_Parts {
	/**
	 * Method weather_description_html.
	 *
	 * @param array $attributes block attributes.
	 * @param array $weather_data weather api data.
	 *
	 * @return mixed
	 */
	public static function weather_description_html( $attributes, $weather_data ) {
		$show_description = $attributes['displayWeatherConditions'];
		if ( ! $show_description ) {
			return '';
		}

		ob_start()
		?>
			<div class="spl-weather-card-short-desc">
				<span class="spl-weather-desc">
					<?php echo esc_attr( $weather_data['desc'] ); ?>
				</span>
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Method slider_navigation_buttons
	 *
	 * @return mixed
	 */
	public static function custom_slider_navigation_buttons() {
		ob_start()
		?>
			<button class="spl-weather-custom-slider-nav spl-weather-custom-slider-nav-prev sp-cursor-pointer">
				<i class="splwp-icon-chevron"></i>
			</button>
			<button class="spl-weather-custom-slider-nav spl-weather-custom-slider-nav-next sp-cursor-pointer">
				<i class="splwp-icon-chevron right"></i>
			</button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Method weather_up_arrow
	 *
	 * @param string  $fill is svg icon fill.
	 * @param boolean $is_down_arrow is define that arrow is down or up.
	 * @param string  $size is arrow icon size.
	 * @return mixed
	 */
	public static function weather_up_arrow( $fill = '#2f2f2f', $is_down_arrow = false, $size = '24' ) {
		?>
			<span class="spl-weather-<?php echo $is_down_arrow ? 'down' : 'up'; ?>-arrow-icon sp-d-flex sp-align-i-center">
				<svg width="<?php echo esc_attr( $size ); ?>" height="<?php echo esc_attr( $size ); ?>" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M12.5622 6.31311L11.8551 7.02021L9.37536 4.54043L9.37536 13.3333H8.37536L8.37536 4.54043L5.89558 7.02021L5.18848 6.31311L8.87536 2.62622L12.5622 6.31311Z" fill="<?php echo esc_attr( $fill ); ?>"/>
				</svg>
			</span>
		<?php
	}
}
