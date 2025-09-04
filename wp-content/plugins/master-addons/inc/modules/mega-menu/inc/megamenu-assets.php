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
            // $jltma_get_icons_library_settings = get_option('jltma_icons_library_save_settings', []);

            $megamenu_icons = [
                'linecons' =>[
                    'enqueue'       => [JLTMA_ASSETS . 'fonts/simple-line-icons/simple-line-icons.css'],
                ],
                'fa4' =>[
                    'enqueue'       => ['https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css']
                ],
                'fab' =>[
                    'enqueue'       => ['https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/brands.min.css']
                ],
                'far' => [
                    'enqueue'       => ['https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/regular.min.css']
                ],
                'fas' => [
                    'enqueue'       => ['https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/solid.min.css']
                ],
                'Genericons' =>[
                    'enqueue'       => ['https://cdnjs.cloudflare.com/ajax/libs/genericons/3.1/genericons.min.css']
                ],
                'Linearicons' =>[
                    'enqueue'       => [JLTMA_ASSETS . 'fonts/linear-icons/linear-icons.css']
                ],
                'Icomoon' => [
                    'enqueue_need'       => ['https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css']
                ],
                'Themify' => [
                    'enqueue_need'       => ['https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css']
                ]
            ];
            foreach($megamenu_icons as $key => $iconfont ){
                if(array_key_exists('enqueue',$iconfont )){
                    foreach( $iconfont['enqueue'] as $number => $file){
                        wp_enqueue_style('mega-menu-font-' .$key . '-' . $number, $file);
                    }
                }
            }

            wp_enqueue_style('mega-menu-style', JLTMA_URL . '/assets/megamenu/css/megamenu.css');
            // Scripts
            wp_enqueue_script('icon-picker', JLTMA_URL . '/assets/megamenu/js/icon-picker.js', array('jquery'), JLTMA_VER, true);
            wp_enqueue_script('mega-menu-admin', JLTMA_URL . '/assets/megamenu/js/mega-script.js', array('jquery', 'wp-color-picker', 'icon-picker'), JLTMA_VER, true);


            // Localize Scripts
            $localize_menu_data = array(
                'resturl'       => get_rest_url() . 'masteraddons/v2/'
            );
            wp_localize_script('mega-menu-admin', 'masteraddons', $localize_menu_data);
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
