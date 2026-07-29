<?php
/**
 * Weather AQI Block Template Pollutant Details File.
 *
 * This template displays weather AQI detailed layout.
 *
 * @package Location_Weather_Pro/Blocks
 */

// Prevent direct file access for security.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$pollutants                  = Block_Helpers::get_pollutant_scale_data();
$enable_indicator            = $attributes['enablePollutantIndicator'] ?? true;
$enable_units                = $attributes['enablePollutantMeasurementUnit'] ?? false;
$pollutant_name_symbol_style = $attributes['displaySymbolDisplayStyle'] ?? false;
?>
<div class="spl-aqi-card-pollutant-details-wrapper">
	<div class="spl-aqi-card-pollutant-details sp-d-grid sp-w-full sp-grid-cols-2 sp-gap-10px">
		<?php
		foreach ( $pollutants as $pollutant ) :
			$key   = $pollutant['key'];
			$label = Block_Helpers::get_label( $pollutant['name'], $pollutant['symbol'], 'abbreviation', $pollutant_name_symbol_style );
			$value = $current_aqi_data->$key;
			$data  = Block_Helpers::get_pollutant_data( $value, $key );
			$color = Block_Helpers::hex_to_rgba( $data['color'] );
			?>
			<div class="spl-aqi-card-pollutant-item sp-d-flex sp-w-full sp-justify-between sp-align-i-center spl-aqi-condition-<?php echo esc_html( strtolower( $data['condition'] ) ); ?>" style="--spl-pollutant-color: <?php echo esc_attr( $color ); ?>;">
				<div class="spl-pollutant-title <?php echo $enable_indicator ? 'indicator' : ''; ?>"><?php echo esc_html( $label ); ?></div>
				<div class="spl-pollutant-value"><?php echo esc_html( $value ); ?><?php if ( $enable_units ) : ?>
					<span class="spl-pollutant-unit">µg/m³</span><?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
