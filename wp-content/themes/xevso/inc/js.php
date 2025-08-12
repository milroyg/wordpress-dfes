<?php
if ( class_exists( 'CSF' ) ) {
    function xevso_inline_js() {
        wp_enqueue_script( 'xevso-inline-js', get_template_directory_uri() . '/assets/js/js-inline.js',array('jquery'), false, true );
        $xevso_js_editor = xevso_options( 'xevso_js_editor' );
        if(!empty($xevso_js_editor)){
            $xevso_inline_js =''.esc_attr($xevso_js_editor).'';
        }
        wp_add_inline_script( 'xevso-inline-js', $xevso_inline_js );
    }
}
add_action( 'wp_enqueue_scripts', 'xevso_inline_js' );