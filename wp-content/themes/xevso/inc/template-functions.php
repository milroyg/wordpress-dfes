<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package xevso
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function xevso_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'xevso_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function xevso_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'xevso_pingback_header' );

//Get excluded sidebar list
if( ! function_exists( 'xevso_sidebars' ) ) {
	function xevso_sidebars() {

		$options = array();
		// set ids of the registered sidebars for exclude
		$exclude = array( 'footer-1-widget-area','footer-2-widget-area','footer-3-widget-area','footer-4-widget-area' );

		global $wp_registered_sidebars;

		if( ! empty( $wp_registered_sidebars ) ) {
			foreach( $wp_registered_sidebars as $sidebar ) {
				if( ! in_array( $sidebar['id'], $exclude ) ) {
					$options[$sidebar['id']] = $sidebar['name'];
				}
			}
		}

		return $options;

	}
}