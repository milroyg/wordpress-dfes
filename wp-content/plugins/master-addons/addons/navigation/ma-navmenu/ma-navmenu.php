<?php

namespace MasterAddons\Addons;

// Elementor Classes
use MasterAddons\Inc\Classes\Base\Master_Widget;
use Elementor\Icons_Manager;
// Master Addons
use MasterAddons\Inc\Classes\Animation;
use MasterAddons\Inc\Admin\Config;
use MasterAddons\Inc\Admin\Settings\Settings;
use MasterAddons\Inc\Classes\Upgrades\Control_Key_Migrator;

/**
 * Author Name: Liton Arefin
 * Author URL : https: //jeweltheme.com
 * Date       : 9/29/19
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Master Mega Menu Addon
 */
class Nav_Menu extends Master_Widget
{
    use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

    /**
     * The id of the <ul> the main nav rendered, so the dropdown can point at it
     * instead of rendering the whole menu a second time. Empty when there is no
     * main nav to clone from — the 'dropdown' layout, where the dropdown is the
     * only menu on the page and still has to be rendered server side.
     *
     * @var string
     */
    private $main_menu_list_id = '';

    public function get_name()
    {
        return 'ma-navmenu';
    }
    public function get_title()
    {
        return __('Navigation Menu', 'master-addons' );
    }

    public function get_icon()
    {
        return 'jltma-icon ' . Config::get_addon_icon($this->get_name());
    }

    public function get_keywords()
    {
        return ['nav', 'navigation', 'menu', 'nav menu', 'header', 'footer', 'sidebar'];
    }
    
    protected function is_dynamic_content(): bool {
        return true;
    }

    public function get_widget_class()
    {
        return 'jltma-nav-menu';
    }

    public function get_widget_selector()
    {
        return '.' . $this->get_widget_class();
    }

    protected $nav_menu_index = 1;

    protected function jltma_nav_menu_help_section()
    {
        // Help Docs section (links from config.php)
        $this->jltma_help_docs();

        $this->upgrade_to_pro_message();

    }

    /**
     * Register Nav Menu Controls Section
     */

    protected function register_controls()
    {
        $widget_selector = $this->get_widget_selector();
        $menus = $this->get_available_menus();

        // Content Tabs
       $file_path = __DIR__ . '/options/content.php';
       if (file_exists($file_path)) {
            require_once $file_path;
            
            if (class_exists('\\MasterAddons\\Addons\\Navmenu\\Options\\Content')) {
                new \MasterAddons\Addons\Navmenu\Options\Content($this, $widget_selector, $menus);
            }
        }
        
        $this->jltma_nav_menu_help_section();

        // Style Tabs
        $file_path = __DIR__ . '/options/style.php';
        if (file_exists($file_path)) {
            require_once $file_path;
            
            if (class_exists('\\MasterAddons\\Addons\\Navmenu\\Options\\Content')) {
                new \MasterAddons\Addons\Navmenu\Options\Style($this, $widget_selector);
            }
        }
    }

    /**
     *
     * @return array menus list
     */
    public function get_available_menus()
    {
        // TODO: optimize it later with transient key
        static $menus = null;
        
        if ($menus === null) {
            $menus = wp_list_pluck(wp_get_nav_menus(), 'name', 'term_id');
        }

        return $menus;
    }

    /**
     *
     * @return menu index
     */
    protected function get_nav_menu_index()
    {
        return $this->nav_menu_index++;
    }

