<?php
/**
 * Weather Block Vertical Template Renderer File.
 *
 * This template displays weather vertical layout.
 *
 * @package    Location_Weather/Blocks
 * @since      3.2.0
 * @version    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$display_date_update_time = $attributes['displayDateUpdateTime'] ?? false;
$display_forecast_data    = $attributes['displayWeatherForecastData'] ?? true;
$forecast_display_style   = 'inline';


?>
<div class="spl-weather-template-wrapper spl-weather-<?php echo esc_attr( $template ); ?>-wrapper">
	<?php require self::block_template_renderer( 'current-weather.php' ); ?>
	<?php require self::block_template_renderer( 'additional-data/additional-data.php' ); ?>
	<?php
	if ( 'inline' === $forecast_display_style ) {
		require self::block_template_renderer( 'forecast-data/forecast-data.php' );
	}
	?>
	<?php require self::block_template_renderer( 'footer.php' ); ?>
</div>
