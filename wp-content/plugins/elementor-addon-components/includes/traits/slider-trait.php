<?php
namespace EACCustomWidgets\Includes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

trait Slider_Trait {

	/** Les contrôles du slider */
	protected function register_slider_content_controls() {

		$this->add_control(
			'slider_autoplay',
			array(
				'label'   => esc_html__( 'Autoplay', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'no',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'slider_delay',
			array(
				'label'     => esc_html__( 'Autoplay speed (ms)', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 6000,
				'step'      => 500,
				'default'   => 2000,
				'condition' => array( 'slider_autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_loop',
			array(
				'label'     => esc_html__( 'Loop', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'yes',
				'toggle'    => false,
				'condition' => array( 'slider_autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_images_number',
			array(
				'label'     => esc_html__( 'Slides displayed', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 30,
				'step'      => 1,
				'default'   => 3,
				'condition' => array(
					'slider_effect!' => array( 'creative', 'fade' ),
				),
			)
		);

		$this->add_control(
			'slider_images_number_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'The number of slides displayed must be less than or equal to twice the total number of slides', 'eac-components' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array(
					'slider_autoplay' => 'yes',
					'slider_loop'     => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_images_centered',
			array(
				'label'     => esc_html__( 'Slide centered', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'no',
				'toggle'    => false,
				'condition' => array(
					'slider_autoplay' => 'yes',
					'slider_effect!'  => array( 'creative', 'fade' ),
				),
			)
		);

		$start = is_rtl() ? 'end' : 'start';
		$end   = is_rtl() ? 'start' : 'end';
		$this->add_control(
			'slider_rtl',
			array(
				'label'        => esc_html__( 'Display direction', 'eac-components' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'  => array(
						'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
						'icon'  => "eicon-order-{$start}",
					),
					'right' => array(
						'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
						'icon'  => "eicon-order-{$end}",
					),
				),
				'default'   => 'right',
				'toggle'    => false,
				'condition' => array(
					'slider_autoplay' => 'yes',
					'slider_effect!'  => 'creative',
				),
			)
		);

		$this->add_control(
			'slider_effect',
			array(
				'label'   => esc_html__( 'Transition', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide'     => esc_html__( 'Default', 'eac-components' ),
					'coverflow' => 'Coverflow',
					'creative'  => esc_html__( 'Creative', 'eac-components' ),
					'fade'      => 'Fade',
				),
			)
		);

		$this->add_responsive_control(
			'slider_width',
			array(
				'label'          => esc_html__( 'Slider width (%)', 'eac-components' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'default'        => array(
					'unit' => '%',
					'size' => 60,
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 60,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'range'          => array(
					'%' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 10,
					),
				),
				'selectors'      => array( '{{WRAPPER}} .swiper' => 'inline-size: {{SIZE}}%;' ),
				'render_type'    => 'template',
				'conditions'     => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'slider_effect',
							'operator' => 'in',
							'value'    => array( 'fade', 'creative' ),
						),
						array(
							'name'     => 'slider_images_number',
							'operator' => '===',
							'value'    => 1,
						),
					),
				),
				'separator'      => 'before',
			)
		);

		$this->add_responsive_control(
			'slider_height',
			array(
				'label'       => esc_html__( 'Slider height (px)', 'eac-components' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 300,
				),
				'range'       => array(
					'px' => array(
						'min'  => 100,
						'max'  => 1000,
						'step' => 50,
					),
				),
				'selectors'   => array( '{{WRAPPER}} .swiper-wrapper .swiper-slide > div' => 'block-size: {{SIZE}}{{UNIT}}; inline-size: auto;' ),
				'render_type' => 'template',
				'conditions'  => array(
					'terms' => array(
						array(
							'name'     => 'slider_effect',
							'operator' => '!in',
							'value'    => array( 'fade', 'creative' ),
						),
						array(
							'name'     => 'slider_images_number',
							'operator' => '===',
							'value'    => 0,
						),
					),
				),
				'separator'   => 'before',
			)
		);

		/** Active le ratio image */
		$this->add_control(
			'slider_ratio_enable',
			array(
				'label'     => esc_html__( 'Enable image ratio', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'yes',
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'slider_ratio',
			array(
				'label'                => esc_html__( 'Image ratio', 'eac-components' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => '1-1',
				'tablet_default'       => '1-1',
				'mobile_default'       => '9-16',
				'options'              => array(
					'1-1'  => esc_html__( 'Default', 'eac-components' ),
					'9-16' => esc_html( '9-16' ),
					'4-3'  => esc_html( '4-3' ),
					'3-2'  => esc_html( '3-2' ),
					'16-9' => esc_html( '16-9' ),
					'21-9' => esc_html( '21-9' ),
				),
				'selectors_dictionary' => array(
					'1-1'  => '1 / 1',
					'9-16' => '9 / 16',
					'4-3'  => '4 / 3',
					'3-2'  => '3 / 2',
					'16-9' => '16 / 9',
					'21-9' => '21 / 9',
				),
				'selectors'            => array( '{{WRAPPER}} .swiper-wrapper .swiper-slide img' => 'aspect-ratio:{{SIZE}};' ),
				'conditions'           => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'slider_images_number',
									'operator' => '>',
									'value'    => 0,
								),
								array(
									'name'     => 'slider_ratio_enable',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'slider_effect',
									'operator' => 'in',
									'value'    => array( 'fade', 'creative' ),
								),
								array(
									'name'     => 'slider_ratio_enable',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'slider_position',
			array(
				'label'          => esc_html__( 'Vertical position', 'eac-components' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'default'        => array(
					'size' => 50,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'range'          => array(
					'%' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					),
				),
				'selectors'      => array( '{{WRAPPER}} .swiper-wrapper .swiper-slide img' => 'object-position: 50% {{SIZE}}%;' ),
				'conditions'     => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'slider_images_number',
									'operator' => '>',
									'value'    => 0,
								),
								array(
									'name'     => 'slider_ratio_enable',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'slider_effect',
									'operator' => 'in',
									'value'    => array( 'fade', 'creative' ),
								),
								array(
									'name'     => 'slider_ratio_enable',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'slider_navigation',
			array(
				'label'     => esc_html__( 'Navigation', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'no',
				'toggle'    => false,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'slider_pagination',
			array(
				'label'   => esc_html__( 'Paging', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'no',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'slider_pagination_click',
			array(
				'label'     => esc_html__( 'Clickable', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'no',
				'toggle'    => false,
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_scrollbar',
			array(
				'label'   => esc_html__( 'Scrollbar', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'no',
				'toggle'  => false,
			)
		);
	}

	/** Les styles du slider */
	protected function register_slider_style_controls() {

		$this->add_control(
			'slider_style_navigation',
			array(
				'label'     => esc_html__( 'Navigation', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( 'slider_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_navigation_size',
			array(
				'label'     => esc_html__( 'Size', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 45,
				'max'       => 100,
				'step'      => 10,
				'default'   => 45,
				'selectors' => array( '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'block-size: {{VALUE}}px; inline-size: {{VALUE}}px;' ),
				'condition' => array( 'slider_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_navigation_color',
			array(
				'label'     => esc_html__( 'Color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'default'   => '#000',
				'selectors' => array( '{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' => 'background-color: {{VALUE}};' ),
				'condition' => array( 'slider_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_style_pagination',
			array(
				'label'     => esc_html__( 'Paging', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( 'slider_pagination' => 'yes' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'slider_pagination_color',
			array(
				'label'     => esc_html__( 'Bullets color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'default'   => 'black',
				'selectors' => array( '{{WRAPPER}} .swiper .swiper-pagination-bullet.swiper-pagination-bullet' => 'background-color: {{VALUE}};' ),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_color_active',
			array(
				'label'     => esc_html__( 'Active bullet color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'default'   => 'red',
				'selectors' => array( '{{WRAPPER}} .swiper .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}};' ),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_width',
			array(
				'label'     => esc_html__( 'Bullets width', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 40,
				'step'      => 1,
				'default'   => 10,
				'selectors' => array( '{{WRAPPER}} .swiper .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'inline-size: {{VALUE}}px;' ),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_height',
			array(
				'label'     => esc_html__( 'Bullets height', 'eac-components' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 3,
				'max'       => 15,
				'step'      => 1,
				'default'   => 3,
				'selectors' => array(
					'{{WRAPPER}} .swiper .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'block-size: {{VALUE}}px;',
				),
				'condition' => array( 'slider_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_radius',
			array(
				'label'              => esc_html__( 'Border radius', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
				'selectors'          => array(
					'{{WRAPPER}} .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array( 'slider_pagination' => 'yes' ),
			)
		);
	}
}
