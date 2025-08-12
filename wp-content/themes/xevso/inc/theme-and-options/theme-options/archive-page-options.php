<?php
//Archive Options
CSF::createSection($xevsoThemeOption, array(
    'parent' => 'xevso_page_options',
    'title'  => esc_html__('Archive Page', 'xevso'),
    'icon'   => 'fa fa-archive',
    'fields' => array(
        array(
            'id'      => 'xevso_archive_layout',
            'type'    => 'select',
            'title'   => esc_html__('Archive Layout', 'xevso'),
            'options' => array(
                'grid'          => esc_html__('Grid Full', 'xevso'),
                'grid-ls'       => esc_html__('Grid With Left Sidebar', 'xevso'),
                'grid-rs'       => esc_html__('Grid With Right Sidebar', 'xevso'),
                'left-sidebar'  => esc_html__('Left Sidebar', 'xevso'),
                'right-sidebar' => esc_html__('Right Sidebar', 'xevso'),
            ),
            'default' => 'right-sidebar',
            'desc'    => esc_html__('Select blog page layout.', 'xevso'),
        ),
        array(
            'id'       => 'xevso_archive_banner',
            'type'     => 'switcher',
            'title'    => esc_html__('Enable Archive Banner', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Enable or disable archive page banner.', 'xevso'),
        ),
        array(
            'id'       => 'xevso_archive_pagination',
            'type'     => 'switcher',
            'title'    => esc_html__('Enable Archive Banner', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Enable or disable archive Pagination.', 'xevso'),
        ),
        array(
            'id'     => 'xevso_archive_banner_title_static_color',
            'type'   => 'color',
            'title'  => esc_html__( 'Banner Static Title Color', 'xevso' ),
            'output' => '.page-header .container h2.archive-title',
            'dependency' => array('xevso_archive_banner', '==', true),
            'desc'        => esc_html__('Select banner Static title color.', 'xevso'),
        ),
        array(
            'id'     => 'xevso_archive_banner_title_color',
            'type'   => 'color',
            'title'  => esc_html__( 'Banner Title Color', 'xevso' ),
            'output' => '.archive-title span',
            'dependency' => array('xevso_archive_banner', '==', true),
            'desc'        => esc_html__('Select banner title color.', 'xevso'),
        ),
    )
));