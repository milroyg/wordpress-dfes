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
class xevso_team_three_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-team-three';
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
		return esc_html__( 'xevso Team Three', 'xevsocore' );
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
		return [ 'xevso', 'team three' ];
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
			'xevso_teamth_section',
			[
				'label' => esc_html__( 'Team', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_team_ima',
			[
			    'label' => esc_html__( 'Team Image', 'xevsocore' ),
			    'type' => Controls_Manager::MEDIA,
			]
		);
	
		$this->add_control(
			'teamth_icon',
			[
				'label' => __( 'Team Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		
	    $this->add_control(
			'xevso_teamth_icon_link',
			[
			    'label'		=> esc_html__( 'Icon Link', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => esc_html__('#','xevsocore'),
			]
		);
		
		
		
		$this->add_control(
			'teamth_icon1',
			[
				'label' => __( 'Team Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		
	    $this->add_control(
			'xevso_teamth_icon_link1',
			[
			    'label'		=> esc_html__( 'Icon Link', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => esc_html__('#','xevsocore'),
			]
		);
		
		$this->add_control(
			'teamth_icon2',
			[
				'label' => __( 'Team Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		
	    $this->add_control(
			'xevso_teamth_icon_link2',
			[
			    'label'		=> esc_html__( 'Icon Link', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => esc_html__('#','xevsocore'),
			]
		);
		
		
		$this->add_control(
			'teamth_icon3',
			[
				'label' => __( 'Team Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		
	    $this->add_control(
			'xevso_teamth_icon_link3',
			[
			    'label'		=> esc_html__( 'Icon Link', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => esc_html__('#','xevsocore'),
			]
		);
		

     $this->end_controls_section();
        $this->start_controls_section(
            'xevso_teafmth_setting',
            [
                'label' => esc_html__( 'Content Setting', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
    
		$this->add_control(
			'xevso_teamth_title',
			[
			    'label'		=> esc_html__( 'Title Here', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => esc_html__('Md Jahid Hasan','xevsocore'),
			    'placeholder' => esc_html__( 'Service Title', 'xevsocore' ),
			]
		);
		
		$this->add_control(
			'xevso_teamth_title_link',
			[
			    'label'		=> esc_html__( 'Title Link', 'xevsocore' ),
			    'type'        => Controls_Manager::TEXT,
			    'default'     => esc_html__('#','xevsocore'),
			]
		);
	
		
		$this->add_control(
			'xevso_teamth_des',
			[
			    'label' => esc_html__( 'Description Here', 'xevsocore' ),
			    'type' => Controls_Manager::TEXTAREA,
			    'default' => esc_html__( 'Lorem ipsum dolor sit amet, consecte adipiscing elit Morbi vitae.','xevsocore' ),
			]
		);
		$this->end_controls_section();
		
		
		
        $this->start_controls_section(
            'xevso_team_two_title_style',
            [
                'label' => esc_html__( 'Team Title', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_team_threbd',
                'label' => esc_html__( 'Team Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .card_area .card-content',
            ]
        );
        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_car_two_title_typo',
                'label' => esc_html__( 'Title Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .card-content .pas',
            ]
        );
        $this->add_control(
            'xevso_team_two_title_color',
            [
                'label' => esc_html__( 'Title color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .card-content .pas' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        
        $this->add_control(
            'xevso_team_two_title_hcolor',
            [
                'label' => esc_html__( 'Title Hover color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0b5be0',
                'selectors' => [
                    '{{WRAPPER}} .card-content .pas:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
       
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_team_two_stitle_typo',
                'label' => esc_html__( 'Small Title Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .card-content h5',
            ]
        );
        $this->add_control(
            'xevso_team_two_stitle_color',
            [
                'label' => esc_html__( 'Small Title color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#798795',
                'selectors' => [
                    '{{WRAPPER}} .card-content h5' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'xevso_team_thx_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '36',
                    'right' => '0',
                    'bottom' => '36',
                    'left' => '0',
                    'isLinked' => false
                ],
                'selectors' => [
                    '{{WRAPPER}} .card-content h5' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        
		$this->end_controls_section();
        $this->start_controls_section(
            'xevso_team_two_social_style',
            [
                'label' => esc_html__( 'Team Social', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'xevso_team_two_sicon_size',
            [
                'label' => esc_html__( 'Icon SIze', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 14,
                        'max' => 30,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}, {{WRAPPER}} .card-content .soci i' => 'font-size: {{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_team_two_sicon_radius',
            [
                'label' => esc_html__( 'Icon Radius', 'xevsocore' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .card-content .soci i' => 'border-radius: {{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_control(
            'xevso_team_two_sicon_color',
            [
                'label' => esc_html__( 'Icon color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0b5be0',
                'selectors' => [
                    '{{WRAPPER}} .card-content .soci i' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'xevso_team_thx_so',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '36',
                    'right' => '0',
                    'bottom' => '36',
                    'left' => '0',
                    'isLinked' => false
                ],
                'selectors' => [
                    '{{WRAPPER}} .card-content .soci' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_team_two_sicon_bd',
                'label' => esc_html__( 'Icon Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .card-content .soci i',
            ]
        );
       
        $this->add_responsive_control(
            'xevso_team_two_siconbox_padding',
            [
                'label' => esc_html__( 'Padding', 'xevsocore' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => '36',
                    'right' => '0',
                    'bottom' => '36',
                    'left' => '0',
                    'isLinked' => false
                ],
                'selectors' => [
                    '{{WRAPPER}} .team-social' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        $this->add_control(
            'hr1',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'xevso_team_two_note1',
            [
                'label' => __( '<strong>Icon Hover  Options</strong>', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );
        $this->add_control(
            'xevso_team_two_sicon_hcolor',
            [
                'label' => esc_html__( 'Icon Hover color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .card-content .soci i:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_team_two_sicon_hbd',
                'label' => esc_html__( 'Icon Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .card-content .soci i:hover',
            ]
        );
        
         $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_hover_threbd',
                'label' => esc_html__( 'Background Hover ', 'xevsocore' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .card_area .card-content:hover',
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
			<div class="card_area"> 
				<div class="card_thumb"> 
					<img src="<?php echo esc_html($settings['xevso_team_ima']['url']);?>" alt="" />
				</div>
				<div class="card-content">
					<div class="soci"> 
						<a href="<?php echo esc_html($settings['xevso_teamth_title_link']); ?>"><i class="<?php echo esc_html($settings['teamth_icon']['value']);?>"></i></a>
						<a href="<?php echo esc_html($settings['xevso_teamth_icon_link1']); ?>"><i class="<?php echo esc_html($settings['teamth_icon1']['value']);?>"></i></a>
						<a href="<?php echo esc_html($settings['xevso_teamth_icon_link2']); ?>"><i class="<?php echo esc_html($settings['teamth_icon2']['value']);?>"></i></a>
						<a href="<?php echo esc_html($settings['xevso_teamth_icon_link3']); ?>"><i class="<?php echo esc_html($settings['teamth_icon3']['value']);?>"></i></a>
					</div>
					<a href="<?php echo esc_html($settings['xevso_teamth_title_link']); ?>" class="pas"><?php echo esc_html($settings['xevso_teamth_title']); ?></a>
					<h5><?php echo ($settings['xevso_teamth_des']); ?></h5>
				</div>
			</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_team_three_Widget );