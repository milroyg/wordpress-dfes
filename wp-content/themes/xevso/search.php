<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package xevso
 */

get_header();
$xevso_searchLayout = xevso_options('xevso_search_layout', 'grid');
$xevso_search_banner = xevso_options('xevso_search_banner', true);
$xevso_search_pagination = xevso_options('xevso_search_pagination', true);
?>
<?php if($xevso_search_banner == true ) : ?>
	<div class="breadcroumb-boxs">
		<div class="container">
			<div class="breadcroumb-box">
				<div class="brea-title">
					<h2>
						<?php 
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'xevso' ), '<span>' . get_search_query() . '</span>' );
						?>
					</h2>
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
<div class="default-page-section layout-<?php echo esc_attr($xevso_searchLayout);?>">
	<div class="container">
		<?php
			if($xevso_searchLayout == 'grid'){
				get_template_part( 'template-parts/blog/post-grid');
			}else{
				get_template_part( 'template-parts/blog/post-sidebar');
			}
		?>
	</div>
</div>
<?php get_footer(); ?>
