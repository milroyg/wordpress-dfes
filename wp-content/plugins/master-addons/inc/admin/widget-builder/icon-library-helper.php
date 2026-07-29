<?php
namespace MasterAddons\Inc\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Icon Library Helper
 * Handles icon library CSS enqueuing and configuration
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */
class Icon_Library_Helper
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Enqueue icon library CSS files
     */
    public function enqueue_icon_libraries()
    {
        $plugin_url = trailingslashit(JLTMA_URL);
        $fonts_path = $plugin_url . 'assets/fonts/';

        // Elementor Icons — use Elementor's copy if available, otherwise local fallback
        if (wp_style_is('elementor-icons', 'registered')) {
            wp_enqueue_style('elementor-icons');
        } else {
            wp_enqueue_style('jltma-elementor-icons');
        }

        // Simple Line Icons
        wp_enqueue_style(
            'jltma-simple-line-icons',
            $fonts_path . 'simple-line-icons/simple-line-icons.css',
            [],
            JLTMA_VER
        );

        // Iconic Fonts
        wp_enqueue_style(
            'jltma-iconic-fonts',
            $fonts_path . 'iconic-fonts/iconic-font.min.css',
            [],
            JLTMA_VER
        );

        // Linear Icons
        wp_enqueue_style(
            'jltma-linear-icons',
            $fonts_path . 'linear-icons/linear-icons.css',
            [],
            JLTMA_VER
        );

        // Material Icons
        wp_enqueue_style(
            'jltma-material-icons',
            $fonts_path . 'material-icons/material-icons.css',
            [],
            JLTMA_VER
        );

        /**
         * Allow premium to register extra icon font stylesheets (Feather, Remix, Teeny, etc.).
         *
         * Each entry: [ 'handle' => string, 'src' => string (absolute URL) ].
         *
         * @param array  $extra_fonts Additional icon font stylesheets to enqueue.
         * @param string $fonts_path  Base URL of the free icon fonts folder.
         */
        $extra_fonts = apply_filters('master_addons/widget_builder/icon_libraries', [], $fonts_path);

        if (is_array($extra_fonts)) {
            foreach ($extra_fonts as $font) {
                if (empty($font['handle']) || empty($font['src'])) {
                    continue;
                }

                wp_enqueue_style($font['handle'], $font['src'], [], JLTMA_VER);
            }
        }
    }

    /**
     * Get icon library configuration
     * Parses CSS files to extract icon class names
     */
    public function get_icon_library_config()
    {
        $fonts_dir = JLTMA_PATH . 'assets/fonts/';

        $libraries = [];

        // Add Elementor Icons — use Elementor's copy if available, otherwise local fallback
        $eicons_css = null;
        if (defined('ELEMENTOR_ASSETS_PATH') && file_exists(ELEMENTOR_ASSETS_PATH . 'lib/eicons/css/elementor-icons.min.css')) {
            $eicons_css = ELEMENTOR_ASSETS_PATH . 'lib/eicons/css/elementor-icons.min.css';
        } elseif (file_exists(JLTMA_PATH . 'assets/fonts/elementor-icon/elementor-icons.css')) {
            $eicons_css = JLTMA_PATH . 'assets/fonts/elementor-icon/elementor-icons.css';
        }
        if ($eicons_css) {
            $libraries['Elementor Icons'] = [
                'prefix' => 'eicon-',
                'display_prefix' => 'eicon eicon-',
                'list-icon' => 'eicon eicon-elementor',
                'icon-style' => 'elementor-icons',
                'css_file' => $eicons_css
            ];
        }

        // Add other icon libraries
        $libraries = array_merge($libraries, [
            'Simple Line Icons' => [
                'prefix' => 'icon-',
                'display_prefix' => 'icon-',
                'list-icon' => 'icon-heart',
                'icon-style' => 'simple-line-icons',
                'css_file' => $fonts_dir . 'simple-line-icons/simple-line-icons.css'
            ],
            'Iconic Font Icons' => [
                'prefix' => 'im-',
                'display_prefix' => 'im im-',
                'list-icon' => 'im im-flag',
                'icon-style' => 'iconic-fonts',
                'css_file' => $fonts_dir . 'iconic-fonts/iconic-font.min.css'
            ],
            'Linear Icons' => [
                'prefix' => 'lnr-',
                'display_prefix' => 'lnr lnr-',
                'list-icon' => 'lnr lnr-flag',
                'icon-style' => 'linear-icons',
                'css_file' => $fonts_dir . 'linear-icons/linear-icons.css'
            ],
            'Material Icons' => [
                'prefix' => 'jltma-material-icon-',
                'display_prefix' => 'jltma-material-icon-',
                'list-icon' => 'jltma-material-icon-flag',
                'icon-style' => 'material-icons',
                'css_file' => $fonts_dir . 'material-icons/material-icons.css'
            ],
        ]);

        /**
         * Allow premium to register extra icon libraries (Feather, Remix, Teeny, etc.).
         *
         * Each entry keyed by library name:
         * [ 'prefix' => ..., 'display_prefix' => ..., 'list-icon' => ..., 'icon-style' => ..., 'css_file' => absolute path ].
         *
         * @param array  $libraries Icon libraries to parse.
         * @param string $base_path Plugin filesystem base path (JLTMA_PATH).
         */
        $libraries = apply_filters('master_addons/widget_builder/icon_library_config', $libraries, JLTMA_PATH);

        $config = [];

        foreach ($libraries as $library_name => $library_data) {
            $icons = $this->parse_icon_classes_from_css($library_data['css_file'], $library_data['prefix']);

            if (!empty($icons)) {
                $config[$library_name] = [
                    '' => [
                        'prefix' => $library_data['display_prefix'],
                        'list-icon' => $library_data['list-icon'],
                        'icon-style' => $library_data['icon-style'],
                        'icons' => $icons
                    ]
                ];
            }
        }

        return $config;
    }

    /**
     * Parse CSS file to extract icon class names
     */
    private function parse_icon_classes_from_css($css_file, $prefix)
    {
        if (!file_exists($css_file)) {
            return [];
        }

        $css_content = file_get_contents($css_file);
        $icons = [];

        // Standard pattern matching for all libraries
        $trim_prefix = trim($prefix);
        $pattern = '/\.' . preg_quote($trim_prefix, '/') . '([a-zA-Z0-9_-]+)(?:\s|,|:|\{|::)/i';

        preg_match_all($pattern, $css_content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $icon_name) {
                // Skip pseudo-classes and common CSS properties
                if (in_array($icon_name, ['before', 'after', 'hover', 'active', 'focus', 'disabled'])) {
                    continue;
                }

                // Return just the icon name (suffix after prefix)
                if (!in_array($icon_name, $icons)) {
                    $icons[] = $icon_name;
                }
            }
        }

        // Remove duplicates and sort
        $icons = array_unique($icons);
        sort($icons);

        return array_values($icons);
    }

    /**
     * Localize icon library data for JavaScript
     */
    public function localize_icon_library()
    {
        $icon_config = $this->get_icon_library_config();

        wp_localize_script(
            'jltma-widget-builder-app',
            'JLTMAIconLibrary',
            [
                'libraries' => $icon_config
            ]
        );
    }
}
