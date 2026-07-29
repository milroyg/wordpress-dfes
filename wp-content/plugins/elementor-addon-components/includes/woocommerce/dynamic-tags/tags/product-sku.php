<?php
/**
 * Class: Product_Sku
 *
 * @return affiche le numéro d'inventaire (SKU/UGS)
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Sku extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-sku';
	}

	public function get_title(): string {
		return esc_html__( 'Product SKU', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-woo-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::TEXT_CATEGORY );
	}

	protected function register_controls(): void {
		$this->register_product_id_control();
	}

	public function render(): void {
		$product_id = $this->get_settings( 'product_id' );
		$value      = '';

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		if ( $product->get_sku() ) {
			$value = $product->get_sku();
		}
		echo wp_kses_post( $value );
	}
}
