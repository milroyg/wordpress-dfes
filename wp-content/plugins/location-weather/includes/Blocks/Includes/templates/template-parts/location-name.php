<?php
/**
 * Location Name Template
 *
 * This template displays the weather location name with optional icon.
 * The output varies based on whether the location was searched by name or coordinates.
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

// Validate required variables.
if ( ! $attributes['showLocationName'] ) {
	return;
}
$city               = $weather_data['city'] ?? '';
$country            = $weather_data['country'] ?? '';
$custom_city_name   = $attributes['customCityName'] ?? '';
$search_weather_by  = $attributes['searchWeatherBy'] ?? 'city_name';
$show_location_icon = 'vertical-three' === $template || 'horizontal' === $block_name;

?>
<div class="spl-weather-card-location-name<?php echo $show_location_icon ? ' sp-d-flex sp-align-i-center sp-gap-4px' : ''; ?>">
	<?php
	if ( $show_location_icon ) {
		echo '<i class="splwp-icon-location-icon-1"></i>';
	}
	?>
	<span class="spl-weather-country-city-name">
		<?php echo empty( $custom_city_name ) ? esc_html( $city . ', ' . $country ) : esc_html( $custom_city_name ); ?>
	</span>
</div>
