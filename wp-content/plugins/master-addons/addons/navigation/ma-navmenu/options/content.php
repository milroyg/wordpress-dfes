<?php
// File: master-addons/addons/navigation/ma-navmenu/options/content.php

namespace MasterAddons\Addons\Navmenu\Options;

use \Elementor\Controls_Manager;
use MasterAddons\Inc\Classes\Helper;

// Add other necessary use statements

if (!defined('ABSPATH')) {
    exit;
}

class Content {

    private $widget;
    private $widget_selector;
    private $menus;

    public function __construct($widget, $widget_selector, $menus) {
        $this->widget = $widget;
        $this->widget_selector = $widget_selector;
        $this->menus = $menus;
        $this->register_controls();
    }

    /**
     * A condition that holds when a responsive control's value on one device --
     * the value actually in force there, inherited from wider devices when the
     * device has none of its own -- is the given one.
     *
     * This is what lets a control that belongs to one device be hidden on
     * another: paired with device_args below, the Type shown beside a vertical
     * desktop is the vertical one, and the Type shown beside a hamburger phone
     * is the hamburger one, rather than both at once.
     *
     * @return array
     */
    private function device_condition($name, $value, $device = 'desktop', $operator = '===') {
        $desktop = array(
            'relation' => 'and',
            'terms' => array(array('name' => $name, 'operator' => $operator, 'value' => $value)),
        );

        if ('desktop' === $device) {
            return $desktop;
        }

        // Nothing saved for a device means it inherits, so the device's own
        // value only counts once it has one -- otherwise the question is passed
        // up to the device it inherits from. Without that guard an empty value
        // would answer a "is not" test on its own.
        $tablet = array(
            'relation' => 'or',
            'terms' => array(
                array(
                    'relation' => 'and',
                    'terms' => array(
                        array('name' => $name . '_tablet', 'operator' => '!==', 'value' => ''),
                        array('name' => $name . '_tablet', 'operator' => $operator, 'value' => $value),
                    ),
                ),
                array(
                    'relation' => 'and',
                    'terms' => array(
                        array('name' => $name . '_tablet', 'operator' => '===', 'value' => ''),
                        $desktop,
                    ),
                ),
            ),
        );

        if ('tablet' === $device) {
            return $tablet;
        }

        return array(
            'relation' => 'or',
            'terms' => array(
                array(
                    'relation' => 'and',
                    'terms' => array(
                        array('name' => $name . '_mobile', 'operator' => '!==', 'value' => ''),
                        array('name' => $name . '_mobile', 'operator' => $operator, 'value' => $value),
                    ),
                ),
                array(
                    'relation' => 'and',
                    'terms' => array(
                        array('name' => $name . '_mobile', 'operator' => '===', 'value' => ''),
                        $tablet,
                    ),
                ),
            ),
        );
    }

    /**
     * The same, for a set of controls that all have to hold on one device.
     * Each entry is array($name, $value) or array($name, $value, $operator).
     *
     * @return array
     */
    private function device_conditions($pairs, $device = 'desktop') {
        $terms = array();

        foreach ($pairs as $pair) {
            $operator = isset($pair[2]) ? $pair[2] : '===';
            $terms[] = $this->device_condition($pair[0], $pair[1], $device, $operator);
        }

        return array('relation' => 'and', 'terms' => $terms);
    }

    /**
     * True when some device satisfies all of the given pairs.
     *
     * For a section, which cannot be responsive: it belongs to whichever width
     * uses it, so it is offered as long as one of them does.
     *
     * @return array
     */
    private function any_device_conditions($pairs) {
        return array(
            'relation' => 'or',
            'terms' => array(
                $this->device_conditions($pairs, 'desktop'),
                $this->device_conditions($pairs, 'tablet'),
                $this->device_conditions($pairs, 'mobile'),
            ),
        );
    }

    /**
     * The per device conditions for a control that belongs to one layout, ready
     * to hand to add_responsive_control()'s device_args.
     *
     * @return array
     */
    private function device_args_for($name, $value) {
        return $this->device_args_for_all(array(array($name, $value)));
    }

    /**
     * device_args for a control that belongs to a combination of settings --
     * the Side type's Position, which is only of use on a vertical menu of that
     * type, at the width being edited.
     *
     * @return array
     */
    private function device_args_for_all($pairs) {
        return array(
            'tablet' => array('conditions' => $this->device_conditions($pairs, 'tablet')),
            'mobile' => array('conditions' => $this->device_conditions($pairs, 'mobile')),
        );
    }

