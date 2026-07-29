<?php
/**
 * Class: Url_External_Image
 *
 * @return l'URL de l'image saisie dans le champ correspondant
 * @since 1.6.2
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Url_External_Image extends Data_Tag {

	public function get_name(): string {
		return 'eac-addon-external-image-widget';
	}

	public function get_title(): string {
		return esc_html__( 'External image', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-url' );
	}

	public function get_categories(): array {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	protected function register_controls(): void {
		$this->add_control(
			'url_image_externe',
			array(
				'label'       => esc_html( 'URL' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'http://your-cdn-link.com',
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);
	}

	public function get_value( array $options = array() ): array {
		$settings = $this->get_settings();
		if ( empty( $settings['url_image_externe']['url'] ) ) {
			return array(
				'url' => Utils::get_placeholder_image_src(),
				'id'  => '',
			); }

		return array(
			'url' => $settings['url_image_externe']['url'],
			'id'  => '',
		);
	}
}
