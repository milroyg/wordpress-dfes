<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Create layout and options section
CSF::createSection( $xevsoThemeOption, array(
    'title' => esc_html__( 'Footer Settings', 'xevso' ),
    'id'    => 'xevso_footer_options',
    'icon'  => 'fa fa-sort-amount-asc',
) );
// Header Style
CSF::createSection( $xevsoThemeOption, array(
    'parent' => 'xevso_footer_options',
    'title'  => esc_html__( 'Footer Options', 'xevso' ),
    'icon'   => 'fa fa-header',
    'fields' => array(

        array(
            'id'            => 'xevso_copyright_text',
            'type'          => 'wp_editor',
            'title'         => esc_html__( 'Copyright Text', 'xevso' ),
            'subtitle'      => esc_html__( 'Site copyright text', 'xevso' ),
            'desc'          => esc_html__( 'Type site copyright text here.', 'xevso' ),
            'tinymce'       => true,
            'quicktags'     => true,
            'media_buttons' => false,
            'height'        => '100px',
        ),
        array(
            'id'    => 'xevso_fotter_img_switch',
            'type'  => 'switcher',
            'title' => esc_html__( 'Enable Background Image', 'xevso' ),
            'subtitle'      => esc_html__( 'Enable Background Image if You use Background Image', 'xevso' ),
            
        ),
        array(
            'id'                    => 'xevso_fotter_img',
            'type'                  => 'background',
            'output'                => '.footer-one',
            'title'                 => esc_html__( 'Background Image', 'xevso' ),
            'background_color'      => false,
            'background_gradient'   => false,
            'background_origin'     => false,
            'background_clip'       => false,
            'background_blend_mode' => false,
            'default'               => array(
                'background-size'     => 'cover',
                'background-position' => 'center center',
                'background-repeat'   => 'no-repeat',
            ),
            'subtitle'      => esc_html__( 'Upload Footer Background Image', 'xevso' ),
            
        ),
        array(
            'id'          => 'xevso_fotter_bg',
            'type'        => 'color',
            'title'       => esc_html__( 'Footer Background Color', 'xevso' ),
            'output'      => 'footer.footer-one.footer-top:after',
            'output_mode' => 'background-color',
            'subtitle'      => esc_html__( 'Choose Footer Background Color', 'xevso' ),
           
        ),
    ),
) );
// End Header Style
// Widget Layout
CSF::createSection( $xevsoThemeOption, array(
    'parent' => 'xevso_footer_options',
    'title'  => esc_html__( 'Widget Layout', 'xevso' ),
    'icon'   => 'fa fa-th',
    'fields' => array(
        array(
            'id'      => 'footer_column_layout',
            'type'    => 'image_select',
            'title'   => esc_attr__( 'Footer Widget Columns', 'xevso' ),
            'options' => array(
                '12'      => get_template_directory_uri() . '/assets/images/widgets/footer_col_12.png',
                '6_6'     => get_template_directory_uri() . '/assets/images/widgets/footer_col_6_6.png',
                '4_4_4'   => get_template_directory_uri() . '/assets/images/widgets/footer_col_4_4_4.png',
                '3_3_3_3' => get_template_directory_uri() . '/assets/images/widgets/footer_col_3_3_3_3.png',
                '8_4'     => get_template_directory_uri() . '/assets/images/widgets/footer_col_8_4.png',
                '4_8'     => get_template_directory_uri() . '/assets/images/widgets/footer_col_4_8.png',
                '6_3_3'   => get_template_directory_uri() . '/assets/images/widgets/footer_col_6_3_3.png',
                '3_3_6'   => get_template_directory_uri() . '/assets/images/widgets/footer_col_3_3_6.png',
                '8_2_2'   => get_template_directory_uri() . '/assets/images/widgets/footer_col_8_2_2.png',
                '2_2_8'   => get_template_directory_uri() . '/assets/images/widgets/footer_col_2_2_8.png',
                '6_2_2_2' => get_template_directory_uri() . '/assets/images/widgets/footer_col_6_2_2_2.png',
                '2_2_2_6' => get_template_directory_uri() . '/assets/images/widgets/footer_col_2_2_2_6.png',
            ),
            'default' => '3_3_3_3',
            'after'   => esc_attr__( 'Select Footer Column layout View for widgets.', 'xevso' ),
        ),
        array(
            'id'       => 'xevso_footer_title_color',
            'type'     => 'color',
            'title'    => esc_attr__( 'Footer Title Color', 'xevso' ),
            'subtitle' => esc_html__( 'Select Color for Footer Title', 'xevso' ),
            'output'   => 'footer h2.widtet-title,footer dfn, footer cite, footer em, footer i, footer strong,footer dfn, footer cite, footer em, footer i, footer span',
            'default'  => '#ffffff',
        ),
        array(
            'id'       => 'xevso_footer_body_color',
            'type'     => 'color',
            'title'    => esc_attr__( 'Footer Body Color', 'xevso' ),
            'subtitle' => esc_html__( 'Select Color for Footer Body font', 'xevso' ),
            'output'   => 'footer p,footer,footer .widget.widget_calendar #wp-calendar th,footer .widget.widget_calendar #wp-calendar td,footer .widget ul li,.ft-subscribe-dec p',
            'default'  => '#798795',
        ),
        array(
            'id'       => 'xevso_footer_link_color',
            'type'     => 'color',
            'title'    => esc_attr__( 'Footer Link Color', 'xevso' ),
            'subtitle' => esc_html__( 'Select Color for Footer Link', 'xevso' ),
            'output'   => 'footer .widget ul li a,footer .widget a,footer .widget ul li .comment-author-link a,footer .widget_tag_cloud .tagcloud a,.widget .footer-widget__post-list-content h3 a',
            'default'  => '#8e96a0',
        ),
        array(
            'id'       => 'xevso_footer_linkh_color',
            'type'     => 'color',
            'title'    => esc_attr__( 'Footer Link Hover Color', 'xevso' ),
            'subtitle' => esc_html__( 'Select Color for Footer Link Hover', 'xevso' ),
            'output'   => 'footer .widget ul li a:hover,footer  .widget a:hover,footer .widget ul li .comment-author-link a:hover, footer .widget_tag_cloud .tagcloud a:hover,.widget .footer-widget__post-list-content h3 a:hover',
            'default'  => '#FB6D62',
        ),
    ),
) );