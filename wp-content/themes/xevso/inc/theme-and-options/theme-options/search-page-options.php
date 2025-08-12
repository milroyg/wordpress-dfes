<?php
//search Options
CSF::createSection($xevsoThemeOption, array(
    'parent' => 'xevso_page_options',
    'title'  => esc_html__('Search Page', 'xevso'),
    'icon'   => 'fa fa-search',
    'fields' => array(
        array(
            'id'      => 'xevso_search_layout',
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
            'id'       => 'xevso_search_banner',
            'type'     => 'switcher',
            'title'    => esc_html__('Enable search Banner', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Enable or disable search page banner.', 'xevso'),
        ),
        array(
            'id'       => 'xevso_search_post_author',
            'type'     => 'switcher',
            'title'    => esc_html__('Show Author Name', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide / Show post author name.', 'xevso'),
        ),

        array(
            'id'       => 'xevso_search_post_date',
            'type'     => 'switcher',
            'title'    => esc_html__('Show Post Date', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide / Show post date.', 'xevso'),
        ),

        array(
            'id'         => 'xevso_search_cmnt_number',
            'type'       => 'switcher',
            'title'      => esc_html__('Show Comment Number', 'xevso'),
            'default'    => true,
            'text_on'    => esc_html__('Yes', 'xevso'),
            'text_off'   => esc_html__('No', 'xevso'),
            'desc'       => esc_html__('Hide / Show post comment number.', 'xevso'),
        ),

        array(
            'id'         => 'xevso_search_show_category',
            'type'       => 'switcher',
            'title'      => esc_html__('Show Category Name', 'xevso'),
            'default'    => true,
            'text_on'    => esc_html__('Yes', 'xevso'),
            'text_off'   => esc_html__('No', 'xevso'),
            'desc'       => esc_html__('Hide / Show post category name.', 'xevso'),
        ),
        array(
            'id'       => 'xevso_search_pagination',
            'type'     => 'switcher',
            'title'    => esc_html__('Enable search Banner', 'xevso'),
            'default'  => true,
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Enable or disable search Pagination.', 'xevso'),
        ),
    )
));