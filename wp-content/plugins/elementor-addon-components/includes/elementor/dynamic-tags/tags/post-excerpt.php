<?php
/**
 * Class: Post_Excerpt
 *
 * @return le résumé ou tous les paragraphes d'un article créés avec un thème basé sur les blocks
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Post_Excerpt extends Tag {
	public function get_name(): string {
		return 'eac-addon-post-excerpt';
	}

	public function get_title(): string {
		return esc_html__( 'Excerpt', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-post' );
	}

	public function get_categories(): array {
		return array( TagsModule::TEXT_CATEGORY );
	}

	protected function register_controls(): void {
		$this->add_control(
			'excerpt_length',
			array(
				'label'   => esc_html__( 'Number of words', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 3,
				'max'     => 200,
				'step'    => 5,
				'default' => 25,
			)
		);
	}

	public function render(): void {
		$settings = $this->get_settings();
		$post = get_post();

		$longeur = empty( $settings['excerpt_length'] ) ? 25 : $settings['excerpt_length'];
		echo \EACCustomWidgets\Core\Utils\Eac_Tools_Util::get_post_excerpt( $post->ID, absint( $longeur ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
