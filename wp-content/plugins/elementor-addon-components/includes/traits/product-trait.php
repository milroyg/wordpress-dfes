<?php
/** @since 1.9.8 Création du trait pour les balises dynamiques Woocommerce */
namespace EACCustomWidgets\Includes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Product_Trait {
	public function register_product_id_control( $args = array() ) {
		$default_args = array(
			'control_condition' => array(),
		);
		$args = wp_parse_args( $args, $default_args );

		$this->add_control(
			'product_id',
			array(
				'label'       => esc_html__( 'Select product', 'eac-components' ),
				'type'        => 'eac-select2',
				'select2Options' => array(
					'object_type' => 'product',
				),
				'default'     => false,
				'condition'   => $args['control_condition'],
			)
		);
	}

	/** @since 1.9.9 'query_type' 'taxonomy' */
	public function register_product_taxonomy_control() {
		$this->add_control(
			'product_taxo',
			array(
				'label'       => esc_html__( 'Taxonomy filter', 'eac-components' ),
				'type'        => 'eac-select2',
				'select2Options' => array(
					'object_type' => 'product',
					'query_type'  => 'taxonomy',
				),
				'default'     => false,
			)
		);
	}

	/** @since 1.9.9 */
	public function register_product_term_control( $args = array() ) {
		$default_args = array(
			'control_condition' => array(),
		);
		$args = wp_parse_args( $args, $default_args );

		$this->add_control(
			'product_category',
			array(
				'label'       => esc_html__( 'Category filter', 'eac-components' ),
				'type'        => 'eac-select2',
				'select2Options' => array(
					'object_type' => 'product',
					'query_type'  => 'term',
					'query_taxo'  => 'product_cat',
				),
				'default'     => false,
				'condition'   => $args['control_condition'],
			)
		);
	}
}
