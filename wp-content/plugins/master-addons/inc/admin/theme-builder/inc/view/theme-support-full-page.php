<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php echo esc_html( wp_get_document_title() ); ?>
		</title>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php do_action('masteraddons/template/before_full_page'); ?>

<div class="jltma-template-content-markup jltma-template-content-full-page jltma-template-content-theme-support">
	<?php
		// Get the full page template ID (templates_template - index 3)
		$template_ids = \MasterAddons\Inc\Admin\Theme_Builder\Activator::template_ids();
		$full_page_template_id = $template_ids[3] ?? null;
		
		if ( $full_page_template_id ) {
			echo \MasterAddons\Inc\Admin\Theme_Builder\Theme_Builder::render_elementor_content( $full_page_template_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_elementor_content returns Elementor's get_builder_content_for_display output which is sanitized HTML
		}
	?>
</div>

<?php do_action('masteraddons/template/after_full_page'); ?>

<?php wp_footer(); ?>

</body>
</html>