<?php
/**
 * Class: Eac_Acf_Options_Page
 *
 * Description: Construit et expose un nouveau type d'article
 * utilisé comme support pour les pages d'options ACF
 *
 * @since 1.8.4
 */

namespace EACCustomWidgets\Includes\Acf;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Config_Elements;

/**
 * Administrateur ou nouvelle capacité
 * Check capacité 'edit_page_options'
 * $setting_page   = isset( $_GET['page'] ) && EAC_DOMAIN_NAME === $_GET['page'];
 */
$option_capa = get_option( Eac_Config_Elements::get_option_page_capability_name() );
if ( ! current_user_can( 'manage_options' ) && ( ! $option_capa || ! current_user_can( $option_capa ) ) ) {
	return;
}

class Eac_Acf_Options_Page {

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * @var $acf_post_type
	 *
	 * Le libellé du type d'article
	 */
	private static $acf_post_type = 'eac_options_page';

	/**
	 * @var $options_page_name
	 *
	 * Le libellé de la page d'options
	 */
	private static $options_page_name = 'eac_options_page-';

	/**
	 * instance.
	 *
	 * Garantir une seule instance de la class
	 *
	 * @return Eac_Acf_Options_Page une instance de la class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructeur de la class
	 *
	 * @access private
	 */
	public function __construct() {
		// Construction du post_type
		add_action( 'init', array( $this, 'register_post_type_option_page' ) );

		// Ajout du sous-menu
		add_action( 'admin_menu', array( $this, 'add_submenu_to_admin_menu' ) );

		// Ajout des colonnes et des données dans la vue du post_type
		add_filter( 'manage_' . self::$acf_post_type . '_posts_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_' . self::$acf_post_type . '_posts_custom_column', array( $this, 'data_columns' ), 10, 2 );

		// L'article page d'options est enregistré
		add_action( 'save_post_' . self::$acf_post_type, array( $this, 'save_options_page' ), 10, 2 );

		// L'article est mis dans la poubelle
		add_action( 'wp_trash_post', array( $this, 'delete_options_page' ), 10 );

		// Champ ACF supprimé. C'est l'action suivante qui fait le job
		// add_action('acf/delete_field', array($this, 'delete_acf_field'));

		// Groupe ACF modifié
		add_filter( 'acf/update_field_group', array( $this, 'update_acf_field_group' ) );

		// Groupe ACF est mis dans la poubelle
		add_action( 'acf/trash_field_group', array( $this, 'delete_acf_group' ) );
	}

	/**
	 * update_acf_field_group
	 *
	 * Modifie toutes les options dans la table des options pour ce groupe
	 * Déclenchée par l'action ACF 'update_field_group'
	 *
	 * @var $group Le groupe cible à mettre à jour
	 */
	public function update_acf_field_group( $group_updated ) {
		// Récupère tous les articles du post type
		$articles = get_posts(
			array(
				'post_type'      => self::$acf_post_type,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			)
		);

		// Boucle sur tous les articles pour à nouveau sauvegarder les options
		if ( ! empty( $articles ) && ! is_wp_error( $articles ) ) {
			foreach ( $articles as $article ) {
				// Tous les groupes de champs pour cette article
				$groups = acf_get_field_groups( array( 'post_id' => $article->ID ) );
				foreach ( $groups as $group ) {
					if ( is_array( $group ) && ! empty( $group ) && $group['ID'] === $group_updated['ID'] ) {
						$this->save_options_page( $article->ID, $article );
					}
				}
			}
		}
	}

	/**
	 * delete_acf_group
	 *
	 * Supprime toutes les options (eac_options) des champs d'un groupe
	 * Déclenchée par l'action ACF 'trash_field_group'
	 *
	 * @var $group Le groupe de champ ACF mis à la poubelle
	 */
	public function delete_acf_group( $group ) {
		$fields = acf_get_fields( $group['ID'] );
		// Pas de champ
		if ( ! is_array( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			$this->delete_acf_field( $field );
		}
	}

	/**
	 * delete_acf_field
	 *
	 * Supprime une option d'une page d'options dans la table 'eac_options'
	 *
	 * @var $field L'objet champ à supprimer
	 */
	public function delete_acf_field( $field ) {
		global $wpdb;
		$key = $field['key'];

		/**
		 * C'est un champ de type group
		 * On le supprime ainsi que tous les sous-champs
		 */
		if ( 'group' === $field['type'] ) {
			$option = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT option_name
					FROM {$wpdb->prefix}options
					WHERE option_name LIKE %s",
					'%-' . $wpdb->esc_like( $key )
				)
			);

			if ( $option && ! empty( $option ) && count( $option ) === 1 ) {
				$option_name = reset( $option )->option_name;
				delete_option( $option_name );
			}

			foreach ( $field['sub_fields'] as $sub_field ) {
				$key = $sub_field['key'];

				$option = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT option_name
						FROM {$wpdb->prefix}options
						WHERE option_name LIKE %s",
						'%-' . $wpdb->esc_like( $key )
					)
				);

