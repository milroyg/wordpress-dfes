<?php
/**
 * Weather AQI Block Template summary location name and duration File.
 *
 * @package Location_Weather_Pro/Blocks
 */

// Prevent direct file access for security.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_current_date    = $attributes['showCurrentDate'] ?? true;
$show_current_time    = $attributes['showCurrentTime'] ?? true;
$show_location_name   = $attributes['showLocationName'] ?? true;
$custom_location_name = $attributes['customCityName'] ?? '';
$city_name            = ! empty( $custom_location_name ) && ! $visitors_location ? $custom_location_name : $weather_data['city'] . ', ' . $weather_data['country'];
$city_name            = apply_filters( 'splw_city_name', $city_name, $shortcode_id );

?>
<div class="spl-aqi-card-location-time sp-d-flex sp-gap-4px">
	<?php if ( $show_location_name ) : ?>
	<div class="spl-weather-card-location-name">
		<span class="spl-weather-country-city-name"><?php echo esc_html( $city_name ); ?></span>
	</div>
	<?php endif; ?>
	<?php if ( $show_current_time || $show_current_date ) : ?>
	<div class="spl-weather-card-date-time sp-d-flex sp-align-i-center">
		<?php if ( $show_current_time ) : ?>
		<span class="spl-weather-current-time">
			<?php echo esc_html( $weather_data['time'] ); ?>
		</span>
		<?php endif; ?>
		<?php if ( $show_current_date ) : ?>
		,<span class="spl-weather-date"> 
			<?php echo esc_html( $weather_data['date'] ); ?>
		</span>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>