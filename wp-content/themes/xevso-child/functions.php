<?php
// Load parent theme stylesheet
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('xevso-parent-style', get_template_directory_uri() . '/style.css');
});
