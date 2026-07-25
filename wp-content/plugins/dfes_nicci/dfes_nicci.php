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
    async function fetchAndFixScript(scriptUrl) {
      try {
        // 1. Fetch the third-party script as plain text
        const response = await fetch(scriptUrl);
        if (!response.ok) throw new Error(`HTTP error! status: ` + response.status);
        
        let scriptText = await response.text();
    
        // 2. String parse & Regex replacement (Fix your errors here)
        scriptText = scriptText.replace(/\$/g, 'jQuery');
    
        // 3. Create a Blob URL and append as a script tag
        const blob = new Blob([scriptText], { type: 'application/javascript' });
        const blobUrl = URL.createObjectURL(blob);
    
        const script = document.createElement('script');
        script.src = blobUrl;
        
        // Optional: Clean up memory once loaded
        script.onload = () => URL.revokeObjectURL(blobUrl);
    
        document.head.appendChild(script);
    
      } catch (error) {
        console.error('Failed to patch and run script:', error);
      }
    }

    jQuery(document).ready(function () {
        jQuery.ajax({
            type: 'POST',
            url: 'https://niccicms.raj.nic.in/nicci/chatbotService.asmx/chatbot_nicci',
            data: \"{authtoken: 'Gst2O6E2cpSOnGOB'}\",
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function (c) {
                fetchAndFixScript('https://niccicms.raj.nic.in/nicci/nicci/js/livesupport.js');
                fetchAndFixScript('https://niccicms.raj.nic.in/nicci/nicci/js/restrict_v1.js');
                fetchAndFixScript('https://niccicms.raj.nic.in/nicci/nicci/js/cb1t_v1.js');
                fetchAndFixScript('https://niccicms.raj.nic.in/nicci/nicci/js/voint_v1.js');
                var chatbotcode = c.d;
                // The original code replaces a specific jQuery URL to prevent it from loading/conflicting
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/jquery.min.js', 'https://niccicms.raj.nic.in/nicci/nicci/js/jquery.min.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/livesupport.js', 'https://niccicms.raj.nic.in/nicci/nicci/js/livesupport.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/restrict_v1.js', 'https://niccicms.raj.nic.in/nicci/nicci/js/restrict_v1.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/cb1t_v1.js', 'https://niccicms.raj.nic.in/nicci/nicci/js/cb1t_v1.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/voint_v1.js', 'https://niccicms.raj.nic.in/nicci/nicci/js/voint_v1.js1');
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
