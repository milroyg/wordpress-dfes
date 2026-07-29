<?php
/**
 * Class: Product_Upsell_Gallery
 *
 * @return créer un tableau d'ID des produits relatifs (upsell) à un produit
 * @since 2.2.2
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Cross_Sell_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-crosssell-gallery';
	}

	public function get_title(): string {
		return esc_html__( 'Cross-sell gallery', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-woo-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::GALLERY_CATEGORY );
	}

	protected function register_controls(): void {
		$this->register_product_id_control();
	}

	public function get_value( array $options = array() ): array {
		$product_id = $this->get_settings( 'product_id' );
		$value      = array();

		if ( empty( $product_id ) ) {
			return $value;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product || 'simple' !== $product->get_type() ) {
			return $value;
		}

		foreach ( $product->get_cross_sell_ids() as $cross_sell_id ) {
			$product_cross_sell = wc_get_product( $cross_sell_id );
			if ( is_a( $product_cross_sell, 'WC_Product' ) && 'simple' === $product_cross_sell->get_type() ) {
				$attachment_id  = $product_cross_sell->get_image_id();
				$value[]        = array( 'id' => $attachment_id . '::product::' . $product_cross_sell->get_id() );
			}
		}
		return $value;
	}
}
