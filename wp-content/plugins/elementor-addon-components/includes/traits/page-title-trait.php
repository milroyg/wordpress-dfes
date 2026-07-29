<?php
/** @since 2.1.0 */

namespace EACCustomWidgets\Includes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Page_Title_Trait {
	public function get_page_title( $include_context = false ) {
		$title = '';

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			$title = esc_html( get_the_title( wc_get_page_id( 'shop' ) ) );
		} elseif ( is_singular() ) {
			/* translators: %s: Search term. */
			$title = esc_html( get_the_title() );

			if ( $include_context ) {
				$post_type_obj = get_post_type_object( get_post_type() );
				$title         = sprintf( '%s: %s', $post_type_obj->labels->singular_name, $title );
			}
		} elseif ( is_search() ) {
			$title = sprintf( '%1$s: %2$s', esc_html__( 'Search results for', 'eac-components' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				$title .= sprintf( '&nbsp;&ndash; Page: %s', get_query_var( 'paged' ) );
			}
		} elseif ( is_category() ) {
			$title = single_cat_title( '', false );

			if ( $include_context ) {
				$title = sprintf( '%1$s: %2$s', esc_html__( 'Category', 'eac-components' ), $title );
			}
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
			if ( $include_context ) {
				$title = sprintf( '%1$s: %2$s', esc_html__( 'Tag', 'eac-components' ), $title );
			}
		} elseif ( is_author() ) {
			$title = get_the_author();

			if ( $include_context ) {
				$title = sprintf( '%1$s: %2$s', esc_html__( 'Author', 'eac-components' ), $title );
			}
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );

			if ( $include_context ) {
				$title = sprintf( 'Archives: %s', $title );
			}
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );

			if ( $include_context ) {
				$tax = get_taxonomy( get_queried_object()->taxonomy );
				$title = sprintf( '%1$s: %2$s', $tax->labels->singular_name, $title );
			}
		} elseif ( is_archive() ) {
			$title = esc_html__( 'Archives', 'eac-components' );
		} elseif ( is_404() ) {
			$title = esc_html__( 'Page not found', 'eac-components' );
		} else {
			$title = esc_html__( 'Unknown page type', 'eac-components' );
		}
		return $title;
	}
}
