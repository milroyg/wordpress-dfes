<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 04/08/20
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.

class JLTMA_Extension_Custom_JS
{

    private static $instance = null;

    public function __construct()
    {
        // Add new controls to Advanced Tab globally for elements
        add_action("elementor/element/after_section_end", array($this, 'jltma_add_section_custom_js_controls'), 25, 3);
        // Add page-level custom JS support
        add_action('elementor/documents/register_controls', [$this, 'jltma_add_section_page_custom_js_controls'], 20);
        add_action('wp_print_footer_scripts', [$this, 'jltma_page_custom_js'], 999);
        add_action('wp_print_footer_scripts', [$this, 'jltma_element_custom_js'], 1000);
    }

    public function jltma_add_section_custom_js_controls($widget, $section_id, $args)
    {
        if ('section_custom_css_pro' !== $section_id) {
            return;
        }

        if (!current_user_can('unfiltered_html')) {
            return;
        }

        $widget->start_controls_section(
            'jltma_custom_js_section',
            [
                'label' => esc_html__('Custom JS ', 'master-addons') . JLTMA_EXTENSION_BADGE,
                'tab' => Controls_Manager::TAB_ADVANCED
            ]
        );

        $widget->add_control(
            'custom_js',
            [
                'type' => Controls_Manager::CODE,
                'label' => esc_html__('Custom JS', 'master-addons'),
                'label_block' => true,
                'language' => 'javascript'
            ]
        );

        $widget->add_control(
            'custom_js_description',
            [
                'raw' => esc_html__('Add your custom JavaScript code here. No need to add <script> tags.', 'master-addons'),
                'type' => Controls_Manager::RAW_HTML,
                'content_classes' => 'elementor-descriptor',
                'separator' => 'none'
            ]
        );

        $widget->end_controls_section();
    }

    public function jltma_add_section_page_custom_js_controls($controls)
    {
        if (!current_user_can('unfiltered_html')) {
            return;
        }

        if (!current_user_can('edit_posts')) {
            return;
        }

        $controls->start_controls_section(
            'jtlma_section_custom_js',
            [
                'label' => JLTMA_BADGE . esc_html__(' Custom JS', 'master-addons'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );

        $controls->add_control(
            'jtlma_custom_js_label',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__('Add your own custom JS here', 'master-addons'),
            ]
        );

        $controls->add_control(
            'jtlma_custom_js',
            [
                'type' => Controls_Manager::CODE,
                'show_label' => false,
                'language' => 'javascript',
            ]
        );

        $controls->add_control(
            'jtlma_custom_js_usage',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('No need to write `$( document ).ready()`, write direct code. <br> You may use both jQuery selector e.g. $(\'.selector\') or Vanilla JS selector e.g. document.queryselector(\'.selector\')', 'master-addons'),
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $controls->add_control(
            'jtlma_custom_js_docs',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('For more information, <a href="https://master-addons.com/docs/addons/custom-js-extension/" target="_blank">click here</a>', 'master-addons'),
                'content_classes' => 'elementor-descriptor',
            ]
        );

        $controls->end_controls_section();
    }


    public function jltma_element_custom_js()
    {
        if (!class_exists('\Elementor\Plugin') || !\Elementor\Plugin::$instance) {
            return;
        }

        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
            return;
        }

        $document = \Elementor\Plugin::$instance->documents->get(get_the_ID());

        if (!$document) {
            return;
        }

        $data = $document->get_elements_data();

        if (empty($data)) {
            return;
        }

        $custom_js_code = $this->jltma_collect_element_custom_js($data);

        if (empty($custom_js_code)) {
            return;
        }

        echo "<script type='text/javascript'>jQuery(document).ready(function($){
            'use strict';
            " . $custom_js_code . "
        });</script>";
    }

    private function jltma_collect_element_custom_js($elements)
    {
        $js_code = '';

        foreach ($elements as $element) {
            if (!empty($element['settings']['custom_js'])) {
                // Replace 'selector' with the element's unique selector
                $element_selector = '.elementor-element-' . $element['id'];
                $custom_js = str_replace('selector', $element_selector, $element['settings']['custom_js']);
                $js_code .= $custom_js . "\n";
            }

            if (!empty($element['elements'])) {
                $js_code .= $this->jltma_collect_element_custom_js($element['elements']);
            }
        }

        return $js_code;
    }

    public function jltma_page_custom_js()
    {
        if (!class_exists('\Elementor\Plugin') || !\Elementor\Plugin::$instance) {
            return;
        }

        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode()) {
            return;
        }

        $document = \Elementor\Plugin::$instance->documents->get(get_the_ID());

        if (!$document)
            return;

        $custom_js = $document->get_settings('jtlma_custom_js');

        if (empty($custom_js))
            return;

        echo "<script type='text/javascript'>jQuery(document).ready(function($){
            'use strict';
            " . $custom_js . "
        });</script>";
    }



    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

JLTMA_Extension_Custom_JS::get_instance();
