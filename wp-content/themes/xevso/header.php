<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package xevso
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php get_template_part('inc/preloader') ?>
<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'xevso' ); ?></a>

<?php
	if( class_exists( 'CSF' ) ) {
		get_template_part('inc/header/header');
	}else{
		get_template_part('inc/header/header-default');
	}
 ?>
<div id="content" class="site-content">
