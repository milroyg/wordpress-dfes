<?php
/**
 * Popup Canvas Template for Elementor Preview
 * This template is used when previewing popups in Elementor editor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( get_the_title() ) . ' - Popup Preview'; ?></title>
    <?php wp_head(); ?>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .jltma-popup-preview-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .jltma-popup-preview-header {
            background: #2271b1;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .jltma-popup-preview-header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }
        
        .jltma-popup-preview-body {
            padding: 0;
            min-height: 500px;
            position: relative;
        }
        
        /* Popup canvas styling */
        .jltma-popup-canvas {
            width: 100%;
            min-height: 500px;
            position: relative;
            background: white;
        }
        
        .jltma-popup-canvas.jltma-popup-type-modal {
            max-width: 600px;
            margin: 40px auto;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .jltma-popup-canvas.jltma-popup-type-slide-in {
            max-width: 400px;
            margin: 20px;
            margin-left: auto;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .jltma-popup-canvas.jltma-popup-type-notification {
            width: 100%;
            margin: 0;
            border-radius: 0;
            min-height: 80px;
        }
        
        .jltma-popup-canvas.jltma-popup-type-full-screen {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            border-radius: 0;
        }
        
        /* Elementor editor specific styles */
        .elementor-editor-active .jltma-popup-canvas {
            border: 2px dashed #e0e0e0;
        }
        
        .elementor-editor-active .jltma-popup-canvas:hover {
            border-color: #2271b1;
        }
    </style>
</head>
<body <?php body_class('jltma-popup-preview'); ?>>
    
    <div class="jltma-popup-preview-container">
        <div class="jltma-popup-preview-header">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3,3H21V5H3V3M4,6H20V21H4V6M5,7V20H19V7H5Z" />
            </svg>
            <h1><?php echo esc_html( get_the_title() ); ?> - Popup Preview</h1>
        </div>
        
        <div class="jltma-popup-preview-body">
            <?php
            // Output the Elementor content
            if (class_exists('\Elementor\Plugin')) {
                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor's get_builder_content_for_display returns sanitized HTML
            } else {
                echo '<div style="padding: 40px; text-align: center; color: #666;">Elementor is required to preview popup content.</div>';
            }
            ?>
        </div>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>