<?php
/**
 * Register Settings
 *
 * @package Post Slider and Carousel
 * @since 3.5
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin register settings
 * Handles to register plugin settings
 *
 * @since 3.5
 */
function psac_register_settings() {
	register_setting( 'psacp_settings', 'psacp_opts', 'psac_validate_settings' );
}

// Action to register settings
add_action( 'admin_init', 'psac_register_settings' );

/**
 * Handles to validate plugin settings before updation
 *
 * @since 3.5
 */
function psac_validate_settings( $input ) {
	
	global $psacp_options;
	
	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}
	
	$input = $input ? $input : array();
	
	parse_str( $_POST['_wp_http_referer'], $referrer );
	$tab = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
	
	// Sanitize filter according to tab
	if( $tab ) {
		$input = apply_filters( 'psac_validate_settings_' . $tab, $input );
	}
	
	// General sanitize filter
	$input = apply_filters( 'psac_validate_settings', $input );
	
	// Merge our new settings with the existing
	$output = array_merge( $psacp_options, $input );
	
	return $output;
}

/**
 * Plugin Settings Tab array
 *
 * @since 3.5
 */
function psac_settings_tab() {
	
	$result_arr		= array();
	$settings_arr	= array(
							'welcome'	=> __('Welcome', 'post-slider-and-carousel'),
							'general'	=> __('General', 'post-slider-and-carousel'),
							'trending'	=> __('Trending Post', 'post-slider-and-carousel'),
							'sharing'	=> __('Sharing', 'post-slider-and-carousel'),
							'css'		=> __('CSS', 'post-slider-and-carousel'),
							'misc'		=> __('Misc', 'post-slider-and-carousel'),
							'pro'		=> __('Premium Features', 'post-slider-and-carousel'),
						);

	foreach ( $settings_arr as $sett_key => $sett_val ) {
		if( ! empty($sett_key) && ! empty( $sett_val ) ) {
			$result_arr[trim( $sett_key )] = trim( $sett_val );
		}
	}

	return $result_arr;
}

/**
 * Plugin default settings
 *
 * @since 3.5
 */
function psac_default_settings() {
	
	$psacp_options = array(
					'post_types'			=> array(0 => 'post'),
					'post_first_img'		=> 1,
					'post_default_feat_img'	=> '',
					'custom_css'			=> '',
					'disable_font_awsm_css'	=> 0,
					'disable_owl_css'		=> 0,
					'post_content_fix'		=> 1,
					'fix_owl_conflict'		=> 0,
				);

	return $psacp_options;
}

/**
 * Plugin Setup On First Time Activation
 *
 * Does the initial setup when plugin is going to activate first time,
 * set default values for the plugin options.
 *
 * @since 3.5
 */
function psac_set_default_settings() {

	global $psacp_options;

	// Plugin default settings
	$psacp_options = psac_default_settings();

	// Update default options
	update_option( 'psacp_opts', $psacp_options );
}

/**
 * Get Settings From Option Page
 * 
 * Handles to return all settings value
 * 
 * @since 3.5
 */
function psac_get_settings() {
	
	$options	= get_option('psacp_opts');
	$settings	= ( is_array( $options ) ) ? $options : array();

	return $settings;
}

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 3.5
 */
function psac_get_option( $key = '', $default = false ) {

	global $psacp_options;

	// Get default settings
	$default_setting = psac_default_settings();

	if ( ! isset( $psacp_options[ $key ] ) && isset( $default_setting[ $key ] ) && ! $default ) {
		$value = $default_setting[ $key ];
	} else {
		$value = ! empty( $psacp_options[ $key ] ) ? $psacp_options[ $key ] : $default;
	}

	return $value;
}

/**
 * Handles to validate General tab settings
 *
 * @since 3.5
 */
function psac_validate_general_settings( $input ) {

	$input['post_types'] 			= isset( $input['post_types'] ) 			? psac_clean( $input['post_types'] ) : array();
	$input['post_first_img']		= isset( $input['post_first_img'] ) 		? 1 : 0;
	$input['post_default_feat_img']	= isset( $input['post_default_feat_img'] )	? psac_clean_url( $input['post_default_feat_img'] ) : '';

	// check default post type
	if( ! in_array('post', $input['post_types']) ) {
		$input['post_types'][] = 'post';
	}

	return $input;
}
add_filter( 'psac_validate_settings_general', 'psac_validate_general_settings', 9, 1 );

/**
 * Handles to validate CSS tab settings
 *
 * @since 3.5
 */
function psac_validate_css_settings( $input ) {

	$input['custom_css'] = isset($input['custom_css']) ? sanitize_textarea_field( $input['custom_css'] ) : '';

	return $input;
}
add_filter( 'psac_validate_settings_css', 'psac_validate_css_settings', 9, 1 );

/**
 * Handles to validate Misc tab settings
 *
 * @since 3.5
 */
function psac_validate_misc_settings( $input ) {

	$input['post_content_fix']		= ! empty( $input['post_content_fix'] )			? 1 : 0;
	$input['disable_font_awsm_css'] = ! empty( $input['disable_font_awsm_css'] ) 	? 1 : 0;
	$input['disable_owl_css'] 		= ! empty( $input['disable_owl_css'] ) 			? 1 : 0;
	$input['fix_owl_conflict'] 		= ! empty( $input['fix_owl_conflict'] ) 		? 1 : 0;

	return $input;
}
add_filter( 'psac_validate_settings_misc', 'psac_validate_misc_settings', 9, 1 );