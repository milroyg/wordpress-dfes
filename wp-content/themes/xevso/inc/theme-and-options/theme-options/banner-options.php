<?php

//Banner Options
CSF::createSection($xevsoThemeOption, array(
    'parent' => 'xevso_page_options',
    'title'  => esc_html__('Banner / Breadcrumb Area', 'xevso'),
    'icon'   => 'fa fa-flag',
    'fields' => array(


        array(
            'id'                    => 'xevso_banner_default_options',
            'type'                  => 'background',
            'title'                 => esc_html__('Banner Background', 'xevso'),
            'background_gradient'   => true,
            'background_origin'     => false,
            'background_clip'       => false,
            'background_blend-mode' => false,
            'default'               => array(
                'background-color'              => '#798795',
                'background-gradient-color'     => '',
                'background-gradient-direction' => 'to right',
                'background-size'               => 'cover',
                'background-position'           => 'center center',
                'background-repeat'             => 'no-repeat',
            ),
            'output'                => '.breadcroumb-boxs',
            'subtitle'              => esc_html__('Select banner default properties for all page / post. You can override this settings on individual page / post.', 'xevso'),
            'desc'                  => esc_html__('If you use gradient background color (Second Color) then background image will not working. Gradient background priority is higher then background image', 'xevso'),
        ),
        array(
            'id'       => 'xevso_banner_title_color',
            'type'     => 'color',
            'title'    => esc_html__('Banner Title Color', 'xevso'),
            'output'   => '.brea-title h2',
            'subtitle' => esc_html__('Banner title color.', 'xevso'),
            'desc'     => esc_html__('Select banner title color.', 'xevso'),
        ),
        array(
            'id'       => 'xevso_breadcrumb_normal_color',
            'type'     => 'color',
            'title'    => esc_html__('Breadcrumb Text Color', 'xevso'),
            'output'   => '.breadcroumb-boxs span.current-item',
            'subtitle' => esc_html__('Breadcrumb Text Color', 'xevso'),
            'desc'     => esc_html__('Select breadcrumb text color.', 'xevso'),
        ),
        array(
            'id'       => 'xevso_breadcrumb_link_color',
            'type'     => 'link_color',
            'title'    => esc_html__('Breadcrumb Link Color', 'xevso'),
            'output'   => array('.breadcroumb-boxs .breadcrumb-bcn a'),
            'subtitle' => esc_html__('Breadcrumb Link color', 'xevso'),
            'desc'     => esc_html__('Select breadcrumb link and link hover color.', 'xevso'),
        ),
    )
));