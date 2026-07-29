<?php
/**
 * Weather Card Footer Template
 *
 * Displays footer content including detailed weather link, update time, and attribution.
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

$display_weather_attribution = $attributes['displayWeatherAttribution'] ?? false;

if ( ! $display_weather_attribution ) {
	return;
}

$city_id                = $weather_data['city_id'] ?? '';
$link_to_openweathermap = $attributes['displayLinkToOpenWeatherMap'] ?? false;

$footer_data = array(
	'openweather_api' => array(
		'text' => __( 'Weather from OpenWeather', 'location-weather' ),
		'url'  => 'https://openweathermap.org/',
	),
	'weather_api'     => array(
		'text' => __( 'Weather from WeatherAPI', 'location-weather' ),
		'url'  => 'https://www.weatherapi.com/',
	),
);

$footer_text = $footer_data[ $api_source ]['text'] ?? __( 'Weather from OpenWeather', 'location-weather' );
$footer_url  = $footer_data[ $api_source ]['url'] ?? 'https://openweathermap.org/';

?>
<div class="spl-weather-card-footer">
	<?php if ( $display_weather_attribution ) : ?>
		<div class="spl-weather-attribution sp-text-align-center">
			<?php if ( $link_to_openweathermap ) : ?>
				<a href="<?php echo esc_url( $footer_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html( $footer_text ); ?>
				</a>
			<?php else : ?>
				<span><?php echo esc_html( $footer_text ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
