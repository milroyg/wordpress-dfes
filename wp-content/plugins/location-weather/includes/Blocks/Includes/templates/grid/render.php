<?php
/**
 * Weather Block Grid Template Renderer File.
 *
 * This template displays weather grid layout.
 *
 * @since      3.2.0
 * @version    1.0.0
 *
 * @package Location_Weather_Pro/Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use ShapedPlugin\Weather\Blocks\Includes\Block_Helpers;

$forecast_options = Block_Helpers::process_forecast_data_options( $attributes['forecastData'] );
$active_forecast  = $forecast_options[0];

?>
<div class="spl-weather-template-wrapper spl-weather-<?php echo esc_attr( $template ); ?>-wrapper sp-d-flex sp-flex-col">
	<?php require self::block_template_renderer( 'grid/grid-one.php', true ); ?>
</div>
