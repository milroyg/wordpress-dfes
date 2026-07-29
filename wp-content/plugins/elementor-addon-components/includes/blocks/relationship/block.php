<?php
/**
 * EAC Relationship Block
 * @since 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use EACCustomWidgets\EAC_Plugin;

require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';

/* ---------------------------
 * Actions
 * --------------------------- */
add_action( 'init', 'eac_relationship_register_block_assets' );
add_action( 'enqueue_block_editor_assets', 'eac_relationship_enqueue_editor_assets' );
add_action( 'enqueue_block_assets', 'eac_relationship_enqueue_assets' );
add_action( 'rest_api_init', 'eac_register_route_relationship', 30 ); // Priorité élevée pour éviter les conflits avec d'autres plugins qui pourraient enregistrer la même route REST

/**
 * eac_relationship_register_block_assets
 * Register block type & assets
 *
 * @return void
 */
function eac_relationship_register_block_assets(): void {
	$block_js_handler         = 'eac-relationship-block';
	$editor_css_handler       = 'eac-relationship-editor';
	$frontend_css_handler     = 'eac-relationship-frontend';
	$accessibility_js_handler = 'eac-lib-accessibility';

	// Register block script and include ajax helper as dependency (editor)
	wp_register_script(
		$block_js_handler,
		EAC_Plugin::instance()->get_script_url( 'includes/blocks/relationship/assets/js/block' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'wp-data', 'wp-api-fetch' ),
		filemtime( EAC_Plugin::instance()->get_script_path( 'includes/blocks/relationship/assets/js/block' ) ),
		true
	);

	/** Chaine traduites avec LOCO translate */
	wp_set_script_translations( $block_js_handler, 'eac-components', EAC_PLUGIN_PATH . 'languages' );

	wp_register_script(
		$accessibility_js_handler,
		EAC_Plugin::instance()->get_script_url( 'includes/blocks/lib-accessibility' ),
		array(),
		filemtime( EAC_Plugin::instance()->get_script_path( 'includes/blocks/lib-accessibility' ) ),
		true
	);

	wp_register_style(
		$frontend_css_handler,
		EAC_Plugin::instance()->get_style_url( 'includes/blocks/relationship/assets/css/frontend' ),
		array(),
		filemtime( EAC_Plugin::instance()->get_style_path( 'includes/blocks/relationship/assets/css/frontend' ) ),
	);

	// Use metadata registration (block.json) and attach render callback.
	if ( function_exists( 'register_block_type_from_metadata' ) ) {
		register_block_type_from_metadata( __DIR__, array( 'render_callback' => 'eac_relationship_render_block' ) );
	}
}

/**
 * eac_relationship_enqueue_editor_assets
 * Enqueue editor assets
 *
 * @return void
 */
function eac_relationship_enqueue_editor_assets(): void {
	wp_enqueue_script( 'eac-relationship-block' );
	wp_enqueue_style( 'eac-relationship-frontend' );
}

/**
 * eac_relationship_enqueue_assets
 * Enqueue frontend assets
 *
 * @return void
 */
function eac_relationship_enqueue_assets(): void {
	wp_enqueue_script( 'eac-lib-accessibility' );
	wp_enqueue_style( 'eac-relationship-frontend' );
}

/**
 * eac_relationship_render_block
 * Render callback du block
 *
 * @param array $attributes
 *
 * @return string
 */
