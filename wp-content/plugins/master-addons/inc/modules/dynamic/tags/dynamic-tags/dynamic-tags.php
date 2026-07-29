<?php

namespace MasterAddons\Modules\Dynamic;

use MasterAddons\Inc\Classes\Helper;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MasterAddons\Modules\Dynamic\DynamicTags')) {
    /**
     * Dynamic Tags Extension
     *
     * Registers custom dynamic tags for Elementor
     */
    class DynamicTags
    {
        /**
         * Instance
         *
         * @var DynamicTags|null
         */
        private static $instance = null;

        /**
         * Tags directory path
         *
         * @var string
         */
        private $tags_path;

        /**
         * Group titles mapping
         *
         * @var array
         */
        private $group_titles = [
            'archive'  => 'Archive',
            'author'   => 'Author',
            'post'     => 'Post',
            'site'     => 'Site',
            'comments' => 'Comments',
            'action'   => 'Action',
            'media'    => 'Media',
            'URL'      => 'URL',
        ];

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->tags_path = __DIR__ . '/inc/';
            $this->init();
        }

        /**
         * Initialize the extension
         */
        /**
         * Whether tags were successfully registered
         *
         * @var bool
         */
        private $tags_registered = false;

        public function init()
        {
            add_action('elementor/dynamic_tags/register', [$this, 'register_dynamic_tags']);

            // Hide Elementor's "Dynamic Content PRO" promotion teaser since we provide our own dynamic tags
            add_action('elementor/editor/after_enqueue_styles', [$this, 'hide_dynamic_promotion']);
        }

        /**
         * Hide Elementor's dynamic tags promotion teaser via CSS
         * Elementor checks window.elementorPro (hasPro()) to show the teaser —
         * removing dynamicPromotionURL from config doesn't prevent it.
         * We hide via CSS in the editor since our tags replace the native ones.
         */
        public function hide_dynamic_promotion()
        {
            wp_add_inline_style('elementor-editor', '.elementor-tags-list__teaser { display: none !important; }');
        }

        /**
         * Convert filename to class name
         * e.g., 'archive-description.php' => 'Archive_Description'
         *
         * @param string $filename
         * @return string
         */
        private function filename_to_classname($filename)
        {
            $name = str_replace('.php', '', $filename);
            $name = str_replace('-', ' ', $name);
            $name = ucwords($name);
            $name = str_replace(' ', '_', $name);
            return $name;
        }

        /**
         * Register dynamic tags with Elementor
         *
         * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags
         */
        public function register_dynamic_tags($dynamic_tags)
        {
            $registered_groups = [];

            // Get all PHP files from inc directory
            $tag_files = glob($this->tags_path . '*.php');

            if (empty($tag_files)) {
                return;
            }

            foreach ($tag_files as $file) {
                $filename = basename($file);
                $class_name = $this->filename_to_classname($filename);
                $full_class = 'MasterAddons\\Modules\\DynamicTags\\Tags\\' . $class_name;

                // Include the tag file
                include_once $file;

                // Check if class exists
                if (!class_exists($full_class)) {
                    continue;
                }

                // Create instance to get group info
                $tag_instance = new $full_class();

                // Get group from tag
                $group = $tag_instance->get_group();

                // Register group if not already registered
                if (!isset($registered_groups[$group])) {
                    $title = isset($this->group_titles[$group]) ? $this->group_titles[$group] : ucfirst($group);
                    Helper::jltma_elementor()->dynamic_tags->register_group($group, [
                        'title' => $title
                    ]);
                    $registered_groups[$group] = true;
                }

                // Register the tag
                $dynamic_tags->register($tag_instance);
                $this->tags_registered = true;
            }
        }

        /**
         * Get instance
         *
         * @return DynamicTags
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }
}

// Initialize the extension
DynamicTags::get_instance();
