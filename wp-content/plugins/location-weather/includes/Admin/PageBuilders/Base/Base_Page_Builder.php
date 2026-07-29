<?php
/**
 * Base Page Builder Trait
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base trait for page builder integrations.
 *
 * Provides common functionality for rendering saved templates
 * across different page builders.
 *
 * @since 3.2.0
 */
trait Base_Page_Builder {

	/**
	 * Get saved templates list.
	 *
	 * @since 3.2.0
	 *
	 * @return array Template ID => Title pairs
	 */
	protected function get_saved_templates_list() {
		$templates = array(
			'0' => esc_html__( '- Select Template -', 'location-weather' ),
		);

		$query = new \WP_Query(
			array(
				'post_type'      => 'spl_weather_template',
				'post_status'    => 'publish',
				'posts_per_page' => 10000,
			)
		);

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$templates[ $post->ID ] = ! empty( $post->post_title )
					? $post->post_title
					: '#' . $post->ID;
			}
		}
		krsort( $templates );
		return $templates;
	}

	/**
	 * Render saved template content.
	 *
	 * @since 3.2.0
	 *
	 * @param int  $template_id Template post ID.
	 * @param bool $is_editor   Whether rendering in page builder editor.
	 * @return string Rendered content or error message.
	 */
	protected function render_template( $template_id, $is_editor = false ) {
		if ( empty( $template_id ) || 0 === $template_id ) {
			return $this->error_message( esc_html__( 'Please select a saved template', 'location-weather' ) );
		}

		$template_post = get_post( $template_id );
		if ( ! $template_post || 'publish' !== $template_post->post_status ) {
			return $this->error_message( esc_html__( 'Template not found or not published', 'location-weather' ) );
		}

		if ( $is_editor ) {
			$this->enqueue_editor_css( $template_id );
		}

		return do_shortcode( '[location_weather id="' . absint( $template_id ) . '"]' );
	}

	/**
	 * Enqueue CSS for page builder editor.
	 *
	 * @since 3.2.0
	 *
	 * @param int $template_id Template post ID.
	 * @return void
	 */
	protected function enqueue_editor_css( $template_id ) {
		static $enqueued = array();
		if ( isset( $enqueued[ $template_id ] ) ) {
			return;
		}

		$upload_dir = wp_upload_dir();
		$css_file   = trailingslashit( $upload_dir['basedir'] ) . 'spl-weather-css/spl-weather-' . $template_id . '.css';
		$css_url    = trailingslashit( $upload_dir['baseurl'] ) . 'spl-weather-css/spl-weather-' . $template_id . '.css';

		if ( file_exists( $css_file ) ) {
			// Echo CSS link for page builder editor.
			echo '<link rel="stylesheet" href="' . esc_url( $css_url . '?v=' . LOCATION_WEATHER_VERSION ) . '">'; // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			// Fallback: CSS from post meta.
			$css = get_post_meta( $template_id, '_spl_weather_css', true );
			if ( ! empty( $css ) ) {
				echo '<style id="splw-builder-dynamic-css-' . esc_attr( $template_id ) . '">' . $css . '</style>'; // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		// Load Google Fonts.
		$fonts = get_post_meta( $template_id, '_spl_weather_fonts', true );
		if ( ! empty( $fonts ) && is_array( $fonts ) ) {
			$fonts = array_unique( $fonts );
			foreach ( $fonts as $font ) {
				if ( ! empty( $font ) ) {
					echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=' . esc_attr( $font ) . '">'; // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}

		$enqueued[ $template_id ] = true;
	}

	/**
	 * Generate error message HTML.
	 *
	 * @since 3.2.0
	 *
	 * @param string $message Error message.
	 * @return string Error HTML.
	 */
	protected function error_message( $message ) {
		return sprintf(
			'<div style="text-align:center;padding:20px;border:2px dashed #ccc;color:#999;font-size:14px;">%s</div>',
			esc_html( $message )
		);
	}
}
