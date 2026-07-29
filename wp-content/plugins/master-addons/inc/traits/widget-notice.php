<?php

namespace MasterAddons\Inc\Traits;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Admin\Config;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

trait Widget_Notice
{
    /**
     * Adding Go Premium message to all widgets
     */
    public function upgrade_to_pro_message()
    {
        if (!Helper::jltma_premium()) {
            $this->start_controls_section(
                'jltma_pro_section',
                [
                    'label' => sprintf(
                        /* translators: %s: icon for the "Pro" section */
                        __('%s Unlock more possibilities', 'master-addons'),
                        '<i class="eicon-pro-icon"></i>'
                    ),
                ]
            );

            $this->add_control(
                'jltma_get_pro_style_tab',
                [
                    'label' => __('Unlock more possibilities', 'master-addons'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        '1' => [
                            'title' => '',
                            'icon' => 'eicon-lock',
                        ],
                    ],
                    'default' => '1',
                    'toggle'    => false,
                    'description' => Helper::unlock_pro_feature(),
                ]
            );

            $this->end_controls_section();
        }
    }

    /**
     * Add Help Docs section with links from config
     * Auto-detects widget key from $this->get_name() if not provided
     *
     * @param string $widget_key Optional widget key (defaults to $this->get_name())
     */
    public function jltma_help_docs($widget_key = '')
    {
        // Auto-detect widget key from widget name if not provided
        if (empty($widget_key)) {
            $widget_key = $this->get_name();
        }

        // Get docs from config
        $docs = Config::get_addon_docs($widget_key);

        // Skip if no links available
        if (empty($docs['demo_url']) && empty($docs['docs_url']) && empty($docs['tuts_url'])) {
            return;
        }

        $demo_url = $docs['demo_url'] ?? '';
        $docs_url = $docs['docs_url'] ?? '';
        $tuts_url = $docs['tuts_url'] ?? '';

        $this->start_controls_section(
            'jltma_section_help_docs',
            [
                'label' => esc_html__('Help Docs', 'master-addons'),
            ]
        );

        $control_index = 1;

        if (!empty($demo_url)) {
            $this->add_control(
                'help_doc_' . $control_index++,
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(
                        /* translators: %1$s: Opening anchor tag, %2$s: Closing anchor tag. */
                        esc_html__('%1$s Live Demo %2$s', 'master-addons'),
                        '<a href="' . esc_url($demo_url) . '" target="_blank" rel="noopener">',
                        '</a>'
                    ),
                    'content_classes' => 'jltma-editor-doc-links',
                ]
            );
        }

        if (!empty($docs_url)) {
            $this->add_control(
                'help_doc_' . $control_index++,
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(
                        /* translators: %1$s: Opening anchor tag, %2$s: Closing anchor tag. */
                        esc_html__('%1$s Documentation %2$s', 'master-addons'),
                        '<a href="' . esc_url($docs_url) . '?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">',
                        '</a>'
                    ),
                    'content_classes' => 'jltma-editor-doc-links',
                ]
            );
        }

        if (!empty($tuts_url)) {
            $this->add_control(
                'help_doc_' . $control_index++,
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(
                        /* translators: %1$s: Opening anchor tag, %2$s: Closing anchor tag. */
                        esc_html__('%1$s Watch Video Tutorial %2$s', 'master-addons'),
                        '<a href="' . esc_url($tuts_url) . '" target="_blank" rel="noopener">',
                        '</a>'
                    ),
                    'content_classes' => 'jltma-editor-doc-links',
                ]
            );
        }

        $this->end_controls_section();
    }
}
