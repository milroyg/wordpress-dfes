<?php
/**
 * Class: Product_Rating
 *
 * @return affiche les notes moyennes du produit
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Rating extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-rating';
	}

	public function get_title(): string {
		return esc_html__( 'Product review', 'eac-components' );
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
			'eac_woo_rating_mode',
			array(
				'label'       => esc_html__( 'Notation', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'average_rating' => esc_html__( 'Average notes', 'eac-components' ),  // Average rating
					'average_html'   => esc_html__( 'Average notes HTML', 'eac-components' ),
					'rating_count'   => esc_html__( 'Number of notes', 'eac-components' ),      // Rating count
					'review_count'   => esc_html__( 'Review count', 'eac-components' ),        // Review count
				),
				'default'     => 'average_rating',
				'label_block' => true,
			)
		);
	}

	public function render(): void {
		$product_id    = $this->get_settings( 'product_id' );
		$settings_mode = $this->get_settings( 'eac_woo_rating_mode' );
		$value         = '';

		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		switch ( $settings_mode ) {
			case 'average_rating':
				$value = $product->get_average_rating();
				break;
			case 'average_html':
				$value = wc_get_rating_html( $product->get_average_rating() );
				break;
			case 'rating_count':
				$value = $product->get_rating_count();
				break;
			case 'review_count':
				$value = $product->get_review_count();
				break;
		}

		echo wp_kses_post( $value );
	}
}
