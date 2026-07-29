<?php
/**
 * Class: Page_Title
 *
 * @return affiche le titre de la page
 * @since 2.0.2
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Page_Title extends Tag {
	use \EACCustomWidgets\Includes\Traits\Page_Title_Trait;

	public function get_name(): string {
		return 'eac-addon-page-title';
	}

	public function get_title(): string {
		return esc_html__( 'Page title', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-site-groupe' );
	}

	public function get_categories(): array {
		return array( TagsModule::TEXT_CATEGORY );
	}

	protected function register_controls(): void {

		$this->add_control(
			'page_title_context',
			array(
				'label'   => esc_html__( 'Include context', 'eac-components' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'yes' => array(
						'title' => esc_html__( 'Yes', 'eac-components' ),
						'icon'  => 'eicon-check',
					),
					'no'  => array(
						'title' => esc_html__( 'No', 'eac-components' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'no',
			)
		);
	}

	public function render(): void {
		$has_context = 'yes' === $this->get_settings( 'page_title_context' ) ? true : false;

		$title = $this->get_page_title( $has_context );
		echo esc_html( $title );
	}
}
