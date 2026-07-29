<?php
/**
 * Forecast Data Renderer.
 *
 * Displays the forecast data options and layout variations.
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

if ( ! $attributes['displayWeatherForecastData'] ) {
	return;
}
use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$forecast_type    = 'hourly';
$forecast_options = Block_Helpers::process_forecast_data_options( $attributes['forecastData'] );
$active_forecast  = $forecast_options[0];

$active_forecast_layout   = in_array(
	$template,
	array(
		'vertical-one',
		'vertical-three',
	),
	true
) ? 'normal' : 'swiper';
$display_data_update_time = $attributes['displayDateUpdateTime'] ?? false;
$forecast_display_style   = 'inline';
$show_data_update_time    = $display_data_update_time && 'vertical' === $block_name && 'inline' === $forecast_display_style;

?>
<div class="spl-weather-card-forecast-data splwb-forecast">
	<?php require self::block_template_renderer( 'forecast-data/forecast-header.php' ); ?>
	<!-- forecast data -->
		<?php
			$data_type           = $forecast_type;
			$each_forecast_array = $forecast_data;
			require self::block_template_renderer( 'forecast-data/regular-layout.php' );
		?>
		<?php
			$data_type           = $forecast_type;
			$each_forecast_array = $forecast_data;
			require self::block_template_renderer( 'forecast-data/swiper-layout.php' );
		?>
	<?php
	if ( $show_data_update_time ) {
		?>
		<div class="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
			<div class="spl-weather-last-updated-time">
				Last updated: <?php echo esc_html( $weather_data['updated_time'] ?? '' ); ?>
			</div>
		</div>
		<?php
	}
	?>
</div>
