<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$xevsometabox = 'xevso_metabox';

//
// Create a metabox
//
CSF::createMetabox( $xevsometabox, array(
    'title'        => 'Metabox Options',
    'post_type'    => array( 'page', 'post','project','team' ),
    'show_restore' => true,
) );

//
// Create a section
//
CSF::createSection( $xevsometabox, array(
    'title'  => esc_html__( 'Header', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'       => 'xevso_meta_enable_header',
            'type'     => 'switcher',
            'title'    => esc_html__( 'Enable Header', 'xevso' ),
            'subtitle' => esc_html__( 'Enable this Options if you need','xevso' ),
        ),
        array(
            'id'          => 'xevso_meta_select_header',
            'type'        => 'select',
            'title'       => esc_html__( 'Select Header Style', 'xevso' ),
            'placeholder' => esc_html__( 'Select an option', 'xevso' ),
            'options'     => array(
                'one'   => esc_html__( 'Header One', 'xevso' ),
                'two'   => esc_html__( 'Header Two', 'xevso' ),
            ),
            'dependency'  => array( 'xevso_meta_enable_header', '==', 'true' ),
        ),
        array(
            'id'         => 'xevso_meta_header4_top_anable',
            'type'       => 'switcher',
            'title'      => esc_html__( 'Enable Top Header', 'xevso' ),
            'subtitle'   => esc_html__( 'Enable this Options if you need','xevso'),
            'dependency' => array( 'xevso_meta_select_header|xevso_meta_enable_header', '==|==', 'four|true' ),
        ),
        array(
            'id'          => 'xevso_meta_header4_top_margin',
            'type'        => 'spacing',
            'title'       => 'Spacing',
            'output'      => '.header-top4',
            'dependency'  => array( 'xevso_meta_select_header|xevso_meta_enable_header|xevso_meta_header4_top_anable', '==|==|==', 'four|true|true' ),
            'output_mode' => 'margin', // or margin, relative
            'default'     => array(
                'top'    => '0',
                'right'  => '0',
                'bottom' => '0',
                'left'   => '0',
                'unit'   => 'px',
            ),
        ),
        array(
            'id'         => 'xevso_meta_header4_topbg',
            'type'       => 'color',
            'title'      => esc_html__( 'Background Color', 'xevso' ),
            'subtitle'   => esc_html__( 'Choose Your Header Top Background Color', 'xevso' ),
            'default'    => '#2c3943',
            'dependency' => array( 'xevso_meta_select_header|xevso_meta_enable_header|xevso_meta_header4_top_anable', '==|==|==', 'four|true|true' ),
        ),
        array(
            'id'         => 'xevso_meta_header4_top_colors',
            'type'       => 'color_group',
            'title'      => esc_html__( 'Top Header Items Colors', 'xevso' ),
            'dependency' => array( 'xevso_meta_select_header|xevso_meta_enable_header|xevso_meta_header4_top_anable', '==|==|==', 'four|true|true' ),
            'subtitle'   => esc_html__( 'Choose Your Top Header Colors', 'xevso' ),
            'options'    => array(
                'meta_header4_label'     => esc_html__( 'Label Color', 'xevso' ),
                'meta_header4_text'      => esc_html__( 'Text Color', 'xevso' ),
                'meta_header4_link'      => esc_html__( 'link Color', 'xevso' ),
                'meta_header4_linkhover' => esc_html__( 'link Hover Color', 'xevso' ),
                'meta_header4_border'    => esc_html__( 'border Color', 'xevso' ),
            ),
        ),

        array(
            'id'         => 'xevso_meta_header4_quete_colors',
            'type'       => 'color_group',
            'title'      => esc_html__( 'Quote Colors', 'xevso' ),
            'dependency' => array( 'xevso_meta_select_header|xevso_meta_enable_header|xevso_meta_header4_top_anable', '==|==|==', 'four|true|true' ),
            'subtitle'   => esc_html__( 'Choose Your Top Header Colors', 'xevso' ),
            'options'    => array(
                'meta_header4_qc'   => esc_html__( 'Text Color', 'xevso' ),
                'meta_header4_qbg'  => esc_html__( 'Background Color', 'xevso' ),
                'meta_header4_qhtc' => esc_html__( 'Hover Text Color', 'xevso' ),
                'meta_header4_qhbc' => esc_html__( 'Background Hover Color', 'xevso' ),
            ),
        ),
        array(
            'id'         => 'xevso_meta_header4_menubg',
            'type'       => 'color',
            'title'      => esc_html__( 'Menu Background', 'xevso' ),
            'subtitle'   => esc_html__( 'Choose Your Header Top Background Color', 'xevso' ),
            'dependency' => array( 'xevso_meta_select_header|xevso_meta_enable_header|xevso_meta_header4_top_anable', '==|==|==', 'four|true|true' ),
        ),
    ),
) );

