<?php

namespace MasterAddons\Modules\Dynamic;

use \Elementor\Controls_Manager;
use \Elementor\Element_Base;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MasterAddons\Modules\Dynamic\WrapperLink')) {
class WrapperLink
{
    private static $instance = null;

    private function __construct()
    {
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'add_controls_section'], 1);
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'add_controls_section'], 1);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'add_controls_section'], 1);
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'add_controls_section'], 1);

        add_action('elementor/frontend/before_render', [$this, 'before_render'], 1);
    }

    public function add_controls_section(Element_Base $element)
    {
        $tabs = Controls_Manager::TAB_CONTENT;

        if (in_array($element->get_name(), ['section', 'column', 'container'])) {
            $tabs = Controls_Manager::TAB_LAYOUT;
        }

        $element->start_controls_section(
            'jltma_section_wrapper_link',
            [
                'label' => esc_html__('Wrapper Link', 'master-addons') . JLTMA_EXTENSION_BADGE,
                'tab'   => $tabs,
            ]
        );

        $element->add_control(
            'jltma_section_element_link',
            [
                'label'       => esc_html__('Link', 'master-addons'),
                'type'        => Controls_Manager::URL,
                'dynamic'     => [
                    'active' => true,
                ],
                'placeholder' => 'https://example.com',
            ]
        );

        $element->end_controls_section();
    }

    public function before_render(Element_Base $element)
    {
        $link_settings = $element->get_settings_for_display('jltma_section_element_link');

        if (empty($link_settings['url'])) {
            return;
        }

        $link_settings['url'] = esc_url($link_settings['url'] ?? '');
        unset($link_settings['custom_attributes']);

        if ($link_settings && !empty($link_settings['url'])) {
            $element->add_render_attribute(
                '_wrapper',
                [
                    'data-jltma-wrapper-link' => wp_json_encode($link_settings),
                    'style'                   => 'cursor: pointer'
                ]
            );

            // Enqueue module script when wrapper link is used
            wp_enqueue_script('master-addons-wrapper-link');
        }
    }

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
}

WrapperLink::get_instance();
