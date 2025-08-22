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
class xevso_promoc_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-promo-content';
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
		return esc_html__( 'xevso Promo Content', 'xevsocore' );
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
		return [ 'xevso', 'promo content' ];
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
			'xevso_promoc_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_promoc_stitle',
			[
			    'label' => esc_html__( 'Small Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Small Title','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_promo_title',
			[
			    'label' => esc_html__( 'Title', 'xevsocore' ),
			    'type' => Controls_Manager::TEXT,
			    'default' => esc_html__( 'Customer Service','xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_promoc_img',
			[
			    'label' => esc_html__('Image','xevsocore'),
			    'type'=>Controls_Manager::MEDIA,
			    'default' => [
				  'url' => Utils::get_placeholder_image_src(),
			    ],
			]
		);
	
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
			    'name' => 'xevso_promoc_img_resize',
			    'default' => 'large',
			    'separator' => 'none',
			]
		);
		$this->add_control(
			'xevso_promoc_icon',
			[
				'label' => esc_html__( 'Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICON,
			]
		);
		$this->add_control(
			'xevso_promoc_btn_enable',
			[
				'label' => esc_html__( 'Enable Buttons', 'xevsocore' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'xevsocore' ),
				'label_off' => esc_html__( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'xevso_promoc_buttons_links',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
				'condition' => [
					'xevso_promoc_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_promoc_buttons_link_extralnal',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'xevso_promoc_buttons_links' => 'extranal',
					'xevso_promoc_btn_enable' => 'yes',
				],
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_promoc_buttons_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => [
					'xevso_promoc_buttons_links' => 'page',
					'xevso_promoc_btn_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'xevso_promoc_button_new_tab',
			[
			    'label'         => esc_html__( 'Open New Tab ? ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'Yes', 'xevsocore' ),
			    'label_off'     => esc_html__( 'No', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			    'condition' => [
					'xevso_promoc_btn_enable' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_promoc_box_styles',
			[
				'label' => esc_html__( 'box', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_promoc_box_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .promoc-boxs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_promoc_box_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .promoc-boxs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_promoc_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .promoc-boxs',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_promoc_icon_styles',
			[
				'label' => esc_html__( 'icon', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_promoc_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .promo-icon i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'xevso_promoc_icon_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .promo-icon i:before' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_responsive_control(
			'xevso_promoc_icon_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .promo-icon i:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_promoc_icon_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .promo-icon i:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_promoc_content_styles',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs(
			'xevso_title_tabs'
		);
		$this->start_controls_tab(
			'xevso_promoc_stitle_stab',
			[
				'label' => esc_html__( 'Small Title', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_promoc_stitle_scolor',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .promoc-titles span' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_promoc_stitle_typrography',
			    'label' => esc_html__( 'Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .promoc-titles span',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_promoc_title_tab',
			[
				'label' => esc_html__( 'Title', 'xevsocore' ),
			]
		);
		
		$this->add_control(
			'xevso_promoc_title_hcolor',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .promoc-titles h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_promoc_title_htyprography',
			    'label' => esc_html__( 'Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .promoc-titles h2',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_promoc_buttons_style',
			[
				'label' => esc_html__( 'Buttons', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_promoc_buttons_size',
			[
				'label' => esc_html__( 'button Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .promoc-btn i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'xevso_promoc_buttons_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .promoc-btn i:before' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_promoc_buttons_bg',
				'label' => esc_html__( 'background color', 'xevsocore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} a.promoc-btn',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_promoc_buttons_border',
				'label' => __( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .a.promoc-btn',
			]
		);
		$this->add_control(
			'btn_line',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'important_note',
			[
				'label' => esc_html__( '<strong>Button Hover Section</strong>', 'xevsocore' ),
				'type' =>Controls_Manager::RAW_HTML,
			]
		);
		$this->add_control(
			'xevso_promoc_buttons_hcolor',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' =>'#ffffff',
				'selectors' => [
					'{{WRAPPER}} a.promoc-btn:hover i:before' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_promoc_buttons_hbg',
				'label' => esc_html__( 'background color', 'xevsocore' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} a.promoc-btn:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_promoc_buttons_hborder',
				'label' => esc_html__( 'Hover Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} a.promoc-btn:hover',
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
		if( $settings['xevso_promoc_buttons_page_link'] == 'page' ){
			$xevso_promoc_btn_page = get_page_link( $settings['xevso_promoc_buttons_page_link'] );
		}else{
			$xevso_promoc_btn_page =  $settings['xevso_promoc_buttons_link_extralnal'];
		}
		?>
		<div class="promoc-boxs">
			<?php if(!empty($settings['xevso_promoc_img'] )) : ?>
			<div class="promoc-img">
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'xevso_promoc_img_resize', 'xevso_promoc_img' ); ?>
			</div>
			<?php endif; ?>
			<div class="promoc-content d-flex flex-wrap align-content-center">
				<div class="left d-flex flex-wrap align-content-center">
					<?php if(!empty($settings['xevso_promoc_img'] )) : ?>
					<div class="promo-icon">
						<i class="<?php echo esc_attr($settings['xevso_promoc_icon']); ?>"></i>
					</div>
					<?php endif; ?>
					<div class="promoc-titles">
					<?php
					if(!empty($settings['xevso_promoc_stitle'] )){
						echo '<span>'.$settings['xevso_promoc_stitle'].'</span>';
					} 
					if(!empty($settings['xevso_promo_title'] )){
						echo '<h2>'.$settings['xevso_promo_title'].'</h2>';
					} 
					?>
					</div>
				</div>
				<?php if(!empty($settings['xevso_promoc_btn_enable'] )) : ?>
				<div class="right">
					<a href="<?php echo esc_url($xevso_promoc_btn_page); ?>" <?php if(!empty($settings['xevso_promoc_button_new_tab'] == 'yes' )) : ?>target="_blank"<?php endif; ?> class="promoc-btn"><i class="flaticon-right-arrow"></i></a>					
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_promoc_Widget );