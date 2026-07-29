<?php
/**
 * Weather Condition Icon Template
 *
 * Displays the weather condition icon with proper attributes.
 *
 * @since      1.0.0
 * @version    1.0.0
 * @package    Location_Weather
 * @subpackage Location_Weather/public/templates
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! $attributes['weatherConditionIcon'] ) {
	return;
}

$icon      = $weather_data['icon'];
$icon_set  = $attributes['weatherConditionIconType'];
$image_url = self::forecast_icon_url( $icon, $icon_set );

?>

<div class="spl-weather-condition-icon sp-d-flex">
	<img
		src="<?php echo esc_url( $image_url ); ?>"
		alt="weather image"
		class="spl-weather-icon"
		decoding="async" 
	/>
</div>
