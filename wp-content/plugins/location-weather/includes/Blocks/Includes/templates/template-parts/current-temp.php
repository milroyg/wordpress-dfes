<?php
/**
 * Current Temperature Template.
 *
 * Displays the current temperature with unit options and layout variations.
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

// Return nothing if temperature is disabled.
if ( ! $attributes['displayTemperature'] ) {
	return;
}

$is_vertical_layout = in_array(
	$template,
	array(
		'vertical-one',
		'tabs-one',
	),
	true
) || 'grid' === $block_name;

?>

<div class="spl-weather-card-current-temperature <?php echo $is_vertical_layout ? 'splw-vertical-temp' : 'splw-horizontal-temp'; ?> sp-d-flex sp-gap-4px">
	<span class="spl-weather-current-temp">
		<?php echo esc_attr( $weather_data['temp'] ); ?>
	</span>
	<div class="spl-weather-temperature-metric splw-single-unit">
		<span class="spl-weather-temp-scale">
			<?php echo 'metric' === $weather_units ? '°C' : '°F'; ?>
		</span>
	</div>
</div>
