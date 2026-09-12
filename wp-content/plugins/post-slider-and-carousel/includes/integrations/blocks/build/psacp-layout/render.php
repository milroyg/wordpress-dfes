<?php
/**
 * Block - Post Slider and Carousel Layout Render
 *
 * @package Post Slider and Carousel
 * @since 3.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$layout_id 		= isset( $attributes['layout_id'] ) ? intval( $attributes['layout_id'] )	: 0;
$layout_class	= isset( $attributes['align'] )		? "align{$attributes['align']}"			: '';

if ( $layout_id > 0 ) {
	echo '<div class="psacp-block-wrap '.esc_attr( $layout_class ).'">' . do_shortcode( '[psacp_tmpl layout_id="'.esc_attr( $layout_id ).'"]' ) . '</div>';
}