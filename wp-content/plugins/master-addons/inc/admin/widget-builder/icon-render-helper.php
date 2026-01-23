<?php
/**
 * Helper for rendering Elementor Icons in widgets
 *
 * Usage in widget render method:
 * \Elementor\Icons_Manager::render_icon( $settings['icon_field_name'], [ 'aria-hidden' => 'true' ] );
 */

// Example of how to properly render an icon in the widget's render_widget_content method:
/*
protected function render_widget_content($settings) {
    ?>
    <div class="icon-holder">
        <?php
        // Check if icon is set
        if ( ! empty( $settings['jltma_advanced_icons_28']['value'] ) ) {
            \Elementor\Icons_Manager::render_icon( $settings['jltma_advanced_icons_28'], [ 'aria-hidden' => 'true' ] );
        }
        ?>
    </div>
    <?php
}
*/

// Alternative method using the icon HTML directly:
/*
protected function render_widget_content($settings) {
    ?>
    <div class="icon-holder">
        <?php
        // Check if icon is set
        if ( ! empty( $settings['jltma_advanced_icons_28']['value'] ) ) {
            // For Font Awesome or other icon fonts
            if ( ! empty( $settings['jltma_advanced_icons_28']['library'] ) ) {
                echo '<i class="' . esc_attr( $settings['jltma_advanced_icons_28']['value'] ) . '" aria-hidden="true"></i>';
            }
        }
        ?>
    </div>
    <?php
}
*/