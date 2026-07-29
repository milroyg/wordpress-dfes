<?php
/**
 * Current Weather Card Template.
 *
 * Displays current weather data(including additional data).
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

use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;
use ShapedPlugin\Weather\Blocks\Includes\Template_Parts;

$show_additional_data       = $attributes['displayAdditionalData'];
$additional_data_options    = $attributes['additionalDataOptions'];
$additional_data_options    = Block_Helpers::process_additional_data_options( $additional_data_options );
$display_colon              = false;
$is_swiper_layout           = false;
$additional_navigation_icon = $attributes['additionalNavigationIcon'];
require self::block_template_renderer( 'additional-data/weather-icons.php' );
$data_slider = array(
	'items_per_page'   => 4,
	'gap'              => 8,
	'slider_per_click' => 3,
);

?>
<div class="spl-weather-current-weather-card sp-d-flex sp-flex-col sp-justify-between">
	<div class="spl-weather-header-info-wrapper sp-d-flex sp-flex-col">
		<div class="sp-d-flex sp-justify-between sp-align-i-center">
			<span class="spl-weather-card-location-name">
				<?php echo esc_html__( 'Current Weather', 'location-weather' ); ?>
			</span>
			<?php require self::block_template_renderer( 'location-name.php' ); ?>
		</div>
		<?php require self::block_template_renderer( 'date-time.php' ); ?>
	</div>
	<div class="sp-d-flex sp-justify-between sp-align-i-center">
		<div class="spl-weather-current-weather-icon-wrapper sp-d-flex">
			<!-- weather image -->
			<?php require self::block_template_renderer( 'weather-image.php' ); ?>
			<!-- current temp -->
			<?php require self::block_template_renderer( 'current-temp.php' ); ?>
		</div>
		<div class="sp-text-align-right">
			<?php require self::block_template_renderer( 'weather-description.php' ); ?>
		</div>
	</div>
	<?php
	if ( $show_additional_data ) {
		?>
		<div class="spl-weather-card-daily-details">
			<div class='spl-weather-custom-slider'>
				<?php
				foreach ( $additional_data_options as $option ) {
					if ( ! empty( $weather_data[ $option ] ) ) {
						?>
						<div class="spl-weather-custom-slider-item">
							<?php require self::block_template_renderer( 'additional-data/single-weather.php' ); ?>
						</div>
						<?php
					}
				}
				?>
				<?php echo wp_kses_post( Template_Parts::custom_slider_navigation_buttons() ); ?>
			</div>
		</div>
		<?php
	}
	?>
</div>
