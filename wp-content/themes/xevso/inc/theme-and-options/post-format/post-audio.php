<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$postaudio = 'xevso_postmeta_audio';

//
// Create a metabox
//
CSF::createMetabox( $postaudio, array(
    'title'        => esc_html('Post Format Audio','xevso'),
    'post_type'    => array( 'post' ),
    'post_formats' => 'audio',
) );

//
// Create a section
//
CSF::createSection( $postaudio, array(
    'title'  => esc_html__( 'Add Audio Link ', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'       => 'xevso_post_audio',
            'type'     => 'text',
            'title'    => esc_html__( 'Audio Link', 'xevso' ),
            'subtitle' => esc_html__( 'Add Post Audio Link here', 'xevso' ),
            'default'  => 'https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/336227439&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true'
        ),
    ),
) );