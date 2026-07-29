<?php
/**
 * Weather Block Grid One Template Renderer File.
 *
 * @since      3.2.0
 * @version    1.0.0
 *
 * @package Location_Weather_Pro/Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( 'grid-one' !== $template ) {
	return;
}
use ShapedPlugin\Weather\Frontend\Shortcode;

$display_forecast  = $attributes['displayWeatherForecastData'];
$selected_forecast = 'hourly';

?>

<div class="spl-weather-map-and-current-weather sp-d-flex sp-w-full">
	<?php require self::block_template_renderer( 'current-weather-card.php' ); ?>
	<?php require self::block_template_renderer( 'maps/render-maps.php' ); ?>
</div>
<div class="spl-weather-grid-card-tabs-forecast splwb-forecast">
	<?php
		$forecast_type = 'hourly';
		require self::block_template_renderer( 'forecast-data/forecast-header.php' );
	?>
	<?php
		$data_type              = 'hourly';
		$active_forecast_layout = 'normal';
		$each_forecast_array    = $forecast_data;
		$is_both_forecast       = false;
		require self::block_template_renderer( 'forecast-data/regular-layout.php' );
	?>
</div>
	