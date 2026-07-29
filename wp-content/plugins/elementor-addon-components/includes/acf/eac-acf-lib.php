<?php
/**
 * Class: Eac_Acf_Lib
 *
 * Description: Module ACF pour mettre à disposition les méthodes nécessaires
 * aux balises dynamiques ACF
 *
 * @since 1.7.5
 */

namespace EACCustomWidgets\Includes\Acf;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\Acf\Eac_Acf_Options_Page;

class Eac_Acf_Lib {

	/**
	 * Constructeur de la class
	 *
	 * @access public
	 */
	public function __construct() {
		add_filter( 'acf/pre_load_post_id', array( $this, 'fix_post_id_on_preview' ), 10, 2 );
	}

	/**
	 * fix_post_id_on_preview
	 *
	 * Fix des champs ACF en mode preview qui ne s'affichent pas pour Gutenberg ou Elementor
	 */
	public function fix_post_id_on_preview( $nul, $post_id ) {
		if ( is_preview() ) {
			return ( ( empty( $post_id ) || is_null( $post_id ) ) ? get_the_ID() : get_the_ID() === $post_id ) ? get_the_ID() : $post_id;
		}

		if ( $post_id instanceof \WP_Post ) {
			return $post_id->ID;
		}
		return $nul;
	}

	/**
	 * get_acf_repeater_field
	 *
	 * @param string $post_id
	 *
	 * @return array
	 */
	public static function get_acf_repeater_field( $post_id = '' ): array {
		$postid         = empty( $post_id ) ? get_the_ID() : $post_id;
		$options        = array( '' => esc_html__( 'Select...', 'eac-components' ) );
		$acf_groups     = array();
		$acf_groups_pt  = array();
		$acf_groups_cpt = array();

		$acf_groups_pt = acf_get_field_groups( array( 'post_id' => $postid ) );

		if ( class_exists( Eac_Acf_Options_Page::class, false ) ) {
			$acf_groups_cpt = Eac_Acf_Options_Page::get_acf_field_groups();
		}

		$acf_groups = array_merge( $acf_groups_cpt, $acf_groups_pt );

		foreach ( $acf_groups as $group ) {
			if ( ! $group['active'] ) {
				continue;
			}

			if ( isset( $group['ID'] ) && ! empty( $group['ID'] ) ) {
				$fields = acf_get_fields( $group['ID'] );
			} else {
				$fields = acf_get_fields( $group );
			}

			// Pas de champ
			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field ) {
				if ( in_array( $field['type'], array( 'eac_repeater' ), true ) ) {
					$options[ esc_attr( $field['key'] ) ] = esc_html( $field['label'] );
				}
			}
		}
		return $options;
	}

	/**
	 * get_acf_fields_options
	 *
	 * @param array  $field_types Les types de champ pour lequel les données seront retournées
	 * @param string $post_id l'ID du post
	 * @param string $add_group Ajouter les groupes ACF 'none', 'group' ou 'relational'
	 *
	 * @return array La liste des champs (Clé/Label) des groupes ACF et du type de champ 'GROUP' dans les groupes ACF
	 */
	public static function get_acf_fields_options( $field_types, $post_id = '', $add_group = 'none' ): array {
		$postid         = empty( $post_id ) ? get_the_ID() : $post_id;
		$acf_groups     = array();
		$acf_groups_pt  = array();
		$acf_groups_cpt = array();
		$groups         = array(
			array(
				'label'   => esc_html__( '_None', 'eac-components' ),
				'options' => array( '' => esc_html__( 'Select...', 'eac-components' ) ),
			),
		);

		/**$trace = debug_backtrace();
		// Affiche le nom de la fonction qui a appelé maMethode
		if ( isset( $trace[1] ) ) {
			write_log( 'Appelée par => ' . $trace[1]['class'] . '::' . $trace[1]['function'] );
		}*/

		$acf_groups_pt = acf_get_field_groups( array( 'post_id' => $postid ) );

		if ( class_exists( Eac_Acf_Options_Page::class, false ) ) {
			$acf_groups_cpt = Eac_Acf_Options_Page::get_acf_field_groups();
		}

		$acf_groups = array_merge( $acf_groups_cpt, $acf_groups_pt );

		foreach ( $acf_groups as $group ) {
			$options = array();

			// Le groupe est actif
			if ( ! $group['active'] ) {
				continue;
			}

			if ( isset( $group['ID'] ) && ! empty( $group['ID'] ) ) {
				$fields = acf_get_fields( $group['ID'] );
			} else {
				$fields = acf_get_fields( $group );
			}

			// Pas de champ
			if ( ! is_array( $fields ) ) {
				continue;
			}

			/**
			 * none       = récupère que les champs de premier niveau
			 * group      = récupère que les champs inclus dans un type de champ GROUP
			 * relational = récupère tous les champs directs et dans un type de champ GROUP. Uniquement utilisé par le widget acf-relationship.php
			 */
			foreach ( $fields as $field ) {
				if ( in_array( $field['type'], $field_types, true ) && in_array( $add_group, array( 'none', 'relational' ), true ) ) {
					$key             = $field['key'] . '::' . $field['name'];
					$options[ esc_attr( $key ) ] = esc_html( $field['label'] );
				} elseif ( in_array( $field['type'], array( 'group' ), true ) && in_array( $add_group, array( 'group', 'relational' ), true ) ) {
					foreach ( $field['sub_fields'] as $sub_field ) {
						if ( in_array( $sub_field['type'], $field_types, true ) ) {
							// Pour maintenir la compatibilité ascendante
							if ( 'relational' === $add_group ) {
								$key = $sub_field['key'] . '::' . $sub_field['name'];
							} else {
								$key = $field['key'] . '::' . $sub_field['key'] . '::' . $sub_field['name'];
							}
							$options[ esc_attr( $key ) ] = esc_html( $sub_field['label'] );
						} elseif ( in_array( $sub_field['type'], array( 'group' ), true ) ) {
							foreach ( $sub_field['sub_fields'] as $nested_field ) {
								if ( in_array( $nested_field['type'], $field_types, true ) ) {
									$key = $sub_field['key'] . '::' . $nested_field['key'] . '::' . $nested_field['name'];
									$options[ esc_attr( $key ) ] = esc_html( $nested_field['label'] );
								}
							}
						}
					}
				}
			}
			if ( empty( $options ) ) {
				continue;
			}

			$groups[] = array(
				'label'   => esc_html( $group['title'] ),
				'options' => $options,
			);
		}

		usort( $groups, function ( $a, $b ) {
			return strcasecmp( $a['label'], $b['label'] );
		} );

		return $groups;
	}

	/**
	 * get_acf_field_name
	 *
	 * Retourne le field name complet d'un champ de type group 'field_group_key_field_key'
	 * Ou d'un groupe imbriqué dans un groupe
	 *
	 * @param $metavalue    La meta_value recherchée (field_xxxx)
	 * @param $metakey      La meta_key recherchée (field_name)
	 * @param $postid       L'ID de l'article
	 * @return string
	 */
	public static function get_acf_field_name( $metavalue, $metakey, $postid ): string {
		global $wpdb;
		$name = '';

		$meta_key = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT meta_key
				FROM {$wpdb->prefix}postmeta
				WHERE meta_value = %s
				AND post_id = %d
				AND meta_key LIKE %s",
				$metavalue,
				$postid,
				'%' . $wpdb->esc_like( $metakey )
			)
		);

		if ( $meta_key && ! empty( $meta_key ) && count( $meta_key ) === 1 ) { // Il ne doit y avoir qu'une seule meta_key
			$name = substr( current( (array) $meta_key )->meta_key, 1, 999 ); // Supprime l'underscore du début de la donnée
		}
		return $name;
	}
}