    /**
     *
     * @return link classes list
     */
    public function get_link_classes($atts, $item, $args, $depth)
    {
        $settings = $this->get_active_settings();

        $classes = ($depth ? $this->get_widget_class() . '__dropdown-item-sub' : $this->get_widget_class() . '__dropdown-item');
        $is_hash_anchor = false !== strpos($atts['href'], '#');

        if ( !empty($is_hash_anchor) ) {
            $classes .= ' ' . $this->get_widget_class() . '__item-anchor';
        }

        // FIXME: it will be active menu handle
        // if (!$is_anchor && in_array('current-menu-item', $item->classes, true)) {
        //     $classes .= ' ' . $this->get_widget_class() . '__item-active';
        // }

        if (empty($atts['class'])) {
            $atts['class'] = $classes;
        } else {
            $atts['class'] .= ' ' . $classes;
        }

        return $atts;
    }

    /**
     * Set Dropdown Icon in menu item
     * @return void
     */
    public function set_menu_dropdown_icon($item_output, $item, $depth, $args) {
        if (in_array('menu-item-has-children', $item->classes)) {
            $settings = $this->get_active_settings();

            // Top level and sub levels have their own icon + show/hide toggle.
            if (0 === $depth) {
                $is_visible      = !isset($settings['indicator_main_popover']) || 'show' === $settings['indicator_main_popover'];
                $indicator_icon  = isset($settings['indicator_main']) ? $settings['indicator_main'] : '';
                $arrow_class     = $this->get_widget_class() . '__arrow';
            } else {
                $is_visible      = !isset($settings['jltma_indicator_submenu_icon']) || 'show' === $settings['jltma_indicator_submenu_icon'];
                $indicator_icon  = isset($settings['indicator_submenu']) ? $settings['indicator_submenu'] : '';
                $arrow_class     = $this->get_widget_class() . '__arrow ' . $this->get_widget_class() . '__arrow-sub';
            }

            if (!$is_visible || empty($indicator_icon)) {
                return $item_output;
            }

            // render_icon() echoes and returns a bool -- use try_get_icon_html()
            // so the markup lands inside the item, not at the top of the widget.
            $icon_html = Icons_Manager::try_get_icon_html($indicator_icon, ['aria-hidden' => 'true']);

            if (!empty($icon_html)) {
                $icon = '<span class="' . esc_attr($arrow_class) . '">' . $icon_html . '</span>';
                $item_output = str_replace('</a>', $icon . '</a>', $item_output);
            }
        }
        
        return $item_output;
    }

    /**
     * Settings, with values still saved under a renamed control's old key.
     *
     * A control's name is the key its value is saved under, so `layout`
     * becoming `jltma_nav_layout` leaves every page built before the rename
     * holding a value the widget no longer asks for. The upgrade routine
     * rewrites those pages once (see Control_Key_Migrator), and this covers the
     * ones it has not reached: a page restored from a revision, a template
     * imported from an older export, a site upgraded with the routine skipped.
     *
     * @return array
     */
    public function get_active_settings($settings = null, $controls = null)
    {
        $active = parent::get_active_settings($settings, $controls);

        // A caller that brought its own settings gets them back untouched.
        if (null !== $settings || !class_exists('MasterAddons\Inc\Classes\Upgrades\Control_Key_Migrator')) {
            return $active;
        }

        return Control_Key_Migrator::restore_from_saved($this->get_name(), $active, $this->get_data('settings'));
    }

    /**
     * Renamed controls, as a template built on an older version is imported.
     *
     * Elementor hands every widget its own data here before it is saved, which
     * is the one moment an import can be brought up to date without touching
     * what is already on the site.
     *
     * @param array $element
     *
     * @return array
     */
    public function on_import($element)
    {
        if (class_exists('MasterAddons\Inc\Classes\Upgrades\Control_Key_Migrator')) {
            $element = Control_Key_Migrator::rename_element($element);
        }

        return $element;
    }

