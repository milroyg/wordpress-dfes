<?php

namespace MasterAddons\Inc\Admin\Templates\Widgets;

use MasterAddons\Inc\Classes\Helper;

defined('ABSPATH') || exit;

/**
 * Widgets Library admin screen.
 */
class Widgets_Library
{
    private static $_instance = null;

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu'], 19);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('in_admin_header', [$this, 'suppress_admin_notices'], 1000);
    }

    /**
     * Strip third-party notices from the Widgets Library screen.
     *
     * The app renders a full-bleed layout of its own, and unrelated plugin
     * notices push it down and break the header. Runs late on in_admin_header,
     * which is after notices are registered but before any are printed.
     */
    public function suppress_admin_notices()
    {
        if (!$this->is_library_screen()) {
            return;
        }

        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        remove_all_actions('network_admin_notices');
        remove_all_actions('user_admin_notices');
    }

    private function is_library_screen()
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        return $screen && false !== strpos($screen->id, 'jltma-widgets-library');
    }

    public function add_menu()
    {
        add_submenu_page(
            'master-addons-settings',
            __('Widgets Library', 'master-addons'),
            __('Widgets Library', 'master-addons'),
            'manage_options',
            'jltma-widgets-library',
            [$this, 'render_page']
        );
    }

    public function enqueue_assets($hook)
    {
        if (strpos($hook, 'jltma-widgets-library') === false) {
            return;
        }

        wp_enqueue_style('jltma-page-importer');
        wp_enqueue_style('jltma-widgets-library-app');
        wp_enqueue_script('jltma-widgets-library-app');

        wp_localize_script('jltma-widgets-library-app', 'JLTMAWidgetsLibrary', [
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce(Widgets_Ajax::NONCE_ACTION),
            'is_pro'    => Helper::jltma_premium(),
            'pluginUrl' => JLTMA_URL,
            'strings'   => [
                'importSuccess'     => __('Widget imported successfully!', 'master-addons'),
                'importError'       => __('Failed to import widget.', 'master-addons'),
                'previewTemplate'   => __('Preview', 'master-addons'),
                'importTemplate'    => __('Import', 'master-addons'),
                'searchPlaceholder' => __('Search widgets...', 'master-addons'),
            ],
        ]);
    }

    public function render_page()
    {
        // Mounted bare, like the Template Library root — the app supplies its
        // own full-bleed layout and a .wrap would add admin margins on top.
        echo '<div id="jltma-widgets-library-app"></div>';
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