function eac_relationship_render_block( array $attributes ): string {
	$class_name      = isset( $attributes['className'] ) ? $attributes['className'] : '';
	$container_width = isset( $attributes['containerWidth'] ) ? $attributes['containerWidth'] : 'container-none';
	$relationship    = isset( $attributes['selectedRelationship'] ) ? $attributes['selectedRelationship'] : '';
	$post_source     = isset( $attributes['postSource'] ) ? $attributes['postSource'] : 'current';
	$postid          = isset( $attributes['postId'] ) ? (int) absint( $attributes['postId'] ) : 0;
	$display_type    = isset( $attributes['displayType'] ) ? $attributes['displayType'] : 'grid';
	$post_fields     = isset( $attributes['selectedPostfields'] ) && is_array( $attributes['selectedPostfields'] ) ? $attributes['selectedPostfields'] : array();
	$heading_title   = isset( $attributes['headingTitle'] ) ? $attributes['headingTitle'] : 'div';
	$block_bg        = isset( $attributes['blockBackground'] ) ? $attributes['blockBackground'] : '#FFFFFF';
	$item_bg         = isset( $attributes['itemBackground'] ) ? $attributes['itemBackground'] : '#FFFFFF';
	$color_text      = isset( $attributes['colorText'] ) ? $attributes['colorText'] : '#000000';
	$font_text       = isset( $attributes['fontText'] ) ? $attributes['fontText'] : array();
	$item_border     = isset( $attributes['itemBorder'] ) ? $attributes['itemBorder'] : array();
	$item_radius     = isset( $attributes['itemBorderRadius'] ) && is_array( $attributes['itemBorderRadius'] ) ? $attributes['itemBorderRadius'] : array();
	$image_size      = isset( $attributes['imageSizes'] ) ? $attributes['imageSizes'] : 'medium';
	$image_ratio     = isset( $attributes['imageRatio'] ) ? $attributes['imageRatio'] : '';
	$image_pos       = isset( $attributes['imagePosition'] ) ? $attributes['imagePosition'] . '%' : '50%';
	$block_spacing   = isset( $attributes['marginTopBottom'] ) && is_array( $attributes['marginTopBottom'] ) ? $attributes['marginTopBottom'] : array();
	$align_hrz_text  = isset( $attributes['alignmentHrzText'] ) ? $attributes['alignmentHrzText'] : 'start';
	$align_vrt_text  = isset( $attributes['alignmentVrtText'] ) ? $attributes['alignmentVrtText'] : 'start';
	$relationship_col = isset( $attributes['relationshipCol'] ) && is_array( $attributes['relationshipCol'] ) ? $attributes['relationshipCol'] : array();
	$relationship_gap = isset( $attributes['relationshipGap'] ) && is_array( $attributes['relationshipGap'] ) ? $attributes['relationshipGap'] : array();
	$title_link       = isset( $attributes['titleLink'] ) ? (bool) $attributes['titleLink'] : false;
	$card_link       = isset( $attributes['globalLink'] ) ? (bool) $attributes['globalLink'] : false;
	$nofollow_link   = isset( $attributes['nofollowLink'] ) ? (bool) $attributes['nofollowLink'] : false;
	$item_style      = isset( $attributes['itemStyle'] ) ? $attributes['itemStyle'] : '';

	if ( ! empty( $block_spacing ) ) {
		$unit                       = $block_spacing['unit'];
		$block_spacing['marginSup'] = ! empty( $block_spacing['marginSup'] ) ? (string) $block_spacing['marginSup'] . $unit : '0' . $unit;
		$block_spacing['marginInf'] = ! empty( $block_spacing['marginInf'] ) ? (string) $block_spacing['marginInf'] . $unit : '0' . $unit;
	} else {
		$block_spacing['marginSup'] = '0';
		$block_spacing['marginInf'] = '0';
	}

	$font_size = '16px';
	if ( ! empty( $font_text ) ) {
		$clamp     = ! empty( $font_text['fontClamp'] ) ? $font_text['fontClamp'] : null;
		$font_size = $clamp ? $clamp : '16px';
	}

	if ( ! empty( $item_border ) ) {
		$width = ! empty( $item_border['width'] ) ? $item_border['width'] : 0;
		$color = ! empty( $item_border['color'] ) ? $item_border['color'] : 'rgba(0, 0, 0, .1)';
		$item_border = sprintf( '%1$dpx solid %2$s', absint( $width ), $color );
	}

	if ( ! empty( $item_radius ) ) {
		$unit = $item_radius['unit'];
		$item_radius['width'] = ! is_null( $item_radius['width'] ) ? (string) $item_radius['width'] . $unit : '0' . $unit;
	}

	$gap_size = '16px';
	if ( ! empty( $relationship_gap ) ) {
		$clamp     = ! empty( $relationship_gap['gapClamp'] ) ? $relationship_gap['gapClamp'] : null;
		$gap_size  = $clamp ? $clamp : '16px';
	}

	// Préparer variables communes pour le template
	$wrapper_classes = sprintf(
		'cols-desktop-%1$d cols-tabletland-%2$d cols-tablet-%3$d cols-mobileland-%4$d cols-mobile-%5$d',
		! empty( $relationship_col['desktopCol'] ) ? absint( $relationship_col['desktopCol'] ) : 3,
		! empty( $relationship_col['tabletLandCol'] ) ? absint( $relationship_col['tabletLandCol'] ) : 3,
		! empty( $relationship_col['tabletCol'] ) ? absint( $relationship_col['tabletCol'] ) : 2,
		! empty( $relationship_col['mobileLandCol'] ) ? absint( $relationship_col['mobileLandCol'] ) : 2,
		! empty( $relationship_col['mobileCol'] ) ? absint( $relationship_col['mobileCol'] ) : 1
	);

	$type      = 'post';
	$source_id = 0;

	// Déterminer l'ID source selon la source sélectionnée
	if ( 'current' === $post_source ) {
		$source_id = get_the_ID();
	} elseif ( 'other' === $post_source ) {
		$source_id = $postid;
	} elseif ( 'author' === $post_source ) {
		$source_id = ! empty( get_the_author_meta( 'ID' ) ) ? get_the_author_meta( 'ID' ) : 1; // admin par défaut pour les templates
		$type = 'user';
	}

	if ( empty( $source_id ) || empty( $relationship ) || empty( $post_fields ) ) {
		$no_file = sprintf( '<div class="eac-relationship-empty">%s</div>', esc_html__( 'No relationship selected or post not found.', 'eac-components' ) );
		return $no_file;
	}

	if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
		$no_file = sprintf( '<div class="eac-relationship-empty">%s</div>', esc_html__( 'You do not have permission to use this block.', 'eac-components' ) );
		return $no_file;
	}

	// Le templates/{displayType}.php
	$display = 'list' === $display_type ? 'grid' : $display_type;
	$template_file = sprintf( '%s.php', __DIR__ . '/assets/templates/' . $display );
	if ( is_readable( $template_file ) ) {
		ob_start();
		require $template_file;
		$content = ob_get_clean();
		return $content;
	} else {
		$no_file = sprintf( '<div>%1$s: /templates/%2$s.php</div>', esc_html__( 'No such file', 'eac-components' ), $display );
		return $no_file;
	}
}

