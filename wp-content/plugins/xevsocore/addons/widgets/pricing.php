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
class xevso_pricing_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-pricing';
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
		return esc_html__( 'xevso Pricing Table', 'xevsocore' );
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
		return [ 'xevso', 'Pricing' ];
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
			'xevso_pricing_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'xevso_popular',
			[
			    'label' => esc_html__( 'Popular', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Popular Title','xevsocore'),
			]
		);
		
		$this->add_control(
			'xevso_pr_title',
			[
			    'label' => esc_html__( 'Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Pricing Title','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_pr_subtitle',
			[
			    'label' => esc_html__( 'Sub Title ', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('label','xevsocore'),
			]
		);
		
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'xevso_coninfo_list_contnet',
			[
			    'label' => esc_html__( 'Content', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Dambo Dika US. Road 123','xevsocore'),
			]
		);
		$repeater->add_control(
			'xevso_coninfo_icon',
			[
			'label' => esc_html__( 'Icon', 'xevsocore' ),
			'type' => Controls_Manager::ICONS,
			'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_coninfo_slides',
				[
				'label' => esc_html__( 'Repeater List', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'xevso_coninfo_list_contnet' => esc_html__( 'Dambo Dika US. Road 123', 'xevsocore' ),
						'xevso_coninfo_icon' => '',
					],
				],
			]
		); 
	$this->end_controls_section();	
		
	$this->start_controls_section(
			'xevso_pricing_btm',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);	
		
		$this->add_control(
			'xevso_price',
			[
			    'label' => esc_html__( 'Amount with Currency ', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => '$99.99',
			]
		);
		
		$this->add_control(
			'xevso_month',
			[
			    'label' => esc_html__( 'Month/Year', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Per year','xevsocore'),
			]
		);

		$this->add_control(
			'xevso_btn_link',
			[
			    'label' => esc_html__( 'button Link', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			]
		);
		$this->add_control(
			'xevso_btn',
			[
			    'label' => esc_html__( 'Button Text', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'	=> esc_html('Select plan','xevsocore'),
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_pricing_border_styles',
			[
			    'label' => esc_html__( 'Pricing box', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_pricing_border',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .pricing-box',
			]
		);
		
		$this->add_control(
			'more_optis7f',
			[
				'label' => esc_html__( 'Popular Options', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_responsive_control(
			'xevso_pricipospa_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .pricing .item .price-title span' => 'color: {{VALUE}}',
				],
			]
		);

	    $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_tepopler',
				'selector' => '{{WRAPPER}} .pricing .item .price-title span',
			]
		);

		$this->add_responsive_control(
			'xevso_pricing1_btpopspan_hradius',
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
					'{{WRAPPER}} .pricing .item .price-title span' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing1_polspan_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pricing .item .price-title span',
			]
		);
		
		$this->add_responsive_control(
			'xevso_pricing_span',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .pricing .item .price-title span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		
		$this->add_control(
			'more_optis4',
			[
				'label' => esc_html__( 'Title Options', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_responsive_control(
			'xevso_pricing_box_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .pricing-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_box_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .pricing-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_pricing_header_styles',
			[
			    'label' => esc_html__( 'Pricing Header', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->start_controls_tabs('xevso_pricing_header_tabs');

        	$this->start_controls_tab( 'xevso_pricing_header_title_tab',
			[
				'label' => esc_html__( 'Title', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_pricing_title_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#000000',
				'selectors' => [
					'{{WRAPPER}} .price-title h5' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_title_typo',
				'selector' => '{{WRAPPER}} .price-title h5',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'xevso_pricing_header_year_tab',
			[
				'label' => esc_html__( 'Sub Title', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_pricing_year_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#0a5be0',
				'selectors' => [
					'{{WRAPPER}} .price-title h4' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_year_typo',
				'selector' => '{{WRAPPER}} .price-title h4',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'line2',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_header_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .price-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing_header_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .price-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_pricing_body_styles',
			[
			    'label' => esc_html__( 'Pricing Body', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
			
		$this->add_control(
			'xevso_pricing_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#798795',
				'selectors' => [
					'{{WRAPPER}} .pricing .item ul li i' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_body_typo_icom',
				'selector' => '{{WRAPPER}} .pricing .item ul li i',
			]
		);
		$this->add_control(
			'xevso_pricing_body_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#798795',
				'selectors' => [
					'{{WRAPPER}} .pricing .item ul li' => 'color: {{VALUE}};',
				],
			]
		); 
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_body_typo',
				'selector' => '{{WRAPPER}} .pricing .item ul li',
			]
		);
	
		$this->end_controls_section();
		// Button Start
		$this->start_controls_section(
			'xevso_pricing1_btn_three',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'more_options',
			[
				'label' => esc_html__( 'Content Options', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);
		
		$this->add_control(
			'xevso_pricing_btrle_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#000000',
				'selectors' => [
					'{{WRAPPER}} .pricing .item .purchase h3' => 'color: {{VALUE}};',
				],
			]
		); 
		
		$this->add_control(
			'xevso_pricing_bthovere_color',
			[
				'label' => esc_html__( 'Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#000000',
				'selectors' => [
					'{{WRAPPER}} .pricing .item:hover .purchase h3' => 'color: {{VALUE}};',
				],
			]
		); 
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_tibtn_typo',
				'selector' => '{{WRAPPER}} .pricing .item .purchase h3',
			]
		);
		$this->add_responsive_control(
			'xevso_prici1_btn_hrews_padng',
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
					'{{WRAPPER}} .pricing .item .purchase h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
	
			
		$this->add_control(
			'xevso_pricing_span_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#000000',
				'selectors' => [
					'{{WRAPPER}} .pricing .item .purchase h3 span' => 'color: {{VALUE}};',
				],
			]
		); 
		
		$this->add_control(
			'xevso_pricing_btspan_color',
			[
				'label' => esc_html__( 'Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#000000',
				'selectors' => [
					'{{WRAPPER}} .pricing .item:hover .purchase h3 span' => 'color: {{VALUE}};',
				],
			]
		); 
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing_spann_typo',
				'selector' => '{{WRAPPER}} .pricing .item .purchase h3 span',
			]
		);
		
		
    	$this->add_control(
			'more_optis2',
			[
				'label' => esc_html__( 'Background Options', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing1_bt_aller_norram',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pricing .item .purchase',
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing1_bt_aller',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pricing .item:hover .purchase',
			]
		);
		
		
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_pricing1_btn_three_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .purchase a',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing1_btn_three_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .purchase a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_pricing1_btn_three_padding',
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
					'{{WRAPPER}} .purchase a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'xevso_pricing1_btn_three_tabs'
		);
		$this->start_controls_tab(
			'xevso_pricing1_btn_three_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_pricing1_btn_three_ncolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .purchase a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing1_btn_three_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .purchase a',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_pricing1_btn_three_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .purchase a',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing1_btn_three_nradius',
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
					'{{WRAPPER}} .purchase a' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_pricing1_btn_three_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_pricing1_btn_three_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .purchase a:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_pricing1_btn_three_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .purchase a:hover',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_pricing1_btn_three_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .purchase a:hover',
			]
		);
		$this->add_responsive_control(
			'xevso_pricing1_btn_three_hradius',
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
					'{{WRAPPER}} .purchase a:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
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
		$settings = $this->get_settings_for_display();
		
		?> 	<div class="pricing">
			<div class="item text-center wow fadeInLeft" data-wow-delay="0.2s" data-wow-duration="1s">
				<div class="price-title">
				    <span><?php echo esc_html( $settings['xevso_popular'] ); ?></span>
					<h5><?php echo esc_html( $settings['xevso_pr_title'] ); ?></h5>
					<h4><?php echo esc_html( $settings['xevso_pr_subtitle'] ); ?></h4>
				</div>
				<ul>
				 <?php foreach ($settings['xevso_coninfo_slides'] as $xevso_coninfo_slide) : ?>
					<li><i class="<?php echo esc_attr($xevso_coninfo_slide['xevso_coninfo_icon']['value']); ?>"></i><?php echo esc_html($xevso_coninfo_slide['xevso_coninfo_list_contnet']); ?></li>
				 <?php endforeach; ?>
				</ul>
				<div class="purchase">
					<h3><?php echo esc_html( $settings['xevso_price'] ); ?> <span><?php echo esc_html( $settings['xevso_month'] ); ?></span></h3>
					<a href="<?php echo esc_html( $settings['xevso_btn_link'] ); ?>"><?php echo esc_html( $settings['xevso_btn'] ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_pricing_Widget );