				if ( $option && ! empty( $option ) && count( $option ) === 1 ) {
					$option_name = reset( $option )->option_name;
					delete_option( $option_name );
				}
			}
		} else {
			$option = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT option_name
					FROM {$wpdb->prefix}options
					WHERE option_name LIKE %s",
					'%-' . $wpdb->esc_like( $key )
				)
			);

			if ( $option && ! empty( $option ) && count( $option ) === 1 ) {
				$option_name = reset( $option )->option_name;
				delete_option( $option_name );
			}
		}
	}

	/**
	 * save_options_page
	 *
	 * Enregistre les champs ACF d'une page d'options dans la table 'eac_options'
	 * Trigger par WordPress 'save_post'
	 *
	 * @var $post_id ID de l'article
	 * @var $post l'objet article
	 */
	public function save_options_page( $post_id, $post ) {
		$post_title  = $post->post_title;
		$post_type   = $post->post_type;
		$post_status = $post->post_status;

		/** if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; } */

		// Ce n'est pas le type d'article attendu
		if ( self::$acf_post_type !== $post_type ) {
			return;
		}

		// L'article n'est pas publié
		if ( 'publish' !== $post_status ) {
			return;
		}

		/**
		 * Supprime toutes les options systématiquement
		 * dû au fait que la règle ACF du groupe a pu être changée
		 * ou que le groupe a été modifié (Méthode: update_acf_field_group)
		 */
		$this->delete_all_options_page( $post_id );

		$args = array(
			'id'           => '',
			'parent_group' => '',
			'title'        => $post_title,
			'name'         => '',
			'meta_key'     => '',
			'field_key'    => '',
			'field_type'   => '',
		);

		// Tous les groupes de champs pour cette article
		$groups = acf_get_field_groups( array( 'post_id' => $post_id ) );

		foreach ( $groups as $group ) {
			$continue_loop = false;

			// Le groupe n'est pas désactivé
			if ( ! $group['active'] ) {
				continue;
			}

			// Les règles
			foreach ( $group['location'] as $locations ) {
				if ( empty( $locations ) ) {
					continue;
				}

				foreach ( $locations as $rule ) {
					if ( 'post_type' === $rule['param'] && '==' === $rule['operator'] && self::$acf_post_type === $rule['value'] ) {
						$continue_loop = true;
					}
				}
			}

			if ( false === $continue_loop ) {
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

			$option_name = self::$options_page_name . $post_id . '-' . $group['key'];

			// La clé n'est pas utilisée dans une autre page d'options
			if ( empty( self::get_options_page_id( $group['key'] ) ) ) {
				$args['id']         = sanitize_text_field( $group['ID'] );
				$args['title']      = sanitize_text_field( $group['title'] );
				$args['field_key']  = sanitize_text_field( $group['key'] );
				$args['field_type'] = 'group';

				// Ajoute l'option du group principal
				update_option( $option_name, $args, false );
			}

			// Pour tous les champs fils
			$args['id'] = $post_id;

			foreach ( $fields as $field ) {
				// Le type du champ est un group
				if ( 'group' === $field['type'] ) {
					$option_name = self::$options_page_name . $post_id . '-' . $field['key'];

					if ( empty( self::get_options_page_id( $field['key'] ) ) ) {
						$args['title']        = sanitize_text_field( $field['label'] );
						$args['parent_group'] = sanitize_text_field( $group['key'] );
						$args['name']         = sanitize_text_field( $field['name'] );
						$args['meta_key']     = sanitize_text_field( $field['name'] );
						$args['field_key']    = sanitize_text_field( $field['key'] );
						$args['field_type']   = sanitize_text_field( $field['type'] );

						// Ajoute l'option du type de champ group
						update_option( $option_name, $args, false );
					}

					$sub_fields = have_rows( $field['key'] ) ? $field['sub_fields'] : false;

					// Parcourt les champs du type group
					if ( $sub_fields ) {
						foreach ( $sub_fields as $sub_field ) {
							$option_name = self::$options_page_name . $post_id . '-' . $sub_field['key'];

							if ( empty( self::get_options_page_id( $sub_field['key'] ) ) ) {
								$args['title']        = sanitize_text_field( $sub_field['label'] );
								$args['parent_group'] = sanitize_text_field( $field['key'] );
								$args['name']         = sanitize_text_field( $sub_field['name'] );
								$args['meta_key']     = sanitize_text_field( $field['name'] ) . '_' . sanitize_text_field( $sub_field['name'] ); // Concaténe le nom du type de groupe et le nom du champ voir 'eac_postmeta'
								$args['field_key']    = sanitize_text_field( $sub_field['key'] );
								$args['field_type']   = sanitize_text_field( $sub_field['type'] );

								// Ajoute l'option du champ
								update_option( $option_name, $args, false );
							}
						}
					}
				} else {
					$option_name = self::$options_page_name . $post_id . '-' . $field['key'];

					if ( empty( self::get_options_page_id( $field['key'] ) ) ) {
						$args['title']        = sanitize_text_field( $field['label'] );
						$args['parent_group'] = sanitize_text_field( $group['key'] );
						$args['name']         = sanitize_text_field( $field['label'] );
						$args['meta_key']     = sanitize_text_field( $field['name'] );
						$args['field_key']    = sanitize_text_field( $field['key'] );
						$args['field_type']   = sanitize_text_field( $field['type'] );

						// Ajoute l'option du champ
						update_option( $option_name, $args, false );
					}
				}
			} // Fields
		} // Group
	}

	/**
	 * delete_options_page
	 *
	 * Supprime une option d'une page d'options dans la table 'eac_options'
	 * Déclenchée par l'action WordPress 'wp_trash_post'
	 *
	 * @var $post_id ID de l'article
	 */
	public function delete_options_page( $post_id ) {
		$post      = get_post( $post_id );
		$post_type = $post->post_type;

		// Ce n'est pas le type d'article attendu
		if ( self::$acf_post_type !== $post_type ) {
			return;
		}

		$fields = get_field_objects( $post_id );

		if ( $fields && ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$option_name = self::$options_page_name . $post_id . '-' . $field['key'];
				delete_option( $option_name );
			}
		}
	}

	/**
	 * delete_all_options_page
	 *
	 * Supprime toutes les options d'une page d'options
	 *
	 * @var $post_id ID de l'article
	 */
	public function delete_all_options_page( $post_id ) {
		global $wpdb;
		$option_name = self::$options_page_name . $post_id;

		$options = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_name
				FROM {$wpdb->prefix}options
				WHERE option_name LIKE %s",
				$wpdb->esc_like( $option_name ) . '%'
			)
		);

		if ( $options && ! empty( $options ) ) {
			foreach ( $options as $option ) {
				delete_option( $option->option_name );
			}
		}
	}

	/**
	 * get_options_page_id
	 *
	 * Retourne l'ID de la page d'options dans la table 'eac_options'
	 *
	 * @var $key La clé du champ
	 */
	public static function get_options_page_id( $key ) {
		global $wpdb;

		/** Recherche de l'option par sa clé dans la table eac_options */
		$option = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_name, option_value
				FROM {$wpdb->prefix}options
				WHERE option_name LIKE %s",
				'%-' . $wpdb->esc_like( $key )
			)
		);

		// Une seule option pour la clé
		if ( $option && ! empty( $option ) && count( $option ) === 1 ) {
			$name                          = reset( $option )->option_name;
			$value                         = maybe_unserialize( reset( $option )->option_value );
			list($prefix, $id, $field_key) = array_pad( explode( '-', $name ), 3, '' ); // eac_options_page-9602-field_63e0fbabf2cea

			/**
			C'est un article et c'est la bonne clé du groupe
			if (is_string(get_post_status($id)) && $group_key && ! empty($group_key) && $value['group'] === $group_key) {
			*/
			if ( ! empty( $id ) && is_string( get_post_status( $id ) ) ) {
				return $id;
			}
		}
		return '';
	}

	/**
	 * register_post_type_option_page
	 *
	 * Enregistre un nouveau type d'article
	 *
	 * @return void
	 */
	public function register_post_type_option_page(): void {
		$labels = array(
			'name'               => esc_html_x( "ACF Pages d'Options", 'Post type general name', 'eac-components' ),
			'singular_name'      => esc_html_x( "ACF Page d'Options", 'Post type singular name', 'eac-components' ),
			'menu_name'          => esc_html__( "ACF Pages d'Options", 'eac-components' ),
			'name_admin_bar'     => esc_html__( "ACF Pages d'Options", 'eac-components' ),
			'archives'           => esc_html__( 'Liste Archives', 'eac-components' ),
			'parent_item_colon'  => esc_html__( 'Parent', 'eac-components' ),
			'all_items'          => esc_html__( "Toutes les Pages d'Options", 'eac-components' ),
			'add_new_item'       => esc_html__( "Nouvelle Page d'Options", 'eac-components' ),
			'add_new'            => esc_html__( 'Ajouter', 'eac-components' ),
			'new_item'           => esc_html__( "Nouvelle Page d'Options", 'eac-components' ),
			'edit_item'          => esc_html__( "Ajouter une Page d'Options", 'eac-components' ),
			'update_item'        => esc_html__( "Modifier une Page d'Options", 'eac-components' ),
			'view_item'          => esc_html__( "Voir une Page d'Options", 'eac-components' ),
			'search_items'       => esc_html__( "Chercher dans les Pages d'Options", 'eac-components' ),
			'not_found'          => esc_html__( 'Pas trouvé', 'eac-components' ),
			'not_found_in_trash' => esc_html__( 'Pas trouvé dans la poubelle', 'eac-components' ),
		);

		$args = array(
			'label'               => esc_html__( "Liste des Pages d'Options", 'eac-components' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'author', 'editor' ),
			'public'              => false,
			'rewrite'             => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'hierarchical'        => false,
			'publicly_queryable'  => false,
			'query_var'           => false,
			'has_archive'         => false,
			'show_in_rest'        => true,
			'taxonomies'          => array( 'category', 'post_tag' ),
			'capabilities'        => Eac_Config_Elements::get_option_page_capabilities(),
		);

		if ( ! post_type_exists( self::$acf_post_type ) ) {
			register_post_type( self::$acf_post_type, $args );
		}
		/**write_log( $GLOBALS['wp_post_types']['eac_options_page']->capability_type );
		write_log( $GLOBALS['wp_post_types']['eac_options_page']->map_meta_cap );
		write_log( $GLOBALS['wp_post_types']['eac_options_page']->cap );*/
	}

	/**
	 * add_submenu_to_admin_menu
	 *
	 * Ajout d'un sous-menu pour le type d'article au menu pricipal 'EAC composants'
	 * Check nouvelle capacité pour ajouter le sous-menu
	 */
	public function add_submenu_to_admin_menu() {
		$callback    = 'edit.php?post_type=' . self::$acf_post_type;
		$title       = esc_html__( "ACF Page d'Options", 'eac-components' );
		$main_menu   = menu_page_url( EAC_DOMAIN_NAME, false );
		$option      = get_option( Eac_Config_Elements::get_option_page_capability_name(), false );
		$menu_option = '';

		if ( current_user_can( 'manage_options' ) ) {
			$menu_option = 'manage_options';
		} elseif ( $option && current_user_can( $option ) ) {
			$menu_option = $option;
		}

		if ( $main_menu && ! empty( $menu_option ) ) {
			add_submenu_page( EAC_DOMAIN_NAME, $title, $title, $menu_option, $callback );
		}
	}

	/**
	 * add_columns
	 *
	 * Ajout des colonnes à la vue d'admin des pages
	 */
	public function add_columns( $columns ) {
		unset( $columns['date'] );
		return array_merge(
			$columns,
			array(
				'eac_type'        => esc_html__( 'Type', 'eac-components' ),
				'eac_group'       => esc_html__( 'Groupes', 'eac-components' ),
				'eac_field'       => esc_html__( 'Champs', 'eac-components' ),
				'eac_field_saved' => esc_html__( 'Enregistrés', 'eac-components' ),
				'eac_id'          => 'ID',
			)
		);
	}

	/**
	 * data_columns
	 *
	 * Ajoute le contenu des colonnes à la vue d'admin des pages d'options
	 */
	public function data_columns( $column_name, $post_id ) {
		?><style type="text/css">
			th#categories { width: 8%; }
			th#eac_type { width: 7%; }
		</style>
		<?php
		switch ( $column_name ) {
			case 'eac_type':
				echo esc_html( get_post_type_object( get_post_type( $post_id ) )->labels->singular_name );
				break;
			case 'eac_group':
				$title  = array();
				$groups = acf_get_field_groups( array( 'post_id' => $post_id ) );
				foreach ( $groups as $group ) {
					$title[] = $group['title'] . ' (' . $group['key'] . ')';
				}
				if ( ! empty( $title ) ) {
					echo implode( '<br /> ', $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo 'Not found';
				}
				break;
			case 'eac_field':
				$id     = array();
				$groups = acf_get_field_groups( array( 'post_id' => $post_id ) );

				foreach ( $groups as $group ) {
					if ( isset( $group['ID'] ) && ! empty( $group['ID'] ) ) {
						$fields = acf_get_fields( $group['ID'] );
					} else {
						$fields = acf_get_fields( $group );
					}

					foreach ( $fields as $field ) {
						if ( 'group' === $field['type'] ) {
							$id[] = $field['name'] . ' (' . $field['key'] . ')';
							foreach ( $field['sub_fields'] as $sub_field ) {
								$id[] = $field['name'] . '_' . $sub_field['name'] . ' (' . $sub_field['key'] . ')';
							}
						} else {
							$id[] = $field['name'] . ' (' . $field['key'] . ')';
						}
					}
				}

				if ( ! empty( $id ) ) {
					echo implode( '<br />', $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo 'Not found';
				}

				break;
			case 'eac_field_saved':
				$fields_count = 0;
				$saved        = esc_html__( 'Oui', 'eac-components' );
				$groups       = acf_get_field_groups( array( 'post_id' => $post_id ) );

				foreach ( $groups as $group ) {
					$continue_loop = false;

					foreach ( $group['location'] as $locations ) {
						if ( empty( $locations ) ) {
							continue;
						}

						foreach ( $locations as $rule ) {
							if ( 'post_type' === $rule['param'] && '==' === $rule['operator'] && self::$acf_post_type === $rule['value'] ) {
								$continue_loop = true;
							}
						}
					}

					if ( false === $continue_loop ) {
						continue;
					}

					if ( isset( $group['ID'] ) && ! empty( $group['ID'] ) ) {
						$fields = acf_get_fields( $group['ID'] );
					} else {
						$fields = acf_get_fields( $group );
					}

					$fields_count += count( $fields );

					foreach ( $fields as $field ) {
						if ( 'group' === $field['type'] ) {
							foreach ( $field['sub_fields'] as $sub_field ) {
								$option_name = self::$options_page_name . $post_id . '-' . $sub_field['key'];
								if ( get_option( $option_name ) === false ) {
									$saved = esc_html__( 'Non', 'eac-components' );
								}
							}
						} else {
							$option_name = self::$options_page_name . $post_id . '-' . $field['key'];
							if ( get_option( $option_name ) === false ) {
								$saved = esc_html__( 'Non', 'eac-components' );
							}
						}
					}
				}

				if ( 0 === $fields_count ) {
					echo '----';
				} else {
					echo esc_html( $saved );
				}
				break;
			case 'eac_id':
				echo absint( $post_id );
				break;
		}
	}

	/**
	 * get_acf_field_groups
	 *
	 * Retourne la liste des groupes pour le post_type à l'exclusion des autres règles
	 *
	 * TODO faire un delete_transient( 'eac_options_page_acf' ); dans plusieurs filtres/actions
	 *
	 * @param string $post_type
	 * @return array
	 * @since 2.3.6 ajout du cache transient
	 */
	public static function transient_get_acf_field_groups( string $post_type = '' ): array {
		if ( empty( $post_type ) ) {
			$post_type = self::$acf_post_type;
		}

		$groups = get_transient( 'eac_options_page_acf' );

		if ( false === $groups ) {
			$acf_field_groups = acf_get_field_groups();
			$groups           = array();
			foreach ( $acf_field_groups as $acf_field_group ) {
				if ( $acf_field_group['active'] ) {
					foreach ( $acf_field_group['location'] as $locations ) {
						if ( empty( $locations ) ) {
							continue;
						}

						foreach ( $locations as $rule ) {
							if ( 'post_type' === $rule['param'] && '==' === $rule['operator'] && $rule['value'] === $post_type ) {
								$groups[] = $acf_field_group;
							}
						}
					}
				}
			}
			if ( ! empty( $groups ) ) {
				set_transient( 'eac_options_page_acf', $groups, MINUTE_IN_SECONDS ); // 1 minutes
			}
		}
		return $groups;
	}

	/**
	 * get_acf_field_groups
	 *
	 * Retourne la liste des groupes pour le post_type à l'exclusion des autres règles
	 *
	 * @param string $post_type
	 * @return array
	 */
	public static function get_acf_field_groups( string $post_type = '' ): array {
		if ( empty( $post_type ) ) {
			$post_type = self::$acf_post_type;
		}

		$groups           = array();
		$acf_field_groups = acf_get_field_groups();

		foreach ( $acf_field_groups as $acf_field_group ) {
			if ( $acf_field_group['active'] ) {
				foreach ( $acf_field_group['location'] as $locations ) {
					if ( empty( $locations ) ) {
						continue;
					}

					foreach ( $locations as $rule ) {
						if ( 'post_type' === $rule['param'] && '==' === $rule['operator'] && $rule['value'] === $post_type ) {
							$groups[] = $acf_field_group;
						}
					}
				}
			}
		}
		return $groups;
	}
}
