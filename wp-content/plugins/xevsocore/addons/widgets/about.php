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
class xevso_about_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-about';
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
		return esc_html__( 'xevso About', 'xevsocore' );
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
		return [ 'xevso', 'about' ];
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
			'xevso_about_contens',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
            
		$this->add_control(
			'xevso_abvout_stitle',
			[
			    'label'   => esc_html__( 'Small Title', 'xevsocore' ),
			    'type'    => Controls_Manager::TEXT,
			    'default' => esc_html__('About Us','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_about_hadding',
			[
				'label' => esc_html__( 'Hadding', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'We have more than about 20+ years experience IT solutions.', 'xevsocore' ),
				'placeholder' => esc_html__( 'Type your hadding Content here', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_about_dec',
			[
				'label' => esc_html__( 'Description', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => wp_kses(
					__( '<p>On the other hand, we denounce with righteous indiation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded desire, that they cannot foresee the pain and trouble that arund to ensue; and equal blame belongs. 
					<ul>
						<li>IT Solutions</li>
						<li>IT Consulting</li>
						<li>IT Services</li>
						<li>IT Insurance</li>
					</ul></p>', 'xevsocore' ),
					array(
						'ul'		=>array(),
					    	'li' 		=> array(),
					    	'span' 	=> array(),
					    	'br' 		=> array(),
					    	'strong' 	=> array(),
					)
				),
				'placeholder' => esc_html__( 'Type your Description here', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_about_reb_enable',
			[
			    'label'         => esc_html__( 'Enable Button ', 'xevsocore' ),
			    'type'          => Controls_Manager::SWITCHER,
			    'label_on'      => esc_html__( 'On', 'xevsocore' ),
			    'label_off'     => esc_html__( 'Off', 'xevsocore' ),
			    'return_value'  => 'yes',
			    'default'       => 'no',
			]
		);
		$this->add_control(
			'xevso_about_btn_text',
			[
			    'label'   => esc_html__( 'Button Text', 'xevsocore' ),
			    'type'    => Controls_Manager::TEXT,
			    'default' => esc_html__('More About','xevsocore'),
			    'condition' => [
					'xevso_about_reb_enable' => 'yes',
				]
			]
		);
		$this->add_control(
			'xevso_about_links',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'no' => esc_html__( 'Select Options', 'xevsocore' ),
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
				'condition' => [
                        	'xevso_about_reb_enable' => 'yes',
                  	]
			]
		);
		$this->add_control(
			'xevso_about_extranal',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => array(
					'xevso_about_links' => 'extranal',
					'xevso_about_reb_enable' => 'yes',
				),
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_about_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => array(
					'xevso_about_links' => 'page',
					'xevso_about_reb_enable' => 'yes',
				),
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_about_titles_css',
			[
				'label' => esc_html__( 'Title', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_about_stitle',
			    'label' => esc_html__( 'Small Title Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .about-titles h4',
			]
		);
		$this->add_control(
			'xevso_about_stitle_color',
			[
			    'label' => esc_html__( 'Small Title Color', 'xevsocore' ),
			    'type' => Controls_Manager::COLOR,
			    'default'=>'#0a5be0',
			    'selectors' => [
				  '{{WRAPPER}} .about-titles h4' => 'color: {{VALUE}}',
			    ],
			]
		  );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_about_hadding',
			    'label' => esc_html__( 'Hadding Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .about-titles h2',
			]
		);
		$this->add_control(
			'xevso_about_hadding_color',
			[
			    'label' => esc_html__( 'Hadding Color', 'xevsocore' ),
			    'type' => Controls_Manager::COLOR,
			    'default'=>'#000000',
			    'selectors' => [
				  '{{WRAPPER}} .about-titles h2' => 'color: {{VALUE}}',
			    ],
			]
		);
		$this->add_responsive_control(
			'xevso_about_title_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .about-titles h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );        
		$this->add_responsive_control(
			'xevso_about_title_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .about-titles h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();

		
		$this->start_controls_section(
			'xevso_about_styles',
			[
				'label' => esc_html__( 'Description', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_about_dec',
			    'label' => esc_html__( 'Description Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .about-content p',
			]
		);
		$this->add_responsive_control(
			'xevso_about_dec_color',
			[
				'label' => esc_html__( 'LDescriptionist Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .about-content p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_about_dec_list_typo',
			    'label' => esc_html__( 'Content List Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .about-content>ul li',
			]
		);
		$this->add_responsive_control(
			'xevso_about_dec_list_color',
			[
				'label' => esc_html__( 'List Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .about-content>ul li' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_about_dec_list_icolor',
			[
				'label' => esc_html__( 'List Icon Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .about-content>ul li:before' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_about_dec_li',
			[
				'label' => esc_html__( 'List Width', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .about-content>ul li' => 'width: {{SIZE}}%;',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_about_dec_margin',
			[
				'label' => esc_html__( 'Description Margin', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .about-content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_about_dec_padding',
			[
				'label' => esc_html__( 'Description Padding', 'xevsocore' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '40',
					'right' => '0',
					'bottom' => '40',
					'left' => '0',
					'isLinked' => true
				],
				'selectors' => [
					'{{WRAPPER}} .about-content p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_about_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .about-boxs' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		  );        
		$this->add_responsive_control(
			'xevso_about_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .about-boxs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);   
		  
		$this->end_controls_section();
		
		// Button Start
		$this->start_controls_section(
			'xevso_about_btn_three',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_about_btn_three_typo',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn',
			]
		);
		$this->add_responsive_control(
			'xevso_about_btn_three_margin',
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
			'xevso_about_btn_three_padding',
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
			'xevso_about_btn_three_tabs'
		);
		$this->start_controls_tab(
			'xevso_about_btn_three_ntab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_about_btn_three_ncolor',
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
				'name' => 'xevso_about_btn_three_nbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_about_btn_three_nborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_about_btn_three_nradius',
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
			'xevso_about_btn_three_htab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_responsive_control(
			'xevso_about_btn_three_hcolor',
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
				'name' => 'xevso_about_btn_three_hbg',
				'label' => esc_html__( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .blob-btn__blob',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_about_btn_three_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .blob-btn:hover .blob-btn__inner',
			]
		);
		$this->add_responsive_control(
			'xevso_about_btn_three_hradius',
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
		$xevso_about_wp_kses = array(
			'a' => array(
			    'href' => array(),
			    'class' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'span' => array(),
			'ul' => array(),
			'li' => array(),
			'p'  =>array(),
		);
		$xevso_abouts_wp_kses = array(
			'a' => array(
			    'href' => array(),
			    'class' => array()
			),
			'br' => array(),
			'strong' => array(),
		);
		if( $settings['xevso_about_reb_enable'] == 'page' ){
			$xevso_about_btn = get_page_link( $settings['xevso_about_page_link'] );
		}else{
			$xevso_about_btn =  $settings['xevso_about_extranal'];
		}
		?>
		<div class="about-boxs">
			<div class="about-box">
				<div class="about-titles">
					<h4 class="color1">
					<?php echo esc_html($settings['xevso_abvout_stitle']); ?>
					</h4>
					<h2><?php echo wp_kses( $settings['xevso_about_hadding'], $xevso_abouts_wp_kses ) ?></h2>
				</div>
				<div class="about-content">
				<?php echo wp_kses(wpautop($settings['xevso_about_dec']),$xevso_about_wp_kses); ?>
				</div>
				<?php if($settings['xevso_about_reb_enable'] == 'yes') : ?>
				<div class="theme-about_btn">
					<a href="<?php echo esc_url($xevso_about_btn); ?>" class="blob-btn"><?php echo esc_html($settings['xevso_about_btn_text']) ?><i class="flaticon-right-arrow"></i>
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
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_about_Widget );