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
class xevso_animatebtn_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-animatebtn';
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
		return esc_html__( 'Service New', 'xevsocore' );
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
				'label' => esc_html__( 'Service Font', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		 $this->add_control(
            'servicefont_iconbox_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::ICONS,
            ]
         );
		
		$this->add_control(
			'xevso_colleps_title',
			[
			    'label' => esc_html__( 'Service Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_colleps_des',
			[
			    'label' => esc_html__( 'Service Description', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Lock Our Protfolo','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_colleps_nmer',
			[
			    'label' => esc_html__( 'Service Number', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('01','xevsocore')
			]
		);
		
		
		$this->end_controls_section();
		
		
		
		
		$this->start_controls_section(
			'xevso_buttons_section2',
			[
				'label' => esc_html__( 'Service Back', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
            'xevso_slider_two_img',
            [
                'label' => esc_html__('Image','xevsocore'),
                'type'=>Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
		
		$this->add_control(
			'service_back_title',
			[
			    'label' => esc_html__( 'Service Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		$this->add_control(
			'service_back_des',
			[
			    'label' => esc_html__( 'Service Description', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Lock Our Protfolo','xevsocore')
			]
		);
		
		$this->add_control(
			'service_back_num',
			[
			    'label' => esc_html__( 'Service Number', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('01','xevsocore')
			]
		);
		
		$this->add_control(
			'service_back_btn',
			[
			    'label' => esc_html__( 'Service Button Text ', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Read More','xevsocore')
			]
		);
		
		$this->add_control(
			'service_back_btn_link',
			[
			    'label' => esc_html__( 'Service Button Text Link ', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('#','xevsocore')
			]
		);
		
		
		$this->end_controls_section();
		
		
		
		$this->start_controls_section(
			'xevso_sliders_two_video_btns',
			[
				'label' => esc_html__( 'Service Font Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
			]
		);
		
		
	    $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'Service_back_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_allre_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main',
			]
		);
		
		
		
		 $this->add_responsive_control(
            'ser_font_title_orer',
            [
                'label' => esc_html__( 'Order Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--order' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
			'ser_font_ti_orer',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--order' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_foont_con_orer',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--order',
            ]
        );
		
		
		
		
		 $this->add_responsive_control(
            'ser_font_title_color',
            [
                'label' => esc_html__( 'Title Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--title' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
			'ser_font_ti_pad',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_foont_con_titl',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--title',
            ]
        );
		
		 $this->add_responsive_control(
            'ser_font_desc_color',
            [
                'label' => esc_html__( 'Description Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--description' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		
		$this->add_responsive_control(
			'ser_font_desc_pad',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_foont_con_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--description',
            ]
        );
		
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
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		
		  $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'Service_icon_back_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon',
			]
		);
		
		$this->add_control(
			'xevso_s2nbtn_radius',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_iconbox_css_dec_typo',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon i',
            ]
        );
		
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon i' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
		
		$this->start_controls_section(
			'xevso_sliders_refhase_btns',
			[
				'label' => esc_html__( 'Service Back Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'Service_hover_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-main .item--icon',
			]
		);
		
		 $this->add_responsive_control(
            'ser_back_title_color',
            [
                'label' => esc_html__( 'Title Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--title' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		$this->add_responsive_control(
			'ser_back_ti_pad',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_back_con_titl',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--title',
            ]
        );
		
		 $this->add_responsive_control(
            'ser_back_desc_color',
            [
                'label' => esc_html__( 'Description Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--description' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		
		$this->add_responsive_control(
			'ser_back_desc_pad',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_back_con_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--description',
            ]
        );
		
		
		
		$this->add_responsive_control(
            'ser_back_btton_color',
            [
                'label' => esc_html__( 'Button Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--link a' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		
		$this->add_responsive_control(
			'ser_btton_desc_pad',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--link a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_btton_con_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--link a',
            ]
        );	
		

	$this->add_responsive_control(
            'ser_back_orer2_color',
            [
                'label' => esc_html__( 'Order Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--order' => 'color: {{VALUE}}',
                ],
            ]
        );
		
		
		$this->add_responsive_control(
			'ser_btton_orer2_pad',
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
					'{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--order' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_btorer2_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .ct-fancy-box-layout6 .ct-fancy-hover .item--order',
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
        
			<div class="ct-fancy-box ct-fancy-box-layout6">
			<div class="ct-fancy-main">
				<div class="item--icon"> 
					<i aria-hidden="true" class="<?php echo esc_attr($settings['servicefont_iconbox_icon']['value']); ?>"></i>
					</div>
						<div class="item--holder">
							<h3 class="item--title"> <?php echo esc_attr($settings['xevso_colleps_title']); ?></h3>
								<div class="item--description">
								<p> 
									<?php echo esc_attr($settings['xevso_colleps_des']); ?>
								</p>
							</div>
						</div>
						<div class="item--order">
						<?php echo esc_attr($settings['xevso_colleps_nmer']); ?>
						</div>
					</div>
			
			<div class="ct-fancy-hover">
				<div class="bg-image" style="background-repeat: no-repeat; background-size: cover; background-image:url(<?php echo esc_url(wp_get_attachment_image_url( $settings['xevso_slider_two_img']['id'], 'full' )); ?>);">
				</div>
						
					<h3 class="item--title"><?php echo esc_attr($settings['service_back_title']); ?></h3>
						<div class="item--description"><p><?php echo esc_attr($settings['service_back_des']); ?></p>
							</div>
								<div class="item--meta">
									<div class="item--order"> <?php echo esc_attr($settings['service_back_num']); ?></div>
								<div class="item--link"> 
							<a href="#"><?php echo esc_attr($settings['service_back_btn']); ?></a>
						</div>
					</div>
				</div>
			</div>
		
	<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_animatebtn_Widget );