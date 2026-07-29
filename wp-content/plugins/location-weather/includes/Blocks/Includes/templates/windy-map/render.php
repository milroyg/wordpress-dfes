<?php
/**
 * Weather Block Windy Map Template Renderer File.
 *
 * This template displays the Windy.com weather map.
 *
 * @since 3.2.0
 * @version 1.0.0
 *
 * @package Location_Weather_Pro/Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require self::block_template_renderer( 'maps/windy-map.php' );
