<?php
/**
 * Plugin Name: DFES Nicci
 * Description: Integrates the Nicci Chatbot into WordPress.
 * Version: 1.0
 * Author: Junie
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue scripts.
 */
function dfes_nicci_enqueue_scripts() {
    // Enqueue the external Nicci script. 
    // We assume WordPress's built-in jQuery is sufficient.
    wp_enqueue_script( 'nicci-js', 'https://niccicms.raj.nic.in/nicci/js/nicci.js', array( 'jquery' ), '1.0', true );

    // Add the initialization logic from chat.html
    $inline_script = "
    jQuery(document).ready(function () {
        jQuery.ajax({
            type: 'POST',
            url: 'https://niccicms.raj.nic.in/nicci/chatbotService.asmx/chatbot_nicci',
            data: \"{authtoken: 'Gst2O6E2cpSOnGOB'}\",
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function (c) {
                var chatbotcode = c.d;
                // The original code replaces a specific jQuery URL to prevent it from loading/conflicting
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/jquery.min.js', 'https://niccicms.raj.nic.in/nicci/nicci/js/jquery.min.js1');
                chatbotcode = chatbotcode.replaceAll('$(', 'jQuery(');
                jQuery('#ContentNicci').append(chatbotcode);
            }
        });
    });";
    wp_add_inline_script( 'nicci-js', $inline_script );
}
add_action( 'wp_enqueue_scripts', 'dfes_nicci_enqueue_scripts' );

/**
 * Inject the chatbot container into the footer.
 */
function dfes_nicci_inject_container() {
    echo '<div id="ContentNicci"></div>';
}
add_action( 'wp_footer', 'dfes_nicci_inject_container' );
