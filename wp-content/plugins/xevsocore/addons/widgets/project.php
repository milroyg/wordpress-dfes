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
class xevso_project_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-project';
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
		return esc_html__( 'xevso Project', 'xevsocore' );
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
		return [ 'xevso', 'project' ];
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
			'xevso_projects_contnet',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_project_stitle',
			[
			    'label' => esc_html__( 'Small Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Latest Project','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_project_hadding',
			[
			    'label' => esc_html__( 'Hadding', 'xevsocore' ),
			    'type' => Controls_Manager::TEXTAREA,
			    'default' => esc_html__( 'There are more latest project done yet.','xevsocore' ),
			    'placeholder' => esc_html__( 'Hadding', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_project_style',
			[
				'label' => esc_html__( 'Select Style', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'  => esc_html__( 'Style One', 'xevsocore' ),
					'2'  => esc_html__( 'Style Two', 'xevsocore' ),
					'3'  => esc_html__( 'Style Three', 'xevsocore' ),
				],
			]
		);
		$this->add_control(
			'xevso_project_enable_sliders',
			[
				'label' => esc_html__( 'Enable Slide ?', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'xevso_project_style' => '1',
				]
			]
		);
		
		$this->add_control(
			'xevso_project_nav',
			[
				'label' => esc_html__( 'Enable Nav', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'xevso_project_style' => '1',
					'xevso_project_enable_sliders' => 'yes',
				]
			]
		);
		$this->add_control(
			'xevso_project_aplay',
			[
				'label' => esc_html__( 'Enable Auto Play', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'xevso_project_style' => '1',
					'xevso_project_enable_sliders' => 'yes',
				]
			]
		);
		$this->add_control(
			'xevso_project_aspeed_enable',
			[
				'label' => esc_html__( 'Enable Auto Play Speed', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'xevso_project_style' => '1',
					'xevso_project_enable_sliders' => 'yes',
				]
			]
		);
		$this->add_control(
			'xevso_project_aspeed',
			[
				'label' 	=> esc_html__( 'Slide auto Speed', 'xevsocore' ),
			    	'type' 	=> Controls_Manager::NUMBER,
			    	'min' 	=> 500,
			    	'max' 	=> 5000,
			    	'step' 	=> 50,
			    	'default' 	=> 1500,
			   	'condition' => array(
					'xevso_project_style' => '1',
					'xevso_project_aspeed_enable' => 'yes',
					'xevso_project_enable_sliders' => 'yes',

				)
			]
		);
		$this->add_control(
			'xevso_project_speed_enable',
			[
				'label' => esc_html__( 'Enable Speed', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'xevso_project_style' => '1',
					'xevso_project_enable_sliders' => 'yes',
				]
			]
		);
		$this->add_control(
			'xevso_project_speed',
			[
				'label' 	=> esc_html__( 'Slide Speed', 'xevsocore' ),
			    	'type' 	=> Controls_Manager::NUMBER,
			    	'min' 	=> 500,
			    	'max' 	=> 5000,
			    	'step' 	=> 50,
			    	'default' 	=> 1500,
			   	'condition' => array(
					'xevso_project_style' => '1',
					'xevso_project_speed_enable' => 'yes',
					'xevso_project_enable_sliders' => 'yes',

				)
			]
		);
		$this->add_responsive_control(
			'xevso_project_vicon_position',
			[
			    'label' => esc_html__( 'video Icon position', 'xevsocore' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				  'px' => [
					'min' => -200,
					'max' => 200,
					'step' => 1,
				  ],
				  '%' => [
					'min' => -100,
					'max' => 100,
				  ],
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .project-video' => 'top: {{SIZE}}{{UNIT}};'
			    ],
			]
		);
		$this->add_responsive_control(
			'xevso_project_button_position',
			[
			    'label' => esc_html__( 'button Position Bottom', 'xevsocore' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				  'px' => [
					'min' => -100,
					'max' => 500,
					'step' => 1,
				  ],
				  '%' => [
					'min' => -100,
					'max' => 100,
				  ],
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .project-box .project-title' => 'bottom: {{SIZE}}{{UNIT}};'
			    ],
			]
		);
		$this->add_responsive_control(
			'xevso_project_button_position_r',
			[
			    'label' => esc_html__( 'button Position Right', 'xevsocore' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				  'px' => [
					'min' => -100,
					'max' => 500,
					'step' => 1,
				  ],
				  '%' => [
					'min' => -100,
					'max' => 100,
				  ],
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .project-box .project-title' => 'right: {{SIZE}}{{UNIT}};'
			    ],
			]
		);
		$this->add_control(
			'xevso_project_per_post',
			[
			    'label' => esc_html__( 'Show Items', 'xevsocore' ),
			   'type' 	=> Controls_Manager::NUMBER,
			    	'min' 	=> -1,
			    	'max' 	=> 15,
			    	'step' 	=> 1,
			    	'default' 	=> -1,
			]
		);
		$this->add_control(
			'xevso_project_pagi',
			[
				'label' => esc_html__( 'Enable Pagination', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_project_hadding_style',
			[
				'label' => esc_html__( 'Hadding', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
            'xevso_project_hsaline',
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
				'justify' => [
					'title' => esc_html__( 'Justified', 'xevsocore' ),
					'icon' => 'fa fa-align-justify',
				],
			],
			'selectors' => [
				'{{WRAPPER}} .project-hadding' => 'text-align: {{VALUE}};',
			],
			'default' => 'left',
			'separator' =>'before',
			]
		);
		$this->add_control(
			'xevso_project_hscolor',
			[
				'label' => esc_html__( 'Small Text Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0b5be0',
				'selectors' => [
					'{{WRAPPER}} .project-hadding span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_project_stypo',
			    'label' => esc_html__( 'Small Text Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .project-hadding span',
			]
		);
		$this->add_control(
			'xevso_project_hcolor',
			[
				'label' => esc_html__( 'Hadding Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .project-hadding h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_project_h_padding_right',
			[
			    'label' => esc_html__( 'Hadding margin Right', 'xevsocore' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				  'px' => [
					'min' => 0,
					'max' => 500,
					'step' => 1,
				  ],
				  '%' => [
					'min' => 10,
					'max' => 100,
				  ],
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .project-hadding h2' => 'margin-right: {{SIZE}}{{UNIT}};'
			    ],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_project_typo',
			    'label' => esc_html__( ' Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .project-hadding h2',
			]
		);
		$this->add_responsive_control(
			'xevso_project_hpadding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .project-haddings' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_project_tab_menu_style',
			[
				'label' => esc_html__( 'Category Menu', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_project_mtypo',
			    'label' => esc_html__( ' Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .project-menu ul li',
			]
		);
		$this->add_control(
			'xevso_project_mcolor',
			[
				'label' => esc_html__( 'Menu color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .project-menu ul li' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'xevso_project_macolor',
			[
				'label' => esc_html__( 'Active color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .project-menu ul li.active' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_project_mbcolor',
				'label' => esc_html__( 'active Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .project-menu ul li.active',
			]
		);
		$this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'xevso_project_note',
			[
				'label' => __( '<strong>Hover Section</strong>', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
			]
		);
		$this->add_control(
			'xevso_project_mhscolor',
			[
				'label' => esc_html__( 'Hover color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .project-menu ul li:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_project_mbhcolor',
				'label' => esc_html__( 'Hover Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .project-menu ul li:hover',
			]
		);
		$this->add_responsive_control(
			'xevso_project_mmargin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
				  'top' => '5',
				  'right' => '5',
				  'bottom' => '5',
				  'left' => '5',
				  'isLinked' => false
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .project-menu ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->add_responsive_control(
			'xevso_project_mpadding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
				  'top' => '11',
				  'right' => '25',
				  'bottom' => '11',
				  'left' => '25',
				  'isLinked' => false
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .project-menu ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_project_item_style',
			[
				'label' => esc_html__( 'Item', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'xevso_project_item_icon_color',
			[
				'label' => esc_html__( 'Icon color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .project-video a i:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'xevso_project_item_icon_hcolor',
			[
				'label' => esc_html__( 'Icon Hover color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0b5be0',
				'selectors' => [
					'{{WRAPPER}} .project-video a:hover i:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'hr3',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'xevso_project_note3',
			[
				'label' => __( '<strong>image Hover background color</strong>', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_project_im_hbg',
				'label' => esc_html__( 'Background Color', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'default' => 'rgba(0, 0, 0, 0.52)',
				'selector' => '{{WRAPPER}} .project-signle:after',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_project_box_style',
			[
				'label' => esc_html__( 'Box', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_project_box_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '95px',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'isLinked' => false
				],
			    'selectors' => [
				  '{{WRAPPER}} .project-boxs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->add_responsive_control(
			'xevso_project_box_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'isLinked' => false
				],
			    	'selectors' => [
					'{{WRAPPER}} .project-boxs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    	],
			    'separator' =>'before',
			]
		);
		$this->end_controls_section();
		// Button Start
		$this->start_controls_section(
			'xevso_projects_btns_three',
			[
				'label' => esc_html__( 'Button Three', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_projects_btns_three_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn',
			]
		);
		$this->add_responsive_control(
			'xevso_projects_btns_three_margin',
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
			'xevso_projects_btns_three_padding',
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
			'xevso_projects_btns_three_tabs'
		);
		$this->start_controls_tab(
			'xevso_projects_btns_three_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_projects_btns_three_ncolor',
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
				'name' => 'xevso_projects_btns_three_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_projects_btns_three_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_projects_btns_three_nradius',
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
			'xevso_projects_btns_three_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_projects_btns_three_hcolor',
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
				'name' => 'xevso_projects_btns_three_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__blob',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_projects_btns_three_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn:hover .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_projects_btns_three_hradius',
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
		$dynamic_id = rand(1241, 3256);
		if($settings['xevso_project_enable_sliders'] == 'yes' ){
			if($settings['xevso_project_aplay'] == 'yes' ){
				$aplay = 'true';
			}else{
				$aplay = 'false';
			}
			if($settings['xevso_project_nav'] == 'yes' ){
				$nav = 'true';
			}else{
				$nav = 'false';
			}
			echo '
			<script>
			jQuery(document).ready(function($) {
				"use strict";
				$("#project-'.esc_attr($dynamic_id).'").slick({
					autoplay:'.esc_attr($aplay).',
					arrows:'.esc_attr($nav).',
					slidesToShow:3,
					slidesToScroll:1,';
					if(!empty($settings['xevso_project_aspeed'])){
						echo 'autoplaySpeed:'.esc_attr($settings['xevso_project_aspeed']).',';
					}
					if(!empty($settings['xevso_project_speed'])){
						echo 'speed:'.esc_attr($settings['xevso_project_speed']).',';
					}
					echo '
					responsive: [
						{
						breakpoint: 1024,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 2,
								
							}
						},
						{
							breakpoint: 992,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 2
							}
						},
						{
							breakpoint: 600,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						},
						{
							breakpoint: 480,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
					]
				});
			});
			</script>';
		}
		if($settings['xevso_project_style'] == '2' ){
			$dynamic_num = rand(35245545, 541541745);
		?>
			<script>
				jQuery(window).ready(function($) {
				'use strict';
				jQuery('.rafo-project-shorting li').on('click',function(){
					jQuery('.rafo-project-shorting li').removeClass('active');
					jQuery(this).addClass('active');
					var selector = jQuery(this).attr('data-filter');
					jQuery('.projectlist-<?php echo esc_attr($dynamic_num) ?>').isotope({
						filter:selector,
					});
				});
				});
				jQuery(window).on('load',function($) {
					'use strict';
					jQuery(".projectlist-<?php echo esc_attr($dynamic_num) ?>").isotope();
				});
			</script>
			<?php
		}
		if($settings['xevso_project_style'] == '1'){
			$xevso_pro_type = 'project-style-one';
		}elseif($settings['xevso_project_style'] == '2'){
			$xevso_pro_type = 'project-style-two';
		}else{
			$xevso_pro_type = 'project-style-three';
		}
		if($settings['xevso_project_style'] == '3'){
			$col_num = '12';
		}elseif($settings['xevso_project_style'] == '2'){
			$col_num = '8';
		}elseif($settings['xevso_project_enable_sliders'] != 'yes'){
			$col_num = '12';
		}else{
			$col_num = '8';
		}
		?>
		<div class="project-boxs">
			<div class="project-box <?php echo esc_attr($xevso_pro_type); ?>">
				<div class="project-haddings <?php if($settings['xevso_project_style'] == '1') : ?>container<?php endif; ?>">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 col-lg-<?php echo esc_attr($col_num) ?>">
							<div class="project-hadding">
								<span><?php echo esc_html($settings['xevso_project_stitle']); ?></span>
								<h2><?php echo wp_kses_post($settings['xevso_project_hadding']); ?></h2>
							</div>
						</div>
						<?php if($settings['xevso_project_style'] == '2' ) :
						$xevso_prjects = get_terms( 'project_cat' ); if( !empty($xevso_prjects) && ! is_wp_error( $xevso_prjects ) ) :
						?>
						<div class="d-flex flex-wrap align-content-center col-12 col-sm-12 col-md-12 <?php if(empty($settings['xevso_project_stitle'] || $settings['xevso_project_hadding']) ) : ?> col-lg-12 justify-content-xl-center<?php else : ?>col-lg-8 justify-content-xl-end <?php endif; ?>">
							<div class="project-menu">
								<ul class="rafo-project-shorting">
									<li class="active" data-filter="*"><?php esc_html_e('all','xevsocore'); ?></li>
									<?php foreach($xevso_prjects as $xevso_prject) : ?>
									<li data-filter=".<?php echo esc_attr($xevso_prject->slug) ?>"><?php echo esc_html($xevso_prject->name) ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
						<?php endif; endif; ?>
					</div>
				</div>
				<div class="project-contents arrow-nav">
					<div class="project-items row projectlist-<?php if(!empty($settings['xevso_project_style'] == '2' )){echo esc_attr($dynamic_num);} ?>"  id="project-<?php echo esc_attr($dynamic_id); ?>">
						<?php  global $post;
						$paged = get_query_var('paged') ? get_query_var('paged') : 1;
						$p = new \WP_Query(array(
						'posts_per_page' => esc_attr($settings['xevso_project_per_post']),
						'post_type' => 'project',
						'paged' => $paged
						));
						while($p->have_posts()) : $p->the_post();
							$xevso_idd = get_the_ID();
							$xevso_pro_meta = get_post_meta($xevso_idd, 'xevso_project_meta', true);
					    		$project_catagory = get_the_terms( get_the_ID(), 'project_cat' );
						if ( $project_catagory && ! is_wp_error( $project_catagory ) ) {
					   	 	$project_cat_list = array();
					    	foreach ( $project_catagory as $project_cat ) {
					        	$project_cat_list[] = $project_cat->slug;
					   	}
							$project_catshow = join( ", ", $project_cat_list );
						}else{
							$project_catshow = '';
					  	}
						?>
						<div class="item <?php if( ! empty($settings['xevso_project_style'] !== "1" ) || !empty( $settings['xevso_project_enable_sliders'] !== "yes" ) ) : ?>service-3 col-12 col-sm-<?php if( $settings['xevso_project_style'] == '1' && $settings['xevso_project_enable_sliders'] != 'yes' ) : ?>12<?php else : ?>6<?php endif; ?>  col-md-6 col-lg-4 col-xl-4 <?php echo esc_attr($project_catshow) ?><?php endif; ?>">
							<div class="project-signle">
							    <div class="project_sig_thre">
							    
								<?php
								if($settings['xevso_project_style'] == '2' ){
									the_post_thumbnail('xevso-project-image2');
								}else{
									the_post_thumbnail('xevso-project-image');
								}
								?>
								<?php if(!empty( $settings['xevso_project_style'] == '3' )) : ?>
								</div>
								<div class="project-content">
									<div class="pro-left">
									    
									    	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
									    
										<?php if ( $project_catagory && ! is_wp_error( $project_catagory ) ) : ?>
										<ul>
											<li><?php echo esc_html($project_catshow); ?></li>
										</ul>
										<?php endif; ?>
									
									</div>
									<div class="pro-right">
										<a href="<?php the_permalink(); ?>" class="theme-button">
											<i class="flaticon-right-arrow"></i>
										</a>
									</div>
								</div>
								<?php else : ?>
									<?php if(!empty($xevso_pro_meta['xevso_pro_video_link'])) : ?>
									<div class="project-video">
										<a href="#" data-video-url="<?php echo esc_url($xevso_pro_meta['xevso_pro_video_link']); ?>" class="popUp" style="cursor: pointer;">
										<i aria-hidden="true" class="flaticon-play-button-1"></i></a>
									</div>
									<?php endif; ?>
								<div class="project-title">
									<div class="theme-projects_btns">
										<a href="<?php the_permalink(); ?>" class="blob-btn"><?php the_title(); ?><i class="flaticon-right-arrow"></i>
											<span class="blob-btn__inner">
												<span class="blob-btn">
												</span>
											</span>
										</a>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
						<?php endwhile; wp_reset_postdata(); wp_reset_query();?>
					</div>
					<?php if($settings['xevso_project_pagi'] == 'yes') : ?>
					<div class="cpaginations">
						<?php xevso_paginate_nav( $p ); ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_project_Widget );
