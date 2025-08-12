<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$projectmeta = 'xevso_project_meta';

//
// Create a metabox
//
CSF::createMetabox( $projectmeta, array(
    'title'        => 'Project Options',
    'post_type'    => array( 'project' ),
) );
//
// Create a section
//
CSF::createSection( $projectmeta, array(
    'title'  => esc_html__( 'Project Information', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'      => 'xevso_pro_title',
            'type'    => 'text',
            'title'   => esc_html__( 'Project Title', 'xevso' ),
            'desc'    => esc_html__( 'Please Type Project Title here', 'xevso' ),
            'default' => esc_html__( 'Business Agency ', 'xevso' ),
        ),
        array(
            'id'      => 'xevso_pro_client',
            'type'    => 'text',
            'title'   => esc_html__( 'Project Client', 'xevso' ),
            'desc'    => esc_html__( 'Please Type Project Client Name here', 'xevso' ),
            'default' => esc_html__( 'Mahi Al Rabbi', 'xevso' ),
        ),
        array(
            'id'      => 'xevso_pro_status',
            'type'    => 'text',
            'title'   => esc_html__( 'Project Status', 'xevso' ),
            'desc'    => esc_html__( 'Please Type Project Status here', 'xevso' ),
            'default' => esc_html__( 'Complete', 'xevso' ),
        ),
        array(
            'id'      => 'xevso_pro_date',
            'type'    => 'text',
            'title'   => esc_html__( 'Project Date', 'xevso' ),
            'desc'    => esc_html__( 'Please Type Project Date here', 'xevso' ),
            'default' => esc_html__( '05 August 2019', 'xevso' ),
        ),
        array(
            'id'      => 'xevso_pro_value',
            'type'    => 'text',
            'title'   => esc_html__( 'Project Value', 'xevso' ),
            'desc'    => esc_html__( 'Please Type Project Status here', 'xevso' ),
            'default' => esc_html__( '$500', 'xevso' ),
        ),
        array(
            'id'      => 'xevso_pro_video_link',
            'type'    => 'text',
            'title'   => esc_html__( 'Project Project video Link', 'xevso' ),
            'desc'    => esc_html__( 'Add Project video link', 'xevso' ),
            'default' => esc_html__( 'https://www.youtube.com/watch?v=u8Egk_j0EbY', 'xevso' ),
        ),
    ),
) );