    /**
     * A responsive setting's value on each device.
     *
     * A device with nothing of its own inherits from the one above it, the way
     * Elementor's own responsive settings cascade -- so what comes back here is
     * always the value actually in force at that width, and ma-navmenu.js can
     * put the matching class on the nav without repeating the cascade.
     *
     * @return array
     */
    private function get_device_values($name, $fallback)
    {
        $settings = $this->get_active_settings();
        $desktop = !empty($settings[$name]) ? $settings[$name] : $fallback;
        $tablet = !empty($settings[$name . '_tablet']) ? $settings[$name . '_tablet'] : $desktop;
        $mobile = !empty($settings[$name . '_mobile']) ? $settings[$name . '_mobile'] : $tablet;

        return array(
            'desktop' => $desktop,
            'tablet'  => $tablet,
            'mobile'  => $mobile,
        );
    }

    /**
     * The layout for each device, as saved on the responsive Layout control.
     *
     * @return array
     */
    private function get_device_layouts()
    {
        return $this->get_device_values('jltma_nav_layout', 'horizontal');
    }

    /**
     * Whether any width shows the menu as a hamburger.
     *
     * The toggle, its panel parts and their classes are rendered once, server
     * side, for every width -- a menu that is horizontal on the desktop and a
     * hamburger on a phone is one menu, and which of the two is in force is a
     * class away (see jltmaApplyLayout in ma-navmenu.js).
     *
     * @return bool
     */
    private function has_hamburger_layout()
    {
        return in_array('dropdown', $this->get_device_layouts(), true);
    }

    /**
     * The hamburger toggle, as markup.
     *
     * Returned rather than echoed: the caller prints it inside the <nav>, so
     * the toggle and the list it controls stay in one element -- which is what
     * lets the stylesheet open the list from the toggle's own state, and what
     * a screen reader needs to follow aria-controls.
     *
     * @return string
     */
    public function get_toggle()
    {
        $settings = $this->get_active_settings();

        // Rendered for any width that shows the menu as a hamburger; the
        // stylesheet hides it at the widths that do not.
        if (!$this->has_hamburger_layout()) {
            return '';
        }

        $dropdown_type = !empty($settings['jltma_dropdown_menu_type']) ? $settings['jltma_dropdown_menu_type'] : 'default';

        $this->add_render_attribute('toggle-container', 'class', array(
            $this->get_widget_class() . '__toggle-container',
            'jltma-menu-dropdown-type-' . esc_attr($dropdown_type),
        ));

        $this->add_render_attribute('toggle-container', array(
            'role'           => 'button',
            'tabindex'       => '0',
            'aria-expanded'  => 'false',
            'aria-label'     => __('Toggle menu', 'master-addons'),
        ));

        // Points at the menu list itself, which is rendered by wp_nav_menu()
        // under the id render() hands over here.
        if (!empty($this->main_menu_list_id)) {
            $this->add_render_attribute('toggle-container', 'aria-controls', $this->main_menu_list_id);
        }

        $toggle_type = !empty($settings['dropdown_toggle_type']) ? $settings['dropdown_toggle_type'] : 'icon';
        $toggle_inner = '';

        // Type decides which halves are rendered; Both keeps the icon first, so
        // the "Horizontal Gap" control -- which spaces whatever follows the
        // active icon -- has the label to push away from it.
        if ('text' !== $toggle_type) {
            $toggle_inner .= $this->get_toggle_icon($settings);
        }

        if ('icon' !== $toggle_type) {
            $toggle_inner .= $this->get_toggle_text($settings);
        }

        return '<div ' . $this->get_render_attribute_string('toggle-container') . '>' .
            '<div class="' . esc_attr($this->get_widget_class()) . '__toggle">' .
            $toggle_inner .
            '</div>' .
        '</div>';
    }

    /**
     * The toggle's label.
     *
     * @return string
     */
    private function get_toggle_text($settings = array())
    {
        $label = !empty($settings['dropdown_toggle_text'])
            ? $settings['dropdown_toggle_text']
            : __('Menu', 'master-addons');

        return '<span class="' . esc_attr($this->get_widget_class()) . '__toggle-label">' .
            esc_html($label) .
        '</span>';
    }

