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
class xevso_service_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-service';
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
		return esc_html__( 'xevso Service', 'xevsocore' );
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
		return [ 'xevso', 'service' ];
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
			'xevso_service_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_service_select',
			[
			    'label' => esc_html__('Choose Style','xevsocore'),
			    'type' =>Controls_Manager::SELECT,
			    'options' => [
					'1'  	=> esc_html__( 'Style One', 'xevsocore' ),
					'2' 	=> esc_html__( 'Style Two', 'xevsocore' ),
					'3' 	=> esc_html__( 'Style Three', 'xevsocore' ),
			    	],
			    'default' => '1',
			]
		  );
		  $this->add_control(
			'service_icons_type',
			[
			    'label' => esc_html__('Service Icon Type','xevsocore'),
			    'type' =>Controls_Manager::CHOOSE,
			    'options' =>[
				  'img' =>[
					'title' =>esc_html__('Image','xevsocore'),
					'icon' =>'fa fa-picture-o',
				  ],
				  'icon' =>[
					'title' =>esc_html__('Icon','xevsocore'),
					'icon' =>'fa fa-info',
				  ]
			    ],
			    'default' => 'icon',
			    'condition' => [
					'xevso_service_select'=> array('1','2'),
				]
			]
		  );
	
		  $this->add_control(
			'xevso_sicon_img',
			[
			    'label' => esc_html__('Image','xevsocore'),
			    'type'=>Controls_Manager::MEDIA,
			    'default' => [
				  'url' => Utils::get_placeholder_image_src(),
			    ],
			    'condition' => [
				  'service_icons_type' => 'img',
			    ]
			]
		  );
	
		  $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
			    'name' => 'xevso_sicon_img_resize',
			    'default' => 'large',
			    'separator' => 'none',
			    'condition' => [
				   'xevso_service_select'=> array('1','2'),
				  'service_icons_type' => 'img',
			    ]
			]
		  );
		$this->add_control(
			'xevso_service_icon',
			[
				'label' => esc_html__( 'Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'condition'=>[
					'xevso_service_select'=> array('1','2'),
					'service_icons_type'=> 'icon',
				],
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_service_title',
			[
			    'label'		=> esc_html__( 'Title', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => esc_html__('It Managment','xevsocore'),
			    'placeholder' => esc_html__( 'Service Title', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_service_titleicon',
			[
				'label' => esc_html__( 'Title Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'condition'=>[
					'xevso_service_select'=> array('2','3'),
				],
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_service_links',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'no' => esc_html__( 'Select Options', 'xevsocore' ),
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
			]
		);
		$this->add_control(
			'xevso_service_extranal',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'xevso_service_links' => 'extranal',
				],
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_service_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => [
					'xevso_service_links' => 'page',
				],
			]
		);
		$this->add_control(
			'xevso_service_dec',
			[
			    'label' => esc_html__( 'Description', 'xevsocore' ),
			    'type' => Controls_Manager::WYSIWYG,
			    'default' => esc_html__( 'Lorem ipsum dolor sit amet, consecte adipiscing elit Morbi vitae.','xevsocore' ),
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_service_box_styles',
			[
			    'label' => esc_html__( 'Box Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_service_box_style_tabs');

        	$this->start_controls_tab( 'xevso_service_nbox_tab',
			[
				'label' => esc_html__( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_service_box_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .service-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_service_box_padding',
			[
				'label' => esc_html__( 'Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .service-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_service_box_bg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .service-box',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_service_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service-box',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_service_box_border',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service-box',
			]
		);
		$this->add_responsive_control(
			'xevso_service_box_radius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .service-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'xevso_service_hbox_tab',
			[
				'label' => esc_html__( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_service_box_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .service-box:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_service_box_hshadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service-box:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_service_box_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service-box:hover',
			]
		);
		$this->add_responsive_control(
			'xevso_service_box_hradius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .service-box:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'xevso_service_box_animations',
			[
				'label' => esc_html__( 'Hover Animation', 'xevsocore' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'prefix_class' => 'elementor-animation-',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_service_cbox_styles',
			[
			    'label' => esc_html__( 'Content Box', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_service_cbox_margin',
			[
				'label' => esc_html__( 'Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .service-contents' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_service_cbox_padding',
			[
				'label' => esc_html__( 'Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .service-contents' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_service_cbox_bg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .service-contents',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_service_cbox_border',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service-contents',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_service_cbox_hshadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .service-contents',
			]
		);


		$this->add_control(
			'xevso_service_content3_color',
			[
				'label' => esc_html__( 'Content Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .style-three .service-dec p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'xevso_service_select' => '3',
				],
			]
		);
		$this->add_control(
			'xevso_service_content3h_color',
			[
				'label' => esc_html__( 'Content Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .style-three:hover .service-dec p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'xevso_service_select' => '3',
				],
			]
		);
		
		$this->add_responsive_control(
			'xevso_service_cervice-dec',
			[
				'label' => esc_html__( 'Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .service-dec' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_service_icons_styles',
			[
			    'label' => esc_html__( 'Icons Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_service_icons_style_tabs');

        	$this->start_controls_tab( 'xevso_service_ficon_tab',
			[
				'label' => esc_html__( 'Font icon Normal', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_sicon_color',
			[
				'label' => esc_html__( 'Icon Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0b5be0',
				'selectors' => [
					'{{WRAPPER}} .service-icon i:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_service_ficon_size',
			[
				'label' => esc_html__( 'Font Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 2,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 84,
				],
				'selectors' => [
					'{{WRAPPER}} .service-icon i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_sficon_border_color',
				'label' => esc_html__( 'border color', 'xevsocore' ),
				'types' => [ 'classic' ],
				'default' => '#bfbfbf',
				'selector' => '{{WRAPPER}} .service-icon i:after',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'xevso_service_ficon_htab',
			[
				'label' => esc_html__( 'Font icon Hover', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_sicon_hcolor',
			[
				'label' => esc_html__( 'Icon Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .service-box:hover .service-icon i:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_sficon_hborder_color',
				'label' => esc_html__( 'border Hover', 'xevsocore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .service-box:hover .service-icon i:after',
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
		$this->add_control(
			'xevso_note_sms1',
			[
				'label' => esc_html__( 'Image ICON', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'If You Select image Icon Then Worked This area Options', 'xevsocore' ),
				'content_classes' => 'note-message',
			]
		);
		$this->add_responsive_control(
			'xevso_service_iicon_size',
			[
				'label' => esc_html__( 'Image Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 29,
				],
				'selectors' => [
					'{{WRAPPER}} .service-icon img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'line3',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_responsive_control(
			'xevso_service_icons_alignment',
			[
				'label' => esc_html__( 'Alignment', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .service-icon' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
				'toggle' => true,
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_service_tite_styles',
			[
			    'label' => esc_html__( 'Title Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_service_title_tabs');

        	$this->start_controls_tab( 'xevso_service_title_tab',
			[
				'label' => esc_html__( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_service_title_typo',
			    'selector' => '{{WRAPPER}} .service-titles h3',
			]
		);  
		$this->add_control(
			'xevso_service_title_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .service-titles h3 a' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->end_controls_tab();

		$this->start_controls_tab( 'xevso_service_titleh_tab',
			[
				'label' => esc_html__( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_service_title_hcolor',
			[
				'label' => esc_html__( 'Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .service-box:hover .service-titles a' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'line4',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_responsive_control(
			'xevso_service_title_alignment',
			[
				'label' => esc_html__( 'Alignment', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .service-titles h3' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
			'xevso_service_title_padding',
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
				  '{{WRAPPER}} .service-titles h3' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_service_title_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '30px',
				  'right' => '0',
				  'bottom' => '20px',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .service-titles h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_service_tite_icon_style',
			[
			    'label' => esc_html__( 'Title Icon', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'xevso_service_title_icon_color',
			[
				'label' => esc_html__( 'Font Icon Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .service-title-con i:before' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_responsive_control(
			'xevso_service_title_icon_size',
			[
				'label' => esc_html__( 'Font Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 300,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 41,
				],
				'selectors' => [
					'{{WRAPPER}} .service-title-con i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'xevso_service_title_hicon_color',
			[
				'label' => esc_html__( 'Icon hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .service-box:hover .service-title-con i' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_control(
			'ticon1',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		
		$this->add_responsive_control(
			'xevso_service_title_icon_margin',
			[
				'label' => esc_html__( 'Title Icon Margin Right', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 80,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .service-title-con' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_service_title_icon_top',
			[
				'label' => esc_html__( 'Title Icon Position', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .service-title-con' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

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
		if($settings['xevso_service_select'] == 2 ){
			$xevso_service_style = 'style-two';
		}elseif($settings['xevso_service_select'] == 3 ){
			$xevso_service_style = 'style-three';
		}else{
			$xevso_service_style = '';
		}
		if( $settings['xevso_service_links'] == 'page' ){
			$xevso_service_source = get_page_link( $settings['xevso_service_page_link'] );
		}else{
			$xevso_service_source =  $settings['xevso_service_extranal'];
		}
		?>
		<div class="service-box <?php echo esc_attr($xevso_service_style); ?> <?php echo esc_attr($settings['xevso_service_box_animations']) ?>">
			<?php if(empty($settings['xevso_service_select'] == '3'  )) : ?>
			<div class="service-icon">
				<?php 
				if(!empty($settings['service_icons_type'] == 'img' )){
					echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'xevso_sicon_img_resize', 'xevso_sicon_img' );
				}else{
					echo '<i class="'.esc_attr($settings['xevso_service_icon']['value']).'"></i>';
				}
				 ?>
			</div>
			<?php endif; ?>
			<div class="service-contents">
				<?php if(!empty($settings['xevso_service_select'] == '2' )) : ?>
				<div class="service-title-con">
				<i class="<?php echo esc_attr($settings['xevso_service_titleicon']); ?>"></i>
				</div>
				<?php endif; ?>
				<div class="service-c-inner">
					<div class="service-titles">
						<?php if(!empty($settings['xevso_service_select'] == '3' )) : ?>
						<div class="service-title-con">
							<i class="<?php echo esc_attr($settings['xevso_service_titleicon']['value']); ?>"></i>
						</div>
						<?php endif; ?>
					<div class="service-dec">
						<h3>
							<a href="<?php echo esc_url($xevso_service_source); ?>"><?php echo esc_html($settings['xevso_service_title']); ?></a>
						</h3>
						<?php echo wp_kses_post(wpautop($settings['xevso_service_dec'])); ?>
					</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_service_Widget );