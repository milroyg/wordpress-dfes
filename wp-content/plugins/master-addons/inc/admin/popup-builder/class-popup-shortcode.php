<?php
/**
 * Master Addons Popup Shortcode Handler
 *
 * @package MasterAddons
 * @subpackage PopupBuilder
 */

namespace MasterAddons\Inc\Admin\PopupBuilder;

use MasterAddons\Inc\Classes\Assets_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class Popup_Shortcode {

    private static $instance = null;
    private $queued_popups = [];

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_shortcode('jltma_popup', [$this, 'render_popup_shortcode']);
        add_action('wp_footer', [$this, 'render_queued_popups'], 15);
    }

    /**
     * Render popup shortcode
     * Usage: [jltma_popup id="123"]
     */
    public function render_popup_shortcode($atts) {
        $atts = shortcode_atts([
            'id' => '',
        ], $atts, 'jltma_popup');

        if (empty($atts['id'])) {
            return '';
        }

        $popup_id = intval($atts['id']);
        $popup = get_post($popup_id);

        if (!$popup || $popup->post_type !== 'jltma_popup' || $popup->post_status !== 'publish') {
            return '';
        }

        // Respect the popup activation toggle. An inactive popup must never render,
        // even when explicitly placed via shortcode. Only display conditions are bypassed.
        if (get_post_meta($popup_id, '_jltma_popup_activation', true) !== 'yes') {
            return '';
        }

        // Queue this popup for footer rendering (bypasses conditions since user explicitly placed it)
        $this->queued_popups[$popup_id] = true;

        // Enqueue frontend assets
        Assets_Manager::enqueue('popup-builder-frontend');

        return '';
    }

    /**
     * Render queued popups in footer
     * Reuses the same HTML structure as Popup_Frontend::render_single_popup()
     */
    public function render_queued_popups() {
        if (empty($this->queued_popups)) {
            return;
        }

        foreach (array_keys($this->queued_popups) as $popup_id) {
            // Skip if already rendered by Popup_Frontend
            if (in_array($popup_id, Popup_Frontend::get_instance()->get_displayed_popups())) {
                continue;
            }

            $this->render_single_popup($popup_id);
        }
    }

    /**
     * Render a single popup HTML
     */
    private function render_single_popup($popup_id) {
        if (!defined('ELEMENTOR_VERSION')) {
            return;
        }

        $elementor_content = \Elementor\Plugin::instance()->frontend->get_builder_content($popup_id, false);

        if (empty($elementor_content)) {
            return;
        }

        // Get popup settings from Elementor page settings
        $elementor_settings = get_post_meta($popup_id, '_elementor_page_settings', true);
        if (empty($elementor_settings)) {
            $elementor_settings = [];
        }

        $popup_settings = [
            'popup_trigger' => $elementor_settings['popup_trigger'] ?? 'page-load',
            'popup_load_delay' => $elementor_settings['popup_load_delay'] ?? 1,
            'popup_scroll_progress' => $elementor_settings['popup_scroll_progress'] ?? 50,
            'popup_element_scroll' => $elementor_settings['popup_element_scroll'] ?? '',
            'popup_specific_date' => $elementor_settings['popup_specific_date'] ?? '',
            'popup_inactivity_time' => $elementor_settings['popup_inactivity_time'] ?? 15,
            'popup_custom_trigger' => $elementor_settings['popup_custom_trigger'] ?? '',
            'popup_click_trigger' => $elementor_settings['popup_click_trigger'] ?? '',
            'popup_display_as' => $elementor_settings['popup_display_as'] ?? 'modal',
            'popup_position' => $elementor_settings['popup_position'] ?? 'center-center',
            'popup_animation' => $elementor_settings['popup_animation'] ?? 'jltma-anim-fade-in',
            'popup_animation_duration' => $elementor_settings['popup_animation_duration'] ?? 400,
            'popup_custom_positioning' => $elementor_settings['popup_custom_positioning'] ?? '',
            'popup_show_close_button' => $elementor_settings['popup_show_close_button'] ?? 'yes',
            'popup_close_button_position' => $elementor_settings['popup_close_button_position'] ?? 'top-right',
            'popup_close_button_display_delay' => $elementor_settings['popup_close_button_display_delay'] ?? 0,
            'popup_automatic_close_switch' => $elementor_settings['popup_automatic_close_switch'] ?? 'no',
            'popup_automatic_close_delay' => $elementor_settings['popup_automatic_close_delay'] ?? 10,
            'popup_close_on_overlay' => $elementor_settings['popup_close_on_overlay'] ?? 'yes',
            'popup_close_esc_key' => $elementor_settings['popup_close_esc_key'] ?? 'yes',
            'popup_disable_page_scroll' => $elementor_settings['popup_disable_page_scroll'] ?? 'yes',
            'popup_show_again_delay' => $elementor_settings['popup_show_again_delay'] ?? 'no-delay',
            'popup_show_on_device' => $elementor_settings['popup_show_on_device'] ?? 'yes',
            'popup_show_on_device_tablet' => $elementor_settings['popup_show_on_device_tablet'] ?? 'yes',
            'popup_show_on_device_mobile' => $elementor_settings['popup_show_on_device_mobile'] ?? 'yes',
            'popup_stop_after_date' => $elementor_settings['popup_stop_after_date'] ?? 'no',
            'popup_stop_after_date_select' => $elementor_settings['popup_stop_after_date_select'] ?? '',
            'popup_show_via_referral' => $elementor_settings['popup_show_via_referral'] ?? 'no',
            'popup_referral_keyword' => $elementor_settings['popup_referral_keyword'] ?? '',
            'popup_disable_automatic' => $elementor_settings['popup_disable_automatic'] ?? 'no',
            'popup_disable_after' => $elementor_settings['popup_disable_after'] ?? '',
        ];

        $position_class = 'jltma-popup-position-' . str_replace('_', '-', $popup_settings['popup_position']);

        if (!empty($elementor_settings['popup_custom_positioning']) && $elementor_settings['popup_custom_positioning'] === 'yes') {
            $position_class .= ' jltma-popup-custom-position';
        }

        $encoded_settings = wp_json_encode($popup_settings);

        if (\Elementor\Plugin::instance()->preview->is_preview_mode()) {
            return;
        }

        ?>
        <div id="jltma-popup-id-<?php echo esc_attr($popup_id); ?>" class="jltma-template-popup <?php echo esc_attr($position_class); ?>" data-settings='<?php echo esc_attr($encoded_settings); ?>'>
            <div class="jltma-template-popup-inner">
                <div class="jltma-popup-overlay"></div>
                <div class="jltma-popup-container">
                    <?php
                    $show_close_button = !isset($elementor_settings['popup_show_close_button']) || $elementor_settings['popup_show_close_button'] === 'yes';
                    if ($show_close_button) :
                    ?>
                        <div class="jltma-popup-close-btn jltma-popup-close-<?php echo esc_attr($elementor_settings['popup_close_button_position'] ?? 'top-right'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </div>
                    <?php endif; ?>
                    <div class="jltma-popup-container-inner">
                        <?php echo $elementor_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
