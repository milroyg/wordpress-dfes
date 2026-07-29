<?php
/**
 * Weather Block AQI Template Renderer File.
 *
 * This template displays weather accordion layout.
 *
 * @since      3.2.0
 * @version    1.0.0
 *
 * @package Location_Weather_Pro/Blocks
 */

// Prevent direct file access for security.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$display_date_update_time  = $attributes['displayDateUpdateTime'] ?? true;
$card_forecast_style       = $attributes['aqiForecastStyle'] ?? 'list';
$show_pollutant            = $attributes['enablePollutantDetails'] ?? 'list';
$aqi_summary_heading_label = $attributes['aqiSummaryHeadingLabel'] ?? __( 'Today\'s Air Quality Index( AQI )', 'location-weather' );
$current_aqi_data          = $aqi_data->list[0]->components ?? array();
?>
<div class="spl-weather-aqi-minimal-card-wrapper spl-weather-template-wrapper sp-d-flex sp-flex-col sp-justify-center sp-<?php echo esc_attr( $template ); ?>-template">
	<?php
	require self::block_template_renderer( 'aqi-minimal/parts/summary.php', true );
	if ( $show_pollutant ) {
		require self::block_template_renderer( 'aqi-minimal/parts/pollutant.php', true );
	}
	?>
	<?php if ( $display_date_update_time ) : ?>
		<div class="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center has-padding">
			<div class="spl-weather-last-updated-time">
				Last updated: <?php echo esc_html( $weather_data['updated_time'] ?? '' ); ?>
			</div>
		</div>
	<?php endif; ?>
<?php require self::block_template_renderer( 'footer.php' ); ?>
</div>
