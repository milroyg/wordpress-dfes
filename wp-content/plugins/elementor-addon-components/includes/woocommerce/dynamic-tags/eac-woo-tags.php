<?php
/**
 * Class: Eac_Woo_Tags
 *
 * Description: Module de base qui enregistre les objets des balises dynamiques WooCommerce
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Eac_Woo_Tags {

	const TAG_NAMESPACE  = __NAMESPACE__ . '\\tags\\';

	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'Product_Add_To_Cart',
		'Product_Excerpt',
		'Product_Featured_Image',
		'Product_Onsale',
		'Product_Prices',
		'Product_Rating',
		'Product_Sku',
		'Product_Stock',
		'Product_Terms',
		'Product_Title',
		'Product_Url',
		'Product_Sale',
		'Product_Category_Image',
		'Product_Category_Url',
		'Product_Field_Keys',
		'Product_Field_Values',
		'Product_Best_Selling_Gallery',
		'Product_Category_Gallery',
		'Product_Categories_Gallery',
		'Product_Featured_Gallery',
		'Product_Gallery_Images',
		'Product_Recent_Sales_Gallery',
		'Product_Similar_Gallery',
		'Product_Upsell_Gallery',
	);

	/**
	 * Constructeur de la class
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );

		/** Supprime les zéros à la fin des prix */
		add_filter( 'woocommerce_price_trim_zeros', '__return_true' );
	}

	/**
	 * Enregistre le groupe et les balises dynamiques WooCommerce
	 */
	public function register_tags( $dynamic_tags ) {
		// Enregistre le nouveau groupe avant d'enregistrer les Tags
		$dynamic_tags->register_group( 'eac-woo-groupe', array( 'title' => esc_html__( 'EAC WooCommerce', 'eac-components' ) ) );

		foreach ( $this->tags_list as $file => $class_name ) {
			$full_class_name = self::TAG_NAMESPACE . $class_name;
			$dynamic_tags->register( new $full_class_name() );
		}
	}
}
