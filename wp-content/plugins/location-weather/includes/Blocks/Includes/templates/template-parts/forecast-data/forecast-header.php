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

use ShapedPlugin\Weather\Blocks\Includes\Template_Parts;

$live_filter_type = in_array( $template, array( 'horizontal-one', 'grid-one' ), true ) ? 'tabs' : 'select';
$live_filter_type = $pre_defined_type ?? $live_filter_type;
// vertical block forecast popup layout two forecast title.
$forecast_popup_title = $forecast_popup_title ?? '';

?>
<!-- forecast header start -->
<div class="spl-weather-forecast-header-area spl-weather-forecast-<?php echo esc_attr( $live_filter_type ); ?>-header">
	<?php
	if ( 'select' === $live_filter_type ) {
		?>
			<div class="spl-weather-forecast-header-type-select sp-d-flex sp-justify-between sp-align-i-center">
				<div class="spl-weather-forecast-title-wrapper">
					<?php
					if ( $forecast_popup_title ) {
						echo '<span class="spl-weather-forecast-overview-label">' . esc_html( $forecast_popup_title ) . '</span>';
					}
					?>
					<span
						class="spl-weather-forecast-title hourly">
						<?php echo esc_attr( $attributes['hourlyTitle'] ); ?>
					</span>
				</div>
				<!-- forecast live filter select -->
				<div class="spl-weather-forecast-select">
					<div
						class="spl-weather-select-active-item sp-d-flex sp-align-i-center sp-justify-between sp-gap-4px">
						<span class="spl-weather-forecast-selected-option" data-value="<?php echo esc_attr( $active_forecast ); ?>">
							<?php echo esc_html( $weather_item_labels[ $active_forecast ] ); ?>
						</span>
						<!-- arrow icon -->
						<span class="spl-weather-forecast-select-svg sp-d-flex inactive">
							<i class="splwp-icon-chevron"></i>
						</span>
					</div>
					<!-- select options -->
					<ul class="spl-weather-forecast-select-list sp-d-hidden sp-flex-col">
						<?php foreach ( $forecast_options as $value ) : ?>
						<li class="spl-weather-forecast-select-item<?php echo ( $active_forecast === $value ) ? ' active' : ''; ?>"
							data-value="<?php echo esc_html( $value ); ?>">
							<?php echo esc_html( $weather_item_labels[ $value ] ); ?>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php
	}
	if ( 'tabs' === $live_filter_type ) {
		// forecast live filter tabs.
		?>
			<ul class="spl-weather-forecast-tabs">
			<?php foreach ( $forecast_options as $item ) : ?>
					<li class="spl-weather-forecast-tab sp-cursor-pointer <?php echo ( $active_forecast === $item ) ? ' active' : ''; ?>"
						data-value="<?php echo esc_html( $item ); ?>">
						<?php echo esc_html( $weather_item_labels[ $item ] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php
	}
	?>
</div>
