<?php
/**
 * Class: Url_Audio
 *
 * @return l'URL absolue d'un fichier MEDIA au format txt mpeg json
 *
 * @since 2.3.6
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

/**
 * Post Url
 */
class Url_Audio extends Data_Tag {

	public function get_name() {
		return 'eac-addon-audio-url';
	}

	public function get_title() {
		return 'Audio';
	}

	public function get_group() {
		return 'eac-url';
	}

	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	public function get_panel_template_setting_key() {
		return 'audio_url';
	}

	protected function register_controls() {
		$this->add_control(
			'audio_url',
			array(
				'label'   => esc_html__( 'Audio Url', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_all_audio_url(),
			)
		);
	}

	protected function get_value( array $options = array() ) {
		$param_name = $this->get_settings( 'audio_url' );
		return wp_kses_post( $param_name );
	}

	/**
	 * get_all_audio_url
	 *
	 * @param string $posttype
	 *
	 * @return array
	 */
	private function get_all_audio_url( $posttype = 'attachment' ): array {
		$post_list = array( '' => esc_html__( 'Select...', 'eac-components' ) );

		$attachments = get_posts(
			array(
				'post_type'      => $posttype,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'post_mime_type' => 'audio/mpeg',
				'post_parent'    => null,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		if ( ! empty( $attachments ) && ! is_wp_error( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				$post_list[ esc_url( wp_get_attachment_url( $attachment->ID ) ) ] = esc_html( $attachment->post_title );
			}
		}
		return $post_list;
	}
}
