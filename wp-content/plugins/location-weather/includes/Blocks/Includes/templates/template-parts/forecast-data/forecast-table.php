<?php
/**
 * Weather Forecast For Tabs and Table Block.
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

if ( ! $attributes['displayWeatherForecastData'] ) {
	return;
}
use ShapedPlugin\Weather\Frontend\Shortcode;
use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$table_forecast = $forecast_data;

if ( empty( $table_forecast ) || ! $table_forecast ) {
	return;
}

$forecast_options = Block_Helpers::process_forecast_data_options( $attributes['forecastData'] );
$extra_option     = array( 'daily' === $forecast_key ? 'day' : 'hour', 'weather' );
$forecast_options = array_merge(
	$extra_option,
	$forecast_options,
);

$is_show_table_top = in_array( $template, array( 'table-one' ), true );
require self::block_template_renderer( 'additional-data/weather-icons.php' );

?>

<div class="spl-weather-forecast-table-layout">
	<?php if ( 'table' === $block_name ) : ?>
	<div class="spl-weather-table-forecast-title">
		<?php echo esc_html__( 'Hourly Forecast', 'location-weather' ); ?> 
	</div>
	<?php endif ?>
	<table class="spl-weather-forecast-table sp-w-full">
	<!-- table header -->
		<thead>
			<?php
			if ( $is_show_table_top ) {
				?>
					<tr class="spl-weather-table-top-header">
						<th>
							<?php echo esc_html( __( 'Time', 'location-weather' ) ); ?>
						</th>
						<th colspan="2">
							<?php echo esc_html( __( 'Weather Condition', 'location-weather' ) ); ?>
						</th>
						<th colspan="3">
							<?php echo esc_html( __( 'Comport', 'location-weather' ) ); ?>
						</th>
						<th colspan="3">
							<?php echo esc_html( __( 'Precipitation', 'location-weather' ) ); ?>
						</th>
					</tr>
				<?php
			}
			?>
			<tr class="spl-weather-table-header">
				<?php
				foreach ( $forecast_options as $option ) {
					$label = $weather_item_labels[ $option ] ?? '';
					$icon  = $block_weather_icons[ $option ] ?? null;
					?>
						<th scope="col">
							<?php if ( $icon ) : ?>
								<span class="spl-weather-details-icon">
									<i class="<?php echo esc_html( $icon ); ?>"></i>
								</span>
							<?php endif; ?>
							<?php
							if ( $is_show_table_top && 'precipitation' === $option ) {
								echo esc_html( $weather_item_labels['amount'] );
							} else {
								echo esc_html( $label );
							}
							?>
						</th>
					<?php
				}
				?>
			</tr>
		</thead>
	<!-- table body -->
		<tbody>
			<?php
			foreach ( $table_forecast as $index => $single_forecast ) {
				$single_forecast     = Shortcode::get_forecast_data( $single_forecast, $measurement_units, $time_settings, true );
				$wind_direction_icon = Block_Helpers::get_wind_direction_icon( $single_forecast );
				?>
					<tr class="spl-weather-forecast-table-row">
						<!-- forecast date -->
						<td class="spl-weather-table-forecast-date">
							<?php
								$data_type       = $forecast_key;
								$is_layout_three = false;
								require self::block_template_renderer( 'forecast-data/forecast-date.php' );
							?>
						</td>
						<!-- forecast conditions -->
						<td class="spl-weather-table-forecast-weather">
							<?php
							$show_description = true;
							require self::block_template_renderer( 'forecast-data/forecast-image.php' );
							?>
						</td>
						<?php
						foreach ( $forecast_options as $option ) {
							?>
							<?php if ( 'temperature' === $option ) : ?>
							<!-- forecast temperature -->
							<td class="spl-weather-table-forecast-temperature">
								<?php require self::block_template_renderer( 'forecast-data/forecast-temp.php' ); ?>
							</td>
							<?php elseif ( 'precipitation' === $option ) : ?>
							<!-- forecast precipitation -->
							<td class="spl-weather-table-forecast-precipitation">
								<span class="spl-weather-forecast-value sp-d-flex sp-align-i-center">
									<?php echo esc_html( $single_forecast['precipitation'] ); ?>
								</span>
							</td>
							<?php elseif ( 'rainchance' === $option ) : ?>
							<!-- forecast rainchance -->
							<td class="spl-weather-table-forecast-rainchance">
								<span class="spl-weather-forecast-value sp-d-flex sp-align-i-center">
									<?php echo esc_html( $single_forecast['rain'] ); ?>
								</span>
							</td>
							<?php elseif ( 'wind' === $option ) : ?>
							<!-- forecast wind -->
							<td class="spl-weather-table-forecast-wind">
								<span class="spl-weather-forecast-value sp-d-flex sp-align-i-center">
				    	    		<?php echo wp_kses_post( $single_forecast['wind'] ) . $wind_direction_icon; // phpcs:ignore ?> 
								</span>
							</td>
							<?php elseif ( 'humidity' === $option ) : ?>
							<!-- forecast humidity -->
							<td class="spl-weather-table-forecast-humidity">
								<span class="spl-weather-forecast-value sp-d-flex sp-align-i-center">
									<?php echo esc_html( $single_forecast['humidity'] ); ?>
								</span>
							</td>
							<?php elseif ( 'pressure' === $option ) : ?>
							<!-- forecast pressure -->
							<td class="spl-weather-table-forecast-pressure">
								<span class="spl-weather-forecast-value sp-d-flex sp-align-i-center">
									<?php echo esc_html( $single_forecast['pressure'] ); ?>
								</span>
							</td>
							<?php elseif ( 'snow' === $option ) : ?>
							<!-- forecast pressure -->
							<td class="spl-weather-table-forecast-pressure">
								<span class="spl-weather-forecast-value sp-d-flex sp-align-i-center">
									<?php echo esc_html( $single_forecast['snow'] ); ?>
								</span>
							</td>
							<?php endif; ?>
							<?php
						}
						?>
					</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>
