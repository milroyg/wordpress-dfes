<?php
/**
 * Class: Product_Recent_Sales_Gallery
 *
 * @return créer un tableau d'ID des images des dernières ventes
 * Ce n'est pas le total des ventes récentes par produit
 * @since 2.2.3
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Recent_Sales_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Traits\Product_Trait;

	public function get_name(): string {
		return 'eac-addon-woo-recent-sales-gallery';
	}

	public function get_title(): string {
		return esc_html__( 'Recent sales gallery', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-woo-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::GALLERY_CATEGORY );
	}

	protected function register_controls(): void {
		$this->add_control(
			'woo_recent_status',
			array(
				'label'       => esc_html__( 'Order status', 'eac-components' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => array(
					'wc-pending'   => esc_html__( 'Pending payment', 'eac-components' ),
					'wc-completed' => esc_html__( 'Payment completed', 'eac-components' ),
				),
				'default'     => array( 'wc-completed' ),
				'multiple'    => true,
			)
		);

		$this->add_control(
			'woo_recent_days',
			array(
				'label'   => esc_html__( 'Number of days', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 1000,
				'step'    => 7,
				'default' => 7,
			)
		);

		$this->add_control(
			'woo_recent_count',
			array(
				'label'   => esc_html__( 'Product count', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
				'default' => 4,
			)
		);

		$this->register_product_term_control();
	}

	public function get_value( array $options = array() ): array {
		$status   = $this->get_settings( 'woo_recent_status' );
		$days     = ! empty( $this->get_settings( 'woo_recent_days' ) ) ? $this->get_settings( 'woo_recent_days' ) : 7;
		$count    = ! empty( $this->get_settings( 'woo_recent_count' ) ) ? $this->get_settings( 'woo_recent_count' ) : 10;
		$termid   = $this->get_settings( 'product_category' );
		$value    = array();

		$all_orders = wc_get_orders(
			array(
				'limit'      => -1,
				'status'     => $status,
				'date_after' => date_i18n( 'Y-m-d', strtotime( "-{$days} days" ) ),
				'return'     => 'ids',
			)
		);

		foreach ( $all_orders as $all_order ) {
			$order = wc_get_order( $all_order );
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( 'line_item' !== $item->get_type() ) {
					continue;
				}
				$product = $item->get_product();
				if ( ! is_a( $product, 'WC_Product' ) || 'simple' !== $product->get_type() ) {
					continue;
				}

				$product_id = $product->get_id();
				if ( ! empty( $termid ) && ! has_term( $termid, 'product_cat', $product_id ) ) {
					continue;
				}

				$thumb_id = $product->get_image_id();
				if ( $thumb_id ) {
					$qty                           = isset( $value[ $product->get_slug() ] ) ? $item->get_quantity() + (int) explode( '::', implode( $value[ $product->get_slug() ] ) )[3] : $item->get_quantity();
					$value[ $product->get_slug() ] = array( 'id' => $thumb_id . '::recent::' . $product_id . '::' . $qty );
				}
			}
		}

		if ( ! empty( $value ) ) {
			ksort( $value, SORT_STRING );
			return array_slice( $value, 0, $count, true );
		}
		return $value;
	}
}
