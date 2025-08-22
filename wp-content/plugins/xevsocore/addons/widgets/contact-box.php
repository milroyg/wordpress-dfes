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
class xevso_contactbox_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-contactbox';
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
		return esc_html__( 'xevso Contact Box', 'xevsocore' );
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
		return [ 'xevso', 'contact-box' ];
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
			'xevso_contactbox_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_cbox_icon_type',
			[
			    'label' => esc_html__('Icon Type','xevsocore'),
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
			]
		  );
	
		  $this->add_control(
			'xevso_cbox_icon_img',
			[
			    'label' => esc_html__('Image','xevsocore'),
			    'type'=>Controls_Manager::MEDIA,
			    'default' => [
				  'url' => Utils::get_placeholder_image_src(),
			    ],
			    'condition' => [
				  'xevso_cbox_icon_type' => 'img',
			    ]
			]
		  );
	
		  $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
			    'name' => 'xevso_cbox_icon_img_resize',
			    'default' => 'large',
			    'separator' => 'none',
			    'condition' => [
				  'xevso_cbox_icon_type' => 'img',
			    ]
			]
		  );
		  $this->add_control(
			'xevso_cbox_icon',
			[
				'label' => esc_html__( 'Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'condition'=>[
					'xevso_cbox_icon_type'=> 'icon',
				],
				'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_cbox_title',
			[
			    'label' => esc_html__( 'Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Contact Us','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_cbox_content',
			[
			    'label' => esc_html__( 'content', 'xevsocore' ),
			    'type' => Controls_Manager::WYSIWYG,
			    'default'       => wp_kses(
				    __('
				    <ul>
				    	<li>24info@example.com</li>
				    	<li>support@example.com</li>
				    </ul>','xevsocore'),
				    array(
					'ul' => array(),
					'li' => array(),
					'strong' => array(),
					'span' => array(),
					'p' => array(),
				    )
			    )
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_cbox_box_style',
			[
			    'label' => esc_html__( 'Box Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_cbox_alignment',
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
					'{{WRAPPER}} .static-contact-box' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
			'xevso_cbox_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .static-contact-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->add_responsive_control(
			'xevso_cbox_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .static-contact-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_cbox_icons_styles',
			[
			    'label' => esc_html__( 'Icons Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs('xevso_cbox_icons_style_tabs');

        	$this->start_controls_tab( 'xevso_cbox_ficon_tab',
			[
				'label' => esc_html__( 'Font icon Normal', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_cbox_ficon_color',
			[
				'label' => esc_html__( 'Icon Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0b5be0',
				'selectors' => [
					'{{WRAPPER}} .cbox-icon i:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_cbox_ficon_size',
			[
				'label' => esc_html__( 'Font Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 40,
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
					'size' => 64,
				],
				'selectors' => [
					'{{WRAPPER}} .cbox-icon i:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_tab();
		$this->start_controls_tab( 'xevso_cbox_ficon_htab',
			[
				'label' => esc_html__( 'Font icon Hover', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_cbox_ficon_hcolor',
			[
				'label' => esc_html__( 'Icon Hover Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .static-contact-box:hover .cbox-icon i:before' => 'color: {{VALUE}};',
				],
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
				'label' => __( '<strong>Image ICON</string>', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'content_classes' => 'note-message',
			]
		);
		$this->add_responsive_control(
			'xevso_cbox_iimg_icon_size',
			[
				'label' => esc_html__( 'Image Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 65,
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
					'size' => 17,
				],
				'selectors' => [
					'{{WRAPPER}} .cbox-icon img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
	
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_cbox_title_style',
			[
			    'label' => esc_html__( 'Title Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'xevso_cbox_title_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#0a5be0',
				'selectors' => [
					'{{WRAPPER}} .cbox-title h2' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_cbox_title_typro',
				'selector' => '{{WRAPPER}} .cbox-title h2',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_cbox_dec_style',
			[
			    'label' => esc_html__( 'Content Style', 'xevsocore' ),
			    'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'xevso_cbox_con_color',
			[
				'label' => esc_html__( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'default'	=>'#798795',
				'selectors' => [
					'{{WRAPPER}} .cbox-dec' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_cbox_dec_typro',
				'selector' => '{{WRAPPER}} .cbox-dec p,.cbox-dec ul li',
			]
		);
		$this->add_responsive_control(
			'xevso_cbox_title_margin',
			[
			    'label' => __( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
				  'top' => '0',
				  'right' => '0',
				  'bottom' => '0',
				  'left' => '0',
				  'isLinked' => true
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .cbox-dec' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->add_responsive_control(
			'xevso_cbox_title_padding',
			[
			    'label' => __( 'padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'default' => [
				  'top' => '0',
				  'right' => '38',
				  'bottom' => '0',
				  'left' => '38',
				  'isLinked' => true
			    ],
			    'selectors' => [
				  '{{WRAPPER}} .cbox-dec' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		?>
		<div class="static-contact-box">
			<div class="cbox-icon">
				<?php
					if(!empty($settings['xevso_cbox_icon_type'] == 'img' )){
						echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'xevso_cbox_icon_img_resize', 'xevso_cbox_icon_img' );
					}else{
						echo '<i class="'.esc_attr($settings['xevso_cbox_icon']['value']).'"></i>';
					}
				?>
			</div>
			<div class="cbox-title">
				<h2><?php echo esc_html($settings['xevso_cbox_title']) ?></h2>
			</div>
			<div class="cbox-dec">
				<?php echo wp_kses_post(wpautop($settings['xevso_cbox_content'])); ?>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_contactbox_Widget );