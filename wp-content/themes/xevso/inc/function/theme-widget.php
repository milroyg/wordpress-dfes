<?php 

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function xevso_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'xevso' ),
		'id'            => 'sidebar',
		'class'            => 'sidebar',
		'description'   => esc_html__( 'Add widgets here.', 'xevso' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widtet-title"><span>',
		'after_title'   => '</span></h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Header Canvas', 'xevso' ),
		'id'            => 'header5',
		'description'   => esc_html__( 'Add widgets here.', 'xevso' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s Header-widget">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widtet-title"><span>',
		'after_title'   => '</span></h2>',
	) );
	if ( class_exists( 'WooCommerce' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Shop Widget', 'xevso' ),
			'id'            => 'tuchno-shop',
			'description'   => esc_html__( 'Add widgets here.', 'xevso' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widtet-title"><span>',
			'after_title'   => '</span></h2>',
		) );
	}
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer One', 'xevso' ),
			'id'            => 'footer-1-widget-area',
			'description'   => esc_html__( 'Add widgets here.', 'xevso' ),
			'before_widget' => '<section id="%1$s" class="widget ftwidgets %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widtet-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Two', 'xevso' ),
			'id'            => 'footer-2-widget-area',
			'description'   => esc_html__( 'Add widgets here.', 'xevso' ),
			'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widtet-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Three', 'xevso' ),
			'id'            => 'footer-3-widget-area',
			'description'   => esc_html__( 'Add widgets here.', 'xevso' ),
			'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widtet-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Four', 'xevso' ),
			'id'            => 'footer-4-widget-area',
			'description'   => esc_html__( 'Add widgets here.', 'xevso' ),
			'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widtet-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'xevso_widgets_init' );