    /**
     * The toggle's two icons: the one shown while the menu is closed, and the
     * one that replaces it while it is open. Both are always rendered -- the
     * stylesheet swaps them -- so neither has to be built again on click.
     *
     * @return string
     */
    private function get_toggle_icon($settings = array())
    {
        $icon = !empty($settings['dropdown_toggle_icon']) ? $settings['dropdown_toggle_icon'] : array();
        $active_icon = !empty($settings['dropdown_toggle_icon_active']) ? $settings['dropdown_toggle_icon_active'] : array();

        if (empty($icon['value'])) {
            $icon = array('value' => 'eicon-menu-bar', 'library' => 'eicon');
        }

        if (empty($active_icon['value'])) {
            $active_icon = array('value' => 'eicon-close', 'library' => 'eicon');
        }

        // try_get_icon_html() returns the markup; render_icon() would echo it
        // straight out of the widget, ahead of everything built here.
        return '<span class="jltma-toggle-icon">' .
            Icons_Manager::try_get_icon_html($icon, array('aria-hidden' => 'true')) .
        '</span>' .
        '<span class="jltma-toggle-icon-active">' .
            Icons_Manager::try_get_icon_html($active_icon, array('aria-hidden' => 'true')) .
        '</span>';
    }

    /**
     * The close control that sits inside a Popup or Offcanvas panel.
     *
     * The toggle itself stays where it was rendered, in the page -- behind the
     * overlay for a popup, off to the side for an offcanvas -- so a panel needs
     * a way out of its own. Built from the Popup / Offcanvas Settings controls,
     * whose selectors expect the icon and the label as siblings here.
     *
     * @return string
     */
    private function get_dropdown_close($settings = array())
    {
        $close_type = !empty($settings['popup_offcanvas_close_type']) ? $settings['popup_offcanvas_close_type'] : 'icon';
        $icon = !empty($settings['close_icon']) ? $settings['close_icon'] : array();
        $inner = '';

        if (empty($icon['value'])) {
            $icon = array('value' => 'eicon-close', 'library' => 'eicon');
        }

        if ('text' !== $close_type) {
            $inner .= Icons_Manager::try_get_icon_html($icon, array('aria-hidden' => 'true'));
        }

        if ('icon' !== $close_type) {
            $label = !empty($settings['close_text']) ? $settings['close_text'] : __('Close', 'master-addons');
            $inner .= '<span class="' . esc_attr($this->get_widget_class()) . '__dropdown-close-label">' .
                esc_html($label) . '</span>';
        }

        // The row around it is what the Close Button > Alignment control moves
        // the button in: the button is sized by its own content, so there is
        // nothing in it for justify-content to place. The row is what the
        // stylesheet pins to the screen or to the canvas, and the button is
        // what carries the look, which keeps every other Close control -- gaps,
        // colours, padding -- pointed at the button itself.
        return '<div class="' . esc_attr($this->get_widget_class()) . '__dropdown-close-container">' .
            '<div class="' . esc_attr($this->get_widget_class()) . '__dropdown-close" role="button" tabindex="0" aria-label="' .
            esc_attr__('Close menu', 'master-addons') . '">' . $inner . '</div>' .
        '</div>';
    }

    /**
     * The hamburger's menu, with the parts its Type asks for around it.
     *
     * Default drops the list in place under the toggle; Popup floats it over the
     * page and Offcanvas slides it in from the side. The list itself is the
     * panel in all three -- ma-navmenu.scss reads the type class off the nav --
     * and the two that cover the page get a close control and an overlay as its
     * siblings.
     *
     * Deliberately no wrapper around the list: every style control in this
     * widget addresses items as __main > ul > li, so an element between the two
     * would quietly drop the whole Style tab on the floor.
     *
     * @return string
     */
    private function get_dropdown_parts($menu_html, $settings, $dropdown_type)
    {
        if ('popup' !== $dropdown_type && 'offcanvas' !== $dropdown_type) {
            return $menu_html;
        }

        return $this->get_dropdown_close($settings) .
            $menu_html .
            '<div class="' . esc_attr($this->get_widget_class()) . '__overlay" aria-hidden="true"></div>';
    }

