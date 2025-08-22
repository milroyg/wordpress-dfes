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
class xevso_faq_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-animatebtn';
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
		return esc_html__( 'xevso FAQ', 'xevsocore' );
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
		return [ 'xevso', 'animatebtn' ];
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
			'xevso_buttons_section',
			[
				'label' => esc_html__( 'FAQ No-1', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'xevso_colleps_test',
			[
			    'label' => esc_html__( 'FAQ Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_colleps_des',
			[
			    'label' => esc_html__( 'FAQ Description', 'xevsocore' ),
			    'type'          => Controls_Manager::WYSIWYG,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		
		$this->end_controls_section();
		
		
		
		
		$this->start_controls_section(
			'xevso_buttons_section2',
			[
				'label' => esc_html__( 'FAQ No-2', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'xevso_colleps_test2',
			[
			    'label' => esc_html__( 'FAQ Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_colleps_des2',
			[
			    'label' => esc_html__( 'FAQ Description', 'xevsocore' ),
			    'type'          => Controls_Manager::WYSIWYG,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		
		$this->end_controls_section();
		
		
		
		
		$this->start_controls_section(
			'xevso_buttons_section3',
			[
				'label' => esc_html__( 'FAQ No-3', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'xevso_colleps_test3',
			[
			    'label' => esc_html__( 'FAQ Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_colleps_des3',
			[
			    'label' => esc_html__( 'FAQ Description', 'xevsocore' ),
			    'type'          => Controls_Manager::WYSIWYG,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		
		$this->end_controls_section();
		
		
		
		
			
		$this->start_controls_section(
			'xevso_buttons_section4',
			[
				'label' => esc_html__( 'FAQ No-3', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'xevso_colleps_test4',
			[
			    'label' => esc_html__( 'FAQ Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Check Our','xevsocore')
			]
		);
		
		$this->add_control(
			'xevso_colleps_des4',
			[
			    'label' => esc_html__( 'FAQ Description', 'xevsocore' ),
			    'type'          => Controls_Manager::WYSIWYG,
			    'default'       => esc_html__('Check Our Protfolo','xevsocore')
			]
		);
		
		
		$this->end_controls_section();
		
		
		
		
		
		
		$this->start_controls_section(
			'xevso_sliders_two_video_btns',
			[
				'label' => esc_html__( 'FAQ Area', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_allre_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .collapes',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_button_title',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .collapes .collapsible',
			]
		);
		
		$this->add_control(
			'xevso_s2nicon_color',
			[
				'label' => __( 'FAQ Title Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collapes .collapsible' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_responsive_control(
			'xevso_buttons_title_padding',
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
					'{{WRAPPER}} .collapes .collapsible' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'xevso_s2ntext_color',
			[
				'label' => __( 'Text Color', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .collapes .content p' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'xevso_buttons_three_title',
				'label' => esc_html__( 'Typography', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .collapes .content p',
			]
		);
		
		$this->add_responsive_control(
			'xevso_buttons_con_padding',
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
					'{{WRAPPER}} .collapes .content p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'xevso_buttons_three_hborder',
				'label' => esc_html__( 'Border', 'xevsocore' ),
				'selector' => '{{WRAPPER}} .collapsible',
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_s2nbtn_bg_color',
				'label' => __( 'Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .collapes .collapsible',
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'xevso_activ_bg_color',
				'label' => __( 'Active Background', 'xevsocore' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .active, .collapsible:hover ',
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
		$settings = $this->get_settings_for_display();
        
		?>
		<div class="collapes"> 
			<button class="collapsible"><?php echo esc_html__($settings['xevso_colleps_test']); ?></button>
			<div class="content">
			  <p><?php echo esc_html__($settings['xevso_colleps_des']); ?></p>
			</div>
			
			
			
			<button class="collapsible"><?php echo esc_html__($settings['xevso_colleps_test2']); ?></button>
			<div class="content">
			  <p><?php echo esc_html__($settings['xevso_colleps_des2']); ?></p>
			</div>
			
			
			<button class="collapsible"><?php echo esc_html__($settings['xevso_colleps_test3']); ?></button>
			<div class="content">
			  <p><?php echo esc_html__($settings['xevso_colleps_des3']); ?></p>
			</div>
			
			
			<button class="collapsible"><?php echo esc_html__($settings['xevso_colleps_test4']); ?></button>
			<div class="content">
			  <p><?php echo esc_html__($settings['xevso_colleps_des4']); ?></p>
			</div>
		</div>	
		<script>
			var coll = document.getElementsByClassName("collapsible");
			var i;

			for (i = 0; i < coll.length; i++) {
			  coll[i].addEventListener("click", function() {
				this.classList.toggle("active");
				var content = this.nextElementSibling;
				if (content.style.maxHeight){
				  content.style.maxHeight = null;
				} else {
				  content.style.maxHeight = content.scrollHeight + "px";
				} 
			  });
			}
		</script>
		
	<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_faq_Widget );