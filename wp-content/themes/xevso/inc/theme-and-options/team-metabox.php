<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//
// Metabox of the PAGE
// Set a unique slug-like ID
//
$teammeta = 'xevso_teammeta';

//
// Create a metabox
//
CSF::createMetabox( $teammeta, array(
    'title'        => 'Team Options',
    'post_type'    => array( 'team' ),
    'show_restore' => true,
) );

//
// Create a section
//
CSF::createSection( $teammeta, array(
    'title'  => esc_html__( 'Additional Informations ', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'       => 'xevso_team_sort_dec',
            'type'     => 'textarea',
            'title'    => esc_html__( 'Sort Description', 'xevso' ),
            'subtitle' => esc_html__( 'Add Team Sort Designation here', 'xevso' ),
            'default'  => esc_html__( 'Since joining Zircona in 2009, Tobby May has helped turn the company from a group of bright technology minds working with startups into a global', 'xevso' ),
        ),
        array(
            'id'       => 'xevso_team_stitle',
            'type'     => 'text',
            'title'    => esc_html__( 'Designation', 'xevso' ),
            'subtitle' => esc_html__( 'Add Team Designation here', 'xevso' ),
            'default'  => esc_html__( 'Web Developer', 'xevso' ),
        ),
        array(
            'id'       => 'xevso_team_age',
            'type'     => 'text',
            'title'    => esc_html__( 'Age', 'xevso' ),
            'subtitle' => esc_html__( 'Add Age here', 'xevso' ),
            'default'  => esc_html__( '24', 'xevso' ),
        ), 
        array(
            'id'       => 'xevso_team_blood',
            'type'     => 'text',
            'title'    => esc_html__( 'Blood Group', 'xevso' ),
            'subtitle' => esc_html__( 'Add Blood Group here', 'xevso' ),
            'default'  => esc_html__( 'A+', 'xevso' ),
        ),
        array(
            'id'       => 'xevso_team_work',
            'type'     => 'text',
            'title'    => esc_html__( 'Work Progress', 'xevso' ),
            'subtitle' => esc_html__( 'Add Work Progress here', 'xevso' ),
            'default'  => esc_html__( '100%', 'xevso' ),
        ),
        array(
            'id'       => 'xevso_team_gmail',
            'type'     => 'text',
            'title'    => esc_html__( 'E-mail', 'xevso' ),
            'subtitle' => esc_html__( 'Add E-mail here', 'xevso' ),
            'default'  => esc_html__( 'example@example.com', 'xevso' ),
        ),
        array(
            'id'       => 'xevso_team_phone',
            'type'     => 'text',
            'title'    => esc_html__( 'Phone', 'xevso' ),
            'subtitle' => esc_html__( 'Add Phone here', 'xevso' ),
            'default'  => esc_html__( '+555 6666 77 88', 'xevso' ),
        ),
    ),
) );

CSF::createSection( $teammeta, array(
    'title'  => esc_html__( 'Social Links ', 'xevso' ),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'      => 'xevso_team_socials',
            'type'    => 'repeater',
            'title'   => esc_html__( 'Social Links', 'xevso' ),
            'fields'  => array(
                array(
                    'id'      => 'xevso_team_social_label',
                    'type'    => 'text',
                    'title'   => esc_html__( 'Social Label', 'xevso' ),
                    'default' => esc_html__( 'Facebook','xevso' ),
                ),
                array(
                    'id'      => 'xevso_team_social_url',
                    'type'    => 'text',
                    'title'   => esc_html__( 'Social Website', 'xevso' ),
                    'default' => '#',
                ),
                array(
                    'id'      => 'xevso_team_social_icon',
                    'type'    => 'icon',
                    'title'   => esc_html__( 'Social Icon', 'xevso' ),
                    'default' => 'fa fa-facebook',
                ),
            ),
            'default' => array(
                array(
                    'xevso_team_social_label' => esc_html__( 'Facebook', 'xevso' ),
                    'xevso_team_social_url'   => '#',
                    'xevso_team_social_icon'  => 'fa fa-facebook',
                ),
                array(
                    'xevso_team_social_label' => esc_html__( 'Twitter', 'xevso' ),
                    'xevso_team_social_url'   => '#',
                    'xevso_team_social_icon'  => 'fa fa-twitter',
                ),
                array(
                    'xevso_team_social_label' => esc_html__( 'Linkedin', 'xevso' ),
                    'xevso_team_social_url'   => '#',
                    'xevso_team_social_icon'  => 'fa fa-linkedin',
                ),
                array(
                    'xevso_team_social_label' => esc_html__( 'Instagram', 'xevso' ),
                    'xevso_team_social_url'   => '#',
                    'xevso_team_social_icon'  => 'fa fa-instagram',
                ),
            ),
        ),
    ),
) );