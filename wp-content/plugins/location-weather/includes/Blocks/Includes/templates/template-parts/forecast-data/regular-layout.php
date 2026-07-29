<?php
/**
 * Forecast Data Regular layout Renderer.
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

if ( 'swiper' === $active_forecast_layout || ! $each_forecast_array ) {
	return;
}
$is_layout_three = false;
$wrapper_style   = 'display:flex';
$forecast_layout = $is_layout_three ? 'layout-three' : 'normal';
$forecast_layout = $pre_defined_layout ?? $forecast_layout;

?>
<div class="spl-weather-forecast-data spl-weather-normal <?php echo esc_attr( $data_type ); ?> sp-flex-col" style="<?php echo esc_attr( $wrapper_style ); ?>">
	<?php foreach ( $each_forecast_array as $single_forecast ) : ?>
		<?php require self::block_template_renderer( 'forecast-data/render-forecast.php' ); ?>
	<?php endforeach; ?>
</div>
