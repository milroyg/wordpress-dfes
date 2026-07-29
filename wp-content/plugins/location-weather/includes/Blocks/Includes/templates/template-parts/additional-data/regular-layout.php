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

if ( $is_swiper_layout ) {
	return;
}
?>

<div class="spl-weather-details-regular-data sp-w-full">
	<?php foreach ( $additional_data_options as $option ) : ?>
		<?php require self::block_template_renderer( 'additional-data/single-weather.php' ); ?>
	<?php endforeach; ?>
</div>
<?php if ( $display_comport_layout ) : ?>
<div class="spl-weather-details-comport-data sp-d-flex sp-justify-between sp-w-full">
	<?php
	foreach ( $comport_data as $option ) :
		?>
		<div 
			class="spl-weather-details spl-weather-<?php echo esc_attr( $option ); ?> sp-d-flex sp-align-i-center sp-gap-8px" 
			title="<?php echo esc_attr( $weather_item_labels[ $option ] ); ?>"
		>
				<span class="spl-weather-details-title-wrapper">
					<span class="spl-weather-details-icon sp-d-flex sp-align-i-center">
						<i class="<?php echo esc_attr( $block_weather_icons[ $option ] ); ?>"></i>
					</span>
				</span>
				<span class="spl-weather-details-value">
					<?php
					if ( 'wind' === $option ) {
						echo wp_kses_post( $weather_data['wind'] );
					} elseif ( 'pressure' === $option ) {
						echo esc_html( $weather_data['pressure'] ?? '' );
					} elseif ( 'humidity' === $option ) {
						echo esc_html( $weather_data['humidity']['value'] ?? '' ) . '%';
					}
					?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
