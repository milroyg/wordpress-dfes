<?php
/**
 * Class: Eac_Acf_Url
 *
 * Méthode 'get_acf_supported_fields' pour la liste des champs 'URL'
 *
 * @return La valeur d'un champ ACF de type 'URL' pour l'article courant
 *
 * @since 1.7.6
 * @since 2.2.4 Simplication du calcul de l'ID
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\Eac_Acf_Lib;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Url extends Data_Tag {
	use \EACCustomWidgets\Includes\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Traits\Post_Main_Id_Trait;

	public function get_name(): string {
		return 'eac-addon-url-acf-values';
	}

	public function get_title(): string {
		return esc_html( 'Url' );
	}

	public function get_group(): array {
		return array( 'eac-acf-groupe' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::URL_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'acf_url_key';
	}

	protected function register_controls(): void {

		$this->add_control(
			'acf_url_key',
			array(
				'label'       => esc_html__( 'Field', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);
	}

	public function get_value( array $options = array() ): string {
		$field_value = '';
		$key         = $this->get_settings( 'acf_url_key' );

		if ( ! empty( $key ) ) {
			list($field_key, $meta_key) = array_pad( explode( '::', $key ), 2, '' );
			$real_id = $this->get_post_template_id( $field_key );
			$field = get_field_object( $field_key, $real_id );

			if ( $field && ! empty( $field['value'] ) ) {
				// La valeur par défaut du champ (url type)
				$field_value = $field['value'];

				// Ne prend que la première URL si mutiples URLs
				if ( is_array( $field_value ) && isset( $field_value[0] ) ) {
					$field_value = $field_value[0];
				}

				switch ( $field['type'] ) {
					case 'email':
							$field_value = 'mailto:' . sanitize_email( $field_value );
						break;
					case 'file':
						switch ( $field['return_format'] ) {
							case 'array':
								$field_value = esc_url( $field_value['url'] );
								break;
							case 'id':
								$field_value = esc_url( wp_get_attachment_url( $field_value ) );
								break;
						}
						break;
					case 'post_object':
					case 'relationship':
						switch ( $field['return_format'] ) {
							case 'object':
								$field_value = esc_url( get_permalink( get_post( $field_value->ID )->ID ) );
								break;
							case 'id':
								$field_value = esc_url( get_permalink( get_post( $field_value )->ID ) );
								break;
						}
						break;
					case 'link':
						switch ( $field['return_format'] ) {
							case 'array':
								$field_value = esc_url( $field_value['url'] );
								break;
						}
						break;
				}
			}
		}

		return wp_kses_post( $field_value );
	}

	protected function get_acf_supported_fields(): array {
		return array(
			'email',
			'link',
			'page_link',
			'url',
			'post_object',
			'relationship',
		);
	}

	public function print_panel_template() {
		$this->fix_print_panel_template(); // L'autre trait
	}
}
