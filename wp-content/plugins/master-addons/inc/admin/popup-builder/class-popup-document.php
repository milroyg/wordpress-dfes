<?php

/**
 * Master Addons Popup Document Type for Elementor
 *
 * @package MasterAddons
 * @subpackage PopupBuilder
 */

namespace MasterAddons\Inc\Admin\PopupBuilder;

use MasterAddons\Inc\Classes\Helper;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\Elementor\Core\Base\Document')) {
    return;
}

class Popup_Document extends \Elementor\Core\Base\Document
{

    public function get_name()
    {
        return 'jltma_popup';
    }

    public static function get_title()
    {
        return __('MA Popup', 'master-addons');
    }

    public static function get_type()
    {
        return 'jltma_popup';
    }

    public function get_css_wrapper_selector()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return '.jltma-template-popup';
        } else {
            return '#jltma-popup-id-' . $this->get_main_id();
        }
    }

    public static function get_properties()
    {
        $properties = parent::get_properties();

        $properties['support_kit'] = true;
        $properties['show_in_finder'] = true;
        $properties['show_on_admin_bar'] = false;

        return $properties;
    }

    public static function get_editor_panel_config()
    {
        $config = parent::get_editor_panel_config();

        $config['has_elements'] = true;
        $config['support_kit'] = true;
        $config['widgets_settings']['theme_builder_promotion'] = [
            'show' => false,
        ];

        return $config;
    }

    protected function get_remote_library_config()
    {
        $config = parent::get_remote_library_config();
        $config['type'] = 'popup';
        $config['default_route'] = 'templates/popups';
        return $config;
    }




    protected function register_controls()
    {
        // Settings Section
        $this->start_controls_section(
            'popup_trigger_settings',
            [
                'label' => __('Settings', 'master-addons'),
                'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        // Free: all options visible with "(Pro)" labels. Pro filter removes the labels.
        $trigger_options = apply_filters('master_addons/popup_builder/trigger_options', [
            'page-load'           => __('On Page Load', 'master-addons'),
            'on-click'            => __('On Click (Pro)', 'master-addons'),
            'on-scroll'           => __('On Scroll (Pro)', 'master-addons'),
            'on-scroll-to-element' => __('On Scroll to Element (Pro)', 'master-addons'),
            'on-exit-intent'      => __('On Exit Intent (Pro)', 'master-addons'),
            'after-inactivity'    => __('After Inactivity (Pro)', 'master-addons'),
            'custom'              => __('Custom (Pro)', 'master-addons'),
        ]);

        $this->add_control(
            'popup_trigger',
            [
                'label' => __('Open Popup', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'page-load',
                'options' => $trigger_options,
            ]
        );

        $this->add_control(
            'popup_load_delay',
            [
                'label' => __('Delay after Page Load (sec)', 'master-addons'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 0,
                'max' => 60,
                'step' => 0.1,
                'condition' => [
                    'popup_trigger' => 'page-load',
                ],
            ]
        );

        // Pro trigger controls (scroll amount, element selector, click selector, inactivity time)
        do_action('master_addons/popup_builder/after_trigger_controls', $this);

        // Free: all options visible. Original free options + pro extras with "(Pro)" labels.
        $delay_options = apply_filters('master_addons/popup_builder/delay_options', [
            'no-delay'      => __('No Delay', 'master-addons'),
            'do-not-show'   => __('Do Not Show Again (Pro)', 'master-addons'),
            '1-minute'      => __('1 Minute', 'master-addons'),
            '3-minutes'     => __('3 Minutes', 'master-addons'),
            '5-minutes'     => __('5 Minutes', 'master-addons'),
            '10-minutes'    => __('10 Minutes (Pro)', 'master-addons'),
            '30-minutes'    => __('30 Minutes (Pro)', 'master-addons'),
            '1-hour'        => __('1 Hour (Pro)', 'master-addons'),
            '3-hours'       => __('3 Hours (Pro)', 'master-addons'),
            '6-hours'       => __('6 Hours (Pro)', 'master-addons'),
            '12-hours'      => __('12 Hours (Pro)', 'master-addons'),
            '1-day'         => __('1 Day (Pro)', 'master-addons'),
            '3-days'        => __('3 Days (Pro)', 'master-addons'),
            '5-days'        => __('5 Days (Pro)', 'master-addons'),
            '1-week'        => __('1 Week (Pro)', 'master-addons'),
            '7-days'        => __('7 Days (Pro)', 'master-addons'),
            '10-days'       => __('10 Days (Pro)', 'master-addons'),
            '15-days'       => __('15 Days (Pro)', 'master-addons'),
            '20-days'       => __('20 Days (Pro)', 'master-addons'),
            '1-month'       => __('1 Month (Pro)', 'master-addons'),
        ]);

        $this->add_control(
            'popup_show_again_delay',
            [
                'label' => __('Show Again Delay', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'no-delay',
                'options' => $delay_options,
                'description' => __('This option determines when to show popup again to a visitor after it is closed.', 'master-addons'),
            ]
        );

        $this->add_control(
            'popup_disable_page_scroll',
            [
                'label' => __('Disable Page Scroll', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'master-addons'),
                'label_off' => __('No', 'master-addons'),
            ]
        );

        // Disable popup automatically (Pro feature — lock icon + upgrade link in free)
        $disable_auto_control = apply_filters('master_addons/popup_builder/disable_automatic_control', [
            'label' => esc_html__('Disable popup automatically (Pro)', 'master-addons'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                '1' => [
                    'title' => '',
                    'icon' => 'eicon-lock',
                ],
            ],
            'default' => '1',
            'description' => Helper::upgrade_to_pro('Disable popup automatically available on'),
        ]);

        $this->add_control('popup_disable_automatic', $disable_auto_control);

        $disable_after_control = apply_filters('master_addons/popup_builder/disable_after_control', [
            'label' => esc_html__('Disable After (Pro)', 'master-addons'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                '1' => [
                    'title' => '',
                    'icon' => 'eicon-lock',
                ],
            ],
            'default' => '1',
            'description' => Helper::upgrade_to_pro('Disable after date available on'),
        ]);


        if (apply_filters('master_addons/popup_builder/show_pro_notices', true)) {
            $this->add_control(
                'popup_disable_automatic_notice',
                [
                    'label' => '',
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => Helper::jltma_pro_upgrade_message(),
                ]
            );
        }


        $this->add_control('popup_disable_after', $disable_after_control);

        // Hook point for additional pro settings controls
        do_action('master_addons/popup_builder/after_settings_controls', $this);

        $this->end_controls_section();

        // Layout Section
        $this->start_controls_section(
            'popup_layout_settings',
            [
                'label' => __('Layout', 'master-addons'),
                'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'popup_display_as',
            [
                'label' => __('Display As', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'modal',
                'options' => [
                    'modal' => __('Popup Modal', 'master-addons'),
                    'notification' => __('Notification Bar', 'master-addons'),
                    'fullscreen' => __('Fullscreen', 'master-addons'),
                ],
                'prefix_class' => 'jltma-popup-',
            ]
        );

        $this->add_control(
            'popup_position',
            [
                'label' => __('Position', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'center-center',
                'options' => [
                    'center-center' => __('Center Center', 'master-addons'),
                    'center-left' => __('Center Left', 'master-addons'),
                    'center-right' => __('Center Right', 'master-addons'),
                    'top-left' => __('Top Left', 'master-addons'),
                    'top-center' => __('Top Center', 'master-addons'),
                    'top-right' => __('Top Right', 'master-addons'),
                    'bottom-left' => __('Bottom Left', 'master-addons'),
                    'bottom-center' => __('Bottom Center', 'master-addons'),
                    'bottom-right' => __('Bottom Right', 'master-addons'),
                ],
                'condition' => [
                    'popup_display_as' => 'modal',
                ],
            ]
        );

        $this->add_control(
            'popup_animation',
            [
                'label' => __('Animation', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'jltma-anim-fade-in',
                'options' => [
                    // Fade
                    'jltma-anim-fade-in'              => __('Fade In', 'master-addons'),
                    'jltma-anim-fade-in-down'         => __('Fade In Down', 'master-addons'),
                    'jltma-anim-fade-in-down-1'       => __('Fade In Down 1', 'master-addons'),
                    'jltma-anim-fade-in-down-2'       => __('Fade In Down 2', 'master-addons'),
                    'jltma-anim-fade-in-up'           => __('Fade In Up', 'master-addons'),
                    'jltma-anim-fade-in-up-1'         => __('Fade In Up 1', 'master-addons'),
                    'jltma-anim-fade-in-up-2'         => __('Fade In Up 2', 'master-addons'),
                    'jltma-anim-fade-in-left'         => __('Fade In Left', 'master-addons'),
                    'jltma-anim-fade-in-left-1'       => __('Fade In Left 1', 'master-addons'),
                    'jltma-anim-fade-in-left-2'       => __('Fade In Left 2', 'master-addons'),
                    'jltma-anim-fade-in-right'        => __('Fade In Right', 'master-addons'),
                    'jltma-anim-fade-in-right-1'      => __('Fade In Right 1', 'master-addons'),
                    'jltma-anim-fade-in-right-2'      => __('Fade In Right 2', 'master-addons'),
                    // Slide
                    'jltma-anim-slide-from-right'     => __('Slide From Right', 'master-addons'),
                    'jltma-anim-slide-from-left'      => __('Slide From Left', 'master-addons'),
                    'jltma-anim-slide-from-top'       => __('Slide From Top', 'master-addons'),
                    'jltma-anim-slide-from-bot'       => __('Slide From Bottom', 'master-addons'),
                    // Mask
                    'jltma-anim-mask-from-top'        => __('Mask From Top', 'master-addons'),
                    'jltma-anim-mask-from-bot'        => __('Mask From Bottom', 'master-addons'),
                    'jltma-anim-mask-from-left'       => __('Mask From Left', 'master-addons'),
                    'jltma-anim-mask-from-right'      => __('Mask From Right', 'master-addons'),
                    // Rotate
                    'jltma-anim-rotate-in'            => __('Rotate In', 'master-addons'),
                    'jltma-anim-rotate-in-down-left'  => __('Rotate In Down Left', 'master-addons'),
                    'jltma-anim-rotate-in-down-left-1'  => __('Rotate In Down Left 1', 'master-addons'),
                    'jltma-anim-rotate-in-down-left-2'  => __('Rotate In Down Left 2', 'master-addons'),
                    'jltma-anim-rotate-in-down-right' => __('Rotate In Down Right', 'master-addons'),
                    'jltma-anim-rotate-in-down-right-1' => __('Rotate In Down Right 1', 'master-addons'),
                    'jltma-anim-rotate-in-down-right-2' => __('Rotate In Down Right 2', 'master-addons'),
                    'jltma-anim-rotate-in-up-left'    => __('Rotate In Up Left', 'master-addons'),
                    'jltma-anim-rotate-in-up-left-1'  => __('Rotate In Up Left 1', 'master-addons'),
                    'jltma-anim-rotate-in-up-left-2'  => __('Rotate In Up Left 2', 'master-addons'),
                    'jltma-anim-rotate-in-up-right'   => __('Rotate In Up Right', 'master-addons'),
                    'jltma-anim-rotate-in-up-right-1' => __('Rotate In Up Right 1', 'master-addons'),
                    'jltma-anim-rotate-in-up-right-2' => __('Rotate In Up Right 2', 'master-addons'),
                    // Zoom
                    'jltma-anim-zoom-in'              => __('Zoom In', 'master-addons'),
                    'jltma-anim-zoom-in-1'            => __('Zoom In 1', 'master-addons'),
                    'jltma-anim-zoom-in-2'            => __('Zoom In 2', 'master-addons'),
                    'jltma-anim-zoom-in-3'            => __('Zoom In 3', 'master-addons'),
                    // Scale
                    'jltma-anim-scale-up'             => __('Scale Up', 'master-addons'),
                    'jltma-anim-scale-up-1'           => __('Scale Up 1', 'master-addons'),
                    'jltma-anim-scale-up-2'           => __('Scale Up 2', 'master-addons'),
                    'jltma-anim-scale-down'           => __('Scale Down', 'master-addons'),
                    'jltma-anim-scale-down-1'         => __('Scale Down 1', 'master-addons'),
                    'jltma-anim-scale-down-2'         => __('Scale Down 2', 'master-addons'),
                    // Flip
                    'jltma-anim-flip-in-down'         => __('Flip In Down', 'master-addons'),
                    'jltma-anim-flip-in-down-1'       => __('Flip In Down 1', 'master-addons'),
                    'jltma-anim-flip-in-down-2'       => __('Flip In Down 2', 'master-addons'),
                    'jltma-anim-flip-in-up'           => __('Flip In Up', 'master-addons'),
                    'jltma-anim-flip-in-up-1'         => __('Flip In Up 1', 'master-addons'),
                    'jltma-anim-flip-in-up-2'         => __('Flip In Up 2', 'master-addons'),
                    'jltma-anim-flip-in-left'         => __('Flip In Left', 'master-addons'),
                    'jltma-anim-flip-in-left-1'       => __('Flip In Left 1', 'master-addons'),
                    'jltma-anim-flip-in-left-2'       => __('Flip In Left 2', 'master-addons'),
                    'jltma-anim-flip-in-left-3'       => __('Flip In Left 3', 'master-addons'),
                    'jltma-anim-flip-in-right'        => __('Flip In Right', 'master-addons'),
                    'jltma-anim-flip-in-right-1'      => __('Flip In Right 1', 'master-addons'),
                    'jltma-anim-flip-in-right-2'      => __('Flip In Right 2', 'master-addons'),
                    'jltma-anim-flip-in-right-3'      => __('Flip In Right 3', 'master-addons'),
                    // Pulse
                    'jltma-anim-pulse-in'             => __('Pulse In 1', 'master-addons'),
                    'jltma-anim-pulse-in-1'           => __('Pulse In 2', 'master-addons'),
                    'jltma-anim-pulse-in-2'           => __('Pulse In 3', 'master-addons'),
                    'jltma-anim-pulse-in-3'           => __('Pulse In 4', 'master-addons'),
                    'jltma-anim-pulse-in-4'           => __('Pulse In 5', 'master-addons'),
                    'jltma-anim-pulse-out-1'          => __('Pulse Out 1', 'master-addons'),
                    'jltma-anim-pulse-out-2'          => __('Pulse Out 2', 'master-addons'),
                    'jltma-anim-pulse-out-3'          => __('Pulse Out 3', 'master-addons'),
                    'jltma-anim-pulse-out-4'          => __('Pulse Out 4', 'master-addons'),
                    // Specials
                    'jltma-anim-shake'                => __('Shake', 'master-addons'),
                    'jltma-anim-bounce-in'            => __('Bounce In', 'master-addons'),
                    'jltma-anim-jack-in-box'          => __('Jack In the Box', 'master-addons'),
                ],
            ]
        );

        $this->add_control(
            'popup_animation_duration',
            [
                'label' => __('Animation Duration (ms)', 'master-addons'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 400,
                'min' => 0,
                'max' => 5000,
                'step' => 50,
            ]
        );

        $this->add_responsive_control(
            'popup_width',
            [
                'label' => __('Width', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1920,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 640,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'popup_height',
            [
                'label' => __('Height', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1920,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'popup_custom_positioning',
            [
                'label' => __('Custom Positioning', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', 'master-addons'),
                'label_off' => __('No', 'master-addons'),
            ]
        );

        $this->add_responsive_control(
            'popup_top_distance',
            [
                'label' => __('Top Distance', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'popup_custom_positioning' => 'yes',
                ],
                'selectors' => [
                    '.jltma-popup-container' => 'top: {{SIZE}}{{UNIT}} !important; position: fixed !important; transform: none !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'popup_left_distance',
            [
                'label' => __('Left Distance', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'popup_custom_positioning' => 'yes',
                ],
                'selectors' => [
                    '.jltma-popup-container' => 'left: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'popup_bottom_distance',
            [
                'label' => __('Bottom Distance', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'popup_custom_positioning' => 'yes',
                ],
                'selectors' => [
                    '.jltma-popup-container' => 'bottom: {{SIZE}}{{UNIT}} !important; top: auto !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'popup_right_distance',
            [
                'label' => __('Right Distance', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'popup_custom_positioning' => 'yes',
                ],
                'selectors' => [
                    '.jltma-popup-container' => 'right: {{SIZE}}{{UNIT}} !important; left: auto !important;',
                ],
            ]
        );

        $this->add_control(
            'popup_custom_width',
            [
                'label' => __('Custom Width', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'popup_custom_positioning' => 'yes',
                ],
                'selectors' => [
                    '.jltma-popup-container' => 'width: {{SIZE}}{{UNIT}} !important; max-width: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'popup_custom_height',
            [
                'label' => __('Custom Height', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    'popup_custom_positioning' => 'yes',
                ],
                'selectors' => [
                    '.jltma-popup-container' => 'height: {{SIZE}}{{UNIT}} !important; max-height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        // Overlay Section
        $this->start_controls_section(
            'popup_overlay_settings',
            [
                'label' => __('Overlay', 'master-addons'),
                'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
                'condition' => [
                    'popup_display_as' => 'modal',
                ],
            ]
        );

        $this->add_control(
            'popup_show_overlay',
            [
                'label' => __('Show Overlay', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'master-addons'),
                'label_off' => __('No', 'master-addons'),
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'popup_overlay_bg',
                'label' => __('Background', 'master-addons'),
                'types' => ['classic', 'gradient'],
                'fields_options' => [
                    'color' => [
                        'default' => 'rgba(0,0,0,0.75)',
                    ],
                ],
                'selector' => '{{WRAPPER}} .jltma-popup-overlay',
                'condition' => [
                    'popup_show_overlay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'popup_close_on_overlay',
            [
                'label' => __('Close on Overlay Click', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'master-addons'),
                'label_off' => __('No', 'master-addons'),
                'condition' => [
                    'popup_show_overlay' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Close Button Section
        $this->start_controls_section(
            'popup_close_button_settings',
            [
                'label' => __('Close Button', 'master-addons'),
                'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'popup_show_close_button',
            [
                'label' => __('Show Close Button', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Yes', 'master-addons'),
                'label_off' => __('No', 'master-addons'),
            ]
        );

        $this->add_control(
            'popup_close_button_position',
            [
                'label' => __('Position', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'top-right',
                'options' => [
                    'top-left' => __('Top Left', 'master-addons'),
                    'top-right' => __('Top Right', 'master-addons'),
                    'inside-top-left' => __('Inside Top Left', 'master-addons'),
                    'inside-top-right' => __('Inside Top Right', 'master-addons'),
                ],
                'condition' => [
                    'popup_show_close_button' => 'yes',
                ],
            ]
        );

        $close_esc_control = apply_filters('master_addons/popup_builder/close_esc_key_control', [
            'label' => esc_html__('Close on ESC Key (Pro)', 'master-addons'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                '1' => [
                    'title' => '',
                    'icon' => 'eicon-lock',
                ],
            ],
            'default' => '1',
            'description' => Helper::upgrade_to_pro('Close on ESC Key available on'),
        ]);

        $this->add_control('popup_close_esc_key', $close_esc_control);

        // Pro close button controls hook point
        do_action('master_addons/popup_builder/after_close_button_controls', $this);

        $this->end_controls_section();

        // Style Tab Sections (registered before parent to appear before Advanced tab)
        $this->start_controls_section(
            'popup_container_styles',
            [
                'label' => __('Popup', 'master-addons'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'popup_container_bg',
                'label' => __('Background', 'master-addons'),
                'types' => ['classic', 'gradient'],
                'fields_options' => [
                    'color' => [
                        'default' => '#ffffff',
                    ],
                ],
                'selector' => '{{WRAPPER}} .jltma-popup-container-inner'
            ]
        );

        $this->add_control(
            'popup_scrollbar_color',
            [
                'label' => __('ScrollBar Color', 'master-addons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-container-inner::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'popup_container_padding',
            [
                'label' => __('Padding', 'master-addons'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 20,
                    'right' => 20,
                    'bottom' => 20,
                    'left' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-container-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'popup_container_radius',
            [
                'label' => __('Border Radius', 'master-addons'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-container-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'popup_container_border',
                'label' => __('Border', 'master-addons'),
                'selector' => '{{WRAPPER}} .jltma-popup-container-inner',
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'popup_container_shadow',
                'selector' => '{{WRAPPER}} .jltma-popup-container-inner'
            ]
        );

        $this->end_controls_section();

        // Close Button Styles
        $this->start_controls_section(
            'popup_close_btn_styles',
            [
                'label' => __('Close Button', 'master-addons'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_popup_close_btn_style');

        $this->start_controls_tab(
            'tab_popup_close_btn_normal',
            [
                'label' => __('Normal', 'master-addons'),
            ]
        );

        $this->add_control(
            'popup_close_btn_color',
            [
                'label' => __('Color', 'master-addons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#6b7280',
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'popup_close_btn_bg_color',
            [
                'label' => __('Background Color', 'master-addons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'background-color: {{VALUE}}',
                ]
            ]
        );

        $this->add_control(
            'popup_close_btn_border_color',
            [
                'label' => __('Border Color', 'master-addons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'popup_close_btn_box_shadow',
                'selector' => '{{WRAPPER}} .jltma-popup-close-btn',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_popup_close_btn_hover',
            [
                'label' => __('Hover', 'master-addons'),
            ]
        );

        $this->add_control(
            'popup_close_btn_color_hr',
            [
                'label' => __('Color', 'master-addons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#54595f',
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'popup_close_btn_bg_color_hr',
            [
                'label' => __('Background Color', 'master-addons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn:hover' => 'background-color: {{VALUE}}',
                ]
            ]
        );

        $this->add_control(
            'popup_close_btn_border_color_hr',
            [
                'label' => __('Border Color', 'master-addons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn:hover' => 'border-color: {{VALUE}}',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'popup_close_btn_size',
            [
                'label' => __('Size', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'popup_close_btn_box_size',
            [
                'label' => __('Box Size', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 36,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'popup_close_btn_vr_position',
            [
                'label' => __('Vertical Position', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'popup_show_close_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'popup_close_btn_hr_position',
            [
                'label' => __('Horizontal Position', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'popup_show_close_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'popup_close_btn_border_type',
            [
                'label' => __('Border Type', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'none' => __('None', 'master-addons'),
                    'solid' => __('Solid', 'master-addons'),
                    'double' => __('Double', 'master-addons'),
                    'dotted' => __('Dotted', 'master-addons'),
                    'dashed' => __('Dashed', 'master-addons'),
                    'groove' => __('Groove', 'master-addons'),
                ],
                'default' => 'solid',
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'border-style: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'popup_close_btn_border_width',
            [
                'label' => __('Border Width', 'master-addons'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => 1,
                    'right' => 1,
                    'bottom' => 1,
                    'left' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'popup_close_btn_border_type!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'popup_close_btn_radius',
            [
                'label' => __('Border Radius', 'master-addons'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 50,
                    'right' => 50,
                    'bottom' => 50,
                    'left' => 50,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'popup_close_btn_position_vr',
            [
                'label' => __('Vertical Position', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'popup_close_btn_position_hr',
            [
                'label' => __('Horizontal Position', 'master-addons'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-popup-close-btn' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Upgrade to Pro notice section (banner approach - jltma_pro_notice_html)
        if (apply_filters('master_addons/popup_builder/show_pro_notices', true)) {
            $this->start_controls_section(
                'popup_upgrade_to_pro',
                [
                    'label' => __('Unlock More Features', 'master-addons') . ' &#11088;',
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'popup_upgrade_notice',
                [
                    'label' => '',
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => Helper::jltma_pro_notice_html(),
                ]
            );

            $this->end_controls_section();
        }

        // Default Document Settings (General Settings, Title, Status, etc.)
        parent::register_controls();

        // Unlock Pro Features section — last section, auto-open on load
        if (apply_filters('master_addons/popup_builder/show_pro_notices', true)) {
            $this->start_controls_section(
                'popup_pro_features',
                [
                    'label' => __('Unlock Pro Features', 'master-addons') . ' &#11088;',
                    'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
                ]
            );

            $features = [
                __('Open Popup: On Page Scroll', 'master-addons'),
                __('Open Popup: On Scroll to Element', 'master-addons'),
                __('Open Popup: After Specific Date', 'master-addons'),
                __('Open Popup: After User Inactivity', 'master-addons'),
                __('Open Popup: After User Exit Intent', 'master-addons'),
                __('Open Popup: Custom Trigger (Button Click or Selector)', 'master-addons'),
                __('Show Again Delay: Set any time (hours, days, weeks) - This option determines when to show popup again to a visitor after it is closed.', 'master-addons'),
                __('Stop showing after Specific Date', 'master-addons'),
                __('Automatic Closing Delay', 'master-addons'),
                __('Show Popup for Specific Roles', 'master-addons'),
                __('Show according to URL Keyword - Popup will show up if URL(referral) contains chosen keyword', 'master-addons'),
                __('Show/Hide Popup on any Device', 'master-addons'),
                __('Prevent Popup closing on "ESC" key', 'master-addons'),
            ];

            $features_html = '<ul style="list-style:none; padding-left:0; margin:10px 0 20px; color:#fff;">';
            foreach ($features as $feature) {
                $features_html .= '<li style="margin-bottom:8px; line-height:1.5; padding-left:20px; position:relative;">'
                    . '<span style="position:absolute; left:0; top:0; color:#ff6441;">&#8226;</span>'
                    . esc_html($feature) . '</li>';
            }
            $features_html .= '</ul>';
            $features_html .= '<div style="text-align:center; margin-top:15px;">'
                . '<a href="https://master-addons.com/pricing/" target="_blank" style="display:inline-block; background:#ff6441; color:#fff; padding:10px 24px; border-radius:4px; text-decoration:none; font-weight:600; font-size:14px;">'
                . esc_html__('Get Pro version', 'master-addons') . '</a></div>';

            // Auto-open this section on panel load
            $features_html .= '<script>
            (function(){
                if (window.jltmaProFeaturesAutoOpen) return;
                window.jltmaProFeaturesAutoOpen = true;
                var panel = document.getElementById("elementor-panel-content-wrapper");
                if (!panel) return;
                function openSection() {
                    var section = panel.querySelector(".elementor-control-popup_pro_features");
                    if (section && !section.classList.contains("elementor-open")) {
                        section.classList.add("elementor-open");
                    }
                }
                var observer = new MutationObserver(openSection);
                observer.observe(panel, {childList: true, subtree: true});
                openSection();
            })();
            </script>';

            $this->add_control(
                'popup_pro_features_list',
                [
                    'label' => '',
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => $features_html,
                ]
            );

            $this->end_controls_section();
        }
    }
}
