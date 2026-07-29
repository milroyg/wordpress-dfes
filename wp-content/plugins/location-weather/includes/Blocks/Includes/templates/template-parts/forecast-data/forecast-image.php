<?php
/**
 * Weather Forecast Image.
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

if ( ! $attributes['forecastDataIcon'] ) {
	return;
}

$icon      = $single_forecast['icon'];
$icon_set  = $attributes['forecastDataIconType'];
$image_url = self::forecast_icon_url( $icon, $icon_set );
// Allow the caller to specify whether to show the description.
$show_description = $show_description ?? false;
$forecast_desc    = $single_forecast['desc'] ?? '';
?>

<div class="spl-weather-forecast-icon">
	<img
		src="<?php echo esc_url( $image_url ); ?>"
		alt="weather image"
		class="spl-weather-icon"
		decoding="async" 
	/>
	<?php if ( $show_description ) : ?>
		<span class="spl-weather-forecast-description">
			<?php echo esc_html( $forecast_desc ); ?>
		</span>
	<?php endif; ?>
</div>
