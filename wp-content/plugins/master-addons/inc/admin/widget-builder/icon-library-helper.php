<?php
namespace MasterAddons\Admin\WidgetBuilder;

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

        // Elementor Icons (already loaded by Elementor)
        if (!wp_style_is('elementor-icons', 'enqueued')) {
            wp_enqueue_style(
                'elementor-icons',
                ELEMENTOR_ASSETS_URL . 'lib/eicons/css/elementor-icons.min.css',
                [],
                JLTMA_VER
            );
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

        // Feather Icons
        wp_enqueue_style(
            'jltma-feather-icons',
            $fonts_path . 'feather-icons/feather-icons.min.css',
            [],
            JLTMA_VER
        );

        // Remix Icons
        wp_enqueue_style(
            'jltma-remix-icons',
            $fonts_path . 'remix-icons/remix-icons.min.css',
            [],
            JLTMA_VER
        );

        // Teeny Icons
        wp_enqueue_style(
            'jltma-teeny-icons',
            $fonts_path . 'teeny-icons/teeny-icons.min.css',
            [],
            JLTMA_VER
        );
    }

    /**
     * Get icon library configuration
     * Parses CSS files to extract icon class names
     */
    public function get_icon_library_config()
    {
        $fonts_dir = JLTMA_PATH . 'assets/fonts/';

        $libraries = [];

        // Add Elementor Icons if Elementor is active
        if (defined('ELEMENTOR_ASSETS_PATH') && file_exists(ELEMENTOR_ASSETS_PATH . 'lib/eicons/css/elementor-icons.min.css')) {
            $libraries['Elementor Icons'] = [
                'prefix' => 'eicon-',
                'display_prefix' => 'eicon eicon-',
                'list-icon' => 'eicon eicon-elementor',
                'icon-style' => 'elementor-icons',
                'css_file' => ELEMENTOR_ASSETS_PATH . 'lib/eicons/css/elementor-icons.min.css'
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
            'Feather Icons' => [
                'prefix' => 'jltma-feather-icon-',
                'display_prefix' => 'jltma-feather-icon-',
                'list-icon' => 'jltma-feather-icon-feather',
                'icon-style' => 'feather-icons',
                'css_file' => $fonts_dir . 'feather-icons/feather-icons.min.css'
            ],
            'Remix Icons' => [
                'prefix'         => 'jltma-ri-',
                'display_prefix' => 'jltma-ri-',
                'list-icon'      => 'jltma-ri-remixicon-fill',
                'icon-style'     => 'remix-icons',
                'css_file'       => $fonts_dir . 'remix-icons/remix-icons.min.css'
            ],
            'Teeny Icons' => [
                'prefix'         => 'jltma-ti-',
                'display_prefix' => 'jltma-ti-',
                'list-icon'      => 'jltma-ti-mood-laugh',
                'icon-style'     => 'teeny-icons',
                'css_file'       => $fonts_dir . 'teeny-icons/teeny-icons.min.css'
            ],
        ]);

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
