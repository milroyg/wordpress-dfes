<?php
/**
 * Class: Product_Prices
 *
 * @return affiche les prix du produit régulier et promo
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Prices extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-prices';
	}

	public function get_title(): string {
		return esc_html__( 'Product price', 'eac-components' );
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
			'eac_woo_prices_format',
			array(
				'label'   => esc_html__( 'Price', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'regular' => esc_html__( 'Regular', 'eac-components' ),
					'promo'   => esc_html__( 'Sale', 'eac-components' ),
					'both'    => esc_html__( 'Both', 'eac-components' ),
				),
				'default' => 'both',
			)
		);
	}

	public function render(): void {
		$product_id      = $this->get_settings( 'product_id' );
		$settings_format = $this->get_settings( 'eac_woo_prices_format' );
		$value           = '';

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		switch ( $settings_format ) {
			case 'both':
				$value = $product->get_price_html();
				break;
			case 'regular':
				$value = wc_price( $product->get_regular_price() ) . $product->get_price_suffix();
				break;
			case 'promo' && $product->is_on_sale():
				$value = wc_price( $product->get_sale_price() ) . $product->get_price_suffix();
				break;
		}

		echo wp_kses_post( $value );
	}
}
