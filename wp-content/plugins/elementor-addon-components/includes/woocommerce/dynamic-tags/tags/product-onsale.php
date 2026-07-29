<?php
/**
 * Class: Product_Onsale
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

class Product_Onsale extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-sale';
	}

	public function get_title(): string {
		return esc_html__( 'Product sale', 'eac-components' );
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
			'eac_woo_onsale_percent',
			array(
				'label'        => esc_html__( 'Display percent', 'eac-components' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'eac-components' ),
				'label_off'    => esc_html__( 'No', 'eac-components' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'eac_woo_onsale_text',
			array(
				'label'     => esc_html__( 'Badge label', 'eac-components' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Sale!', 'eac-components' ),
				'condition' => array( 'eac_woo_onsale_percent!' => 'yes' ),
			)
		);
	}

	public function render(): void {
		$product_id       = $this->get_settings( 'product_id' );
		$settings_text    = $this->get_settings( 'eac_woo_onsale_text' );
		$settings_percent = $this->get_settings( 'eac_woo_onsale_percent' ) === 'yes' ? true : false;
		$value            = '';

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		if ( $product->is_on_sale() ) {
			if ( $settings_percent ) {
				$value = '-' . round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 ) . '%';
			} else {
				$value = $settings_text;
			}
		}

		echo wp_kses_post( $value );
	}
}
