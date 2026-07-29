<?php
/**
 * Weather Map Template Renderer File.
 *
 * This template displays Both (OWM and Windy) Weather Map template.
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

if ( ! $attributes ['displayWeatherMap'] ) {
	return;
}

?>
<div class="sp-weather-card-map-renderer">
	<?php require self::block_template_renderer( 'maps/windy-map.php' ); ?>
</div>
