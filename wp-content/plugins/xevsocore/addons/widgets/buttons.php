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
class xevso_buttons_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-buttons';
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
		return esc_html__( 'xevso Buttons', 'xevsocore' );
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
		return [ 'xevso', 'buttons' ];
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
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_button_style',
			[
				'label' => esc_html__( 'Select Buttons', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'  	=> esc_html__( 'Style One', 'xevsocore' ),
					'2' 	=> esc_html__( 'Style Two', 'xevsocore' ),
					'3' 	=> esc_html__( 'Style Three', 'xevsocore' ),
				],
			]
		);
		$this->add_control(
			'xevso_buttons_links',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
			]
		);
		$this->add_control(
			'xevso_buttons_link_extralnal',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'xevso_buttons_links' => 'extranal',
				],
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_buttons_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => [
					'xevso_buttons_links' => 'page',
				],
			]
		);
		$this->add_control(
			'xevso_buttons_static_test',
			[
			    'label' => esc_html__( 'Readmore Text', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('We work creatively and specially for our client','xevsocore'),
			    'condition' => [
					'xevso_button_style' => '2',
			    ],
			]
		);
		$this->add_control(
			'xevso_buttons_readmore_link_test',
			[
			    'label' => esc_html__( 'Button Link Text', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		$this->add_control(
			'xevso_button_new_tab',
			[
			    'label'         => esc_html__( 'Open New Tab ? ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'Yes', 'xevsocore' ),
			    'label_off'     => esc_html__( 'No', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_buttons_style',
			[
				'label' => esc_html__( 'Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_buttons_tabs');

		$this->start_controls_tab( 'xevso_buttons_tab1',
			[
				'label' => esc_html__( 'Style One', 'xevsocore' ),
			]
		);   
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Typography', 'xevsocore' ),
			    	'name' => 'xevso_buttons_style1_typo',
			    	'selector' => '{{WRAPPER}} .xevso-theme-buttons a.theme-button',
			]
		);   
		$this->add_control(
			'xevso_buttons_style1_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xevso-theme-buttons a.theme-button' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_buttons_style1_bg',
				'label' => esc_html__( 'background color', 'xevsocore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .xevso-theme-buttons a.theme-button',
			]
		);
		$this->add_responsive_control(
			'xevso_buttons_style1_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .xevso-theme-buttons a.theme-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);        
		$this->add_responsive_control(
			'xevso_buttons_style1_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
					'top' => '17',
					'right' => '30',
					'bottom' => '17',
					'left' => '30',
					'isLinked' => false
			  	],
			    'selectors' => [
				 	 '{{WRAPPER}} .xevso-theme-buttons a.theme-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    	],
			    'separator' =>'before',
			]
		);   
		$this->add_control(
			'btn_line',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'important_note',
			[
				'label' => __( '<strong>Button Hover Section</strong>', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
			]
		);
		$this->add_control(
			'xevso_buttons_style1_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xevso-theme-buttons a.theme-button:hover' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_buttons_style1_hbg',
				'label' => esc_html__( 'background color', 'xevsocore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .xevso-theme-buttons a.theme-button:hover',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'xevso_buttons_tab2',
			[
				'label' => esc_html__( 'Style Two', 'xevsocore' ),
			]
		);      
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'link Typography', 'xevsocore' ),
			    	'name' => 'xevso_buttons_style2_typo',
			    	'selector' => '{{WRAPPER}} .xevso-theme-buttons.button-style-2 .theme-button',
			]
		);   
		$this->add_control(
			'xevso_buttons_style2_color',
			[
				'label' => esc_html__( 'Link Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xevso-theme-buttons.button-style-2 .theme-button' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_control(
			'xevso_buttons_style2_hcolor',
			[
				'label' => esc_html__( 'Link Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xevso-theme-buttons.button-style-2 .theme-button:hover' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_control(
			'btn_line2',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'important_note2',
			[
				'label' => __( '<strong>Busston Style Two Static Test </strong>', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Static Text Typography', 'xevsocore' ),
			    	'name' => 'xevso_buttons_style2_stypo',
			    	'selector' => '{{WRAPPER}} .xevso-theme-buttons.button-style-2 .theme-buttons span',
			]
		);   
		$this->add_control(
			'xevso_buttons_style2_scolor',
			[
				'label' => esc_html__( 'Static Text Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xevso-theme-buttons.button-style-2 .theme-buttons span' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		// Button Start
		$this->start_controls_section(
			'xevso_buttons_three',
			[
				'label' => esc_html__( 'Button Three', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'xevso_button_style' => '3',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_buttons_three_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn',
			]
		);
		$this->add_responsive_control(
			'xevso_buttons_three_margin',
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
			'xevso_buttons_three_padding',
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
			'xevso_buttons_three_tabs'
		);
		$this->start_controls_tab(
			'xevso_buttons_three_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_buttons_three_ncolor',
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
				'name' => 'xevso_buttons_three_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_buttons_three_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_buttons_three_nradius',
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
			'xevso_buttons_three_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_buttons_three_hcolor',
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
				'name' => 'xevso_buttons_three_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__blob',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_buttons_three_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn:hover .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_buttons_three_hradius',
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
		$this->start_controls_section(
			'xevso_buttons_box',
			[
				'label' => esc_html__( 'Box', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_buttons_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .xevso-theme-buttons' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);        
		$this->add_responsive_control(
			'xevso_buttons_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .xevso-theme-buttons' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);   
		$this->add_responsive_control(
			'xevso_buttons_alignment',
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
				  ]
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .xevso-theme-buttons' => 'text-align: {{VALUE}};',
			    ],
			    'separator' =>'before',
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
		if( $settings['xevso_buttons_links'] == 'page' ){
			$xevso_btn_page = get_page_link( $settings['xevso_buttons_page_link'] );
		}else{
			$xevso_btn_page =  $settings['xevso_buttons_link_extralnal'];
		}
		?>
		<div class="xevso-theme-buttons <?php if(!empty($settings['xevso_button_style'] == '2' )) : ?>button-style-2<?php endif; ?>">
			<div class="theme-buttons">
				<?php if(!empty($settings['xevso_button_style'] == '2' )) : ?>
					<span><?php echo esc_html($settings['xevso_buttons_static_test']); ?></span>
				<?php endif; ?>
				<?php if(!empty($settings['xevso_button_style'] == '2' || $settings['xevso_button_style'] == '1')) : ?>
				<a <?php if(!empty($settings['xevso_button_new_tab'] == 'yes' )) : ?>target="_blank"<?php endif; ?> href="<?php echo esc_url($xevso_btn_page) ?>" class="theme-button colorbg">
					<?php echo esc_html($settings['xevso_buttons_readmore_link_test']) ?><i class="flaticon-right-arrow"></i>
				</a>
				<?php endif; ?>
				<?php if(!empty($settings['xevso_button_style'] == '3' )) : ?>
					<a <?php if(!empty($settings['xevso_button_new_tab'] == 'yes' )) : ?>target="_blank"<?php endif; ?> href="<?php echo esc_url($xevso_btn_page) ?>" class="blob-btn"><?php echo esc_html($settings['xevso_buttons_readmore_link_test']) ?><i class="flaticon-right-arrow"></i>
						<span class="blob-btn__inner">
							<span class="blob-btn__blobs">
								<span class="blob-btn__blob"></span>
								<span class="blob-btn__blob"></span>
								<span class="blob-btn__blob"></span>
								<span class="blob-btn__blob"></span>
							</span>
						</span>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_buttons_Widget );