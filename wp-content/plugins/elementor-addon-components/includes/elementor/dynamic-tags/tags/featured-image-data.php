<?php
/**
 * Class: Featured_Image_Data
 *
 * @return affiche les données, attributs de l'image en avant (Featured image) de l'article courant
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Featured_Image_Data extends Tag {

	public function get_name(): string {
		return 'eac-addon-featured-image-data';
	}

	public function get_title(): string {
		return esc_html__( 'Featured image data', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-post' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'eac_attachement_data';
	}

	protected function register_controls(): void {
		$this->add_control(
			'eac_attachement_data',
			array(
				'label'   => esc_html__( 'Données', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => array(
					'title'       => esc_html__( 'Title', 'eac-components' ),
					'alt'         => 'Alt',
					'caption'     => esc_html__( 'Legend', 'eac-components' ),
					'description' => esc_html__( 'Description', 'eac-components' ),
					'src'         => esc_html__( 'URL image', 'eac-components' ),
					'href'        => esc_html__( 'Attachment URL', 'eac-components' ),
				),
			)
		);
	}

	public function render(): void {
		$settings   = $this->get_settings_for_display();
		$attachment = $this->get_attachement();

		if ( ! $attachment ) {
			return; }

		$value = '';

		switch ( $settings['eac_attachement_data'] ) {
			case 'alt':
				$value = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
				break;
			case 'caption':
				$value = $attachment->post_excerpt;
				break;
			case 'description':
				$value = $attachment->post_content;
				break;
			case 'href':
				$value = get_permalink( $attachment->ID );
				break;
			case 'src':
				$value = $attachment->guid;
				break;
			case 'title':
				$value = $attachment->post_title;
				break;
		}
		echo wp_kses_post( $value );
	}

	private function get_attachement() {
		$settings = $this->get_settings();
		$id       = get_post_thumbnail_id();

		if ( ! $id || 0 === $id ) {
			return false;
		}

		return get_post( $id );
	}
}
