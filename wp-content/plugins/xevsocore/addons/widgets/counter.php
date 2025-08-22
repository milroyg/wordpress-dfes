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
class xevso_counter_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-counter';
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
		return esc_html__( 'xevso Counter', 'xevsocore' );
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
		return [ 'xevso', 'counter' ];
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
			'xevso_counter_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_counter_title',
			[
			    'label' => esc_html__( 'Title', 'xevsocore' ),
			    'type'    => Controls_Manager::TEXT,
			    'default' => esc_html__('Counter Title','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_counter_num',
			[
				'label' => esc_html__( 'Number', 'xevsocore' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'step' => 1,
				'default' => 50,
			]
		);
		$this->add_control(
			'xevso_counter_symbel',
			[
			    'label' => esc_html__( 'Symble', 'xevsocore' ),
			    'type'     => Controls_Manager::TEXT,
			    'default'  => esc_html__('+','xevsocore'),
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'xevso_counter_styles',
			[
				'label' => esc_html__( 'Style', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'xevso_counter_box_alignment',
			[
				'label' => esc_html__( 'Box Alignment', 'xevsocore' ),
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
					'{{WRAPPER}} .counter-box' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
				'toggle' => true,
			]
		);
		$this->add_responsive_control(
			'xevso_counter_box_padding',
			[
			    'label' => esc_html__( 'Box Padding', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'default' => [
				  'top' => '120px',
				  'right' => '0',
				  'bottom' => '120px',
				  'left' => '0',
				  'isLinked' => false
			    ],
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .counter-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_responsive_control(
			'xevso_counter_box_margin',
			[
			    'label' => esc_html__( 'Box Margin', 'xevsocore' ),
			    'type' => Controls_Manager::DIMENSIONS,
			    'size_units' => [ 'px', '%', 'em' ],
			    'selectors' => [
				  '{{WRAPPER}} .counter-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			    ],
			    'separator' =>'before',
			]
		); 
		$this->add_control(
			'coline0',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'xevso_counter_number_color',
			[
				'label' => esc_html__( 'Number Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .counter-count.timer' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => esc_html__( 'Title Typography', 'xevsocore' ),
			    	'name' => 'xevso_counter_number_typo',
			    	'selector' => '{{WRAPPER}} .counter-count.timer',
			]
		);  
		$this->add_control(
			'coline',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'xevso_counter_symble_color',
			[
				'label' => esc_html__( 'Title Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .counter-num label' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_counter_symble_typo',
			    'label' => esc_html__( 'Symble Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .counter-num label',
			]
		); 
		$this->add_control(
			'coline1',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'xevso_counter_title_color',
			[
				'label' => esc_html__( 'Symble Color', 'xevsocore' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .counter-content.couner-time h4' => 'color: {{VALUE}};',
				],
			]
		); 
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
			    'name' => 'xevso_counter_title_typo',
			    'label' => esc_html__( 'Symble Typography', 'xevsocore' ),
			    'selector' => '{{WRAPPER}} .counter-content.couner-time h4',
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
		echo '
			<script>
			jQuery(document).ready(function($) {
				 "use strict";
				$(".timer").countTo();
				$(".couner-time").appear(function() {
				    $(".timer").countTo();
				}, {
				    accY: -200
				});
			});
			</script>
		';
		?>
		<div class="counter-box">
			<div class="counter-content couner-time">
				<div class="counter-num">
				<div class="counter-count timer" data-to="<?php echo esc_attr($settings['xevso_counter_num']); ?>" data-speed="5000">
			<?php echo esc_html($settings['xevso_counter_num']); ?>
				</div>
				<?php if(!empty($settings['xevso_counter_symbel'])) : ?>
				<label><?php echo esc_html($settings['xevso_counter_symbel']) ?></label>
			<?php endif; ?>
				</div>
				<?php if(!empty($settings['xevso_counter_title'])){
					echo '<h4>'.esc_html($settings['xevso_counter_title']).'</h4>';
				}?>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_counter_Widget );