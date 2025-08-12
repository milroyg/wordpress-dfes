<?php
/**
 * Plugin generic functions file
 *
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 * 
 * @since 1.0
 */
function psac_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'psac_clean', $var );
	} else {
		$data = is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		return wp_unslash($data);
	}
}

/**
 * Sanitize number value and return fallback value if it is blank
 * 
 * @since 1.0
 */
function psac_clean_number( $var, $fallback = null, $type = 'int' ) {

	$var = trim( $var );

	if( $type == 'int' ) {
		$data = absint( $var );
	} elseif ( $type == 'number' ) {
		$data = intval( $var );
	} else {
		$data = abs( $var );
	}

	return ( empty($data) && isset($fallback) ) ? $fallback : $data;
}

/**
 * Sanitize url
 * 
 * @since 1.0
 */
function psac_clean_url( $url ) {
	return esc_url_raw( trim($url) );
}

/**
 * Sanitize multiple HTML classes
 * 
 * @since 1.0
 */
function psac_sanitize_html_classes($classes, $sep = " ") {
	$return = "";

	if( ! is_array($classes) ) {
		$classes = explode($sep, $classes);
	}

	if( ! empty($classes) ) {
		foreach($classes as $class) {
			$return .= sanitize_html_class($class) . " ";
		}
		$return = trim( $return );
	}

	return $return;
}

/**
 * Function to get allowed post types from setting.
 * 
 * @since 3.5
 */
function psac_allowed_post_types() {

	$post_types = psac_get_option( 'post_types', array() );
	return $post_types;
}

/**
 * Function to unique number value
 * 
 * @since 1.0
 */
function psac_get_unique() {
	static $unique = 0;
	$unique++;

	// For VC front end editing
	if ( ( function_exists('vc_is_page_editable') && vc_is_page_editable() )
		 || ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_POST['action'] ) && $_POST['action'] == 'elementor_ajax' && isset($_POST['editor_post_id']) )
		)
	{
		return rand() .'-'. current_time( 'timestamp' );
	}

	return $unique;
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 3.5
 * @param string|bool $string String to convert. If a bool is passed it will be returned as-is.
 * @return bool
 */
