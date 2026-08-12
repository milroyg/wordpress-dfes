<?php
/**
 Plugin Name: DFES NICCI Chatbot
 Description: Integrates the Nicci Chatbot into WordPress.
 Version: 1.1
 Author: Alan Lobo
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue scripts.
 */
function dfes_nicci_enqueue_scripts() {
    // Ensure jQuery is enqueued.
    wp_enqueue_script( 'jquery' );

    // Add the initialization logic from chat.html
    $inline_script = "
 
    jQuery(document).ready(function() { 
      const targetSrc = 'https://niccicms.raj.nic.in/nicci/images/mili.png';
      const replacementSrc = '/wp-content/plugins/dfes-nicci/chatbot.png';
      
      const imgobserver = new MutationObserver((mutationsList) => {
          mutationsList.forEach((mutation) => {
              if (mutation.addedNodes.length) {
                  jQuery(mutation.addedNodes).each(function() {
                      // Find all newly added images
                      const imgs = jQuery(this).is('img') ? jQuery(this) : jQuery(this).find('img');
                      
                      imgs.each(function() {
                          // Check for exact URL match
                          if (jQuery(this).attr('src') === targetSrc) {
                              jQuery(this).attr('src', replacementSrc);
                          }
                      });
                  });
              }
          });
      });
      imgobserver.observe(document.body, { childList: true, subtree: true });
    });
    
    async function fetchAndFixScript(scriptUrl) {
      try {
        // 1. Fetch the third-party script as plain text
        const response = await fetch(scriptUrl);
        if (!response.ok) throw new Error(`HTTP error! status: ` + response.status);
        
        let scriptText = await response.text();
    
        // 2. String parse & Regex replacement (Fix your errors here)
        scriptText = scriptText.replace(/\\$\\(/g, 'jQuery(');
        scriptText = scriptText.replace(/\\$\\[/g, 'jQuery[');
        scriptText = scriptText.replace(/\\$\\./g, 'jQuery.');
        scriptText = scriptText.replace(/function IsAlphaNumeric\(.*0x6088ab.{2}/, '');
    
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
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/cb1t_v1.js', '/cb1t_v1.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/voint_v1.js', '/voint_v1.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/jquery.min.js', '/jquery.min.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/livesupport.js', '/livesupport.js1');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/js/restrict_v1.js', '/restrict_v1.js1');
                chatbotcode = chatbotcode.replace('\' + _rootPath + \'images/mili.png', '/wp-content/plugins/dfes-nicci/chatbot.png');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/images/mili.png', '/wp-content/plugins/dfes-nicci/chatbot.png');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/images/mili1.png', '/wp-content/plugins/dfes-nicci/chatbot.png');
                chatbotcode = chatbotcode.replace('https://niccicms.raj.nic.in/nicci/nicci/images/nicci.gif', '/wp-content/plugins/dfes-nicci/chatbot.png');
                chatbotcode = chatbotcode.replaceAll('$(', 'jQuery(');
                jQuery('#ContentNicci').append(chatbotcode);
            }
        });
    });
    
    function IsAlphaNumeric(_0x43c295,_0x3bd91a,_0x2d27a7){return true;}";
    wp_add_inline_script( 'jquery', $inline_script );
}
add_action( 'wp_enqueue_scripts', 'dfes_nicci_enqueue_scripts' );

/**
 * Inject the chatbot container into the footer.
 */
function dfes_nicci_inject_container() {
    echo '<div id="ContentNicci"></div>';
}
add_action( 'wp_footer', 'dfes_nicci_inject_container' );
