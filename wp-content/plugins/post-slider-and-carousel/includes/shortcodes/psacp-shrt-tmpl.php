<?php
/**
 * Shortcode Template Generator
 * `psacp_tmpl` Shortcode
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_shortcode_template( $atts, $content = null ) {

	global $psacp_layout_id;

	/* Only for page builder preview - Start */
	if ( ( function_exists('vc_is_page_editable') && vc_is_page_editable() && ! empty( $atts['layout_id'] ) && isset( $atts['psac_layout_preview'] ) && 'no' == $atts['psac_layout_preview'] ) 
		|| ( is_admin() && empty( $atts['psac_layout_preview'] ) && ( isset( $_GET['elementor-preview'] ) || ( isset( $_POST['action'] ) && 'elementor_ajax' === $_POST['action'] ) || ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) ) )
	) {

		return '<div class="psacp-pb-shrt-prev-wrap">
				<div class="psacp-pb-shrt-title"><span>Post Slider and Carousel - Layout</span></div>
				[<span>psacp_tmpl layout_id="'.esc_attr( $atts['layout_id'] ).'"</span>]
			</div>';
	}
	/* Only for page builder preview - Ends */


	// Shortcode Parameters
	$atts = shortcode_atts(array(
		'layout_id'	=> '',
	), $atts, 'psacp_tmpl');

	// Taking some variables
	$meta_prefix	= PSAC_META_PREFIX;
	$layout_id		= psac_clean_number( $atts['layout_id'] );

	/* Layout ID - New Method */
	if( $layout_id ) {

		// Set Global Layout ID
		$psacp_layout_id	= $layout_id;
		$layout_data		= get_post( $layout_id );

		if( $layout_data && isset( $layout_data->post_type ) && PSAC_LAYOUT_POST_TYPE == $layout_data->post_type ) {
			$template_enable	= ( isset( $layout_data->post_status ) && 'publish' == $layout_data->post_status ) ? 1 : 0;
			$template_shortcode	= get_post_meta( $layout_id, $meta_prefix.'layout_shrt', true );
		}
	}

	ob_start();

	// If template exist
	if( ! empty( $template_shortcode ) ) {

		if( ! empty( $template_enable ) ) {
			echo do_shortcode( $template_shortcode );
		}

	} else {
		esc_html_e( 'Sorry, layout does not exist.', 'post-slider-and-carousel' );
	}

	// Reset global layout id
	$psacp_layout_id = '';

	$content .= ob_get_clean();
	return $content;
}

// Layout Template Shortcode
add_shortcode( 'psacp_tmpl', 'psac_render_shortcode_template' );