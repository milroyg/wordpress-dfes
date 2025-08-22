<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
wp_enqueue_style( 'xevso-client', plugins_url( 'css/clients.css', __FILE__ ) );
wp_enqueue_style( 'xevso-service', plugins_url( 'css/service.css', __FILE__ ) );
wp_enqueue_style( 'xevso-project', plugins_url( 'css/project.css', __FILE__ ) );
wp_enqueue_script( 'xevso-progress', plugins_url( 'js/circle-progress-min.js', __FILE__ ) );
wp_enqueue_script( 'xevso-projectjs', plugins_url( 'js/isotop-min.js', __FILE__ ) );
wp_enqueue_script( 'xevso-counter', plugins_url( 'js/count-to.js', __FILE__ ) );
wp_enqueue_script( 'xevso-counter-appear', plugins_url( 'js/jquery.appear.js', __FILE__ ) );