/**
 * eac_register_route_relationship
 * Récupère les relationship Post block ACF du post pour l'API REST
 * http://127.0.0.1/eac234/wp-json/eac-blocks/v1/acf-relationship?postId=60932
 * 'permission_callback' => '__return_true' pour autoriser l'accès à tous les utilisateurs (y compris les non connectés)
 *
 * @return void
 */
function eac_register_route_relationship() {
	register_rest_route(
		'eac-blocks/v1',
		'/acf-relationship/(?P<postId>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE, // GET
			'callback'            => 'eac_rest_get_relationship',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'args' => array(
				'postId' => array(
					'required'           => true,
					'validate_callback'  => function ( $param ) {
						return is_numeric( $param ) && intval( $param ) > 0;
					},
					'sanitize_callback'  => 'absint',
				),
			),
		)
	);

	register_rest_route(
		'eac-blocks/v1',
		'/acf-relationship-author/(?P<authorId>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE, // GET
			'callback'            => 'eac_rest_get_relationship_author',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
			'args' => array(
				'authorId' => array(
					'required'           => true,
					'validate_callback'  => function ( $param ) {
						return is_numeric( $param ) && intval( $param ) > 0;
					},
					'sanitize_callback'  => 'absint',
				),
			),
		)
	);
}

/**
 * eac_rest_get_relationship
 * Récupère les relationship et Post block ACF du post pour l'API REST
 *
 * @param WP_REST_Request $request
 *
 * @return WP_REST_Response | WP_Error
 */
