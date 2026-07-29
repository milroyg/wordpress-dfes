<?php
/**
 * Class: Product_Sale
 *
 * @return affiche le nombre total de produits vendus
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Sale extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-sale-total';
	}

	public function get_title(): string {
		return esc_html__( 'Product sold', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-woo-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::TEXT_CATEGORY );
	}

	protected function register_controls(): void {
		$this->register_product_id_control();

		$this->add_control(
			'eac_woo_sale_total_fallback',
			array(
				'label'       => esc_html__( 'Alternate text', 'eac-components' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'If quantity is zero', 'eac-components' ),
				'placeholder' => esc_html__( 'Be the first to buy this product', 'eac-components' ),
				'label_block' => true,
			)
		);
	}

	public function render(): void {
		$product_id       = $this->get_settings( 'product_id' );
		$product_fallabck = $this->get_settings( 'eac_woo_sale_total_fallback' );
		$value            = '';

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		$value = absint( $product->get_total_sales() );

		if ( 0 === $value && ! empty( $product_fallabck ) ) {
			$value = $product_fallabck;
		}

		echo wp_kses_post( $value );
	}
}
