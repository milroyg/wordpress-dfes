<?php
/**
 * Class: Featured_Image_Url
 *
 * @return l'url de l'image en avant (Featured image) de l'article courant
 * pour créer un lien vers cette image
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Featured_Image_Url extends Data_Tag {
	public function get_name(): string {
		return 'eac-addon-featured-url';
	}

	public function get_title(): string {
		return esc_html__( 'Featured image', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-url' );
	}

	public function get_categories(): array {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_value( array $options = array() ): string {
		$thumb_id = get_post_thumbnail_id();
		if ( $thumb_id && 0 !== $thumb_id ) {
			$id  = get_post( $thumb_id );
			$url = get_permalink( $id->ID );
			if ( $url ) {
				return esc_url( $url );
			}
		}

		return '';
	}
}
