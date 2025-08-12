<?php
//Single Post
CSF::createSection($xevsoThemeOption, array(
    'parent' => 'xevso_page_options',
    'title'  => esc_html__('Single Post / Post Details', 'xevso'),
    'icon'   => 'fa fa-pencil',
    'fields' => array(

        array(
            'id'       => 'xevso_single_post_author',
            'type'     => 'switcher',
            'title'    => esc_html__('Post Author Name', 'xevso'),
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide or show author name on post details page.', 'xevso'),
            'default'  => true
        ),

        array(
            'id'       => 'xevso_single_post_date',
            'type'     => 'switcher',
            'title'    => esc_html__('Post Date', 'xevso'),
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide or show date on post details page.', 'xevso'),
            'default'  => true
        ),

        array(
            'id'       => 'xevso_single_post_cmnt',
            'type'     => 'switcher',
            'title'    => esc_html__('Post Comments Number', 'xevso'),
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide or show comments number on post details page.', 'xevso'),
            'default'  => true
        ),

        array(
            'id'       => 'xevso_single_post_cat',
            'type'     => 'switcher',
            'title'    => esc_html__('Post Categories', 'xevso'),
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide or show categories on post details page.', 'xevso'),
            'default'  => true
        ),

        array(
            'id'       => 'xevso_single_post_tag',
            'type'     => 'switcher',
            'title'    => esc_html__('Post Tags', 'xevso'),
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide or show tags on post details page.', 'xevso'),
            'default'  => true
        ),

        array(
            'id'       => 'xevso_post_share',
            'type'     => 'switcher',
            'title'    => esc_html__('Social Share icons', 'xevso'),
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide or show social share icons on post details page.', 'xevso'),
            'default'  => true
        ),

        array(
            'id'       => 'xevso_author_profile',
            'type'     => 'switcher',
            'title'    => esc_html__('Post Author Info', 'xevso'),
            'text_on'  => esc_html__('Yes', 'xevso'),
            'text_off' => esc_html__('No', 'xevso'),
            'desc'     => esc_html__('Hide or show post author info on post details page.', 'xevso'),
            'default'  => false
        ),
    ),
));