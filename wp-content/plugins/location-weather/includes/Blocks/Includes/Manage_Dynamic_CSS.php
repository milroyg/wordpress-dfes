<?php
/**
 * SPL Weather Dynamic CSS Handler.
 *
 * Handles saving, enqueueing, and cleaning up dynamic CSS for all blocks.
 *
 * @package    Location_Weather.
 */

namespace ShapedPlugin\Weather\Blocks\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Manage_Dynamic_CSS
 *
 * Handles saving, enqueueing, and cleaning up dynamic CSS for SPL Weather blocks.
 *
 * - Registers REST API endpoints for saving block CSS.
 * - Enqueues frontend dynamic CSS and Google Fonts.
 * - Cleans up CSS files and transients when posts are deleted or no blocks exist.
 */
class Manage_Dynamic_CSS {

	/**
	 * Initialize hooks.
	 */
	public function __construct() {
		// REST API.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		if ( ! is_admin() ) {
			// Enqueue CSS on frontend only.
			add_action( 'wp_enqueue_scripts', array( $this, 'handle_frontend_dynamic_css' ) );
		}

		// Cleanup on post deletion.
		add_action( 'deleted_post', array( $this, 'delete_generated_css_file' ) );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		register_rest_route(
			'spl-weather/v2',
			'/weather-save-block-css',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_block_content_css' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Save block CSS via REST API for SPL Weather blocks.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response
	 */
	public function save_block_content_css( $request ) {
		$params = $request->get_params();

		$post_id            = isset( $params['post_id'] ) ? sanitize_text_field( $params['post_id'] ) : '';
		$slug               = isset( $params['slug'] ) ? sanitize_text_field( $params['slug'] ) : '';
		$widget_id          = isset( $params['widget_id'] ) ? sanitize_text_field( $params['widget_id'] ) : '';
		$theme              = isset( $params['theme'] ) ? sanitize_text_field( $params['theme'] ) : '';
		$block_css          = isset( $params['block_css'] ) ? sanitize_textarea_field( $params['block_css'] ) : '';
		$google_fonts       = isset( $params['fonts'] ) && ! empty( $params['fonts'] ) ? array_map( 'sanitize_text_field', $params['fonts'] ) : array();
		$is_preview         = isset( $params['preview'] ) ? rest_sanitize_boolean( $params['preview'] ) : false;
		$has_block          = isset( $params['has_block'] ) ? rest_sanitize_boolean( $params['has_block'] ) : false;
		$block_ref_id       = isset( $params['ref_id'] ) ? sanitize_text_field( $params['ref_id'] ) : '';
		$block_type         = isset( $params['block_type'] ) ? sanitize_text_field( $params['block_type'] ) : '';
		$reusable_block_ids = isset( $params['reusable_block_ids'] ) && is_array( $params['reusable_block_ids'] ) ? $params['reusable_block_ids'] : array();
		/**
		 * Handle widget-based CSS.
		 */
		if ( ! empty( $widget_id ) ) {
			$storage_key = '_spl_weather_widget_' . $widget_id;
			$font_key    = '_spl_weather_fonts_widget_' . $widget_id;

			if ( $has_block ) {
				update_option( $storage_key, $block_css );
				update_option( $font_key, $google_fonts );
			} else {
				delete_option( $storage_key );
				delete_option( $font_key );
			}

			return new \WP_REST_Response(
				array(
					'success' => true,
					'message' => __( 'Widget CSS saved successfully.', 'location-weather' ),
				)
			);
		}

		/**
		 * Handle template & template-part CSS.
		 */
		if ( ( 'wp_template' === $post_id || 'wp_template_part' === $post_id ) && 'wp_block' !== $block_type ) {
			$storage_key = '_spl_weather_template_' . $theme . $slug;
			$font_key    = '_spl_weather_fonts_template_' . $theme . $slug;

			if ( $has_block ) {
				update_option( $storage_key, $block_css );
				update_option( $font_key, $google_fonts );
			} else {
				delete_option( $storage_key );
				delete_option( $font_key );
			}

			return new \WP_REST_Response(
				array(
					'success' => true,
					'message' => __( 'Template CSS saved successfully.', 'location-weather' ),
				)
			);
		}

		// Store CSS & fonts in file and post-meta.
		if ( ! empty( $block_css ) ) {
			// Preview mode (transient).
			if ( $is_preview ) {
				set_transient( '_spl_weather_preview_' . $post_id, $block_css, HOUR_IN_SECONDS );
				set_transient( '_spl_weather_preview_fonts_' . $post_id, $google_fonts, HOUR_IN_SECONDS );
				return new \WP_REST_Response(
					array(
						'success' => true,
						'message' => __( 'Preview CSS saved temporarily.', 'location-weather' ),
					)
				);
			}

			// Reusable block handling.
			if ( 'wp_block' === $block_type ) {
				$post_id = $block_ref_id;
			}

			// Validate post ID.
			if ( ! is_numeric( $post_id ) ) {
				return new \WP_REST_Response(
					array(
						'success' => false,
						'message' => __( 'Invalid post ID.', 'location-weather' ),
					),
					400
				);
			}

			// Ensure the current user is allowed to edit this specific post before storing CSS against it.
			if ( ! current_user_can( 'edit_post', (int) $post_id ) ) {
				return new \WP_REST_Response(
					array(
						'success' => false,
						'message' => __( 'You are not allowed to save CSS for this post.', 'location-weather' ),
					),
					403
				);
			}

			// Save CSS to file using WP_Filesystem.
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				WP_Filesystem();
			}

			$upload_dir = wp_upload_dir();
			$css_dir    = trailingslashit( $upload_dir['basedir'] ) . 'spl-weather-css/';

			// Ensure directory exists.
			if ( ! $wp_filesystem->is_dir( $css_dir ) ) {
				$wp_filesystem->mkdir( $css_dir );
			}

			$filename = "spl-weather-{$post_id}.css";
			$filepath = $css_dir . $filename;

			// Try saving CSS.
			if ( $wp_filesystem->put_contents( $filepath, $block_css ) ) {
				update_post_meta( $post_id, '_spl_weather_css', $block_css );
				update_post_meta( $post_id, '_spl_weather_fonts', $google_fonts );

				return new \WP_REST_Response(
					array(
						'success' => true,
						'message' => __( 'CSS file saved successfully.', 'location-weather' ),
					)
				);
			} else {
				return new \WP_REST_Response(
					array(
						'success' => false,
						'message' => __( 'CSS could not be saved due to file permission issues.', 'location-weather' ),
					),
					500
				);
			}
		} else {

			/**
			 * Delete CSS & fonts when no blocks exist.
			 */
			if ( 'wp_block' === $block_type ) {
				$post_id = $block_ref_id;
			}

			// Delete saved transients for preview page.
			if ( $is_preview ) {
				delete_transient( '_spl_weather_preview_' . $post_id );
				delete_transient( '_spl_weather_preview_fonts_' . $post_id, $google_fonts, HOUR_IN_SECONDS );
				return new \WP_REST_Response(
					array(
						'success' => true,
						'message' => __( 'Temporary preview CSS deleted .', 'location-weather' ),
					)
				);
			}

			// Delete stored css and fonts in post meta.
			delete_post_meta( $post_id, '_spl_weather_css' );
			delete_post_meta( $post_id, '_spl_weather_fonts' );

			// Delete generated css file of this post.
			$this->delete_generated_css_file( $post_id );

			return new \WP_REST_Response(
				array(
					'success' => true,
					'message' => __( 'CSS file deleted successfully.', 'location-weather' ),
				)
			);

		}
	}

