<?php

namespace MasterAddons\Modules\Animation;

use \Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

if (!class_exists('MasterAddons\Modules\Animation\Particles')) {
class Particles
{
    private static $_instance = null;

    public function __construct()
    {
        // Register controls
        add_action('elementor/element/after_section_end', [$this, 'register_controls'], 10, 3);

        // Editor preview scripts
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_editor_scripts']);

        // Editor template
        add_action('elementor/section/print_template', [$this, '_print_template'], 10, 2);
        add_action('elementor/column/print_template', [$this, '_print_template'], 10, 2);
        add_action('elementor/container/print_template', [$this, '_print_template'], 10, 2);

        // Frontend render
        add_action('elementor/frontend/section/before_render', [$this, '_before_render'], 10, 1);
        add_action('elementor/frontend/column/before_render', [$this, '_before_render'], 10, 1);
        add_action('elementor/frontend/container/before_render', [$this, '_before_render'], 10, 1);

        // Inline CSS
        add_action('elementor/frontend/section/after_render', [$this, 'after_render']);
        add_action('elementor/frontend/column/after_render', [$this, 'after_render']);
        add_action('elementor/frontend/container/after_render', [$this, 'after_render']);
    }

    public function enqueue_editor_scripts()
    {
        wp_enqueue_script('master-addons-particles');
    }

    public function register_controls($element, $section_id, $args)
    {
        if (
            ('section' === $element->get_name() && 'section_background' === $section_id) ||
            ('column' === $element->get_name() && 'section_style' === $section_id) ||
            ('container' === $element->get_name() && 'section_background' === $section_id)
        ) {
            $element->start_controls_section(
                'ma_el_particles',
                [
                    'tab'   => Controls_Manager::TAB_STYLE,
                    'label' => __('Particles', 'master-addons') . JLTMA_EXTENSION_BADGE
                ]
            );

            $element->add_control(
                'ma_el_enable_particles',
                [
                    'type'         => Controls_Manager::SWITCHER,
                    'label'        => __('Enable Particle Background', 'master-addons'),
                    'default'      => '',
                    'label_on'     => __('Yes', 'master-addons'),
                    'label_off'    => __('No', 'master-addons'),
                    'return_value' => 'yes',
                    'prefix_class' => 'jltma-particle-',
                    'render_type'  => 'template',
                ]
            );

            $element->add_control(
                'ma_el_particle_area_zindex',
                [
                    'label'              => __('Z-index', 'master-addons'),
                    'type'               => Controls_Manager::NUMBER,
                    'default'            => 0,
                    'condition'          => [
                        'ma_el_enable_particles' => 'yes',
                    ],
                    'frontend_available' => true,
                ]
            );

            $element->add_control(
                'ma_el_enable_particles_alert',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'content_classes' => 'elementor-control-field-description',
                    'raw'             => __('<a href="https://vincentgarreau.com/particles.js/" target="_blank">Click here</a> to generate JSON for the below field.', 'master-addons'),
                    'separator'       => 'none',
                    'condition'       => [
                        'ma_el_enable_particles' => 'yes',
                    ],
                ]
            );

            $element->add_control(
                'ma_el_particle_json',
                [
                    'type'        => Controls_Manager::CODE,
                    'label'       => __('Particle JSON', 'master-addons'),
                    'default'     => '{"particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"}},"opacity":{"value":0.5,"random":false},"size":{"value":3,"random":true},"line_linked":{"enable":true,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4}}},"retina_detect":true}',
                    'render_type' => 'template',
                    'condition'   => [
                        'ma_el_enable_particles' => 'yes'
                    ]
                ]
            );

            $element->end_controls_section();
        }
    }

    public function _before_render($element)
    {
        if (!in_array($element->get_name(), ['section', 'column', 'container'])) {
            return;
        }

        $settings = $element->get_settings();

        if ($settings['ma_el_enable_particles'] === 'yes') {
            $particle_json = $settings['ma_el_particle_json'];
            $particle_data = json_decode($particle_json, true);

            if ($particle_data) {
                $element->add_render_attribute('_wrapper', 'data-jltma-particle', wp_json_encode($particle_data));
            }
            $element->add_render_attribute('_wrapper', 'data-jltma-particle-zindex', $settings['ma_el_particle_area_zindex']);

            // Enqueue particles vendor library + module script
            wp_enqueue_script('jltma-particles');
            wp_enqueue_script('master-addons-particles');
        }
    }

    public function _print_template($template, $widget)
    {
        if (!in_array($widget->get_name(), ['section', 'column', 'container'])) {
            return $template;
        }

        ob_start();
        ?>
        <# if (settings.ma_el_enable_particles === 'yes') {
            var zindex = settings.ma_el_particle_area_zindex || 0;
        #>
            <div class="jltma-particle-wrapper" id="jltma-particle-{{ view.getID() }}" data-jltma-particles-editor="{{ settings.ma_el_particle_json }}" style="position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;pointer-events:none;z-index:{{ zindex }};"></div>
        <# } #>
        <?php
        return $template . ob_get_clean();
    }

    public function after_render($element)
    {
        $data     = $element->get_data();
        $settings = $element->get_settings_for_display();
        $type     = $data['elType'];

        if (!in_array($type, ['section', 'column', 'container']) || $settings['ma_el_enable_particles'] !== 'yes') {
            return;
        }

        $id = $element->get_id();
        $z  = !empty($settings['ma_el_particle_area_zindex']) ? (int) $settings['ma_el_particle_area_zindex'] : 0;
        ?>
        <style>
        .elementor-element-<?php echo esc_attr($id); ?>.jltma-particle-yes{position:relative;overflow:hidden;}
        .elementor-element-<?php echo esc_attr($id); ?> .jltma-particle-wrapper{position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;pointer-events:none;z-index:<?php echo esc_attr($z); ?>;}
        .elementor-element-<?php echo esc_attr($id); ?> .jltma-particle-wrapper>canvas{position:absolute;top:0;left:0;width:100%!important;height:100%!important;pointer-events:none;}
        </style>
        <?php
    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
}

Particles::instance();
