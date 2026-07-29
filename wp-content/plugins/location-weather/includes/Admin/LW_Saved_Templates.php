<?php
/**
 * Saved Location Weather Templates.
 *
 * @link http://shapedplugin.com
 * @since 2.0.0
 *
 * @package Location_Weather_Pro.
 * @subpackage Location_Weather_Pro/includes.
 */

namespace ShapedPlugin\Weather\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LW_Saved_Templates' ) ) {
	/**
	 * Handle Saved Templates functionality.
	 */
	class LW_Saved_Templates {

		/**
		 * Constructor.
		 *
		 * Initialize hooks for saved templates functionality including CPT registration,
		 * admin redirects, block editor enforcement, and shortcode handling.
		 *
		 * @return void
		 */
		public function __construct() {
			// Register the CPT early enough for REST requests (Gutenberg saves via REST).
			add_action( 'init', array( $this, 'location_weather_saved_template_post_type' ) );
			add_action( 'admin_init', array( $this, 'redirect_saved_template_edit_page' ) );
			add_action( 'admin_init', array( $this, 'maybe_redirect_to_block_editor' ) );
			add_filter( 'use_block_editor_for_post_type', array( $this, 'splw_force_block_editor_for_save_templates' ), 100, 2 );
			add_shortcode( 'location_weather', array( $this, 'lw_saved_template_callback' ) );
		}

		/**
		 * Redirect "Add New Weather" to the block-editor Saved Template flow
		 * when the user previously picked Block Editor in the welcome modal
		 * and ticked "Remember this choice".
		 *
		 * @return void
		 */
		public function maybe_redirect_to_block_editor() {
			global $pagenow;

			if ( 'post-new.php' !== $pagenow ) {
				return;
			}

			$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
			if ( 'location_weather' !== $post_type ) {
				return;
			}

			if ( 'block_editor' !== get_option( 'splw_blocks_promo_modal_choice' ) ) {
				return;
			}

			wp_safe_redirect( admin_url( 'post-new.php?post_type=spl_weather_template&splwblock_inserter' ) );
			exit;
		}

		/**
		 * Location Weather Saved Template post type
		 */
		public function location_weather_saved_template_post_type() {
			if ( post_type_exists( 'spl_weather_template' ) ) {
				return;
			}
			$show_ui = current_user_can( 'manage_options' ) ? true : false;
			// Set the location weather saved template post type labels.
			$labels =
			array(
				'name'                     => __( 'Saved Templates', 'location-weather' ),
				'singular_name'            => __( 'Saved Template', 'location-weather' ),
				'menu_name'                => __( 'Saved Templates', 'location-weather' ),
				'all_items'                => __( 'Saved Templates', 'location-weather' ),
				'add_new'                  => __( 'Add New Template', 'location-weather' ),
				'add_new_item'             => __( 'Add New Template', 'location-weather' ),
				'edit'                     => __( 'Edit', 'location-weather' ),
				'edit_item'                => __( 'Edit Template', 'location-weather' ),
				'view_item'                => __( 'View Template', 'location-weather' ),
				'new_item'                 => __( 'New Templates', 'location-weather' ),
				'search_items'             => __( 'Search Template', 'location-weather' ),
				'not_found'                => __( 'No Template found', 'location-weather' ),
				'not_found_in_trash'       => __( 'No Template found in Trash', 'location-weather' ),
				'item_published'           => __( 'Template Published', 'location-weather' ),
				'item_published_privately' => __( 'Template published privately.', 'location-weather' ),
				'item_reverted_to_draft'   => __( 'Template reverted to draft.', 'location-weather' ),
				'item_scheduled'           => __( 'Template scheduled.', 'location-weather' ),
				'item_updated'             => __( 'Template updated.', 'location-weather' ),
			);
			// Set the Location Weather Saved Template post type (spl_weather_template) arguments.
			$args =
			array(
				'labels'              => $labels,
				'public'              => false,
				'supports'            => array( 'title', 'editor', 'revisions' ),
				'show_in_rest'        => true,
				'hierarchical'        => false,
				'rewrite'             => false,
				'show_ui'             => $show_ui,
				'show_in_menu'        => false,
				'show_in_nav_menu'    => true,
				'exclude_from_search' => true,
				'capability_type'     => 'page',
			);

			register_post_type( 'spl_weather_template', $args );
		}

		/**
		 * Redirect Saved Template edit page with proper sanitization.
		 *
		 * Sanitizes GET parameters and uses WordPress global $pagenow for robust
		 * admin page detection to prevent CSRF and URL manipulation attacks.
		 *
		 * @return void
		 */
		public function redirect_saved_template_edit_page() {
			global $pagenow;

			// If we are on post.php and editing a post.
			if ( isset( $_GET['post'], $_GET['action'] ) && 'edit' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {

				$post_id   = absint( $_GET['post'] );
				$post_type = get_post_type( $post_id );

				// Check custom post type.
				if ( 'spl_weather_template' === $post_type ) {
					return;
				}
			}

			// When creating a new post (post-new.php) - use $pagenow for robust check.
			if ( 'post-new.php' === $pagenow ) {
				return; // DO NOT redirect.
			}

			// Redirect default list table edit.php?post_type=spl_weather_template.
			if ( isset( $_GET['post_type'] ) && 'spl_weather_template' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) {

				$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

				if ( 'splw_admin_dashboard#saved_templates' !== $page ) {
					wp_safe_redirect( admin_url( 'admin.php?page=splw_admin_dashboard#saved_templates' ) );
					exit;
				}
			}
		}

		/**
		 * Force to save template via block editor.
		 */
		public function splw_force_block_editor_for_save_templates( $use_block_editor, $post_type ) {
			if ( 'spl_weather_template' === $post_type ) {
				return true;
			}
			return $use_block_editor;
		}

		/**
		 * Ensure CSS file exists for a saved template.
		 *
		 * If CSS file doesn't exist, generate it from post meta CSS.
		 * This handles cases where CSS file was not generated during template save.
		 *
		 * @since 3.2.0
		 *
		 * @param int     $template_id Template post ID.
		 * @param WP_Post $template_post Template post object.
		 * @return void
		 */
		private function ensure_template_css_file( $template_id, $template_post ) {
			$upload_dir    = wp_upload_dir();
			$css_file_path = trailingslashit( $upload_dir['basedir'] ) . "spl-weather-css/spl-weather-{$template_id}.css";

			// If file already exists, no action needed.
			if ( file_exists( $css_file_path ) ) {
				return;
			}

			// Get CSS from post meta.
			$css = get_post_meta( $template_id, '_spl_weather_css', true );

			// If no CSS in meta, nothing to do.
			if ( empty( $css ) ) {
				return;
			}

			// Create CSS directory if it doesn't exist.
			$css_dir = trailingslashit( $upload_dir['basedir'] ) . 'spl-weather-css';
			if ( ! is_dir( $css_dir ) ) {
				wp_mkdir_p( $css_dir );
			}

			// Write CSS to file.
			file_put_contents( $css_file_path, $css, LOCK_EX );
		}

		/**
		 * Saved Template (shortcode) callback.
		 *
		 * @attributes array $attribute The shortcode attributes.
		 * @return statement.
		 */
		public function lw_saved_template_callback( $attributes ) {

			$attributes = shortcode_atts(
				array(
					'id' => '',
				),
				$attributes
			);

			$id = $attributes['id'];
			$id = is_numeric( $id ) ? absint( $id ) : false;

			$content = '';
			if ( $id ) {

				// Do not render blocks in Elementor editor.
				// if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				// return '[location_weather id="' . $id . '"]';
				// }

				$content_post = get_post( $id );
				$post_status  = isset( $content_post ) ? $content_post->post_status : '';

				if ( 'publish' === $post_status ) {
					$content .= $content_post ? $content_post->post_content : null;
					$content  = do_blocks( $content );
					$content  = do_shortcode( $content );
					$content  = str_replace( ']]>', ']]&gt;', $content );
					$content  = preg_replace( '%<p>&nbsp;\s*</p>%', '', $content );
					$content  = preg_replace( '/^(?:<br\s*\/?>\s*)+/', '', $content );

					// Ensure CSS file exists for the template.
					$this->ensure_template_css_file( $id, $content_post );

					$upload_dir    = wp_upload_dir();
					$css_file_path = trailingslashit( $upload_dir['basedir'] ) . "spl-weather-css/spl-weather-{$id}.css";

					wp_enqueue_style( 'splw_index_style' );
					wp_enqueue_script( 'spl-weather-block-script' );

					// Add dynamic CSS.
					if ( file_exists( $css_file_path ) ) {
						$css_url = trailingslashit( $upload_dir['baseurl'] ) . "spl-weather-css/spl-weather-{$id}.css";
						wp_enqueue_style(
							"spl-weather-dynamic-{$id}",
							$css_url,
							array(),
							filemtime( $css_file_path )
						);
					} else {
						wp_add_inline_style( 'splw_index_style', wp_strip_all_tags( get_post_meta( $id, '_spl_weather_css', true ) ) );
					}
					return $content;
				}
			}
			return '';
		}
	}
}
