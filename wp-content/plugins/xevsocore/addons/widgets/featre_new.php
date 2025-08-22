<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor progress widget.
 *
 * Elementor widget that displays an escalating progress bar.
 *
 * @since 1.0.0
 */
class xevso_new_feture_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve progress widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'xevso-newfet';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve progress widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Feature New', 'xevsocore' );
	}

    
	public function get_categories() {
		return [ 'xevsocore' ];
	}
    
	/**
	 * Get widget icon.
	 *
	 * Retrieve progress widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-t-letter';
	}
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'xevso', 'animatebtn' ];
	}

	/**
	 * Register progress widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		
		$this->start_controls_section(
			'xevso_buttons_section',
			[
				'label' => esc_html__( 'Feature Area', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		 $this->add_control(
            'feat_font_iconbox_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::ICONS,
            ]
         );
		 
		 $this->add_control(
			'xevso_feature_number',
			[
			    'label' => esc_html__( 'Feature Number', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('01','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_feature_title',
			[
			    'label' => esc_html__( 'Feature Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_feature_des',
			[
			    'label' => esc_html__( 'Feature Description', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Lock Our Protfolo','xevsocore')
			]
		);
		
		 $this->add_control(
            'feat_font_num_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::ICONS,
            ]
         );
		
		$this->add_control(
			'xevso_feature_nmer_link',
			[
			    'label' => esc_html__( 'Feature Icon Link', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('#','xevsocore')
			]
		);
		

		$this->end_controls_section();
		
		
		
		
		
		$this->start_controls_section(
			'xevso_sliders_two_video_btns',
			[
				'label' => esc_html__( 'Feature Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
			]
		);
		
		
	    $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'Feature_back_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'gradient', 'classic'],
				'selector' => '{{WRAPPER}} .service_item',
			]
		);
		
		 $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'Feature_hov_bg',
				'label' => __( 'Hover Background', 'xevsocore' ),
				'types' => [ 'gradient', 'classic'],
				'selector' => '{{WRAPPER}} .service_item:before ',
			]
		);


	$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'Feature_back_hover_bg',
				'label' => __( 'Before ', 'xevsocore' ),
				'types' => [ 'gradient', 'classic'],
				'selector' => '{{WRAPPER}} .service_item:hover',
			]
		);	
		
		
		$this->add_responsive_control(
			'xevso_buttons_title_padding',
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
					'{{WRAPPER}} .service_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_allre_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service_item',
			]
		);
		
		$this->end_controls_section();
		
		
		
		
$this->start_controls_section(
            'xevso_fet_iconbox_css_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'xevso_fet_iconbox_css_icon_size',
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
                    '{{WRAPPER}} .service_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_fet_iconbox_css_dec_typo',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service_icon i',
            ]
        );
		
        $this->add_responsive_control(
            'xevso_fet_iconbox_css_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_icon i' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'fet_font_iconer_hovrv_color',
            [
                'label' => esc_html__( 'Icon Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_item:hover .service_icon i' => 'color: {{VALUE}}',
                ],
            ]
        );
   
        $this->add_responsive_control(
            'xevso_fet_iconbox_css_icon_margin',
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
                    '{{WRAPPER}} .service_icon i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_fet_iconbox_css_icon_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .service_icon i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		
		
		
		$this->add_responsive_control(
            'fet_font_numb_color',
            [
                'label' => esc_html__( 'Number Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_icon span.right' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
            'fet_font_numb_hover_color',
            [
                'label' => esc_html__( 'Number Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_item:hover .service_icon span.right' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
			'fet_fon_numb_pad',
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
					'{{WRAPPER}} .service_icon span.right' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'fet_foont_numb_titl',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service_icon span.right',
            ]
        );	
		
		
		
		

		$this->add_responsive_control(
            'fet_font_title_color',
            [
                'label' => esc_html__( 'Title Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_content h3' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
            'fet_font_title_hover_color',
            [
                'label' => esc_html__( 'Title Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_item:hover h3' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
			'fet_font_ti_pad',
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
					'{{WRAPPER}} .service_content h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'fet_foont_con_titl',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service_content h3',
            ]
        );	
		
		
		$this->add_responsive_control(
            'fet_font_desc_color',
            [
                'label' => esc_html__( 'Description Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_content p' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
            'fet_font_deschov_color',
            [
                'label' => esc_html__( 'Description Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_item:hover p' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		
		$this->add_responsive_control(
			'fet_font_desc_pad',
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
					'{{WRAPPER}} .service_content p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'fet_foont_tp_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service_content p',
            ]
        );
		
		
		
		$this->add_responsive_control(
            'fet_font_btn_color',
            [
                'label' => esc_html__( 'Button Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_btn a' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
            'fet_font_btn_hov_color',
            [
                'label' => esc_html__( 'Button Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service_item:hover .service_btn a' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		
		$this->add_responsive_control(
			'fet_font_btn_pad',
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
					'{{WRAPPER}} .service_btn a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'fet_foont_btn_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service_btn a',
            ]
        );
		
		 $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'fet_bet_back_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .service_btn a',
			]
		);
		
		$this->add_control(
			'xevso_fet_bet_radius',
			[
				'label' => __( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .service_btn a' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_back_tf_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service_btn a',
			]
		);
		
		
        $this->end_controls_section();

		$this->end_controls_tab();
	}

	/**
	 * Render progress widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
					$settings = $this->get_settings_for_display();?>
					
					<div class="service_item">
						<div class="service_inner">
							<div class="service_icon">
							<i class="<?php echo esc_attr($settings['feat_font_iconbox_icon']['value']); ?>"></i>
							<span class="right"><?php echo esc_attr($settings['xevso_feature_number']); ?></span>
							</div>
							<div class="service_content">
								<h3><?php echo esc_attr($settings['xevso_feature_title']); ?></h3>
								<p><?php echo esc_attr($settings['xevso_feature_des']); ?></p>
							</div>
							<div class="service_btn">
								<a href="<?php echo esc_attr($settings['xevso_feature_nmer_link']); ?>"><i class="<?php echo esc_attr($settings['feat_font_num_icon']['value']); ?>"></i></a>
							</div>
						</div>
					</div>
		
	<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_new_feture_Widget );