	/**
	 * Get All Reusable IDs content.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	protected function get_reusable_ids( $post_id ) {
		$reusable_id = array();
		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( isset( $post->post_content ) ) {
				if ( has_blocks( $post->post_content ) &&
					strpos( $post->post_content, 'wp:block' ) &&
					strpos( $post->post_content, '"ref"' ) !== false
				) {
					$blocks = parse_blocks( $post->post_content );
					foreach ( $blocks as $key => $value ) {
						if ( isset( $value['attrs']['ref'] ) ) {
							$reusable_id[] = $value['attrs']['ref'];
						}
					}
				}
			}
		}
		return $reusable_id;
	}

	/**
	 * Enqueue frontend dynamic CSS + fonts.
	 */
	public function handle_frontend_dynamic_css() {
		global $post, $wp_registered_sidebars;

		$current_post_id = ! empty( $post->ID ) ? $post->ID : 0;
		$css             = '';
		$fonts           = array();

		/**
		 * Helper: Merge CSS and fonts into accumulators.
		 */
		$merge_assets = function ( $css_source, $fonts_source ) use ( &$css, &$fonts ) {
			if ( ! empty( $css_source ) ) {
				$css .= $css_source;
			}
			if ( ! empty( $fonts_source ) && is_array( $fonts_source ) ) {
				$fonts = array_merge( $fonts, $fonts_source );
			}
		};

		/**
		 * Preview mode CSS & Fonts.
		 */
		if ( is_preview() && $current_post_id ) {
			$preview_css   = get_transient( '_spl_weather_preview_' . $current_post_id );
			$preview_fonts = get_transient( '_spl_weather_preview_fonts_' . $current_post_id );

			if ( $preview_css || $preview_fonts ) {
				$merge_assets( $preview_css, $preview_fonts );
			}
		}

		/**
		 * Widgets CSS & Fonts.
		 */
		if ( ! empty( $wp_registered_sidebars ) ) {
			$sidebars_widgets = get_option( 'sidebars_widgets', array() );
			if ( ! empty( $sidebars_widgets ) && is_array( $sidebars_widgets ) ) {
				foreach ( $sidebars_widgets as $sidebar => $widgets ) {
					if ( 'wp_inactive_widgets' === $sidebar || empty( $widgets ) || ! is_array( $widgets ) ) {
						continue;
					}
					foreach ( $widgets as $widget_id ) {
						$merge_assets(
							get_option( '_spl_weather_widget_' . $widget_id, '' ),
							get_option( '_spl_weather_fonts_widget_' . $widget_id, array() )
						);
					}
				}
			}
		}

		/**
		 * 2. Theme templates (header, footer, home, archive, single, page).
		 */
		$theme_slug        = wp_get_theme()->get_stylesheet();
		$template_contexts = array( 'header', 'footer', 'sidebar' );

		if ( is_home() ) {
			$template_contexts[] = 'home';
		} elseif ( is_archive() ) {
			$template_contexts[] = 'archive';
		} elseif ( is_single() ) {
			$template_contexts[] = 'single';
		} elseif ( is_page() ) {
			$template_contexts[] = 'page';
		}

		foreach ( $template_contexts as $context ) {
			// Template CSS + Fonts.
			$merge_assets(
				get_option( "_spl_weather_template_{$theme_slug}{$context}", '' ),
				get_option( "_spl_weather_fonts_template_{$theme_slug}{$context}", array() )
			);

			// Reusable block CSS + Fonts inside templates.
			$reused_ids = (array) get_option( "_spl_weather_template_reused_blocks_{$theme_slug}{$context}", array() );
			foreach ( $reused_ids as $id ) {
				$merge_assets(
					get_post_meta( $id, '_spl_weather_css', true ),
					get_post_meta( $id, '_spl_weather_fonts', true )
				);
			}
		}

		/**
		 * Singular post/page CSS & Fonts.
		 *    (skipped if preview)
		 */
		if ( is_singular() && $current_post_id ) {
			$upload_dir    = wp_upload_dir();
			$css_file_path = trailingslashit( $upload_dir['basedir'] ) . "spl-weather-css/spl-weather-{$current_post_id}.css";

			if ( file_exists( $css_file_path ) ) {
				$css_url = trailingslashit( $upload_dir['baseurl'] ) . "spl-weather-css/spl-weather-{$current_post_id}.css";
				wp_enqueue_style(
					"spl-weather-dynamic-{$current_post_id}",
					$css_url,
					array(),
					filemtime( $css_file_path )
				);
				$merge_assets( '', get_post_meta( $current_post_id, '_spl_weather_fonts', array() ) );
			} else {
				$merge_assets(
					get_post_meta( $current_post_id, '_spl_weather_css', true ),
					get_post_meta( $current_post_id, '_spl_weather_fonts', array() )
				);
			}

			// Reusable blocks inside post/page.
			if ( method_exists( $this, 'get_reusable_ids' ) ) {
				foreach ( $this->get_reusable_ids( $current_post_id ) as $ref_id ) {
					$merge_assets(
						get_post_meta( $ref_id, '_spl_weather_css', true ),
						get_post_meta( $ref_id, '_spl_weather_fonts', true )
					);
				}
			}
		}

		/**
		 * 4. Add inline CSS to the frontend.
		 */
		if ( ! empty( $css ) ) {
			wp_add_inline_style( 'splw_index_style', wp_strip_all_tags( $css ) );
		}

		/**
		 * 5. Enqueue Google Fonts.
		 */
		if ( ! empty( $fonts ) ) {
			// Flatten nested arrays and cast everything to string.
			$fonts = array_map( 'strval', array_merge( ...array_map( ( fn( $f ) => (array) $f ), $fonts ) ) );

			foreach ( array_unique( $fonts ) as $font ) {
				if ( '' === $font ) {
					continue;
				}

				[ $font_name, $font_weights ] = array_pad( explode( ':', $font ), 2, '400' );
				$font_name                    = str_replace( ' ', '+', $font_name );

				wp_enqueue_style(
					'spl-weather-font-' . sanitize_title( $font_name ),
					'//fonts.googleapis.com/css?family=' . $font_name . ':' . $font_weights,
					array(),
					LOCATION_WEATHER_VERSION
				);
			}
		}
	}


	/**
	 * Remove dynamic css file.
	 *
	 * @param int $post_id The ID of the deleted post.
	 */
	public function delete_generated_css_file( $post_id ) {
		// Delete dynamic CSS file.
		$upload_dir    = wp_upload_dir();
		$css_dir       = trailingslashit( $upload_dir['basedir'] ) . 'spl-weather-css';
		$css_file_path = trailingslashit( $css_dir ) . "spl-weather-{$post_id}.css";

		if ( file_exists( $css_file_path ) ) {
			wp_delete_file( $css_file_path );
		}

		// Check if the folder exists and is empty, then delete it.
		if ( is_dir( $css_dir ) ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			WP_Filesystem();
			global $wp_filesystem;

			$files = $wp_filesystem->dirlist( $css_dir );

			// If no files remain, delete the directory.
			if ( empty( $files ) ) {
				$wp_filesystem->rmdir( $css_dir );
			}
		}
	}
}
