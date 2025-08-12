<?php
/**
 * Script Class
 *
 * Handles the script and style functionality of plugin
 *
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PSAC_Scripts {

	function __construct() {

		// Action for admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'psac_admin_script_style' ) );
		
		// Action for public scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'psac_public_script_style' ) );

		// Action to add custom CSS in head
		add_action( 'wp_head', array($this, 'psac_render_custom_css'), 20 );

		// Action to add admin script and style when edit with elementor at front side
		add_action( 'elementor/editor/after_enqueue_scripts', array($this, 'psac_admin_elementor_script_style') );
	}

	/**
	 * Registring and enqueing admin sctipts and styles
	 *
 	 * @since 1.0
	 */
	public function psac_admin_script_style( $hook_suffix ) {

		global $post_type, $post;

		$suffix				= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$allowed_post_types = psac_allowed_post_types();

		$pages_arr = array( PSAC_SCREEN_ID.'_page_psacp-settings', 'toplevel_page_psacp-layouts', PSAC_SCREEN_ID.'_page_psacp-styles' );

		/* Styles */

		// Select2 Style
		if( ! wp_style_is( 'select2', 'registered' ) ) {
			wp_register_style( 'select2', PSAC_URL.'assets/css/select2.min.css', array(), '4.0.3' );
		}

		// FS Pricing CSS
		if( PSAC_SCREEN_ID.'_page_psacp-layouts-pricing' == $hook_suffix ) {
			wp_register_style( 'psacp-fs-pricing', PSAC_URL . 'assets/css/fs-pricing.css', array(), PSAC_VERSION );
			wp_enqueue_style( 'psacp-fs-pricing' );
		}

		// Admin Style
		wp_register_style( 'psacp-admin-style', PSAC_URL . "assets/css/psacp-admin{$suffix}.css", array(), PSAC_VERSION );


		/* Scripts */

		// Select2 JS
		if( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( 'select2', PSAC_URL.'assets/js/select2.full.min.js', array('jquery'), '4.0.3', true );
		}

		// Shortcode Generator JS
		wp_register_script( 'psacp-shrt-generator', PSAC_URL . "assets/js/psacp-shortcode-generator.min.js", array( 'jquery' ), PSAC_VERSION, true );
		wp_localize_script( 'psacp-shrt-generator', 'Psacp_Shrt_Generator', array(
														'shortcode_err'				=> esc_js( __('Sorry, Something happened wrong. Kindly please be sure that you have choosen relevant shortcode from the dropdown.', 'post-slider-and-carousel') ),
														'select2_input_too_short'	=> esc_js( __( 'Please enter 1 or more characters', 'post-slider-and-carousel' ) ),
														'select2_remove_all_items'	=> esc_js( __( 'Remove all items', 'post-slider-and-carousel' ) ),
														'select2_remove_item'		=> esc_js( __( 'Remove item', 'post-slider-and-carousel' ) ),
														'select2_searching'			=> esc_js( __( 'Searching…', 'post-slider-and-carousel' ) ),
														'select2_placeholder'		=> esc_js( __( 'Select Data', 'post-slider-and-carousel' ) ),
													));

		// Admin JS
		wp_register_script( 'psacp-admin-script', PSAC_URL . "assets/js/psacp-admin{$suffix}.js", array( 'jquery' ), PSAC_VERSION, true );
		wp_localize_script( 'psacp-admin-script', 'PsacpAdmin', array(
																	'syntax_highlighting'	=> ( 'false' === wp_get_current_user()->syntax_highlighting ) ? 0 : 1,
																	'confirm_msg'			=> esc_js( __('Are you sure you want to do this?', 'post-slider-and-carousel') ),
																	'reset_msg'				=> esc_js( __('Click OK to reset all options. All settings will be lost!', 'post-slider-and-carousel') ),
																	'reset_post_view_msg'	=> esc_js( __('Click OK to reset post view count. This process can not be undone!', 'post-slider-and-carousel') ),
																	'wait_msg'				=> esc_js( __('Please Wait...', 'post-slider-and-carousel') ),
																));

		// All Layouts Page
		if( 'toplevel_page_psacp-layouts' == $hook_suffix ) {
			wp_enqueue_script( 'clipboard' );
		}

		// Post Screen, Taxonomy Screen and Widget Screen
		if( ($hook_suffix == 'widgets.php') || (in_array( $post_type, $allowed_post_types ) && ( $hook_suffix == 'post.php' || $hook_suffix == 'post-new.php' )) ) {
			wp_enqueue_style( 'psacp-admin-style' ); 	// Admin Styles
			wp_enqueue_script( 'psacp-admin-script' );	// Admin Script
		}

		if( in_array( $hook_suffix, $pages_arr ) ) {

			// Admin Styles
			wp_enqueue_style( 'psacp-admin-style' );

			/* --------------------------------- */

			// Admin Scripts
			if( ! empty( $_GET['tab'] ) && $_GET['tab'] == 'css' ) {
				wp_enqueue_code_editor( array(
					'type' 			=> 'text/css',
					'codemirror' 	=> array(
						'indentUnit' 	=> 2,
						'tabSize'		=> 2,
					),
				) );
			}

			wp_enqueue_script( 'postbox' );
			wp_enqueue_script( 'psacp-admin-script' );
			wp_enqueue_media();
		}

		// Shortcode Builder
		if( $hook_suffix == PSAC_SCREEN_ID.'_page_psacp-shrt-builder' || PSAC_SCREEN_ID.'_page_psacp-layout' == $hook_suffix ) {
			wp_enqueue_style( 'select2' );
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_style( 'psacp-admin-style' );

			wp_enqueue_script( 'clipboard' );
			wp_enqueue_script('shortcode');
			wp_enqueue_script('jquery-ui-accordion');
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'psacp-admin-script' );
			wp_enqueue_script( 'psacp-shrt-generator' );
		}

		// For VC Front End Page Editing
		if( function_exists('vc_is_frontend_editor') && vc_is_frontend_editor() ) {
			wp_register_script( 'psacp-vc-frontend', PSAC_URL . 'assets/js/vc/psacp-vc-frontend.js', array(), PSAC_VERSION, true );
			wp_enqueue_script( 'psacp-vc-frontend' );
		}
	}

	/**
	 * Registring and enqueing public scripts
	 *
 	 * @since 1.0
	 */
	public  function psac_public_script_style() {

		global $post;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/***** Styles *****/
		// Registring font awesome css
		if( ! wp_style_is( 'inf-font-awesome', 'registered' ) && psac_get_option('disable_font_awsm_css') == 0 ) {
			wp_register_style( 'inf-font-awesome', PSAC_URL . 'assets/css/font-awesome.min.css', array(), PSAC_VERSION );
		}

		// Registring owl slider css
		if( ! wp_style_is( 'owl-carousel', 'registered' ) && psac_get_option('disable_owl_css') == 0 ) {
			wp_register_style( 'owl-carousel', PSAC_URL.'assets/css/owl.carousel.min.css', array(), PSAC_VERSION );
		}

		// Registring public script
		wp_register_style( 'psacp-public-style', PSAC_URL . "assets/css/psacp-public{$suffix}.css", array(), PSAC_VERSION );

		// Enqueing Script
		wp_enqueue_style( 'inf-font-awesome' );
		wp_enqueue_style( 'owl-carousel' );
		wp_enqueue_style( 'psacp-public-style' );


		/***** Scripts *****/

		// Taking post id to update post view count
		$post_id = isset( $post->ID ) ? $post->ID : '';

		// Registring slick slider script
		if( ! wp_script_is( 'jquery-owl-carousel', 'registered' ) ) {
			wp_register_script( 'jquery-owl-carousel', PSAC_URL. 'assets/js/owl.carousel.min.js', array('jquery'), PSAC_VERSION, true );
		}

		// Registring slick slider script
		if( ! wp_script_is( 'jquery-vticker', 'registered' ) ) {
			wp_register_script( 'jquery-vticker', PSAC_URL. "assets/js/jquery-vticker{$suffix}.js", array('jquery'), PSAC_VERSION, true );
		}
		
		// Admin Script (Do not forgot to update for elementor script action also)
		wp_register_script( 'psacp-public-script', PSAC_URL . "assets/js/psacp-public{$suffix}.js", array( 'jquery' ), PSAC_VERSION, true );
		wp_localize_script( 'psacp-public-script', 'Psacp', array( 
																'ajax_url' 			=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
																'is_mobile'			=> wp_is_mobile(),
																'is_rtl' 			=> ( is_rtl() ) ? 1 : 0,
																'fix_owl_conflict'	=> psac_get_option( 'fix_owl_conflict', 0 ),
																'vc_page_edit'		=> ( function_exists('vc_is_page_editable') && vc_is_page_editable() ) ? 1 : 0,
															));

		/*===== Page Builder Scripts =====*/
		// VC Front End Page Editing
		if ( function_exists('vc_is_page_editable') && vc_is_page_editable() ) {
			wp_enqueue_script( 'jquery-owl-carousel' );
			wp_enqueue_script( 'jquery-vticker' );
			wp_enqueue_script( 'psacp-public-script' );
		}

		// Elementor Frontend Editing
		if ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_GET['elementor-preview'] ) && $post_id == (int) $_GET['elementor-preview'] ) {
			wp_register_script( 'psacp-elementor-script', PSAC_URL . 'assets/js/elementor/psacp-elementor.js', array(), PSAC_VERSION, true );

			wp_enqueue_script( 'jquery-owl-carousel' );
			wp_enqueue_script( 'jquery-vticker' );
			wp_enqueue_script( 'psacp-public-script' );
			wp_enqueue_script( 'psacp-elementor-script' );
		}
	}

	/**
	 * Add custom css to head
	 * 
	 * @since 1.0
	 */
	function psac_render_custom_css() {

		// Custom CSS
		$custom_css = psac_get_option('custom_css');

		if( ! empty( $custom_css ) ) {
			echo '<style type="text/css">' . "\n" .
					wp_strip_all_tags( $custom_css )
				 . "\n" . '</style>' . "\n";
		}
	}

	/**
	 * Add admin script and style when edit with elementor at front side
	 * 
	 * @since 1.0
	 */
	function psac_admin_elementor_script_style() {

		global $post;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Admin Style
		wp_register_style( 'psacp-admin-style', PSAC_URL . "assets/css/psacp-admin{$suffix}.css", array(), PSAC_VERSION );

		// Admin Script
		wp_register_script( 'psacp-admin-script', PSAC_URL . "assets/js/psacp-admin{$suffix}.js", array( 'jquery' ), PSAC_VERSION, true );
		wp_localize_script( 'psacp-admin-script', 'PsacpAdmin', array(
																	'post_id'				=> isset( $post->ID ) ? $post->ID : 0,
																	'syntax_highlighting'	=> ( 'false' === wp_get_current_user()->syntax_highlighting ) ? 0 : 1,
																	'elementor'				=> 1,
																	'confirm_msg'			=> esc_js( __('Are you sure you want to do this?', 'post-slider-and-carousel') ),
																	'reset_msg'				=> esc_js( __('Click OK to reset all options. All settings will be lost!', 'post-slider-and-carousel') ),
																	'reset_post_view_msg'	=> esc_js( __('Click OK to reset post view count. This process can not be undone!', 'post-slider-and-carousel') ),
																	'wait_msg'				=> esc_js( __('Please Wait...', 'post-slider-and-carousel') ),
																));

		wp_enqueue_style( 'psacp-admin-style' ); 	// Admin Styles
		wp_enqueue_script( 'psacp-admin-script' );	// Admin Script
	}
}