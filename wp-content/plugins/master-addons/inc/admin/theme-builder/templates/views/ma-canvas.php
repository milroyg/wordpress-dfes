<?php
/**
 * Master Addons Canvas Template
 */

use MasterHeaderFooter\Master_Header_Footer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the template ID that was stored in wp_query
global $wp_query;
$template_id = $wp_query->get( 'master_template_id' );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title>
			<?php echo esc_html(wp_get_document_title()); ?>
		</title>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php
do_action( 'wp_body_open' );

// Render Master Addons template content
if ( $template_id && class_exists( 'MasterHeaderFooter\Master_Header_Footer' ) ) {
	echo Master_Header_Footer::render_elementor_content( $template_id );
} else {
	echo '<p>Master Addons template system not available.</p>';
}

wp_footer();
?>

</body>
</html>