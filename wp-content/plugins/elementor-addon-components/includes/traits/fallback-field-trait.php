<?php
/**
 * Les contrôles des champs ACF par défaut
 *
 * @since 2.3.2
 */

namespace EACCustomWidgets\Includes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;

trait Fallback_Field_Trait {

	protected function register_fallback_field_control( $args = array() ): void {
		$default_args = array(
			'control_condition' => array(),
		);
		$args = wp_parse_args( $args, $default_args );

		$this->add_control(
			'fallback_acf_field_key',
			array(
				'label'       => esc_html__( 'Fallback: Field key', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'field_xxxxxxx',
				'dynamic'     => array( 'active' => false ),
				'ai'          => array( 'active' => false ),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => $args['control_condition'],
			)
		);
	}
}
