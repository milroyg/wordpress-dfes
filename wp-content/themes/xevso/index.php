<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xevso
 */

get_header();
$xevso_blogc_title      = xevso_options( 'xevso_blog_title' );
$xevso_banner_enable    = xevso_options( 'xevso_blog_banner_enable', true );
$xevso_blog_layout      = xevso_options( 'xevso_blog_layout', 'right-sidebar' );
?>
    <?php if ( $xevso_banner_enable == true ) : ?>
      <div class="breadcroumb-boxs">
            <div class="container">
                  <div class="breadcroumb-box">
                        <div class="brea-title">
                              <h2><?php echo esc_html( $xevso_blogc_title ); ?></h2>
                        </div>
                  </div>
            </div>
      </div>
      <?php endif;?>
      <main id="primary" class="site-main layout-<?php echo esc_attr( $xevso_blog_layout ); ?>">
        <section class="blog-page default-page-section">
            <div class="container">
            <?php
            if ( $xevso_blog_layout == 'grid' ) {
                get_template_part( 'template-parts/blog/post-grid' );
            } else {
                get_template_part( 'template-parts/blog/post-sidebar' );
            }?>
            </div>
        </section>
	</main>
<?php get_footer(); ?>
