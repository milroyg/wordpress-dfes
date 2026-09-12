<?php
/**
 * PSACP Elementor Widget Class
 *
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists('PSAC_Layout_Elementor_Widget') ) {

	class PSAC_Layout_Elementor_Widget extends \Elementor\Widget_Base {

		public function get_name() {
			return 'psacp_layout_elementor_widget';
		}

		public function get_title() {
			return esc_html__( 'Post Slider and Carousel', 'post-slider-and-carousel' );
		}

		public function get_icon() {
			return 'eicon-post-list';
		}

		public function get_categories() {
			return [ 'general' ];
		}

		public function get_keywords() {
			return [ 'psacp', 'slider', 'carousel', 'post', 'post type slider', 'post type carousel', 'post slider', 'post carousel', 'post gridbox slider' ];
		}

		protected function register_controls() {

			// Taking some variables
			$add_layout_url = add_query_arg( array('page' => 'psacp-layout', 'shortcode' => 'psac_post_slider', 'action' => 'add'), admin_url('admin.php') );

			// Content Tab Start
			$this->start_controls_section(
				'section_title',
				[
					'label' => esc_html__( 'Choose Layout', 'post-slider-and-carousel' ),
					'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'psac_layout_id',
				[
					'label'			=> esc_html__( 'Layout', 'post-slider-and-carousel' ),
					'label_block'	=> true,
					'description'	=> sprintf( esc_html__( 'Choose your created layout by name or id. You can create the layout from %shere%s.', 'post-slider-and-carousel' ), '<a href="'.esc_url( $add_layout_url ).'" target="_black">', '</a>' ),
					'type'			=> 'psacp-select2-ajax-control',
					'multiple'		=> false,
					'options'		=> '',
					'default'		=> '',
				]
			);

			$this->add_control(
				'psac_layout_preview',
				[
					'label'			=> esc_html__( 'Preview', 'post-slider-and-carousel' ),
					'type'			=> \Elementor\Controls_Manager::SWITCHER,
					'label_off'		=> esc_html__( 'Hide', 'post-slider-and-carousel' ),
					'label_on'		=> esc_html__( 'Show', 'post-slider-and-carousel' ),
					'default'		=> 'yes',
					'description'	=> esc_html__( 'Enable layout preview in editor mode.', 'post-slider-and-carousel' ),
				]
			);

			$this->end_controls_section();
			// Content Tab End
		}

		/**
		 * Frontend Output
		 */
		protected function render() {

			$settings = $this->get_settings_for_display();

			// Render Shortcode
			echo do_shortcode('[psacp_tmpl layout_id="'.esc_attr( $settings['psac_layout_id'] ).'" psac_layout_preview="'.esc_attr( $settings['psac_layout_preview'] ).'"]');
		}
	}
}