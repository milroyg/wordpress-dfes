<?php
/**
 * Class: Product_Url
 *
 * @return affiche la liste des produits par leur URL
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Url extends Data_Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-product-url-tag';
	}

	public function get_title(): string {
		return esc_html__( 'Products URL', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-woo-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::URL_CATEGORY );
	}

	protected function register_controls(): void {
		$this->register_product_id_control();
	}

	public function get_value( array $options = array() ): string {
		$product_id = $this->get_settings( 'product_id' );

		if ( empty( $product_id ) ) {
			return '';
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';
		}

		return esc_url( get_permalink( $product_id ) );
	}
}
