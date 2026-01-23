<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php echo wp_get_document_title(); ?>
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
	$template_ids = \MasterHeaderFooter\JLTMA_HF_Activator::template_ids();
	$header_template_id = $template_ids[0] ?? null;
	
	// Display header if template exists
	if ($header_template_id) {
		echo \MasterHeaderFooter\Master_Header_Footer::render_elementor_content($header_template_id);
	}
?>
</div>
<?php do_action('masteraddons/template/after_header'); ?>
