<?php
namespace EACCustomWidgets\Includes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

trait Button_Read_More_Trait {

	/**
	 * Les contrôles du bouton
	 *
	 * @since 2.2.7 Ajout d'une liste d'arguments pour les conditions d'affichages des controls
	 */
	protected function register_button_more_content_controls( $args = array() ): void {
		$default_args = array(
			'control_condition' => array(),
		);
		$args = wp_parse_args( $args, $default_args );

		$this->add_control(
			'button_more_label',
			array(
				'label'     => esc_html__( 'Label', 'eac-components' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'ai'        => array( 'active' => false ),
				'default'   => esc_html__( 'Read post', 'eac-components' ),
				'condition' => $args['control_condition'],
			)
		);

		$this->add_control(
			'button_add_more_picto',
			array(
				'label'   => esc_html__( 'Add pictogram', 'eac-components' ),
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
				'condition' => $args['control_condition'],
			)
		);

		$this->add_control(
			'button_more_picto',
			array(
				'label'              => esc_html__( 'Pictogram', 'eac-components' ),
				'type'               => Controls_Manager::ICONS,
				'skin'               => 'inline',
				'default'            => array(
					'value'   => 'fas fa-eye',
					'library' => 'fa-solid',
				),
				'frontend_available' => true,
				'condition'          => array_merge( $args['control_condition'], array( 'button_add_more_picto' => 'yes' ) ), // Merge les conditions globales et locale
			)
		);

		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';
		$this->add_control(
			'button_more_position',
			array(
				'label'     => esc_html__( 'Position', 'eac-components' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'before' => array(
						'title' => is_rtl() ? esc_html__( 'After', 'eac-components' ) : esc_html__( 'Before', 'eac-components' ),
						'icon'  => "eicon-h-align-{$start}",
					),
					'after'  => array(
						'title' => is_rtl() ? esc_html__( 'Before', 'eac-components' ) : esc_html__( 'After', 'eac-components' ),
						'icon'  => "eicon-h-align-{$end}",
					),
				),
				'default'   => 'before',
				'toggle'    => false,
				'condition' => array_merge( $args['control_condition'], array( 'button_add_more_picto' => 'yes' ) ),
			)
		);

		$this->add_control(
			'button_more_marge',
			array(
				'label'              => esc_html__( 'Margin', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => array( 'left', 'right' ),
				'default'            => array(
					'left'     => 0,
					'right'    => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'selectors'          => array(
					'{{WRAPPER}} .button__readmore-wrapper .button-icon' => 'margin-block: 0; margin-inline: {{LEFT}}px {{RIGHT}}px;',
				),
				'condition'          => array_merge( $args['control_condition'], array( 'button_add_more_picto' => 'yes' ) ),
			)
		);
	}

	/** Les styles du bouton */
	protected function register_button_more_style_controls( $args = array() ): void {
		$default_args = array(
			'control_condition' => array(),
		);
		$args = wp_parse_args( $args, $default_args );

		$this->start_controls_tabs( 'button_more_controls_tabs' );

			$this->start_controls_tab(
				'button_more_tab_normal',
				array(
					'label'     => esc_html__( 'Normal', 'eac-components' ),
					'condition' => $args['control_condition'],
				)
			);

				$this->add_control(
					'button_more_color',
					array(
						'label'     => esc_html__( 'Color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper, {{WRAPPER}} .buttons-wrapper' => 'color: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name'      => 'button_more_typo',
						'label'     => esc_html__( 'Typography', 'eac-components' ),
						'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
						'selector'  => '{{WRAPPER}} .button__readmore-wrapper',
						'condition' => $args['control_condition'],
					)
				);

				$this->add_control(
					'button_more_bg',
					array(
						'label'     => esc_html__( 'Background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper' => 'background-color: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'button_more_tab_hover',
				array(
					'label'     => esc_html__( 'Hover', 'eac-components' ),
					'condition' => $args['control_condition'],
				)
			);

				$this->add_control(
					'button_more_color_hover',
					array(
						'label'     => esc_html__( 'Color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper:hover, {{WRAPPER}} .button__readmore-wrapper:focus' => 'color: {{VALUE}};',
							'{{WRAPPER}} .button__readmore-wrapper:hover svg, {{WRAPPER}} .button__readmore-wrapper:focus svg' => 'fill: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

				$this->add_control(
					'button_more_bg_hover',
					array(
						'label'     => esc_html__( 'Background color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper:hover, {{WRAPPER}} .button__readmore-wrapper:focus' => 'background-color: {{VALUE}};',
						),
						'condition' => $args['control_condition'],
					)
				);

				$this->add_control(
					'button_more_border_color_hover',
					array(
						'label'     => esc_html__( 'Border color', 'eac-components' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .button__readmore-wrapper:hover, {{WRAPPER}} .button__readmore-wrapper:focus' => 'border-block-color: {{VALUE}}; border-inline-color: {{VALUE}};',
						),
						'condition' => array_merge( $args['control_condition'], array( 'button_more_border_border!' => 'none' ) ),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'button_more_border',
				'selector'  => '{{WRAPPER}} .button__readmore-wrapper',
				'separator' => 'before',
				'condition' => $args['control_condition'],
			)
		);

		$this->add_control(
			'button_more_radius',
			array(
				'label'              => esc_html__( 'Border radius', 'eac-components' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
				'selectors'          => array(
					'{{WRAPPER}} .button__readmore-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $args['control_condition'],
			)
		);

		$this->add_responsive_control(
			'button_more_padding',
			array(
				'label'     => esc_html__( 'Padding', 'eac-components' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .button__readmore-wrapper' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $args['control_condition'],
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_more_shadow',
				'label'     => esc_html__( 'Shadow', 'eac-components' ),
				'selector'  => '{{WRAPPER}} .button__readmore-wrapper',
				'condition' => $args['control_condition'],
			)
		);
	}

	/** Le rendu du bouton */
	protected function render_button_more( $args = array() ): void {
		$settings         = $this->get_settings_for_display();
		$permalink        = $args['permalink'];
		$post_title       = $args['item_title'];
		$has_global_link  = $args['global_link'];
		$default_label    = isset( $args['default_label'] ) ? $args['default_label'] : '';
		$has_noffolow     = isset( $args['nofollow'] ) && true === $args['nofollow'] ? true : false;
		$has_button_picto = 'yes' === $settings['button_add_more_picto'] ? true : false;

		$label = ! empty( $settings['button_more_label'] ) ? $settings['button_more_label'] : $default_label;
		if ( $has_global_link ) {
			$this->add_render_attribute( 'button_readmore', 'class', 'button-readmore eac-accessible-link card-link' );
		} else {
			$this->add_render_attribute( 'button_readmore', 'class', 'button-readmore eac-accessible-link' );
		}
		$this->add_render_attribute( 'button_readmore', 'aria-label', sprintf( '%1$s - %2$s', esc_attr( $label ), ucfirst( esc_attr( $post_title ) ) ) );
		$this->add_render_attribute( 'button_readmore', 'href', esc_url( $permalink ) );
		if ( $has_noffolow ) {
			$this->add_render_attribute( 'button_readmore', 'rel', 'nofollow' );
		}
		?>
		<a <?php $this->print_render_attribute_string( 'button_readmore' ); ?>>
			<span class='button__readmore-wrapper'>
				<?php
				if ( $has_button_picto && 'before' === $settings['button_more_position'] ) { ?>
					<span class='button-icon eac-icon-svg'>
						<?php Icons_Manager::render_icon( $settings['button_more_picto'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php }
				printf( '<span class="label-icon">%s</span>', esc_html( trim( $label ) ) );
				if ( $has_button_picto && 'after' === $settings['button_more_position'] ) { ?>
					<span class='button-icon eac-icon-svg'>
						<?php Icons_Manager::render_icon( $settings['button_more_picto'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php } ?>
			</span>
		</a>
		<?php
		$this->remove_render_attribute( 'button_readmore' );
	}
}
