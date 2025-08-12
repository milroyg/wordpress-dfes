<?php
//Blog Page Options
CSF::createSection($xevsoThemeOption, array(
    'parent' => 'xevso_page_options',
    'title'  => esc_html__('Blog Page', 'xevso'),
    'icon'   => 'fa fa-pencil-square-o',
    'fields' => array(
        array(
            'id'      => 'xevso_blog_layout',
            'type'    => 'select',
            'title'   => esc_html__('Blog Layout', 'xevso'),
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
            'id'       => 'xevso_blog_banner_enable',
            'type'     => 'switcher',
            'title'    => esc_html__('Enable Banner', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide / Show Banner.', 'xevso'),
        ),
        array(
            'id'         => 'xevso_blog_title',
            'type'       => 'text',
            'title'      => esc_html__('Banner Title', 'xevso'),
            'dependency' => array( 'xevso_blog_banner_enable', '==', 'true' ),
            'desc'       => esc_html__('Type blog banner title here.', 'xevso'),
        ),

       
        array(
            'id'       => 'xevso_post_author',
            'type'     => 'switcher',
            'title'    => esc_html__('Show Author Name', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide / Show post author name.', 'xevso'),
        ),

        array(
            'id'       => 'xevso_post_date',
            'type'     => 'switcher',
            'title'    => esc_html__('Show Post Date', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide / Show post date.', 'xevso'),
        ),

        array(
            'id'         => 'xevso_cmnt_number',
            'type'       => 'switcher',
            'title'      => esc_html__('Show Comment Number', 'xevso'),
            'default'    => true,
            'text_on'    => esc_html__('Yes', 'xevso'),
            'text_off'   => esc_html__('No', 'xevso'),
            'desc'       => esc_html__('Hide / Show post comment number.', 'xevso'),
        ),

        array(
            'id'         => 'xevso_show_category',
            'type'       => 'switcher',
            'title'      => esc_html__('Show Category Name', 'xevso'),
            'default'    => true,
            'text_on'    => esc_html__('Yes', 'xevso'),
            'text_off'   => esc_html__('No', 'xevso'),
            'desc'       => esc_html__('Hide / Show post category name.', 'xevso'),
        ),
        array(
            'id'         => 'xevso_show_pagination',
            'type'       => 'switcher',
            'title'      => esc_html__('Show Pagination', 'xevso'),
            'default'    => true,
            'text_on'    => esc_html__('Yes', 'xevso'),
            'text_off'   => esc_html__('No', 'xevso'),
            'desc'       => esc_html__('Hide / Show post category name.', 'xevso'),
        ),
    )
));