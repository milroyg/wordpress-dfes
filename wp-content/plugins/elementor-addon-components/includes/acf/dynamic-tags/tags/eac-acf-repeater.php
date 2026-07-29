<?php
/**
 * Class: Eac_Acf_Repeater
 *
 * @return un tableau d'ID et URL des images d'un champ ACF de type GROUP 'GALLERY' pour l'article courant
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

class Eac_Acf_Repeater extends Data_Tag {
	use \EACCustomWidgets\Includes\Traits\Post_Main_Id_Trait;

	public function get_name(): string {
		return 'eac-addon-repeater-acf-values';
	}

	public function get_title(): string {
		return esc_html__( 'Repeater', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-acf-groupe' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::MEDIA_CATEGORY,
			TagsModule::GALLERY_CATEGORY,
			TagsModule::IMAGE_CATEGORY,
			TagsModule::URL_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'acf_repeater_key';
	}

	protected function register_controls(): void {

		$this->add_control(
			'acf_repeater_key',
			array(
				'label'       => esc_html__( 'Field', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => Eac_Acf_Lib::get_acf_repeater_field(),
				'label_block' => true,
			)
		);

		foreach ( Eac_Acf_Lib::get_acf_repeater_field() as $repeater_key => $repeater_label ) {
			$this->add_control(
				'acf_repeater_subkey_' . $repeater_key,
				array(
					'label'       => esc_html__( 'Sub fields', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => true,
					'options'     => $this->get_all_sub_field( $repeater_key ),
					'condition'   => array(
						'acf_repeater_key' => $repeater_key,
					),
				)
			);
		}
	}

	/**
	 * get_value
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function get_value( array $options = array() ): string {
		$key_subkey    = 'acf_repeater_subkey_' . $this->get_settings( 'acf_repeater_key' );
		$key           = ! is_null( $this->get_settings( $key_subkey ) ) ? $this->get_settings( $key_subkey ) : '';
		$repeater_key  = ! empty( $this->get_settings( 'acf_repeater_key' ) ) ? explode( '::', $this->get_settings( 'acf_repeater_key' ) )[0] : '';
		$repeater_data = array();

		if ( ! empty( $key ) && ! empty( $repeater_key ) ) {
			$repeater_data = $this->get_repeater_data( $repeater_key, $key );
		}
		return ! empty( $repeater_data ) ? $repeater_data[0] : '';
	}

	/**
	 * get_repeater_data
	 *
	 * @param string $repeater
	 * @param string $key
	 *
	 * @return array
	 */
	private function get_repeater_data( string $repeater, string $key ): array {
		$sub_fields = array();
		$real_id    = $this->get_post_template_id( $repeater );

		if ( have_rows( $repeater, $real_id ) ) {
			$the_row = the_row();
			foreach ( $the_row as $field_key => $any_value ) {
				$field = get_sub_field_object( $field_key );
				if ( $field && $key === $field['key'] && in_array( $field['type'], $this->get_acf_supported_fields(), true ) ) {
					$field_value = $field['value'];
					$field_label = $field['label'];
					if ( 'image' === $field['type'] ) {
						switch ( $field['return_format'] ) {
							case 'array':
								$field_value = array(
									'id'  => $field_value['ID'],
									'url' => $field_value['url'],
								);
								break;
							case 'url':
								$field_value = array(
									'id'  => attachment_url_to_postid( $field_value ),
									'url' => $field_value,
								);
								break;
							case 'id':
								$src = wp_get_attachment_image_src( $field_value, $field['preview_size'] );
								$field_value = array(
									'id'  => $field_value,
									'url' => $src[0],
								);
						}
					} elseif ( 'select' === $field['type'] ) {
						$select_values = array();
						foreach ( $field_value as $value ) {
							if ( 'array' === $field['return_format'] ) {
								$select_values[] = $value['value'];
							} else {
								$select_values[] = $value;
							}
						}
						$field_value = implode( ', ', $select_values );
					} elseif ( in_array( $field['type'], array( 'url', 'page_link' ), true ) ) {
						$field_value = is_array( $field_value ) ? $field['value'][0] : $field_value;
					} elseif ( 'link' === $field['type'] ) {
						$field_value  = 'array' === $field['return_format'] ? $field['value']['url'] : $field_value;
						$field_label  = 'array' === $field['return_format'] ? $field['value']['title'] : $field['label'];
					} elseif ( 'number' === $field['type'] || 'text' === $field['type'] ) {
						$field_value = sprintf( '%1$s %2$s %3$s', $field['prepend'], $field_value, $field['append'] );
					} elseif ( 'file' === $field['type'] ) {
						switch ( $field['return_format'] ) {
							case 'array':
								$field_value = $field_value['url'];
								break;
							case 'id':
								$field_value = wp_get_attachment_url( $field_value );
								break;
						}
					}

					$sub_fields[] = $field_value;
					// break du foreach
					break;
				}
			}
			reset_rows();
		}
		return $sub_fields;
	}

	/**
	 * get_all_sub_field
	 *
	 * @param string $repeater
	 *
	 * @return array
	 */
	private function get_all_sub_field( string $repeater_key ): array {
		$options       = array();
		$repeater_obj  = get_field_object( $repeater_key );
		$repeater_name = '';
		$real_id       = $this->get_post_template_id( $repeater_key );
		$rows          = get_field( $repeater_key, $real_id );

		if ( $repeater_obj ) {
			$repeater_name = $repeater_obj['name'];
		}
		if ( empty( $repeater_name ) ) {
			return $options;
		}

		if ( $rows ) {
			$index = 0;
			foreach ( $rows as $row ) {
				foreach ( $row as $sub_field_name => $sub_field_value ) {
					// _first_repeater_0_photo = _LabelDuRepeater_index_LabelDuSouschamp : table xxxx_postmeta
					$sub_key = sprintf( '_%1$s_%2$s_%3$s', $repeater_name, $index, $sub_field_name );
					$sub_field_key = get_field( $sub_key, $real_id );

					if ( $sub_field_key && ! empty( $sub_field_key ) ) {
						$sub_field_object = get_field_object( $sub_field_key );
						if ( $sub_field_object ) {
							$options[ $sub_field_key ] = $sub_field_object['label'];
						}
					}
				}
				++$index;
			}
		}
		return $options;
	}

	/**
	 * get_acf_supported_fields
	 *
	 * @return array
	 */
	protected function get_acf_supported_fields(): array {
		return array(
			'repeater',
			'image',
			'text',
			'textarea',
			'email',
			'url',
			'link',
			'page_link',
			'select',
			'number',
			'date_picker',
			'file',
		);
	}
}
