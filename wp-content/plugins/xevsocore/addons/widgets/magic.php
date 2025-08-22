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
class magic_about_Widget extends \Elementor\Widget_Base {

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
		return 'aoton-about';
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
		return esc_html__( 'aoton magic', 'xevsocore' );
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
		return [ 'xevsocore', 'about' ];
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
			'aoton_about_contens',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
            
		$this->add_control(
			'aoton_about_hadding',
			[
			    'label'   => esc_html__( 'Title', 'xevsocore' ),
			    'type'    => Controls_Manager::TEXTAREA,
			    'default' => esc_html__('About Us','xevsocore'),
			]
		);
		$this->add_control(
			'aoton_abvout_stitle',
			[
				'label' => esc_html__( 'Sub Hadding', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'We have more than about 20+ years experience IT solutions.', 'xevsocore' ),
				'placeholder' => esc_html__( 'Type your hadding Content here', 'xevsocore' ),
			]
		);

		$this->add_control(
			'aoton_abvout_price',
			[
				'label' => esc_html__( 'Price Here', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( ' 20', 'xevsocore' ),
				'placeholder' => esc_html__( 'Type Here', 'xevsocore' ),
			]
		);
	
		$this->add_control(
			'aoton_about_btn_text',
			[
			    'label'   => esc_html__( 'Button Text', 'xevsocore' ),
			    'type'    => Controls_Manager::TEXT,
			    'default' => esc_html__('More About','xevsocore'),
			]
		);
		
		$this->add_control(
			'aoton_about_btn_link',
			[
			    'label'   => esc_html__( 'Button Link', 'xevsocore' ),
			    'type'    => Controls_Manager::TEXT,
			    'default' => esc_html__('#','xevsocore'),
			]
		);
		
		
		$this->end_controls_section();


		$this->start_controls_section(
			'aoton_about',
			[
				'label' => esc_html__( 'Service Box', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

	     $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'aoton_flipbox_b',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .home_services_box',
            ]
        );

	    $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'aoton_flipbox_erb',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient', 'video' ],
                'selector' => '{{WRAPPER}} .home_services_box:hover',
            ]
        );
		$this->add_responsive_control(
			'aoton_aboutma',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );        
		$this->add_responsive_control(
			'aoton_aboutpa',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();








		$this->start_controls_section(
			'aoton_about_titles_css',
			[
				'label' => esc_html__( 'Title', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'aoton_about_stitle',
			    'label' => esc_html__( 'Title Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .home_services_box h4',
			]
		);
		$this->add_control(
			'aoton_about_stitle_color',
			[
			    'label' => esc_html__( 'Title Color', 'xevsocore' ),
			    'type' => Controls_Manager::COLOR,
			    'default'=>'#0a5be0',
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box h4' => 'color: {{VALUE}}',
			    ],
			]
		  );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'aoton_about_hadding',
			    'label' => esc_html__( 'Sub Hadding Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .home_services_box .price span',
			]
		);
		$this->add_control(
			'aoton_about_hadding_color',
			[
			    'label' => esc_html__( 'Hadding Color', 'xevsocore' ),
			    'type' => Controls_Manager::COLOR,
			    'default'=>'#000000',
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box .price span' => 'color: {{VALUE}}',
			    ],
			]
		);
		$this->add_responsive_control(
			'aoton_about_title_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box .price span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );        
		$this->add_responsive_control(
			'aoton_about_title_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box .price span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();

		
		$this->start_controls_section(
			'aoton_about_styles',
			[
				'label' => esc_html__( 'Price', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'aoton_about_haddin',
			    'label' => esc_html__( 'Price Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .home_services_box .price',
			]
		);
		$this->add_control(
			'aoton_about_hadding_colo',
			[
			    'label' => esc_html__( 'Price Color', 'xevsocore' ),
			    'type' => Controls_Manager::COLOR,
			    'default'=>'#000000',
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box .price' => 'color: {{VALUE}}',
			    ],
			]
		);

		$this->add_responsive_control(
			'aoton_about_paria',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box .price ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);  

		$this->add_responsive_control(
			'aoton_about_pri',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .home_services_box .price ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );    
		  
		$this->end_controls_section();
		
		// Button Start
		$this->start_controls_section(
			'aoton_about_btn_three',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'aoton_about_btn_three_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .home_services_box a',
			]
		);
		$this->add_responsive_control(
			'aoton_about_btn_thre_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .home_services_box a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'aoton_about_btn_three_padding',
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
					'{{WRAPPER}} .home_services_box .plan-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'aoton_about_btn_three_tabs'
		);
		$this->start_controls_tab(
			'aoton_about_btn_three_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'aoton_about_btn_three_ncolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .home_services_box .plan-button' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'aoton_about_btn_three_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .home_services_box .plan-button',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'aoton_about_btn_three_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .home_services_box .plan-button',
			]
		);
		$this->add_responsive_control(
			'aoton_about_btn_three_nradius',
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
					'{{WRAPPER}} .home_services_box .plan-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'aoton_about_btn_three_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'aoton_about_btn_three_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .home_services_box .plan-button:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'aoton_about_btn_three_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .home_services_box .plan-button:hover',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'aoton_about_btn_three_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .home_services_box .plan-button:hover',
			]
		);
		$this->add_responsive_control(
			'aoton_about_btn_three_hradius',
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
					'{{WRAPPER}} .home_services_box .plan-button:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		// Button End
	
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
	<div class="home_services_box">
		<div class="right_arrow"></div>
			<div class="left_arrow"></div>
			<h4><?php echo esc_html($settings['aoton_about_hadding']); ?></h4>
			<hr class="sep">
			<div class="price"><span><?php echo esc_html($settings['aoton_abvout_stitle']); ?></span> <?php echo esc_html($settings['aoton_abvout_price']); ?></div>
	 		<div><a class="plan-button" href="<?php echo esc_html($settings['aoton_about_btn_link']) ?>"><?php echo esc_html($settings['aoton_about_btn_text']) ?></a></div>
	</div>
	<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new magic_about_Widget );