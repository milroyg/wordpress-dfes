<?php
/**
 * Class: Eac_Select2_Actions
 *
 * Description: Charge les actions du control 'eac-select2'
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Elementor\Controls;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Select2_Actions {

	/**
	 * Constructeur
	 */
	public function __construct() {
		add_action( 'wp_ajax_autocomplete_ajax', array( $this, 'autocomplete_ajax' ) );
		add_action( 'wp_ajax_autocomplete_ajax_reload', array( $this, 'autocomplete_ajax_reload' ) );
	}

	/**
	 * autocomplete_ajax
	 *
	 * Action qui recouvre la liste des articles/taxonomies par leur titre et leur ID
	 *
	 * @param sanitize_text_field String la chaine de la recherche
	 * @param object_type String le nom du type d'article
	 * @param query_type String le type de recherche
	 * @param query_taxo String La taxonomie recherchée
	 *
	 * @return Array of objects {"id": id, "text": text}
	 */
	public function autocomplete_ajax() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'autocomplete_ajax_nonce' ) ) {
			$error[] = array(
				'id'   => 0,
				'text' => esc_html__( 'Security error', 'eac-components' ),
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		global $wpdb;
		// Chaine à rechercher
		$search = sanitize_text_field( wp_unslash( $_POST['search'] ) );

		// post, page, product... et 'any' pour tous les post_types
		if ( ! empty( $_POST['object_type'] ) ) {
			$raw = wp_unslash( $_POST['object_type'] );
			if ( ! is_array( $raw ) ) {
				$post_type = sanitize_text_field( $raw );
			} else {
				$post_type = array_map( 'sanitize_text_field', $raw );
			}
		}

		// taxonomy, term, author, url. défaut 'post'
		$query_type = sanitize_text_field( wp_unslash( $_POST['query_type'] ) );

		// category, post_tag, product_cat, product_tag, pa_xxxxx (attribute: pa_tissu)
		if ( ! empty( $_POST['query_taxo'] ) ) {
			$raw = wp_unslash( $_POST['query_taxo'] );
			if ( ! is_array( $raw ) ) {
				$query_taxo = array( sanitize_text_field( $raw ) );
			} else {
				$query_taxo = array_map( 'sanitize_text_field', $raw );
			}
		}

		// Nombre d'entrées
		$query_limit = 40;

		$suggestions = array();
		$objects     = array();
		$taxonomies  = array();
		$terms       = array();
		$list_author = array();
		$list_url    = array();

		if ( 'any' === $post_type ) {
			$all_post_type = \EACCustomWidgets\Core\Utils\Eac_Tools_Util::get_all_post_types();
			if ( ! empty( $all_post_type ) ) {
				foreach ( $all_post_type as $post_type_name => $post_type_label ) {
					if ( \str_contains( strtolower( $post_type_label ), $search ) ) {
						$suggestions[] = array(
							'id'   => esc_attr( $post_type_name ),
							'text' => esc_attr( $post_type_label ),
						);
					}
				}
				usort( $suggestions, function ( $a, $b ) {
					return $a['text'] <=> $b['text'];
				} );
				wp_send_json_success( wp_json_encode( $suggestions ) );
			}

			$error[] = array(
				'id'   => 0,
				'text' => esc_html__( 'No result found', 'eac-components' ),
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		switch ( $query_type ) {
			case 'post':
				$where = '';
				$where = sprintf( "AND post_type = '%s'", $post_type );
				if ( ! empty( $search ) ) {
					$where .= sprintf( " AND post_title LIKE '%s'", '%' . $wpdb->esc_like( $search ) . '%' );
				}
				$limit = sprintf( 'ORDER BY post_title LIMIT %d', $query_limit );

				$query = "SELECT ID, post_title from {$wpdb->prefix}posts where post_status = 'publish' $where $limit";

				$objects = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				break;
			case 'taxonomy':
				// @since 2.3.7 object vs names
				$taxonomies = get_object_taxonomies( $post_type, 'objects' );
				break;
			case 'term':
				$args = array(
					'taxonomy'   => '',
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
				);

				if ( ! empty( $search ) ) {
					$args['name__like'] = $search;
				}

				if ( ! empty( $query_taxo ) ) {
					$args['taxonomy'] = $query_taxo;
				} else {
					unset( $args['taxonomy'] );
				}

				$terms = get_terms( $args );
				break;
			case 'author':
				$users = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT ID, display_name
						FROM {$wpdb->prefix}users
						WHERE display_name LIKE %s",
						'%' . $wpdb->esc_like( $search ) . '%' // Le LIKE peut être vide retourne toutes les lignes
					)
				);

				if ( $users && ! empty( $users ) ) {
					foreach ( $users as $user ) {
						$list_author[ $user->ID ] = ucfirst( $user->display_name );
					}
				}
				break;
			case 'url':
				$list_url = $this->get_post_type_data( $search );
				break;
		}

		if ( ! empty( $objects ) ) {
			foreach ( $objects as $result ) {
				$thumbnail     = '<span>' . get_the_post_thumbnail( $result->ID, array( 20, 20 ), array( 'class' => 'mega-menu_item-thumb' ) ) . '</span>';
				$suggestions[] = array(
					'id'   => esc_attr( $result->ID ),
					'text' => esc_attr( $result->post_title ),
				);
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $taxonomies ) && ! empty( $taxonomies ) ) {
			// @since 2.3.7
			foreach ( $taxonomies as $taxonomy ) {
				// Si $search est vide str_contains retourne toujouts true
				if ( \str_contains( strtolower( $taxonomy->label ), $search ) ) {
					$suggestions[] = array(
						'id'   => esc_attr( $taxonomy->name ),
						'text' => esc_attr( $taxonomy->label ),
					);
				}
			}
			usort( $suggestions, function ( $a, $b ) {
				return strcasecmp( $a['text'], $b['text'] );
			} );
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$groups = array();
			$taxonomies_object = get_object_taxonomies( $post_type, 'objects' );

			if ( ! is_wp_error( $taxonomies_object ) && ! empty( $taxonomies_object ) ) {
				foreach ( $taxonomies_object as $taxonomy ) {
					$suggestions = array();
					foreach ( $terms as $term ) {
						if ( $term->taxonomy === $taxonomy->name ) {
							$suggestions[] = array(
								'id'   => esc_attr( $term->slug ),
								'text' => esc_attr( $term->name ),
							);
						}
					}
					if ( empty( $suggestions ) ) {
						continue;
					}

					usort( $suggestions, function ( $a, $b ) {
						return strcasecmp( $a['text'], $b['text'] );
					} );

					$groups[] = array(
						'id'       => esc_attr( $taxonomy->name ),
						'text'     => esc_attr( $taxonomy->labels->singular_name ),
						'children' => $suggestions,
					);
				}
				usort( $groups, function ( $a, $b ) {
					return strcasecmp( $a['text'], $b['text'] );
				} );
			}
			wp_send_json_success( wp_json_encode( $groups ) );
		} elseif ( ! empty( $list_author ) ) {
			foreach ( $list_author as $key => $name ) {
				$suggestions[] = array(
					'id'   => esc_attr( $key ),
					'text' => esc_attr( $name ),
				);
			}
			usort( $suggestions, function ( $a, $b ) {
				return strcasecmp( $a['text'], $b['text'] );
			} );
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! empty( $list_url ) ) {
			wp_send_json_success( wp_json_encode( $list_url ) );
		}

		$error[] = array(
			'id'   => 0,
			'text' => esc_html__( 'No result found', 'eac-components' ),
		);
		wp_send_json_error( wp_json_encode( $error ) );
	}

	/**
	 * autocomplete_ajax_reload
	 *
	 * Action qui recouvre la liste des articles/taxonomies par leur titre et leur ID
	 *
	 * @param search Array ou String la liste des id/name à recouvrir
	 * @param object_type String le nom du type d'article
	 * @param query_type String le type de recherche
	 * @param query_taxo String La taxonomie recherchée
	 *
	 * @return Array of objects {"id": id, "text": text}
	 */
	public function autocomplete_ajax_reload() {
		global $wpdb;

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'autocomplete_ajax_nonce' ) ) {
			$error[] = array(
				'id'   => 0,
				'text' => esc_html__( 'Security error', 'eac-components' ),
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		if ( ! isset( $_POST['search'] ) || empty( $_POST['search'] ) ) {
			$error[] = array(
				'id'   => 0,
				'text' => '',
			);
			wp_send_json_error( wp_json_encode( $error ) );
		}

		$search = is_array( $_POST['search'] ) ? $_POST['search'] : explode( ',', $_POST['search'] );
		$search = array_map( 'sanitize_text_field', $search );

		if ( ! empty( $_POST['object_type'] ) ) {
			$raw = wp_unslash( $_POST['object_type'] );
			if ( ! is_array( $raw ) ) {
				$post_type = sanitize_text_field( $raw );
			} else {
				$post_type = array_map( 'sanitize_text_field', $raw );
			}
		}

		// post ou taxonomy
		$query_type = sanitize_text_field( wp_unslash( $_POST['query_type'] ) );

		// category, post_tag, product_cat, product_tag, pa_xxxxx (attribute: pa_tissu)
		if ( ! empty( $_POST['query_taxo'] ) ) {
			$raw = wp_unslash( $_POST['query_taxo'] );
			if ( ! is_array( $raw ) ) {
				$query_taxo = array( sanitize_text_field( $raw ) );
			} else {
				$query_taxo = array_map( 'sanitize_text_field', $raw );
			}
		}

		$suggestions = array();
		$objects     = array();
		$taxonomies  = array();
		$terms       = array();
		$list_author = array();
		$list_url    = array();

		if ( 'any' === $post_type ) {
			foreach ( $search as $post_type ) {
				$the_post_type = get_post_type_object( $post_type );
				if ( $the_post_type ) {
					$suggestions[] = array(
						'id'   => esc_attr( $the_post_type->name ),
						'text' => esc_attr( $the_post_type->labels->singular_name ),
					);
				}
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		}

		switch ( $query_type ) {
			case 'post':
				$objects = get_posts(
					array(
						'post_type' => $post_type,
						'post__in'  => $search,
					)
				);
				break;
			case 'taxonomy':
				foreach ( $search as $name ) {
					$taxonomies[] = get_taxonomies( array( 'name' => $name ), 'objects' );
				}
				break;
			case 'term':
				$term_ids = array();
				$all_taxonomy = get_taxonomies( array(), 'names' );
				$search = array_map( function ( $item ) {
					return \str_contains( $item, '::' ) ? explode( '::', $item, 2 )[1] : $item;
				}, $search);

				/** slug to term_id nécessaire pour param 'include' de $args */
				foreach ( $search as $slug ) {
					foreach ( $all_taxonomy as $taxonomy ) {
						$term = get_term_by( 'slug', $slug, $taxonomy );
						if ( $term && ! is_wp_error( $term ) ) {
							$term_ids[] = (string) $term->term_id;
							break;
						}
					}
				}

				$args = array(
					'taxonomy'   => '',
					'hide_empty' => true,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'include'    => $term_ids,
				);

				if ( ! empty( $query_taxo ) ) {
					$args['taxonomy'] = $query_taxo;
				} else {
					unset( $args['taxonomy'] );
				}

				$terms = wp_list_pluck( get_terms( $args ), 'name', 'slug' );
				break;
			case 'author':
				foreach ( $search as $author ) {
					$auteur = get_user_by( 'ID', $author );
					if ( $auteur ) {
						$list_author[ $auteur->ID ] = ucfirst( $auteur->display_name );
					}
				}
				break;
			case 'url':
				$list_url = $this->get_post_type_data( $search );
				break;
		}

		if ( ! is_wp_error( $objects ) && ! empty( $objects ) ) {
			foreach ( $objects as $index => $value ) {
				$suggestions[] = array(
					'id'   => esc_attr( $value->ID ),
					'text' => esc_attr( $value->post_title ),
				);
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $taxonomies ) && ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomie ) {
				foreach ( $taxonomie as $key => $value ) {
					$suggestions[] = array(
						'id'   => esc_attr( $value->name ),
						'text' => esc_attr( $value->label ),
					);
				}
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $key => $name ) {
				$suggestions[] = array(
					'id'   => esc_attr( $key ),
					'text' => esc_attr( $name ),
				);
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! empty( $list_author ) ) {
			foreach ( $list_author as $key => $name ) {
				$suggestions[] = array(
					'id'   => esc_attr( $key ),
					'text' => esc_attr( $name ),
				);
			}
			wp_send_json_success( wp_json_encode( $suggestions ) );
		} elseif ( ! empty( $list_url ) ) {
			wp_send_json_success( wp_json_encode( $list_url ) );
		}

		$error[] = array(
			'id'   => 0,
			'text' => esc_html__( 'No result found', 'eac-components' ),
		);
		wp_send_json_error( wp_json_encode( $error ) );
	}

	private function get_post_type_data( $search ): array {
		$groups     = array();
		$post_types = \EACCustomWidgets\Core\Utils\Eac_Tools_Util::get_filter_post_types();

		foreach ( $post_types as $post_type_name => $post_type_label ) {
			$all_posts = array();
			$options   = array();

			if ( is_string( $search ) ) {
				$all_posts = $this->get_all_post_data( $post_type_name, $search );
			} elseif ( is_array( $search ) && ! empty( $search ) ) {
				$all_posts = $this->get_post_data( $post_type_name, $search );
			}

			if ( ! empty( $all_posts ) && ! is_wp_error( $all_posts ) ) {
				foreach ( $all_posts as $the_post ) {
					$options[] = (object) array(
						'id' => absint( $the_post->ID ),
						'text' => esc_attr( $the_post->post_title ),
					);
				}
				if ( empty( $options ) ) {
					continue;
				}
				usort( $options, fn( $a, $b ) => strcmp( $a->text, $b->text ) );
				$groups[] = array(
					'id'       => esc_attr( $post_type_name ),
					'text'     => esc_attr( $post_type_label ),
					'children' => $options,
				);
			}
		}
		return $groups;
	}

	private function get_all_post_data( $post_type, $search ): array {
		global $wpdb;

		$posts_list = get_posts( array(
			'post_type'      => $post_type,
			'post_status'    => array( 'publish' ),
			'posts_per_page' => -1,
			's'              => $search,
			'search_columns' => array( 'post_title' ),
		) );
		return $posts_list;
	}

	private function get_post_data( $post_type, $search ): array {
		global $wpdb;
		$search = implode( ',', array_map( 'absint', $search ) );

		$posts_list = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title, post_name
				FROM {$wpdb->prefix}posts
				WHERE post_type = %s
				AND ID IN ({$search})
				AND post_status = 'publish'",
				$post_type
			)
		);
		return $posts_list;
	}
}
