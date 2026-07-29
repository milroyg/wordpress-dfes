<?php
/**
 * Weather Block Horizontal Template Renderer File.
 *
 * This template displays weather horizontal layout.
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

use ShapedPlugin\Weather\Frontend\Shortcode;
use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$weather_graph = false;
$forecast_type = 'hourly';

$enable_global_style      = $attributes['enableTemplateGlobalStyle'] ?? false;
$template_primary_color   = $attributes['templatePrimaryColor'] ?? '';
$forecast_data_color      = $attributes['forecastDataColor'] ?? '';
$chart_text_color         = $enable_global_style ? $template_primary_color : ( empty( $forecast_data_color ) ? $template_primary_color : $forecast_data_color );
$display_date_update_time = $attributes['displayDateUpdateTime'] ?? true;

?>

<div class="spl-weather-template-wrapper spl-weather-<?php echo esc_attr( $template ); ?>-wrapper">
	<?php
	switch ( $template ) {
		case 'horizontal-one':
			?>
			<div class="spl-weather-horizontal-top sp-d-flex sp-justify-between sp-w-full">
				<div class='spl-weather-horizontal-left-wrapper'>
					<?php require self::block_template_renderer( 'current-weather.php' ); ?>
				</div>
				<?php require self::block_template_renderer( 'additional-data/additional-data.php' ); ?>
			</div>
			<?php
			if ( $enable_forecasting_days ) {
				?>
				<div class="spl-weather-card-forecast-data splwb-forecast">
					<?php
					$forecast_options = Block_Helpers::process_forecast_data_options( $attributes['forecastData'] );
					$active_forecast  = $forecast_options[0];
					require self::block_template_renderer( 'forecast-data/forecast-header.php' );
					$data_type              = 'hourly';
					$active_forecast_layout = 'normal';
					$each_forecast_array    = $forecast_data;
					$is_both_forecast       = false;
					require self::block_template_renderer( 'forecast-data/regular-layout.php' );
					?>

				</div>
				<?php
			}
			break;
	}
	?>
</div>
<?php
if ( 'horizontal-three' !== $template ) :
	?>
	<?php if ( $display_date_update_time ) : ?>
		<div class="spl-weather-detailed sp-d-flex sp-justify-end sp-align-i-center has-padding">
			<div class="spl-weather-last-updated-time">
				Last updated: <?php echo esc_html( $weather_data['updated_time'] ?? '' ); ?>
			</div>
		</div>
	<?php endif; ?>
	<?php require self::block_template_renderer( 'footer.php' ); ?>
<?php endif; ?>
