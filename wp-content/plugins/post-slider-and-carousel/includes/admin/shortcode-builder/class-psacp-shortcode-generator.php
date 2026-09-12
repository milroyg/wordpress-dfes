<?php
/**
 * Shortcode Generator Class
 * Handles shortcode builder data functionality.
 *
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PSAC_Shortcode_Generator {

	function __construct() {
		
		// Ajax action to get shortcode parameters data
		add_action( 'wp_ajax_psac_get_shrt_params_data', array( $this, 'psac_get_shrt_params_data' ) );

		// Ajax action to get categories based on search
		add_action( 'wp_ajax_psac_category_sugg', array( $this, 'psac_category_sugg' ) );
	}

	/**
	 * Get shortcode parameters data
	 * 
	 * @since 3.5
	 */
	function psac_get_shrt_params_data() {

		// Taking some defaults
		$result		= array(
							'success'			=> 0,
							'msg'				=> esc_js( __('Sorry, Something happened wrong.', 'post-slider-and-carousel') ),
							'data'				=> array(),
							'invalid_params'	=> array(),
						);
		$params				= isset( $_POST['params'] )				? psac_clean( $_POST['params'] )				: '';
		$predefined_params	= isset( $_POST['predefined_params'] )	? psac_clean( $_POST['predefined_params'] )	: '';
		$shortcode			= isset( $_POST['shortcode'] )			? psac_clean( $_POST['shortcode'] )			: '';
		$psacp_nonce		= isset( $_POST['nonce'] )				? psac_clean( $_POST['nonce'] )				: '';

		if( ! wp_verify_nonce( $psacp_nonce, 'psacp-shortcode-builder' ) ) {
			wp_send_json( $result );	
		}

		// Taking shortcode arguments
		$taxonomy = PSAC_CAT;

		/***** Category *****/
		$category_params = array( 'category' );

		foreach( $category_params as $param ) {

			$result['data'][$param] = '';

			/* Append the predefined data */
			if( ! empty( $predefined_params[$param] ) ) {

				foreach( $predefined_params[$param] as $predefined_param_key => $predefined_param_data ) {
					
					if( ! isset( $predefined_param_data['id'] ) || ! isset( $predefined_param_data['text'] ) ) {
						continue;
					}

					$result['data'][$param] .= '<option value="'.esc_attr( $predefined_param_data['id'] ).'" selected="selected">'.esc_html( $predefined_param_data['text'] ).'</option>';
				}
			}

			if( empty( $params[$param] ) ) {
				continue;
			}

			$result['data'][$param] = isset( $result['data'][$param] ) ? $result['data'][$param] : '';
			$param_data				= psac_clean( explode( ',', $params[$param] ) );
			$processed_data			= array();

			if( $taxonomy ) {
				$terms_args = array(
								'taxonomy'		=> $taxonomy,
								'orderby'		=> 'name',
								'order'			=> 'ASC',
								'number'		=> 0,
								'fields'		=> 'id=>name',
								'hide_empty'	=> false,
							);

				// Compatibility with slug
				if( is_numeric( $param_data[0] ) ) {
					$terms_args['include'] = $param_data;
				} else {
					$terms_args['slug'] = $param_data;
				}

				$terms = get_terms( $terms_args );

				if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term_id => $term ) {
						
						$term_title	= ( $term != '' ) ? $term : __('Category', 'post-slider-and-carousel');
						$term_title	= $term_title . " - (#{$term_id})";

						$result['data'][$param] .= '<option value="'.esc_attr( $term_id ).'" selected="selected">'.esc_html( $term_title ).'</option>';

						// Store temp data
						$processed_data[ $term_id ] = $term_id;
					}
				}
			}

			/**
			 * Loop of params data so we can identify the missing values due to delete or not available with current selection
			 */
			foreach( $param_data as $param_data_key => $param_data_val ) {
				if( ! isset( $processed_data[ $param_data_val ] ) && $param_data_val != '' ) {
					$result['data'][$param] .= '<option value="'.esc_attr( $param_data_val ).'" selected="selected">'.esc_html( "Term - (#{$param_data_val}) (Not Available)" ).'</option>';

					// Set invalid parameters
					$result['invalid_params'][$param][] = $param_data_val;
				}
			}
		}

		$result['success']	= 1;
		$result['msg']		= esc_js( __('Success', 'post-slider-and-carousel') );

		$result = apply_filters( 'psacp_shrt_builder_params_data', $result, $params );

		wp_send_json( $result );
	}

	/**
	 * Get Category Suggestion
	 * 
	 * @since 3.5
	 */
	function psac_category_sugg() {

		// Taking some defaults
		$result			= array();
		$taxonomy		= isset( $_GET['taxonomy'] )	? psac_clean( $_GET['taxonomy'] )	: '';
		$search			= isset( $_GET['search'] )		? psac_clean( $_GET['search'] )	: '';
		$psacp_nonce	= isset( $_GET['nonce'] )		? psac_clean( $_GET['nonce'] )		: '';

		if( ! empty( $taxonomy ) && $search && wp_verify_nonce( $psacp_nonce, 'psacp-shortcode-builder' ) ) {

			$terms_args = array(
									'taxonomy'		=> $taxonomy,
									'orderby'		=> 'name',
									'order'			=> 'ASC',
									'number'		=> 25,
									'fields'		=> 'id=>name',
									'hide_empty'	=> false,
								);

			if( ctype_digit( $search ) ) {
				$terms_args['include'] = $search;
			} else {
				$terms_args['search'] = $search;
			}

			$terms = get_terms( $terms_args );

			if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term_id => $term ) {
					
					$term_title	= ( $term != '' ) ? $term : __('Category', 'post-slider-and-carousel');
					$term_title	= $term_title . " - (#{$term_id})";

					$result[]	= array( $term_id, $term_title );
				}
			}

			$result = apply_filters( 'psacp_shrt_builder_category_sugg', $result );
		}

		wp_send_json( $result );
	}
}

$psac_shortcode_generator = new PSAC_Shortcode_Generator();