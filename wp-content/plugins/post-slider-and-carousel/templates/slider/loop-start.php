<?php
/**
 * Loop Start - Slider Template
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="psacp-wrap psacp-slider-wrap-main">
	<div id="psacp-post-slider-wrap-<?php echo esc_attr( $atts['unique'] ); ?>" class="psacp-post-slider-wrap owl-carousel <?php echo 'psacp-'.esc_attr( $atts['design'] ) .' '. esc_attr( $atts['css_class'] ); ?> psacp-clearfix" data-conf="<?php echo htmlspecialchars(json_encode( $atts['slider_conf'] )); ?>">