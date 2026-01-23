<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/5/2021
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.

if (!class_exists('MasterAddons\Modules\JLTMA_Extension_Glassmorphism')) {
class JLTMA_Extension_Glassmorphism
{

    /*
	* Instance of this class
	*/
    private static $instance = null;

    public function __construct()
    {
        // Add new controls to advanced tab globally
        // add_action("elementor/element/section/section_background/before_section_end", array($this, 'jltma_section_add_glassmorphism_controls'), 19, 3);
        // add_action("elementor/element/column/section_style/before_section_end", array($this, 'jltma_section_add_glassmorphism_controls'), 19, 3);
        // add_action("elementor/element/common/_section_background/before_section_end", array($this, 'jltma_section_add_glassmorphism_controls'), 19, 3);
        // add_action("elementor/element/container/section_background/before_section_end", array($this, 'jltma_section_add_glassmorphism_controls'), 19, 3);

        // // Add before_render hooks to apply class to container only
        // add_action('elementor/frontend/section/before_render', array($this, 'before_render_glass_effect'), 10);
        // add_action('elementor/frontend/column/before_render', array($this, 'before_render_glass_effect'), 10);
        // add_action('elementor/frontend/container/before_render', array($this, 'before_render_glass_effect'), 10);
        // add_action('elementor/frontend/widget/before_render', array($this, 'before_render_glass_effect'), 10);


        // Register controls
        add_action('elementor/element/after_section_end', [$this, 'jltma_section_add_glassmorphism_controls'], 11, 3);
    }

    private function add_controls($element, $args)
    {
        // Create separate section for glassmorphism
        $element->start_controls_section(
            'jltma_glassmorphism_section',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => __('Glassmorphism', 'master-addons') . JLTMA_EXTENSION_BADGE 
            ]
        );

        // Glassmorphism controls - filtered through Pro_Modules.php
        $glassmorphism_controls = apply_filters('master_addons/modules/glassmorphism/controls', [
            [
                'type'       => 'control',
                'control_id' => 'jltma_enable_glassmorphism_effect_pro',
                'args'       => [
                    'label'        => __('Enable Glassmorphism', 'master-addons'),
                    'type'         => Controls_Manager::SWITCHER,
                    'default'      => 'no',
                    'return_value' => 'yes',
                    'description'  => Master_Addons_Helper::upgrade_to_pro('Glassmorphism available on'),
                    'label_on'     => __('Enable', 'master-addons'),
                    'label_off'    => __('Disable', 'master-addons'),
                    'prefix_class' => 'jltma-glass-effect-',
                ],
            ],
        ]);

        foreach ($glassmorphism_controls as $control) {
            if ($control['type'] === 'control') {
                $element->add_control($control['control_id'], $control['args']);
            } elseif ($control['type'] === 'responsive_control') {
                $element->add_responsive_control($control['control_id'], $control['args']);
            }
        }

        $element->end_controls_section();
    }

    public function jltma_section_add_glassmorphism_controls($element, $section_id, $args)
    {
        // Only add glassmorphism section to specific elements after their sections
        if (
            ('widget' === $element->get_type() && '_section_style' === $section_id) ||
            ('section' === $element->get_name() && 'section_advanced' === $section_id) ||
            ('column' === $element->get_name() && 'section_advanced' === $section_id) ||
            ('container' === $element->get_name() && 'section_layout' === $section_id)
        ) {
            $this->add_controls($element, $args);
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

if (class_exists('MasterAddons\Modules\JLTMA_Extension_Glassmorphism')) {
    JLTMA_Extension_Glassmorphism::get_instance();
}