function psac_string_to_bool( $string ) {
	$string = $string ? trim( $string ) : '';
	return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

/**
 * Explode the data.
 * 
 * @since 3.5
 */
function psac_maybe_explode( $data, $separator = ',' ) {

	if( is_array( $data ) ) {
		return $data;
	}

	$data = trim( $data );
	if( '' == $data ) {
		return array();
	}

	return explode( $separator, $data );
}

/**
 * Function to get post excerpt
 * Custom function so some theme filter will not affect it.
 * 
 * @since 3.5
 */
function psac_post_excerpt( $post = null ) {

	$post = get_post( $post );

	if ( empty( $post ) ) {
		return '';
	}
 
	if ( post_password_required( $post ) ) {
		return esc_html__( 'There is no excerpt because this is a protected post.', 'post-slider-and-carousel' );
	}

	return apply_filters( 'psacp_post_excerpt', $post->post_excerpt, $post );
}

/**
 * Function to get post short content either via excerpt or content.
 * 
 * @since 1.0
 */
function psac_get_post_excerpt( $post_id = null, $content = '', $word_length = 55, $more = '...' ) {

	global $post;

	$word_length		= ! empty( $word_length ) ? $word_length : 55;
	$post_content_fix	= psac_get_option('post_content_fix');

	// If post id is passed
	if( ! empty( $post_id ) ) {
		if( has_excerpt($post_id) ) {
		  $content = psac_post_excerpt( $post );
		} else {
		  $content = ! empty( $content ) ? $content : get_the_content( null, false, $post_id );
		}
	}

	// Storing original content
	$orig_content = $content;
	
	/***** Divi Theme Tweak Starts *****/
	if( function_exists('et_strip_shortcodes') ) {
		$content = et_strip_shortcodes( $content );
	}
	if( function_exists('et_builder_strip_dynamic_content') ) {
		$content = et_builder_strip_dynamic_content( $content );
	}

	/***** Avada Theme Tweak Starts *****/
	if( function_exists('fusion_extract_shortcode_contents') ) {
		$pattern = get_shortcode_regex();
		$content = preg_replace_callback( "/$pattern/s", 'fusion_extract_shortcode_contents', $content );
	}

	/* General tweak strip shortcodes and keep the content */
	if( $post_content_fix ) {
		$content = preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		$content = str_replace( [ '"', "'" ], [ '&quot;', '&#39;' ], $content );
	}

	$content = apply_filters( 'psacp_pre_post_content', $content, $orig_content, $post_id, $word_length, $more );

	if( $content ) {
		$content = strip_shortcodes( $content ); // Strip shortcodes
		$content = wp_trim_words( $content, $word_length, $more );
	}

	return apply_filters( 'psacp_post_content', $content, $orig_content, $post_id, $word_length, $more );
}

/**
 * Function to get post featured image
 * 
 * @since 1.0
 */
function psac_get_post_feat_image( $post_id = null, $size = 'large', $default_img = false ) {

	$size				= ! empty( $size ) ? $size : 'large';
	$post_first_img		= psac_get_option('post_first_img');
	$default_feat_img	= psac_get_option('post_default_feat_img');
	
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

	if( ! empty( $image ) ) {
		$image = isset($image[0]) ? $image[0] : '';
	}

	if( empty( $image ) && ! empty( $post_first_img ) ) {
		
		// Get post content
		$post_content = get_the_content( null, false, $post_id );

		preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"].*?>/i', $post_content, $matches);
		$image	= ! empty( $matches[1][0] ) ? $matches[1][0] : '';
	}

	// Getting default image
	if( empty( $image ) && $default_img && ! empty( $default_feat_img ) ) {
		$image = $default_feat_img;
	}

	return apply_filters( 'psacp_post_feat_image', $image, $post_id, $size, $default_img );
}

/**
 * Function to get post external link or permalink
 * 
 * @since 1.0
 */
function psac_get_post_link( $post_id = '' ) {

	$post_link  = false;
	$prefix     = PSAC_META_PREFIX;

	if( ! empty( $post_id ) ) {

		$post_link = get_post_meta( $post_id, $prefix.'post_link', true );

		if( empty($post_link) ) {
			$post_link = get_permalink( $post_id );
		}
	}
	return $post_link;
}

/**
 * Function to get term external link or permalink
 * 
 * @since 1.0
 */
function psac_get_term_link( $term = '' ) {

	$term_link  		= false;
	$prefix     		= PSAC_META_PREFIX;
	$term_id			= is_object( $term ) ? $term->term_id : $term;
	$allowed_taxonomy	= psac_get_option( 'taxonomy', array() );

	if( ! empty($term) ) {

		// Get term object if term id is passed
		if( ! is_object( $term ) ) {
			$term = get_term( $term_id );
		}

		// Get taxonomy custom link if it is enabled from setting
		if( isset( $term->taxonomy ) && isset( $allowed_taxonomy[$term->taxonomy] ) ) {
			$term_link = get_term_meta( $term_id, $prefix.'term_link', true );
		}

		if( empty($term_link) ) {
			$term_link = get_term_link( $term );
		}
	}
	return $term_link;
}

/**
 * Function to get post categories with HTML
 * 
 * @since 1.0
 */
function psac_get_post_terms( $post_id = '', $taxonomy = PSAC_CAT, $limit = null, $join = ' ' ) {

	$cat_count  = 1;
	$cat_links  = array();
	$terms      = get_the_terms( $post_id, $taxonomy );

	if( ! is_wp_error( $terms ) && $terms ) {
		foreach ( $terms as $term ) {
			$term_link      = psac_get_term_link( $term );
			$cat_links[]    = '<a class="psacp-post-cat-link psacp-post-cat-'.esc_attr( $term->term_id ).'" href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';

			// Upto number of limits
			if( $cat_count == $limit ) {
				break;
			}

			$cat_count++;
		}
	}
	$cat_links = join( $join, $cat_links );

	return $cat_links;
}

/**
 * Function to get post meta data like author, date and etc
 * 
 * @since 3.5
 */
function psac_post_meta_data( $meta = array(), $args = array(), $join = ' &ndash; ', $output = 'html' ) {

	global $post;

	$result				= array();
	$join				= '<span class="psacp-post-meta-sep">'. $join .'</span>';
	$meta				= is_array( $meta ) ? $meta : (array)$meta;
	$default_meta_args	= array(
								'icon'				=> true,
								'hide_empty'		=> true,
								'comment_text'		=> _n( 'Reply', 'Replies', get_comments_number(), 'post-slider-and-carousel' ),
								'post_id'			=> !empty( $args['post_id'] ) ? $args['post_id'] : $post->ID,
								'taxonomy'			=> PSAC_CAT,
								'tag_taxonomy'		=> '',
								'cat_limit'			=> '',
								'tag_limit'			=> '',
							);
	$args				= wp_parse_args( $args, $default_meta_args );

	// Loop of meta data
	if( ! empty( $meta ) ) {
		foreach ($meta as $meta_key => $meta_val) {

			if( empty( $meta_key ) || empty( $meta_val ) ) {
				continue;
			}

			// Post Author
			if( $meta_key == 'author' ) {
				$icon				= ( $args['icon'] ) ? '<i class="fa fa-user"></i>' : null;
				$result[$meta_key]	= '<span class="psacp-post-meta-data psacp-post-author">'. $icon . ucfirst( get_the_author() ).'</span>';
			}

			// Post Date
			if( $meta_key == 'post_date' ) {
				$icon				= ( $args['icon'] ) ? '<i class="fa fa-clock-o"></i>' : null;
				$result[$meta_key]	= '<span class="psacp-post-meta-data psacp-post-date">'. $icon . get_the_date().'</span>';
			}

			// Post Year
			if( $meta_key == 'year' ) {
				$result[$meta_key] = '<span class="psacp-post-meta-data psacp-post-year">'.get_the_date('Y').'</span>';
			}

			// Post Year
			if( $meta_key == 'month' ) {
				$result[$meta_key] = '<span class="psacp-post-meta-data psacp-post-month">'.get_the_date('M').'</span>';
			}

			// Post Day
			if( $meta_key == 'day' ) {
				$result[$meta_key] = '<span class="psacp-post-meta-data psacp-post-day">'.get_the_date('d').'</span>';
			}

			// Post Date
			if( $meta_key == 'comments' ) {

				$comment_count	= get_comments_number();
				$icon			= ( $args['icon'] ) ? '<i class="fa fa-comments"></i>' : null;

				if( (! $args['hide_empty']) || ($args['hide_empty'] && $comment_count > 0) ) {
					$result[$meta_key] = '<span class="psacp-post-meta-data psacp-post-comments">'. $icon . $comment_count .' '. $args['comment_text'].'</span>';
				}
			}

			// Post Category
			if( $meta_key == 'category' ) {
				$icon		= ( $args['icon'] ) ? '<i class="fa fa-folder-open"></i>' : null;
				$cat_list	= psac_get_post_terms( $args['post_id'], $args['taxonomy'], $args['tag_limit'] );

				if( $cat_list ) {
					$result[$meta_key] = '<span class="psacp-post-meta-data psacp-post-cats">'. $icon . $cat_list.'</span>';
				}
			}

			// Post Category
			if( $meta_key == 'tag' ) {
				$icon		= ( $args['icon'] ) ? '<i class="fa fa fa-tags"></i>' : null;
				$tag_list	= psac_get_post_terms( $args['post_id'], $args['tag_taxonomy'], $args['tag_limit'], ', ' );

				if( $tag_list ) {
					$result[$meta_key] = '<span class="psacp-post-meta-data psacp-post-tags">'. $icon . $tag_list.'</span>';
				}
			}
		}
	}

	// HTML Output
	if( $output == 'html' ) {
		$result = join( $join, $result );
	}

	return $result;
}

/**
 * Function to get registered post types
 * 
 * @since 3.5
 */
function psac_get_post_types() {

	$post_types     = array();
	$reg_post_types = get_post_types( array('public' => true), 'name' );

	// Exclude some builin WP Post Types
	$exclude_post = array('attachment', 'revision', 'nav_menu_item');

	foreach ($reg_post_types as $post_type_key => $post_data) {
		if( ! in_array( $post_type_key, $exclude_post) ) {
			$post_types[$post_type_key] = $post_data->label;
		}
	}

	return $post_types;
}

/**
 * Function to get registered Taxonomies List based on post type
 * 
 * @since 3.5
 */
function psac_get_taxonomies( $post_type = '', $output = '' ) {
	
	// Taking some variables
	$result         = array();
	$taxonomy_list  = '';

	if( $post_type ) {

		$taxonomy_objects = get_object_taxonomies( $post_type, 'object' );

		if( ! empty($taxonomy_objects) && ! is_wp_error($taxonomy_objects) )  {
			foreach($taxonomy_objects as $object => $taxonomy) { 
				if( !empty($taxonomy->public) && 'post_format' != $object ) {					
					
					if( $output == 'list' ) {
						$result[] = $object;
					} else {
						$result[$object] = ! empty( $taxonomy->label ) ? $taxonomy->label : $object;
					}
				}
			}
		}

		// If output is list
		if( $output == 'list' ) {
			$result = implode(', ', $result);
		}
	}
	return $result;
}

/**
 * Get Taxonomies 
 * 
 * @since 1.0
 */
function psac_get_taxonomy_options($objects = array(), $selected_val = '') {

	$output = '';

	if( ! empty( $objects ) && ! is_wp_error( $objects ) ) {
		foreach($objects as $object => $taxonomy) {
			if( 'post_format' != $object && ! empty( $taxonomy->public ) ) {
				$output .= '<option value="'. esc_attr( $object ) .'" '.selected( $selected_val, $object, false ).'>'. esc_attr( $taxonomy->label . ' - '.$taxonomy->name ) .'</option>';
			}
		}
	} else {
		$output .= '<option value="">'.esc_html__('No Taxonomies Found', 'post-slider-and-carousel').'</option>';
	}
	return $output;
}

/**
 * Get Post Format
 * 
 * @since 1.0
 */
function psac_get_post_format($post_id = '') {

	$format	= get_post_format( $post_id );
	$format	= empty( $format ) ? 'standard' : $format;

	return $format;
}

/**
 * Function to validate that public script should be enqueue at last.
 * Call this function at last.
 * @since 1.0
 */
function psac_enqueue_script() {

	// Check public script is in queue
	if( wp_script_is( 'psacp-public-script', 'enqueued' ) ) {
		
		// Dequeue Script
		wp_dequeue_script( 'psacp-public-script' );

		// Enqueue Script
		wp_enqueue_script( 'psacp-public-script' );
	}
}

/**
 * Get Post Format HTML
 * 
 * @since 1.0
 */
function psac_post_format_html( $format ) {
	$result = '';

	if($format == 'video') {
		$result = '<span class="psacp-format-icon"><i class="psacp-post-icon fa fa-play"></i></span>';
	} else if ($format == 'audio') {
			$result = '<span class="psacp-format-icon"><i class="psacp-post-icon fa fa-music"></i></span>';
	} else if ($format == 'quote') {
			$result = '<span class="psacp-format-icon"><i class="psacp-post-icon fa fa-quote-left"></i></span>';
	} else if ($format == 'gallery') {
			$result = '<span class="psacp-format-icon"><i class="psacp-post-icon fa fa-picture-o"></i></span>';
	} else if ($format == 'link') {
			$result = '<span class="psacp-format-icon"><i class="psacp-post-icon fa fa-link"></i></span>';
	} else {
		$result = '<span class="psacp-format-icon"><i class="psacp-post-icon fa fa-thumb-tack"></i></span>';
	}

	return $result;
}

/**
 * Function to get post carousel 'psac_post_carousel' shortcode design
 * 
 * @since 1.0
 */
function psac_post_carousel_designs() {
	
	$design_arr = array(
		'design-1'  => esc_html__('Design 1', 'post-slider-and-carousel'),
		'design-2'  => esc_html__('Design 2', 'post-slider-and-carousel'),
	);

	return $design_arr;
}

/**
 * Function to get post slider 'psacp_post_slider' shortcode design
 * 
 * @since 1.0
 */
function psac_post_slider_designs() {
	
	$design_arr = array(
		'design-1'  => esc_html__('Design 1', 'post-slider-and-carousel'),
		'design-2'  => esc_html__('Design 2', 'post-slider-and-carousel'),
	);

	return $design_arr;
}

/**
 * Function to get post scrolling widgets design
 * 
 * @since 1.0
 */
function psac_post_scrolling_widget_designs() {
	
	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'post-slider-and-carousel'),
	);

	return $design_arr;
}

