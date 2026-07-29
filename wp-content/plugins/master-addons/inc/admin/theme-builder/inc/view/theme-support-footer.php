<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php do_action('masteraddons/template/before_footer'); ?>
	<div class="jltma-template-content-markup jltma-template-content-footer jltma-template-content-theme-support">
		<?php
			$template = \MasterAddons\Inc\Admin\Theme_Builder\Activator::template_ids();
			if ($template[1]) {
				echo \MasterAddons\Inc\Admin\Theme_Builder\Theme_Builder::render_elementor_content($template[1]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_elementor_content returns Elementor's get_builder_content_for_display output which is sanitized HTML
			}
		?>
	</div>
	<?php do_action('masteraddons/template/after_footer'); ?>
	<?php wp_footer(); ?>

	</body>
</html>
