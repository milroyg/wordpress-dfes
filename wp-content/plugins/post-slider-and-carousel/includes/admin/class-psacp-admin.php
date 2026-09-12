<?php
/**
 * Admin Class
 * Handles the admin side functionality of plugin
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PSAC_Admin {

	function __construct() {

		// Action to register admin menu
		add_action( 'admin_menu', array( $this, 'psac_register_menu' ) );

		// Shortcode Preview
		add_action( 'current_screen', array($this, 'psac_generate_preview_screen') );

		// Admin prior processes
		add_action( 'admin_init', array( $this, 'psac_admin_init_process' ) );

		// Filter for post row data
		add_filter( 'post_row_actions', array($this, 'psac_manage_post_row_data'), 10, 2 );
	}

	/**
	 * Function to register admin menus
	 * 
	 * @since 1.0
	 */
	function psac_register_menu() {

		// Getting Started Page
		add_menu_page( __('Post Slider and Carousel', 'post-slider-and-carousel'), __('Post Slider and Carousel', 'post-slider-and-carousel'), 'manage_options', 'psacp-layouts', array($this, 'psac_plugin_all_layouts'), 'dashicons-sticky' );

		// All Layouts Page
		add_submenu_page( 'psacp-layouts', __('All Layouts - Post Slider and Carousel', 'post-slider-and-carousel'), __('All Layouts', 'post-slider-and-carousel'), 'manage_options', 'psacp-layouts', array($this, 'psac_plugin_all_layouts') );

		// Add New Layout Page
		add_submenu_page( 'psacp-layouts', __('Layout - Post Slider and Carousel', 'post-slider-and-carousel'), __('Add New Layout', 'post-slider-and-carousel'), 'manage_options', 'psacp-layout', array($this, 'psac_plugin_add_layout') );

		// Shortcode Builder
		add_submenu_page( 'psacp-layouts', __('Shortcode Builder - Post Slider and Carousel', 'post-slider-and-carousel'), __('Shortcode Builder', 'post-slider-and-carousel'), 'manage_options', 'psacp-shrt-builder', array($this, 'psac_shortcode_generator') );

		// Style Manager
		add_submenu_page( 'psacp-layouts', __('Style Manager - Post Slider and Carousel', 'post-slider-and-carousel'), __('Style Manager', 'post-slider-and-carousel'), 'manage_options', 'psacp-styles', array($this, 'psac_style_manager_page') );

		// Setting Page
		add_submenu_page( 'psacp-layouts', __('Post Slider and Carousel', 'post-slider-and-carousel'), __('Settings', 'post-slider-and-carousel'), 'manage_options', 'psacp-settings', array($this, 'psac_plugin_settings') );

		// Shortcode Preview
		add_submenu_page( '', __('Shortcode Preview - Post Slider and Carousel', 'post-slider-and-carousel'), __('Shortcode Preview', 'post-slider-and-carousel'), 'manage_options', 'psacp-shortcode-preview', array($this, 'psac_shortcode_preview_page') );
	}

	/**
	 * Plugin All Layouts Page
	 * 
	 * @since 3.5
	 */
	function psac_plugin_all_layouts() {
		include_once( PSAC_DIR . '/includes/admin/shortcode-builder/class-psacp-layout-list.php' );
	}

	/**
	 * Plugin Add / Edit Layout Page
	 * 
	 * @since 3.5
	 */
	function psac_plugin_add_layout() {
		include_once( PSAC_DIR . '/includes/admin/shortcode-builder/add-layout.php' );
	}

	/**
	 * Plugin Setting Page
	 * 
	 * @since 1.0
	 */
	function psac_plugin_settings() {
		include_once( PSAC_DIR . '/includes/admin/settings/psacp-settings.php' );
	}

	/**
	 * Plugin Shortcode Builder Page
	 * 
	 * @since 1.0
	 */
	function psac_shortcode_generator() {
		include_once( PSAC_DIR . '/includes/admin/shortcode-builder/shortcode-builder.php' );
	}

	/**
	 * Plugin Style Manager Page
	 * 
	 * @since 3.5
	 */
	function psac_style_manager_page() {
		include_once( PSAC_DIR . '/includes/style-manager/styles-form.php' );
	}

	/**
	 * Handle plugin shoercode preview
	 * 
	 * @since 1.0
	 */
	function psac_shortcode_preview_page() {
	}

	/**
	 * Handle plugin shoercode preview
	 * 
	 * @since 1.0
	 */
	function psac_generate_preview_screen( $screen ) {
		if( $screen->id == 'admin_page_psacp-shortcode-preview' ) {
			include_once( PSAC_DIR . '/includes/admin/shortcode-builder/shortcode-preview.php' );
			exit;
		}
	}

	/**
	 * Admin prior processes
	 *
	 * @since 3.5
	 */
	function psac_admin_init_process() {

		// Add Layout to database
		if( ! empty( $_POST['psacp_layout_save'] ) && ! empty( $_POST['psacp_layout_shrt_val'] ) && ! empty( $_POST['psacp_layout_save_nonce'] ) && wp_verify_nonce( $_POST['psacp_layout_save_nonce'], 'psacp-layout-save-nonce' ) ) {

			// Taking some variables
			$meta_prefix			= PSAC_META_PREFIX;
			$registered_shortcodes 	= psac_registered_shortcodes();
			$action					= ( ! empty( $_GET['action'] ) && 'edit' == $_GET['action'] ) ? 'edit' : 'add';
			$layout_id				= ! empty( $_GET['id'] )						? psac_clean_number( $_GET['id'] )					: '';
			$layout_shrt_type		= ! empty( $_POST['psacp_layout_shrt_type'] )	? psac_clean( $_POST['psacp_layout_shrt_type'] )		: '';
			$layout_desc			= ! empty( $_POST['psacp_layout_desc'] )		? sanitize_textarea_field( strip_shortcodes( $_POST['psacp_layout_desc'] ) )	: '';
			$layout_enable			= ! empty( $_POST['psacp_layout_enable'] )		? 1	: 0;
			$layout_shrt_val		= psac_clean( $_POST['psacp_layout_shrt_val'] );

			// If it is not a valid shortcode
			if( ! isset( $registered_shortcodes[ $layout_shrt_type ] ) || ! ( strpos( $layout_shrt_val, "[{$layout_shrt_type} " ) === 0 || strpos( $layout_shrt_val, "[{$layout_shrt_type}]" ) === 0 ) ) {
				
				$redirect_url = add_query_arg( array( 'message' => '0' ) );
				wp_redirect( $redirect_url );
				exit;
			}

			// Generating Layout Title
			$layout_title = ! empty( $_POST['psacp_layout_title'] ) ? psac_clean( $_POST['psacp_layout_title'] ) : __('Layout', 'post-slider-and-carousel') .' - '. $registered_shortcodes[ $layout_shrt_type ];

			// Insert the Layout Post
			$post_arr = array(
				'post_title'		=> $layout_title,
				'post_content'		=> $layout_desc,
				'post_type'			=> PSAC_LAYOUT_POST_TYPE,
				'post_status'		=> $layout_enable ? 'publish' : 'pending',
				'comment_status'	=> 'closed',
				'ping_status'		=> 'closed',
				'meta_input'		=> array(
										$meta_prefix.'layout_shrt'		=> $layout_shrt_val,
										$meta_prefix.'layout_shrt_type'	=> $layout_shrt_type,
									),
			);

			// If layout is being updated
			if( $layout_id ) {
				$post_arr['ID'] = $layout_id;
			}

			$layout_post_id = wp_insert_post( $post_arr );
			
			if( empty( $layout_post_id ) || is_wp_error( $layout_post_id ) ) {
				$redirect_url = add_query_arg( array( 'page' => 'psacp-layout', 'shortcode' => $layout_shrt_type, 'message' => '0' ), admin_url( 'admin.php' ) );
			} else {
				$redirect_url = add_query_arg( array( 'page' => 'psacp-layout', 'shortcode' => $layout_shrt_type, 'action' => 'edit', 'id' => $layout_post_id, 'message' => '1' ), admin_url( 'admin.php' ) );
			}

			wp_redirect( $redirect_url );
			exit;
		}

		// Delete Layout
		if( ( (isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) || (isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' ) )
			&& isset($_GET['page']) && $_GET['page'] == 'psacp-layouts'
			&& ! empty( $_GET['psacp_layout'] )
			&& ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'bulk-psacp_layouts' ) )
		) {

			// Get bulk array from $_GET
			$layout_ids = psac_clean( $_GET['psacp_layout'] );
			$layout_ids = (array)$layout_ids;

			// Loop to delete layouts
			if( ! empty( $layout_ids ) ) {
				foreach ( $layout_ids as $layout_id ) {

					if( empty( $layout_id ) ) {
						continue;
					}

					wp_delete_post( $layout_id, true );
				}
			}

			$redirect_url = add_query_arg( array( 'page' => 'psacp-layouts', 'message' => '1' ), admin_url( 'admin.php' ) );
			wp_redirect( $redirect_url );
			exit;
		}

		// Duplicate Layout
		if( isset( $_GET['action'] ) && $_GET['action'] == 'duplicate_layout'
			&& isset( $_GET['page'] ) && $_GET['page'] == 'psacp-layouts'
			&& ! empty( $_GET['id'] )
			&& ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], "psacp-duplicate-layout-{$_GET['id']}" ) )
		) {

			$meta_prefix	= PSAC_META_PREFIX;
			$layout_id		= psac_clean_number( $_GET['id'] );
			$layout_shrt	= isset( $_GET['shortcode'] ) ? psac_clean( $_GET['shortcode'] ) : false;
			$old_layout		= get_post( $layout_id );

			if( $old_layout && isset( $old_layout->post_type ) && $old_layout->post_type == PSAC_LAYOUT_POST_TYPE ) {
				
				$layout_shrt_type	= get_post_meta( $layout_id, $meta_prefix.'layout_shrt_type', true );
				$layout_shrt_val	= get_post_meta( $layout_id, $meta_prefix.'layout_shrt', true );

				// Duplicate the Layout Post
				$post_arr = array(
					'post_title'		=> $old_layout->post_title .' '. __('(Copy)', 'post-slider-and-carousel'),
					'post_content'		=> $old_layout->post_content,
					'post_type'			=> PSAC_LAYOUT_POST_TYPE,
					'post_status'		=> $old_layout->post_status,
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
					'meta_input'		=> array(
											$meta_prefix.'layout_shrt'		=> $layout_shrt_val,
											$meta_prefix.'layout_shrt_type'	=> $layout_shrt_type,
										),
				);

				$duplicate_layout_id = wp_insert_post( $post_arr );

				if( empty( $duplicate_layout_id ) || is_wp_error( $duplicate_layout_id ) ) {
					$redirect_url = add_query_arg( array( 'page' => 'psacp-layout', 'shortcode' => $layout_shrt, 'action' => 'edit', 'id' => $layout_id, 'message' => '0' ), admin_url( 'admin.php' ) );
				} else {
					$redirect_url = add_query_arg( array( 'page' => 'psacp-layout', 'shortcode' => $layout_shrt_type, 'action' => 'edit', 'id' => $duplicate_layout_id, 'message' => '2' ), admin_url( 'admin.php' ) );
				}

				wp_redirect( $redirect_url );
				exit;

			} else {

				$redirect_url = add_query_arg( array( 'page' => 'psacp-layout', 'shortcode' => $layout_shrt, 'action' => 'edit', 'id' => $layout_id, 'message' => '0' ), admin_url( 'admin.php' ) );
				wp_redirect( $redirect_url );
				exit;
			}
		}

		// A Tweak to remove some unnecessary paramters from the URL on layout listing page like WP did on post listing page.
		if ( ! empty( $_GET['page'] ) && 'psacp-layouts' == $_GET['page'] && ! empty( $_REQUEST['_wp_http_referer'] ) ) {
			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}
	}

	/**
	 * Manage post row actions at post listing page
	 * 
	 * @since 1.0
	 */
	function psac_manage_post_row_data( $actions, $post ) {

		if( $post->post_type == PSAC_POST_TYPE ) {
			return array_merge( array( 'post_id' => esc_html__('ID:', 'post-slider-and-carousel') .' '. $post->ID ), $actions );
		}
		return $actions;
	}
}

$psac_admin = new PSAC_Admin();