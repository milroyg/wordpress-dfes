<?php
/** @since 1.9.8 Création du trait pour les balises dynamiques Woocommerce */
namespace EACCustomWidgets\Includes\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Product_Trait {
	public function register_product_id_control() {
		$this->add_control(
			'product_id',
			array(
				'label'       => esc_html__( 'Sélectionner un produit', 'eac-components' ),
				'type'        => 'eac-select2',
				'select2Options' => array(
					'object_type' => 'product',
				),
				'default'     => false,
			)
		);
	}

	/** @since 1.9.9 'query_type' 'taxonomy' */
	public function register_product_taxonomy_control() {
		$this->add_control(
			'product_taxo',
			array(
				'label'       => esc_html__( 'Sélectionner une taxonomie', 'eac-components' ),
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
	public function register_product_term_control() {
		$this->add_control(
			'product_category',
			array(
				'label'       => esc_html__( 'Sélectionner une catégorie', 'eac-components' ),
				'type'        => 'eac-select2',
				'select2Options' => array(
					'object_type' => 'product',
					'query_type'  => 'term',
					'query_taxo'  => 'product_cat',
				),
				'default'     => false,
			)
		);
	}
}
