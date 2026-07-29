<?php
/**
 * Class: User_Info
 *
 * @return affiche la valeur d'une métadonnée pour l'utilisateur courant logué
 * @since 1.6.0
 * @since 1.6.1 Ajout du rôle dans la liste des informations de l'utilisateur
 * @since 1.9.0 'id' deprecated use 'ID'
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class User_Info extends Tag {

	private const VALS_LENGTH = 25;

	public function get_name(): string {
		return 'eac-addon-user-info';
	}

	public function get_title(): string {
		return esc_html__( 'User info', 'eac-components' );
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
		return 'user_info_type';
	}

	protected function register_controls(): void {
		$this->add_control(
			'user_info_type',
			array(
				'label'   => esc_html__( 'Field', 'eac-components' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''            => esc_html__( 'Select...', 'eac-components' ),
					'ID'          => esc_html( 'ID' ),
					'role'        => esc_html__( 'Role', 'eac-components' ),
					'nickname'    => esc_html__( 'Nickname', 'eac-components' ),
					'login'       => esc_html__( 'Login ident', 'eac-components' ),
					'first_name'  => esc_html__( 'First name', 'eac-components' ),
					'last_name'   => esc_html__( 'Name', 'eac-components' ),
					'description' => esc_html( 'Bio' ),
					'email'       => esc_html( 'Email' ),
					'url'         => esc_html__( 'Website', 'eac-components' ),
					'meta'        => esc_html__( 'User meta', 'eac-components' ),
				),
			)
		);

		$this->add_control(
			'user_info_meta_key',
			array(
				'label'     => esc_html__( 'Key', 'eac-components' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_user_metas(),
				'default'   => 'nickname',
				'condition' => array( 'user_info_type' => 'meta' ),
			)
		);
	}

	public function render(): void {
		$type = $this->get_settings( 'user_info_type' );
		$user = wp_get_current_user();

		// User non logué
		if ( empty( $type ) || 0 === $user->ID ) {
			esc_html_e( 'Not logged-in', 'eac-components' );
			return;
		}

		$value = '';
		switch ( $type ) {
			case 'email':
				$field = 'user_' . $type;
				$value = isset( $user->$field ) ? sanitize_email( $user->$field ) : '';
				break;
			case 'url':
				$field = 'user_' . $type;
				$value = isset( $user->$field ) ? esc_url( $user->$field ) : '';
				break;
			case 'login':
			case 'nicename':
				$field = 'user_' . $type;
				$value = isset( $user->$field ) ? esc_html( $user->$field ) : '';
				break;
			case 'ID':
			case 'description':
			case 'first_name':
			case 'last_name':
			case 'nickname':
				$value = isset( $user->$type ) ? esc_html( $user->$type ) : '';
				break;
			case 'meta':
				$key = $this->get_settings( 'user_info_meta_key' );
				if ( ! empty( $key ) ) {
					$value = get_user_meta( $user->ID, $key, true );
				}
				break;
			case 'role': // @since 1.6.1
				$user_info = get_userdata( $user->ID );
				$value    = implode( ', ', array_map( 'esc_html', $user_info->roles ) );
				break;
		}

		echo wp_kses_post( $value );
	}

	/**
	 *
	 * Retourne la liste des métadonnées de l'utilisateur courant si il est logué
	 */
	public function get_user_metas() {
		$list             = array();
		$current_user     = wp_get_current_user();
		$user_meta_fields = Eac_Tools_Util::get_supported_user_meta_field();

		// User non logué
		if ( 0 === $current_user->ID ) {
			return $list;
		}

		$usermetas = array_map(
			function ( $a ) {
				return $a[0];
			},
			get_user_meta( $current_user->ID, '', true )
		);

		foreach ( $usermetas as $key => $vals ) {
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
