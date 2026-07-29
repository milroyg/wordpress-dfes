<?php
/**
 * Sun orbit Template.
 *
 * This template displays the sun orbit template.
 *
 * @package    Location_Weather
 * @subpackage Location_Weather_Pro/Blocks/templates
 * @since      1.0.0
 * @version    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( 'combined' === $block_name ) {
	$sunrise      = $single_forecast['sunrise_time'] ?? $weather_data['sunrise_time'] ?? '';
	$sunset       = $single_forecast['sunset_time'] ?? $weather_data['sunset_time'] ?? '';
	$sun_position = 0 === $index ? $weather_data['sun_position'] : '';
	$sunrise_time = new DateTime( $sunrise );
	$sunset_time  = new DateTime( $sunset );
	// Calculate the interval between sunrise and sunset.
	$interval = $sunrise_time->diff( $sunset_time );
	echo '<div class="spl-weather-day-length">' . esc_html( $interval->h ) . ' hr ' . esc_html( $interval->i ) . ' min</div>';
	$translate = '-60px';
} else {
	$sunrise      = $weather_data['sunrise_time'] ?? '';
	$sunset       = $weather_data['sunset_time'] ?? '';
	$sun_position = $weather_data['sun_position'] ?? '';
	$translate    = in_array( $template, array( 'table-two', 'tabs-one' ), true ) ? '-140px' : '-60px';
}

$calculate_sun_position_angle = $sun_position > 10 ? $sun_position : 10;
$show_sun                     = 10 !== $calculate_sun_position_angle ? true : false;
$animation_name               = wp_unique_id( 'spl-weather-sun-orbit-animation-' );

?>
<div class="spl-weather-sun-orbit sp-d-flex sp-justify-between">
	<?php if ( $sunrise ) : ?>
		<div class="spl-weather-sunrise sp-d-flex sp-flex-col sp-justify-center">
			<span class="lw-title-wrapper">
				<span class="spl-weather-details-title">
					<?php esc_html_e( 'Sunrise', 'location-weather' ); ?>
				</span>
			</span>
			<span class="spl-weather-details-value">
				<?php echo esc_html( $sunrise ); ?>
			</span>
		</div>
	<?php endif; ?>
	<div class="spl-weather-sun-orbit-sky sp-d-flex sp-flex-row sp-align-i-center sp-justify-between">
		<div class="spl-weather-sun-orbit-sunrise-icon">
			<i class="splwp-icon-sunrise-2"></i>
		</div>
		<?php if ( $sun_position ) : ?>
			<style>
				@keyframes <?php echo esc_attr( $animation_name ); ?> {
					0% {
						transform: rotate(0deg) translate(<?php echo esc_attr( $translate ); ?>) rotate(10deg);
					}
					100% {
						transform: rotate(<?php echo esc_attr( $sun_position ); ?>deg) translate(<?php echo esc_attr( $translate ); ?>) rotate(10deg);
					}
				}
			</style>
			<div 
				class="spl-weather-sun-orbit-sun"
				style="transform: rotate(<?php echo esc_attr( $sun_position ); ?>deg) translate(<?php echo esc_attr( $translate ); ?>) rotate(0deg); animation: <?php echo esc_attr( $animation_name ); ?> 6s linear;"
			>
				<?php if ( $show_sun ) : ?>
					<img
						decoding="async"
						src="<?php echo esc_url( LOCATION_WEATHER_ASSETS ); ?>/images/icons/weather-icons/01d.svg"
						alt="<?php esc_attr_e( 'Sun in orbit', 'location-weather' ); ?>"
						width="30"
						height="30"
					/>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="spl-weather-sun-orbit-sunset-icon">
			<i class="splwp-icon-sunset-2"></i>
		</div>
	</div>
	<?php if ( $sunset ) : ?>
		<div class="spl-weather-sunset sp-d-flex sp-flex-col sp-justify-center">
			<span class="lw-title-wrapper">
				<span class="spl-weather-details-title">
					<?php esc_html_e( 'Sunset', 'location-weather' ); ?>
				</span>
			</span>
			<span class="spl-weather-details-value">
				<?php echo esc_html( $sunset ); ?>
			</span>
		</div>
	<?php endif; ?>
</div>
