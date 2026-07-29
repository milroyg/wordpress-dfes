<?php
/**
 * Date and Time Template
 *
 * Displays the current weather date and time with flexible formatting options.
 * The layout adapts based on template settings and display preferences.
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

$show_current_date = $attributes['showCurrentDate'];
$show_current_time = $attributes['showCurrentTime'];

if ( ! $show_current_date && ! $show_current_time ) {
	return;
}
$weather_time = $weather_data['time'] ?? '';
$weather_date = $weather_data['date'] ?? '';

?>
<div class="spl-weather-card-date-time sp-d-flex">
	<?php if ( $show_current_time ) : ?>
		<span class="spl-weather-current-time">
			<?php echo esc_html( $weather_time ); ?>
		</span>
	<?php endif; ?>

	<?php if ( $show_current_date ) : ?>
		<span class="spl-weather-date">
			<?php echo esc_html( $weather_date ); ?>
		</span>
	<?php endif; ?>
</div>
