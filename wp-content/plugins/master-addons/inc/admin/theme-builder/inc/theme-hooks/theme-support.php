<?php
namespace MasterAddons\Inc\Admin\Theme_Builder\Theme_Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * Header & Footer will replace force fully
 */

class Theme_Support {

	function __construct($template_ids) {

		if($template_ids[0] != null){
			add_action( 'get_header', [ $this, 'jltma_get_header' ] );
		}
		

		if($template_ids[1] != null){
			add_action( 'get_footer', [ $this, 'jltma_get_footer' ] );
		}

		if($template_ids[2] != null){
			add_filter( 'comments_template', [ $this, 'jltma_get_comment_form' ] );
		}

		// Handle full page templates (search, 404, single, archive, product, product_archive)
		if($template_ids[3] != null){
			add_filter( 'template_include', [ $this, 'jltma_get_full_page_template' ], 999 );
		}

		// Handle popup templates if needed (index 4)
		if($template_ids[4] != null){
			add_action( 'wp_footer', [ $this, 'jltma_get_popup_template' ] );
		}

	}


	public function jltma_get_comment_form( $comment_template ){

        ob_start();
		return \JLTMA_PATH . 'inc/admin/theme-builder/inc/view/theme-support-comment.php';
		ob_get_clean();
	}


	public function jltma_get_header( $name ) {
		require_once \JLTMA_PATH . 'inc/admin/theme-builder/inc/view/theme-support-header.php';


		$templates = [];
		$name = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "header-{$name}.php";
		}

		$templates[] = 'header.php';

		// Avoid running wp_head hooks again
		remove_all_actions( 'wp_head' );
		ob_start();
		// It cause a `require_once` so, in the get_header it self it will not be required again.
		locate_template( $templates, true );
		ob_get_clean();
	}


	public function jltma_get_footer( $name ) {
		require_once \JLTMA_PATH . 'inc/admin/theme-builder/inc/view/theme-support-footer.php';

		$templates = [];
		$name = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "footer-{$name}.php";
		}

		$templates[] = 'footer.php';

		// Avoid running wp_footer hooks again
		ob_start();
		// It cause a `require_once` so, in the get_footer it self it will not be required again.
		locate_template( $templates, true );
		ob_get_clean();
	}

	/**
	 * Handle full page templates (search, 404, single, archive, product, product_archive)
	 * This replaces the entire page template with our MA template
	 */
	public function jltma_get_full_page_template( $template ) {
		// Only run on frontend, skip Elementor preview/editor
		if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only Elementor preview context detection
			return $template;
		}

		// Skip if Elementor is in preview or edit mode
		if ( class_exists( '\Elementor\Plugin' ) ) {
			if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ||
				 \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				return $template;
			}
		}

		// Get the templates_template ID (index 3)
		$template_ids = \MasterAddons\Inc\Admin\Theme_Builder\Activator::template_ids();
		$full_page_template_id = $template_ids[3] ?? null;

		if ( ! $full_page_template_id ) {
			return $template;
		}

		// Verify the template's conditions actually match the current page context
		$template_type = get_post_meta( $full_page_template_id, 'master_template_type', true );
		$conditions_data = get_post_meta( $full_page_template_id, 'master_template_conditions_data', true );

		if ( ! empty( $conditions_data ) && is_array( $conditions_data ) ) {
			$activator = \MasterAddons\Inc\Admin\Theme_Builder\Activator::instance();
			$location = $activator->get_current_location();

			// Only apply if the template type matches the current location
			// (e.g., a '404' template should only apply on actual 404 pages)
			if ( $template_type !== 'templates' && $template_type !== $location ) {
				return $template;
			}
		}

		// Return our custom template file that will render the full MA template
		$ma_template_file = \JLTMA_PATH . 'inc/admin/theme-builder/inc/view/theme-support-full-page.php';

		if ( file_exists( $ma_template_file ) ) {
			return $ma_template_file;
		}

		return $template;
	}

	/**
	 * Handle popup templates (output in footer)
	 */
	public function jltma_get_popup_template() {
		// Get the popup template ID (index 4)
		$template_ids = \MasterAddons\Inc\Admin\Theme_Builder\Activator::template_ids();
		$popup_template_id = $template_ids[4] ?? null;

		if ( $popup_template_id ) {
			echo '<div class="jltma-template-content-markup jltma-template-content-popup jltma-template-content-theme-support">';
			echo \MasterAddons\Inc\Admin\Theme_Builder\Theme_Builder::render_elementor_content( $popup_template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_elementor_content returns safe Elementor-rendered HTML
			echo '</div>';
		}
	}


}
