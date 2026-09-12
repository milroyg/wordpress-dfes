<?php

namespace MasterAddons\Inc\Admin\WidgetBuilder;

use MasterAddons\Inc\Classes\Helper;

defined('ABSPATH') || exit;

/**
 * Brand-colours the PRO badge on the Master Addons Widgets panel category.
 *
 * The badge itself is rendered by Elementor from the category's `promotion`
 * property (registered in class-master-elementor-addons.php). This class only
 * restyles it, scoped to our own category so Elementor's own Pro promotions
 * keep their native colour.
 */
class Widget_Category_Badge
{
    const CATEGORY_SLUG = 'master-addons-widgets';

    /** Brand purple. Reads well on the light editor theme. */
    const BRAND_COLOR = '#6814cd';

    /**
     * Lighter tint for the dark editor theme — #6814cd only reaches roughly
     * 2:1 contrast against Elementor's near-black panel (#1f2124).
     */
    const BRAND_COLOR_DARK = '#a855f7';

    private static $_instance = null;

    public function __construct()
    {
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_badge_style']);
    }

    public function enqueue_badge_style()
    {
        // Licensed sites get no promotion registered, so nothing to style.
        if (Helper::jltma_premium()) {
            return;
        }

        // The plain `color` declaration is the fallback for browsers without
        // light-dark(); Elementor sets color-scheme on the root, so supporting
        // browsers pick the tint matching the editor's UI theme.
        $css = sprintf(
            '#elementor-panel-category-%1$s .elementor-panel-heading-promotion a,' .
            '#elementor-panel-category-%1$s .elementor-panel-heading-promotion i' .
            '{color:%2$s;color:light-dark(%2$s,%3$s)}' .
            '#elementor-panel-category-%1$s .elementor-panel-heading-promotion a:hover{opacity:.8}',
            self::CATEGORY_SLUG,
            self::BRAND_COLOR,
            self::BRAND_COLOR_DARK
        );

        wp_register_style('jltma-widget-category-badge', false, [], JLTMA_VER);
        wp_enqueue_style('jltma-widget-category-badge');
        wp_add_inline_style('jltma-widget-category-badge', $css);
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
