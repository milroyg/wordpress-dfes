<?php
/**
 * Class: Eac_Acf_Number
 *
 * @return Affiche la valeur d'un champ ACF de type 'NUMBER' pour l'article courant
 *
 * @since 1.7.6
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\Eac_Acf_Lib;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Eac_Acf_Number extends Tag {
	use \EACCustomWidgets\Includes\Traits\Panel_Template_Trait;
	use \EACCustomWidgets\Includes\Traits\Post_Main_Id_Trait;

	public function get_name(): string {
		return 'eac-addon-number-acf-values';
	}

	public function get_title(): string {
		return esc_html__( 'Number', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-acf-groupe' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::NUMBER_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'acf_number_key';
	}

	protected function register_controls(): void {

		$this->add_control(
			'acf_number_key',
			array(
				'label'       => esc_html__( 'Field', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'groups'      => Eac_Acf_Lib::get_acf_fields_options( $this->get_acf_supported_fields() ),
				'label_block' => true,
			)
		);
	}

	public function render(): void {
		$field_value = '';
		$key         = $this->get_settings( 'acf_number_key' );

		if ( ! empty( $key ) ) {
			list($field_key, $meta_key) = array_pad( explode( '::', $key ), 2, '' );
			// Fonction du trait Post_Main_Id_Trait
			$real_id = $this->get_post_template_id( $field_key );

			// Récupère l'objet Field
			$field = get_field_object( $field_key, $real_id );

			if ( $field && ! empty( $field['value'] ) ) {
				if ( 'number' === $field['type'] ) {
					$field_value = sprintf( '%1$s %2$s %3$s', $field['prepend'], $field['value'], $field['append'] );
				} else {
					$field_value = $field['value'];
				}
			} else {
				$field_value = get_post_meta( $real_id, $meta_key, true );
				if ( is_array( $field_value ) ) {
					$field_value = implode( ', ', $field_value ); }
			}
		}

		echo wp_kses_post( $field_value );
	}

	protected function get_acf_supported_fields(): array {
		return array(
			'number',
			'range',
		);
	}

	public function print_panel_template() {
		$this->fix_print_panel_template();
	}
}
