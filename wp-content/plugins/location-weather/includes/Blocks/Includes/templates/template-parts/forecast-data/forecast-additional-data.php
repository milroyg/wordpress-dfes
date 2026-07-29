<?php
/**
 * Forecast Additional Data Renderer.
 *
 * This file will render all the forecast additional data (like precipitation, sunrise, sunset, uv-index).
 *
 * @since      1.0.0
 * @version    1.0.0
 * @package    Location_Weather
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$rain_chance = $single_forecast['rain'];
$wind        = $single_forecast['wind'];
$wind_gust   = $single_forecast['gusts'];
$uv_index    = $single_forecast['uvi'];
$humidity    = $single_forecast['humidity'];
$pressure    = $single_forecast['pressure'];
?>

<div class="spl-weather-forecast-details sp-d-flex sp-justify-center">
	<?php foreach ( $forecast_options as $option ) : ?>
		<div class="spl-weather-forecast-card sp-d-flex sp-flex-col sp-justify-center sp-align-i-center sp-gap-4px">
			<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
				<i class="<?php echo esc_attr( $block_weather_icons[ $option ] ); ?>"></i>
			</span>
			<span class="spl-weather-forecast-details-label">
				<?php echo esc_html( $weather_item_labels[ $option ] ); ?>
			</span>
			<span class="spl-weather-forecast-details-value">
				<?php if ( in_array( $option, array( 'precipitation', 'sunrise_time', 'sunset_time', 'clouds', 'pressure', 'snow' ), true ) ) : ?>
					<?php echo esc_html( $single_forecast[ $option ] ); ?>
					<?php echo 'clouds' === $option ? '%' : ''; ?>
				<?php elseif ( 'rainchance' === $option ) : ?>
				<span class="spl-weather-forecast-value rainchance">
					<?php echo esc_html( $rain_chance ); ?>
				</span>
				<?php elseif ( 'uv_index' === $option ) : ?>
				<span class="spl-weather-forecast-value uv_index">
					<?php echo esc_html( $uv_index ); ?>
				</span>
				<?php elseif ( 'wind' === $option ) : ?>
				<span class="spl-weather-forecast-value wind">
					<?php echo wp_kses_post( $wind ); ?> 
				</span>
				<?php elseif ( 'gust' === $option ) : ?>
				<span class="spl-weather-forecast-value gusts">
					<?php echo wp_kses_post( $wind_gust ); ?>
				</span>
				<?php elseif ( 'humidity' === $option ) : ?>
				<span class="spl-weather-forecast-value humidity">
					<?php echo esc_html( $humidity->value . ' ' . $humidity->unit ); ?>
				</span>
				<?php endif; ?>
			</span>
		</div>
	<?php endforeach; ?>
</div>
