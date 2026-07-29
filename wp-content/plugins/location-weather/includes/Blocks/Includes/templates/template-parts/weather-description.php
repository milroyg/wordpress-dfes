<?php
/**
 * Short Description Template
 *
 * Displays the weather short description with proper escaping.
 *
 * @since      1.0.0
 * @version    1.0.0
 * @package    Location_Weather
 * @subpackage Location_Weather_Pro/Blocks/templates
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! $attributes['displayWeatherConditions'] ) {
	return;
}
?>
<div class="spl-weather-real-feel-desc-wrapper sp-d-flex sp-align-i-center sp-justify-center sp-gap-8px">
	<div class="spl-weather-card-short-desc">
		<span class="spl-weather-desc">
			<?php echo esc_attr( $weather_data['desc'] ); ?>
		</span>
	</div>
</div>


