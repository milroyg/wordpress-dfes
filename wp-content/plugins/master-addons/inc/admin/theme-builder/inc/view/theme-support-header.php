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
<?php do_action('masteraddons/template/before_header'); ?>
<div class="jltma-template-content-markup jltma-template-content-header jltma-template-content-theme-support">
<?php
	// Get the header template ID from the theme builder system
	$template_ids = \MasterAddons\Inc\Admin\Theme_Builder\Activator::template_ids();
	$header_template_id = $template_ids[0] ?? null;
	
	// Display header if template exists
	if ($header_template_id) {
		echo \MasterAddons\Inc\Admin\Theme_Builder\Theme_Builder::render_elementor_content($header_template_id); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_elementor_content() returns sanitized Elementor/wp_kses_post markup
	}
?>
</div>
<?php do_action('masteraddons/template/after_header'); ?>
