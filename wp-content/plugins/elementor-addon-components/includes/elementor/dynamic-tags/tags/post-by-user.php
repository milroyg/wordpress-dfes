<?php
/**
 * Class: Post_By_User
 *
 * @return un tableau d'options de la liste de tous les auteurs (display_name) par leur ID
 * @since 1.6.0
 * @since 1.9.2 Rapatrie la méthode 'get_all_authors' de l'objet 'eac-dynamic-tags'
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

class Post_By_User extends Tag {

	public function get_name(): string {
		return 'eac-addon-post-user';
	}

	public function get_title(): string {
		return esc_html__( 'Authors', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-post' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'author_custom_field';
	}

	protected function register_controls(): void {

		$this->add_control(
			'author_custom_field',
			array(
				'label'       => esc_html__( 'Key', 'eac-components' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_custom_keys_array(),
			)
		);
	}

	public function render(): void {
		$key = $this->get_settings( 'author_custom_field' );

		if ( empty( $key ) ) {
			return;
		}
		echo implode( ',', $key ); // phpcs:ignore
	}

	private function get_custom_keys_array(): array {
		$all_authors = array();
		$options     = array();

		$all_authors = $this->get_all_authors(); // Authors

		if ( ! empty( $all_authors ) ) {
			foreach ( $all_authors as $key => $value ) {
				$options[ $key ] = ucfirst( $value ); // $options[ID de l'author] = display_name
			}
		}
		return $options;
	}

	/**
	 * Retourne la liste de tous les users du blog par leur ID et nom
	 *
	 * @since 1.6.0 Vérifier le niveau des droits (roles)
	 * @since 1.9.2 Rapatrie la méthode 'get_all_authors'
	 */
	private function get_all_authors(): array {
		$list  = array();
		$users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );

		// Boucle sur Array of stdClass objects.
		foreach ( $users as $user ) {
			$list[ $user->ID ] = esc_html( $user->display_name );
		}
		ksort( $list );
		return $list;
	}
}
