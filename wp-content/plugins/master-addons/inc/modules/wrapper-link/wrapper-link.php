<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \Elementor\Element_Base;


/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 2/12/2020
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.


class JLTMA_Extension_Wrapper_Link
{

    private static $instance = null;

    private function __construct()
    {
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'jltma_wrapper_link_add_controls_section'], 1);
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'jltma_wrapper_link_add_controls_section'], 1);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'jltma_wrapper_link_add_controls_section'], 1);
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'jltma_wrapper_link_add_controls_section'], 1);

        add_action('elementor/frontend/before_render', [$this, 'widget_before_render_content'], 1);
    }

    public function jltma_wrapper_link_add_controls_section(\Elementor\Element_Base $element)
    {

        $tabs = Controls_Manager::TAB_CONTENT;

        if ('section' === $element->get_name() || 'column' === $element->get_name()  || 'container' === $element->get_name()) {
            $tabs = Controls_Manager::TAB_LAYOUT;
        }

        $element->start_controls_section(
            'jltma_section_wrapper_link',
            [
                'label' => JLTMA_BADGE . esc_html__('Wrapper Link', 'master-addons'),
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
                'placeholder' => 'https://wrapper-link.com',
            ]
        );

        $element->end_controls_section();
    }



    public function widget_before_render_content(\Elementor\Element_Base $element)
    {

		$link_settings = $element->get_settings_for_display( 'jltma_section_element_link' );
        if (empty($link_settings['url'])) return;

		$link_settings['url'] = esc_url( $link_settings['url'] ?? '' );
		unset( $link_settings['custom_attributes'] );

		if ( $link_settings && ! empty( $link_settings['url'] ) ) {
			$element->add_render_attribute(
				'_wrapper',
				[
					'data-jltma-wrapper-link' => json_encode( $link_settings ),
					'style'                   => 'cursor: pointer'
				]
			);
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

JLTMA_Extension_Wrapper_Link::get_instance();
