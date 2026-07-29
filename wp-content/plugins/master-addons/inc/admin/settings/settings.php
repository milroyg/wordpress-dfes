<?php

namespace MasterAddons\Inc\Admin\Settings;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use MasterAddons\Inc\Admin\Config;
use MasterAddons\Inc\Admin\REST_API;
use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Classes\Utils;
use MasterAddons\Inc\Classes\Recommended_Plugins;

/**
 * Centralized Settings Manager for Master Addons
 *
 * Single source of truth for all plugin settings: addons, extensions, third-party
 * plugins, icon libraries, and API keys. Wraps WordPress options (wp_options table)
 * with caching, legacy-key migration, input sanitization, and a fluent query API.
 *
 * Architecture:
 *   Settings (singleton) → SettingsProxy (per-group) → SettingsGroup (per-sub-group)
 *
 * Option keys (wp_options):
 *   jltma_addons      – addon toggle map         {addon_key: 0|1}
 *   jltma_extensions   – extension toggle map     {ext_key: 0|1}
 *   jltma_plugins      – third-party plugin map   {plugin_key: 0|1}
 *   jltma_icons        – icon-library toggle map  {icon_key: 0|1}
 *   jltma_api          – API credentials          {service_key: string}
 *
 * Usage examples:
 *
 * 1. Instance chaining (recommended for multiple reads):
 *    $s = Settings::instance();
 *    $s->addons->get('accordion');          // single value
 *    $s->addons->get();                     // full array
 *    $s->addons->is_enabled('accordion');   // bool
 *    $s->addons->enabled();                 // all enabled with Config data
 *    $s->addons->group('basic')->all();     // Config items in sub-group
 *    $s->addons->group('basic')->enabled(); // enabled items in sub-group
 *    $s->addons->group('basic')->counts();  // ['total' => n, 'enabled' => n]
 *    $s->addons->group('basic')->enable();  // bulk-enable sub-group
 *
 * 2. Global helper function:
 *    jltma_settings()->addons->get('accordion');
 *    jltma_settings()->api->get('google_maps_api_key');
 *    jltma_settings()->get('jltma_white_label_settings');              // any wp_option
 *    jltma_settings()->get('jltma_white_label_settings', 'sub_key');   // sub-key access
 *
 * 3. Static facade shorthand (one-liner reads):
 *    Settings::addons('accordion');          // = instance()->addons->get('accordion')
 *    Settings::api('google_maps_api_key');   // = instance()->api->get(...)
 *
 * 4. Classic static methods (backward-compatible):
 *    Settings::get_addons('key');
 *    Settings::is_addon_enabled('key');
 *    Settings::save_addons($array);
 *    Settings::get_enabled_addons_by_group('basic');
 *
 * Security:
 *   - Option keys validated against known constants before any DB read/write
 *   - Toggle maps sanitized to integer 0|1; API values run through sanitize_text_field()
 *   - All array keys sanitized with sanitize_key()
 *
 * @package MasterAddons\Inc\Admin\Settings
 * @since   2.0.0
 */
class Settings
{
    /**
     * Option key constants
     */
    const ADDONS_KEY     = 'jltma_addons';
    const EXTENSIONS_KEY = 'jltma_extensions';
    const PLUGINS_KEY    = 'jltma_plugins';
    const ICONS_KEY      = 'jltma_icons';
    const API_KEY        = 'jltma_api';
    const WHITE_LABEL    = 'jltma_white_label_settings';
    const VERSION_KEY    = '_master_addons_version';

    /**
     * Group name → option key map (used by __get and __callStatic)
     */
    const PROXY_MAP = [
        'addons'     => self::ADDONS_KEY,
        'extensions' => self::EXTENSIONS_KEY,
        'plugins'    => self::PLUGINS_KEY,
        'icons'      => self::ICONS_KEY,
        'api'        => self::API_KEY,
    ];

    /**
     * Legacy option keys for migration
     */
    const LEGACY_KEYS = [
        'maad_el_save_settings'             => self::ADDONS_KEY,
        'ma_el_extensions_save_settings'    => self::EXTENSIONS_KEY,
        'ma_el_third_party_plugins_save_settings' => self::PLUGINS_KEY,
        'jltma_icons_library_save_settings' => self::ICONS_KEY,
        'jltma_api_save_settings'           => self::API_KEY,
    ];

    /**
     * Cache for settings
     */
    private static $cache = [];


	public function __construct()
	{
        add_action('admin_menu', [$this, 'register_admin_menu'], 10);
        add_action('network_admin_menu', [$this, 'register_admin_menu'], 10);
        add_action('current_screen', [$this, 'jltma_set_page_title']);
        add_action('admin_enqueue_scripts', [$this, 'jltma_admin_settings_scripts'], 99);
        add_action('admin_body_class', [$this, 'jltma_admin_body_class']);
        add_action('admin_enqueue_scripts', [$this, 'jltma_global_admin_css']);
        add_action('wp_ajax_jltma_subscribe', [$this, 'handle_subscribe_ajax']);
    }

    /**
     * Set page title for MA admin pages before admin-header.php runs.
     * Freemius may override menu registration, leaving global $title unset.
     */
    public function jltma_set_page_title($screen)
    {
        global $title;
        if (!empty($title)) {
            return;
        }
        if (strpos($screen->id, 'master-addons-setup-wizard') !== false) {
            $title = __('Setup Wizard', 'master-addons');
        } elseif (strpos($screen->id, 'master-addons-settings') !== false) {
            $title = apply_filters('master_addons/white_label/page_title', __('Master Addons for Elementor', 'master-addons'));
        }
    }

