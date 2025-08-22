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
class xevso_testimonial_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-testimonial';
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
		return esc_html__( 'xevso Testimonial', 'xevsocore' );
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
		return [ 'xevso', 'testimonial' ];
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
			'xevso_testimonial_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $this->add_control(
            'xevso_testimonial_style',
            [
                'label' => esc_html__( 'Select Style', 'xevsocore' ),
                'type' => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1'  => esc_html__( 'Style One', 'xevsocore' ),
                    '2'  => esc_html__( 'Style Two', 'xevsocore' ),
                ],
            ]
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'xevso_testi_img',
            [
                'label' => esc_html__('Image','xevsocore'),
                'type'=>Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
            'xevso_testi_title', [
                'label' => esc_html__( 'Title', 'xevsocore' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Salina Smith' , 'xevsocore' ),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'xevso_testi_stitle', [
                'label' => esc_html__( 'Sub Title', 'xevsocore' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'CEO & Founder' , 'xevsocore' ),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'xevso_testi_content', [
                'label' => esc_html__( 'Content', 'xevsocore' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__( 'At vero eos et accusamus et iusto odio ducimus qui praesentium voluptatum demtor eos et accus accus leniti atque corrupti quos dolores. excepturi sint occaecati cupiditate non provident,  similique sunt in culpa qui officia deserunt mollitia. ' , 'xevsocore' ),
                'show_label' => false,
            ]
        );
        $repeater->add_control(
            'xevso_testi_ratting',
            [
                'label' => __( 'Alignment', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => esc_html__( '1', 'xevsocore' ),
                    ],
                    '2' => [
                        'title' => esc_html__( '2', 'xevsocore' ),
                    ],
                    '3' => [
                        'title' => esc_html__( '3', 'xevsocore' ),
                    ],
                    '4' => [
                        'title' => esc_html__( '4', 'xevsocore' ),
                    ],
                    '5' => [
                        'title' => esc_html__( '5', 'xevsocore' ),
                    ],
                ],
                'default' => '5',
                'toggle' => false,
            ]
        );
        $repeater->add_control(
            'xevso_testi_review', [
                'label' => esc_html__( 'Review Number', 'xevsocore' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( '7' , 'xevsocore' ),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'xevso_testi_slides',
            [
                'label' => esc_html__( 'Repeater List', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'xevso_testi_title' => esc_html__( 'Salina Smith', 'xevsocore' ),
                        'xevso_testi_stitle' => esc_html__( 'CEO & Founder', 'xevsocore' ),
                        'xevso_testi_content' => esc_html__( 'At vero eos et accusamus et iusto odio ducimus qui praesentium voluptatum demtor eos et accus accus leniti atque corrupti quos dolores. excepturi sint occaecati cupiditate non provident,  similique sunt in culpa qui officia deserunt mollitia. ', 'xevsocore' ),
                        'xevso_testi_ratting' => 5,
                        'xevso_testi_review' => 7,
                    ],
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_testi_slide_setting',
            [
                'label' => esc_html__( 'Slide Setting', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'xevso_testi_slide',
            [
                'label' => esc_html__( 'Enable Slide ?', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'xevso_testi_nav',
            [
                'label' => esc_html__( 'Enable Nav', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'xevso_testi_slide' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'xevso_testi_aplay',
            [
                'label' => esc_html__( 'Enable Auto Play', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'xevso_testi_slide' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'xevso_testi_aspeed',
            [
                'label' 	=> esc_html__( 'Slide auto Speed', 'xevsocore' ),
                'type' 	=> Controls_Manager::NUMBER,
                'min' 	=> 500,
                'max' 	=> 5000,
                'step' 	=> 50,
                'default' 	=> 1500,
                'condition' => array(
                    'xevso_testi_slide' => 'yes',
                    'xevso_testi_aplay' => 'yes',

                )
            ]
        );
        $this->add_control(
            'xevso_testi_speed_enable',
            [
                'label' => esc_html__( 'Enable Speed', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'xevso_testi_slide' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'xevso_testi_speed',
            [
                'label' 	=> esc_html__( 'Slide Speed', 'xevsocore' ),
                'type' 	=> Controls_Manager::NUMBER,
                'min' 	=> 500,
                'max' 	=> 5000,
                'step' 	=> 50,
                'default' 	=> 1500,
                'condition' => array(
                    'xevso_testi_slide' => 'yes',
                    'xevso_testi_speed_enable' => 'yes',

                )
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_testi_box_style',
            [
                'label' => esc_html__( 'box Style', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs('xevso_testi_tabs');
        $this->start_controls_tab(
			'xevso_testi_tab',
			[
				'label' => esc_html__( 'Normal', 'xevsocore' ),
			]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_testi_box_bg',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient' ],
                'default' =>'#ffffff',
                'selector' => '{{WRAPPER}} .testi-single',
            ]
        );

        $this->add_responsive_control(
			'xevso_testi_box_radius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
                    ],
                    '%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .testi-single' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_testi_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .testi-single',
			]
        );
        $this->add_responsive_control(
			'xevso_testi_box_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .testi-single' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_testi_box_margin',
			[
			    'label' => esc_html__( 'Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .testi-single' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'xevso_ty_cbox_border',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .testi-single',
			]
		);
        $this->end_controls_tab();
        $this->start_controls_tab(
			'xevso_testi_tabh',
			[
				'label' => esc_html__( 'Hover', 'xevsocore' ),
			]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_testi_box_hbg',
                'label' => esc_html__( 'Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .testi-single',
            ]
        );
        $this->add_responsive_control(
			'xevso_testi_box_radiush',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
                    ],
                    '%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .testi-single' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_testi_boxh_shadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .testi-single',
			]
		);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_testi_img_style',
            [
                'label' => esc_html__( 'Image Style', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
			'xevso_testi_img_width',
			[
				'label' => esc_html__( 'Width', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 70,
						'max' => 150,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 70,
				],
				'selectors' => [
					'{{WRAPPER}} .testi-top .left .thumb img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
        );
        $this->add_responsive_control(
			'xevso_testi_img_height',
			[
				'label' => esc_html__( 'height', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 70,
						'max' => 150,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 70,
				],
				'selectors' => [
					'{{WRAPPER}} .testi-top .left .thumb img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
        );
		
		$this->add_responsive_control(
			'xevso_testhmp_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .testi-top .left .thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		
        $this->add_responsive_control(
			'xevso_testi_img_radius',
			[
				'label' => esc_html__( 'Border Radius', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .testi-top .left img' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'xevso_testi_img_shadow',
				'label' => esc_html__( 'Box Shadow', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .testi-top .left img',
			]
		);
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_testi_title_style',
            [
                'label' => esc_html__( 'Title Style', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_testi_title_typo',
                'label' => esc_html__( 'Title Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .testi-top .left .namer h2',
            ]
        );
        $this->add_control(
            'xevso_testi_title_color',
            [
                'label' => esc_html__( 'Title color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .testi-top .left .namer h2' => 'color: {{VALUE}};',
                ],
            ]
        );
        
         $this->add_responsive_control(
			'xevso_testi_titne_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .testi-top .left .namer h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			]
		); 
        $this->add_control(
            'testi_line',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_testi_stitle_typo',
                'label' => esc_html__( 'Small Title Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .testi-top .left .namer h4',
            ]
        );
        $this->add_control(
            'xevso_testi_stitle_color',
            [
                'label' => esc_html__( 'Small Title color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#798795',
                'selectors' => [
                    '{{WRAPPER}} .testi-top .left .namer h4' => 'color: {{VALUE}};',
                ],
            ]
        );
        
            
        $this->add_responsive_control(
			'xevso_testi_contre_padding',
			[
			    'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .testi-top .left .namer h4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
        
        $this->end_controls_section();
        $this->start_controls_section(
            'xevso_testi_content_style',
            [
                'label' => esc_html__( 'Content Style', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_testi_content_typo',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .testi-content p',
            ]
        );
        $this->add_control(
            'xevso_testi_dec_color',
            [
                'label' => esc_html__( 'color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .testi-content p' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        
        
        $this->add_control(
            'xevso_testi_cicon_color',
            [
                'label' => esc_html__( 'Content Lable color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0b5be0',
                'selectors' => [
                    '{{WRAPPER}} .xevso-tes-ratting label' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_testi_lale_typo',
                'label' => esc_html__( 'Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .xevso-tes-ratting label',
            ]
        ); 
        
        $this->add_responsive_control(
			'xevso_testi_cicon_size',
			[
				'label' => esc_html__( 'Icon Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 50,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 35,
				],
				'selectors' => [
					'{{WRAPPER}} .testi-content p:before,.testi-content p:after' => 'font-size: {{SIZE}}{{UNIT}};',
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
        $dynamic_num = rand(35245545, 541541745);
        if($settings['xevso_testi_slide'] == 'yes'){
            if($settings['xevso_testi_aplay'] == 'yes' ){
                $aplay = 'true';
            }else{
                $aplay = 'false';
            }
            if($settings['xevso_testi_nav'] == 'yes' ){
                $nav = 'true';
            }else{
                $nav = 'false';
            }
            if($settings['xevso_testimonial_style'] == '1'){
                $items = '2';
            }elseif($settings['xevso_testimonial_style'] == '2'){
                $items = '3';
            }
            echo '
			<script>
			jQuery(document).ready(function($) {
				"use strict";
				$("#testi-'.esc_attr($dynamic_num).'").slick({
					autoplay:'.esc_attr($aplay).',
					arrows:'.esc_attr($nav).',
					dots:true,
					slidesToShow:'.esc_attr($items).',
					slidesToScroll:1,';
                if(!empty($settings['xevso_testi_aspeed'])){
                    echo 'autoplaySpeed:'.esc_attr($settings['xevso_testi_aspeed']).',';
                }
                if(!empty($settings['xevso_testi_speed'])){
                    echo 'speed:'.esc_attr($settings['xevso_testi_speed']).',';
                }
                if($settings['xevso_testimonial_style'] == '1'){
                echo 'responsive: [
						{
						    breakpoint: 1025,
							settings: {
								slidesToShow: 3,
								slidesToScroll: 1,
							}
                        },
                        {
							breakpoint: 991,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 2
							}
						},
						{
							breakpoint: 600,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 2
							}
						},
						{
							breakpoint: 480,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1
							}
						}
                    ]';
                }
					
                echo '
				});
			});
			</script>';
        }
		?>
		<div class="testimonial-boxs <?php if($settings['xevso_testimonial_style'] == '1') : ?>style-one<?php else : ?>style-two<?php endif; ?>">
		<div class="testimonial-box">
        <div class="testimonial-items <?php if($settings['xevso_testi_slide'] ==! 'yes') : ?>row<?php endif; ?>" id="testi-<?php echo esc_attr($dynamic_num); ?>">
                    <?php foreach ($settings['xevso_testi_slides'] as $xevso_testi_slide) : ?>
                    <div class="item <?php if($settings['xevso_testi_slide'] ==! 'yes') : ?> no-slide<?php endif; ?>">
                        <div class="testi-single">
                            <div class="testi-top">
                                <div class="left">
									<div class="thumb"> 
                                    <?php
                                    if(!empty($xevso_testi_slide['xevso_testi_img'])){
                                         echo wp_get_attachment_image( $xevso_testi_slide['xevso_testi_img']['id'], 'full' );
                                    } ?>
									</div>
									<div class="namer"> 
									<h2><?php echo esc_html($xevso_testi_slide['xevso_testi_title']); ?></h2>
                                    <h4><?php echo esc_html($xevso_testi_slide['xevso_testi_stitle']); ?></h4>
									</div>
								</div>
                                <div class="right btm">
									<div class="testi-content">
									   <p><?php echo wp_kses_post($xevso_testi_slide['xevso_testi_content']); ?></p>
									</div>
                                </div>
                                <div class="rate">
                                    <span> <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i> <span> // Nice Tour </span></span>
                                </div>
                            </div>
							
                        </div>
                    </div>
                     <?php endforeach; ?>
                </div>
            </div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_testimonial_Widget );