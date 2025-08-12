<?php

namespace MasterAddons\Admin\Dashboard\Addons\Elements;

if (!class_exists('JLTMA_Icons_Library')) {
    class JLTMA_Icons_Library
    {
        private static $instance = null;
        public static $jltma_icons_library;

        public function __construct()
        {
            self::$jltma_icons_library = [
                'jltma-icons-library'      => [
                    // 'title'             => esc_html__('Icons Library', 'master-addons' ),
                    'title'             => 'Icons Library',
                    'libraries'          => [
                        [
                            // 'title'    => esc_html__('Simple Line Icons', 'master-addons' ),
                            'title'    => 'Simple Line Icons',
                            'key'      => 'simple-line-icons',
                            'demo_url' => '',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Elementor Icons', 'master-addons' ),
                            'title'    => 'Elementor Icons',
                            'key'      => 'elementor-icon',
                            'demo_url' => '',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Ionic Font', 'master-addons' ),
                            'title'    => 'Ionic Font',
                            'key'      => 'iconic-fonts',
                            'class'    => '',
                            'demo_url' => '',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Linear Icons', 'master-addons' ),
                            'title'    => 'Linear Icons',
                            'key'      => 'linear-icons',
                            'class'    => '',
                            'demo_url' => '',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Material Icons', 'master-addons' ),
                            'title'    => 'Material Icons',
                            'key'      => 'material-icons',
                            'class'    => '',
                            'demo_url' => '',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                    ]
                ]
            ];
        }

        public static function get_instance()
        {
            if (!self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }
    }
    JLTMA_Icons_Library::get_instance();
}
