<?php

namespace MasterAddons\Inc\Admin\Templates\Widgets;

use MasterAddons\Inc\Admin\WidgetBuilder\Widget_Category_Badge;

defined('ABSPATH') || exit;

/**
 * Boots Widgets Library components.
 */
class Widgets_Loader
{
    private static $_instance = null;

    public function __construct()
    {
        Widgets_Library::get_instance();
        Widgets_Ajax::get_instance();
        Widget_Category_Badge::get_instance();
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
