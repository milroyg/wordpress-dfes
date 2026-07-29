<?php
/**
 * Additional Data Regular Layout Renderer.
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

$value = $option ?? '';
if ( empty( $weather_data[ $value ] ) ) {
	return;
}
?>

<div class="<?php echo $is_swiper_layout ? 'swiper-slide ' : ''; ?>spl-weather-details spl-weather-<?php echo esc_attr( $value ); ?>">
	<!-- weather label and icon wrapper-->
	<span class="spl-weather-details-title-wrapper">
		<!-- weather icon -->
		<?php if ( $show_additional_data_icon ) : ?>
			<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
				<i class="<?php echo esc_attr( $block_weather_icons[ $value ] ); ?>"></i>
			</span>
		<?php endif; ?>
		<!-- weather label -->
		<span class="spl-weather-details-title">
			<?php
				echo esc_html( $weather_item_labels[ $value ] );
				echo $display_colon ? ':' : '';
			?>
		</span>
	</span>
	<!-- weather value -->
	<span class="spl-weather-details-value">
		<?php
		if ( in_array( $value, array( 'clouds', 'sunrise_time', 'sunset_time' ), true ) ) {
			echo esc_html( $weather_data[ $value ] );
		} elseif ( 'humidity' === $value ) {
			echo esc_html( $weather_data['humidity']['value'] . ' %' );
		} elseif ( 'visibility' === $value ) {
			echo esc_html( $weather_data['visibility'] );
		} elseif ( 'wind' === $value ) {
			?>
				<span class="spl-weather-wind-value">
					<?php echo wp_kses_post( $weather_data['wind'] ); ?>
				</span>
			<?php
		} elseif ( 'gust' === $value ) {
			echo wp_kses_post( $weather_data['gust'] );
		} elseif ( 'pressure' === $value ) {
			echo esc_html( $weather_data['pressure'] );
		}
		?>
	</span>
</div>
