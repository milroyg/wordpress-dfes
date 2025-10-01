<?php

function xevso_assets() {
    $xevso_css_files = array(
        'bootstrap'            => xevso_ASSETS_DIR . 'bootstrap/bootstrap-min.css',
        'animate'              => xevso_ASSETS_DIR . 'animations/animate.css',
        'owl-carousel'         => xevso_ASSETS_DIR . 'owlcarousel/owl-carousel.css',
        'font-awesome'         => xevso_ASSETS_DIR . 'css/font-awesome-min.css',
        'flaticon'             => xevso_ASSETS_DIR . 'css/flaticon.css',
        'sm-core'              => xevso_ASSETS_DIR . 'menu/sm-core.css',
        'sm-simple'            => xevso_ASSETS_DIR . 'menu/sm-simple.css',
        'slick'                => xevso_ASSETS_DIR . 'slick/slick.css',
        'slick-theme'          => xevso_ASSETS_DIR . 'slick/slick-theme.css',
        'xevso-default'     => xevso_ASSETS_DIR . 'css/default.css',
        'xevso-typrography' => xevso_ASSETS_DIR . 'css/typrography.css',
        'magnific-popup'       => xevso_ASSETS_DIR . 'gallery/magnific-popup.css',
        'xevso-theme'       => xevso_ASSETS_DIR . 'css/theme.css',
        'xevso-responsive'  => xevso_ASSETS_DIR . 'css/responsive.css',
    );
    foreach ( $xevso_css_files as $xevso_handle => $xevso_css_file ) {
        wp_enqueue_style( $xevso_handle, $xevso_css_file, null, xevso_VERSION );
    }
    wp_enqueue_style( 'xevso-style', get_stylesheet_uri(), null, xevso_VERSION );
    wp_style_add_data( 'xevso-style', 'rtl', 'replace' );

    // Add WordPress Default Masonry, Used for attach grid.
    wp_enqueue_script( 'jquery-masonry' );
    $xevso_js_files = array(
        'popper-min'                   => array( 'src' => xevso_ASSETS_DIR . 'bootstrap/popper-min.js', 'dep' => array( 'jquery' ) ),
        'poshtrtyht'                   => array( 'src' => xevso_ASSETS_DIR . 'bootstrap/bootstrap.bundle.min.js', 'dep' => array( 'jquery' ) ),
        'bootstrap'                    => array( 'src' => xevso_ASSETS_DIR . 'bootstrap/bootstrap-min.js', 'dep' => array( 'jquery' ) ),
        'smartmenus'                   => array( 'src' => xevso_ASSETS_DIR . 'menu/smartmenus.js', 'dep' => array( 'jquery' ) ),
        'owl-carousel'                 => array( 'src' => xevso_ASSETS_DIR . 'owlcarousel/owl-carousel-min.js', 'dep' => array( 'jquery' ) ),
        'slick-min'                    => array( 'src' => xevso_ASSETS_DIR . 'slick/slick-min.js', 'dep' => array( 'jquery' ) ),
        'magnific-popup-js'            => array( 'src' => xevso_ASSETS_DIR . 'gallery/jquery-magnific-popup-min.js', 'dep' => array( 'jquery' ) ),
        'xevso-navigation'          => array( 'src' => xevso_ASSETS_DIR . 'js/navigation.js', 'dep' => array( 'jquery' ) ),
        'xevso-skip-link-focus-fix' => array( 'src' => xevso_ASSETS_DIR . 'js/skip-link-focus-fix.js', 'dep' => array( 'jquery' ) ),
        'xevso-main'                => array( 'src' => xevso_ASSETS_DIR . 'js/main.js', 'dep' => array( 'jquery' ) ),
    );

    foreach ( $xevso_js_files as $xevso_handle => $xevso_js_file ) {
        wp_enqueue_script( $xevso_handle, $xevso_js_file['src'], $xevso_js_file['dep'], xevso_VERSION, true );
    }

    // Add comment reply script.
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

}
add_action( 'wp_enqueue_scripts', 'xevso_assets' );

