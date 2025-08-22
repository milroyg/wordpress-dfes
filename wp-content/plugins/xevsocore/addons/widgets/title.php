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
class xevso_title_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-title';
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
		return esc_html__( 'xevso Title', 'xevsocore' );
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
		return [ 'xevso', 'title' ];
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
			'xevso_title_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_small_title',
			[
			    'label' => esc_html__( 'Small Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Small Title','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_title',
			[
			    'label' => esc_html__( 'Hadding', 'xevsocore' ),
			    'type' => Controls_Manager::WYSIWYG,
			    'default' => esc_html__( 'We work creatively and specially for our clients You can check out our work.','xevsocore' ),
			    'placeholder' => esc_html__( 'Title Hadding', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_title_btn_enable',
			[
				'label' => esc_html__( 'Enable Buttons', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'xevso_title_buttons_links',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
				'condition' => [
					'xevso_title_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_title_buttons_link_extralnal',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'xevso_title_buttons_links' => 'extranal',
					'xevso_title_btn_enable' => 'yes',
				],
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_title_buttons_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => [
					'xevso_title_buttons_links' => 'page',
					'xevso_title_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_title_buttons_readmore_link_test',
			[
			    'label' => esc_html__( 'Button Link Text', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Read More','xevsocore'),
			    'condition' => [
					'xevso_title_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_title_button_new_tab',
			[
			    'label'         => esc_html__( 'Open New Tab ? ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'Yes', 'xevsocore' ),
			    'label_off'     => esc_html__( 'No', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			    'condition' => [
					'xevso_title_btn_enable' => 'yes',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_title_styles',
			[
				'label' => esc_html__( 'Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'xevso_title_tabs'
		);
		$this->start_controls_tab(
			'xevso_title_stab',
			[
				'label' => esc_html__( 'Small Title', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_title_scolor',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .small-title' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_title_typrography',
			    'label' => esc_html__( 'Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .small-title',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_title_hadding_tab',
			[
				'label' => esc_html__( 'Hadding', 'xevsocore' ),
			]
		);
		
		$this->add_control(
			'xevso_about_v_con_hcolor',
			[
				'label' => esc_html__( 'Hadding Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .title-hadding span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_title_htyprography',
			    'label' => esc_html__( 'Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .title-hadding p',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'xevso_title_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .title-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);        
		$this->add_responsive_control(
			'xevso_title_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .title-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);  
		$this->add_responsive_control(
			'xevso_title_alignment',
			[
			    'label' => __( 'Alignment', 'xevsocore' ),
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
				  '{{WRAPPER}} .title-box' => 'text-align: {{VALUE}};',
			    ],
			    'separator' =>'before',
			]
		  );
		$this->end_controls_section();
		// Button Start
		$this->start_controls_section(
			'xevso_title_btns',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_title_btns_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn',
			]
		);
		$this->add_responsive_control(
			'xevso_title_btns_margin',
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
			'xevso_title_btns_padding',
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
			'xevso_title_btns_tabs'
		);
		$this->start_controls_tab(
			'xevso_title_btns_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_title_btns_ncolor',
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
				'name' => 'xevso_title_btns_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_title_btns_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_title_btns_nradius',
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
			'xevso_title_btns_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_title_btns_hcolor',
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
				'name' => 'xevso_title_btns_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__blob',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_title_btns_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn:hover .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_title_btns_hradius',
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
		if( $settings['xevso_title_buttons_page_link'] == 'page' ){
			$xevso_title_btn_page = get_page_link( $settings['xevso_title_buttons_page_link'] );
		}else{
			$xevso_title_btn_page =  $settings['xevso_title_buttons_link_extralnal'];
		}
		?>
		<div class="title-boxs">
			<div class="title-box">
				<?php if(!empty($settings['xevso_small_title'])) : ?>
				<span class="small-title"><?php echo esc_html($settings['xevso_small_title']); ?></span>
				<?php endif; ?>
				<?php if(!empty($settings['xevso_title'])) : ?>
					<div class="title-hadding"><?php echo wp_kses_post(wpautop($settings['xevso_title'])); ?></div>
				<?php endif; ?>
				<?php if(!empty( $settings['xevso_title_btn_enable'] == 'yes' )) : ?>
					<div class="xevso-theme-buttons ">
						<div class="theme-buttons">
							<a <?php if(!empty($settings['xevso_title_button_new_tab'] == 'yes' )) : ?>target="_blank"<?php endif; ?> href="<?php echo esc_url($xevso_title_btn_page) ?>" class="blob-btn"><?php echo esc_html($settings['xevso_title_buttons_readmore_link_test']); ?><i class="flaticon-right-arrow"></i>
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
Plugin::instance()->widgets_manager->register_widget_type( new xevso_title_Widget );