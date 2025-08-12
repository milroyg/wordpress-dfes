<?php 
$xevso_copyright_text = xevso_options('xevso_copyright_text');
?>
<div class="container">
    <div class="footer-copyright-area">
        <div class="row">
            <div class="col-md-6 d-flex flex-wrap align-content-center">
                <div class="copyright">
                <?php echo wp_kses_post(wpautop($xevso_copyright_text));?>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <div class="footer-menu">
                    <?php
                        if ( has_nav_menu( 'footer-menu' ) ) {
                            wp_nav_menu( array(
                                'container_id'=>'footermenu',
                                'menu_class'=>'navbar-nav mr-auto ml-auto sm sm-simple',
                                'menu_id'=>'footer-menu',
                                'theme_location'=>'footer-menu',
                                'echo'            => true,
                                'fallback_cb'     => true,
                            ));
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>