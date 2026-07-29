<?php
/**
 * Class: Site_URL
 *
 * @return l'URL du site
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Site_URL extends Data_Tag {

	public function get_name(): string {
		return 'eac-addon-site-url';
	}

	public function get_title(): string {
		return esc_html__( 'Site URL', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-site-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_value( array $options = array() ): string {
		return esc_url( home_url() );
	}
}