// Create layout section
CSF::createSection($xevsometabox, array(
    'title'  => esc_html__('Layout', 'xevso'),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'      => 'xevso_layout_meta',
            'type'    => 'select',
            'title'   => esc_html__('Layout', 'xevso'),
            'placeholder' => esc_html__( 'Select an option', 'xevso'),
            'options' => array(
                'full-width'    => esc_html__('Full Width', 'xevso'),
                'left-sidebar'  => esc_html__('Left Sidebar', 'xevso'),
                'right-sidebar' => esc_html__('Right Sidebar', 'xevso'),
            ),
            'desc'    => esc_html__('Select layout', 'xevso'),
        ),
        array(
            'id'         => 'xevso_sidebar_meta',
            'type'       => 'select',
            'title'      => esc_html__('Sidebar', 'xevso'),
            'options'    => 'xevso_sidebars',
            'dependency' => array('xevso_layout_meta', 'any', 'left-sidebar,right-sidebar'),
            'desc'       => esc_html__('Select sidebar you want to show with this page.', 'xevso'),
        ),
        array(
            'id'         => 'xevso_meta_page_navbar',
            'type'       => 'switcher',
            'title'      => esc_html__( 'Enable Pagination', 'xevso' ),
            'subtitle'   => esc_html__( 'This Options only for Default page','xevso' ),
            'default' => true
        ),
    )
));

// Create a section
CSF::createSection( $xevsometabox, array(
    'title'  => esc_html__( 'Banner / Breadcrumb Area', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'       => 'xevso_meta_enable_banner',
            'type'     => 'switcher',
            'title'    => esc_html__( 'Enable Banner', 'xevso' ),
            'text_on'  => esc_html__( 'Yes', 'xevso' ),
            'text_off' => esc_html__( 'No', 'xevso' ),
            'default'  => true,
            'desc'     => esc_html__( 'Enable or disable banner.', 'xevso' ),
        ),
        array(
            'id'                    => 'xevso_meta_banner_options',
            'type'                  => 'background',
            'title'                 => esc_html__( 'Banner Background', 'xevso' ),
            'background_gradient'   => true,
            'background_origin'     => false,
            'background_clip'       => false,
            'background_blend-mode' => false,
            'default'               => array(
                'background-color'              => '',
                'background-gradient-color'     => '',
                'background-gradient-direction' => 'to right',
                'background-size'               => 'cover',
                'background-position'           => 'center center',
                'background-repeat'             => 'no-repeat',
            ),
            'dependency'            => array( 'xevso_meta_enable_banner', '==', true ),
            'output'                => '.page-header__bg',
            'desc'                  => esc_html__( 'If you use gradient background color (Second Color) then background image will not working. Gradient background priority is higher then background image', 'xevso' ),
        ),

        array(
            'id'         => 'xevso_meta_custom_title',
            'type'       => 'text',
            'title'      => esc_html__( 'Banner Custom Title', 'xevso' ),
            'dependency' => array( 'xevso_meta_enable_banner', '==', true ),
            'desc'       => esc_html__( 'If you want to use custom title write title here.If you dont, leave it empty.', 'xevso' ),
        ),

        array(
            'id'         => 'xevso_meta_banner_title_color',
            'type'       => 'color',
            'title'      => esc_html__( 'Banner Title Color', 'xevso' ),
            'output'     => '.page-header .container h2',
            'dependency' => array( 'xevso_meta_enable_banner', '==', true ),
            'desc'       => esc_html__( 'Select banner title color.', 'xevso' ),
        ),

        array(
            'id'         => 'xevso_meta_breadcrumb_normal_color',
            'type'       => 'color',
            'title'      => esc_html__( 'Breadcrumb Text Color', 'xevso' ),
            'output'     => '.thm-breadcrumb span.current-item',
            'subtitle'   => esc_html__( 'Breadcrumb Text Color', 'xevso' ),
            'dependency' => array( 'xevso_meta_enable_banner', '==', true ),
            'desc'       => esc_html__( 'Select breadcrumb text color.', 'xevso' ),
        ),

        array(
            'id'         => 'xevso_meta_breadcrumb_link_color',
            'type'       => 'link_color',
            'title'      => esc_html__( 'Breadcrumb Link Color', 'xevso' ),
            'output'     => array( '.thm-breadcrumb.list-unstyled span>a' ),
            'subtitle'   => esc_html__( 'Breadcrumb Link color', 'xevso' ),
            'dependency' => array( 'xevso_meta_enable_banner', '==', true ),
            'desc'       => esc_html__( 'Select breadcrumb link and link hover color.', 'xevso' ),
        ),

    ),
) );
CSF::createSection( $xevsometabox, array(
    'title'  => esc_html__( 'Footer Settings', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'          => 'xevso_meta_footer_styles',
            'type'        => 'select',
            'title'       => esc_html__( 'Footer Styles', 'xevso' ),
            'placeholder' => esc_html__( 'Select an option', 'xevso' ),
            'options'     => array(
                'one'   => esc_html__( 'Footer One', 'xevso' ),
            ),
            'subtitle'    => esc_html__( 'Select Your Footer', 'xevso' ),
        ),
        array(
            'id'          => 'xevso_meta_footer_bt_top',
            'type'        => 'spacing',
            'title'       => esc_html__( 'Padding Top', 'xevso' ),
            'subtitle'       => esc_html__( 'This Filed only for Blank Templae', 'xevso' ),
            'output'      => '.default-page-section.blank-page',
            'page_templates' => 'blank-template.php', // Spesific page template
            'output_mode' => 'padding', 
            'left'  => false,
            'top'  => false,
            'right' => false,
            'units' => array( 'px' ),
            'default'     => array(
              'bottom'    => '100',
            ),
        ),
    ),
) );

