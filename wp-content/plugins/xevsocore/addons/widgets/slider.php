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
class xevso_slider_two_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-slider-two';
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
		return esc_html__( 'xevso Slider Two', 'xevsocore' );
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
		return [ 'xevso', 'slider Two' ];
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
			'xevso_slider_two_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'xevso_slider_two_img',
            [
                'label' => esc_html__('Image','xevsocore'),
                'type'=>Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
            'xevso_slider_two_title', [
                'label' => esc_html__( 'Title', 'xevsocore' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __( 'Choose Your IT Solution For Good Business!' , 'xevsocore' ),
                'show_label' => true,
            ]
        );
        
        $repeater->add_control(
            'xevso_slider_two_content', [
                'label' => esc_html__( 'Content', 'xevsocore' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __( 'Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh.sagittis magna.From helpdesk.' , 'xevsocore' ),
                'show_label' => true,
            ]
		);
		

        $repeater->add_control(
			'xevso_slider_two_btns',
			[
				'label' => esc_html__( 'Select Link', 'xevsocore' ),
				'type'  => Controls_Manager::SELECT,
				'default'	=> 'extranal',
				'options' => [
					'extranal' => esc_html__( 'Extranal', 'xevsocore' ),
					'page' =>  esc_html__( 'Page', 'xevsocore' ),
				],
				'label_block' => true,
			]
		);
		
        $repeater->add_control(
			'xevso_slider_two_extral',
			[
				'label' => esc_html__( 'Extranal Link', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'xevso_slider_two_btns' => 'extranal',
				],
				'placeholder' => esc_html__( 'Add Extranal Link', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'xevso_slider_two_page_link',
			[
				'label' => esc_html__( 'Page Link', 'xevsocore' ),
				'type' => Controls_Manager::SELECT,
				'options' => xevso_page_list(),
				'condition' => [
					'xevso_slider_two_btns' => 'page',
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'xevso_slider_two_btn_text',
			[
				'label' => esc_html__( 'Buttn Text', 'xevsocore' ),
				'type' => Controls_Manager::TEXT,
				'default'	=> esc_html__( 'Read More', 'xevsocore' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'xevso_slidertow_enable',
			[
				'label' => __( 'Enable Video button', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'xevsocore' ),
				'label_off' => __( 'Hide', 'xevsocore' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$repeater->add_control(
			'xevso_slider_video_position',
			[
				'label' => __( 'Select Position', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'  => __( 'Left Side', 'xevsocore' ),
					'2' => __( 'Right Side', 'xevsocore' ),
				],
			]
		);
		$repeater->add_control(
			'xevso_slider_video_link',
			[
				'label' => __( 'Video Link', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'xevso_slidertow_enable' => 'yes',
				],
				'label_block' => true,
				'default' => 'https://www.youtube.com/watch?v=f3NWvUV8MD8',			
			]
		);
		$repeater->add_control(
			'xevso_slider_video_text',
			[
				'label' => __( 'Video Text', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'xevso_slidertow_enable' => 'yes',
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'xevso_slider_two_title_ani',
			[
				'label' => esc_html__( 'Title Animation', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ANIMATION,
				'prefix_class' => 'animated ',
				 'default' => 'fadeInUp',
			]
		);
		$repeater->add_responsive_control(
			'xevso_slider_two_title_dtime',
			[
				'label' => esc_html__( 'Title Animation Delay', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range' => [
					's' => [
						'min' => .1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 1,
				],
			]
		);
        $repeater->add_control(
			'xevso_slider_two_content_ani',
			[
				'label' => esc_html__( 'Content Animation', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ANIMATION,
				'prefix_class' => 'animated ',
				 'default' =>'fadeInUp',
			]
		);
		$repeater->add_responsive_control(
			'xevso_slider_two_dec_dtime',
			[
				'label' => esc_html__( 'Content Animation Delay', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range' => [
					's' => [
						'min' => .1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 1,
				],
			]
		);
		$repeater->add_control(
			'xevso_slider_two_btn_ani',
			[
				'label' => esc_html__( 'Button Animation', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ANIMATION,
				'prefix_class' => 'animated ',
				 'default' =>'fadeInUp',
			]
		);
		$repeater->add_responsive_control(
			'xevso_slider_two_button_dtime',
			[
				'label' => esc_html__( 'Button Animation Delay', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range' => [
					's' => [
						'min' => .1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 1,
				],
			]
		);
		$repeater->add_control(
			'xevso_slider_video_ani',
			[
				'label' => esc_html__( 'Video Button Animation', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ANIMATION,
				'prefix_class' => 'animated ',
				'label_block' => true,
				'default' => __( 'fadeInUp' , 'xevsocore' ),
				'condition' => [
					'xevso_slidertow_enable' => 'yes',
				],
			]
		);
		$repeater->add_responsive_control(
			'xevso_slider_two_video_dtime',
			[
				'label' => esc_html__( 'Video Animation Delay', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range' => [
					's' => [
						'min' => .1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 1,
				],
			]
		);
        $this->add_control(
            'xevso_sliders_two',
            [
                'label' => esc_html__( 'Slider List', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'xevso_slider_two_title' => __( 'Choose Your IT Solution For Good Business!', 'xevsocore' ),
                        'xevso_slider_two_content' => __('Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh.sagittis magna.From helpdesk. ', 'xevsocore' ),
                        'xevso_slider_two_btn_text' =>  __( 'Read More', 'xevsocore' ),
                        'xevso_slider_two_title_ani' =>  'fadeInUp',
                        'xevso_slider_two_content_ani' => 'fadeInUp',
                        'xevso_slider_two_btn_ani' => 'fadeInUp',
                        'xevso_slider_video_ani' => 'fadeInUp',
                        'xevso_slider_video_position' => 1,
                        'xevso_slider_video_text' => '',
                        'xevso_slider_video_link' =>  'https://www.youtube.com/watch?v=f3NWvUV8MD8',
                    ],
                ],
            ]
        );
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_sliders_two_styles',
			[
				'label' => esc_html__( 'Box', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		); 
		$this->add_control(
			'xevso_sliders_two_aligment',
			[
				'label' => __( 'Alignment', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'xevsocore' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'xevsocore' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'xevsocore' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .slider-content' => 'text-align: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_sliders_box_height',
			[
				'label' => esc_html__( 'Height', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 700,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 750,
				],
				'selectors' => [
					'{{WRAPPER}} .single-slider' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .image-layout' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_sliders_two_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '200',
				  'right' => '0',
				  'bottom' => '200',
				  'left' => '0',
			    ],
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .single-slider' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_control(
			'slider_not',
			[
				'label' => __( 'Image Opacity with Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'xevso_slider_two_img_background',
			[
				'label' => esc_html__( 'Opacity Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .image-layout:after' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_sliders_two_title_style',
			[
				'label' => esc_html__( 'Title', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_sliders_two_title_css',
			    'label' => esc_html__( 'Title', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .single-slider .sub-title',
			]
		); 
		$this->add_control(
			'xevso_sliders_two_title_color',
			[
				'label' => __( 'Title Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single-slider .sub-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_sliders_two_title_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' 	=> '0',
				  'right' 	=> '0',
				  'bottom' 	=> '0',
				  'left' 	=> '0',
			    ],
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .single-slider .sub-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_sliders_two_title_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' 	=> '0',
				  'right' 	=> '0',
				  'bottom' 	=> '0',
				  'left' 	=> '0',
			    ],
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .single-slider .sub-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_sliders_two_dec_style',
			[
				'label' => esc_html__( 'Description', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_sliders_two_dec_css',
			    'label' => esc_html__( 'Description', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .single-slider .slider-dec',
			]
		); 
		$this->add_control(
			'xevso_sliders_two_dec_color',
			[
				'label' => __( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .single-slider .slider-dec' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_sliders_two_dec_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .single-slider .slider-dec' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_sliders_two_dec_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .single-slider .slider-dec' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_sliders_two_btn_style',
			[
				'label' => esc_html__( 'Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_sliders_two_btn_css',
			    'label' => esc_html__( 'Content', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .xevso-slider2-btnss a.blob-btn',
			]
		);  
		
		$this->add_responsive_control(
			'xevso_sliders_two_btn_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .xevso-slider2-btnss a.blob-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_sliders_two_btn_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .xevso-slider2-btnss a.blob-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_sliders_two_btn_icon_top',
			[
				'label' => esc_html__( 'Icon Top To Bottom', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xevso-slider2-btnss a.blob-btn i' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_sliders_two_btn_icon_left',
			[
				'label' => esc_html__( 'Icon Left To Right', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .xevso-slider2-btnss a.blob-btn i' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'xevso_sliders_two_btn_tabs'
		);
		$this->start_controls_tab(
			'xevso_sliders_two_normal_tab',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_sliders_two_btn_ncolor',
			[
				'label' => __( 'Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xevso-slider2-btnss a.blob-btn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_sliders_two_btn_bg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .xevso-slider2-btnss a.blob-btn',
			]
		);
	$this->add_responsive_control(
			'xevso_buttons_thrffq',
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
					'{{WRAPPER}} a.blob-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'xevso_sliders_two_hover_tab',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_sliders_two_btn_hcolr',
			[
				'label' => __( 'Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .xevso-slider2-btnss a.blob-btn:hover' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_responsive_control(
			'xevso_buttons_three_hrartq',
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
					'{{WRAPPER}} a.blob-btn:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		
		
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'xevso_sliders_two_btn_hbg',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .xevso-slider2-btnss a:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		$this->start_controls_section(
			'xevso_sliders_two_video_btns',
			[
				'label' => esc_html__( 'video Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
			]
		);
	
		$this->add_control(
			'xevso_sl_vlheight',
			[
				'label' => __( 'line Height', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 7,
				],
				'selectors' => [
					'{{WRAPPER}} a.slider-video i' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'xevso_sl_vicon_size',
			[
				'label' => __( 'Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 60,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 36,
				],
				'selectors' => [
					'{{WRAPPER}} a.slider-video i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'xevso_slvideoss_two_iconvi',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .slider-video-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
	$this->add_responsive_control(
			'tav-slider-video-btnn_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px'],
			    'selectors' => [
				  '{{WRAPPER}} .slider-video-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		);
		$this->start_controls_tabs(
			'xevso_s2tabs'
		);

		$this->start_controls_tab(
			'xevso_s2_tab_normal',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_control(
			'xevso_s2nicon_color',
			[
				'label' => __( 'Icon Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.slider-video i:before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'xevso_s2ntext_color',
			[
				'label' => __( 'Text Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slider-video-btn span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'xevso_s2nbtn_glw_olor',
			[
				'label' => __( 'Glow Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.slider-video:before' => 'background: {{VALUE}}',
					'{{WRAPPER}} a.slider-video:before' => 'background: {{VALUE}}',
				],
				'default' => '#0045ff',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_s2nbtn_bg_color',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} a.slider-video:after',
			]
		);
		$this->end_controls_tab();




		$this->start_controls_tab(
			'xevso_s2_tab_hover',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);

		$this->add_control(
			'xevso_s2nicon_hcolor',
			[
				'label' => __( 'Icon hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.slider-video:hover i:before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'xevso_s2htext_color',
			[
				'label' => __( 'Text Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slider-video-btn:hover span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'xevso_s2hbtglow_color',
			[
				'label' => __( 'Hover Glow Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.slider-video:hover:before' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_s2hn_bg_color',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} a.slider-video:hover:after',
			]
		);
		$this->add_control(
			'xevso_s2hbtn_radius',
			[
				'label' => __( 'Hover Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} a.slider-video' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} a.slider-video:before' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} a.slider-video:after' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();
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
		<script>
			jQuery(document).ready(function($) {
				 "use strict";
				function mainSlider() {
			        var BasicSlider = $('.slider-active');
			        BasicSlider.on('init', function (e, slick) {
			            var $firstAnimatingElements = $('.single-slider:first-child').find('[data-animation]');
			            doAnimations($firstAnimatingElements);
			        });
			        BasicSlider.on('beforeChange', function (e, slick, currentSlide, nextSlide) {
			            var $animatingElements = $('.single-slider[data-slick-index="' + nextSlide + '"]').find('[data-animation]');
			            doAnimations($animatingElements);
			        });
			        BasicSlider.slick({
			            autoplay: true,
			            autoplaySpeed: 8000,
			            dots: false,
			            fade: true,
			            arrows: true,
			            prevArrow: '<span class="prev"><i class="fas fa-chevron-left"></i></span>',
			            nextArrow: '<span class="next"><i class="fas fa-chevron-right"></i></span>',
			            responsive: [
			                {
			                    breakpoint: 767,
			                    settings: {
			                        arrows: false
			                    }
			                }
			            ]
			        });

			        function doAnimations(elements) {
			            var animationEndEvents = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
			            elements.each(function () {
			                var $this = $(this);
			                var $animationDelay = $this.data('delay');
			                var $animationType = 'animated ' + $this.data('animation');
			                $this.css({
			                    'animation-delay': $animationDelay,
			                    '-webkit-animation-delay': $animationDelay
			                });
			                $this.addClass($animationType).one(animationEndEvents, function () {
			                    $this.removeClass($animationType);
			                });
			            });
			        }
			    }
			    mainSlider();
			});
			</script>
			<script>
				jQuery(document).ready(function($) {
					 "use strict";
					$('.slider-video').magnificPopup({
						disableOn: 700,
						type: 'iframe',
						mainClass: 'mfp-fade',
						removalDelay: 160,
						preloader: false,

						fixedContentPos: false
					});
				});
			</script>
		 <div class="slider-area slider-active">
			<?php foreach ($settings['xevso_sliders_two'] as $xevso_slider) : 
				if($xevso_slider['xevso_slider_two_btns'] == 'page' ){
					$link = get_page_link( $xevso_slider['xevso_slider_two_page_link'] );
				}else{
					$link = $xevso_slider['xevso_slider_two_extral'];
				}
				if($xevso_slider['xevso_slider_video_position'] == '2'){
					$col = '9';
				}else{
					$col = '10';
				}
				
			?> 

	       <div class="single-slider">
			<div class="image-layout" style="background-image:url( <?php echo esc_url(wp_get_attachment_image_url( $xevso_slider['xevso_slider_two_img']['id'], 'full' )); ?> )"></div>
	            <div class="container">
	                <div class=" justify-content-center <?php if($xevso_slider['xevso_slider_video_position'] == '1') : ?>row<?php endif; ?>">
	                    <div class="col-lg-12">
	                        <div class="slider-content">
	                            <div class="sub-title" data-animation="<?php echo esc_attr($xevso_slider['xevso_slider_two_title_ani']); ?>" data-delay="<?php echo esc_attr($xevso_slider['xevso_slider_two_title_dtime']['size']); ?>s"><?php echo wp_kses_post($xevso_slider['xevso_slider_two_title']); ?></div>
	                            <div class="slider-dec" data-animation="<?php echo esc_attr($xevso_slider['xevso_slider_two_content_ani']); ?>" data-delay="<?php echo esc_attr($xevso_slider['xevso_slider_two_dec_dtime']['size']); ?>s"><?php echo wp_kses_post($xevso_slider['xevso_slider_two_content']); ?></div>
	                            <div class="xevso-slider2-btnss">
									<?php
/*
<a class="blob-btn" href="<?php echo esc_url($link); ?>" data-animation="<?php echo esc_attr($xevso_slider['xevso_slider_two_btn_ani']); ?>" data-delay="<?php echo esc_attr($xevso_slider['xevso_slider_two_button_dtime']['size']); ?>s">
    <?php echo esc_html__($xevso_slider['xevso_slider_two_btn_text']); ?><i class="flaticon-right-arrow"></i>
</a>
*/
?>
	                            	 <?php if($xevso_slider['xevso_slider_video_position'] == '1') : ?>
									<div class="slider-video-btn" data-animation="<?php echo esc_attr($xevso_slider['xevso_slider_video_ani']); ?>" data-delay="<?php echo esc_attr($xevso_slider['xevso_slider_two_video_dtime']['size']); ?>s">
									<a href="<?php echo esc_url($xevso_slider['xevso_slider_video_link']); ?>"
									class="slider-video" aria-label="<?php echo esc_attr( !empty($xevso_slider['xevso_slider_video_text']) ? 'Play video: ' . $xevso_slider['xevso_slider_video_text'] : 'Play video' ); ?>"
									tabindex="0">
									<i class="fa fa-play" aria-hidden="false"></i>
									</a>
									</div>
					                <?php endif; ?>
	                            </div>
	                        </div>
	                    </div>
	                    <?php if($xevso_slider['xevso_slider_video_position'] == '2') : ?>
	                    <div class="col-lg-3 d-flex flex-wrap align-content-center">
							<div class="slider-video-btn" data-animation="<?php echo esc_attr($xevso_slider['xevso_slider_video_ani']); ?>" data-delay="<?php echo esc_attr($xevso_slider['xevso_slider_two_video_dtime']['size']); ?>s">
								<a href="<?php echo esc_url($xevso_slider['xevso_slider_video_link']); ?>"
									class="slider-video" aria-label="<?php echo esc_attr( !empty($xevso_slider['xevso_slider_video_text']) ? 'Play video: ' . $xevso_slider['xevso_slider_video_text'] : 'Play video' ); ?>"
									tabindex="0">
									<i class="fa fa-play" aria-hidden="false"></i>
									</a>							
								<?php if(!empty($xevso_slider['xevso_slider_video_text'])){
									echo '<span>'.esc_html($xevso_slider['xevso_slider_video_text']).'</span>';
								}?> 								
							</div>
	                    </div>
	                <?php endif; ?>
	                </div>
	            </div>
	        </div>
			<?php endforeach; ?>
	    </div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_slider_two_Widget );
