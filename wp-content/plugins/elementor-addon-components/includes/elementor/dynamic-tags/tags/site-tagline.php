<?php
/**
 * Class: Site_Tagline
 *
 * @return affiche la valeur du slogan du site
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Site_Tagline extends Tag {
	public function get_name(): string {
		return 'eac-addon-site-tagline';
	}

	public function get_title(): string {
		return esc_html__( 'Site tagline', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-site-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::TEXT_CATEGORY );
	}

	public function render(): void {
		echo esc_html( get_bloginfo( 'description' ) );
	}
}
