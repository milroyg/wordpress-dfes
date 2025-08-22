<?php 
namespace Elementor;

class xevso_iconbox_Widget extends Widget_Base {

    public function get_name() {

        return 'xevso_iconbox';
    }

    public function get_title() {
        return esc_html__( 'xevso Icon Box', 'xevsocore' );
    }

    public function get_icon() {

        return 'eicon-bullet-list';
    }

    public function get_categories() {
        return ['xevsocore'];
    }

    protected function _register_controls() {

        //Content tab start
        $this->start_controls_section(
            'xevso_iconbox_options',
            [
                'label' => esc_html__( 'xevso Icon Box', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_style',
            [
                'label' => esc_html__( 'Select Style', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1'  => esc_html__( 'Style One', 'xevsocore' ),
                    '2' => esc_html__( 'Style Two', 'xevsocore' ),
                ],
            ]
        );
        $this->add_control(
            'xevso_iconbox_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::ICONS,
            ]
        );
        $this->add_control(
            'xevso_iconbox_title',
            [
                'label' => esc_html__( 'Title', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'IT Solutions Service', 'xevsocore' ),
            ]
        );
        $this->add_control(
            'xevso_iconbox_title_enable_link',
            [
                'label' => esc_html__( 'Enable Title Link', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'xevso_iconbox_dec',
            [
                'label' => esc_html__( 'Description', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'We’ve been a strategy thought leader for nearly five decades and we bring unrivaled', 'xevsocore' ),
                'show_label' => true,
            ]
        );
        $this->add_control(
            'xevso_iconbox_btn_link',
            [
                'label' => __( 'Link', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'xevsocore' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
            ]
        );
        $this->add_control(
            'xevso_iconbox_enable_btn',
            [
                'label' => esc_html__( 'Enable Button', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
        'xevso_iconbox_btn_text',
        [
            'label' => esc_html__( 'Button Text', 'xevsocore' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__( 'Read More', 'xevsocore' ),
            'condition' => [
                'xevso_iconbox_enable_btn' => 'yes',
            ],
        ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_iconbox_css_box',
            [
                'label' => esc_html__( 'Box', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'xevso_iconbox_css_box_alignemt',
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
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .iconbox' => 'text-align: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_box_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .iconbox' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_box_padding',
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
                    '{{WRAPPER}} .iconbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs(
			'xevso_iconbox_tabs'
		);

		$this->start_controls_tab(
			'xevso_iconbox_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'xevso_iconbox_nbg',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .iconbox',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_iconbox_nborder',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .iconbox',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_iconbox_css_box_nshaw',
                'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .iconbox',
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_iconbox_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'xevso_iconbox_hbg',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .iconbox:hover',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'xevso_iconbox_hborder',
                'label' => esc_html__( 'Border', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .iconbox:hover',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'xevso_iconbox_css_box_hshaw',
                'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .iconbox:hover',
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
        
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_iconbox_css_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 15,
                        'max' => 200,
                        'step' => 1,
                    ]
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 54,
                ],
                'selectors' => [
                    '{{WRAPPER}} .iconbox-icon i:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .iconbox-icon i:before' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_hcolor',
            [
                'label' => esc_html__( 'Icon Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .iconbox:hover i:before' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_margin',
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
                    '{{WRAPPER}} .iconbox-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_padding',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .iconbox-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_iconbox_css_title',
            [
                'label' => esc_html__( 'Tilte', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_iconbox_css_title_typo',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .iconbox-title span',
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_title_color',
            [
                'label' => esc_html__( 'Title Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .iconbox-title span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_title_hcolor',
            [
                'label' => esc_html__( 'Title Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .iconbox:hover .iconbox-title span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_title_margin',
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
                    '{{WRAPPER}} .iconbox-dec .iconbox-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_title_padding',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .iconbox-dec .iconbox-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_iconbox_css_dec',
            [
                'label' => esc_html__( 'Description', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_iconbox_css_dec_typo',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .iconbox-dec p',
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_dec_color',
            [
                'label' => esc_html__( 'Description Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .iconbox-dec p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_dec_hcolor',
            [
                'label' => esc_html__( 'Description Hver Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .iconbox:hover p' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_dec_margin',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .iconbox-dec p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_dec_padding',
            [
                'label' => esc_html__( 'Margin', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .iconbox-dec p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        
         $this->end_controls_section();
        // Button Start
		$this->start_controls_section(
			'xevso_blogs_btns',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_blogs_btns_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .iconbox-link a',
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .iconbox-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_padding',
			[
				'label' => esc_html__( 'Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '15',
					'right' => '30',
					'bottom' => '15',
					'left' => '30',
					'isLinked' => true
				],
				'selectors' => [
					'{{WRAPPER}} .iconbox-link a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'xevso_blogs_btns_tabs'
		);
		$this->start_controls_tab(
			'xevso_blogs_btns_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_ncolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .iconbox-link a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_blogs_btns_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .iconbox-link a',
			]
		);
		

		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_blogs_btns_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .iconbox-link a',
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_nradius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .iconbox-link a' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_blogs_btns_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .iconbox-link a:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_blogs_btns_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .iconbox-link a:hover',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_blogs_btns_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .iconbox-link a:hover',
			]
		);
		$this->add_responsive_control(
			'xevso_blogs_btns_hradius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .iconbox-link a:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

        $this->end_controls_section();
    }
    //Render
    protected function render() {
        $settings = $this->get_settings_for_display();
            $target = $settings['xevso_iconbox_btn_link']['is_external'] ? ' target="_blank"' : '';
            $nofollow = $settings['xevso_iconbox_btn_link']['nofollow'] ? ' rel="nofollow"' : '';
        ob_start();
        if($settings['xevso_iconbox_style'] == '1' ){
           ?>
            <div class="iconbox">
                <div class="iconbox-icon">
                    <i class="<?php echo esc_attr($settings['xevso_iconbox_icon']['value']); ?>"></i>
                </div>
                <div class="iconbox-dec">
                    <h5 class="iconbox-title">
                        <?php if($settings['xevso_iconbox_title_enable_link'] == 'yes' ){
                            echo ' <a href="">';
                        } ?>
                        <span><?php echo esc_html($settings['xevso_iconbox_title']); ?></span>
                        <?php if($settings['xevso_iconbox_title_enable_link'] == 'yes' ){
                            echo ' </a>';
                        } ?>
                    </h5>
                    <p><?php echo esc_html($settings['xevso_iconbox_dec']); ?></p>
                </div>
                <?php if($settings['xevso_iconbox_enable_btn'] == 'yes' ){
                    echo '<div class="iconbox-link">
                        <a class="blob-btn" href="'. esc_url($settings['xevso_iconbox_btn_link']['url']).'" ' . esc_attr($target) . esc_attr($nofollow) . ' class="link-details">'.$settings['xevso_iconbox_btn_text'].'
                        <span class="blob-btn__inner">
                            <span class="blob-btn__blobs">
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                            </span>
                        </span>
                        </a>
                    </div>';
                } ?>
            </div>
           <?php 
        }else{
            ?>
            <div class="iconbox iconbox-2">
                <div class="iconbox-icon">
                    <i class="<?php echo esc_attr($settings['xevso_iconbox_icon']['value']); ?>"></i>
                </div>
                <div class="iconbox-dec2">
                    <div class="iconbox-dec">
                        <h5 class="iconbox-title">
                            <?php if($settings['xevso_iconbox_title_enable_link'] == 'yes' ){
                                echo ' <a href="">';
                            } ?>
                            <span><?php echo esc_html($settings['xevso_iconbox_title']); ?></span>
                            <?php if($settings['xevso_iconbox_title_enable_link'] == 'yes' ){
                                echo ' </a>';
                            } ?>
                        </h5>
                        <p><?php echo esc_html($settings['xevso_iconbox_dec']); ?></p>
                    </div>
                    <?php if($settings['xevso_iconbox_enable_btn'] == 'yes' ){
                        echo '<div class="iconbox-link">
                        <a class="blob-btn" href="'. esc_url($settings['xevso_iconbox_btn_link']['url']).'" ' . esc_attr($target) . esc_attr($nofollow) . ' class="link-details">'.$settings['xevso_iconbox_btn_text'].'
                        <span class="blob-btn__inner">
                            <span class="blob-btn__blobs">
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                                <span class="blob-btn__blob"></span>
                            </span>
                        </span>
                        </a>
                        </div>';
                    } ?>
                </div>
            </div>
            <?php 
        }
        ?>
        <?php
        echo ob_get_clean();

    }
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_iconbox_Widget );