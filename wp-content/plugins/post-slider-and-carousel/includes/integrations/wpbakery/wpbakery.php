<?php
/**
 * WPBakery Shortcode Support
 *
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Action to add 'psacp_tmpl' shortcode in WPBakery
 * 
 * @since 3.5
 */
function psac_map_wpb_shortcodes() {

	// Taking some variables
	$add_layout_url = add_query_arg( array('page' => 'psacp-layout', 'shortcode' => 'psac_post_slider', 'action' => 'add'), admin_url('admin.php') );

	// Post Grid
		vc_map( array(
			'name' 			=> __( 'Post Slider and Carousel', 'post-slider-and-carousel' ),
			'base' 			=> 'psacp_tmpl',
			'icon' 			=> PSAC_URL.'assets/images/small-icon.jpg',
			'class' 		=> '',
			'category' 		=> __( 'Content', 'post-slider-and-carousel'),
			'description' 	=> __( 'Display post in a slider, carousel, gridbox and many more view.', 'post-slider-and-carousel' ),
			'params' 		=> array(
								// General settings
								array(
									'type'			=> 'autocomplete',
									'heading'		=> __( 'Layout', 'post-slider-and-carousel' ),
									'param_name'	=> 'layout_id',
									'value'			=> '',
									'description'	=> sprintf( esc_html__( 'Choose your created layout by name or id. You can create the layout from %shere%s.', 'post-slider-and-carousel' ), '<a href="'.esc_url( $add_layout_url ).'" target="_black">', '</a>' ),
									'admin_label'	=> true,
									'settings'		=> array(
														'multiple' => false,
													),
									'param_holder_class' => 'psacp-tmpl-wpb-layout-id',
								),
								array(
									'type'			=> 'dropdown',
									'heading'		=> esc_html__( 'Preview', 'post-slider-and-carousel' ),
									'param_name'	=> 'psac_layout_preview',
									'value'			=> array(
															esc_html__( 'Yes', 'post-slider-and-carousel' )	=> 'yes',
															esc_html__( 'No', 'post-slider-and-carousel' )	=> 'no',
														),
									'std'			=> 'yes',
									'description'	=> esc_html__( 'Enable layout preview in editor mode.', 'post-slider-and-carousel' ),
								),
							)
		));
}
add_action( 'vc_before_init', 'psac_map_wpb_shortcodes' );

// If WPBakery auto complete action is fired
if ( 'vc_get_autocomplete_suggestion' === vc_request_param( 'action' ) || 'vc_edit_form' === vc_post_param( 'action' ) ) {

	// Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_psacp_tmpl_layout_id_callback', 'psac_wpb_layout_id_field_search', 10, 1 );
	
	// Render exact product. Must return an array (label,value)
	add_filter( 'vc_autocomplete_psacp_tmpl_layout_id_render', 'psac_wpb_layout_id_field_search', 10, 1 );

	/**
	 * @param $search_string
	 *
	 * @return array
	 */
	function psac_wpb_layout_id_field_search( $search_string ) {

		// If we are rendering the data
		$render			= false;
		$post_status	= array( 'publish' );

		if( isset( $search_string['value'] ) ) {
			$render			= true;
			$search_string	= $search_string['value'];
			$post_status	= array( 'publish', 'pending' );
		}

		// Taking some defaults
		$data		= array();
		$posts_args = array(
								'post_type'				=> PSAC_LAYOUT_POST_TYPE,
								'post_status'			=> $post_status,
								'order'					=> 'ASC',
								'orderby'				=> 'title',
								'limit'					=> 25,
								'no_found_rows'			=> true,
								'ignore_sticky_posts'	=> true,
							);

		if( ctype_digit( $search_string ) ) {
			$posts_args['post__in'] = explode( ',', $search_string );
		} else {
			$posts_args['s'] = $search_string;
		}

		$search_query = get_posts( $posts_args );

		if( $search_query ) {
			foreach ( $search_query as $search_data ) {
				
				$post_status	= ( ! empty( $search_data->post_status ) && 'publish' != $search_data->post_status ) ? ' - '.ucfirst( $search_data->post_status ) : '';
				$post_title		= ! empty( $search_data->post_title ) ? $search_data->post_title : __('Post', 'post-slider-and-carousel');
				$post_title		= $post_title . " - (#{$search_data->ID}{$post_status})";

				$result[]		= array( $search_data->ID, $post_title );

				if( $render ) {

					$data = array(
						'value' => $search_data->ID,
						'label' => $post_title,
						'group' => $search_data->post_type,
					);

				} else {

					$data[] = array(
						'value' => $search_data->ID,
						'label' => $post_title,
						'group' => $search_data->post_type,
					);
				}
			}
		}

		return $data;
	}
}