    /**
     * Handle subscribe AJAX request
     * Delegates to the Subscribe notification class
     */
    public function handle_subscribe_ajax()
    {
        $subscribe = new \MasterAddons\Inc\Classes\Notifications\Subscribe();
        $subscribe->jltma_subscribe();
    }


    /**
     * Global admin CSS for menu styling (runs on all admin pages)
     */
    public function jltma_global_admin_css()
    {
        $css = '
            /* Hide separator below Master Addons menu */
            #adminmenu #toplevel_page_master-addons-settings + .wp-menu-separator {
                display: none !important;
            }
            #toplevel_page_master-addons-settings {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }

            /* Strip ALL borders from submenu items first */
            #toplevel_page_master-addons-settings .wp-submenu li {
                border: none !important;
            }

            /* Then add back only our separators */
            #toplevel_page_master-addons-settings .wp-submenu li.jltma-menu-separator {
                border-bottom: 1px solid hsla(0, 0%, 100%, 0.12) !important;
                padding-bottom: 6px;
                margin-bottom: 6px;
            }


            /* Pricing submenu - full-width green button like Spectra */
            #toplevel_page_master-addons-settings .wp-submenu li.jltma-menu-pricing a {
                background: #ffa500 !important;
                font-weight: 600 !important;
                padding: 8px 12px !important;
            }
            #toplevel_page_master-addons-settings .wp-submenu li.jltma-menu-pricing a span {
                color: #000 !important;
            }
            #toplevel_page_master-addons-settings .wp-submenu li.jltma-menu-pricing a:hover {
                color: #fff !important;
                background: #16a34a !important;
            }
            #toplevel_page_master-addons-settings .wp-submenu li.jltma-menu-pricing a:hover span {
                color: #fff !important;
            }
            /* ── CPT List Page: Injected Buttons (next to native WP "Add New") ── */
            .wrap .jltma-cpt-btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 1px 12px;
                font-size: 13px;
                font-weight: 600;
                line-height: 2.15384615;
                min-height: 28px;
                text-decoration: none;
                cursor: pointer;
                white-space: nowrap;
                border-radius: 3px;
                box-sizing: content-box;
                vertical-align: baseline;
                margin-left: 8px;
            }
            .wrap .jltma-cpt-btn svg {
                flex-shrink: 0;
            }
            .wrap .jltma-cpt-btn-secondary {
                background: linear-gradient(135deg, rgb(153, 41, 234) 0%, rgb(88, 8, 251) 100%);
                color: #fff;
                border: none;
                transition: opacity 0.15s ease;
            }
            .wrap .jltma-cpt-btn-secondary:hover,
            .wrap .jltma-cpt-btn-secondary:focus {
                opacity: 0.88;
                color: #fff;
            }
            .wrap .jltma-cpt-btn-youtube {
                background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
                color: #cc0000;
                border: 1px solid #fecaca;
                transition: border-color 0.15s ease, background 0.15s ease;
            }
            .wrap .jltma-cpt-btn-youtube:hover,
            .wrap .jltma-cpt-btn-youtube:focus {
                background: linear-gradient(135deg, #ffe0e0 0%, #fecaca 100%);
                border-color: #f87171;
                color: #b91c1c;
            }
            .wrap .jltma-cpt-btn-youtube svg {
                fill: #ff0000;
            }
        ';

        // JS to reorder submenu items and add separator/pricing classes
        $js = '
            document.addEventListener("DOMContentLoaded", function() {
                var menu = document.getElementById("toplevel_page_master-addons-settings");
                if (!menu) return;
                var submenu = menu.querySelector(".wp-submenu");
                if (!submenu) return;

                // Reorder: Move "Template Kits" right after "Template Library"
                var items = submenu.querySelectorAll("li a");
                var libraryItem = null, kitsItem = null, wizardItem = null, recommendedItem = null;
                items.forEach(function(a) {
                    var href = a.getAttribute("href") || "";
                    if (href.indexOf("jltma-template-library") !== -1) libraryItem = a.parentElement;
                    if (href.indexOf("jltma-template-kits") !== -1) kitsItem = a.parentElement;
                    if (href.indexOf("master-addons-setup-wizard") !== -1) wizardItem = a.parentElement;
                    if (href.indexOf("master-addons-recommended") !== -1) recommendedItem = a.parentElement;
                });
                // Place Template Kits right after Template Library
                if (libraryItem && kitsItem && libraryItem.nextElementSibling !== kitsItem) {
                    submenu.insertBefore(kitsItem, libraryItem.nextElementSibling);
                }
                // Move "Setup Wizard" just before "Recommended"
                if (wizardItem && recommendedItem) {
                    submenu.insertBefore(wizardItem, recommendedItem);
                }

                // Separators and pricing
                var separatorAfter = [
                    "page=master-addons-settings",
                    "jltma-template-kits",
                    "post_type=jltma_widget"
                ];
                submenu.querySelectorAll("li a").forEach(function(a) {
                    var href = a.getAttribute("href") || "";
                    for (var i = 0; i < separatorAfter.length; i++) {
                        var pos = href.indexOf(separatorAfter[i]);
                        if (pos === -1) { continue; }
                        // Require a param boundary after the match so
                        // "page=master-addons-settings" does NOT also match
                        // "page=master-addons-settings-account" (the Account item).
                        var nextChar = href.charAt(pos + separatorAfter[i].length);
                        if (nextChar === "" || nextChar === "&" || nextChar === "#" || nextChar === "\"") {
                            a.parentElement.classList.add("jltma-menu-separator");
                            break;
                        }
                    }
                    var text = (a.textContent || "").trim().toLowerCase();
                    if (href.indexOf("pricing") !== -1 || text.indexOf("pricing") !== -1 || text.indexOf("upgrade") !== -1) {
                        a.parentElement.classList.add("jltma-menu-pricing");
                    }
                });
            });
        ';

        // Load the menu styling and ordering through the core enqueue APIs
        // (inline-only handles) instead of hardcoded <style>/<script> tags.
        wp_register_style('jltma-admin-menu', false, array(), JLTMA_VER);
        wp_enqueue_style('jltma-admin-menu');
        wp_add_inline_style('jltma-admin-menu', $css);

        wp_register_script('jltma-admin-menu', false, array(), JLTMA_VER, true);
        wp_enqueue_script('jltma-admin-menu');
        wp_add_inline_script('jltma-admin-menu', $js);
    }

	/**
	 * Admin Body Class
	 */
	public function jltma_admin_body_class($class)
	{
		$bodyclass = '';
		$bodyclass .= ' jltma-admin ';
		return $class . $bodyclass;
	}


    public function jltma_admin_settings_scripts() {
        $screen = get_current_screen(); 
        
        $jltma_menu_label = __('Master Addons', 'master-addons');
        $menu_label = apply_filters('master_addons/white_label/menu_label', $jltma_menu_label);
        
        // Check if we're on any Master Addons admin page
		$is_master_addons_page = (
			$screen->id == 'toplevel_page_master-addons-settings' ||
			$screen->id == 'toplevel_page_master-addons-settings-network' ||
			// strpos($screen->id, 'master-addons_page_') === 0 ||
			$screen->id === strtolower(preg_replace('/\s+/', '-', trim($menu_label))) . '_page_master-addons-settings' 
			// || (isset($screen->parent_base) && $screen->parent_base === 'master-addons-settings')
		);

        // Setup Wizard page
        $is_setup_wizard_page = (
            strpos($screen->id, 'master-addons-setup-wizard') !== false
        );
		
		// Load Scripts only Master Addons Admin Page
		if ($is_master_addons_page && empty( $is_setup_wizard_page )) {

            // Hide all admin notices on settings page
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('network_admin_notices');
            remove_all_actions('user_admin_notices');
            remove_action('admin_notices', 'update_nag', 3);
            remove_action('admin_notices', 'maintenance_nag', 10);

            // Hide any remaining admin notices on our settings screen. Attached to the
            // settings stylesheet (enqueued below) instead of a hardcoded <style> tag.
            $jltma_hide_notices_css = '
                    .notice, .error, .updated, .update-nag, .admin-notice,
                    .jltma-plugin-update-notice, .fs-notice, .fs-slug-master-addons,
                    #wpbody-content > .notice, #wpbody-content > .error, #wpbody-content > .updated,
                    .wrap > .notice, .wrap > .error, .wrap > .updated {
                        display: none !important;
                    }
                    #wpcontent {
                        padding-left: 0 !important;
                    }
                    #wpfooter {
                        display: none !important;
                    }';

            // Dequeue Spectra (UAG) zipwp-images style to prevent CSS conflicts on settings page
            add_action('admin_enqueue_scripts', function () {
                wp_dequeue_style('zipwp-images-style');
            }, 9999);

            if (!did_action('wp_enqueue_media')) {
				wp_enqueue_media();
			}

            // Elementor icons for addon card icons — use Elementor's copy if available, otherwise local fallback
            if (wp_style_is('elementor-icons', 'registered')) {
                wp_enqueue_style('elementor-icons');
            } else {
                wp_enqueue_style('jltma-elementor-icons');
            }

            // Only load React app on the React settings page to avoid Lucide icons conflicting with native Image constructor
            wp_enqueue_style('jltma-admin-settings');
            wp_enqueue_script('jltma-admin-settings');

            wp_add_inline_style('jltma-admin-settings', $jltma_hide_notices_css);

            // White Label logo & Hidden Nav Menus
            $white_label_settings = jltma_settings()->get('jltma_white_label_settings');
            $white_label_settings = is_array($white_label_settings) ? $white_label_settings : [];
            $white_label_logo  = '';

            // White Label Logo
            if( !empty(Utils::check_options($white_label_settings['jltma_wl_plugin_logo'] ?? '') ) ) {
				$white_label_logo = wp_get_attachment_image_src($white_label_settings['jltma_wl_plugin_logo'])[0];
			}

            // Hidden Nav Menus — keys must match nav_menus.ts ids
            $hidden_menus = [];
            $tab_keys = [
                'welcome'       => 'jltma_wl_plugin_tab_welcome',
                'addons'        => 'jltma_wl_plugin_tab_addons',
                'extensions'    => 'jltma_wl_plugin_tab_extensions',
                'tools'         => 'jltma_wl_plugin_tab_tools',
                'free_vs_pro'   => 'jltma_wl_plugin_tab_free_vs_pro',
                'white_label'   => 'jltma_wl_plugin_tab_white_label',
                'template_kits' => 'jltma_wl_plugin_tab_template_kits',
            ];
            foreach ($tab_keys as $menu_id => $db_key) {
                $hidden_menus[$menu_id] = !empty($white_label_settings[$db_key]) ? true : false;
            }

            $current_user = wp_get_current_user();

            $localize_data = [
                'pluginSlug' => 'master-addons',
                'restUrl'    => rest_url('master-addons/v1'),
                'nonce'      => wp_create_nonce('wp_rest'),
                'adminUrl'   => admin_url(),
                'assetsUrl'  => JLTMA_ASSETS,
                'logo'       => array(
                    'light' => $white_label_logo ? $white_label_logo : JLTMA_ASSETS . 'images/full-logo.svg',
                    'dark'  => JLTMA_ASSETS . 'images/full-logo.png',
                ),
                'darkMode'     => 'light',
                'data'         => Config::get_config(),
                'is_premium'   => ma_el_fs()->can_use_premium_code__premium_only(),
                'is_developer' => ma_el_fs()->is_plan__premium_only('developer'),
                'hidden_menus'        => $hidden_menus,
                'is_setup_complete'   => REST_API::is_setup_complete(),
                'user_email'          => $current_user->user_email,
                'user_name'           => $current_user->display_name,
                'subscribe_nonce'     => wp_create_nonce('jltma_subscribe_nonce'),
                'version'             => JLTMA_VER,
            ];

            wp_localize_script('jltma-admin-settings', 'JLTMA_SETTINGS', $localize_data);
        }

        if ($is_setup_wizard_page) {
            $this->jltma_setup_wizard_scrips();
        }

        // Recommended Plugins page assets
        $is_recommended_page = (
            strpos($screen->id, 'master-addons-recommended-plugins') !== false
        );
        if ($is_recommended_page) {
            wp_enqueue_style('jltma-recommended-plugins');
            wp_enqueue_script('jltma-recommended-plugins');
        }

        // ADMIN SDK Localize
        wp_enqueue_style('jltma-admin-sdk');
        wp_enqueue_script('jltma-admin-sdk');
        
        wp_localize_script(
			'jltma-admin-sdk',
			'JLTMACORE',
			array(
				'admin_ajax'        => admin_url('admin-ajax.php'),
				'recommended_nonce' => wp_create_nonce('jltma_recommended_nonce'),
				'is_premium'        => Helper::jltma_premium(),
			)
		);        
    }

    /**
     * Enqueue scripts and styles for the Setup Wizard page, and hide admin UI elements for a full-screen experience
     * Note: This is a hidden page only accessed via direct URL, so we can safely hide all admin notices and UI elements without affecting other pages
     */
    public function jltma_setup_wizard_scrips() {
        // Hide admin notices, admin bar, and sidebar for full-screen experience
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        remove_all_actions('network_admin_notices');
        remove_all_actions('user_admin_notices');

        // Hide the footer for the full-screen wizard (attached to the wizard
        // stylesheet below instead of a hardcoded <style> tag).
        $jltma_wizard_css = '#wpfooter { display: none !important; }';

        // Elementor icons for addon card icons
        if (wp_style_is('elementor-icons', 'registered')) {
            wp_enqueue_style('elementor-icons');
        } else {
            wp_enqueue_style('jltma-elementor-icons');
        }

        wp_enqueue_style('jltma-setup-wizard');
        wp_enqueue_script('jltma-setup-wizard');

        wp_add_inline_style('jltma-setup-wizard', $jltma_wizard_css);

        // Build recommended plugins data with pre-computed install status.
        $recommended_instance = Recommended_Plugins::get_instance();
        $raw_plugins          = $recommended_instance->plugins_list();
        $recommended_plugins  = [];

        if ( ! function_exists( 'install_plugin_install_status' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        }
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        foreach ( $raw_plugins as $slug => $plugin ) {
            $plugin_api = (object) $plugin;
            if ( ! isset( $plugin_api->version ) ) {
                $plugin_api->version = '';
            }
            $install_status = \install_plugin_install_status( $plugin_api );

            // Map install_plugin_install_status() result to simple status.
            if ( 'install' === $install_status['status'] ) {
                $status      = 'not_installed';
                $plugin_file = '';
            } elseif ( ! empty( $install_status['file'] ) && is_plugin_active( $install_status['file'] ) ) {
                $status      = 'active';
                $plugin_file = $install_status['file'];
            } else {
                $status      = 'installed';
                $plugin_file = $install_status['file'] ?? '';
            }

            $recommended_plugins[] = [
                'slug'          => $plugin['slug'],
                'name'          => $plugin['name'],
                'icon'          => $plugin['icon'],
                'download_link' => $plugin['download_link'],
                'status'        => $status,
                'plugin_file'   => $plugin_file,
            ];
        }

        $localize_data = [
            'pluginSlug' => 'master-addons',
            'restUrl'    => rest_url('master-addons/v1'),
            'nonce'      => wp_create_nonce('wp_rest'),
            'adminUrl'   => admin_url(),
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'assetsUrl'  => JLTMA_ASSETS,
            'recommended_nonce'   => wp_create_nonce('jltma_recommended_nonce'),
            'recommended_plugins' => $recommended_plugins,
            'logo'       => array(
                'light' => JLTMA_ASSETS . 'images/full-logo.svg',
                'dark'  => JLTMA_ASSETS . 'images/full-logo.png',
            ),
            'darkMode'     => 'light',
            'data'         => Config::get_config(),
            'is_premium'   => ma_el_fs()->can_use_premium_code__premium_only(),
            'is_developer' => ma_el_fs()->is_plan__premium_only('developer'),
            'hidden_menus' => [],
            'step_details' => REST_API::get_instance()->get_setup_status()->data
        ];

        wp_localize_script('jltma-setup-wizard', 'JLTMA_SETUP_WIZARD', $localize_data);
    }

    /**
     * Singleton instance
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Cached SettingsProxy instances (one per group)
     *
     * @var SettingsProxy[]
     */
    private $proxies = [];


    /**
     * Register admin menu page
     */
    public function register_admin_menu()
    {
        $image_dir  = defined('JLTMA_IMAGE_DIR') ? JLTMA_IMAGE_DIR : (defined('JLTMA_PRO_IMAGE_DIR') ? JLTMA_PRO_IMAGE_DIR : '');
        $logo       = $image_dir . 'red-logo.svg';
        $page_title = __('Master Addons for Elementor', 'master-addons');
        $menu_label = __('Master Addons', 'master-addons');

        $logo       = apply_filters('master_addons/white_label/menu_logo', $logo);
        $page_title = apply_filters('master_addons/white_label/page_title', $page_title);
        $menu_label = apply_filters('master_addons/white_label/menu_label', $menu_label);

        // Update badge
        $update_plugins = get_site_transient('update_plugins');
        $has_update     = (defined('JLTMA_BASE') && !empty($update_plugins->response[JLTMA_BASE]))
            || (defined('JLTMA_PRO_BASE') && !empty($update_plugins->response[JLTMA_PRO_BASE]));

        if ($has_update) {
            $menu_label = sprintf('%s <span class="jltma-menu-notice">1</span>', $menu_label);
        }

        add_menu_page(
            $page_title,
            $menu_label,
            'manage_options',
            'master-addons-settings',
            [$this, 'render_settings_page'],
            $logo,
            57
        );

        add_submenu_page(
            'master-addons-settings',
            $page_title,
            __('Settings', 'master-addons'),
            'manage_options',
            'master-addons-settings',
            [$this, 'render_settings_page']
        );

        // Setup Wizard - registered with empty parent for Freemius-independent access,
        // plus a visible submenu link under Master Addons until completed
        if ( ! REST_API::is_setup_complete() ) {
            // Hidden page registration (always accessible regardless of Freemius state)
            add_submenu_page(
                '',
                __('Setup Wizard', 'master-addons'),
                __('Setup Wizard', 'master-addons'),
                'manage_options',
                'master-addons-setup-wizard',
                [$this, 'jltma_setup_wizard_page_content']
            );

            // Visible menu link under Master Addons
            global $submenu;
            $submenu['master-addons-settings'][] = array(
                __('Setup Wizard', 'master-addons'),
                'manage_options',
                'admin.php?page=master-addons-setup-wizard',
            );
        }
    }

    /**
     * Render the settings page (loads welcome.php template)
     */
    public function render_settings_page()
    {
        echo '<div id="jltma-admin-settings-root"></div>';
    }

    /**
	 * Setup Wizard page content
	 */
	public function jltma_setup_wizard_page_content()
	{
		echo '<div id="jltma-setup-wizard-root"></div>';
	}

    /**
     * Get singleton instance
     *
     * @return self
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generic option getter for any wp_options key
     *
     * Reads a raw WordPress option with in-memory caching.
     * Works with any option key — not limited to PROXY_MAP groups.
     *
     * Usage:
     *   jltma_settings()->get('jltma_white_label_settings');
     *   jltma_settings()->get('jltma_white_label_settings', 'jltma_wl_plugin_logo');
     *   jltma_settings()->get('jltma_white_label_settings', 'missing_key', 'fallback');
     *
     * @param string      $option_key WordPress option name
     * @param string|null $sub_key    Optional sub-key within the option array
     * @param mixed       $default    Default value when key is missing
     * @return mixed
     */
    public function get($option_key, $sub_key = null, $default = null)
    {
        if (!isset(self::$cache[$option_key])) {
            self::$cache[$option_key] = get_option($option_key, []);
        }

        $settings = self::$cache[$option_key];

        if ($sub_key === null) {
            return $settings ?: $default;
        }

        return isset($settings[$sub_key]) ? $settings[$sub_key] : $default;
    }

    /**
     * Magic property access → returns cached SettingsProxy
     *
     * Enables: Settings::instance()->addons->get('key')
     *
     * @param string $name Group name (addons, extensions, plugins, icons, api)
     * @return SettingsProxy
     * @throws \InvalidArgumentException If group name is invalid
     */
    public function __get($name)
    {
        if (!isset(self::PROXY_MAP[$name])) {
            throw new \InvalidArgumentException(
                esc_html(sprintf('Unknown settings group "%s". Valid groups: %s', $name, implode(', ', array_keys(self::PROXY_MAP))))
            );
        }

        if (!isset($this->proxies[$name])) {
            $this->proxies[$name] = new SettingsProxy($name, self::PROXY_MAP[$name]);
        }

        return $this->proxies[$name];
    }

    /**
     * Static facade shorthand
     *
     * Enables: Settings::addons('key') as shorthand for Settings::instance()->addons->get('key')
     *
     * @param string $name      Group name
     * @param array  $arguments [0] = key (optional), [1] = default (optional)
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (isset(self::PROXY_MAP[$name])) {
            $proxy = self::instance()->$name;
            $key     = $arguments[0] ?? null;
            $default = $arguments[1] ?? null;

            return $proxy->get($key, $default);
        }

        throw new \BadMethodCallException(
            esc_html(sprintf('Call to undefined method %s::%s()', static::class, $name))
        );
    }

    // -------------------------------------------------------------------------
    // Existing static API (unchanged signatures)
    // -------------------------------------------------------------------------

    /**
     * Get addons settings (enabled/disabled state)
     *
     * @param string|null $key Specific addon key or null for all
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get_addons($key = null, $default = null)
    {
        return self::get_option(self::ADDONS_KEY, $key, $default);
    }

    /**
     * Save addons settings
     *
     * @param array $settings Settings array
     * @return bool
     */
    public static function save_addons($settings)
    {
        return self::save_option(self::ADDONS_KEY, $settings);
    }

    /**
     * Get extensions settings (enabled/disabled state)
     *
     * @param string|null $key Specific extension key or null for all
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get_extensions($key = null, $default = null)
    {
        return self::get_option(self::EXTENSIONS_KEY, $key, $default);
    }

    /**
     * Save extensions settings
     *
     * @param array $settings Settings array
     * @return bool
     */
    public static function save_extensions($settings)
    {
        return self::save_option(self::EXTENSIONS_KEY, $settings);
    }

    /**
     * Get third-party plugins settings
     *
     * @param string|null $key Specific plugin key or null for all
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get_plugins($key = null, $default = null)
    {
        return self::get_option(self::PLUGINS_KEY, $key, $default);
    }

    /**
     * Save third-party plugins settings
     *
     * @param array $settings Settings array
     * @return bool
     */
    public static function save_plugins($settings)
    {
        return self::save_option(self::PLUGINS_KEY, $settings);
    }

    /**
     * Get icons library settings
     *
     * @param string|null $key Specific icon key or null for all
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get_icons($key = null, $default = null)
    {
        return self::get_option(self::ICONS_KEY, $key, $default);
    }

    /**
     * Save icons library settings
     *
     * @param array $settings Settings array
     * @return bool
     */
    public static function save_icons($settings)
    {
        return self::save_option(self::ICONS_KEY, $settings);
    }

    /**
     * Get API settings
     *
     * @param string|null $key Specific API key or null for all
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get_api($key = null, $default = null)
    {
        return self::get_option(self::API_KEY, $key, $default);
    }

    /**
     * Save API settings
     *
     * @param array $settings Settings array
     * @return bool
     */
    public static function save_api($settings)
    {
        return self::save_option(self::API_KEY, $settings);
    }

    /**
     * Check if an addon is enabled
     *
     * @param string $addon_key
     * @return bool
     */
    public static function is_addon_enabled($addon_key)
    {
        return (bool) self::get_addons($addon_key, true);
    }

    /**
     * Check if an extension is enabled
     *
     * @param string $extension_key
     * @return bool
     */
    public static function is_extension_enabled($extension_key)
    {
        return (bool) self::get_extensions($extension_key, true);
    }

    /**
     * Check if a plugin integration is enabled
     *
     * @param string $plugin_key
     * @return bool
     */
    public static function is_plugin_enabled($plugin_key)
    {
        return (bool) self::get_plugins($plugin_key, true);
    }

    /**
     * Check if an icon library is enabled
     *
     * @param string $icon_key
     * @return bool
     */
    public static function is_icon_enabled($icon_key)
    {
        return (bool) self::get_icons($icon_key, true);
    }

    /**
     * Get enabled addons with full config data
     * Merges enabled state with addon definitions from Config
     *
     * @return array
     */
    public static function get_enabled_addons()
    {
        $enabled_settings = self::get_addons() ?: [];
        $all_addons = Config::get_addons();
        $enabled = [];

        foreach ($all_addons as $key => $addon) {
            if (!empty($enabled_settings[$key])) {
                $enabled[$key] = $addon;
            }
        }

        return $enabled;
    }

    /**
     * Get enabled addons by group
     *
     * @param string $group Group key
     * @return array
     */
    public static function get_enabled_addons_by_group($group)
    {
        $enabled_settings = self::get_addons() ?: [];
        $group_addons = Config::get_addons_by_group($group);
        $enabled = [];

        foreach ($group_addons as $key => $addon) {
            if (!empty($enabled_settings[$key])) {
                $enabled[$key] = $addon;
            }
        }

        return $enabled;
    }

    /**
     * Get enabled addons by group and subcategory
     *
     * @param string $group Group key
     * @param string $subcategory Subcategory key
     * @return array
     */
    public static function get_enabled_addons_by_subcategory($group, $subcategory)
    {
        $enabled_settings = self::get_addons() ?: [];
        $subcategory_addons = Config::get_addons_by_subcategory($group, $subcategory);
        $enabled = [];

        foreach ($subcategory_addons as $key => $addon) {
            if (!empty($enabled_settings[$key])) {
                $enabled[$key] = $addon;
            }
        }

        return $enabled;
    }

    /**
     * Get enabled extensions with full config data
     *
     * @return array
     */
    public static function get_enabled_extensions()
    {
        $enabled_settings = self::get_extensions() ?: [];
        $all_extensions = Config::get_extensions();
        $enabled = [];

        foreach ($all_extensions as $key => $extension) {
            if (!empty($enabled_settings[$key])) {
                $enabled[$key] = $extension;
            }
        }

        return $enabled;
    }

    /**
     * Get enabled extensions by group
     *
     * @param string $group Group key
     * @return array
     */
    public static function get_enabled_extensions_by_group($group)
    {
        $enabled_settings = self::get_extensions() ?: [];
        $group_extensions = Config::get_extensions_by_group($group);
        $enabled = [];

        foreach ($group_extensions as $key => $extension) {
            if (!empty($enabled_settings[$key])) {
                $enabled[$key] = $extension;
            }
        }

        return $enabled;
    }

    /**
     * Get addon counts by group (total and enabled)
     *
     * @return array ['group' => ['total' => n, 'enabled' => n]]
     */
    public static function get_addon_counts_by_group()
    {
        $enabled_settings = self::get_addons() ?: [];
        $groups = Config::get_groups();
        $counts = [];

        foreach (array_keys($groups) as $group) {
            $group_addons = Config::get_addons_by_group($group);
            $enabled = 0;

            foreach (array_keys($group_addons) as $key) {
                if (!empty($enabled_settings[$key])) {
                    $enabled++;
                }
            }

            $counts[$group] = [
                'total'   => count($group_addons),
                'enabled' => $enabled,
            ];
        }

        return $counts;
    }

    /**
     * Get extension counts by group (total and enabled)
     *
     * @return array ['group' => ['total' => n, 'enabled' => n]]
     */
    public static function get_extension_counts_by_group()
    {
        $enabled_settings = self::get_extensions() ?: [];
        $extension_groups = Config::get_extension_groups();
        $counts = [];

        foreach (array_keys($extension_groups) as $group) {
            $group_extensions = Config::get_extensions_by_group($group);
            $enabled = 0;

            foreach (array_keys($group_extensions) as $key) {
                if (!empty($enabled_settings[$key])) {
                    $enabled++;
                }
            }

            $counts[$group] = [
                'total'   => count($group_extensions),
                'enabled' => $enabled,
            ];
        }

        return $counts;
    }

    /**
     * Enable all addons in a group
     *
     * @param string $group Group key
     * @return bool
     */
    public static function enable_group($group)
    {
        $current = self::get_addons() ?: [];
        $group_addons = Config::get_addons_by_group($group);

        foreach (array_keys($group_addons) as $key) {
            $current[$key] = true;
        }

        return self::save_addons($current);
    }

    /**
     * Disable all addons in a group
     *
     * @param string $group Group key
     * @return bool
     */
    public static function disable_group($group)
    {
        $current = self::get_addons() ?: [];
        $group_addons = Config::get_addons_by_group($group);

        foreach (array_keys($group_addons) as $key) {
            $current[$key] = false;
        }

        return self::save_addons($current);
    }

    /**
     * Enable all extensions in a group
     *
     * @param string $group Group key
     * @return bool
     */
    public static function enable_extension_group($group)
    {
        $current = self::get_extensions() ?: [];
        $group_extensions = Config::get_extensions_by_group($group);

        foreach (array_keys($group_extensions) as $key) {
            $current[$key] = true;
        }

        return self::save_extensions($current);
    }

    /**
     * Disable all extensions in a group
     *
     * @param string $group Group key
     * @return bool
     */
    public static function disable_extension_group($group)
    {
        $current = self::get_extensions() ?: [];
        $group_extensions = Config::get_extensions_by_group($group);

        foreach (array_keys($group_extensions) as $key) {
            $current[$key] = false;
        }

        return self::save_extensions($current);
    }

    /**
     * Get default addon settings (all enabled)
     *
     * @return array
     */
    public static function get_default_addon_settings()
    {
        $all_addons = Config::get_addons();
        $defaults = [];

        foreach (array_keys($all_addons) as $key) {
            $defaults[$key] = true;
        }

        return $defaults;
    }

    /**
     * Get default extension settings (all enabled except mega-menu)
     *
     * @return array
     */
    public static function get_default_extension_settings()
    {
        $all_extensions = Config::get_extensions();
        $defaults = [];

        foreach (array_keys($all_extensions) as $key) {
            $defaults[$key] = true;
        }

        return $defaults;
    }

    /**
     * Get default plugin settings (all enabled)
     *
     * @return array
     */
    public static function get_default_plugin_settings()
    {
        return array_fill_keys(array_keys(Config::get_plugins()), true);
    }

    /**
     * Get default icon library settings (all enabled)
     *
     * @return array
     */
    public static function get_default_icon_settings()
    {
        return array_fill_keys(array_keys(Config::get_icons()), true);
    }

    // -------------------------------------------------------------------------
    // Core get/save (public so SettingsProxy can call them)
    // -------------------------------------------------------------------------

    /**
     * Generic get with migration support
     *
     * @param string      $option_key The option key
     * @param string|null $key        Specific setting key
     * @param mixed       $default    Default value
     * @return mixed
     */
    public static function get_option($option_key, $key = null, $default = null)
    {
        if (!self::is_valid_option_key($option_key)) {
            return $key === null ? [] : $default;
        }

        if (!isset(self::$cache[$option_key])) {
            // Try to get from new key first
            $settings = get_option($option_key, null);

            // If not found, check legacy key
            if ($settings === null) {
                $legacy_key = array_search($option_key, self::LEGACY_KEYS);
                if ($legacy_key !== false) {
                    $settings = get_option($legacy_key, []);
                    // Migrate to new key if legacy data exists
                    if (!empty($settings)) {
                        update_option($option_key, $settings);
                    }
                }
            }

            self::$cache[$option_key] = $settings ?: [];
        }

        $settings = self::$cache[$option_key];

        if ($key === null) {
            return $settings;
        }

        return isset($settings[$key]) ? $settings[$key] : $default;
    }

    /**
     * Generic save method
     *
     * @param string $option_key The option key
     * @param array  $settings   Settings array
     * @return bool
     */
    public static function save_option($option_key, $settings)
    {
        if (!self::is_valid_option_key($option_key)) {
            return false;
        }

        $settings = self::sanitize_settings($option_key, $settings);

        unset(self::$cache[$option_key]);

        return update_option($option_key, $settings);
    }

    /**
     * Clear settings cache
     *
     * @param string|null $option_key Specific key or null for all
     * @return $this|void Returns $this when called on instance for chaining
     */
    public static function clear_cache($option_key = null)
    {
        if ($option_key === null) {
            self::$cache = [];
        } else {
            unset(self::$cache[$option_key]);
        }

        if (self::$instance !== null) {
            return self::$instance;
        }
    }

    /**
     * Migrate all legacy settings to new keys
     *
     * @return array Migration results
     */
    public static function migrate_legacy_settings()
    {
        $results = [];

        foreach (self::LEGACY_KEYS as $legacy_key => $new_key) {
            $legacy_data = get_option($legacy_key, null);

            if ($legacy_data !== null) {
                // Check if new key already has data
                $new_data = get_option($new_key, null);

                if ($new_data === null) {
                    // Migrate data to new key
                    update_option($new_key, $legacy_data);
                    $results[$legacy_key] = 'migrated';
                } else {
                    $results[$legacy_key] = 'skipped (new key exists)';
                }
            } else {
                $results[$legacy_key] = 'no data';
            }
        }

        return $results;
    }

    /**
     * Remove all legacy option keys
     *
     * @return array Deletion results
     */
    public static function remove_legacy_settings()
    {
        $results = [];

        foreach (array_keys(self::LEGACY_KEYS) as $legacy_key) {
            if (delete_option($legacy_key)) {
                $results[$legacy_key] = 'deleted';
            } else {
                $results[$legacy_key] = 'not found';
            }
        }

        return $results;
    }

    /**
     * Get all option keys (new format)
     *
     * @return array
     */
    public static function get_option_keys()
    {
        return [
            'addons'      => self::ADDONS_KEY,
            'extensions'  => self::EXTENSIONS_KEY,
            'plugins'     => self::PLUGINS_KEY,
            'icons'       => self::ICONS_KEY,
            'api'         => self::API_KEY,
            'white_label' => self::WHITE_LABEL,
        ];
    }

    // -------------------------------------------------------------------------
    // Validation & Sanitization helpers
    // -------------------------------------------------------------------------

    /**
     * Check if an option exists in the database (vs being an empty array)
     *
     * Distinguishes "option was never saved" (first install) from
     * "option was saved as empty array" (user disabled everything).
     *
     * @param string $option_key WP option key constant (e.g. Settings::ADDONS_KEY)
     * @return bool
     */
    public static function option_exists($option_key)
    {
        return get_option($option_key, null) !== null;
    }

    /**
     * Check whether an option key is one of the known constants
     *
     * @param string $option_key
     * @return bool
     */
    private static function is_valid_option_key($option_key)
    {
        return in_array($option_key, [
            self::ADDONS_KEY,
            self::EXTENSIONS_KEY,
            self::PLUGINS_KEY,
            self::ICONS_KEY,
            self::API_KEY,
        ], true);
    }

    /**
     * Context-aware sanitization based on option key
     *
     * Toggle maps (addons, extensions, plugins, icons): keys → sanitize_key(), values → (int) 0|1
     * API settings: keys → sanitize_key(), values → sanitize_text_field()
     *
     * @param string $option_key
     * @param mixed  $settings
     * @return array
     */
    private static function sanitize_settings($option_key, $settings)
    {
        if (!is_array($settings)) {
            return [];
        }

        $sanitized = [];

        if ($option_key === self::API_KEY) {
            // API values may be nested (recaptcha: {site_key, secret_key}, twitter: {...}, etc.)
            foreach ($settings as $key => $value) {
                $sanitized[sanitize_key($key)] = self::sanitize_api_value($value);
            }
        } else {
            // Toggle maps: cast to int 0|1
            foreach ($settings as $key => $value) {
                $sanitized[sanitize_key($key)] = (int) (bool) $value;
            }
        }

        return $sanitized;
    }

    /**
     * Recursively sanitize API setting values
     *
     * Handles nested arrays (e.g. recaptcha: {site_key, secret_key}),
     * strings, booleans, and numeric values.
     *
     * @param mixed $value
     * @return mixed
     */
    private static function sanitize_api_value($value)
    {
        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[sanitize_key($k)] = self::sanitize_api_value($v);
            }
            return $result;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            return sanitize_text_field($value);
        }

        return '';
    }
}
