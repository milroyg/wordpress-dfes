<?php
/**
 * Additional Data Swiper Layout Renderer.
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

if ( ! $is_swiper_layout ) {
	return;
}
use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$additional_data_icon      = $attributes['additionalDataIcon'];
$additional_data_icon_type = $attributes['additionalDataIconType'];

$additional_carousel_infinite_loop = $attributes['additionalCarouselInfiniteLoop'];
$additional_carousel_columns       = $attributes['additionalCarouselColumns'];
$additional_carousel_auto_play     = $attributes['additionalCarouselAutoPlay'];
$additional_carousel_delay_time    = $attributes['additionalCarouselDelayTime'];
$additional_carousel_speed         = $attributes['additionalCarouselSpeed'];
$enable_additional_nav_icon        = $attributes['enableAdditionalNavIcon'];
$additional_navigation_icon        = $attributes['additionalNavigationIcon'];
$additional_carousel_stop_on_hover = $attributes['additionalCarouselStopOnHover'];
$additional_data_horizontal_gap    = $attributes['additionalDataHorizontalGap'];

$carousel_speed = 'ms' === $additional_carousel_speed['unit']
	? $additional_carousel_speed['value']
	: $additional_carousel_speed['value'] * 1000;
$carousel_delay = 'ms' === $additional_carousel_delay_time['unit']
	? $additional_carousel_delay_time['value'] : $additional_carousel_delay_time['value'] * 1000;

$additional_carousel_nav = $enable_additional_nav_icon ? array(
	'prevEl' => '#' . $unique_id . ' .spl-weather-additional-data-swiper-nav-prev',
	'nextEl' => '#' . $unique_id . ' .spl-weather-additional-data-swiper-nav-next',
) : false;

$additional_carousel_data = array(
	'loop'          => $additional_carousel_infinite_loop,
	'autoplay'      => $additional_carousel_auto_play ? array(
		'delay'                => $carousel_delay,
		'pauseOnMouseEnter'    => $additional_carousel_stop_on_hover,
		'disableOnInteraction' => false,
	) : false,
	'speed'         => $carousel_speed,
	'enableNavIcon' => $enable_additional_nav_icon,
	'navigation'    => $additional_carousel_nav,
	'freeMode'      => true,
	'breakpoints'   => array(
		0    => array(
			'slidesPerView' => $additional_carousel_columns['device']['Mobile'],
			'spaceBetween'  => $additional_data_horizontal_gap['device']['Mobile'],
		),
		600  => array(
			'slidesPerView' => $additional_carousel_columns['device']['Tablet'],
			'spaceBetween'  => $additional_data_horizontal_gap['device']['Tablet'],
		),
		1024 => array(
			'slidesPerView' => $additional_carousel_columns['device']['Desktop'],
			'spaceBetween'  => $additional_data_horizontal_gap['device']['Desktop'],
		),
	),
);
wp_enqueue_script( 'splw-swiper-scripts' );
wp_enqueue_style( 'splw-swiper-styles' );

?>

<div class="spl-weather-swiper spl-weather-additional-data-swiper" data-weather-carousel="<?php echo esc_attr( wp_json_encode( $additional_carousel_data ) ); ?>">
	<div class="spl-weather-swiper-items swiper-wrapper">
		<?php
		foreach ( $additional_data_options as $option ) {
			require self::block_template_renderer( 'additional-data/single-weather.php' );
		}
		?>
	</div>
	<button class="spl-weather-additional-data-swiper-nav-next spl-weather-swiper-nav additional-data spl-weather-swiper-nav-next lw-arrow sp-cursor-pointer">
		<i class="splwp-icon-<?php echo esc_html( $additional_navigation_icon ); ?> right"></i>
	</button>
	<button class="spl-weather-additional-data-swiper-nav-prev spl-weather-swiper-nav additional-data spl-weather-swiper-nav-prev lw-arrow sp-cursor-pointer">
		<i class="splwp-icon-<?php echo esc_html( $additional_navigation_icon ); ?> left"></i>
	</button>
</div>
