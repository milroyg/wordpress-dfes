<?php
/**
 * Template part for displaying posts sidebar layout
 *
 * @package xevso
 */

if(is_archive()){
	$xevso_pageLayout = xevso_options('xevso_archive_layout', 'right-sidebar');
}else if(is_search()){
	$xevso_pageLayout = xevso_options('xevso_search_layout', 'right-sidebar');
}else{
	$xevso_pageLayout = xevso_options('xevso_blog_layout', 'right-sidebar');
}

if($xevso_pageLayout == 'left-sidebar' && is_active_sidebar('sidebar') || $xevso_pageLayout == 'grid-ls' && is_active_sidebar('sidebar') || $xevso_pageLayout == 'right-sidebar' && is_active_sidebar('sidebar') || $xevso_pageLayout == 'grid-rs' && is_active_sidebar('sidebar')){
	$pageColumnClass = 'col-lg-8';
}else{
	$pageColumnClass = 'col-lg-12';
}


?>
<div class="row blog-page-with-sidebar">
	<?php
	if($xevso_pageLayout == 'left-sidebar' && is_active_sidebar('sidebar') || $xevso_pageLayout == 'grid-ls' && is_active_sidebar('sidebar')){
		get_sidebar();
	}
	?>

	<div class="<?php echo esc_attr($pageColumnClass);?>">
        <div class="row all-posts-wrapper">
            <?php
            if ( have_posts() ) :

                if ( is_home() && ! is_front_page() ) :
                    ?>
                    <header>
                        <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                    </header>
                <?php
                endif;

                /* Start the Loop */
                while ( have_posts() ) :
                    the_post(); ?>
                        <?php get_template_part( 'template-parts/blog/sidebar-post-item', get_post_format() ); ?>

                <?php
                endwhile;

            else :

                get_template_part( 'template-parts/content', 'none' );

            endif;
            ?>
        </div>

        <?php 
        $xevso_pagination = xevso_options('xevso_show_pagination', true );
            if($xevso_pagination == true ){
                xevso_pagination();
            };
        ?>
	</div>

	<?php
	if($xevso_pageLayout == 'right-sidebar' && is_active_sidebar('sidebar') || $xevso_pageLayout == 'grid-rs' && is_active_sidebar('sidebar')){
		get_sidebar();
	}
	?>

</div>