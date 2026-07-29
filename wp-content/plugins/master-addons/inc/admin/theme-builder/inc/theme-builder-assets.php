<?php

namespace MasterAddons\Inc\Admin\Theme_Builder;

defined('ABSPATH') || exit;

class Assets
{

    private static $_instance = null;

    public function __construct()
    {

        add_action('admin_enqueue_scripts', [$this, 'jltma_admin_js']);

        // enqueue scripts
        add_action('admin_enqueue_scripts', [$this, 'jltma_header_footer_enqueue_scripts']);
    }

    // Declare Variable for Rest API
    public function jltma_admin_js()
    {
        // Output the REST var through the enqueue API (inline-only handle) instead of a
        // hardcoded <script> tag.
        wp_register_script('jltma-theme-builder-vars', false, array(), JLTMA_VER, false);
        wp_enqueue_script('jltma-theme-builder-vars');
        wp_add_inline_script('jltma-theme-builder-vars', $this->jltma_common_js());
    }


    public function jltma_common_js()
    {
        ob_start(); ?>
        var masteraddons = { resturl: '<?php echo esc_url( get_rest_url() . 'masteraddons/v2/' ); ?>', }
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function jltma_header_footer_enqueue_scripts()
    {

        $screen = get_current_screen();

        if ($screen->id == 'edit-master_template') {
            // Theme Builder assets (registered by Assets_Manager, includes select2 as dependency)
            wp_enqueue_style('jltma-theme-builder');
            wp_enqueue_script('jltma-theme-builder');

            // Localize Scripts
            $jltma_localize_hfc_data = array(
                'plugin_url'    => \JLTMA_URL,
                'ajaxurl'       => admin_url('admin-ajax.php'),
                'resturl'       => get_rest_url() . 'masteraddons/v2/',
                'ajax_nonce'    => wp_create_nonce('jltma_frontend_ajax_nonce'),
                'rest_nonce'    => wp_create_nonce('wp_rest'),
                'woocommerce_active' => class_exists('WooCommerce'),
                'upgrade_pro'   => /* translators: %s: Upgrade Pro Link. */ sprintf(__('<a href="%1$s" target="_blank">Upgrade to Pro</a> unlock this feature. <a href="%1$s" target="_blank">Upgrade Now</a>', 'master-addons' ), esc_url(apply_filters('master_addons/upgrade_url', 'https://master-addons.com/pricing/')))
            );
            wp_localize_script('jltma-theme-builder', 'masteraddons', $jltma_localize_hfc_data);

            // JLTMACORE for premium check (used by theme-builder.js)
            wp_localize_script('jltma-theme-builder', 'JLTMACORE', array(
                'admin_ajax'      => admin_url('admin-ajax.php'),
                'is_premium'      => apply_filters('master_addons/is_premium', false),
                'pro_conditions'  => apply_filters('master_addons/theme_builder/pro_conditions', false),
            ));
        }
    }


    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

Assets::get_instance();