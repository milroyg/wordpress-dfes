<?php
/**
 * Class: Date_Compare
 *
 * Description:
 *
 * @since 2.1.7
 */

namespace EACCustomWidgets\Includes\DisplayConditions\Conditions;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class Post_Type extends Condition_Base {

	public function get_target_control(): array {
		return array(
			'label'       => esc_html__( 'List of post types', 'eac-components' ),
			'type'        => 'eac-select2',
			'select2Options' => array(
				'object_type' => 'any',
			),
			'multiple'    => true,
			'render_type' => 'none',
			'condition'   => array(
				'element_condition_key' => 'post_type',
			),
		);
	}

	public function get_called_classname(): string {
		return get_called_class();
	}

	public function check( $settings, $value, $operateur = '', $tz = '' ): bool {
		if ( ! is_array( $value ) ) {
			return true;
		}

		switch ( $operateur ) {
			case 'in':
				$etat = in_array( get_post_type( get_the_ID() ), $value, true ) ? false : true;
				break;
			case 'not_in':
				$etat = ! in_array( get_post_type( get_the_ID() ), $value, true ) ? false : true;
				break;
		}

		return $etat;
	}
}
