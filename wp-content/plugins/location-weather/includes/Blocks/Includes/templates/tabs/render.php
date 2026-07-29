<?php
/**
 * Weather Block Tabs Template Renderer File.
 *
 * This template displays weather tabs layout.
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

$default_active_tab       = $attributes['splwDefaultOpenTab'];
$hourly_title             = $attributes['hourlyTitle'] ?? 'Hourly Forecast';
$weather_forecast_type    = 'hourly';
$splw_tab_alignment       = $attributes['splwTabAlignment'] ?? 'start';
$display_weather_forecast = $attributes['displayWeatherForecastData'] ?? true;
$display_weather_map      = $attributes['displayWeatherMap'] ?? false;
$displayed_map_type       = 'windy-map';

$current_weather = array(
	'label' => __( 'Current Weather', 'location-weather' ),
	'value' => 'current_weather',
);

$nav_options = array(
	'hourly' => array(
		$current_weather,
		array(
			'label' => $hourly_title,
			'value' => 'hourly',
		),
	),
);

$tab_nav_options = array();

if ( $display_weather_forecast && isset( $nav_options['hourly'] ) ) {
	$tab_nav_options = array_merge( $tab_nav_options, $nav_options['hourly'] );
}
if ( $display_weather_map ) {
	$tab_nav_options[] = array(
		'label' => __( 'Radar Map', 'location-weather' ),
		'value' => 'map',
	);
}

$alignment_class = 'between' === $splw_tab_alignment
	? 'sp-flex-col'
	: 'sp-justify-' . $splw_tab_alignment;

$forecast_type            = 'hourly';
$display_date_update_time = $attributes['displayDateUpdateTime'];
?>
<div class="spl-weather-template-wrapper">
	<div class="spl-weather-<?php echo esc_attr( $template ); ?>-block-wrapper">
		<div class="spl-weather-tabs-navigation-wrapper sp-w-full">
			<ul class="spl-weather-tab-navs spl-weather-tabs-group">
				<?php foreach ( $tab_nav_options as $option ) : ?>
					<li class="spl-weather-tab-nav spl-weather-tab-btn<?php echo ( $default_active_tab === $option['value'] ) ? ' active' : ''; ?>" data-tab="<?php echo esc_attr( $option['value'] ); ?>">
						<?php echo esc_html( $option['label'] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="spl-weather-tabs-block-content">
			<div class="spl-weather-tab-pane sp-w-full <?php echo ( 'current_weather' === $default_active_tab ) ? 'active' : ''; ?>" id="current_weather">
				<div class="spl-weather-current-data sp-d-flex sp-justify-between">
					<?php require self::block_template_renderer( 'current-weather.php' ); ?>
					<?php if ( 'tabs-one' === $template ) : ?>
						<?php require self::block_template_renderer( 'sun-orbit.php' ); ?>
					<?php endif; ?>
				</div>
				<?php require self::block_template_renderer( 'additional-data/additional-data.php' ); ?>
				<?php if ( $display_date_update_time ) : ?>
					<div class="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
						<div class="spl-weather-last-updated-time">
							Last updated: <?php echo esc_html( $weather_data['updated_time'] ?? '' ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php require self::block_template_renderer( 'footer.php' ); ?>
			</div>
			<?php
			if ( $enable_forecasting_days ) {
				?>
				<div class="spl-weather-tab-pane sp-w-full <?php echo ( 'hourly' === $default_active_tab ) ? 'active' : ''; ?>" id="hourly">
					<?php
					$forecast_key = 'hourly';
					require self::block_template_renderer( 'forecast-data/forecast-table.php' );
					?>
				</div>
				<?php
			}
			?>
			<?php
			if ( $display_weather_map ) {
				?>
					<div class="spl-weather-tab-pane sp-w-full <?php echo ( 'map' === $default_active_tab ) ? 'active' : ''; ?>" id="map">
						<?php require self::block_template_renderer( 'maps/render-maps.php' ); ?>
					</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
