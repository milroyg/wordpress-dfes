<?php
/**
 * Class: Eac_Load_Features
 *
 * Description: Charge les fonctionnalités actives
 *
 * @since 1.9.2
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Config_Elements;

class Eac_Load_Features {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/** Constructeur de la class */
	public function __construct() {

		/** Charge les fonctionnalités, notamment les balises dynamiques */
		foreach ( Eac_Config_Elements::get_features_active() as $element => $active ) {
			if ( Eac_Config_Elements::is_feature_active( $element ) ) {
				$path            = Eac_Config_Elements::get_feature_path( $element );
				$full_class_name = Eac_Config_Elements::get_feature_namespace( $element );
				if ( $path ) {
					/** @since 2.3.0 Pas très jolie mais la création d'un nouveau type ACF se fait au plus tôt dans l'action 'init' */
					if ( 'acf-image-gallery' === $element ) {
						add_action('init', function () use ( $path ) {
							require_once $path;
						}, 99 );
					} else {
						require_once $path;
					}
				} elseif ( $full_class_name ) {
					new $full_class_name();
				}
			}
		}

		/** Les actions AJAX 'wp_ajax_xxxxxx' pour le control 'eac-select2' */
		if ( Eac_Config_Elements::is_feature_active( 'dysplay-condition' ) || Eac_Config_Elements::is_feature_active( 'woo-dynamic-tag' ) ) {
			new \EACCustomWidgets\Includes\Elementor\Controls\Eac_Select2_Actions();
		}

		/** Ajout des filtres pour les champs de la bibliothèque des medias  */
		if ( Eac_Config_Elements::is_feature_active( 'extend-fields-medias' ) ) {
			add_filter( 'attachment_fields_to_edit', array( $this, 'add_custom_attachment_fields' ), 20, 2 );
			add_filter( 'attachment_fields_to_save', array( $this, 'save_custom_attachment_fields' ), 20, 2 );
		}
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * add_custom_attachment_fields
	 *
	 * Ajout des champs URL et catégories pour les images de la librairie des médias
	 */
	public function add_custom_attachment_fields( $form_fields, $post ) {

		if ( ! wp_attachment_is_image( $post->ID ) ) {
			return $form_fields;
		}

		$field_url = get_post_meta( $post->ID, 'eac_media_url', true );
		$field_cat = get_post_meta( $post->ID, 'eac_media_cat', true );

		$form_fields['eac_media_url'] = array(
			'label' => esc_html__( 'EAC URL personnalisée', 'eac-components' ),
			'input' => 'text',
			'value' => $field_url ? esc_url( $field_url ) : '',
		);

		$form_fields['eac_media_cat'] = array(
			'label' => esc_html__( 'EAC catégories', 'eac-components' ),
			'input' => 'text',
			'value' => $field_cat ? esc_html( $field_cat ) : '',
			'helps' => 'Ex: cat1,cat2,cat3',
		);
		return $form_fields;
	}

	/**
	 * save_custom_attachment_fields
	 *
	 * Sauvegarde des champs URL et catégories de la librarie des médias
	 */
	public function save_custom_attachment_fields( $post, $attachment ) {
		if ( ! current_user_can( 'edit_post', $post['ID'] ) ) {
			return $post;
		}

		if ( ! empty( $attachment['eac_media_url'] ) ) {
			$url = esc_url_raw( sanitize_text_field( $attachment['eac_media_url'] ) );
			update_post_meta( $post['ID'], 'eac_media_url', $url );
		}

		if ( ! empty( $attachment['eac_media_cat'] ) ) {
			update_post_meta( $post['ID'], 'eac_media_cat', sanitize_text_field( $attachment['eac_media_cat'] ) );
		}
		return $post;
	}
}