    /**
     * The widths Elementor treats as tablet and mobile.
     *
     * Read from Elementor itself so a site that has customised its breakpoints
     * gets the layout swapped where its own responsive controls swap, and only
     * falls back to Elementor's defaults when the manager is not there.
     *
     * @return array
     */
    private function get_layout_breakpoints()
    {
        $breakpoints = array(
            'mobile' => 767,
            'tablet' => 1024,
        );

        if (!class_exists('\Elementor\Plugin') || empty(\Elementor\Plugin::$instance->breakpoints)) {
            return $breakpoints;
        }

        $active = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();

        foreach (array_keys($breakpoints) as $device) {
            if (isset($active[$device]) && method_exists($active[$device], 'get_value')) {
                $value = (int) $active[$device]->get_value();

                if ($value > 0) {
                    $breakpoints[$device] = $value;
                }
            }
        }

        return $breakpoints;
    }

    /**
     * Put the layout for the width in view on the nav before the browser paints.
     *
     * The nav is rendered with its desktop classes -- one markup for every
     * width -- and ma-navmenu.js swaps them for the width in view. That swap
     * runs on Elementor's frontend init, which is after the first paint: a menu
     * that is a hamburger on a phone is painted as the desktop bar first and
     * then visibly snaps into the hamburger. That flash is what this removes.
     *
     * The script sits right after the </nav> and is not deferred, so it runs
     * while the parser is still on the element and the first paint is already
     * the right layout. It makes exactly the swap jltmaApplyLayout() makes and
     * writes the same data-jltma-active-* markers, so when the widget script
     * runs it finds the layout already in force and changes nothing.
     *
     * @return string
     */
    private function get_layout_boot_script()
    {
        $breakpoints = $this->get_layout_breakpoints();

        // One line of JavaScript per entry, so the script reads top to bottom
        // as it runs. Kept out of a heredoc: WordPress's coding standards do
        // not allow one.
        $script = array(
            '(function () {',
            '    // The nav this script was printed after. An optimiser that has',
            '    // moved the script, and the editor\'s re-render (jQuery evaluates',
            '    // the script detached), leave currentScript null: fall back to',
            '    // the last nav nothing has stamped a layout on yet.',
            '    var nav = document.currentScript ? document.currentScript.previousElementSibling : null;',
            '',
            '    if (!nav || String(nav.className).indexOf("jltma-nav-menu__container") === -1) {',
            '        var pending = document.querySelectorAll(".jltma-nav-menu__container:not([data-jltma-active-layout])");',
            '',
            '        nav = pending.length ? pending[pending.length - 1] : null;',
            '    }',
            '',
            '    if (!nav || !nav.classList) {',
            '        return;',
            '    }',
            '',
            '    var LAYOUTS = ["horizontal", "vertical", "dropdown"];',
            '    var VERTICAL_TYPES = ["normal", "toggle", "accordion", "side"];',
            '    var DROPDOWN_TYPES = ["default", "popup", "offcanvas"];',
            '',
            '    var width = document.documentElement.clientWidth;',
            sprintf(
                '    var device = width <= %d ? "mobile" : (width <= %d ? "tablet" : "desktop");',
                (int) $breakpoints['mobile'],
                (int) $breakpoints['tablet']
            ),
            '',
            '    // The value saved for the device in view. get_device_values() has',
            '    // already cascaded these, so the device\'s own attribute is the',
            '    // value in force.',
            '    function deviceValue(name, allowed, fallback) {',
            '        var value = nav.getAttribute("data-" + name + "-" + device) ||',
            '            nav.getAttribute("data-" + name) ||',
            '            fallback;',
            '',
            '        return allowed.indexOf(value) === -1 ? fallback : value;',
            '    }',
            '',
            '    // Each layout, and each of the two types, is a whole open/close',
            '    // contract of its own, so only the one in force may be on the nav:',
            '    // swapped, not added to. The marker is what ma-navmenu.js reads to',
            '    // see the layout is already here.',
            '    function setActive(prefix, allowed, value) {',
            '        for (var i = 0; i < allowed.length; i++) {',
            '            nav.classList.remove("jltma-" + prefix + "-" + allowed[i]);',
            '        }',
            '',
            '        nav.classList.add("jltma-" + prefix + "-" + value);',
            '        nav.setAttribute("data-jltma-active-" + prefix, value);',
            '    }',
            '',
            '    // The parser paints what it has, so the nav may already be on',
            '    // screen in the layout it was rendered in. Transitioned, the swap',
            '    // below plays that as a panel fading out over the page; this turns',
            '    // the transitions off for it.',
            '    nav.classList.add("jltma-nav-layout-switching");',
            '',
            '    setActive("layout", LAYOUTS, deviceValue("layout", LAYOUTS, "horizontal"));',
            '    setActive("vertical-type", VERTICAL_TYPES, deviceValue("vertical-type", VERTICAL_TYPES, "normal"));',
            '    setActive("menu-dropdown-type", DROPDOWN_TYPES, deviceValue("dropdown-type", DROPDOWN_TYPES, "default"));',
            '',
            '    // Flushed with the transitions still off, so the layout in force is',
            '    // what the next transition starts from, and handed back a frame',
            '    // later -- everything the visitor opens after that animates as it',
            '    // always did.',
            '    void nav.offsetWidth;',
            '',
            '    (window.requestAnimationFrame || function (callback) {',
            '        return setTimeout(callback, 16);',
            '    })(function () {',
            '        nav.classList.remove("jltma-nav-layout-switching");',
            '    });',
            '}());',
        );

        return wp_get_inline_script_tag(implode("\n", $script));
    }

