<?php 
namespace Elementor;

class xevso_flipbox_Widget extends Widget_Base {

    public function get_name() {

        return 'xevso_flipbox';
    }

    public function get_title() {
        return esc_html__( 'xevso Flip box', 'xevsocore' );
    }

    public function get_icon() {

        return 'eicon-flip-box';
    }

    public function get_categories() {
        return ['xevsocore'];
    }

    protected function _register_controls() {

        //Content tab start
        $this->start_controls_section(
            'xevso_flipbox_front',
            [
                'label' => esc_html__( 'xevso Flipbox Front', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'xevso_flipbox_fenable_icon',
            [
                'label' => esc_html__( 'Enable Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'xevso_flipbox_front_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'condition' => [
                    'xevso_flipbox_fenable_icon' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'xevso_flipbox_front_text',
            [
                'label' => esc_html__( 'Title', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'IT Service', 'xevsocore' ),
            ]
        );
        $this->add_control(
            'xevso_flipbox_front_textarea',
            [
                'label' => esc_html__( 'Textarea', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'We’ve been a strategy thought leader for nearly five decades and we bring unrivaled', 'xevsocore' ),
                'show_label' => true,
            ]
        );
        $this->add_control(
            'xevso_flipbox_fenable_btn',
            [
                'label' => esc_html__( 'Enable Button', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fselect_btn',
            [
                'label' => esc_html__( 'Select Button', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '1'  => esc_html__( 'Page Link', 'xevsocore' ),
                    '2' => esc_html__( 'Extranal Link', 'xevsocore' ),
                ],
                'condition' => [
                    'xevso_flipbox_fenable_btn' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fbtn_page',
            [
                'label' => esc_html__( 'Select Page', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => xevso_page_list(),
                'condition' => [
                    'xevso_flipbox_fselect_btn' => '1',
                    'xevso_flipbox_fenable_btn' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'xevso_flipbox_fbtn_link',
            [
                'label' => __( 'Add Button Link', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'xevsocore' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'xevso_flipbox_fselect_btn' => '2',
                    'xevso_flipbox_fenable_btn' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'xevso_flipbox_fbtn_text',
            [
                'label' => esc_html__( 'Button Text', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Read More', 'xevsocore' ),
                'condition' => [
                    'xevso_flipbox_fenable_btn' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_flipbox_back',
            [
                'label' => esc_html__( 'xevso Flipbox Back', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'xevso_flipbox_benable_icon',
            [
                'label' => esc_html__( 'Enable Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'xevso_flipbox_back_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::ICONS,
                'condition' => [
                    'xevso_flipbox_benable_icon' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'xevso_flipbox_back_text',
            [
                'label' => esc_html__( 'Title', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'IT Service', 'xevsocore' ),
            ]
        );
        $this->add_control(
            'xevso_flipbox_back_textarea',
            [
                'label' => esc_html__( 'Textarea', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'We’ve been a strategy thought leader for nearly five decades and we bring unrivaled', 'xevsocore' ),
                'show_label' => true,
            ]
        );
        $this->add_control(
            'xevso_flipbox_benable_btn',
            [
                'label' => esc_html__( 'Enable Button', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bselect_btn',
            [
                'label' => esc_html__( 'Select Button', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '1'  => esc_html__( 'Page Link', 'xevsocore' ),
                    '2' => esc_html__( 'Extranal Link', 'xevsocore' ),
                ],
                'condition' => [
                    'xevso_flipbox_benable_btn' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bbtn_page',
            [
                'label' => esc_html__( 'Select Page', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => xevso_page_list(),
                'condition' => [
                    'xevso_flipbox_bselect_btn' => '1',
                    'xevso_flipbox_benable_btn' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'xevso_flipbox_bbtn_link',
            [
                'label' => __( 'Add Button Link', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'xevsocore' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'condition' => [
                    'xevso_flipbox_bselect_btn' => '2',
                    'xevso_flipbox_benable_btn' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'xevso_flipbox_bbtn_text',
            [
                'label' => esc_html__( 'Button Text', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Read More', 'xevsocore' ),
                'condition' => [
                    'xevso_flipbox_benable_btn' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_flipbox_box_css',
            [
                'label' => esc_html__( 'xevso Flipbox Box CSS', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'xevso_flipbox_box_aligment',
            [
                'label' => __( 'Alignment', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'xevsocore' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'xevsocore' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'xevsocore' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .flipbox' => 'text-align: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_box_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .flipbox' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_box_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '50',
                    'right' => '50',
                    'bottom' => '50',
                    'left' => '50',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .flipbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
       $this->add_responsive_control(
           'xevso_flipbox_fornt_box_height',
           [
               'label' => esc_html__( 'Height', 'xevsocore' ),
               'type' => Controls_Manager::SLIDER,
               'size_units' => [ 'px' ],
               'range' => [
                   'px' => [
                       'min' => 150,
                       'max' => 500,
                       'step' => 1,
                   ]
               ],
               'default' => [
                   'unit' => 'px',
                   'size' => 250,
               ],
               'selectors' => [
                   '{{WRAPPER}} .flip-container, .front, .back' => 'height: {{SIZE}}{{UNIT}};',
               ],
           ]
       );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_flipbox_fornt_css',
            [
                'label' => esc_html__( 'xevso Flipbox Front CSS', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'xevso_fficnote',
            [
                'label' => __( 'Icon CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'xevso_flipbox_benable_icon' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox .icon i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_icon_bcolor',
            [
                'label' => esc_html__( 'Icon BG Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox .icon i' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_flipbox_fornt_css_icon_size',
                'label' => esc_html__( 'Icon Size', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front.flipbox .icon i',
                'exclude' => [
                    'font_family',
                    'font_weight',
                    'font_style',
                    'letter_spacing',
			        'text_transform',
			        'text_decoration',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_flipbox_fornt_css_icon_border',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front.flipbox .icon i',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_icon_radius',
            [
                'label' => esc_html__( 'Icon Border Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox .icon i' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_flipbox_fornt_css_icon_shadow',
                'label' => esc_html__( 'Icon Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front.flipbox .icon i',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_icon_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '20',
                    'left' => '0',
                    'isLinked' => true
                    ],
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox .icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_fftnote',
            [
                'label' => __( 'Title CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_title_color',
            [
                'label' => esc_html__( 'Title Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .front .flip-content h2' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_flipbox_fornt_css_title_typography',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front .flip-content h2',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_title_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '15',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .front .flip-content h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_title_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .front .flip-content h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_ffdecnote',
            [
                'label' => __( 'Description CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_dec_color',
            [
                'label' => esc_html__( 'Description Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_flipbox_fornt_css_dec_typography',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front.flipbox p',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_dec_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '20',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_dec_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_ffbtncnote',
            [
                'label' => __( 'Button CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'xevso_flipbox_fenable_btn' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_btn_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .front .flip-btn .blob-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'xevso_flipbox_fenable_btn' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fornt_css_btn_padding',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '15',
                    'right' => '45',
                    'bottom' => '15',
                    'left' => '45',
                    'isLinked' => true
                    ],
                'selectors' => [
                    '{{WRAPPER}} .front .flip-btn .blob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'xevso_flipbox_fenable_btn' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fbtn_ncolor',
            [
                'label' => esc_html__( 'Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .front .flip-btn .blob-btn' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fbtn_nbg',
            [
                'label' => esc_html__( 'Background Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .front .flip-btn .blob-btn__inner' => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_flipbox_fbtn_nborder',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front .flip-btn .blob-btn',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fbtn_nradius',
            [
                'label' => esc_html__( 'Border Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .front .flip-btn .blob-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_flipbox_fbtn_nshadow',
                'label' => esc_html__( 'Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front .flip-btn .blob-btn',
            ]
        );
        $this->add_control(
            'xevso_ffother',
            [
                'label' => __( 'Others CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'xevso_flipbox_fornt_css_bg',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .front.flipbox',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_flipbox_fborder',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front.flipbox',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_fradius',
            [
                'label' => esc_html__( 'Border Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .front.flipbox' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_flipbox_fshadow',
                'label' => esc_html__( 'Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .front.flipbox',
            ]
        );
        $this->end_controls_section();
        

        $this->start_controls_section(
            'xevso_flipbox_back_css',
            [
                'label' => esc_html__( 'xevso Flipbox Back CSS', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'xevso_bficnote',
            [
                'label' => __( 'Icon CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'xevso_flipbox_benable_icon' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox .icon i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_icon_bcolor',
            [
                'label' => esc_html__( 'Icon BG Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox .icon i' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_flipbox_back_css_icon_size',
                'label' => esc_html__( 'Icon Size', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back.flipbox .icon i',
                'exclude' => [
                    'font_family',
                    'font_weight',
                    'font_style',
                    'letter_spacing',
			        'text_transform',
			        'text_decoration',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_flipbox_back_css_icon_border',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back.flipbox .icon i',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_icon_radius',
            [
                'label' => esc_html__( 'Icon Border Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox .icon i' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_flipbox_back_css_icon_shadow',
                'label' => esc_html__( 'Icon Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back.flipbox .icon i',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_icon_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '20',
                    'left' => '0',
                    'isLinked' => true
                    ],
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox .icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox .icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_bftnote',
            [
                'label' => __( 'Title CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_title_color',
            [
                'label' => esc_html__( 'Title Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back .flip-content h2' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_flipbox_back_css_title_typography',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back .flip-content h2',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_title_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '15',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .back .flip-content h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_title_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .back .flip-content h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_bfdecnote',
            [
                'label' => __( 'Description CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_dec_color',
            [
                'label' => esc_html__( 'Description Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back .flip-content p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_flipbox_back_css_dec_typography',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back.flipbox p',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_dec_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '20',
                    'left' => '0',
                    'isLinked' => true
                ],
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_back_css_dec_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_bfbtncnote',
            [
                'label' => __( 'Button CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'xevso_flipbox_benable_btn' => 'yes',
                ],
            ]
        );
        $this->start_controls_tabs(
			'xevso_flipbox_bbtn_tabs'
        );
        $this->start_controls_tab(
			'xevso_flipbox_bbtn_tab_normal',
			[
                'label' => __( 'Button Normal', 'xevsocore' ),
                'condition' => [
                    'xevso_flipbox_benable_btn' => 'yes',
                ],
			]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bbtn_ncolor',
            [
                'label' => esc_html__( 'Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back .flip-btn .blob-btn' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bbtn_nbg',
            [
                'label' => esc_html__( 'Background Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back .flip-btn .blob-btn__inner' => 'background: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_flipbox_bbtn_nborder',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back .flip-btn .blob-btn',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bbtn_nradius',
            [
                'label' => esc_html__( 'Border Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .back .flip-btn .blob-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_flipbox_bbtn_nshadow',
                'label' => esc_html__( 'Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back .flip-btn .blob-btn',
            ]
        );
        $this->end_controls_tab();


        $this->start_controls_tab(
			'xevso_flipbox_btn_tab_hover',
			[
                'label' => __( 'Button Hover', 'xevsocore' ),
                'condition' => [
                    'xevso_flipbox_benable_btn' => 'yes',
                ],
			]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bbtn_hcolor',
            [
                'label' => esc_html__( 'Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back .flip-btn .blob-btn:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bbtn_hbg',
            [
                'label' => esc_html__( 'Background Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .back .flip-btn .blob-btn:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
         $this->add_responsive_control(
            'xevso_fliox_back_btnew_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .back .flip-btn .blob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_flipbox_bbtn_hborder',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back .flip-btn .blob-btn:hover',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bbtn_hradius',
            [
                'label' => esc_html__( 'Border Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .back .flip-btn .blob-btn:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_flipbox_bbtn_hshadow',
                'label' => esc_html__( 'Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back .flip-btn .blob-btn:hover',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

       
        $this->add_control(
            'xevso_bfother',
            [
                'label' => __( 'Others CSS', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'xevso_flipbox_back_css_bg',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .back.flipbox',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_flipbox_bborder',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back.flipbox',
            ]
        );
        $this->add_responsive_control(
            'xevso_flipbox_bradius',
            [
                'label' => esc_html__( 'Border Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .back.flipbox' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_flipbox_bshadow',
                'label' => esc_html__( 'Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .back.flipbox',
            ]
        );
        $this->end_controls_section();
    }
    //Render
    protected function render() {
        $settings = $this->get_settings_for_display();
        if( $settings['xevso_flipbox_fselect_btn'] == '2' ){
            $target = $settings['xevso_flipbox_fbtn_link']['is_external'] ? ' target="_blank"' : '';
            $nofollow = $settings['xevso_flipbox_fbtn_link']['nofollow'] ? ' rel="nofollow"' : '';
			$xevso_flip_url =  $settings['xevso_flipbox_fbtn_link']['url'];
			
		}else{
            $xevso_flip_url = get_page_link( $settings['xevso_flipbox_fbtn_page'] );
        }
        if( $settings['xevso_flipbox_bselect_btn'] == '2' ){
            $target1 = $settings['xevso_flipbox_bbtn_link']['is_external'] ? ' target="_blank"' : '';
            $nofollow1 = $settings['xevso_flipbox_bbtn_link']['nofollow'] ? ' rel="nofollow"' : '';
            $xevso_flipb_url = $settings['xevso_flipbox_bbtn_link']['url'];
		}else{
            $xevso_flipb_url = get_page_link( $settings['xevso_flipbox_bbtn_page'] );
		}
        ob_start();
        ?>
        <div class="flip-container">
            <div class="flipper">
                <div class="front flipbox">
                    <?php if($settings['xevso_flipbox_fenable_icon'] == 'yes' ){
                        echo '<div class="icon">
                                <i class="'.esc_attr($settings['xevso_flipbox_front_icon']['value']).'"></i>
                            </div>';
                    } ?>
                    <div class='flip-content'>
                        <?php if(!empty($settings['xevso_flipbox_front_text'])){
                            echo ' <h2>'.esc_attr($settings['xevso_flipbox_front_text']).'</h2>';
                        } 
                        if(!empty($settings['xevso_flipbox_front_textarea'])){
                            echo ' <p>'.esc_attr($settings['xevso_flipbox_front_textarea']).'</p>';
                        } 
                        if($settings['xevso_flipbox_fenable_btn'] == 'yes' ){
                            echo '
                                <div class="flip-btn">
                                    <a class="blob-btn" href="'.esc_url($xevso_flip_url).'">'.esc_html($settings['xevso_flipbox_fbtn_text']).'
                                    </a>
                                </div>
                            ';
                        }
                        
                        ?>
                        
                    </div>
                </div>
                <div class="back flipbox">
                    <?php if($settings['xevso_flipbox_benable_icon'] == 'yes' ){
                        echo '<div class="icon">
                                    <i class="'.esc_attr($settings['xevso_flipbox_back_icon']['value']).'"></i>
                                </div>';
                    } ?>
                    <div class='flip-content'>
                        <?php if(!empty($settings['xevso_flipbox_back_text'])){
                            echo ' <h2>'.esc_attr($settings['xevso_flipbox_back_text']).'</h2>';
                        } 
                        if(!empty($settings['xevso_flipbox_back_textarea'])){
                            echo ' <p>'.esc_attr($settings['xevso_flipbox_back_textarea']).'</p>';
                        } 
                        if($settings['xevso_flipbox_benable_btn'] == 'yes' ){
                            echo '
                                <div class="flip-btn">
                                    <a class="blob-btn" href="'.esc_url($xevso_flipb_url).'">'.esc_html($settings['xevso_flipbox_bbtn_text']).'
                                    </a>
                                </div>
                            ';
                        }
                       
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo ob_get_clean();

    }
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_flipbox_Widget );