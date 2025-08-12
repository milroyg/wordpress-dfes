<footer id="colophon" class="site-footer footer-top">
    <div class="container">
        <div class="row xevso-ftw-box">
            <?php if ( is_active_sidebar( 'footer-1-widget-area' ) ) : ?>
                <div class="widget-area col-xs-12 col-sm-12 col-md-6 col-lg-3">
                    <?php dynamic_sidebar( 'footer-1-widget-area' ); ?>
                </div><!-- .widget-area -->
            <?php endif; ?>
            <?php if ( is_active_sidebar( 'footer-2-widget-area' ) ) : ?>
                <div class="widget-area col-xs-12 col-sm-12 col-md-6 col-lg-3">
                    <?php dynamic_sidebar( 'footer-2-widget-area' ); ?>
                </div><!-- .widget-area -->
            <?php endif; ?>
            <?php if ( is_active_sidebar( 'footer-3-widget-area' ) ) : ?>
                <div class="widget-area col-xs-12 col-sm-12 col-md-6 col-lg-3">
                    <?php dynamic_sidebar( 'footer-3-widget-area' ); ?>
                </div><!-- .widget-area -->
            <?php endif; ?>
            <?php if ( is_active_sidebar( 'footer-4-widget-area' ) ) : ?>
                <div class="widget-area col-xs-12 col-sm-12 col-md-6 col-lg-3">
                    <?php dynamic_sidebar( 'footer-4-widget-area' ); ?>
                </div><!-- .widget-area -->
            <?php endif; ?>
        </div>
    </div>
    <div class="ft-copyright">
        <?php get_template_part('inc/footer/footer','bottom'); ?>
    </div>
</footer>