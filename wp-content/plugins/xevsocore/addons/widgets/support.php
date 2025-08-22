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
class xevso_support_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-support';
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
		return esc_html__( 'xevso Support', 'xevsocore' );
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
		return [ 'xevso', 'support' ];
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
			'xevso_support_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_support_title',
			[
			    'label' => esc_html__( 'Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Still need help for any problem?','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_support_dec',
			[
			    'label' => esc_html__( 'Hadding', 'xevsocore' ),
			    'type' => Controls_Manager::WYSIWYG,
			    'default' => esc_html__( 'Check out our In-App Help, Blog, and Youtube Channel. You can also email our support team at support@avso.com.','xevsocore' )
			]
		);
		$this->add_control(
			'xevso_support_btn_enable',
			[
				'label' => esc_html__( 'Enable support_btns', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'xevso_support_support_btns_links',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
				'condition' => [
					'xevso_support_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_support_support_btns_link_extralnal',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'xevso_support_support_btns_links' => 'extranal',
					'xevso_support_btn_enable' => 'yes',
				],
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_support_support_btns_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => [
					'xevso_support_support_btns_links' => 'page',
					'xevso_support_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_support_support_btns_link_test',
			[
			    'label' => esc_html__( 'Button Link Text', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Get free more','xevsocore'),
			    'condition' => [
					'xevso_support_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_support_button_new_tab',
			[
			    'label'         => esc_html__( 'Open New Tab ? ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'Yes', 'xevsocore' ),
			    'label_off'     => esc_html__( 'No', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			    'condition' => [
					'xevso_support_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_support_gb_icon',
			[
				'label' => esc_html__( 'Background Icon', 'xevsocore' ),
				'type' => Controls_Manager::ICON,
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_support_styles',
			[
				'label' => esc_html__( 'box', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_support_box_tabs');

        	$this->start_controls_tab( 'xevso_support_box_tab',
			[
				'label' => esc_html__( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_support_box_bg',
				'label' => esc_html__( 'Background color', 'xevsocore' ),
				'types' => [ 'classic' ],
				'default' => '#0b5be0',
				'selector' => '{{WRAPPER}} .support-boxs',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_support_box_border',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .support-boxs',
			]
		);
		$this->add_responsive_control(
			'xevso_support_box_border_radius',
			[
			    'label' => esc_html__( 'Border Radius', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-boxs' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_support_box_alignment',
			[
				'label' => esc_html__( 'Alignment', 'xevsocore' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'xevsocore' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'xevsocore' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'xevsocore' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .support-boxs' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
			'xevso_support_box_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '50',
				  'right' => '50',
				  'bottom' => '50',
				  'left' => '50',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-boxs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_support_box_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-boxs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_tab();

		$this->start_controls_tab( 'xevso_support_box_htab',
			[
				'label' => esc_html__( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_support_box_hbg',
				'label' => esc_html__( 'Background color', 'xevsocore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .support-boxs:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_support_box_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .support-boxs:hover',
			]
		);
		$this->add_responsive_control(
			'xevso_support_box_border_hradius',
			[
			    'label' => esc_html__( 'Border Radius', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-boxs:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_support_icon_style',
			[
				'label' => esc_html__( 'Icon', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_support_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'{{WRAPPER}} .support-bg-icon i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_support_icon_roted',
			[
				'label' => esc_html__( 'Icon rotate', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'range' => [
					'deg' => [
						'min' => -190,
						'max' => 190,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'deg',
					'size' => 45,
				],
				'selectors' => [
					'{{WRAPPER}} .support-bg-icon' => 'transform:rotate({{SIZE}}{{UNIT}}) ;',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_support_icon_top',
			[
				'label' => esc_html__( 'Icon Position', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => -190,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => -190,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .support-bg-icon' => 'top:{{SIZE}}{{UNIT}} ;',
				],
			]
		);
		$this->add_control(
			'xevso_support_icon_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .support-bg-icon i:before' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_responsive_control(
			'xevso_support_icon_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-bg-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_support_icon_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-bg-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_support_title_styles',
			[
				'label' => esc_html__( 'Title', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_support_title_tabs');

        	$this->start_controls_tab( 'xevso_support_title_tab',
			[
				'label' => esc_html__( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_support_title_color',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' =>'#ffffff',
				'selectors' => [
					'{{WRAPPER}} .support-boxs h2' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_support_title_typo',
			    'selector' => '{{WRAPPER}} .support-boxs h2',
			]
		);  
		$this->end_controls_tab();

		$this->start_controls_tab( 'xevso_support_title_htab',
			[
				'label' => esc_html__( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_support_title_hcolor',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .support-boxs:hover h2' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_support_dec_styles',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_support_dec_tabs');

        	$this->start_controls_tab( 'xevso_support_dec_tab',
			[
				'label' => esc_html__( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_support_dec_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' =>'#ffffff',
				'selectors' => [
					'{{WRAPPER}} .support-dec' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_support_dec_typo',
			    'selector' => '{{WRAPPER}} .support-dec',
			]
		);  
		$this->add_responsive_control(
			'xevso_support_dec_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '10',
				  'right' => '0',
				  'bottom' => '10',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-dec' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_support_dec_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .support-dec' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_tab();

		$this->start_controls_tab( 'xevso_support_dec_htab',
			[
				'label' => esc_html__( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_support_dec_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' =>'#ffffff',
				'selectors' => [
					'{{WRAPPER}} .support-boxs:hover .support-dec' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();
		// Button Start
		$this->start_controls_section(
			'xevso_support_btns',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_support_btns_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn',
			]
		);
		$this->add_responsive_control(
			'xevso_support_btns_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .blob-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_support_btns_padding',
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
					'{{WRAPPER}} .blob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'xevso_support_btns_tabs'
		);
		$this->start_controls_tab(
			'xevso_support_btns_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_support_btns_ncolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .blob-btn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_support_btns_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_support_btns_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_support_btns_nradius',
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
					'{{WRAPPER}} .blob-btn__inner' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_support_btns_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_support_btns_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .blob-btn:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_support_btns_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__blob',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_support_btns_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn:hover .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_support_btns_hradius',
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
					'{{WRAPPER}} .blob-btn:hover .blob-btn__inner' => 'border-radius: {{SIZE}}{{UNIT}};',
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
		if( $settings['xevso_support_support_btns_page_link'] == 'page' ){
			$xevso_title_btn_page = get_page_link( $settings['xevso_support_support_btns_page_link'] );
		}else{
			$xevso_title_btn_page =  $settings['xevso_support_support_btns_link_extralnal'];
		}
		?>
		<div class="support-boxs">
			<div class="support-box">
				<?php if(!empty($settings['xevso_support_gb_icon'])){
					echo '<div class="support-bg-icon"><i class="'.esc_attr($settings['xevso_support_gb_icon']).'"></i></div>';
				}
				?>
				<?php if(!empty($settings['xevso_support_title'])){
					echo '<h2>'.esc_html($settings['xevso_support_title']).'</h2>';
				}?>
				<div class="support-dec">
				<?php echo wp_kses_post(wpautop($settings['xevso_support_dec'])); ?>
				</div>
				<?php if(!empty($settings['xevso_support_btn_enable'])) : ?>
				<div class="support-btn">
					<div class="theme-support_btns">
						<a <?php if(!empty($settings['xevso_support_button_new_tab'])) : ?>target="_blank"<?php endif; ?> href="<?php echo esc_url($xevso_title_btn_page); ?>" class="blob-btn"><?php echo esc_html($settings['xevso_support_support_btns_link_test']); ?><i class="flaticon-right-arrow"></i>
							<span class="blob-btn__inner">
								<span class="blob-btn__blobs">
									<span class="blob-btn__blob"></span>
									<span class="blob-btn__blob"></span>
									<span class="blob-btn__blob"></span>
									<span class="blob-btn__blob"></span>
								</span>
							</span>
						</a>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_support_Widget );