<?php
/**
 * Class: Eac_Acf_Gallery
 *
 * @return un tableau d'ID et URL des images d'un champ ACF de type 'GALLERY' pour l'article courant
 *
 * @since 2.3.0
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\Eac_Acf_Lib;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Traits\Post_Main_Id_Trait;
	use \EACCustomWidgets\Includes\Traits\Fallback_Field_Trait;

	public function get_name(): string {
		return 'eac-addon-gallery-acf-values';
	}

	public function get_title(): string {
		return html_entity_decode( esc_html__( 'Image gallery', 'eac-components' ) );
	}

	public function get_group(): array {
		return array( 'eac-acf-groupe' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::GALLERY_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'acf_gallery_key';
	}

	protected function register_controls(): void {

		$this->add_control(
			'acf_gallery_key',
			array(
				'label'       => esc_html__( 'Field', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);

		// Champ de secours si le champ relationnel est vide
		$this->register_fallback_field_control( array( 'control_condition' => array( 'acf_gallery_key' => '' ) ) );
	}

	/**
	 * get_value
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_value( array $options = array() ): array {
		$key           = $this->get_settings( 'acf_gallery_key' );
		$fallback_key  = $this->get_settings( 'fallback_acf_field_key' );
		$data_gallery  = array();
		$field_key     = '';

		if ( ! empty( $key ) ) {
			list($field_key, $meta_key) = array_pad( explode( '::', $key ), 2, '' );
		} elseif ( ! empty( $fallback_key ) ) {
			$field_key = trim( esc_html( $fallback_key ) );
		}

		if ( ! empty( $field_key ) ) {
			// Fonction du trait Post_Main_Id_Trait
			$real_id = $this->get_post_template_id( $field_key );

			/**
			 * get_field_object, impérativement 3ème param à false
			 * sinon renvoie le contenu de l'attachment au lieu de l'ID
			 */
			$field = get_field_object( $field_key, $real_id, false );

			if ( $field && ! empty( $field['value'] ) ) {
				foreach ( $field['value'] as $attachment_id ) {
					$data_gallery[] = array(
						'id'  => $attachment_id,
					);
				}
			}
		}
		return $data_gallery;
	}

	protected function get_acf_supported_fields(): array {
		return array(
			'eac_gallery',
		);
	}

	public function print_panel_template() {
		$this->fix_print_panel_template();
	}
}
