<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xevso
 */

?>
<div id="post-<?php the_ID(); ?>" <?php post_class('post-single'); ?>>
	<?php if(xevso_post_thumbnail()) : ?>
		<div class="img-post">
		<?php xevso_post_thumbnail(); ?>
		</div>
	<?php endif; ?>
	<?php get_template_part('template-parts/content','summery'); ?>
</div><!-- #post-<?php the_ID(); ?> -->