/**
 * Get plugin registered shortcodes
 * 
 * @since 1.0
 */
function psac_registered_shortcodes( $type = 'simplified' ) {

	$result		= array();
	$shortcodes = array(
					'general' => array(
									'name'			=> esc_html__('General', 'post-slider-and-carousel'),
									'shortcodes'	=> array(
															'psac_post_slider'			=> esc_html__('Post Slider', 'post-slider-and-carousel'),
															'psac_post_carousel'		=> esc_html__('Post Carousel', 'post-slider-and-carousel'),															
															'psac_post_gridbox_slider'	=> esc_html__('Post GridBox Slider', 'post-slider-and-carousel'),
														)
									),
					);

	// For simplified result
	if( $type == 'simplified' && ! empty( $shortcodes ) ) {
		foreach ($shortcodes as $shrt_key => $shrt_val) {
			if( is_array( $shrt_val ) && ! empty( $shrt_val['shortcodes'] ) ) {
				$result = array_merge( $result, $shrt_val['shortcodes'] );
			} else {
				$result[ $shrt_key ] = $shrt_val;
			}
		}
	} else {
		$result = $shortcodes;
	}
	return $result;
}

/**
 * Get plugin allowed registered shortcodes
 * 
 * @since 3.5
 */
function psac_allowed_reg_shortcodes() {
	return array( 'psac_post_slider', 'psac_post_carousel' );
}

