<?php
namespace Elementor;

class xevso_dot_shape_Widget extends Widget_Base {

    public function get_name() {

        return 'xevso_dot_shape';
    }

    public function get_title() {
        return esc_html__( 'xevso Dot shape', 'xevsocore' );
    }

    public function get_icon() {

        return 'eicon-apps';
    }

    public function get_categories() {
        return ['xevsocore'];
    }

    protected function _register_controls() {

        //Content tab start
        $this->start_controls_section(
            'xevso_dots_shape_options',
            [
                'label' => esc_html__( 'Animation', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'xevso_dots_animation_enable',
            [
                'label'     => esc_html__( 'Enable Animation', 'xevsocore' ),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'label_on'  => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
            ]
        );
        $this->add_control(
            'xevso_dots_shape_select',
            [
                'label'     => esc_html__( 'Select Animation', 'xevsocore' ),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'shapeMover',
                'options'   => [
                    'shapeMover'        => esc_html__( 'Shape Mover', 'xevsocore' ),
                    'bubbleMover'       => esc_html__( 'Bubble Mover', 'xevsocore' ),
                    'bounce'            => esc_html__( 'Bounce', 'xevsocore' ),
                    'zoomIn'            => esc_html__( 'ZoomIn', 'xevsocore' ),
                    'flash'             => esc_html__( 'Flash', 'xevsocore' ),
                    'pulse'             => esc_html__( 'Pulse', 'xevsocore' ),
                    'rubberBand'        => esc_html__( 'Rubber Band', 'xevsocore' ),
                    'shake'             => esc_html__( 'ShakeX', 'xevsocore' ),
                    'fadeIn'            => esc_html__( 'FadeIn', 'xevsocore' ),
                    'fadeInDown'        => esc_html__( 'FadeIn Down', 'xevsocore' ),
                    'fadeInLeft'        => esc_html__( 'FadeIn Left', 'xevsocore' ),
                    'fadeInRight'       => esc_html__( 'FadeIn Right', 'xevsocore' ),
                    'fadeInUp'          => esc_html__( 'FadeIn Up', 'xevsocore' ),
                    'fadeOut'           => esc_html__( 'FadeOut', 'xevsocore' ),
                    'fadeOutDown'       => esc_html__( 'FadeOut Down', 'xevsocore' ),
                    'fadeOutLeft'       => esc_html__( 'FadeOut Left', 'xevsocore' ),
                    'fadeOutRight'      => esc_html__( 'FadeOut Right', 'xevsocore' ),
                    'fadeOutUp'         => esc_html__( 'FadeOut Up', 'xevsocore' ),
                    'flip'              => esc_html__( 'Flip', 'xevsocore' ),
                    'flipInX'           => esc_html__( 'FlipInX', 'xevsocore' ),
                    'flipInY'           => esc_html__( 'FlipInY', 'xevsocore' ),
                    'rotateIn'          => esc_html__( 'RotateIn', 'xevsocore' ),
                    'rotateInDownLeft'  => esc_html__( 'RotateIn Down Left', 'xevsocore' ),
                    'rotateInDownRight' => esc_html__( 'RotateIn Down Right', 'xevsocore' ),
                    'rotateInUpLeft'    => esc_html__( 'RotateIn Up Left', 'xevsocore' ),
                    'rotateInUpRight'   => esc_html__( 'RotateIn Up Right', 'xevsocore' ),
                    'rotateOut'         => esc_html__( 'Rotate Out', 'xevsocore' ),
                    'hinge'             => esc_html__( 'Hinge', 'xevsocore' ),
                    'slideInDown'       => esc_html__( 'SlideIn Down', 'xevsocore' ),
                    'slideInLeft'       => esc_html__( 'SlideIn Left', 'xevsocore' ),
                    'slideInRight'      => esc_html__( 'SlideIn Right', 'xevsocore' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .shapeanimation' => 'animation-name: {{VALUE}};',
                ],
                'condition' => [
                    'xevso_dots_animation_enable' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_dots_shape_duration',
            [
                'label'      => esc_html__( 'Animation Duration', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['s'],
                'range'      => [
                    's' => [
                        'min'  => 0.1,
                        'max'  => 100,
                        'step' => 0.1,
                    ],
                ],
                'default'    => [
                    'unit' => 's',
                    'size' => 9,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .shapeanimation' => '-webkit-animation-duration: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .shapeanimation' => 'animation-duration: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [
                    'xevso_dots_animation_enable' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_dot_shape_CSS',
            [
                'label' => esc_html__( 'Dot Shape', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'xevso_dot_shape_width',
            [
                'label'      => esc_html__( 'Width', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['em', 'px'],
                'range'      => [
                    'em' => [
                        'min'  => 2,
                        'max'  => 200,
                        'step' => 1,
                    ],
                    'px' => [
                        'min'  => 10,
                        'max'  => 800,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'em',
                    'size' => 20,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .dot-shapes' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_dot_shape_height',
            [
                'label'      => esc_html__( 'Height', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['em', 'px'],
                'range'      => [
                    'em' => [
                        'min'  => 2,
                        'max'  => 200,
                        'step' => 1,
                    ],
                    'px' => [
                        'min'  => 10,
                        'max'  => 800,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'em',
                    'size' => 20,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .dot-shapes' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_dot_shape_top',
            [
                'label'      => esc_html__( 'Position Top To Bottom', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .dot-shapes.shape_dots' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_dot_shape_left',
            [
                'label'      => esc_html__( 'Position Left To Right', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .dot-shapes.shape_dots' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'xevso_dot_shape_size',
            [
                'label'      => esc_html__( 'Dot Size', 'xevsocore' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => [
                    'px' => [
                        'min'  => 10,
                        'max'  => 40,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 18,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .dot-shapes.shape_dots' => '-webkit-mask-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_dot_shape_color',
            [
                'label'     => esc_html__( 'Color', 'xevsocore' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dot-shapes.shape_dots' => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_section();
    }
    //Render
    protected function render() {
        $settings = $this->get_settings_for_display();

        ob_start();
        ?>
        <div class="dot-shapes shape_dots dot-animate shapeanimation"></div>
        <?php
echo ob_get_clean();

    }
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_dot_shape_Widget );