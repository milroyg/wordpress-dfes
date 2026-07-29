<?php
/**
 * Class: Product_Title
 *
 * @return affiche le titre du site
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Title extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-title';
	}

	public function get_title(): string {
		return esc_html__( 'Product title', 'eac-components' );
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

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		echo esc_html( $product->get_title() );
	}
}