/**
 * Get plugin supported / enabled post types
 * 
 * @since 3.5
 */
function psac_get_supported_post_types() {

	$result					= array();
	$registered_post_types	= psac_get_post_types();
	$enabled_post_types		= psac_get_option( 'post_types', array() );

	if( ! empty( $enabled_post_types ) && ! empty( $registered_post_types ) ) {
		foreach ( $enabled_post_types as $post_key => $post_value ) {

			if( isset( $registered_post_types[ $post_value ] ) ) {
				$result[ $post_value ] = $registered_post_types[ $post_value ];
			}
		}
	}

	return $result;
}

/**
 * Get plugin supported / enabled post types
 * 
 * @since 3.5
 */
function psac_get_post_type_taxonomy( $post_type = PSAC_POST_TYPE, $empty_option = false ) {

	// Taking some variables
	$taxonomies = array();

	if( empty( $post_type ) ) {
		return $taxonomies;
	}

	// Get associated taxonomy
	$taxonomy_objects = get_object_taxonomies( $post_type, 'object' );

	if( ! empty( $taxonomy_objects ) && ! is_wp_error( $taxonomy_objects ) ) {

		if( $empty_option ) {
			$taxonomies[''] = __('Select Taxonomy', 'post-slider-and-carousel');
		}

		foreach( $taxonomy_objects as $object => $taxonomy ) {
			if( 'post_format' != $object && ! empty( $taxonomy->public ) ) {
				$taxonomies[ $object ] = ( $taxonomy->label . ' - ('.$taxonomy->name.')' );
			}
		}
	}

	return $taxonomies;
}