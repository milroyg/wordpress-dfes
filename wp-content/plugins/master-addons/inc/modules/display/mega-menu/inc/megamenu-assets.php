<?php

namespace MasterAddons\Modules\Display\MegaMenu;

defined('ABSPATH') || exit;

class Megamenu_Assets
{

    private static $_instance = null;

    public function __construct()
    {

        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_js']);
        add_action('admin_enqueue_scripts', [$this, 'admin_js']);

        // NOTE: Atomic Widgets CSS for mega menu content posts is registered by the
        // nav walker itself (Megamenu_Nav_Walker::end_el -> queue_megamenu_styles),
        // in the exact scope of each rendered mega menu item. The previous global
        // wp_enqueue_scripts scan that walked every menu location on every request
        // was removed -- it re-discovered what the walker already knows and ran even
        // on pages with no mega menu.
    }

    public function common_js()
    {
        ob_start();
?>
        var masteraddons = {
        resturl: '<?php echo esc_url( get_rest_url() . 'masteraddons/v2/' ); ?>',
        }
<?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }



    // Frontend Scripts
    public function frontend_js()
    {
        $add_inline_script = $this->common_js();
        wp_add_inline_script('mega-menu-nav-menu', $add_inline_script);
    }


    public function admin_enqueue_scripts()
    {

        $screen = get_current_screen();

        if ($screen->base == 'nav-menus') {

            // Stylesheets
            wp_enqueue_style('wp-color-picker');

            // Font Awesome (registered by Assets_Manager with global handle)
            wp_enqueue_style('font-awesome-5-all-css');

            // Enqueue icon libraries using Icon Library Helper
            if (class_exists('\MasterAddons\Inc\Admin\WidgetBuilder\Icon_Library_Helper')) {
                $icon_helper = \MasterAddons\Inc\Admin\WidgetBuilder\Icon_Library_Helper::get_instance();
                $icon_helper->enqueue_icon_libraries();
            }

            // Styles and Scripts (registered by Assets_Loader)
            wp_enqueue_style('master-addons-megamenu');

            // Add wp-color-picker dependency for mega menu script
            global $wp_scripts;
            if (isset($wp_scripts->registered['master-addons-mega-script'])) {
                $wp_scripts->registered['master-addons-mega-script']->deps[] = 'wp-color-picker';
            }
            wp_enqueue_script('master-addons-mega-script');

            // Localize icon library data for mega menu
            $icon_config = [];
            if (class_exists('\MasterAddons\Inc\Admin\WidgetBuilder\Icon_Library_Helper')) {
                $icon_helper = \MasterAddons\Inc\Admin\WidgetBuilder\Icon_Library_Helper::get_instance();
                $icon_config = $icon_helper->get_icon_library_config();
            }

            // Localize Scripts
            $localize_menu_data = array(
                'resturl'       => get_rest_url() . 'masteraddons/v2/',
                'iconLibrary'   => $icon_config,
                'pluginUrl'     => JLTMA_URL,
                'nonce'         => wp_create_nonce('jltma_megamenu_nonce')
            );
            wp_localize_script('master-addons-mega-script', 'masteraddons_megamenu', $localize_menu_data);
        }
    }


    // Admin Rest API Variable
    public function admin_js()
    {
        // Output the REST var through the enqueue API (inline-only handle) instead of a
        // hardcoded <script> tag.
        wp_register_script('jltma-megamenu-vars', false, array(), JLTMA_VER, false);
        wp_enqueue_script('jltma-megamenu-vars');
        wp_add_inline_script('jltma-megamenu-vars', $this->common_js());
    }


    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

Megamenu_Assets::get_instance();


// // Mega Menu
if (!function_exists('jltma_megamenu_assets')) {
    function jltma_megamenu_assets()
    {
        return Megamenu_Assets::get_instance();
    }
}

jltma_megamenu_assets();
