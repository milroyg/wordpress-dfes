<?php
/**
 * 'psac_post_slider' Post Slider Shortcode
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to handle the `psac_post_slider` shortcode
 * 
 * @since 1.0
 */
function psac_render_post_slider( $atts, $content = null )  {

	// Taking some globals
	global $post, $psacp_layout_id;

    // Shortcode Parameters
	$atts = shortcode_atts(array(
		'limit' 				=> 20,
		'design' 				=> 'design-1',
		'show_author' 			=> 'true',
		'show_tags'				=> 'false',
		'show_comments'			=> 'true',
		'show_category' 		=> 'true',
		'show_content' 			=> 'false',
		'show_date' 			=> 'true',
		'media_size' 			=> 'large',
		'content_words_limit' 	=> 20,
		'show_read_more' 		=> 'false',
		'read_more_text'		=> '',
		'order'					=> 'DESC',
		'orderby'				=> 'date',
		'category' 				=> array(),
		'css_class'				=> '',
		
		'sliderheight'          => '',
		'loop'					=> 'true',
		'arrows'				=> 'true',
		'dots' 					=> 'true',
		'autoplay'				=> 'true',
		'autoplay_interval'		=> 5000,
		'speed' 				=> 500,
		'custom_param_1'		=> '',	// Custom Param Passed Just for Developer
		'custom_param_2'		=> '',
		), $atts, 'psac_post_slider');

	$shortcode_designs 				= psac_post_slider_designs();
	$atts['shortcode']				= 'psac_post_slider';
	$atts['layout_id']				= $psacp_layout_id;
	$atts['limit'] 					= psac_clean_number( $atts['limit'], 20, 'number' );
	$atts['show_author'] 			= psac_string_to_bool( $atts['show_author'] );
	$atts['show_tags'] 				= psac_string_to_bool( $atts['show_tags'] );
	$atts['show_comments'] 			= psac_string_to_bool( $atts['show_comments'] );
	$atts['show_date'] 				= psac_string_to_bool( $atts['show_date'] );
	$atts['show_category'] 			= psac_string_to_bool( $atts['show_category'] );
	$atts['show_content'] 			= psac_string_to_bool( $atts['show_content'] );
	$atts['show_read_more'] 		= psac_string_to_bool( $atts['show_read_more'] );
	$atts['category'] 				= psac_maybe_explode( $atts['category'] );
	$atts['content_words_limit'] 	= psac_clean_number( $atts['content_words_limit'], 20 );
	$atts['media_size'] 			= ! empty( $atts['media_size'] )			? $atts['media_size'] 				: 'large';
	$atts['read_more_text']			= !empty( $atts['read_more_text'] )			? $atts['read_more_text']			: esc_html__( 'Read More', 'post-slider-and-carousel' );
	$atts['order'] 					= ( strtolower($atts['order']) == 'asc' ) 	? 'ASC' 							: 'DESC';
	$atts['orderby'] 				= ! empty( $atts['orderby'] )				? $atts['orderby'] 					: 'date';
	
	$atts['loop']					= psac_string_to_bool( $atts['loop'] );
	$atts['arrows']					= psac_string_to_bool( $atts['arrows'] );
	$atts['dots']					= psac_string_to_bool( $atts['dots'] );
	$atts['autoplay']				= psac_string_to_bool( $atts['autoplay'] );
	$atts['autoplay_interval']		= psac_clean_number( $atts['autoplay_interval'], 5000 );
	$atts['speed']					= is_numeric( $atts['speed'] ) ? psac_clean_number( $atts['speed'], 0 ) : psac_string_to_bool( $atts['speed'] );
	$atts['sliderheight'] 			= ! empty( $atts['sliderheight'] ) ? $atts['sliderheight'] : '';
	$atts['design'] 				= ($atts['design'] && (array_key_exists(trim($atts['design']), $shortcode_designs))) ? trim( $atts['design'] ) : 'design-1';
	$atts['unique']					= psac_get_unique();
	$atts['css_class']				.= ( $atts['layout_id'] ) ? " psacp-layout-{$atts['layout_id']}" : '';
	$atts['css_class']				= psac_sanitize_html_classes( $atts['css_class'] );
	
	// For RTL
	if( is_rtl() ) {
		$atts['rtl'] = true;
	} else {
		$atts['rtl'] = false;
	}

	// Taking some variables
	$slide_height			= ( $atts['sliderheight'] ) ? "height:{$atts['sliderheight']}px;" : '';
	$atts['count'] 			= 0;
	$atts['slider_conf'] 	= array( 'loop' => $atts['loop'], 'arrows' => $atts['arrows'], 'dots' => $atts['dots'], 'autoplay' => $atts['autoplay'], 'autoplay_interval' => $atts['autoplay_interval'], 'speed' => $atts['speed'], 'rtl' => $atts['rtl'] );

	// Enqueue scripts
	wp_enqueue_script( 'jquery-owl-carousel' );
	wp_enqueue_script( 'psacp-public-script' );
	psac_enqueue_script();

	// WP Query Parameters
	$args = array( 
		'post_type'      		=> PSAC_POST_TYPE,
		'post_status' 			=> array('publish'),
		'order'          		=> $atts['order'],
		'orderby'        		=> $atts['orderby'],
		'posts_per_page' 		=> $atts['limit'],
		'no_found_rows'			=> true,
		'ignore_sticky_posts'	=> true,
	);

    // Category Parameter
	if( $atts['category'] ) {
		$args['tax_query'] = array(
								array(
									'taxonomy' 	=> PSAC_CAT,
									'terms' 	=> $atts['category'],
									'field' 	=> ( isset($atts['category'][0]) && is_numeric($atts['category'][0]) ) ? 'term_id' : 'slug',
								));
	}

	$args = apply_filters( 'psacp_post_slider_query_args', $args, $atts );

	// WP Query
	$query = new WP_Query( $args );

	ob_start();

	// If post is there
	if ( $query->have_posts() ) {

			include( PSAC_DIR . "/templates/slider/loop-start.php" );

			while ( $query->have_posts() ) : $query->the_post();

				$atts['count'] 			= ( $atts['count'] + 1 );
				$atts['format']			= psac_get_post_format();
				$atts['feat_img'] 		= psac_get_post_feat_image( $post->ID, $atts['media_size'], true );
				$atts['feat_thumb_img'] = psac_get_post_feat_image( $post->ID, 'psacp-medium', true );
				$atts['post_link'] 		= psac_get_post_link( $post->ID );
				$atts['cate_name'] 		= psac_get_post_terms( $post->ID, PSAC_CAT );
				$atts['tags']  			= ( $atts['show_tags'] ) ? psac_post_meta_data( array('tag' => $atts['show_tags']), array('tag_taxonomy' => 'post_tag') ) : '';

				$atts['wrp_cls']		= "psacp-post-{$post->ID} psacp-post-{$atts['format']}";
				$atts['wrp_cls']		.= ( is_sticky( $post->ID ) ) 	? ' psacp-sticky'	: '';
				$atts['wrp_cls'] 		.= empty($atts['feat_img'])		? ' psacp-no-thumb'	: ' psacp-has-thumb';

				// Creating image style
				if( $atts['feat_img'] ) {
					$atts['image_style'] = 'background-image:url('.esc_url( $atts['feat_img'] ).'); '.$slide_height;
				} else {
					$atts['image_style'] = $slide_height;
				}

	            // Include shortcode html file
				include( PSAC_DIR . "/templates/slider/{$atts['design']}.php" );

			endwhile;
			
			include( PSAC_DIR . "/templates/slider/loop-end.php" );	
	}

	wp_reset_postdata(); // Reset WP Query

    $content .= ob_get_clean();
    return $content;
}

// Post Slider Shortcode
add_shortcode( 'psac_post_slider', 'psac_render_post_slider' );