<?php
/**
 * Forecast Data Regular Templates Renderer.
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

if ( 'normal' === $active_forecast_layout || ! $each_forecast_array ) {
	return;
}

$forecast_data_icon              = $attributes['forecastDataIcon'];
$forecast_data_icon_type         = $attributes['forecastDataIconType'];
$forecast_carousel_infinite_loop = $attributes['forecastCarouselInfiniteLoop'];
$forecast_carousel_columns       = $attributes['forecastCarouselColumns'];
$forecast_carousel_auto_play     = $attributes['forecastCarouselAutoPlay'];
$forecast_carousel_delay_time    = $attributes['forecastCarouselAutoplayDelay'];
$forecast_carousel_speed         = $attributes['forecastCarouselSpeed'];
$enable_forecast_nav_icon        = $attributes['showForecastNavIcon'];
$forecast_navigation_icon        = $attributes['forecastCarouselNavIcon'];
$forecast_carousel_stop_on_hover = $attributes['carouselStopOnHover'];
$forecast_nav_visibility         = $attributes['forecastNavigationVisibility'];
$forecast_carousel_gap           = $attributes['forecastCarouselHorizontalGap'];

$forecast_carousel_speed = 'ms' === $forecast_carousel_speed['unit']
	? $forecast_carousel_speed['value']
	: $forecast_carousel_speed['value'] * 1000;
$forecast_carousel_delay = 'ms' === $forecast_carousel_delay_time['unit']
	? $forecast_carousel_delay_time['value'] : $forecast_carousel_delay_time['value'] * 1000;

$forecast_carousel_nav = $enable_forecast_nav_icon ? array(
	'prevEl' => '#' . $unique_id . ' .spl-weather-forecast-swiper-nav-prev',
	'nextEl' => '#' . $unique_id . ' .spl-weather-forecast-swiper-nav-next',
) : false;

$forecast_carousel_data = array(
	'loop'          => $forecast_carousel_infinite_loop,
	'autoplay'      => $forecast_carousel_auto_play ? array(
		'delay'                => $forecast_carousel_delay,
		'pauseOnMouseEnter'    => $forecast_carousel_stop_on_hover,
		'disableOnInteraction' => false,
	) : false,
	'speed'         => $forecast_carousel_speed,
	'enableNavIcon' => $enable_forecast_nav_icon,
	'navigation'    => $forecast_carousel_nav,
	'freeMode'      => true,
	'breakpoints'   => array(
		0    => array(
			'slidesPerView' => $forecast_carousel_columns['device']['Mobile'],
			'spaceBetween'  => $forecast_carousel_gap['device']['Mobile'],
		),
		600  => array(
			'slidesPerView' => $forecast_carousel_columns['device']['Tablet'],
			'spaceBetween'  => $forecast_carousel_gap['device']['Tablet'],
		),
		1024 => array(
			'slidesPerView' => $forecast_carousel_columns['device']['Desktop'],
			'spaceBetween'  => $forecast_carousel_gap['device']['Desktop'],
		),
	),
);
wp_enqueue_script( 'splw-swiper-scripts' );
wp_enqueue_style( 'splw-swiper-styles' );

$wrapper_style   = 'display:flex';
$is_layout_three = false;
?>

<div class="spl-weather-swiper spl-weather-forecast-swiper swiper" data-weather-carousel="<?php echo esc_attr( wp_json_encode( $forecast_carousel_data ) ); ?>">
	<div class="spl-weather-forecast-data swiper-wrapper" style="<?php echo esc_attr( $wrapper_style ); ?>">
		<?php foreach ( $each_forecast_array as $single_forecast ) : ?>
		<div class="swiper-slide">
			<?php require self::block_template_renderer( 'forecast-data/render-forecast.php' ); ?>
		</div>
		<?php endforeach; ?>
	</div>
	<button class="spl-weather-forecast-swiper-nav-next spl-weather-swiper-nav forecast spl-weather-swiper-nav-next lw-arrow sp-cursor-pointer">
		<i class="splwp-icon-<?php echo esc_html( $forecast_navigation_icon ); ?> right"></i>
	</button>
	<button class="spl-weather-forecast-swiper-nav-prev spl-weather-swiper-nav forecast spl-weather-swiper-nav-prev lw-arrow sp-cursor-pointer">
		<i class="splwp-icon-<?php echo esc_html( $forecast_navigation_icon ); ?> left"></i>
	</button>
</div>
