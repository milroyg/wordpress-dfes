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
class xevso_Ser_three_Widget extends \Elementor\Widget_Base {

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
		return 'Service-Three';
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
		return esc_html__( 'Service Three', 'xevsocore' );
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
				'label' => esc_html__( 'Service Three', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'xevso_ser_two_img',
            [
                'label' => esc_html__('Image','xevsocore'),
                'type'=>Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
		 $this->add_control(
            'serv_iconbox_icon',
            [
                'label' => esc_html__( 'Icon', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::ICONS,
            ]
         );
		
		$this->add_control(
			'xevso_ser_title',
			[
			    'label' => esc_html__( 'Service Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_ser_des',
			[
			    'label' => esc_html__( 'Service Description', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Lock Our Protfolo','xevsocore')
			]
		);
		
		$this->add_control(
			'service_back_btn',
			[
			    'label' => esc_html__( 'Service Button', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Read More','xevsocore')
			]
		);
			
		$this->add_control(
			'service_btn_link',
			[
			    'label' => esc_html__( 'Service Button Link ', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('#','xevsocore')
			]
		) ;
		
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
				'selector' => '{{WRAPPER}} .service-item .service-content-mid',
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
					'{{WRAPPER}} .service-item .service-content-mid' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_allre_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service-item .service-content-mid',
			]
		);
		
		
		$this->add_responsive_control(
            'ser_font_title_color',
            [
                'label' => esc_html__( 'Title Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service-box-content h2' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .service-box-content h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_foont_con_titl',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service-box-content h2',
            ]
        );
		
		 $this->add_responsive_control(
            'ser_font_desc_color',
            [
                'label' => esc_html__( 'Description Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service-box-content p' => 'color: {{VALUE}}',
                ],
				   'separator'  => 'before',
            
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
					'{{WRAPPER}} .service-box-content p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_foont_con_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service-box-content p',
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
                    '{{WRAPPER}} .service-hover-img i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		
		  $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'Service_icon_back_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .service-hover-img i',
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
					'{{WRAPPER}} .service-hover-img i' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_iconbox_css_dec_typo',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service-hover-img i',
            ]
        );
		
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service-hover-img i' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .service-hover-img i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_iconbox_css_icon_padding',
            [
                'label' => esc_html__( 'Pading', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .service-hover-img i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
		
		$this->start_controls_section(
			'xevso_sliders_refhase_btns',
			[
				'label' => esc_html__( 'Service Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
			]
		);
		
		$this->add_responsive_control(
            'ser_back_btton_color',
            [
                'label' => esc_html__( 'Button Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service-content-mid a' => 'color: {{VALUE}}',
                ],
            ]
        );
		$this->add_responsive_control(
            'ser_back_btton_hov_color',
            [
                'label' => esc_html__( 'Button Hover Color', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service-content-mid a:hover' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .service-content-mid a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'service_btton_con_desc',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .service-content-mid a',
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
        
			<div class="service-item">
                <div class="service-box-content">
                    <div class="service-back" style="background-image: url(<?php echo esc_url(wp_get_attachment_image_url( $settings['xevso_ser_two_img']['id'], 'full' )); ?>);">
                        <div class="service-content-mid">
                            <div class="service-content">
                                <div class="service-hover-img">
                                     <span> <i class="<?php echo esc_attr($settings['serv_iconbox_icon']['value']); ?>"></i> </span>
                                </div>
                            </div>
							<div class="service-info">
								<h2><?php echo esc_attr($settings['xevso_ser_title']); ?></h2>
								<p><?php echo esc_attr($settings['xevso_ser_des']); ?></p>
								<a  href="<?php echo esc_attr($settings['service_btn_link']); ?>"><?php echo esc_attr($settings['service_back_btn']); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_Ser_three_Widget );