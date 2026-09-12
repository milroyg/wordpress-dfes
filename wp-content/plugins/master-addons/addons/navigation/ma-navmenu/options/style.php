<?php

namespace MasterAddons\Addons\Navmenu\Options;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Border;
use MasterAddons\Inc\Classes\Animation;
use Elementor\Group_Control_Text_Shadow;
use \Elementor\Group_Control_Box_Shadow;
use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Controls\Group\JLTMA_Button_Background;

// Add other necessary use statements

if (!defined('ABSPATH')) {
    exit;
}

class Style {
    private $widget;
    private $widget_selector;

    public function __construct($widget, $widget_selector) {
        $this->widget = $widget;
        $this->widget_selector = $widget_selector;
        $this->register_controls();
    }

    public function register_controls() {
        $this->main_menu_item_section();
        $this->indicator_icon_section();
        $this->dropdown_menu_section();
        $this->dropdown_menu_item_section(); 
        $this->dropdown_submenu_section();
        $this->dropdown_submenu_item_section();
        $this->hamburger_menu_section();
        $this->popup_offcanvas_section();
        // $this->menu_condition_section();
    }

    private function main_menu_item_section() {
        $this->widget->start_controls_section(
            'section_style_main_menu',
            array(
                'label' => __('Main Menu Item', 'master-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->widget->add_responsive_control(
			'main_menu_item_padding',
			[
				'label'             => esc_html__('Padding', 'master-addons'),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => ['px', 'em', '%'],
                'default'           => [
                    'top'    => '10',
                    'right'  => '10',
                    'bottom' => '10',
                    'left'   => '10',
                    'unit'   => 'px',
                    'isLinked' => true,
                ],
				'selectors'         => [
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
				],
			]
		);

        $this->widget->add_responsive_control(
            'main_menu_item_space_between',
            array(
                'label' => __('Gap', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array('max' => 100),
                ),
                'devices' => array(
                    'desktop',
                    'tablet',
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector . '__main > ul' => 'gap: {{SIZE}}{{UNIT}}',
                ),
            )
        );

        $this->widget->add_responsive_control(
            // Not `menu_alignment`: that name belonged to the nav's own
            // alignment before 3.2.2 (now jltma_nav_alignment), and reusing it
            // for a different control would leave the migration unable to tell
            // an old page's value from a new one's.
            'jltma_main_menu_item_alignment',
            [
                'label'        => __('Alignment', 'master-addons' ),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => Helper::jltma_content_flex_alignments(),
                'render_type'  => 'template',
                'default'      => 'center',
                'selectors'    => [
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'justify-content: {{VALUE}}',
                ],
            ]
        );

        $this->widget->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'main_menu_typography',
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a'
            )
        );

        $this->widget->start_controls_tabs('tabs_menu_item_style');

        $this->widget->start_controls_tab(
            'tab_main_menu_item_normal',
            array('label' => __('Normal', 'master-addons' ))
        );

        $this->widget->add_control(
            'main_menu_item_color',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'color: {{VALUE}}'
                ]
            )
        );

        $this->widget->add_control(
            'jltma_menubar_background',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main.jltma-nav-menu__container > ul > li > a' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'main_menu_item_border',
                'label' => __('Border', 'master-addons' ),
                // 'exclude' => array('color'),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),
                        'default' => 'default',
                        // 'prefix_class' => 'jltma-main-menu-item-border-type-',
                        'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector . '__main> ul > li > a' => 'border-style: {{VALUE}};'
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                        'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'main_menu_item_border_radius',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'devices' => array(
                    'desktop',
                    'tablet',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'border-radius: {{SIZE}}{{UNIT}}',
                ),
                'separator' => 'before'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name' => 'main_menu_item_text_shadow',
                'fields_options' => array(
                    'text_shadow_type' => array('label' => __('Text Shadow', 'master-addons' )),
                ),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a'
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_main_menu_item_hover',
            array('label' => __('Hover', 'master-addons' ))
        );

        $this->widget->add_control(
            'main_menu_item_color_hover',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:hover' => 'color: {{VALUE}}'
                ),
            )
        );

        $this->widget->add_control(
            'main_menu_item_bg_hover',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main.jltma-nav-menu__container > ul > li > a:hover' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'main_menu_item_border_hover',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),
                        'default' => 'default',
                        'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector . '__main> ul > li > a:hover' => 'border-style: {{VALUE}};'
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                        'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:hover' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'main_menu_item_border_radius_hover',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'devices' => array(
                    'desktop',
                    'tablet',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:hover' => 'border-radius: {{SIZE}}{{UNIT}}',
                ),
                'separator' => "before"
            )
        );

        $this->widget->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name' => 'main_menu_item_text_shadow_hover',
                'fields_options' => array(
                    'text_shadow_type' => array('label' => __('Text Shadow', 'master-addons' )),
                ),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:hover'
            )
        );

        $this->widget->add_control(
            'main_menu_item_transition',
            array(
                'label' => __('Transition (ms)', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array('max' => 1000),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'transition: all {{SIZE}}ms',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_main_menu_item_active',
            array('label' => __('Active', 'master-addons' ))
        );

        $this->widget->add_control(
            'main_menu_item_color_active',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                   '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:focus' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->widget->add_control(
            'main_menu_item_bg_active',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:focus' => 'background-color: {{VALUE}};'
                ),
                'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'main_menu_item_border_active',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),
                        'default' => 'default',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:focus' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                              '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:focus' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                        'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:focus' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'main_menu_item_border_radius_active',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'separator' => 'before'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name' => 'main_menu_item_text_shadow_active',
                'fields_options' => array(
                    'text_shadow_type' => array('label' => __('Text Shadow', 'master-addons' )),
                ),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a:focus',
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();

        $this->widget->end_controls_section();
    }

    private function indicator_icon_section() {
        $this->widget->start_controls_section(
            'jltma_indicator_icon_tab__style',
            array(
                'label' => __('Indicator Icon', 'master-addons'), 
                'tab' => Controls_Manager::TAB_STYLE
            ),
        );

        // Position control (common - outside tabs)
        $this->widget->add_control(
            'icon_position',
            array(
                'label' => __('Position', 'master-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'master-addons'),
                        'icon' => 'eicon-h-align-left',
                    ),
                    'right' => array(
                        'title' => __('Right', 'master-addons'),
                        'icon' => 'eicon-h-align-right',
                    ),
                ),
                'default' => 'right',
                'toggle' => false,
                'render_type' => 'template',
                'prefix_class' => 'jltma-icon-position-',
                'condition' => array(
                    'indicator_main_popover' => 'show',
                ),
            )
        );

        // Gap Between control (common - outside tabs)
        $this->widget->add_responsive_control(
            'indicator_main_gap',
            array(
                'label' => __('Gap Between', 'master-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'default' => [
                    'size' => 6,
                    'unit' => 'px',
                ],
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a' => 'gap: {{SIZE}}{{UNIT}}',
                ),
                'condition' => array(
                    'indicator_main_popover' => 'show',
                ),
            )
        );

        // Size control (common - outside tabs)
        $this->widget->add_responsive_control(
            'indicator_main_size',
            array(
                'label' => __('Icon Size', 'master-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em'),
                'range' => array(
                    'px' => array(
                        'min' => 5,
                        'max' => 50,
                    ),
                    'em' => array(
                        'min' => 0.5,
                        'max' => 5,
                    ),
                ),
                'default' => [
                    'size' => 12,
                    'unit' => 'px',
                ],
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__arrow, 
                    {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-submenu-indicator, 
                    {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__main-item-text-wrap .jltma-nav-menu__arrow' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__arrow > svg, 
                    {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__main-item-text-wrap .jltma-nav-menu__arrow > svg' => 'width: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array(
                    'indicator_main_popover' => 'show',
                ),
            )
        );

        // Normal/Hover Tabs
        $this->widget->start_controls_tabs(
            'indicator_main_tabs__style',
            array(
                'condition' => array(
                    'indicator_main_popover' => 'show',
                ),
            )
        );

        // ===== NORMAL TAB =====
        $this->widget->start_controls_tab(
            'indicator_main_normal_tab__style',
            array(
                'label' => esc_html__('Normal', 'master-addons'),
            )
        );

        $this->widget->add_control(
            'indicator_main_color',
            array(
                'label' => __('Color', 'master-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__arrow' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__main-item-text-wrap .jltma-nav-menu__arrow' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'indicator_main_rotate',
            array(
                'label' => __('Rotate', 'master-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('deg'),
                'default' => array(
                    'unit' => 'deg',
                    'size' => 0,
                ),
                'range' => array(
                    'deg' => array(
                        'min' => -360,
                        'max' => 360,
                        'step' => 1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__arrow' => 'transform: rotate({{SIZE}}deg);',
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__main-item-text-wrap .jltma-nav-menu__arrow' => 'transform: rotate({{SIZE}}deg);',
                ),
            )
        );

        $this->widget->end_controls_tab();

        // ===== HOVER TAB =====
        $this->widget->start_controls_tab(
            'indicator_main_hover_tab',
            array(
                'label' => esc_html__('Hover', 'master-addons'),
            )
        );

        $this->widget->add_control(
            'indicator_main_color_hover',
            array(
                'label' => __('Color', 'master-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li:hover > a .jltma-nav-menu__arrow' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li:hover > a .jltma-nav-menu__main-item-text-wrap .jltma-nav-menu__arrow' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'indicator_main_rotate_hover',
            array(
                'label' => __('Rotate', 'master-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('deg'),
                'default' => array(
                    'unit' => 'deg',
                    'size' => 180,
                ),
                'range' => array(
                    'deg' => array(
                        'min' => -360,
                        'max' => 360,
                        'step' => 1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li:hover > a .jltma-nav-menu__arrow' => 'transform: rotate({{SIZE}}deg);',
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li:hover > a .jltma-nav-menu__main-item-text-wrap .jltma-nav-menu__arrow' => 'transform: rotate({{SIZE}}deg);',
                ),
            )
        );

        // Transition Duration (inside Hover tab)
        $this->widget->add_control(
            'indicator_main_transition_duration',
            array(
                'label' => __('Transition Duration', 'master-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => array(
                    'size' => 0.3,
                ),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 2,
                        'step' => 0.1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__arrow' => 'transition: transform {{SIZE}}s ease, color {{SIZE}}s ease, font-size {{SIZE}}s ease;',
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li > a .jltma-nav-menu__main-item-text-wrap .jltma-nav-menu__arrow' => 'transition: transform {{SIZE}}s ease, color {{SIZE}}s ease, font-size {{SIZE}}s ease;',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();
        // End of Dropdown Indicator

        // Start Submenu Indicator
        $this->widget->add_control(
            'jltma_submenu_indicator_icon_divider__style',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            )
        );

        $this->widget->add_control(
            'jltma_submenu_indicator_icon_heading__style', // Adds your plugin's unique prefix
            [
                'label' => __( 'Indicator Submenu Icon', 'master-addons' ),
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );

        $this->widget->add_control(
            'dropdown_icon_position',
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
                'prefix_class' => 'jltma-dropdown-icon-',
            )
        );

        $this->widget->add_responsive_control(
            'indicator_submenu_gap',
            array(
                'label' => __('Gap Between', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'default' => [
                    'size' => 6,
                    'unit' => 'px'
                ],
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector . '__main .jltma-menu-has-children > ul.jltma-dropdown .jltma-menu-has-children > a.jltma-nav-menu__dropdown-item-sub' => 'gap: {{SIZE}}{{UNIT}};'
                ),
                'condition' => array(
                    'jltma_indicator_submenu_icon' => 'show',
                    'indicator_submenu[value]!' => '',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'indicator_submenu_size',
            array(
                'label' => __('Size', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range' => array(
                    'px' => array('min' => 6, 'max' => 60),
                    'em' => array('min' => 0.3, 'max' => 4, 'step' => 0.1),
                ),
                'default' => [
                    'size' => 12,
                    'unit' => 'px'
                ],
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__arrow-sub' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__arrow-sub svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array(
                    'jltma_indicator_submenu_icon' => 'show',
                    'indicator_submenu[value]!' => '',
                ),
            )
        );


        $this->widget->start_controls_tabs(
            'indicator_submenu_tabs__style',
            array(
                'condition' => array(
                    'jltma_indicator_submenu_icon' => 'show',
                ),
            )
        );

        $this->widget->start_controls_tab(
            'indicator_submenu_normal_tab__style',
            array(
                'label' => esc_html__('Normal', 'master-addons'),
            )
        );

        $this->widget->add_control(
            'indicator_submenu_color',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__arrow-sub' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__arrow-sub svg' => 'fill: {{VALUE}};',
                ),
                'condition' => array(
                    'jltma_indicator_submenu_icon' => 'show',
                    'indicator_submenu[value]!' => '',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_submenu_indicator_rotate',
            array(
                'label' => __('Rotate', 'master-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('deg'),
                'default' => array(
                    'unit' => 'deg',
                    'size' => 0,
                ),
                'range' => array(
                    'deg' => array(
                        'min' => -360,
                        'max' => 360,
                        'step' => 1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-menu-has-children ul.jltma-dropdown > li.jltma-menu-has-children .jltma-nav-menu__arrow-sub' => 'transform: rotate({{SIZE}}deg);',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'indicator_submenu_hover_tab__style',
            array(
                'label' => esc_html__('Hover', 'master-addons'),
            )
        );

        $this->widget->add_control(
            'indicator_submenu_color_hover',
            array(
                'label' => __('Hover Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__arrow-sub:hover, {{WRAPPER}} li:hover > a ' . $this->widget_selector . '__arrow-sub' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__arrow-sub:hover svg, {{WRAPPER}} li:hover > a ' . $this->widget_selector . '__arrow-sub svg' => 'fill: {{VALUE}};',
                ),
                'condition' => array(
                    'jltma_indicator_submenu_icon' => 'show',
                    'indicator_submenu[value]!' => '',
                ),
            )
        );

        // TODO: currently remove it. we will better solution for this animation effect.
        // $this->widget->add_control(
        //     'indicator_submenu_animation',
        //     array(
        //         'label' => __('Animation', 'master-addons' ),
        //         'type' => Controls_Manager::SELECT,
        //         'options' => array(
        //             'none' => __('None', 'master-addons' ),
        //             'rotate-left' => __('Rotate Left', 'master-addons' ),
        //             'rotate-right' => __('Rotate Right', 'master-addons' ),
        //             'rotate-opposite' => __('Rotate Opposite', 'master-addons' ),
        //             'opacity' => __('Opacity', 'master-addons' ),
        //         ),
        //         'default' => 'none',
        //         'condition' => array(
        //             'indicator_submenu[value]!' => '',
        //             'jltma_indicator_submenu_icon' => 'show',
        //         ),
        //     )
        // );

        $this->widget->add_responsive_control(
            'jltma_submenu_indicator_rotate_hover',
            array(
                'label' => __('Rotate', 'master-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('deg'),
                'default' => array(
                    'unit' => 'deg',
                    'size' => 180,
                ),
                'range' => array(
                    'deg' => array(
                        'min' => -360,
                        'max' => 360,
                        'step' => 1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-menu-has-children ul.jltma-dropdown li.jltma-menu-has-children > a.jltma-nav-menu__dropdown-item-sub:hover .jltma-nav-menu__arrow-sub' => 'transform: rotate({{SIZE}}deg);',
                ),
            )
        );

        $this->widget->add_control(
            'submenu_hover_transition',
            array(
                'label' => __('Transition Duration', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'ms' => array(
                        'min' => 0,
                        'max' => 3000,
                    ),
                ),
                'default' => [
                    'unit' => 'ms',
                    'size' => 150,
                ],
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-menu-has-children ul.jltma-dropdown li.jltma-menu-has-children > a.jltma-nav-menu__dropdown-item-sub .jltma-nav-menu__arrow-sub' => 'transition: all {{SIZE}}ms;',
                ),
                'condition' => array(
                    'indicator_submenu[value]!' => '',
                    // 'indicator_submenu_animation!' => 'none',
                    // 'jltma_indicator_submenu_icon' => 'show',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();


        $this->widget->end_controls_section();
    }

    private function dropdown_menu_section() {
        $this->widget->start_controls_section(
            'section_style_main_menu_dropdown',
            array(
                'label' => __('Dropdown Menu', 'master-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_width',
            array(
                'label' => __('Dropdown Width', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 100,
                        'max' => 500,
                    ),
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown' => 'width: {{SIZE}}{{UNIT}};'
                    // '{{WRAPPER}} ' . $this->widget_selector . '__main.jltma-layout-horizontal > ul > li ul,
				    // {{WRAPPER}} ' . $this->widget_selector . '__main.jltma-layout-vertical.jltma-vertical-type-normal > ul > li ul' => 'width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_align',
            [
                'label'        => __('Alignment', 'master-addons' ),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => Helper::jltma_content_flex_alignments(),
                'default'      => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown li > a' => 'justify-content: {{VALUE}}',
                ]
            ]
        );

        $this->widget->add_control(
            'dropdown_bg_color',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu' => 'background-color: {{VALUE}}'
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_top_distance',
            array(
                'label' => __('Gap from Top', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => -30,
                        'max' => 70,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children > ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu' => 'margin-top: {{SIZE}}{{UNIT}}; --jltma-dropdown-gap: {{SIZE}}{{UNIT}};',
                ),
                 'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'dropdown_border',
                'label'          => __('Border', 'master-addons' ),
                'fields_options' => [
                    'border' => [
                        'options' => [
                            'none'    => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid'   => _x('Solid', 'Border Control', 'master-addons' ),
                            'double'  => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted'  => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed'  => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove'  => _x('Groove', 'Border Control', 'master-addons' ),
                        ],
                        'default'      => 'solid',
                        'selectors'    => [
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu' => 'border-style: {{VALUE}};'
                        ],
                    ],

                    'width' => [
                        'label'     => _x('Border Width', 'Border Control', 'master-addons' ),
                        'default'   => [
                            'top' => 1,
                            'right' => 1,
                            'bottom' => 1,
                            'left'  => 1,
                            'linked' => true,
                            'unit' => 'px',
                        ],
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                        ),
                        'condition' => [
                            'border!' => [
                                'none',
                                'default',
                            ],
                        ],
                    ],
                    'color' => [
                        'label'     => _x('Border Color', 'Border Control', 'master-addons' ),
                        'default'   => '#EEEDED',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu' => 'border-color: {{VALUE}};'
                        ),
                        'condition' => [
                            'border!' => [
                                'none',
                                'default',
                            ],
                        ],
                        'separator' => 'after'
                    ],
                ],
            ]
        );

        $this->widget->add_responsive_control(
            'dropdown_border_radius',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'default' => [
                    'top' => 4,
                    'right' => 4,
                    'bottom' => 4,
                    'left'  => 4,
                    'linked' => true,
                    'unit' => 'px'
                ],
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_padding',
            array(
                'label' => __('Padding', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                 'default' => [
                    'top' => 8,
                    'right' => 16,
                    'bottom' => 8,
                    'left' => 16,
                    'linked' => false,
                    'unit' => 'px'
                ],
                'selectors' => array(
                    // The horizontal halves are published as variables too: a
                    // panel's own padding is the distance its items sit inside
                    // it, which is exactly how far a level opening off one of
                    // those items would otherwise overlap it. ma-navmenu.scss
                    // adds it back.
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu' => 'padding-top: {{TOP}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; --jltma-panel-pad-right: {{RIGHT}}{{UNIT}}; --jltma-panel-pad-left: {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'dropdown_box_shadow',
                'exclude' => array('box_shadow_position'),
                'default' => array(
                    'horizontal' => 0,
                    'vertical' => 0,
                    'blur' => 20,
                    'spread' => -12,
                    'color' => 'rgba(0, 0, 0, 0.5)',
                ),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown, {{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.jltma-has-megamenu > ul.jltma-megamenu',
            )
        );


        $this->widget->end_controls_section();
    }

    private function dropdown_menu_item_section() {
        $this->widget->start_controls_section(
            'section_style_dropdown_item',
            array(
                'label' => __('Dropdown Menu Item', 'master-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->widget->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'dropdown_main_level_typography',
                'fields_options' => array(
                    'typography' => array(
                        'default' => 'custom',
                    ),
                    'font_family' => array(
                        'default' => '',
                    ),
                    'font_weight' => array(
                        'default' => '',
                    ),
                    'font_size' => array(
                        'default' => array(
                            'unit' => 'px',
                            'size' => 15,
                        ),
                    ),
                    'line_height' => array(
                        'default' => array(
                            'unit' => 'em',
                            'size' => 1.5,
                        ),
                    ),
                    'letter_spacing' => array(
                        'default' => array(
                            'unit' => 'px',
                            'size' => '',
                        ),
                    ),
                    'text_transform' => array(
                        'default' => '',
                    ),
                    'font_style' => array(
                        'default' => '',
                    ),
                    'text_decoration' => array(
                        'default' => '',
                    ),
                ),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul a',
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_item_space_main_between',
            array(
                'label' => __('Gap', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'default' => [
                    'size' => 6,
                    'unit' => 'px'
                ],
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown' => 'gap: {{SIZE}}{{UNIT}};',
                ),
                 'separator' => 'after'
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_menu_item_padding',
            array(
                'label' => __('Padding', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown li > a' => 'padding-top: {{TOP}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->start_controls_tabs('tabs_dropdown_main_level_style');

        $this->widget->start_controls_tab(
            'tab_dropdown_main_level_normal',
            array('label' => __('Normal', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_main_level_color',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li a' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_control(
            'dropdown_main_level_bg',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'dropdown_main_level_border',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                         'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                         'selectors' => array(
                              '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                        'separator' => 'after'
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_main_level_border_radius',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_dropdown_main_level_hover',
            array('label' => __('Hover', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_main_level_color_hover',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:hover' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_control(
            'dropdown_main_level_bg_hover',
            array(
                'label' => __('Item Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:hover' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'dropdown_main_level_border_hover',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        // 'prefix_class' => 'jltma-dropdown-main-level-border-type-',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:hover' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                         'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                         'selectors' => array(
                              '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:hover' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                        'separator' => 'after'
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_main_level_border_radius_hover',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_dropdown_main_level_active',
            array('label' => __('Active', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_main_level_color_active',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:focus' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_control(
            'dropdown_main_level_bg_active',
            array(
                'label' => __('Item Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:focus' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'dropdown_main_level_border_active',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        // 'prefix_class' => 'jltma-dropdown-main-level-border-type-',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:focus' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:focus' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                         'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                         'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:focus' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                        'separator' => 'after'
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_main_level_border_radius_active',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li > a:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();

        $this->widget->add_control(
            'heading_dropdown_divider',
            array(
                'label' => __('Divider', 'master-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->widget->add_control(
            'dropdown_divider_type',
            array(
                'label' => __('Divider Type', 'master-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'none' => __('None', 'master-addons' ),
                    'solid' => __('Solid', 'master-addons' ),
                    'double' => __('Double', 'master-addons' ),
                    'doted' => __('Doted', 'master-addons' ),
                    'dashed' => __('Dashed', 'master-addons' ),
                    'groove' => __('Groove', 'master-addons' ),
                ),
                'default' => 'none',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li:not(:first-child)' => 'border-top-style: {{VALUE}};',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_divider_size',
            array(
                'label' => __('Divider Size', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array('max' => 15),
                ),
                'default' => array('size' => 1),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li:not(:first-child)' => 'border-top-width: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array('dropdown_divider_type!' => 'none'),
            )
        );

        $this->widget->add_control(
            'dropdown_divider_color',
            array(
                'label' => __('Divider Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li.menu-item-has-children ul.jltma-dropdown > li:not(:first-child)' => 'border-top-color: {{VALUE}}',
                ),
                'condition' => array('dropdown_divider_type!' => 'none'),
            )
        );

        $this->widget->end_controls_section();
    }

    private function dropdown_submenu_section() {
        $this->widget->start_controls_section(
            'jltma_dropdown_submenu_section__style',
            array(
                'label' => __('Dropdown Submenu', 'master-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_position',
            array(
                'label' => __('Position', 'master-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'master-addons'),
                        'icon' => 'eicon-h-align-left',
                    ),
                    'right' => array(
                        'title' => __('Right', 'master-addons'),
                        'icon' => 'eicon-h-align-right',
                    ),
                ),
                'default' => 'right',
                'toggle' => false,
                'render_type' => 'template',
                'prefix_class' => 'jltma-dropdown-submenu-position-',
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_width',
            array(
                'label' => __('Width', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 100,
                        'max' => 500,
                    ),
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'width: {{SIZE}}{{UNIT}};'
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_align',
            [
                'label'        => __('Alignment', 'master-addons' ),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => Helper::jltma_content_flex_alignments(),
                'default'      => 'flex-start',
                'prefix_class' => 'jltma-dropdown-submenu-align-',
                'selectors'      => [
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'justify-content: {{VALUE}};'
                ]
            ]
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_bg_color',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'background-color: {{VALUE}};'
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_gap_left',
            array(
                'label' => __('Gap Left', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => -100,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'margin-left: {{SIZE}}{{UNIT}};',
                ),
                 'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'jltma_dropdown_submenu_border',
                'label'          => __('Border', 'master-addons' ),
                'fields_options' => [
                    'border' => [
                        'options' => [
                            'none'    => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid'   => _x('Solid', 'Border Control', 'master-addons' ),
                            'double'  => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted'  => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed'  => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove'  => _x('Groove', 'Border Control', 'master-addons' ),
                        ],
                        'default'      => 'default',
                        'selectors'    => [
                            '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'border-style: {{VALUE}};',
                        ],
                    ],
                    'width' => [
                        'label'     => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => [
                            'border!' => [
                                'none',
                                'default',
                            ],
                        ],
                    ],
                    'color' => [
                        'label'     => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'border-color: {{VALUE}};',
                        ),
                        'condition' => [
                            'border!' => [
                                'none',
                                'default',
                            ],
                        ],
                        'separator' => 'after'
                    ],
                ],
            ]
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_border_radius',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_padding',
            array(
                'label' => __('Padding', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'padding-top: {{TOP}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; --jltma-panel-pad-right: {{RIGHT}}{{UNIT}}; --jltma-panel-pad-left: {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'jltma_dropdown_submenu_box_shadow',
                'exclude' => array('box_shadow_position'),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown',
            )
        );

        $this->widget->end_controls_section();
    }

    private function dropdown_submenu_item_section() {
        $this->widget->start_controls_section(
            'jltma_section_style_dropdown_submenu_item',
            array(
                'label' => __('Dropdown Submenu Item', 'master-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );

        $this->widget->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'jltma_dropdown_submenu_typography',
                'selector' => '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown > li a',
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_item_gap',
            array(
                'label' => __('Gap', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown' => 'gap: {{SIZE}}{{UNIT}};',
                ),
                 'separator' => 'after'
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_item_padding',
            array(
                'label' => __('Padding', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'selectors' => array(
                      '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'padding-top: {{TOP}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->start_controls_tabs('jltma_tabs_dropdown_submenu__style');

        $this->widget->start_controls_tab(
            'jltma_tab_dropdown_submenu_normal',
            array('label' => __('Normal', 'master-addons' ))
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_color',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_bg',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'jltma_dropdown_submenu_item_border',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        // 'prefix_class' => 'jltma-dropdown-main-level-border-type-',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'border-style: {{VALUE}}',
                            // '{{WRAPPER}} ' . $this->widget_selector . '__main > ul > li ul > li > a' => 'border-style: {{VALUE}}',
                            // '{{WRAPPER}} ' . $this->widget_selector . '__main .sub-menu li a,
							// {{WRAPPER}} ' . $this->widget_selector . '__dropdown .sub-menu li a' => 'border-style: {{VALUE}};',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            // '{{WRAPPER}} ' . $this->widget_selector . '__main .sub-menu li a,
                            // {{WRAPPER}} ' . $this->widget_selector . '__dropdown .sub-menu li a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                         'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                         'selectors' => array(
                             '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                        'separator' => 'after'
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_item_border_radius',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'jltma_tab_dropdown_submenu_item_hover',
            array('label' => __('Hover', 'master-addons' ))
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_color_hover',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:hover' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_bg_hover',
            array(
                'label' => __('Item Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:hover' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'jltma_dropdown_submenu_item_border_hover',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),
                        'default' => 'default',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:hover' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                              '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                         'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                         'selectors' => array(
                               '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:hover' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                        'separator' => 'after'
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_item_border_radius_hover',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_transition_hover',
            array(
                'label' => __('Transition (ms)', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'ms' => array(
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ),
                ),
                'default' => array(
                    'unit' => 'ms',
                    'size' => 150,
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub' => 'transition: all {{SIZE}}ms',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'jltma_tab_dropdown_submenu_item_active',
            array('label' => __('Active', 'master-addons' ))
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_color_active',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:focus' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_bg_active',
            array(
                'label' => __('Item Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:focus' => 'background-color: {{VALUE}}',
                ),
                'separator' => 'after',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'jltma_dropdown_submenu_item_border_active',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        // 'prefix_class' => 'jltma-dropdown-main-level-border-type-',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:focus' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:focus' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                        'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:focus' => 'border-color: {{VALUE}}',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                        'separator' => 'after'
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_item_border_radius_active',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                     '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li a.jltma-nav-menu__dropdown-item-sub:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_divider_hr',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_divider_heading',
            array(
                'label' => __('Divider', 'master-addons' ),
                'type' => Controls_Manager::HEADING,
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_divider_type',
            array(
                'label' => __('Divider Type', 'master-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'none' => __('None', 'master-addons' ),
                    'solid' => __('Solid', 'master-addons' ),
                    'double' => __('Double', 'master-addons' ),
                    'doted' => __('Doted', 'master-addons' ),
                    'dashed' => __('Dashed', 'master-addons' ),
                    'groove' => __('Groove', 'master-addons' ),
                ),
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li:not(:first-child)' => 'border-top-style: {{VALUE}};',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'jltma_dropdown_submenu_item_divider_size',
            array(
                'label' => __('Divider Size', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array('max' => 15),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li:not(:first-child)' => 'border-top-width: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array('jltma_dropdown_submenu_item_divider_type!' => 'none'),
            )
        );

        $this->widget->add_control(
            'jltma_dropdown_submenu_item_divider_color',
            array(
                'label' => __('Divider Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector .'__main > ul > li.jltma-menu-has-children > ul.jltma-dropdown > li.jltma-menu-has-children ul.jltma-dropdown li:not(:first-child)' => 'border-top-color: {{VALUE}}',
                ),
                'condition' => array('jltma_dropdown_submenu_item_divider_type!' => 'none'),
            )
        );

        $this->widget->end_controls_section();
    }

    private function hamburger_menu_section()
    {

        $this->widget->start_controls_section(
            'section_dropdown_toggle_style',
            [
                'label'      => __('Hamburger', 'master-addons' ),
                'tab'        => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->widget->add_control(
            'dropdown_toggle_description_style',
            [
                'raw' => __('This toggle will appear on resolutions below the one defined in Breakpoint settings.', 'master-addons' ),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                // 'condition' => [
                //     'layout!' => 'dropdown',
                //     'dropdown_breakpoints!' => 'none',
                // ],
            ]
        );

        $this->widget->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'dropdown_toggle_typography',
                // 'fields_options' => array(
                //     'text_decoration' => array(
                //         'selectors' => array(
                //             '{{WRAPPER}}' => '--dropdown-toggle-text-decoration: {{VALUE}};',
                //         ),
                //     ),
                // ),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__toggle .jltma-nav-menu__toggle-label',
                'condition' => array('dropdown_toggle_type!' => 'icon'),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_toggle_icon_size',
            array(
                'label' => __('Icon Size', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 10,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array('dropdown_toggle_type!' => 'text'),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_toggle_padding',
            array(
                'label' => __('Padding', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->widget->start_controls_tabs('tabs_toggle_item_style');

        $this->widget->start_controls_tab(
            'tab_dropdown_toggle_normal',
            array('label' => __('Normal', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_toggle_color',
            [
                'label'     => __('Color', 'master-addons' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}}.jltma-toggle-view-framed ' . $this->widget_selector . '__toggle' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->widget->add_group_control(
            JLTMA_Button_Background::JLTMA_BTN_BG_GROUP,
            [
                'name'      => 'dropdown_toggle_bg',
                'exclude'   => ['color'],
                'selector'  => '{{WRAPPER}} ' . $this->widget_selector . '__toggle',
                'condition' => ['toggle_view!' => 'default'],
            ]
        );

        $this->widget->start_injection(array('of' => 'dropdown_toggle_bg_background'));

        $this->widget->add_control(
            'dropdown_toggle_bg_color',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle' => '--button-bg-color: {{VALUE}}; ' .
                        'background: var( --button-bg-color );',
                ),
                'condition' => array(
                    'dropdown_toggle_bg_background' => array(
                        'color',
                        'gradient',
                    ),
                    'toggle_view!' => 'default',
                ),
            )
        );

        $this->widget->end_injection();

        $this->widget->add_control(
            'dropdown_toggle_hr_normal',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'dropdown_toggle_border',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__toggle' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__toggle' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                        'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__toggle' => 'border-color: {{VALUE}} !important;',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_toggle_border_radius',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'condition' => array('toggle_view!' => 'default'),
                'separator' => 'before'
            )
        );

        $this->widget->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name' => 'dropdown_toggle_text_shadow',
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__toggle-label',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'dropdown_toggle_box_shadow',
                'selector' => '{{WRAPPER}}.jltma-toggle-view-framed ' . $this->widget_selector . '__toggle,
					{{WRAPPER}}.jltma-toggle-view-stacked ' . $this->widget_selector . '__toggle',
                'condition' => array('toggle_view!' => 'default'),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_dropdown_toggle_hover',
            array('label' => __('Hover', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_toggle_color_hover',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}}.jltma-toggle-view-framed ' . $this->widget_selector . '__toggle:hover' => 'border-color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_group_control(
            JLTMA_Button_Background::JLTMA_BTN_BG_GROUP,
            array(
                'name' => 'dropdown_toggle_bg_hover',
                'exclude' => array('color'),
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover',
                'condition' => array('toggle_view!' => 'default'),
            )
        );

        $this->widget->start_injection(array('of' => 'dropdown_toggle_bg_hover_background'));

        $this->widget->add_control(
            'dropdown_toggle_bg_color_hover',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover' => '--button-bg-color: {{VALUE}}; ' .
                        'background: var( --button-bg-color );',
                ),
                'condition' => array(
                    'dropdown_toggle_bg_hover_background' => array(
                        'color',
                        'gradient',
                    ),
                    'toggle_view!' => 'default',
                ),
            )
        );

        $this->widget->end_injection();

        $this->widget->add_control(
            'dropdown_toggle_hr_hover',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'dropdown_toggle_border_hover',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                        'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover' => 'border-color: {{VALUE}} !important;',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_toggle_border_radius_hover',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                    '{{WRAPPER}}.jltma-toggle-view-framed ' . $this->widget_selector . '__toggle:hover,
					{{WRAPPER}}.jltma-toggle-view-stacked ' . $this->widget_selector . '__toggle:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'condition' => array('toggle_view!' => 'default'),
                'separator' => 'before'
            )
        );

        $this->widget->add_control(
            'dropdown_toggle_text_decoration_hover',
            array(
                'label' => __('Text Decoration', 'master-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => __('Default', 'master-addons' ),
                    'none' => _x('None', 'Typography Control', 'master-addons' ),
                    'underline' => _x('Underline', 'Typography Control', 'master-addons' ),
                    'overline' => _x('Overline', 'Typography Control', 'master-addons' ),
                    'line-through' => _x('Line Through', 'Typography Control', 'master-addons' ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover .jltma-nav-menu__toggle-label' => 'text-decoration: {{VALUE}};',
                ),
                'condition' => array('dropdown_toggle_type!' => 'icon'),
            )
        );

        $this->widget->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'dropdown_toggle_text_shadow_hover',
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__toggle:hover ' . $this->widget_selector . '__toggle-label',
            ]
        );

        $this->widget->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'dropdown_toggle_box_shadow_hover',
                'selector' => '{{WRAPPER}}.jltma-toggle-view-framed ' . $this->widget_selector . '__toggle:hover,
					{{WRAPPER}}.jltma-toggle-view-stacked ' . $this->widget_selector . '__toggle:hover',
                'condition' => array('toggle_view!' => 'default'),
            )
        );

        $this->widget->add_control(
            'dropdown_toggle_transition',
            array(
                'label' => __('Transition (ms)', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array('max' => 1000),
                ),
                'default' => [
                    'size' => 150,
                    'unit' => 'px'
                ],
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__toggle ' => 'transition: all {{SIZE}}ms',
                ),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_dropdown_toggle_active',
            array('label' => __('Active', 'master-addons' ))
        );

        $this->widget->add_control(
            'dropdown_toggle_color_active',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle ' => 'color: {{VALUE}} !important; fill: {{VALUE}} !important;',
                    '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle' => 'border-color: {{VALUE}}',
                ),
            )
        );

        $this->widget->add_group_control(
            JLTMA_Button_Background::JLTMA_BTN_BG_GROUP,
            array(
                'name' => 'dropdown_toggle_bg_active',
                'exclude' => array('color'),
                'selector' => '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle',
                'condition' => array('toggle_view!' => 'default'),
            )
        );

        $this->widget->start_injection(array('of' => 'dropdown_toggle_bg_active_background'));

        $this->widget->add_control(
            'dropdown_toggle_bg_color_active',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle' => '--button-bg-color: {{VALUE}}; ' .
                        'background: var( --button-bg-color );',
                ),
                'condition' => array(
                    'dropdown_toggle_bg_active_background' => array(
                        'color',
                        'gradient',
                    ),
                    'toggle_view!' => 'default',
                ),
            )
        );

        $this->widget->end_injection();

        $this->widget->add_control(
            'dropdown_toggle_hr_active',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'dropdown_toggle_border_active',
                'label' => __('Border', 'master-addons' ),
                'fields_options' => array(
                    'border' => array(
                        'options' => array(
                            'none' => _x('None', 'Border Control', 'master-addons' ),
                            'default' => _x('Default', 'Border Control', 'master-addons' ),
                            'solid' => _x('Solid', 'Border Control', 'master-addons' ),
                            'double' => _x('Double', 'Border Control', 'master-addons' ),
                            'dotted' => _x('Dotted', 'Border Control', 'master-addons' ),
                            'dashed' => _x('Dashed', 'Border Control', 'master-addons' ),
                            'groove' => _x('Groove', 'Border Control', 'master-addons' ),
                        ),

                        'default' => 'default',
                        'selectors' => array(
                            '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle' => 'border-style: {{VALUE}}',
                        ),
                    ),
                    'width' => array(
                        'label' => _x('Border Width', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    ),
                    'color' => array(
                        'label' => _x('Border Color', 'Border Control', 'master-addons' ),
                        'selectors' => array(
                            '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle' => 'border-color: {{VALUE}} !important;',
                        ),
                        'condition' => array(
                            'border!' => array(
                                'none',
                                'default',
                            ),
                        ),
                    )
                ),
            )
        );

        $this->widget->add_responsive_control(
            'dropdown_toggle_border_radius_active',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle,
					{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'condition' => array('toggle_view!' => 'default'),
                'separator' => 'before'
            )
        );

        $this->widget->add_control(
            'dropdown_toggle_text_decoration_active',
            array(
                'label' => __('Text Decoration', 'master-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => __('Default', 'master-addons' ),
                    'none' => _x('None', 'Typography Control', 'master-addons' ),
                    'underline' => _x('Underline', 'Typography Control', 'master-addons' ),
                    'overline' => _x('Overline', 'Typography Control', 'master-addons' ),
                    'line-through' => _x('Line Through', 'Typography Control', 'master-addons' ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-nav-menu__toggle-container[aria-expanded=true] ' . $this->widget_selector . '__toggle .jltma-nav-menu__toggle-label' => 'text-decoration: {{VALUE}};',
                ),
                'condition' => array('dropdown_toggle_type!' => 'icon'),
            )
        );

        $this->widget->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name' => 'dropdown_toggle_text_shadow_active',
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__toggle.active ' . $this->widget_selector . '__toggle-label',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'dropdown_toggle_box_shadow_active',
                'selector' => '{{WRAPPER}}.jltma-toggle-view-framed ' . $this->widget_selector . '__toggle.active,
					{{WRAPPER}}.jltma-toggle-view-stacked ' . $this->widget_selector . '__toggle.active',
                'condition' => array('toggle_view!' => 'default'),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();

        $this->widget->end_controls_section();
    }

    private function popup_offcanvas_section() {
        $this->widget->start_controls_section(
            'section_style_popup_offcanvas',
            array(
                'label' => __('Popup / Offcanvas', 'master-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
                // 'condition' => array(
                //     'layout' => 'dropdown',
                //     'dropdown_menu_type' => array(
                //         'popup',
                //         'offcanvas',
                //     ),
                // ),
            )
        );

        $this->widget->add_control(
            'popup_offcanvas_vertical_alignment',
            array(
                'label' => __('Vertical Alignment', 'master-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'flex-start' => array(
                        'title' => __('Top', 'master-addons' ),
                        'icon' => 'eicon-v-align-top',
                    ),
                    'center' => array(
                        'title' => __('Middle', 'master-addons' ),
                        'icon' => 'eicon-v-align-middle',
                    ),
                    'flex-end' => array(
                        'title' => __('Bottom', 'master-addons' ),
                        'icon' => 'eicon-v-align-bottom',
                    ),
                ),
                'default' => 'flex-start',
                'prefix_class' => 'jltma-popup-offcanvas-ver-alignment-',
                // 'condition' => array(
                //     'layout' => 'dropdown',
                //     'dropdown_menu_type' => array(
                //         'popup',
                //         'offcanvas',
                //     ),
                // ),
            )
        );

        $this->widget->add_responsive_control(
            'popup_offcanvas_vertical_margin',
            array(
                'label' => __('Margin', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'allowed_dimensions' => 'vertical',
                'placeholder' => array(
                    'top' => '',
                    'right' => 'auto',
                    'bottom' => '',
                    'left' => 'auto',
                ),
                'selectors' => array(
                    // The panel is the list itself -- there is no wrapper
                    // element around it, and the Type class is on the nav.
                    '{{WRAPPER}} ' . $this->widget_selector . '__container.jltma-menu-dropdown-type-popup > ul' . $this->widget_selector . '__container-inner,
					{{WRAPPER}} ' . $this->widget_selector . '__container.jltma-menu-dropdown-type-offcanvas > ul' . $this->widget_selector . '__container-inner' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
                ),
                'condition' => array(
                    // 'layout' => 'dropdown',
                    // 'dropdown_menu_type' => array(
                    //     'popup',
                    //     'offcanvas',
                    // ),
                    'popup_offcanvas_vertical_alignment!' => 'center',
                ),
            )
        );

        $this->widget->add_control(
            'popup_bg_color',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__main.jltma-layout-dropdown .jltma-nav-menu__container-inner'  => 'background-color: {{VALUE}}',
                ),
                // 'condition' => array(
                //     'layout' => 'dropdown',
                //     'dropdown_menu_type' => array(
                //         'popup',
                //         'offcanvas',
                //     ),
                // ),
            )
        );

        $this->widget->add_control(
            'popup_offcanvas_close_style_heading',
            array(
                'label' => __('Close Button', 'master-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                // 'condition' => array(
                //     'layout' => 'dropdown',
                //     'dropdown_menu_type' => array(
                //         'popup',
                //         'offcanvas',
                //     ),
                // ),
            )
        );

        $this->widget->add_control(
            'popup_offcanvas_close_align',
            array(
                'label' => __('Alignment', 'master-addons' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => array(
                    'flex-start' => array(
                        'title' => __('Left', 'master-addons' ),
                        'icon' => 'eicon-h-align-left',
                    ),
                    'center' => array(
                        'title' => __('Center', 'master-addons' ),
                        'icon' => 'eicon-h-align-center',
                    ),
                    'flex-end' => array(
                        'title' => __('Right', 'master-addons' ),
                        'icon' => 'eicon-h-align-right',
                    ),
                ),
                'label_block' => false,
                'default' => 'flex-end',
                'toggle' => false,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close-container' => 'justify-content: {{VALUE}}',
                ),
                // 'condition' => array(
                //     'layout' => 'dropdown',
                //     'dropdown_menu_type!' => 'default',
                // ),
            )
        );

        $this->widget->add_responsive_control(
            'close_top_gap',
            array(
                'label' => __('Top Gap', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => -100,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'margin-top: {{SIZE}}{{UNIT}};',
                ),
                // 'condition' => array('layout' => 'dropdown'),
            )
        );

        $this->widget->add_responsive_control(
            'close_side_gap',
            array(
                'label' => __('Side Gap', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => -100,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array(
                    // 'layout' => 'dropdown',
                    'popup_offcanvas_close_align!' => 'center',
                ),
            )
        );

        $this->widget->start_controls_tabs('tabs_close_style');

        $this->widget->start_controls_tab(
            'tab_close_normal',
            array('label' => __('Normal', 'master-addons' ))
        );

        $this->widget->add_control(
            'close_color',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'color: {{VALUE}}',
                    '{{WRAPPER}}.jltma-close-view-framed ' . $this->widget_selector . '__dropdown-close' => 'border-color: {{VALUE}}',
                ),
                'condition' => array('popup_offcanvas_close_type!' => 'icon'),
            )
        );

        $this->widget->add_control(
            'close_icon_color',
            array(
                'label' => __('Icon Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close svg' => 'fill: {{VALUE}}',
                    '{{WRAPPER}}.jltma-close-view-framed.jltma-close-type-icon ' . $this->widget_selector . '__dropdown-close' => 'border-color: {{VALUE}}',
                ),
                'condition' => array('popup_offcanvas_close_type!' => 'text'),
            )
        );

        $this->widget->add_control(
            'close_bg_color',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'background-color: {{VALUE}}',
                ),
                'condition' => array('popup_close_view!' => 'default'),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->start_controls_tab(
            'tab_close_hover',
            array('label' => __('Hover', 'master-addons' ))
        );

        $this->widget->add_control(
            'close_color_hover',
            array(
                'label' => __('Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}}.jltma-close-view-framed ' . $this->widget_selector . '__dropdown-close:hover' => 'border-color: {{VALUE}}',
                ),
                'condition' => array('popup_offcanvas_close_type!' => 'icon'),
            )
        );

        $this->widget->add_control(
            'close_icon_color_hover',
            array(
                'label' => __('Icon Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close:hover > i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close:hover svg' => 'fill: {{VALUE}}',
                    '{{WRAPPER}}.jltma-close-view-framed.jltma-close-type-icon ' . $this->widget_selector . '__dropdown-close:hover' => 'border-color: {{VALUE}}',
                ),
                'condition' => array('popup_offcanvas_close_type!' => 'text'),
            )
        );

        $this->widget->add_control(
            'close_bg_color_hover',
            array(
                'label' => __('Background Color', 'master-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close:hover' => 'background-color: {{VALUE}}',
                ),
                'condition' => array('popup_close_view!' => 'default'),
            )
        );

        $this->widget->end_controls_tab();

        $this->widget->end_controls_tabs();

        $this->widget->add_control(
            'close_hr',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            )
        );

        $this->widget->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'close_typography',
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close ' . $this->widget_selector . '__dropdown-close-label',
                'condition' => array(
                    // 'layout' => 'dropdown',
                    'popup_offcanvas_close_type!' => 'icon',
                ),
            )
        );

        $this->widget->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'close_box_shadow',
                'selector' => '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close',
                'condition' => array(
                    // 'layout' => 'dropdown',
                    'dropdown_menu_type!' => 'default',
                    'popup_close_view!' => 'default',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'close_icon_size',
            array(
                'label' => __('Icon Size', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 10,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
                'condition' => array(
                    // 'layout' => 'dropdown',
                    'popup_offcanvas_close_type!' => 'text',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'popup_close_padding',
            array(
                'label' => __('Padding', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'condition' => array(
                    // 'layout' => 'dropdown',
                    // 'dropdown_menu_type!' => 'default',
                    'popup_close_view!' => 'default',
                    'popup_close_shape!' => 'circle',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'popup_close_icon_padding',
            array(
                'label' => __('Padding', 'master-addons' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 10,
                        'max' => 50,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'padding: {{SIZE}}{{UNIT}}',
                ),
                'condition' => array(
                    // 'layout' => 'dropdown',
                    // 'dropdown_menu_type!' => 'default',
                    'popup_close_view!' => 'default',
                    'popup_offcanvas_close_type' => 'icon',
                    'popup_close_shape' => 'circle',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'popup_close_framed_border_width',
            array(
                'label' => __('Border Width', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'condition' => array(
                    // 'layout' => 'dropdown',
                    // 'dropdown_menu_type!' => 'default',
                    'popup_close_view' => 'framed',
                ),
            )
        );

        $this->widget->add_responsive_control(
            'close_border_radius',
            array(
                'label' => __('Border Radius', 'master-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array(
                    'px',
                    '%',
                ),
                'selectors' => array(
                    '{{WRAPPER}} ' . $this->widget_selector . '__dropdown-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'condition' => array(
                    // 'layout' => 'dropdown',
                    // 'dropdown_menu_type!' => 'default',
                    'popup_close_view!' => 'default',
                ),
            )
        );

        $this->widget->end_controls_section();
    }


    private function menu_condition_section() {
        $conditions = array(
            'relation' => 'or',
            'terms' => array(
                array(
                    'name' => 'jltma_nav_layout',
                    'operator' => '=',
                    'value' => 'horizontal',
                ),
                array(
                    'relation' => 'and',
                    'terms' => array(
                        array(
                            'name' => 'jltma_nav_layout',
                            'operator' => '=',
                            'value' => 'vertical',
                        ),
                        array(
                            'name' => 'vertical_menu_type',
                            'operator' => '=',
                            'value' => 'normal',
                        ),
                    ),
                ),
            ),
        );

        Animation::register_sections_controls($this->widget, true, $conditions);
    }

}