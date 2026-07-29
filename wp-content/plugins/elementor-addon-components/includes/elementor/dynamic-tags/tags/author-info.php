<?php
/**
 * Class: Author_Info
 *
 * @return affiche selon la sélection, la bio, l'email, l'URL du site web ou une méta donnée
 * de l'auteur de l'article courant
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Author_Info extends Tag {

	private const VALS_LENGTH = 25;

	public function get_name(): string {
		return 'eac-addon-author-info';
	}

	public function get_title(): string {
		return esc_html__( 'Author info', 'eac-components' );
	}

	public function get_group(): array {
		return array( 'eac-author-groupe' );
	}

	public function get_categories(): array {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		);
	}

	public function get_panel_template_setting_key(): string {
		return 'author_info_type';
	}

	protected function register_controls(): void {
		$this->add_control(
			'author_info_type',
			array(
				'label'   => esc_html__( 'Field', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''            => esc_html__( 'Select...', 'eac-components' ),
					'role'        => esc_html__( 'Role', 'eac-components' ),
					'description' => esc_html( 'Bio' ),
					'email'       => esc_html( 'Email' ),
					'url'         => esc_html__( 'Website', 'eac-components' ),
					'meta'        => esc_html__( 'Meta author', 'eac-components' ),
				),
			)
		);

		$this->add_control(
			'author_info_meta_key',
			array(
				'label'     => esc_html__( 'Key', 'eac-components' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_author_metas(),
				'default'   => 'nickname',
				'condition' => array( 'author_info_type' => 'meta' ),
			)
		);
	}

	public function render(): void {
		// Allow HTML in author bio section
		// remove_filter('pre_user_description', 'wp_filter_kses');
		$value = '';

		$key = $this->get_settings( 'author_info_type' );

		if ( empty( $key ) ) {
			return;
		}

		if ( 'meta' === $key ) {
			$meta = $this->get_settings( 'author_info_meta_key' );
			if ( ! empty( $meta ) ) {
				$value = get_the_author_meta( $meta );
			}
		} elseif ( 'role' === $key ) {
			$user_info = new \WP_User( get_the_author_meta( 'ID' ) );
			if ( ! empty( $user_info->roles ) && is_array( $user_info->roles ) ) {
				$value = implode( ', ', $user_info->roles );
			}
		} else {
			$value = get_the_author_meta( $key );
		}

		echo wp_kses_post( $value );
	}

	/**
	 * Retourne la liste des métadonnées de l'auteur de l'article courant
	 */
	public function get_author_metas() {
		global $authordata;
		$list             = array();
		$user_meta_fields = Eac_Tools_Util::get_supported_user_meta_field();

		/**
		 * La variable globale n'est pas définie
		 */
		if ( ! isset( $authordata->ID ) ) {
			$post = get_post();
			if ( $post ) {
				$authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		// Global $authordata n'est pas instanciée
		if ( ! isset( $authordata->ID ) ) {
			return $list;
		}

		$authormetas = array_map(
			function ( $a ) {
				return $a[0];
			},
			get_user_meta( $authordata->ID, '', true )
		);

		foreach ( $authormetas as $key => $vals ) {
			if ( ! is_serialized( $vals ) && '' !== $vals && '_' !== $key[0] && in_array( $key, $user_meta_fields, true ) ) {
				if ( mb_strlen( $vals, 'UTF-8' ) > self::VALS_LENGTH ) {
						$list[ $key ] = $key . '::' . mb_substr( $vals, 0, self::VALS_LENGTH, 'UTF-8' ) . '...';
				} else {
					$list[ $key ] = $key . '::' . $vals;
				}
			}
		}
		ksort( $list );
		return $list;
	}
}
