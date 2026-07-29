<?php
/**
 * Master Addons Popup Editor Template
 *
 * @package MasterAddons
 * @subpackage PopupBuilder
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Elementor Instance
$elementor_plugin = \Elementor\Plugin::$instance;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <?php if (!current_theme_supports('title-tag')) : ?>
        <title><?php echo esc_html(wp_get_document_title()); ?></title>
    <?php endif; ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <div class="jltma-template-popup">
        <div class="jltma-template-popup-inner">

            <!-- Popup Overlay & Close Button -->
            <div class="jltma-popup-overlay"></div>

            <!-- Template Container -->
            <div class="jltma-popup-container">

                <!-- Popup Close Button -->
                <div class="jltma-popup-close-btn"><i class="eicon-close"></i></div>

                <div class="jltma-popup-container-inner">
                    <?php $elementor_plugin->modules_manager->get_modules('page-templates')->print_content(); ?>
                </div>

            </div>

        </div>
    </div>

    <?php wp_footer(); ?>

</body>
</html>
