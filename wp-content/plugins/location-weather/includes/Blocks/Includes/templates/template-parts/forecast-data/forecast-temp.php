<?php
/**
 * Forecast Data Temperature Renderer.
 *
 * @since      1.0.0
 * @version    1.0.0
 * @package    Location_Weather
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use ShapedPlugin\Weather\Blocks\Includes\Template_Parts;

$min_temp = $single_forecast['min'] ?? '';
$max_temp = $single_forecast['max'] ?? '';
$now_temp = $single_forecast['now'] ?? '';

$hourly_forecast_type = $attributes['hourlyForecastType'];
$is_one_hourly_data   = 'hourly' === $data_type && '1' === $hourly_forecast_type;
$is_one_hourly_data   = $pre_defined_one_hourly ?? $is_one_hourly_data;

$separator = 'slash';
switch ( $template ) {
	case 'vertical-one':
	case 'vertical-two':
	case 'horizontal-one':
		$separator = 'slash';
		break;
	case 'vertical-three':
	case 'tabs-one':
	case 'table-one':
		$separator = 'vertical-bar';
		break;
	default:
		$separator = 'slash';
		break;
}

$temp_separator = $pre_defined_separator ?? $separator;

$is_show_min_max_icon_on_popup = false;

?>
<span class="spl-weather-forecast-value temperature<?php echo 'gradient' === $temp_separator ? ' sp-d-flex sp-align-i-center sp-justify-end' : ''; ?>">
	<?php if ( $is_one_hourly_data ) : ?>
		<span class="spl-weather-forecast-current-temp">
			<span class="spl-weather-forecast-temp">
				<?php echo esc_html( round( $now_temp ) ); ?>
			</span>
			<span class="spl-weather-temp-unit">°</span>
		</span>
	<?php else : ?>
		<span class="spl-weather-forecast-min-temp">
			<span class="spl-weather-forecast-temp">
				<?php echo esc_html( $min_temp ); ?>
			</span>
			<span class="spl-weather-temp-unit">°</span>
		</span>
		<?php if ( ! $is_show_min_max_icon_on_popup ) : ?>
		<span class="spl-weather-forecast-separator <?php echo 'gradient' === $temp_separator ? 'splw-separator-gradient' : 'divider'; ?>">
			<?php echo 'slash' === $temp_separator ? ' / ' : ''; ?>
			<?php echo 'vertical-bar' === $temp_separator ? ' | ' : ''; ?>
		</span>
		<?php endif; ?>
		<span class="spl-weather-forecast-max-temp">
			<span class="spl-weather-forecast-temp">
				<?php echo esc_html( $max_temp ); ?>
			</span>
			<span class="spl-weather-temp-unit">°</span>
		</span>
	<?php endif; ?>
</span>
