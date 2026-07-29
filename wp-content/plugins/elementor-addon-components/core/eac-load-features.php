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

use EACCustomWidgets\Core\Eac_Load_Config;

class Eac_Load_Features {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/** Constructeur de la class */
	public function __construct() {

		$this->load_futures();

		/** Les actions AJAX 'wp_ajax_xxxxxx' pour le control 'eac-select2' */
		if ( Eac_Load_Config::is_feature_active( 'dysplay-condition' ) || Eac_Load_Config::is_feature_active( 'woo-dynamic-tag' ) ) {
			new \EACCustomWidgets\Includes\Elementor\Controls\Eac_Select2_Actions();
		}

		/** Charge les page d'options ACF si la fonctionnalité est active */
		if ( Eac_Load_Config::is_feature_active( 'acf-option-page' ) ) {
			new \EACCustomWidgets\Includes\Acf\Eac_Acf_Options_Page();
		}

		/** Ajout des filtres pour les champs de la bibliothèque des medias  */
		if ( Eac_Load_Config::is_feature_active( 'extend-fields-medias' ) ) {
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
	 * Ajout des champs URL et catégories pour les images de la librairie des médias
	 *
	 * @param array $form_fields
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	public function add_custom_attachment_fields( array $form_fields, \WP_Post $post ): array {
		if ( ! wp_attachment_is_image( $post->ID ) ) {
			return $form_fields;
		}

		$field_url = get_post_meta( $post->ID, 'eac_media_url', true );
		$field_cat = get_post_meta( $post->ID, 'eac_media_cat', true );

		$form_fields['eac_media_url'] = array(
			'label' => esc_html__( 'EAC custom URL', 'eac-components' ),
			'input' => 'text',
			'value' => $field_url ? esc_url( $field_url ) : '',
		);

		$form_fields['eac_media_cat'] = array(
			'label' => esc_html__( 'EAC categories', 'eac-components' ),
			'input' => 'text',
			'value' => $field_cat ? esc_html( $field_cat ) : '',
			'helps' => 'Ex: cat1,cat2,cat3',
		);
		return $form_fields;
	}

	/**
	 * save_custom_attachment_fields
	 * Sauvegarde des champs URL et catégories de la librarie des médias
	 *
	 * @param array $post
	 * @param array $attachment
	 *
	 * @return array
	 */
	public function save_custom_attachment_fields( array $post, array $attachment ): array {
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

	/**
	 * load_futures
	 *
	 * @return void
	 */
	public function load_futures(): void {
		/** Charge les fonctionnalités, notamment les balises dynamiques */
		foreach ( Eac_Load_Config::get_features_active() as $element => $active ) {
			if ( Eac_Load_Config::is_feature_active( $element ) ) {
				$path            = Eac_Load_Config::get_feature_path( $element );
				$full_class_name = Eac_Load_Config::get_feature_namespace( $element );
				if ( $path && is_readable( $path ) ) {
					require_once $path;
				} elseif ( $full_class_name ) {
					new $full_class_name();
				}
			}
		}
	}
}
