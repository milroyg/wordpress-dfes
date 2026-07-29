<?php
/**
 * Weather Block Table Template Renderer File.
 *
 * This template displays weather table layout.
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

$show_additional_data     = $attributes['displayAdditionalData'] ?? true;
$display_date_update_time = $attributes['displayDateUpdateTime'] ?? false;

?>
<div class="spl-weather-template-wrapper spl-weather-<?php echo esc_attr( $template ); ?>-block-wrapper">
	<div class="spl-weather-table-current-data">
		<table class="spl-weather-current-data-table sp-w-full">
			<?php
			if ( 'table-one' === $template ) {
				?>
				<thead>
					<tr class="spl-weather-table-header">
						<th colspan="1"><?php esc_html_e( 'Current Weather ', 'location-weather' ); ?></th>
						<?php if ( $show_additional_data ) : ?>
							<th colspan="1"><?php esc_html_e( 'Additional Data ', 'location-weather' ); ?></th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="spl-weather-current-data-table-left">
							<?php require self::block_template_renderer( 'current-weather.php' ); ?>
							<?php if ( ! $show_additional_data && $display_date_update_time ) : ?>
								<div class="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
									<div class="spl-weather-last-updated-time">
										<?php esc_html_e( 'Last updated: ', 'location-weather' ); ?><?php echo esc_html( $weather_data['updated_time'] ?? '' ); ?>
									</div>
								</div>
							<?php endif; ?>
						</td>
						<?php if ( $show_additional_data ) : ?>
							<td class="spl-weather-current-data-table-right">
								<?php require self::block_template_renderer( 'additional-data/additional-data.php' ); ?>
								<?php if ( $display_date_update_time ) : ?>
									<div class="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center">
										<div class="spl-weather-last-updated-time">
											<?php esc_html_e( 'Last updated: ', 'location-weather' ); ?><?php echo esc_html( $weather_data['updated_time'] ?? '' ); ?>
										</div>
									</div>
								<?php endif; ?>
							</td>
						<?php endif; ?>
					</tr>
					<?php if ( $attributes['displayWeatherAttribution'] ) : ?>
						<tr>
							<td colspan="2" style="padding: 0;">
								<?php require self::block_template_renderer( 'footer.php' ); ?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
				<?php
			}
			?>
		</table>
	</div>
	<?php
	if ( $enable_forecasting_days ) {
		$forecast_key = 'hourly';
		require self::block_template_renderer( 'forecast-data/forecast-table.php' );
	}
	?>
</div>