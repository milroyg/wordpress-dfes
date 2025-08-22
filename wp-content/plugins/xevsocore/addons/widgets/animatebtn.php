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
class hubsoft_animatebtn_Widget extends \Elementor\Widget_Base {

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
		return 'hubsoft-animatebtn';
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
		return esc_html__( 'Animate Button', 'xevsocore' );
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
		return [ 'hubsoft', 'animatebtn' ];
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
			'hubsoft_buttons_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'xevso_small_link',
			[
			    'label' => esc_html__( 'Link ', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => 'https://www.youtube.com/watch?v=f3NWvUV8MD8',
			]
		);
		
		$this->add_control(
			'xevso_aninti_titleicon',
			[
				'label' => __( 'Team Icon', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		
 
		$this->end_controls_section();
		
		
		
		
		$this->start_controls_section(
			'hubsoft_sliders_two_video_btns',
			[
				'label' => esc_html__( 'video Button', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
			]
		);
		$this->add_control(
			'hubsoft_sl_vwidth',
			[
				'label' => __( 'Width', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 250,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} a.slider-video:after' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'hubsoft_sl_vheight',
			[
				'label' => __( 'Height', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 250,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} a.slider-video:after' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'hubsoft_sl_vlheight',
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
			'hubsoft_sl_vicon_size',
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
		$this->add_control(
			'hubsoft_sl_vtest_size',
			[
				'label' => __( 'Text Size', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 17,
				],
				'selectors' => [
					'{{WRAPPER}} .slider-video-btn span' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'hubsoft_sl_vmleft',
			[
				'label' => __( 'Margin Left', 'xevsocore' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 13,
				],
				'selectors' => [
					'{{WRAPPER}} a.slider-video i' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs(
			'hubsoft_s2tabs'
		);

		$this->start_controls_tab(
			'hubsoft_s2_tab_normal',
			[
				'label' => __( 'Normal', 'xevsocore' ),
			]
		);
		$this->add_control(
			'hubsoft_s2nicon_color',
			[
				'label' => __( 'Icon Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.slider-video i:before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'hubsoft_s2ntext_color',
			[
				'label' => __( 'Text Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slider-video-btn span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'hubsoft_s2nbtn_glow_color',
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
				'name' => 'hubsoft_s2nbtn_bg_color',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} a.slider-video:after',
			]
		);
		$this->end_controls_tab();
		
		
		
		
		
		$this->start_controls_tab(
			'hubsoft_s2_tab_hover',
			[
				'label' => __( 'Hover', 'xevsocore' ),
			]
		);

		$this->add_control(
			'hubsoft_s2nicon_hcolor',
			[
				'label' => __( 'Icon hover Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.slider-video:hover i:before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'hubsoft_s2htext_color',
			[
				'label' => __( 'Text Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .slider-video-btn:hover span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'hubsoft_s2hbtn_glow_color',
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
				'name' => 'hubsoft_s2hbtn_bg_color',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} a.slider-video:hover:after',
			]
		);
		$this->add_control(
			'hubsoft_s2hbtn_radius',
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
		$settings = $this->get_settings_for_display();?>
		
		<div class="align-content-center">
			<div class="slider-videos ">
				<a href="<?php echo esc_html($settings['xevso_small_link']); ?>" class="slider-video">
					<i class="<?php echo esc_attr($settings['xevso_aninti_titleicon'] ['value']); ?>"></i>
				</a>
			</div>
		</div>
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
	<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new hubsoft_animatebtn_Widget );