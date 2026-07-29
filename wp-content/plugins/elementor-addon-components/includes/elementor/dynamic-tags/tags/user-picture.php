<?php
/**
 * Class: User_Picture
 *
 * @return l'URL de l'avatar/gravatar de l'utilisateur courant logué
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class User_Picture extends Data_Tag {

	public function get_name(): string {
		return 'eac-addon-user-profile-picture';
	}

	public function get_title(): string {
		return esc_html__( 'User photo', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-author-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	protected function register_controls(): void {
		$this->add_control(
			'user_picture_size',
			array(
				'label'   => esc_html__( 'Size', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '96',
				'options' => array(
					'50'  => '50',
					'60'  => '60',
					'80'  => '80',
					'96'  => '96',
					'120' => '120',
					'140' => '140',
				),
			)
		);
	}
	public function get_value( array $options = array() ): array {
		$size = $this->get_settings( 'user_picture_size' );

		return array(
			'url' => get_avatar_url( (int) get_current_user_id(), array( 'size' => $size ) ),
			'id'  => '',
		);
	}
}
