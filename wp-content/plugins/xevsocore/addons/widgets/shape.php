<?php
namespace Elementor;

class xevso_Animation_shape_Widget extends Widget_Base {

    public function get_name() {

        return 'xevso_Animation_shape';
    }

    public function get_title() {
        return esc_html__( 'xevso Shape Animation', 'xevsocore' );
    }

    public function get_icon() {

        return 'eicon-handle';
    }

    public function get_categories() {
        return ['xevsocore'];
    }

    protected function _register_controls() {

        //Content tab start
        $this->start_controls_section(
            'xevso_Animation_shape_options',
            [
                'label' => esc_html__( 'xevso Animation Shape', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'xevso_shape_select',
            [
                'label'   => esc_html__( 'Select Animation', 'xevsocore' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'shapeMover',
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
            ]
        );
        $repeater->add_responsive_control(
            'xevso_shape_top',
            [
                'label' => esc_html__( 'Top To Bottom', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 2000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 100,
                ],
            ]
        );
        $repeater->add_responsive_control(
            'xevso_shape_left',
            [
                'label' => esc_html__( 'Left To Right', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 2000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 100,
                ],
            ]
        );
        $repeater->add_responsive_control(
            'xevso_shape_duration',
            [
                'label' => esc_html__( 'Animation Duration', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 's' ],
                'range' => [
                    's' => [
                        'min' => 0.1,
                        'max' => 100,
                        'step' => 0.1,
                    ]
                ],
                'default' => [
                    'unit' => 's',
                    'size' => 9,
                ],
            ]
        );
        $repeater->add_control(
            'xevso_shape_img',
            [
                'label'   => __( 'Choose Image', 'xevsocore' ),
                'type'    => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'xevso_shapes',
            [
                'label'   => esc_html__( 'Shape List', 'xevsocore' ),
                'type'    => \Elementor\Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => [
                    [
                        'xevso_shape_select' =>'shapeMover',
                        'xevso_shape_img' => '',
                        'xevso_shape_top' => '200',
                        'xevso_shape_left' => '300',
                        'xevso_shape_duration' => '9',
                    ],
                ],
            ]
        );
        $this->end_controls_section();
    }
    //Render
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <?php 
        ob_start();
        foreach($settings['xevso_shapes'] as $paylo_shape){
            echo '<img src="'.esc_url($paylo_shape['xevso_shape_img']['url']).'" alt="'.esc_attr('xevso','xevsocore').'" class="'.esc_attr($paylo_shape['xevso_shape_select']).' shapeanimation" style="top:'.esc_attr($paylo_shape['xevso_shape_top']['size']). esc_attr($paylo_shape['xevso_shape_top']['unit']).';left:'.esc_attr($paylo_shape['xevso_shape_left']['size']). esc_attr($paylo_shape['xevso_shape_left']['unit']).';-webkit-animation-duration:'.esc_attr($paylo_shape['xevso_shape_duration']['size']). esc_attr($paylo_shape['xevso_shape_duration']['unit']).'; animation-duration: '.esc_attr($paylo_shape['xevso_shape_duration']['size']). esc_attr($paylo_shape['xevso_shape_duration']['unit']).';-webkit-animation-name:'.esc_attr($paylo_shape['xevso_shape_select']).';
            animation-name: '.esc_attr($paylo_shape['xevso_shape_select']).';" >';
        }
        echo ob_get_clean();
    }
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_Animation_shape_Widget );