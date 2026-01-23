<?php

namespace MasterAddons\Modules\MegaMenu;

defined('ABSPATH') || exit;

class JLTMA_Megamenu_Assets
{

    private static $_instance = null;

    public function __construct()
    {

        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_js']);
        add_action('admin_print_scripts', [$this, 'admin_js']);
    }

    public function common_js()
    {
        ob_start();
?>
        var masteraddons = {
        resturl: '<?php echo get_rest_url() . 'masteraddons/v2/'; ?>',
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

            // Enqueue Font Awesome
            $fa_url = '';
            if (defined('ELEMENTOR_ASSETS_URL')) {
                $fa_url = ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css';
            }

            // Always register and enqueue Font Awesome
            if (!empty($fa_url)) {
                wp_register_style('jltma-font-awesome', $fa_url, [], JLTMA_VER);
                wp_enqueue_style('jltma-font-awesome');
            } else {
                // Fallback to CDN if Elementor not available
                wp_register_style(
                    'jltma-font-awesome',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                    [],
                    '6.5.1'
                );
                wp_enqueue_style('jltma-font-awesome');
            }

            // Enqueue icon libraries using Icon Library Helper
            if (class_exists('\MasterAddons\Admin\WidgetBuilder\Icon_Library_Helper')) {
                $icon_helper = \MasterAddons\Admin\WidgetBuilder\Icon_Library_Helper::get_instance();
                $icon_helper->enqueue_icon_libraries();
            }

            wp_enqueue_style('mega-menu-style', JLTMA_URL . '/assets/megamenu/css/megamenu.css');

            // Scripts
            wp_enqueue_script('mega-menu-admin', JLTMA_URL . '/assets/megamenu/js/mega-script.js', array('jquery', 'wp-color-picker'), JLTMA_VER, true);

            // Localize icon library data for mega menu
            $icon_config = [];
            if (class_exists('\MasterAddons\Admin\WidgetBuilder\Icon_Library_Helper')) {
                $icon_helper = \MasterAddons\Admin\WidgetBuilder\Icon_Library_Helper::get_instance();
                $icon_config = $icon_helper->get_icon_library_config();
            }

            // Localize Scripts
            $localize_menu_data = array(
                'resturl'       => get_rest_url() . 'masteraddons/v2/',
                'iconLibrary'   => $icon_config,
                'pluginUrl'     => JLTMA_URL
            );
            wp_localize_script('mega-menu-admin', 'masteraddons_megamenu', $localize_menu_data);
        }
    }


    // Admin Rest API Variable
    public function admin_js()
    {
        echo "<script type='text/javascript'>\n";
        echo $this->common_js();
        echo "\n</script>";
    }


    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

JLTMA_Megamenu_Assets::get_instance();


// // Mega Menu
if (!function_exists('jltma_megamenu_assets')) {
    function jltma_megamenu_assets()
    {
        return JLTMA_Megamenu_Assets::get_instance();
    }
}

jltma_megamenu_assets();
