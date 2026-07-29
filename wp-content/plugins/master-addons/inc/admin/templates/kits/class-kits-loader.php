<?php

namespace MasterAddons\Inc\Admin\Templates\Kits;

defined('ABSPATH') || exit;

/**
 * Kits Loader - Initializes all Template Kits functionality
 */
class Kits_Loader
{
    private static $_instance = null;

    public function __construct()
    {
        $this->load_dependencies();
        $this->init_classes();
    }

    /**
     * Load procedural files that haven't been converted to classes yet
     */
    private function load_dependencies()
    {
        // All files are now class-based and loaded via autoloader
    }

    /**
     * Initialize class-based components
     */
    private function init_classes()
    {
        // Initialize Template Kits menu and page
        Template_Kits::get_instance();

        // Initialize AJAX handlers
        Ajax_Handlers::get_instance();

        // Initialize Importer
        Importer::get_instance();
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
