<?php
/**
 * Class: Product_Excerpt
 *
 * @return affiche le titre le résumé ou le texte long du produit
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Excerpt extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-excerpt';
	}

	public function get_title(): string {
		return esc_html__( 'Product description', 'eac-components' );
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
			'eac_woo_excerpt_len',
			array(
				'label'   => esc_html__( 'Description type', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'long'  => array(
						'title' => esc_html__( 'Description', 'eac-components' ),
						'icon'  => 'eicon-h-align-left',
					),
					'short' => array(
						'title' => esc_html__( 'Excerpt', 'eac-components' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'short',
			)
		);
	}

	public function render(): void {
		$product_id   = $this->get_settings( 'product_id' );
		$settings_len = $this->get_settings( 'eac_woo_excerpt_len' );

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		$texte = 'long' === $settings_len ? $product->get_description() : $product->get_short_description();
		echo wp_kses_post( $texte );
	}
}
