<?php
/**
 * Class: Product_Related_Gallery
 *
 * @return créer un tableau d'ID des images similaires par leurs catégories à un produit
 * @since 2.2.2
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Related_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-gallery-similar';
	}

	public function get_title(): string {
		return esc_html__( 'Related gallery', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-woo-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::GALLERY_CATEGORY );
	}

	protected function register_controls(): void {
		$this->register_product_id_control();

		$this->add_control(
			'woo_similar',
			array(
				'label'   => esc_html__( 'Product count', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
				'default' => 4,
			)
		);
	}

	public function get_value( array $options = array() ): array {
		$product_id = $this->get_settings( 'product_id' );
		$limit      = ! empty( $this->get_settings( 'woo_similar' ) ) ? $this->get_settings( 'woo_similar' ) : 10;
		$value      = array();

		if ( empty( $product_id ) ) {
			return $value;
		}

		$product_cat = wc_get_product( $product_id );
		if ( ! $product_cat ) {
			return $value;    }

		$terms = get_the_terms( $product_cat->get_id(), 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term_cat = $terms[0]->name;
			$args     = array(
				'category' => array( $term_cat ),
				'limit'    => $limit,
				'orderby'  => 'rand',
				'exclude'  => array( $product_id ),
			);

			$products = wc_get_products( $args );
			if ( ! is_wp_error( $products ) && ! empty( $products ) ) {
				foreach ( $products as $product ) {
					$thumb_id = $product->get_image_id();
					if ( $thumb_id ) {
						$value[] = array( 'id' => $thumb_id . '::category::' . $product->get_id() );
					}
				}
			}
		}

		return $value;
	}
}
