<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$postgallery = 'xevso_postmeta_gallery';

//
// Create a metabox
//
CSF::createMetabox( $postgallery, array(
    'title'        => esc_html('Post Format image Gallery','xevso'),
    'post_type'    => array( 'post' ),
    'post_formats' => 'gallery',
) );

//
// Create a section
//
CSF::createSection( $postgallery, array(
    'title'  => esc_html__( 'Add Gallery Image', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'          => 'xevso_post_gallery',
            'type'        => 'gallery',
            'title'       => esc_html('Gallery','xevso'),
            'add_title'   => esc_html('Add Image','xevso'),
            'edit_title'  => esc_html('Edit Image','xevso'),
            'clear_title' => esc_html('Remove Image','xevso'),
        ),
    ),
) );