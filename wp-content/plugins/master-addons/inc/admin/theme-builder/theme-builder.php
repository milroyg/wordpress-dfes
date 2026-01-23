<?php

defined('ABSPATH') || exit;

define('JLTMA_PLUGIN_URL', plugins_url('/', __FILE__));
define('JLTMA_PLUGIN_DIR', plugin_basename(__FILE__));


require plugin_dir_path(__FILE__) . 'class-theme-builder.php';

add_action( 'admin_init', function(){
    global $pagenow;
    $target_post_type = 'master_template';
    
    $redirect_url = admin_url( 'edit.php?post_type=master_template' );
    
    $current_post_type = '';
    $should_redirect = false;
    if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {

        if( isset($_GET['action']) && ( $_GET['action'] === 'elementor' || $_GET['action'] ===  'trash'  || $_GET['action'] ===  'delete' ) ){
            return;
        }
        $post_id = absint( $_GET['post'] );
        if ( $post_id ) {
            $current_post_type = get_post_type( $post_id );
            
            if ( $target_post_type === $current_post_type  ) {
              $master_template_type = get_post_meta($post_id , 'master_template_type', true);
              if(!empty($master_template_type) ){
                $redirect_url = $redirect_url . '&master_template_type_filter=' . $master_template_type; 
              }
                wp_safe_redirect( $redirect_url );
                exit;
            }
        }
    }
    
    if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) ) {
        $current_post_type = sanitize_key( $_GET['post_type'] );

        if ( $target_post_type === $current_post_type && ! isset( $_GET[ $required_query_var ] ) ) {
            wp_safe_redirect( $redirect_url );
            exit;
        }
    }
} );
