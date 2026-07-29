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

class Post_Category extends Condition_Base {

	public function get_target_control(): array {
		return array(
			'label'       => esc_html__( 'List of categories', 'eac-components' ),
			'type'        => 'eac-select2',
			'select2Options' => array(
				'query_type'  => 'term',
				'query_taxo'  => 'category',
			),
			'multiple'    => true,
			'render_type' => 'none',
			'condition'   => array(
				'element_condition_key' => 'post_category',
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

		$categories = get_the_category( get_the_ID() );
		if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
			$category_ids = array();
			foreach ( $categories as $index => $category ) {
				array_push( $category_ids, $category->cat_ID );
			}

			switch ( $operateur ) {
				case 'in':
					$etat = ! empty( array_intersect( $category_ids, $value ) ) ? false : true;
					break;
				case 'not_in':
					$etat = empty( array_intersect( $category_ids, $value ) ) ? false : true;
					break;
			}

			return $etat;
		}
		return true;
	}
}
