<?php
/**
 * Single Forecast Data Renderer.
 *
 * @since      1.0.0
 * @version    1.0.0
 * @package    Location_Weather
 * @subpackage Location_Weather_Pro/Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use ShapedPlugin\Weather\Frontend\Helper;
use ShapedPlugin\Weather\Frontend\Shortcode;

// weather forecast data.
$single_forecast = Shortcode::get_forecast_data( $single_forecast, $measurement_units, $time_settings, true );

$description   = $single_forecast['desc'];
// $precipitation = $single_forecast['precipitation'];
$precipitation = $single_forecast['precipitation'];
$rain_chance   = $single_forecast['rain'];
$wind          = $single_forecast['wind'];
$humidity      = $single_forecast['humidity'];
$pressure      = $single_forecast['pressure'];
$snow          = $single_forecast['snow'] ?? '';
$icon          = $single_forecast['icon'];

// gradient separator styles.
$style                  = array(
	'left'  => '',
	'right' => '',
);
$position_in_percentage = 10;

?>

<div class="spl-weather-forecast-container sp-d-flex sp-justify-between sp-align-i-center<?php echo 'swiper' === $active_forecast_layout ? ' sp-flex-col' : ''; ?>">
	<?php echo $is_layout_three ? '<div class="sp-d-flex sp-row-reverse sp-gap-8px">' : ''; ?>
	<!-- date time -->
	<?php require self::block_template_renderer( 'forecast-data/forecast-date.php' ); ?>
	<!-- forecast image -->
	<?php require self::block_template_renderer( 'forecast-data/forecast-image.php' ); ?>
	<?php echo $is_layout_three ? '</div>' : ''; ?>
	<!-- forecast value -->
	<div class="spl-weather-forecast-value-wrapper sp-d-flex sp-justify-end">
		<!-- forecast temperature -->
		<?php require self::block_template_renderer( 'forecast-data/forecast-temp.php' ); ?>
		<!-- others forecast data values -->
		<span class="spl-weather-forecast-value precipitation sp-d-hidden">
			<?php echo esc_html( $precipitation ); ?>
		</span>
		<span class="spl-weather-forecast-value rainchance sp-d-hidden">
			<?php echo esc_html( $rain_chance ); ?>
		</span>
		<span class="spl-weather-forecast-value wind sp-d-hidden">
			<?php echo wp_kses_post( $wind ); ?> 
		</span>
		<span class="spl-weather-forecast-value humidity sp-d-hidden">
			<?php echo esc_html( $humidity ); ?>
		</span>
		<span class="spl-weather-forecast-value pressure sp-d-hidden">
			<?php echo esc_html( $pressure ); ?>
		</span>
		<span class="spl-weather-forecast-value snow sp-d-hidden">
			<?php echo esc_html( $snow ); ?>
		</span>
	</div>
</div>
