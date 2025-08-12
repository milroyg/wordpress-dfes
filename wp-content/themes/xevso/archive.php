<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xevso 
 */

get_header();
$xevso_archiveLayout = xevso_options('xevso_archive_layout', 'grid');
$xevso_archive_banner = xevso_options('xevso_archive_banner', true);
$xevso_archive_pagination = xevso_options('xevso_archive_pagination', true);
?>
<?php if($xevso_archive_banner == true ) : ?>
	<div class="breadcroumb-boxs">
		<div class="container">
			<div class="breadcroumb-box">
				<div class="brea-title">
				<?php the_archive_title( '<h2 class="archive-title">', '</h2>' ); ?>
				</div>
				<?php if(function_exists('bcn_display')) : ?>
					<div class="breadcrumb-bcn">
						<?php bcn_display();?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
<div class="default-page-section layout-<?php echo esc_attr($xevso_archiveLayout);?>">
	<div class="container">
		<?php
			if($xevso_archiveLayout == 'grid'){
				get_template_part( 'template-parts/blog/post-grid');
			}else{
				get_template_part( 'template-parts/blog/post-sidebar');
			}
		?>
	</div>
</div>
<?php get_footer(); ?>
