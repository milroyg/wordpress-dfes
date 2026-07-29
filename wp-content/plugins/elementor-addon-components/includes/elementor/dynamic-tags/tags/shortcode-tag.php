<?php
/**
 * Class: Shortcode
 *
 * @return exécute le shortcode et affiche le résultat
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Shortcode_Tag extends Tag {
	public function get_name(): string {
		return 'eac-addon-shortcode';
	}

	public function get_title(): string {
		return esc_html__( 'Shortcode', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-site-groupe' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::NUMBER_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::DATETIME_CATEGORY,
		);
	}

	protected function register_controls(): void {
		$this->add_control(
			'shortcode',
			array(
				'label'   => esc_html__( 'Shortcode', 'eac-components' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 8,
			)
		);

		$this->add_control(
			'shortcode_escape',
			array(
				'label'        => esc_html__( 'Shortcode escape', 'eac-components' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'eac-components' ),
				'label_off'    => esc_html__( 'No', 'eac-components' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
	}

	public function render(): void {
		$settings = $this->get_settings();

		if ( empty( $settings['shortcode'] ) ) {
			return;
		}

		$shortcode_value  = $settings['shortcode'];
		$value            = do_shortcode( $shortcode_value );
		$should_escape    = 'yes' === $this->get_settings( 'shortcode_escape' ) ? true : false;

		if ( $should_escape ) {
			$value = wp_kses_post( $value );
		}

		echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
