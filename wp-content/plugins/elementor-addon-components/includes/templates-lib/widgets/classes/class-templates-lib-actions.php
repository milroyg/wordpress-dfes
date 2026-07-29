<?php
/**
 * Class: Class_Templates_Lib_Actions
 *
 * Description: Charge les actions de mise à jour du badge quantité du mini-cart et de l'autocomplete du widget Search
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Widgets\Classes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Templates_Lib_Actions {
	/**
	 * Constructeur
	 */
	public function __construct() {
		add_action( 'wp_ajax_update_mini_cart_counter', array( $this, 'update_mini_cart_counter' ) );
		add_action( 'wp_ajax_nopriv_update_mini_cart_counter', array( $this, 'update_mini_cart_counter' ) );
		add_action( 'wp_ajax_autocomplete_search', array( $this, 'autocomplete_search' ) );
		add_action( 'wp_ajax_nopriv_autocomplete_search', array( $this, 'autocomplete_search' ) );
	}

	/**
	 * update_mini_cart_counter
	 *
	 * @return string
	 */
	public function update_mini_cart_counter(): string {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'eac_update_minicart_counter' ) ) {
			wp_send_json_error( esc_html__( 'Security error', 'eac-components' ) );
		}
		wp_send_json_success( WC()->cart->get_cart_contents_count() );
	}

	/**
	 * autocomplete_search
	 *
	 * @return string
	 */
	public function autocomplete_search(): string {
		check_ajax_referer( 'autocomplete_search_nonce', 'security' );

		if ( ! isset( $_REQUEST['term'] ) ) {
			wp_send_json_success( array() );
		}

		$search_term = sanitize_text_field( wp_unslash( $_REQUEST['term'] ) );
		$groups = array();

		$the_query = new \WP_Query( array(
			's'              => $search_term,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'post_type',
			'order'          => 'DESC',
		) );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$post_type = get_post_type();
				$obj = get_post_type_object( $post_type );

				if ( ! isset( $groups[ $post_type ] ) ) {
					$groups[ $post_type ] = array(
						'slug'  => $post_type,
						'label' => isset( $obj->labels->name ) ? esc_html( $obj->labels->name ) : ucfirst( $post_type ),
						'items' => array(),
					);
				}

				$groups[ $post_type ]['items'][] = array(
					'id'    => absint( get_the_ID() ),
					'label' => esc_html( get_the_title() ),
					'link'  => esc_url( get_the_permalink() ),
				);
			}
			wp_reset_postdata();
		}

		// Renvoie un tableau de groupes (indexé) pour faciliter le parcours côté JS
		$groups_indexed = array_values( $groups );

		wp_send_json_success( $groups_indexed );
	}
}
