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
class xevso_team_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-team';
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
		return esc_html__( 'xevso Team', 'xevsocore' );
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
		return [ 'xevso', 'team' ];
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
			'xevso_team_section',
			[
				'label' => esc_html__( 'Configaration', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $this->add_control(
            'show_team_enable_slide',
            [
                'label' => esc_html__( 'Enable Slide ?', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'xevsocore' ),
                'label_off' => esc_html__( 'No', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'xevso_team_nav',
            [
                'label' => esc_html__( 'Enable Nav', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_team_enable_slide' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'xevso_team_aplay',
            [
                'label' => esc_html__( 'Enable Auto Play', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_team_enable_slide' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'xevso_team_aspeed_enable',
            [
                'label' => esc_html__( 'Enable Auto Play Speed', 'xevsocore' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'xevsocore' ),
                'label_off' => esc_html__( 'Hide', 'xevsocore' ),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'show_team_enable_slide' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'xevso_team_aspeed',
            [
                'label' 	=> esc_html__( 'Slide auto Speed', 'xevsocore' ),
                'type' 	=> Controls_Manager::NUMBER,
                'min' 	=> 500,
                'max' 	=> 5000,
                'step' 	=> 50,
                'default' 	=> 1500,
                'condition' => array(
                    'show_team_enable_slide' => 'yes',
                    'xevso_team_aspeed_enable' => 'yes',

                )
            ]
        );
        $this->add_control(
            'xevso_team_speed',
            [
                'label' 	=> esc_html__( 'Slide Speed', 'xevsocore' ),
                'type' 	=> Controls_Manager::NUMBER,
                'min' 	=> 500,
                'max' 	=> 5000,
                'step' 	=> 50,
                'default' 	=> 1500,
                'condition' => array(
                    'show_team_enable_slide' => 'yes',

                )
            ]
        );
        $this->add_control(
            'xevso_team_showitems',
            [
                'label' 	=> esc_html__( 'Slide items', 'xevsocore' ),
                'type' 	=> Controls_Manager::NUMBER,
                'min' 	=> -1,
                'max' 	=> 50,
                'step' 	=> 1,
                'default' 	=> -1,
            ]
        );
		$this->end_controls_section();
        $this->start_controls_section(
            'xevso_team_title_style',
            [
                'label' => esc_html__( 'Team Title', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_team_title_typo',
                'label' => esc_html__( 'Title Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .team-content h2',
            ]
        );
		$this->add_responsive_control(

			'xevso_counter_tep_numer_padding',

			[

			    'label' => esc_html__( 'Padding', 'xevsocore' ),

			    'type' => Controls_Manager::DIMENSIONS,

			    'size_units' => [ 'px', '%', 'em' ],

			    'selectors' => [

				  '{{WRAPPER}} .team-content h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

			    ],

			    'separator' =>'before',

			]

		);
        $this->add_control(
            'xevso_team_title_color',
            [
                'label' => esc_html__( 'Title color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .team-content h2 a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'xevso_team_title_hcolor',
            [
                'label' => esc_html__( 'Title Hover color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0b5be0',
                'selectors' => [
                    '{{WRAPPER}} .team-content h2 a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'hr',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'xevso_team_note',
            [
                'label' => __( '<strong> Small Title Options</strong>', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'xevso_team_stitle_typo',
                'label' => esc_html__( 'Small Title Typography', 'xevsocore' ),
                'selector' => '{{WRAPPER}} .team-content h4',
            ]
        );
        $this->add_control(
            'xevso_team_stitle_color',
            [
                'label' => esc_html__( 'Small Title color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#798795',
                'selectors' => [
                    '{{WRAPPER}} .team-content h4' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->end_controls_section();
        $this->start_controls_section(
            'xevso_team_social_style',
            [
                'label' => esc_html__( 'Team Social', 'xevsocore' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'xevso_team_sicon_size',
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
                    '{{WRAPPER}}, {{WRAPPER}} .team-social ul li a' => 'font-size: {{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_responsive_control(
            'xevso_team_sicon_radius',
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
                    '{{WRAPPER}} .team-social ul li a' => 'border-radius: {{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_control(
            'xevso_team_sicon_color',
            [
                'label' => esc_html__( 'Icon color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#0b5be0',
                'selectors' => [
                    '{{WRAPPER}} .team-social ul li a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_team_sicon_bd',
                'label' => esc_html__( 'Icon Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .team-social ul li a',
            ]
        );
        $this->add_control(
            'hr2',
            [
                'type' => \Elementor\Controls_Manager::DIVIDER,
            ]
        );
        $this->add_control(
            'xevso_team_note2',
            [
                'label' => __( '<strong>Icon Hover box Options</strong>', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_team_siconbox_bd',
                'label' => esc_html__( 'box Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient'],
                'default' => 'rgba(11, 91, 224, 0.58)',
                'selector' => '{{WRAPPER}} .team-social',
            ]
        );
        $this->add_responsive_control(
            'xevso_team_siconbox_padding',
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
            'xevso_team_note1',
            [
                'label' => __( '<strong>Icon Hover  Options</strong>', 'xevsocore' ),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
            ]
        );
        $this->add_control(
            'xevso_team_sicon_hcolor',
            [
                'label' => esc_html__( 'Icon Hover color', 'xevsocore' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .team-social ul li a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'xevso_team_sicon_hbd',
                'label' => esc_html__( 'Icon Background', 'xevsocore' ),
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .team-social ul li a:hover',
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
        if($settings['show_team_enable_slide'] == 'yes' ){
            if($settings['xevso_team_aplay'] == 'yes' ){
                $aplay = 'true';
            }else{
                $aplay = 'false';
            }
            if($settings['xevso_team_nav'] == 'yes' ){
                $nav = 'true';
            }else{
                $nav = 'false';
            }
            echo '
			<script>
			jQuery(document).ready(function($) {
				"use strict";
				$("#team-'.esc_attr($dynamic_num).'").slick({
					autoplay:'.esc_attr($aplay).',
					arrows:'.esc_attr($nav).',
					slidesToShow:3,
					slidesToScroll:1,';
                  if(!empty($settings['xevso_team_aspeed'])){
                        echo 'autoplaySpeed:'.esc_attr($settings['xevso_team_aspeed']).',';
                    }
                    if(!empty($settings['xevso_team_speed'])){
                        echo 'speed:'.esc_attr($settings['xevso_team_speed']).',';
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
							breakpoint: 600,
							settings: {
								slidesToShow: 2,
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
					]
				});
			});
			</script>';
        }
		?>
		<div class="team-boxs">
			<div class="team-box">
                <div class="team-items row" id="team-<?php echo esc_attr($dynamic_num); ?>">
                 <?php  global $post;
                 $p = new \WP_Query(array('posts_per_page' => $settings['xevso_team_showitems'], 'post_type' => 'team' ));
                 while($p->have_posts()) : $p->the_post();
                    $xevso_idd = get_the_ID();
                    $xevso_team_meta = get_post_meta($xevso_idd, 'xevso_teammeta', true);
                    ?>
                    <div class="item <?php if( empty( $settings['show_team_enable_slide'] == 'yes' ) ) : ?>col-12 col-md-4 col-lg-4 col-xl-4 no-slide<?php endif; ?>">
                        <div class="team-single">
                            <div class="team-image">
                                <?php the_post_thumbnail(); ?>
                                
                            </div>
                            <div class="team-content">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <?php if(!empty($xevso_team_meta['xevso_team_stitle'])) : ?>
                                <h4><?php echo esc_html($xevso_team_meta['xevso_team_stitle']) ?></h4>
                                <?php endif; ?>
                            </div>
							
							<div class="team-social">
                                    <ul>
                                        <?php foreach($xevso_team_meta['xevso_team_socials'] as $xevso_team_two_social ) : ?>
                                            <li><a  data-placement="right" data-original-title="<?php echo esc_attr($xevso_team_two_social['xevso_team_social_label']) ?>" href="<?php echo esc_url($xevso_team_two_social['xevso_team_social_url']) ?>"><span class="<?php echo esc_attr($xevso_team_two_social['xevso_team_social_icon']) ?>"></span></a></li>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                        </div>
                    </div>
                 <?php endwhile; wp_reset_query(); ?>
                </div>
            </div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_team_Widget );