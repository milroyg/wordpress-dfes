<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$xevsopost_meta = 'xevso_metabox_post';

//
// Create a metabox
//
CSF::createMetabox( $xevsometabox, array(
    'title'        => 'Metabox Options',
    'post_type'    => array( 'post'),
) );

// Create layout section
CSF::createSection($xevsopost_meta, array(
    'title'  => esc_html__('Layout', 'xevso'),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'      => 'xevso_layout_post_meta',
            'type'    => 'select',
            'title'   => esc_html__('Layout', 'xevso'),
            'options' => array(
                'full-width'    => esc_html__('Full Width', 'xevso'),
                'left-sidebar'  => esc_html__('Left Sidebar', 'xevso'),
                'right-sidebar' => esc_html__('Right Sidebar', 'xevso'),
            ),
            'default' => 'left-sidebar',
            'desc'    => esc_html__('Select layout', 'xevso'),
        ),
        array(
            'id'         => 'xevso_sidebar_post_meta',
            'type'       => 'select',
            'title'      => esc_html__('Sidebar', 'xevso'),
            'options'    => 'xevso_sidebars',
            'dependency' => array('xevso_layout_post_meta', '!=', 'full-width'),
            'desc'       => esc_html__('Select sidebar you want to show with this page.', 'xevso'),
        ),
    )
));