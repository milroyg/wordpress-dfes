<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xevso 
 */

get_header();
if(get_post_meta( get_the_ID(), 'xevso_metabox', true)) {
    $xevso_commonMeta = get_post_meta( get_the_ID(), 'xevso_metabox', true );
} else {
    $xevso_commonMeta = array();
}


if(array_key_exists('xevso_meta_page_navbar', $xevso_commonMeta)){
	$xevso_meta_page_navbar = $xevso_commonMeta['xevso_meta_page_navbar'];
}else{
	$xevso_meta_page_navbar = '';
}
if(array_key_exists('xevso_layout_meta', $xevso_commonMeta)){
    $xevso_pageLayout = $xevso_commonMeta['xevso_layout_meta'];
}else{
    $xevso_pageLayout = 'full-width';
}

if(array_key_exists('xevso_sidebar_meta', $xevso_commonMeta)){
    $xevso_selectedSidebar = $xevso_commonMeta['xevso_sidebar_meta'];
}else{
    $xevso_selectedSidebar = 'sidebar';
}

if($xevso_pageLayout == 'left-sidebar' && is_active_sidebar($xevso_selectedSidebar) || $xevso_pageLayout == 'right-sidebar' && is_active_sidebar($xevso_selectedSidebar)){
    $xevso_pageColumnClass = 'col-lg-8';
}else{
    $xevso_pageColumnClass = 'col-lg-12';
}


if(array_key_exists('xevso_meta_enable_banner', $xevso_commonMeta)){
    $xevso_postBanner = $xevso_commonMeta['xevso_meta_enable_banner'];
}else{
    $xevso_postBanner = true;
}

if(array_key_exists('xevso_meta_custom_title', $xevso_commonMeta)){
    $xevso_customTitle = $xevso_commonMeta['xevso_meta_custom_title'];
}else{
    $xevso_customTitle = '';
}
?>
<?php if($xevso_postBanner == true ) : ?>
	<div class="breadcroumb-boxs">
		<div class="container">
			<div class="breadcroumb-box">
				<div class="brea-title">
					<h2>
						<?php 
						if(!empty($xevso_customTitle)){
							echo esc_html($xevso_customTitle);
						}else{
							the_title();
						}
						?>
					</h2>
				</div>
				
				 <?php
				/*
				 * if(function_exists('bcn_display')) : ?> 
					<div class="breadcrumb-bcn">
						<?php bcn_display();?>
					</div>
				<?php endif; 
				*/
				?> 
			</div>
		</div>
	</div>
<?php endif; ?>
<div class="default-page-section">
	<div class="container">
		<div class="row">
			<?php
			if($xevso_pageLayout == 'left-sidebar' && is_active_sidebar($xevso_selectedSidebar)){
				get_sidebar();
			}
			?>
			<div class="<?php echo esc_attr($xevso_pageColumnClass);?>">
				<div class="theme-content layout-<?php echo esc_attr($xevso_pageLayout);?>">
				<?php
					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', 'page' );?>
						
						<?php if( get_the_post_navigation() && !empty($xevso_meta_page_navbar) ) : ?>
						<div class="theme-post-navication">
							<div class="nave-arcive">
								 <a href="<?php echo esc_url(home_url('/')); ?>"><i class="fa fa-th-large"></i></a>
							 </div>
							<?php
							// Previous/next post navigation.
							the_post_navigation(array(
								'next_text' => '<span class="meta-nav">' . esc_html__( 'Next Post', 'xevso' ) . '</span><h3 class="title">%title</h3>',
								'prev_text' => '<span class="meta-nav">' . esc_html__( 'Prev Post', 'xevso' ) . '</span><h3 class="title">%title</h3>',
							));
					        ?>
						</div>
						<?php endif; 

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>
				</div>
			</div>
			<?php
			if($xevso_pageLayout == 'right-sidebar' && is_active_sidebar($xevso_selectedSidebar)){
				get_sidebar();
			}?>
		</div>
	</div>
</div>
<?php get_footer(); ?>
