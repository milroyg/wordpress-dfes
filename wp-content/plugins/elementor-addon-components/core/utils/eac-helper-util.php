<?php
/**
 * Class: Eac_Helper_Util
 *
 * Description: Met à disposition un ensemble de méthodes utiles pour les Widgets Post grid et Product grid
 *
 * @since 1.0.0
 */

namespace EACCustomWidgets\Core\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

class Eac_Helper_Util {

	/**
	 * @var $post_query_args
	 *
	 * Variable pour enregistrer les arguments de la requête
	 */
	public $post_query_args = null;

	/**
	 * Constructeur de la class
	 */
	public function __construct() {}

	/**
	 * set_post_query_args
	 * Enregistre les arguments de la requête
	 *
	 * @param mixed $args
	 *
	 * @return void
	 */
	public function set_post_query_args( $args ): void {
		$this->post_query_args = $args;
	}

	/**
	 * get_post_query_args
	 * Pour afficher le contenu de la requête avec 'var_export'
	 *
	 * @return array les arguments de la requête
	 */
	public function get_post_query_args(): array {
		return $this->post_query_args;
	}

	/**
	 * build_post_args
	 * Construit la liste des arguments pour la requête WP_Query
	 *
	 * @param mixed $settings Tous les controls du composant
	 *
	 * @return array argument de la requête
	 */
	public function build_post_args( $settings ): array {
		$query_args = array();

		if ( empty( $settings['al_article_type'] ) ) {
			return $query_args;
		}

		$article = $settings['al_article_type'];

		/**
		$query_args['update_post_meta_cache'] = false;
		$query_args['update_post_term_cache '] = false;
		$query_args['cache_results'] = false;
		*/
		$query_args['post_type']           = $article;
		$query_args['post_status']         = 'publish';
		$query_args['posts_per_page']      = ! empty( $settings['al_article_nombre'] ) ? intval( $settings['al_article_nombre'] ) : -1;
		$query_args['orderby']             = $settings['al_article_orderby'];
		$query_args['order']               = $settings['al_article_order'];
		$query_args['ignore_sticky_posts'] = 1;

		// Récupère le nombre de page pour la pagination/navigation
		if ( 'yes' === $settings['al_content_pagging_display'] || 'yes' === $settings['al_content_nav_display'] ) {
			if ( get_query_var( 'paged' ) ) {
				$query_args['paged'] = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$query_args['paged'] = get_query_var( 'page' );
			} else {
					$query_args['paged'] = 1;
			}

			// Calcul de l'offset si ce n'est pas la première page
			if ( $query_args['paged'] > 1 ) {
				$query_args['offset'] = $query_args['posts_per_page'] * ( $query_args['paged'] - 1 );
			}
		} else {
			// 'no_found_rows' à true s'il n'y a pas de pagination et si on n'a pas besoin du nombre total d'articles
			$query_args['no_found_rows'] = true;
		}

		/** Implémente le filtre sur les Auteurs */
		if ( ! empty( $settings['al_content_user'] ) ) {
			$query_args['author'] = sanitize_text_field( $settings['al_content_user'] );
		}

		// Exclure des articles
		if ( 'yes' === $settings['al_article_id'] && ! empty( $settings['al_article_exclude'] ) ) {
			$query_args['post__not_in'] = explode( ',', sanitize_text_field( $settings['al_article_exclude'] ) );
		}

		// Inclure les enfants
		if ( isset( $settings['al_article_include'] ) && 'yes' !== $settings['al_article_include'] ) {
			$query_args['post_parent'] = 0;
		}

		// Un type d'article est sélectionné, on renseigne la 'tax_query'
		if ( ! empty( $settings['al_article_taxonomy'] ) ) {
			$array_taxonomies = $settings['al_article_taxonomy']; // array de taxonomie
			$array_terms      = $settings['al_article_term'];     // array d'étiquettes
			$terms_slug       = array();

			// Relation entre les taxos
			if ( ! empty( $array_taxonomies ) && is_countable( $array_taxonomies ) && count( $array_taxonomies ) > 1 ) {
				$query_args['tax_query']['relation'] = 'OR';
			}

			// Extrait les slugs du tableau de terms
			if ( ! empty( $array_terms ) ) {
				$terms_slug = array_map(function ( $term_item ) {
					return \str_contains( $term_item, '::' ) ? explode( '::', $term_item, 2 )[1] : $term_item;
				}, $array_terms);
			}

			// Boucle sur toutes les taxonomies
			foreach ( $array_taxonomies as $index => $taxonomy ) {
				$list_terms   = array();
				$custom_terms = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => true,
					)
				);

				if ( ! is_wp_error( $custom_terms ) && ! empty( $custom_terms ) ) {
					foreach ( $custom_terms as $custom_term ) {
						// Le term de la taxo est dans le tableau de slug des terms sélectionnés dans la liste
						if ( ! empty( $terms_slug ) ) {
							if ( in_array( $custom_term->slug, $terms_slug, true ) ) {
								$list_terms[] = $custom_term->slug;
							}
						} else {
							$list_terms[] = $custom_term->slug;
						}
					}

					// Affecte les champs nécessaires à la requête
					$query_args['tax_query'][ $index ]['taxonomy'] = $taxonomy;
					$query_args['tax_query'][ $index ]['field']    = 'slug';
					$query_args['tax_query'][ $index ]['terms']    = $list_terms;
				}
			}
		}

		/** Enregistre les arguments de la requête */
		$this->set_post_query_args( $query_args );

		return $query_args;
	}

	/**
	 * get_user_filter
	 *
	 * @param mixed $which_user une liste d'ID d'auteurs
	 * @param mixed $widget_id identifiant du widget
	 * @param mixed $wrapper_id l'identifiant du wrapper
	 *
	 * @return string les filtres des auteurs des articles formatés en HTML
	 */
	public function get_user_filter( $which_user, $widget_id, $wrapper_id ): string {
		$html        = '';
		$which_users = explode( ',', $which_user );

		/** Affichage standard des filtres */
		$html .= "<div class='al-filters__wrapper'>";
		$html .= "<div class='al-filters__item al-active'><a href='#' class='eac-accessible-link' data-filter='*' role='button' aria-pressed='true' aria-controls='" . esc_attr( $wrapper_id ) . "' aria-label='" . esc_html__( 'Filter grid by', 'eac-components' ) . ' ' . esc_html__( 'All', 'eac-components' ) . "'>" . esc_html__( 'All', 'eac-components' ) . '</a></div>';
		foreach ( $which_users as $id_user ) {
			$disp_user = get_user_by( 'id', trim( $id_user ) );
			if ( false !== $disp_user ) {
				$html .= sprintf( '<div class="al-filters__item"><a href="#" class="eac-accessible-link" data-filter=".%1$s" role="button" aria-pressed="false" aria-controls="%4$s" aria-label="%2$s %3$s">%3$s</a></div>', sanitize_title( $disp_user->display_name ), esc_html__( 'Filter grid by', 'eac-components' ), ucfirst( $disp_user->display_name ), esc_attr( $wrapper_id ) );
			}
		}
		$html .= '</div>';

		/** Filtres sous forme de liste */
		$html .= "<div class='al-filters__wrapper-select'>";
		$html .= "<label id='label_" . esc_attr( $widget_id ) . "' class='visually-hidden' for='listbox_" . esc_attr( $widget_id ) . "'>" . esc_html__( 'Author filters', 'eac-components' ) . '</label>';
		$html .= "<select id='listbox_" . esc_attr( $widget_id ) . "' class='al-filters__select' aria-labelledby='label_" . esc_attr( $widget_id ) . "'>";
		$html .= "<option value='*' selected>" . esc_html__( 'All', 'eac-components' ) . '</option>';
		foreach ( $which_users as $id_user ) {
			$disp_user = get_user_by( 'id', trim( $id_user ) );
			if ( false !== $disp_user ) {
				$html .= sprintf( '<option value=".%1$s">%2$s</option>', sanitize_title( $disp_user->display_name ), ucfirst( esc_attr( $disp_user->display_name ) ) );
			}
		}
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * get_tax_query_filter
	 * Crée et formate les filtres pour la taxonomie
	 * Compare les slugs de la taxonomie et les slugs passés en paramètre
	 *
	 * @param mixed $taxonomy_filter Un tableau de catégories
	 * @param mixed $term_filter Un tableau de slug des étiquettes
	 * @param mixed $widget_id l'identifiant du widget
	 * @param mixed $wrapper_id l'identifiant du wrapper
	 * @param bool $cat_parent inclure le parent
	 *
	 * @return string les filtres des catégories formatées en HTML
	 */
	public function get_tax_query_filter( $taxonomy_filter, $term_filter, $widget_id, $wrapper_id, $cat_parent = false ): string {
		$html          = '';
		$display_terms = array();

		// Ne retourne que les catégories qui ont la valeur de l'attribut 'parent' à zéro. Uniquement le top level
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy_filter,
				'hide_empty' => true,
				'parent'     => 0,
			)
		);

		if ( ! is_wp_error( $terms ) && count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				foreach ( $taxonomy_filter as $taxonomy ) {
					/** TODO utiliser le préfix déjà disponible dans le tag article */
					$prefix = 'post_tag' === $taxonomy ? 'tag-' : $taxonomy . '-';
					$children = get_term_children( absint( $term->term_id ), $taxonomy );

					if ( $cat_parent && ! empty( $children ) && ! is_wp_error( $children ) ) {
						if ( ! empty( $term_filter ) ) {
							if ( in_array( $term->slug, $term_filter, true ) ) {
								$display_terms[ $term->slug ] = $term->name;
							}
						} else {
							$display_terms[ $term->slug ] = $term->name;
						}
					} elseif ( ! $cat_parent ) {
						if ( ! empty( $term_filter ) ) {
							if ( in_array( $term->slug, $term_filter, true ) ) {
								$display_terms[ $term->slug ] = $term->name;
							}
						} else {
							$display_terms[ $term->slug ] = $term->name;
						}
					}
				}
			}
			// Tri
			ksort( $display_terms, SORT_FLAG_CASE | SORT_NATURAL );
		} else {
			return $html;
		}

		/** Affichage standard des filtres */
		$html .= "<div class='al-filters__wrapper'>";
		$html .= "<div class='al-filters__item al-active'><a href='#' class='eac-accessible-link' data-filter='*' role='button' aria-pressed='true' aria-controls='" . esc_attr( $wrapper_id ) . "' aria-label='" . esc_html__( 'Filter grid by', 'eac-components' ) . ' ' . esc_html__( 'All', 'eac-components' ) . "'>" . esc_html__( 'All', 'eac-components' ) . '</a></div>';
		foreach ( $display_terms as $term_slug => $term_name ) {
			$html .= sprintf( '<div class="al-filters__item"><a href="#" class="eac-accessible-link" data-filter=".%1$s" role="button" aria-pressed="false" aria-controls="%4$s" aria-label="%2$s %3$s">%3$s</a></div>', $term_slug, esc_html__( 'Filter grid by', 'eac-components' ), ucfirst( esc_attr( $term_name ) ), esc_attr( $wrapper_id ) );
		}
		$html .= '</div>';

		// Filtres sous forme de liste
		$html .= "<div class='al-filters__wrapper-select'>";
		$html .= "<label id='label_" . esc_attr( $widget_id ) . "' class='visually-hidden' for='listbox_" . esc_attr( $widget_id ) . "'>" . esc_html__( 'Taxonomy filters', 'eac-components' ) . '</label>';
		$html .= "<select id='listbox_" . esc_attr( $widget_id ) . "' class='al-filters__select' aria-labelledby='label_" . esc_attr( $widget_id ) . "'>";
		$html .= "<option value='*' selected>" . esc_html__( 'All', 'eac-components' ) . '</option>';
		foreach ( $display_terms as $term_slug => $term_name ) {
			$html .= sprintf( '<option value=".%1$s">%2$s</option>', $term_slug, ucfirst( esc_attr( $term_name ) ) );
		}
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}
}
