<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package xevso 
 */

if ( ! is_active_sidebar( 'sidebar' ) ) {
	return;
}
?>

<div class="col-lg-4 col-md-12 col-sm-12 col-12">
	<div class="elementor-widget-sidebar">
		<?php dynamic_sidebar( 'sidebar' ); ?>
	</div>
</div>