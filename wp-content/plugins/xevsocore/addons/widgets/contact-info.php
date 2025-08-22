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
class xevso_coninfo_Widget extends \Elementor\Widget_Base {

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
		return 'xevso-contact-info';
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
		return esc_html__( 'xevso contact Info', 'xevsocore' );
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
		return [ 'xevso', 'Contact info' ];
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
			'xevso_coninfo_section',
			[
				'label' => esc_html__( 'Content', 'xevsocore' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'xevso_coninfo_title',
			[
			    'label' => esc_html__( 'Title', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXT,
			    'default'       => esc_html__('Contact Info','xevsocore'),
			]
		);
		$this->add_control(
			'xevso_coninfo_hcontent',
			[
			    'label' => esc_html__( 'Content', 'xevsocore' ),
			    'type' => Controls_Manager::TEXTAREA,
			    'default' => esc_html__( 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.','xevsocore' )
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'xevso_coninfo_list_contnet',
			[
			    'label' => esc_html__( 'Content', 'xevsocore' ),
			    'type'          => Controls_Manager::TEXTAREA,
			    'default'       => esc_html__('Dambo Dika US. Road 123','xevsocore'),
			]
		);
		$repeater->add_control(
			'xevso_coninfo_icon',
			[
			'label' => esc_html__( 'Icon', 'xevsocore' ),
			'type' => Controls_Manager::ICONS,
			'label_block' => true,
			]
		);
		$this->add_control(
			'xevso_coninfo_slides',
				[
				'label' => esc_html__( 'Repeater List', 'xevsocore' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'xevso_coninfo_list_contnet' => esc_html__( 'Dambo Dika US. Road 123', 'xevsocore' ),
						'xevso_coninfo_icon' => '',
					],
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
		
		?>
		<div class="coninfo-boxs">
			<div class="coninfo-title">
				<h2><?php echo esc_html($settings['xevso_coninfo_title']) ?></h2>
			</div>
			<div class="coninfo-dec">
				<?php echo esc_html($settings['xevso_coninfo_hcontent']); ?>
			</div>
			<div class="contact-info-list">
				<ul>
				 <?php foreach ($settings['xevso_coninfo_slides'] as $xevso_coninfo_slide) : ?>
					<li><i class="<?php echo esc_attr($xevso_coninfo_slide['xevso_coninfo_icon']['value']); ?>"></i> <label><?php echo esc_html($xevso_coninfo_slide['xevso_coninfo_list_contnet']); ?></label></li>
				 <?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new xevso_coninfo_Widget );