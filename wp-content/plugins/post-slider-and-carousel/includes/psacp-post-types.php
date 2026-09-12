<?php
/**
 * Register Post Type and Taxonomy
 *
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to register post type
 * 
 * @since 3.5
 */
function psac_register_post_type() {

	$psac_layout_labels = array(
		'name'						=> __( 'Layouts', 'post-slider-and-carousel' ),
		'singular_name'				=> __( 'Layout', 'post-slider-and-carousel' ),
		'add_new'					=> __( 'Add Layout', 'post-slider-and-carousel' ),
		'add_new_item'				=> __( 'Add New Layout', 'post-slider-and-carousel' ),
		'edit_item'					=> __( 'Edit Layout', 'post-slider-and-carousel' ),
		'new_item'					=> __( 'New Layout', 'post-slider-and-carousel' ),
		'all_items'					=> __( 'All Layout', 'post-slider-and-carousel' ),
		'view_item'					=> __( 'View Layout', 'post-slider-and-carousel' ),
		'search_items'				=> __( 'Search Layout', 'post-slider-and-carousel' ),
		'not_found'					=> __( 'No layout found', 'post-slider-and-carousel' ),
		'not_found_in_trash'		=> __( 'No layout found in trash', 'post-slider-and-carousel' ),
		'menu_name'					=> __( 'Post Slider and Carousel Layout', 'post-slider-and-carousel' ),
		'parent_item_colon'			=> '',
		'items_list'				=> __( 'Layout list.', 'post-slider-and-carousel' ),
		'item_published'			=> __( 'Layout published.', 'post-slider-and-carousel' ),
		'item_published_privately'	=> __( 'Layout published privately.', 'post-slider-and-carousel' ),
		'item_reverted_to_draft'	=> __( 'Layout reverted to draft.', 'post-slider-and-carousel' ),
		'item_scheduled'			=> __( 'Layout scheduled.', 'post-slider-and-carousel' ),
		'item_updated'				=> __( 'Layout updated.', 'post-slider-and-carousel' ),
		'item_link'					=> __( 'Layout Link', 'post-slider-and-carousel' ),
		'item_link_description'		=> __( 'A link to a layout.', 'post-slider-and-carousel' ),
	);

	$psac_layout_args = array(
		'labels'				=> $psac_layout_labels,
		'show_in_rest'			=> 'false',
		'public'				=> false,
		'hierarchical'			=> false,
		'publicly_queryable'	=> false,
		'exclude_from_search'	=> true,
		'show_ui'				=> false,
		'query_var'				=> true,
		'rewrite'				=> false,
		'supports'				=> array('title', 'editor', 'author')
	);

	// Register layout post type
	register_post_type( PSAC_LAYOUT_POST_TYPE, apply_filters( 'psacp_layout_registered_post_type_args', $psac_layout_args ) );
}
add_action( 'init', 'psac_register_post_type' );