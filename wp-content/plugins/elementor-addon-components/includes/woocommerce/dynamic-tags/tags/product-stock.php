<?php
/**
 * Class: Product_Stock
 *
 * @return affiche le stock du produit
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Stock extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-stock';
	}

	public function get_title(): string {
		return esc_html__( 'Product stock', 'eac-components' );
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
			'eac_woo_stock_prefix',
			array(
				'label'   => esc_html__( 'Long format', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'yes' => array(
						'title' => esc_html__( 'Display', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'Hide', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'no',
			)
		);
	}

	public function render(): void {
		$product_id      = $this->get_settings( 'product_id' );
		$settings_prefix = $this->get_settings( 'eac_woo_stock_prefix' );
		$value           = '';

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		if ( 'yes' === $settings_prefix ) {
			$value = wc_get_stock_html( $product );
		} else {
			$value = absint( $product->get_stock_quantity() );
		}

		echo wp_kses_post( $value );
	}
}
