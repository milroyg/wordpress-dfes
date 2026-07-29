<?php
/**
 * Class: Product_Category_Url
 *
 * @return affiche la liste des produits par leur URL
 * @since 2.2.0
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Category_Url extends Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-url-cat';
	}

	public function get_title(): string {
		return esc_html__( 'Category URLs', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-woo-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::URL_CATEGORY );
	}

	protected function register_controls(): void {
		$this->register_product_term_control();
	}

	public function render(): void {
		$cat_id = $this->get_settings( 'product_category' );

		if ( empty( $cat_id ) ) {
			return;
		}

		$link = get_term_link( absint( $cat_id ), 'product_cat' );
		if ( ! is_wp_error( $link ) && ! empty( $link ) ) {
			echo esc_url( $link );
		} else {
			return;
		}
	}
}