    /**
     * Render widget plain content.
     *
     * Save generated HTML to the database as plain content.
     *
     */
    public function render_plain_content()
    {
    }

    protected function render()
    {

        // Get available menus
        $available_menus          = $this->get_available_menus();
        if ( empty($available_menus) ) return;

        // Extension Settings
        $jltma_extensions_setting = Settings::get_extensions();
        if (!is_array($jltma_extensions_setting)) {
            $jltma_extensions_setting = array();
        }
        
        $settings = $this->get_active_settings();

        $layout = $settings['jltma_nav_layout'];

        $menu_index = $this->get_nav_menu_index();

        $this->main_menu_list_id = 'menu-' . (string)$menu_index . '-' . $this->get_id();

        $args = array(
            'echo' => false,
            'menu' => $settings['jltma_nav_menu'],
            'menu_class' => $this->get_widget_class() . '__container-inner',
            'menu_id' => $this->main_menu_list_id,
            'fallback_cb' => '__return_empty_string',
            'container' => ''
        );

        if (isset($jltma_extensions_setting['mega-menu']) && ($jltma_extensions_setting['mega-menu'] === 1)) {
            if (class_exists('MasterAddons\Modules\Display\MegaMenu\Megamenu_Nav_Walker')) {
                $args['walker'] = \MasterAddons\Modules\Display\MegaMenu\Megamenu_Nav_Walker::get_instance();
            }
        }

        add_filter('nav_menu_link_attributes', array($this, 'get_link_classes'), 10, 4);
        add_filter('walker_nav_menu_start_el', array($this, 'set_menu_dropdown_icon'), 10, 4);

        $menu_html = wp_nav_menu($args);

        // Scoped to this widget's menu -- left hooked it would also stamp arrows
        // on every other nav menu rendered later on the page.
        remove_filter('walker_nav_menu_start_el', array($this, 'set_menu_dropdown_icon'), 10);

        if( empty($menu_html) ) return;

        $layouts = $this->get_device_layouts();

        // Attributes Inject
        $menu_classes = array(
            $this->get_widget_class() . '__main',
            $this->get_widget_class() . '__container',
            'jltma-layout-' . esc_attr($layout),
            // Off until the layout for the width in view is on the nav: the
            // parser paints what it has, and the boot script's swap would
            // otherwise be transitioned -- a popup fading out over the page on
            // every load. Both the boot script and ma-navmenu.js drop it a
            // frame after the swap, so an optimiser that eats the inline script
            // still leaves the menu with its transitions.
            'jltma-nav-layout-switching',
        );

        // The hamburger's Type (Default / Popup / Offcanvas) decides how the
        // menu opens, so the class goes on the nav as well as on the panel --
        // the toggle, the overlay and the open state are all read from here.
        if ($this->has_hamburger_layout()) {
            $dropdown_types = $this->get_device_values('jltma_dropdown_menu_type', 'default');
            $menu_classes[] = 'jltma-menu-dropdown-type-' . esc_attr($dropdown_types['desktop']);

            // The close control and the overlay belong to Popup and Offcanvas.
            // A menu that is either of them at any width carries them at every
            // width, since only the class swaps as breakpoints are crossed.
            $panel_type = in_array('offcanvas', $dropdown_types, true) || in_array('popup', $dropdown_types, true)
                ? 'popup'
                : 'default';
            $menu_html = $this->get_dropdown_parts($menu_html, $settings, $panel_type);
        }

        // The vertical layout has four sub types (normal / toggle / accordion /
        // side) and each one gets its own open-close contract in the stylesheet
        // and its own click behaviour in ma-navmenu.js. Without this class every
        // sub level of a vertical menu falls back to the absolutely positioned
        // "always visible" base rule and they all stack on top of each other.
        // Rendered whenever any width is vertical; the rules that read it are
        // nested under the vertical layout class, which only one width carries
        // at a time.
        if (in_array('vertical', $layouts, true)) {
            $vertical_types = $this->get_device_values('vertical_menu_type', 'normal');
            $menu_classes[] = 'jltma-vertical-type-' . esc_attr($vertical_types['desktop']);
        }

        $this->add_render_attribute('main-menu', 'class', $menu_classes);

        // The layout in force is a class on the nav, and only one of the three
        // applies at a time -- ma-navmenu.js swaps it as the width crosses a
        // breakpoint, reading the saved values from here. Which means a menu
        // still renders as its desktop layout with no script at all.
        // Both Type controls are responsive too, and each one is its own
        // behaviour -- an accordion is not a flyout, an offcanvas canvas is not
        // a list dropping in place -- so they are swapped the same way.
        $vertical_types = $this->get_device_values('vertical_menu_type', 'normal');
        $dropdown_types = $this->get_device_values('jltma_dropdown_menu_type', 'default');

        $this->add_render_attribute('main-menu', array(
            'data-layout'                => $layouts['desktop'],
            'data-layout-tablet'         => $layouts['tablet'],
            'data-layout-mobile'         => $layouts['mobile'],
            'data-vertical-type'         => $vertical_types['desktop'],
            'data-vertical-type-tablet'  => $vertical_types['tablet'],
            'data-vertical-type-mobile'  => $vertical_types['mobile'],
            'data-dropdown-type'         => $dropdown_types['desktop'],
            'data-dropdown-type-tablet'  => $dropdown_types['tablet'],
            'data-dropdown-type-mobile'  => $dropdown_types['mobile'],
        ));

        $hamburger = $this->get_toggle();

        echo '<nav ' . $this->get_render_attribute_string('main-menu') . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor safe attribute string
            $hamburger . $menu_html . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_nav_menu output
        '</nav>' .
        $this->get_layout_boot_script(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_inline_script_tag() output
    }
}
