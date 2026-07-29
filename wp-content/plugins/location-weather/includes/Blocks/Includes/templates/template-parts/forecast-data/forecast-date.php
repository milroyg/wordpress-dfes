<?php
/**
 * Forecast Data Date Renderer.
 *
 * @since      1.0.0
 * @version    1.0.0
 * @package    Location_Weather
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use ShapedPlugin\Weather\Frontend\Shortcode;

$modified_template = '';
switch ( $template ) {
	case 'vertical-one':
		$modified_template = 'template-one';
		break;
	case 'vertical-three':
		$modified_template = 'template-three';
		break;
	default:
		$modified_template = $template;
		break;
}

$forecast_time = $single_forecast['times'];

?>

<div class="spl-weather-forecast-date-time">
	<?php if ( 'hourly' === $data_type ) : ?>
		<span class="spl-weather-forecast-time sp-d-flex sp-flex-col sp-align-i-start">
			<?php echo esc_html( $forecast_time ); ?>
		</span>	
		</span>
	<?php endif ?>
	<?php if ( $is_layout_three ) : ?>
		<span class="spl-weather-forecast-date spl-forecast-desc">
			<?php echo esc_html( $description ); ?>
		</span>
	<?php endif ?>
</div>