if ( ! function_exists( 'eac_rest_get_relationship' ) ) {
	function eac_rest_get_relationship( WP_REST_Request $request ) {
		$postid = $request->get_param( 'postId' ) ? absint( $request->get_param( 'postId' ) ) : 0;
		$result = array();

		if ( 0 === $postid ) {
			return new WP_Error( 'invalid_post_id', 'Invalid post ID', array( 'status' => 400 ) );
		}

		$post = get_post( $postid );
		if ( ! $post ) {
			return new WP_Error( 'rest_post_invalid', 'Post not found', array( 'status' => 404 ) );
		}

		if ( function_exists( 'acf_get_field_groups' ) ) {
			$groups = acf_get_field_groups( array( 'post_id' => $postid ) );

			foreach ( $groups as $group ) {
				if ( ! $group['active'] ) {
					continue;
				}
				$fields = acf_get_fields( $group['ID'] );

				if ( ! is_array( $fields ) ) {
					continue;
				}

				foreach ( $fields as $field ) {
					if ( isset( $field['type'] ) && \acf_field_type_exists( $field['type'] ) && in_array( $field['type'], array( 'relationship', 'post_object' ), true ) ) {
						$result[] = array(
							'key'   => sanitize_text_field( $field['key'] ),
							'label' => isset( $field['label'] ) ? html_entity_decode( sanitize_text_field( $field['label'] ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) : sanitize_text_field( $field['name'] ),
						);
					} elseif ( 'group' === $field['type'] && ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
						foreach ( $field['sub_fields'] as $sub_field ) {
							if ( isset( $sub_field['type'] ) && \acf_field_type_exists( $sub_field['type'] ) && in_array( $sub_field['type'], array( 'relationship', 'post_object' ), true ) ) {
								$result[] = array(
									'key'   => sprintf( '%s_%s', sanitize_text_field( $field['name'] ), sanitize_text_field( $sub_field['name'] ) ), // combinaison: group_name_field_name
									'label' => isset( $sub_field['label'] ) ? html_entity_decode( sanitize_text_field( $sub_field['label'] ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) : sanitize_text_field( $sub_field['name'] ),
								);
							}
						}
					}
				}
			}
		}
		return rest_ensure_response( $result );
	}
}

/**
 * eac_rest_get_relationship_author
 * Récupère les relationship et Post block ACF du profil utilisateur pour l'API REST
 *
 * @param WP_REST_Request $request
 *
 * @return WP_REST_Response | WP_Error
 */
if ( ! function_exists( 'eac_rest_get_relationship_author' ) ) {
	function eac_rest_get_relationship_author( WP_REST_Request $request ) {
		$author_id = $request->get_param( 'authorId' ) ? absint( $request->get_param( 'authorId' ) ) : 0;
		$result = array();

		if ( 0 === $author_id ) {
			return new WP_Error( 'invalid_author_id', 'Invalid author ID', array( 'status' => 400 ) );
		}

		// Vérifier que l'utilisateur existe
		$user = get_user_by( 'id', $author_id );
		if ( ! $user ) {
			return new WP_Error( 'author_not_found', 'Author not found', array( 'status' => 404 ) );
		}

		if ( function_exists( 'acf_get_field_groups' ) ) {
			// Récupérer les groupes de champs ACF du profil utilisateur
			$groups = acf_get_field_groups( array( 'user_id' => $author_id ) );

			foreach ( $groups as $group ) {
				if ( ! $group['active'] ) {
					continue;
				}
				$fields = acf_get_fields( $group['ID'] );

				if ( ! is_array( $fields ) ) {
					continue;
				}

				foreach ( $fields as $field ) {
					if ( isset( $field['type'] ) && \acf_field_type_exists( $field['type'] ) && in_array( $field['type'], array( 'relationship', 'post_object' ), true ) ) {
						$result[] = array(
							'key'   => sanitize_text_field( $field['key'] ),
							'label' => isset( $field['label'] ) ? html_entity_decode( sanitize_text_field( $field['label'] ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) : sanitize_text_field( $field['name'] ),
						);
					} elseif ( 'group' === $field['type'] && ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
						foreach ( $field['sub_fields'] as $sub_field ) {
							if ( isset( $sub_field['type'] ) && \acf_field_type_exists( $sub_field['type'] ) && in_array( $sub_field['type'], array( 'relationship', 'post_object' ), true ) ) {
								$result[] = array(
									'key'   => sprintf( '%s_%s', sanitize_text_field( $field['name'] ), sanitize_text_field( $sub_field['name'] ) ),
									'label' => isset( $sub_field['label'] ) ? html_entity_decode( sanitize_text_field( $sub_field['label'] ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) : sanitize_text_field( $sub_field['name'] ),
								);
							}
						}
					}
				}
			}
		}
		return rest_ensure_response( $result );
	}
}
