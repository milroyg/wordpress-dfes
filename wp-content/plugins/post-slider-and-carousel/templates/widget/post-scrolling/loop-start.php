<?php
/**
 * Loop Start - Post Scrolling Widget Template
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="psacp-wrap psacp-post-scroling-wdgt psacp-post-scroling-wdgt-js inf-post-scroling-wdgt psacp-post-widget-wrap psacp-design-1 <?php echo esc_attr( $atts['css_class'] ); ?>" id="psacp-post-scroling-wdgt-<?php echo esc_attr( $atts['unique'] ); ?>" data-conf="<?php echo htmlspecialchars(json_encode( $atts['slider_conf'] )); ?>">
	<div class="psacp-vticker-scroling-wdgt psacp-vticker-scroling-wdgt-js psacp-clearfix">
		<ul class="psacp-vscroll-wdgt-wrap">