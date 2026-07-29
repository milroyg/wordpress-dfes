<?php

namespace MasterAddons\Inc\Modules\Animation\ParallaxElements;

use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Parallax extension
 *
 * Adds parallax on widgets and columns
 *
 * @since 1.1.3
 */
class Parallax_Elements
{

    /**
     * Is Common Extension
     *
     * Defines if the current extension is common for all element types or not
     *
     * @since 1.8.0
     * @access private
     *
     * @var bool
     */
    protected $is_common = true;

    /**
     * Constructor
     *
     * @since 1.8.0
     */
    public function __construct()
    {
        $this->add_actions();
    }

    /**
     * A list of scripts that the widgets is depended in
     *
     * @since 1.8.0
     **/
    public function get_script_depends()
    {
        return [
            'parallax-element',
            'jquery-visible',
        ];
    }

    /**
     * Is disabled by default
     *
     * Return wether or not the extension should be disabled by default,
     * prior to user actually saving a value in the admin page.
     * Checks if Elementor Pro is enabled to allow for use of Motion Effects
     *
     * @access public
     * @since 2.1.0
     * @return bool
     */
    public static function is_default_disabled()
    {
        if (function_exists('is_elementor_pro_active') && is_elementor_pro_active()) {
            return true;
        }
        return false;
    }

    /**
     * The description of the current extension
     *
     * @since 1.8.0
     **/
    public static function get_description()
    {
        return __('Adds options to move a column or a widget vertically asynchronously when scrolling the page. Can be found under Advanced &rarr; Master Addons &rarr; Parallax.', 'master-addons');
    }

    /**
     * Add Actions
     *
     * @since 1.1.3
     *
     * @access private
     */
    private function add_controls($element, $args)
    {

        $element_type = $element->get_type();

        $element->add_control(
            'parallax_element_enable',
            [
                'label'            => __('Parallax', 'master-addons'),
                'type'             => Controls_Manager::SWITCHER,
                'default'         => '',
                'label_on'         => __('Yes', 'master-addons'),
                'label_off'     => __('No', 'master-addons'),
                'return_value'     => 'yes',
                'separator'        => 'before',
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'parallax_element_type',
            [
                'label'         => __('Type', 'master-addons'),
                'type'             => Controls_Manager::SELECT,
                'default'         => 'scroll',
                'options'             => [
                    'scroll'     => __('Scroll', 'master-addons'),
                    'mouse'     => __('Mouse', 'master-addons'),
                ],
                'condition' => [
                    'parallax_element_enable!' => '',
                ],
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'parallax_element_relative',
            [
                'label'         => __('Relative to', 'master-addons'),
                'description'     => __('Use "Start position" when the element is visible inside the viewport before scroll.', 'master-addons'),
                'type'             => Controls_Manager::SELECT,
                'default'         => 'middle',
                'options'             => [
                    'middle'         => __('Viewport middle', 'master-addons'),
                    'position'         => __('Start position', 'master-addons'),
                ],
                'condition' => [
                    'parallax_element_enable!'     => '',
                    'parallax_element_type'        => 'scroll',
                ],
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'parallax_element_pan_relative',
            [
                'label'         => __('Relative to', 'master-addons'),
                'type'             => Controls_Manager::SELECT,
                'default'         => 'element',
                'options'             => [
                    'element'         => __('Element Center', 'master-addons'),
                    'viewport'         => __('Viewport Center', 'master-addons'),
                ],
                'condition' => [
                    'parallax_element_enable!'     => '',
                    'parallax_element_type'        => 'mouse',
                ],
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'parallax_element_disable_on',
            [
                'label'     => __('Disable for', 'master-addons'),
                'type'         => Controls_Manager::SELECT,
                'default'     => 'mobile',
                'options'             => [
                    'none'         => __('None', 'master-addons'),
                    'tablet'     => __('Mobile and tablet', 'master-addons'),
                    'mobile'     => __('Mobile only', 'master-addons'),
                ],
                'condition' => [
                    'parallax_element_enable!'     => '',
                    'parallax_element_type'        => 'scroll',
                ],
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'parallax_element_pan_axis',
            [
                'label'     => __('Axis', 'master-addons'),
                'type'         => Controls_Manager::SELECT,
                'default'    => 'both',
                'options'     => [
                    'both'             => __('Both', 'master-addons'),
                    'vertical'         => __('Vertical', 'master-addons'),
                    'horizontal'     => __('Horizontal', 'master-addons'),
                ],
                'frontend_available' => true,
                'condition' => [
                    'parallax_element_enable!'     => '',
                    'parallax_element_type'        => 'mouse',
                ],
            ]
        );

        $element->add_control(
            'parallax_element_invert',
            [
                'label'            => __('Invert Direction', 'master-addons'),
                'type'             => Controls_Manager::SWITCHER,
                'default'         => '',
                'label_on'         => __('Yes', 'master-addons'),
                'label_off'     => __('No', 'master-addons'),
                'return_value'     => 'yes',
                'frontend_available' => true,
                'condition' => [
                    'parallax_element_enable!'     => '',
                ],
            ]
        );

        $element->add_control(
            'parallax_off_viewport',
            [
                'label'            => __('Move outside viewport', 'master-addons'),
                'description'    => __('Move elements even if they are not visible', 'master-addons'),
                'type'             => Controls_Manager::SWITCHER,
                'default'         => '',
                'label_on'         => __('Yes', 'master-addons'),
                'label_off'     => __('No', 'master-addons'),
                'return_value'     => 'yes',
                'frontend_available' => true,
                'condition'     => [
                    'parallax_element_enable!'     => '',
                ],
            ]
        );

        $element->add_responsive_control(
            'parallax_element_pan_distance',
            [
                'label'         => __('Max Distance (px)', 'master-addons'),
                'description'     => __('The maximum distance from the center of the element and the mouse pointer. Enter 0 or empty to disable.', 'master-addons'),
                'type'             => Controls_Manager::SLIDER,
                'range'         => [
                    'px'         => [
                        'min'    => 0,
                        'max'     => 500,
                        'step'    => 1,
                    ],
                ],
                'condition' => [
                    'parallax_element_enable!'             => '',
                    'parallax_element_type'                => 'mouse',
                    'parallax_element_pan_relative'        => 'element',
                ],
                'frontend_available' => true,
            ]
        );

        $element->add_responsive_control(
            'parallax_element_speed',
            [
                'label'         => __('Amount', 'master-addons'),
                'type'             => Controls_Manager::SLIDER,
                'default'        => ['size' => 0.15],
                'range'         => [
                    'px'         => [
                        'min'    => 0.1,
                        'max'     => 1,
                        'step'    => 0.01,
                    ],
                ],
                'condition' => [
                    'parallax_element_enable!'     => '',
                ],
                'frontend_available' => true,
            ]
        );
    }

    /**
     * Add Actions
     *
     * @since 1.1.3
     *
     * @access private
     */
    protected function add_actions()
    {

        // Activate controls for widgets
        add_action('elementor/element/common/section_master_addons_advanced/before_section_end', function ($element, $args) {

            $this->add_controls($element, $args);
        }, 10, 2);

        // Activate controls for columns
        add_action('elementor/element/column/section_master_addons_advanced/before_section_end', function ($element, $args) {

            $this->add_controls($element, $args);
        }, 10, 2);
    }
}