    public function register_controls() {
        $this->general_section();
        $this->indicator_icon_section();
        $this->toggle_switch_section();
        $this->popup_offcanvas_section();
    }

    private function general_section() {
        $this->widget->start_controls_section(
            'jltma_content_tab',
            array('label' => __('General', 'master-addons'))
        );

       if (!empty($this->menus)) {

            $ids = array_keys($this->menus);
            $default = $ids[0];

            /* translators: Navigation Menu widget Select Menu control description. %s: Menus screen link */
            $nav_menu_description = sprintf(__('Add or otherwise manage menus in %s.', 'master-addons' ), sprintf(
                '<a href="%2$s" target="_blank">%1$s</a>',
                __('Menus screen', 'master-addons' ),
                admin_url('nav-menus.php')
            ));

            $this->widget->add_control(
                'jltma_nav_menu',
                array(
                    'label'        => __('Select Menu', 'master-addons' ),
                    'type'         => Controls_Manager::SELECT,
                    'options'      => $this->menus,
                    'default'      => $default,
                    'save_default' => true,
                    'separator'    => 'after',
                    'description'  => $nav_menu_description,
                )
            );
        } else {
            /* translators: Navigation Menu widget no menus notice. %s: Menus screen link */
            $no_menu_message_part = sprintf(__('Go to the Menus screen to %s', 'master-addons' ), sprintf(
                '<a href="%2$s" target="_blank">%1$s</a>',
                __('Create One', 'master-addons' ),
                admin_url('nav-menus.php?action=edit&menu=0')
            ));
            $no_menu_message = sprintf(
                '<strong>%1$s</strong><br>%2$s',
                __('There are no menus in your site.', 'master-addons' ),
                $no_menu_message_part
            );

            $this->widget->add_control(
                'jltma_nav_menu',
                array(
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => $no_menu_message,
                    'separator'       => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                )
            );
        }

        // Define layout options - Pro version overrides via filter
        $layout_options = apply_filters('master_addons/addons/navmenu/layout', array(
            'horizontal'       => array('title' => __('Horizontal', 'master-addons')),
            'vertical'         => array('title' => __('Vertical', 'master-addons')),
            'jltma-navmenu-pro' => array('title' => __('Hamburger (Pro)', 'master-addons')),
        ));

        $this->widget->add_responsive_control(
            'jltma_nav_layout',
            array(
                'label'              => __('Layout', 'master-addons'),
                'type'               => Controls_Manager::SELECT,
                'options'            => $layout_options,
                'default'            => 'horizontal',
                'mobile_default'     => 'dropdown',
                'label_block'        => false,
                'frontend_available' => true,
                'description'        => Helper::upgrade_to_pro('Hamburger Layout available on'),
            )
        );

        // Horizontal - Content Alignment
        $this->widget->add_responsive_control(
            'jltma_nav_alignment',
            [
                'label'        => __('Alignment', 'master-addons' ),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => Helper::jltma_content_flex_alignments(),
                'label_block'  => true,
                'render_type'  => 'template',
                'default'      => 'flex-start',
                'selectors'    => [
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul' => 'justify-content: {{VALUE}};',
                ],
                'conditions'  => $this->device_condition('jltma_nav_layout', 'horizontal'),
                'device_args' => $this->device_args_for('jltma_nav_layout', 'horizontal'),
            ]
        );

        // Dropdown menu type control - Pro version overrides via filter
        $dropdown_menu_type_options = apply_filters('master_addons/addons/navmenu/dropdown_type', array(
            'jltma-dropdown-pro-1' => [
                'title'       => __('Default (Pro)', 'master-addons'),
                'description' => 'Menu drops in place under the hamburger',
            ],
            'jltma-dropdown-pro-2' => [
                'title'       => __('Popup (Pro)', 'master-addons'),
                'description' => 'Menu opens in a popup',
            ],
            'jltma-dropdown-pro-3' => [
                'title'       => __('Offcanvas (Pro)', 'master-addons'),
                'description' => 'Menu slides in from the side of the screen',
            ],
        ));

        $this->widget->add_responsive_control(
            'jltma_dropdown_menu_type',
            [
                'label'              => __('Type', 'master-addons'),
                'type'               => Controls_Manager::SELECT,
                'options'            => $dropdown_menu_type_options,
                'default'            => 'default',
                'label_block'        => false,
                'frontend_available' => true,
                'conditions'         => $this->device_condition('jltma_nav_layout', 'dropdown'),
                'device_args'        => $this->device_args_for('jltma_nav_layout', 'dropdown'),
                'description'        => Helper::upgrade_to_pro('Hamburger Types available on'),
            ]
        );

        $this->widget->add_responsive_control(
            'vertical_menu_type',
            array(
                'label' => __('Type', 'master-addons' ),
                'type'  => Controls_Manager::SELECT,
                'options' => array(
                    'normal' => array(
                        'title' => __('Normal', 'master-addons' ),
                        'description' => 'Dropdown on the side of the menu',
                    ),
                    'toggle' => array(
                        'title' => __('Toggle', 'master-addons' ),
                        'description' => 'Toggle view menu',
                    ),
                    'accordion' => array(
                        'title' => __('Accordion', 'master-addons' ),
                        'description' => 'Accordion view menu',
                    ),
                    'side' => array(
                        'title' => __('Side', 'master-addons' ),
                        'description' => 'Vertical menu on the side of the page',
                    ),
                ),
                'default' => 'normal',
                'label_block' => false,
                'frontend_available' => true,
                'conditions' => $this->device_condition('jltma_nav_layout', 'vertical'),
                'device_args' => $this->device_args_for('jltma_nav_layout', 'vertical'),
            )
        );

        $this->widget->add_control(
            'side_description',
            array(
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('The menu is pinned to the side of the page. Only the main menu items sit in the bar; their submenus open in a panel beside it.', 'master-addons' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'vertical'),
                    array('vertical_menu_type', 'side'),
                )),
            )
        );



        // Which edge the Side bar is pinned to, per width: a bar that is on the
        // left of a desktop can be on the right of a phone, and each width's
        // control is shown only where that width is actually a Side menu.
        $side_pairs = array(
            array('jltma_nav_layout', 'vertical'),
            array('vertical_menu_type', 'side'),
        );

        $this->widget->add_responsive_control(
            'side_menu_position',
            array(
                'label' => __('Position', 'master-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'master-addons' ),
                        'icon' => 'eicon-h-align-left',
                    ),
                    'right' => array(
                        'title' => __('Right', 'master-addons' ),
                        'icon' => 'eicon-h-align-right',
                    ),
                ),
                'default' => 'left',
                'toggle' => false,
                'render_type' => 'template',
                'prefix_class' => 'jltma-side-position%s-',
                'frontend_available' => true,
                'conditions' => $this->device_conditions($side_pairs),
                'device_args' => $this->device_args_for_all($side_pairs),
            )
        );
        
        $this->widget->end_controls_section();
    }

    private function indicator_icon_section() {
         $this->widget->start_controls_section(
            'jltma_indicator_icon_tab',
            array('label' => __('Indicator Icon', 'master-addons'))
        );

        // Start of Dropdown Indicator
        $this->widget->add_control(
            'indicator_main_popover',
            array(
                'label' => esc_html__('Show Icon', 'master-addons'),
                'type' => Controls_Manager::CHOOSE,
                'render_type' => 'template',
                'options' => array(
                    'show' => array(
                        'title' => __('Show', 'master-addons'),
                        'icon' => 'eicon-check',
                    ),
                    'hide' => array(
                        'title' => __('Hide', 'master-addons'),
                        'icon' => 'eicon-close',
                    ),
                ),
                'default' => 'show',
                'toggle' => false,
            )
        );
        
        // Icon selector (common - outside tabs)
        $this->widget->add_control(
            'indicator_main',
            array(
                'label' => __('Dropdown Icon', 'master-addons'),
                'type' => Controls_Manager::ICONS,
                'render_type' => 'template',
                'fa4compatibility' => 'icon',
                'recommended' => array(
                    'fa-solid' => array(
                        'angle-down',
                        'chevron-down',
                        'caret-down',
                        'arrow-down',
                        'circle-chevron-down',
                        'square-caret-down',
                    ),
                    'fa-regular' => array(
                        'circle-down',
                        'square-caret-down',
                    ),
                ),
                'default' => array(
                    'value' => 'simple-line-icons icon-arrow-down',
                    'library' => 'simple-line-icons',
                ),
                'condition' => array(
                    'indicator_main_popover' => 'show',
                ),
            )
        );

        // Submenu Indicator Icon
        $this->widget->add_control(
            'jltma_submenu_indicator_icon_heading', // Adds your plugin's unique prefix
            [
                'label' => __( 'Submenu Indicator Icon', 'master-addons' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->widget->add_control(
            'jltma_indicator_submenu_icon',
            array(
                'label' => esc_html__('Show Submenu Icon', 'master-addons'),
                'type' => Controls_Manager::CHOOSE,
                'render_type' => 'template',
                'options' => array(
                    'show' => array(
                        'title' => __('Show', 'master-addons'),
                        'icon' => 'eicon-check',
                    ),
                    'hide' => array(
                        'title' => __('Hide', 'master-addons'),
                        'icon' => 'eicon-close',
                    ),
                ),
                'default' => 'show',
                'toggle' => false,
            )
        );

        $this->widget->add_control(
            'indicator_submenu',
            array(
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'recommended' => array(
                    'fa-solid' => array(
                        'angle-down',
                        'chevron-down',
                        'caret-down',
                        'arrow-down',
                        'long-arrow-alt-down',
                        'chevron-circle-down',
                        'arrow-circle-down',
                        'angle-right',
                        'chevron-right',
                        'caret-right',
                        'arrow-right',
                        'long-arrow-alt-right',
                        'chevron-circle-right',
                        'arrow-circle-right',
                    ),
                    'fa-regular' => array(
                        'arrow-alt-circle-down',
                        'arrow-alt-circle-right',
                    ),
                ),
                'default' => array(
                    'value' => 'simple-line-icons icon-arrow-down',
                    'library' => 'simple-line-icons',
                ),
                'file' => '',
                'condition' => array('jltma_indicator_submenu_icon' => 'show'),
            )
        );

        $this->widget->end_controls_section();
    }

    // Add other methods for each section
    private function toggle_switch_section() {
        $this->widget->start_controls_section(
            'section_dropdown_toggle',
            array(
                'label' => __('Hamburger Menu', 'master-addons' ),
                // Sections cannot be responsive, so this one is offered as long
                // as some width shows the menu as a hamburger -- the width being
                // edited may not be the one that needs it.
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                )),
            )
        );

        $this->widget->add_responsive_control(
            'toggle_align',
            array(
                'label' => __('Alignment', 'master-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'master-addons' ),
                        'icon' => 'fa fa-align-left',
                    ),
                    'center' => array(
                        'title' => __('Center', 'master-addons' ),
                        'icon' => 'fa fa-align-center',
                    ),
                    'right' => array(
                        'title' => __('Right', 'master-addons' ),
                        'icon' => 'fa fa-align-right',
                    ),
                    'stretch' => array(
                        'title' => __('Justified', 'master-addons' ),
                        'icon' => 'fa fa-align-justify',
                    ),
                ),
                'default' => 'right',
                'toggle' => true,
                'label_block' => false,
                'selectors_dictionary' => array(
                    'left' => 'flex-start;',
                    'center' => 'center;',
                    'right' => 'flex-end;',
                    'stretch' => 'stretch',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle-container' => 'align-items: {{VALUE}};',
                ),
            )
        );

        $this->widget->add_control(
            'dropdown_toggle_type',
            array(
                'label' => __('Type', 'master-addons' ),
                'type' => 'jltma-choose-text',
                'options' => array(
                    'icon' => array(
                        'title' => __('Icon', 'master-addons' ),
                        'description' => 'Switch has only icon',
                    ),
                    'text' => array(
                        'title' => __('Text', 'master-addons' ),
                        'description' => 'Switch has only text',
                    ),
                    'both' => array(
                        'title' => __('Both', 'master-addons' ),
                        'description' => 'Switch has icon and text',
                    ),
                ),
                'default' => 'icon',
                'label_block' => false,
                'separator' => 'before',
            )
        );

        $this->widget->add_control(
            'toggle_view',
            array(
                'label' => __('View', 'master-addons' ),
                'type' => 'jltma-choose-text',
                'options' => array(
                    'default' => array('title' => __('Default', 'master-addons' )),
                    'stacked' => array('title' => __('Stacked', 'master-addons' )),
                    'framed' => array('title' => __('Framed', 'master-addons' )),
                ),
                'default' => 'stacked',
                'label_block' => false,
                'prefix_class' => 'jltma-toggle-view-',
            )
        );

        $this->widget->add_control(
            'toggle_shape',
            array(
                'label' => __('Shape', 'master-addons' ),
                'type' => 'jltma-choose-text',
                'options' => array(
                    'square' => array('title' => __('Square', 'master-addons' )),
                    'circle' => array('title' => __('Circle', 'master-addons' )),
                ),
                'default' => 'square',
                'label_block' => false,
                'prefix_class' => 'jltma-toggle-shape-',
                'condition' => array(
                    'toggle_view!' => 'default',
                    'dropdown_toggle_type' => 'icon',
                    'toggle_align!' => 'stretch',
                ),
            )
        );

        $this->widget->start_controls_tabs(
            'tabs_dropdown_toggle_icon',
            array('condition' => array('dropdown_toggle_type!' => 'text'))
        );

        $this->widget->start_controls_tab(
            'tab_dropdown_toggle_icon_normal',
            array('label' => __('Normal', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_toggle_icon',
            array(
                'label' => __('Icon', 'master-addons' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'recommended' => array(
                    'fa-solid' => array(
                        'align-justify',
                        'hamburger',
                        'list',
                    ),
                ),
                'default' => array(
                    'value' => 'eicon-menu-bar',
                    'library' => 'eicon',
                ),
                'file' => '',
                'show_label' => false,
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_dropdown_toggle_icon_active',
            array('label' => __('Active', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_toggle_icon_active',
            array(
                'label' => __('Icon', 'master-addons' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'recommended' => array(
                    'fa-solid' => array(
                        'times',
                        'times-circle',
                    ),
                    'fa-regular' => array(
                        'times-circle',
                    ),
                ),
                'file' => '',
                'show_label' => false,
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();

        $this->widget->add_control(
            'dropdown_toggle_text',
            array(
                'label' => __('Text', 'master-addons' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Menu', 'master-addons' ),
                'separator' => 'before',
                'condition' => array('dropdown_toggle_type!' => 'icon'),
            )
        );

        $this->widget->add_responsive_control(
            'toggle_text_icon_position',
            array(
                'label' => __('Position', 'master-addons' ),
                'type' => 'jltma-choose-text',
                'description' => __('Will be applied only if Justified Alignment is chosen.', 'master-addons' ),
                'options' => array(
                    'central' => array(
                        'title' => __('Central', 'master-addons' ),
                    ),
                    'on-sides' => array(
                        'title' => __('On Sides', 'master-addons' ),
                    ),
                ),
                'default' => 'central',
                'label_block' => false,
                'prefix_class' => 'jltma-toggle-text-icon%s-position-',
                'condition' => array('dropdown_toggle_type' => 'both'),
            )
        );

        $this->widget->add_control(
            'dropdown_toggle_gap',
            array(
                'label' => __('Horizontal Gap', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle > span.jltma-toggle-icon-active + span' => 'margin-left: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array('dropdown_toggle_type' => 'both'),
            )
        );

        $this->widget->end_controls_section();
    }

    private function popup_offcanvas_section() {
        $this->widget->start_controls_section(
            'section_dropdown_popup_offcanvas',
            array(
                'label' => __('Popup / Offcanvas Settings', 'master-addons' ),
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'default', '!=='),
                )),
            )
        );

        $this->widget->add_control(
            'offcanvas_position',
            array(
                'label' => __('Position', 'master-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'master-addons' ),
                        'icon' => 'eicon-h-align-left',
                    ),
                    'right' => array(
                        'title' => __('Right', 'master-addons' ),
                        'icon' => 'eicon-h-align-right',
                    ),
                ),
                'default' => 'right',
                'toggle' => false,
                'prefix_class' => 'jltma-offcanvas-position-',
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'offcanvas'),
                )),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_offcanvas_width',
            array(
                'label' => __('Canvas Width', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 100,
                        'max' => 1000,
                    ),
                    '%' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                    'vw' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                    'vh' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'size_units' => array(
                    'px',
                    '%',
                    'vw',
                    'vh',
                ),
                'default' => array(
                    'unit' => 'px',
                    'size' => 300,
                ),
                'tablet_default' => array(
                    'unit' => '%',
                    'size' => 40,
                ),
                'mobile_default' => array(
                    'unit' => '%',
                    'size' => 100,
                ),
                'selectors' => array(
                    // The close row is pinned to the same edge and has to be as
                    // wide as the canvas, or Close Button > Alignment would
                    // place the button against the screen instead of the canvas.
                    '{{WRAPPER}} .jltma-menu-dropdown-type-offcanvas > ul' . $this->widget_selector . '__container-inner,
					{{WRAPPER}} .jltma-menu-dropdown-type-offcanvas > ' . $this->widget_selector . '__dropdown-close-container' => 'width: {{SIZE}}{{UNIT}};',
                ),
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'offcanvas'),
                )),
            )
        );

        $this->widget->add_control(
            'dropdown_offcanvas_hr',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'offcanvas'),
                )),
            )
        );

        $this->widget->add_control(
            'popup_offcanvas_close_heading',
            array(
                'label' => __('Close', 'master-addons' ),
                'type' => Controls_Manager::HEADING,
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'default', '!=='),
                )),
            )
        );

        $this->widget->add_control(
            'popup_offcanvas_close_type',
            array(
                'label' => __('Type', 'master-addons' ),
                'type' => 'jltma-choose-text',
                'options' => array(
                    'icon' => array(
                        'title' => __('Icon', 'master-addons' ),
                        'description' => 'Switch has only icon',
                    ),
                    'text' => array(
                        'title' => __('Text', 'master-addons' ),
                        'description' => 'Switch has only text',
                    ),
                    'both' => array(
                        'title' => __('Both', 'master-addons' ),
                        'description' => 'Switch has icon and text',
                    ),
                ),
                'default' => 'icon',
                'label_block' => false,
                'render_type' => 'template',
                'prefix_class' => 'jltma-close-type-',
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'default', '!=='),
                )),
            )
        );

        $this->widget->add_control(
            'popup_close_view',
            array(
                'label' => __('View', 'master-addons' ),
                'type' => 'jltma-choose-text',
                'options' => array(
                    'default' => array('title' => __('Default', 'master-addons' )),
                    'stacked' => array('title' => __('Stacked', 'master-addons' )),
                    'framed' => array('title' => __('Framed', 'master-addons' )),
                ),
                'default' => 'default',
                'label_block' => false,
                'prefix_class' => 'jltma-close-view-',
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'default', '!=='),
                )),
            )
        );

        $this->widget->add_control(
            'popup_close_shape',
            array(
                'label' => __('Shape', 'master-addons' ),
                'type' => 'jltma-choose-text',
                'options' => array(
                    'square' => array('title' => __('Square', 'master-addons' )),
                    'circle' => array('title' => __('Circle', 'master-addons' )),
                ),
                'default' => 'square',
                'label_block' => false,
                'prefix_class' => 'jltma-close-shape-',
                'condition' => array(
                    'jltma_nav_layout' => 'dropdown',
                    'jltma_dropdown_menu_type!' => 'default',
                    'popup_offcanvas_close_type' => 'icon',
                    'popup_close_view!' => 'default',
                ),
            )
        );

        $this->widget->add_control(
            'close_icon',
            array(
                'label' => __('Icon', 'master-addons' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'recommended' => array(
                    'fa-solid' => array(
                        'times',
                        'times-circle',
                    ),
                    'fa-regular' => array(
                        'times-circle',
                    ),
                ),
                'file' => '',
                'condition' => array(
                    'jltma_nav_layout' => 'dropdown',
                    'jltma_dropdown_menu_type!' => 'default',
                    'popup_offcanvas_close_type!' => 'text',
                ),
            )
        );

        $this->widget->add_control(
            'close_text',
            array(
                'label' => __('Text', 'master-addons' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Close', 'master-addons' ),
                'condition' => array(
                    'jltma_nav_layout' => 'dropdown',
                    'jltma_dropdown_menu_type!' => 'default',
                    'popup_offcanvas_close_type!' => 'icon',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'popup_close_gap',
            array(
                'label' => __('Gap Between', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close i + span' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close svg + span' => 'margin-left: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array(
                    'jltma_nav_layout' => 'dropdown',
                    'jltma_dropdown_menu_type!' => 'default',
                    'popup_offcanvas_close_type' => 'both',
                    'close_text!' => '',
                ),
            )
        );

        $this->widget->add_control(
            'overlay_close',
            array(
                'label' => __('Close With Click on Overlay', 'master-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => __('Close popup upon click/tap on overlay', 'master-addons' ),
                'default' => 'yes',
                'frontend_available' => true,
                'separator' => 'before',
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'default', '!=='),
                )),
            )
        );

        $this->widget->add_control(
            'esc_close',
            array(
                'label' => __('Close by ESC Button Click', 'master-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'default', '!=='),
                )),
            )
        );

        $this->widget->add_control(
            'disable_scroll',
            array(
                'label' => __('Disable scroll', 'master-addons' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,
                'conditions' => $this->any_device_conditions(array(
                    array('jltma_nav_layout', 'dropdown'),
                    array('jltma_dropdown_menu_type', 'default', '!=='),
                )),
            )
        );

        $this->widget->end_controls_section();
    }
}