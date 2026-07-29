<?php
/**
 * EAC Repeater Block (fonctions) — inline-localize version
 * @since 2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use EACCustomWidgets\EAC_Plugin;

require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';

/* ---------------------------
 * Actions
 * --------------------------- */
add_action( 'init', 'eac_repeater_register_block_assets' );
add_action( 'enqueue_block_editor_assets', 'eac_repeater_enqueue_editor_assets' );
add_action( 'enqueue_block_assets', 'eac_repeater_enqueue_assets' );
add_action( 'rest_api_init', 'eac_register_route_repeater', 26 );

/**
 * eac_repeater_register_block_assets
 * Register block type & assets
 *
 * @return void
 */
function eac_repeater_register_block_assets(): void {
	$block_js_handler         = 'eac-repeater-block';
	$faq_js_handler           = 'eac-repeater-faq';
	$editor_css_handler       = 'eac-repeater-editor';
	$frontend_css_handler     = 'eac-repeater-frontend';
	$accessibility_js_handler = 'eac-lib-accessibility';

	wp_register_script(
		$faq_js_handler,
		EAC_Plugin::instance()->get_script_url( 'includes/blocks/repeater/assets/js/faq' ),
		array( 'jquery' ),
		filemtime( EAC_Plugin::instance()->get_script_path( 'includes/blocks/repeater/assets/js/faq' ) ),
		true
	);

	// Register block script and include ajax helper as dependency (editor)
	wp_register_script(
		$block_js_handler,
		EAC_Plugin::instance()->get_script_url( 'includes/blocks/repeater/assets/js/block' ),
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'wp-data', 'wp-api-fetch' ),
		filemtime( EAC_Plugin::instance()->get_script_path( 'includes/blocks/repeater/assets/js/block' ) ),
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
		EAC_Plugin::instance()->get_style_url( 'includes/blocks/repeater/assets/css/frontend' ),
		array(),
		filemtime( EAC_Plugin::instance()->get_style_path( 'includes/blocks/repeater/assets/css/frontend' ) ),
	);

	// Use metadata registration (block.json) and attach render callback.
	if ( function_exists( 'register_block_type_from_metadata' ) ) {
		register_block_type_from_metadata( __DIR__, array( 'render_callback' => 'eac_repeater_render_block' ) );
	}
}

/**
 * eac_repeater_enqueue_editor_assets
 * Enqueue editor assets
 *
 * @return void
 */
function eac_repeater_enqueue_editor_assets(): void {
	wp_enqueue_script( 'eac-repeater-block' );
	wp_enqueue_style( 'eac-repeater-frontend' );
}

/**
 * eac_repeater_enqueue_assets
 * Enqueue frontend assets
 *
 * @return void
 */
function eac_repeater_enqueue_assets(): void {
	wp_enqueue_script( 'eac-repeater-faq' );
	wp_enqueue_script( 'eac-lib-accessibility' );
	wp_enqueue_style( 'eac-repeater-frontend' );
}

/**
 * eac_repeater_render_block
 * Render callback du block
 *
 * @param array $attributes
 *
 * @return string
 */
function eac_repeater_render_block( array $attributes ): string {
	$class_name      = isset( $attributes['className'] ) ? $attributes['className'] : '';
	$container_width = isset( $attributes['containerWidth'] ) ? $attributes['containerWidth'] : 'container-none';
	$repeater        = isset( $attributes['selectedRepeater'] ) ? $attributes['selectedRepeater'] : '';
	$sub_fields      = isset( $attributes['selectedSubfields'] ) && is_array( $attributes['selectedSubfields'] ) ? $attributes['selectedSubfields'] : array();
	$post_source     = isset( $attributes['postSource'] ) ? $attributes['postSource'] : 'current';
	$postid          = isset( $attributes['postId'] ) ? (int) $attributes['postId'] : 0;
	$heading_faq     = isset( $attributes['headingFaq'] ) ? $attributes['headingFaq'] : 'div';
	$display_type    = isset( $attributes['displayType'] ) ? $attributes['displayType'] : 'grid';
	$block_bg        = ! empty( $attributes['blockBackground'] ) ? $attributes['blockBackground'] : '#FFFFFF';
	$item_bg         = ! empty( $attributes['itemBackground'] ) ? $attributes['itemBackground'] : '#FFFFFF';
	$color_text      = isset( $attributes['colorText'] ) ? $attributes['colorText'] : '';
	$font_text       = isset( $attributes['fontText'] ) ? $attributes['fontText'] : array();
	$item_border     = isset( $attributes['itemBorder'] ) ? $attributes['itemBorder'] : array();
	$item_radius     = isset( $attributes['itemBorderRadius'] ) && is_array( $attributes['itemBorderRadius'] ) ? $attributes['itemBorderRadius'] : array();
	$image_size      = isset( $attributes['imageSizes'] ) ? $attributes['imageSizes'] : 'medium';
	$image_ratio     = isset( $attributes['imageRatio'] ) ? $attributes['imageRatio'] : '';
	$image_pos       = isset( $attributes['imagePosition'] ) ? $attributes['imagePosition'] . '%' : '50%';
	$block_spacing   = isset( $attributes['marginTopBottom'] ) && is_array( $attributes['marginTopBottom'] ) ? $attributes['marginTopBottom'] : array();
	$align_hrz_text  = isset( $attributes['alignmentHrzText'] ) ? $attributes['alignmentHrzText'] : 'start';
	$align_vrt_text  = isset( $attributes['alignmentVrtText'] ) ? $attributes['alignmentVrtText'] : 'start';
	$repeater_width  = isset( $attributes['repeaterWidth'] ) && is_array( $attributes['repeaterWidth'] ) ? $attributes['repeaterWidth'] : array();
	$repeater_col    = isset( $attributes['repeaterCol'] ) && is_array( $attributes['repeaterCol'] ) ? $attributes['repeaterCol'] : array();
	$repeater_gap    = isset( $attributes['repeaterGap'] ) && is_array( $attributes['repeaterGap'] ) ? $attributes['repeaterGap'] : array();
	$button_link     = isset( $attributes['linkAsButton'] ) ? (bool) $attributes['linkAsButton'] : false;
	$card_link       = isset( $attributes['globalLink'] ) ? (bool) $attributes['globalLink'] : false;
	$nofollow_link   = isset( $attributes['nofollowLink'] ) ? (bool) $attributes['nofollowLink'] : false;
	$item_style      = isset( $attributes['itemStyle'] ) ? $attributes['itemStyle'] : '';
	$color_title_faq = ! empty( $attributes['colorTitleFaq'] ) ? $attributes['colorTitleFaq'] : '#1346CD';
	$color_bg_faq    = ! empty( $attributes['colorTitleFaqBackground'] ) ? $attributes['colorTitleFaqBackground'] : '#F1F1F1';
	$color_title_table    = ! empty( $attributes['colorTitleTable'] ) ? $attributes['colorTitleTable'] : '#000000';
	$color_title_bg_table = ! empty( $attributes['colorTitleTableBackground'] ) ? $attributes['colorTitleTableBackground'] : '#F1F1F1';

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
		$default = 'table' === $display_type ? 1 : 0;
		$width   = ! empty( $item_border['width'] ) ? $item_border['width'] : $default;
		$color   = ! empty( $item_border['color'] ) ? $item_border['color'] : 'rgba(0, 0, 0, .1)';
		$item_border = sprintf( '%1$dpx solid %2$s', absint( $width ), $color );
	}

	if ( ! empty( $item_radius ) ) {
		$unit = $item_radius['unit'];
		$item_radius['width'] = ! is_null( $item_radius['width'] ) ? (string) $item_radius['width'] . $unit : '0' . $unit;
	}

	if ( ! empty( $repeater_width ) ) {
		$repeater_width['desktopWidth']    = ! empty( $repeater_width['desktopWidth'] ) ? (string) $repeater_width['desktopWidth'] . '%' : '60%';
		$repeater_width['tabletLandWidth'] = ! empty( $repeater_width['tabletLandWidth'] ) ? (string) $repeater_width['tabletLandWidth'] . '%' : '00%';
		$repeater_width['tabletWidth']     = ! empty( $repeater_width['tabletWidth'] ) ? (string) $repeater_width['tabletWidth'] . '%' : '80%';
		$repeater_width['mobileLandWidth'] = ! empty( $repeater_width['mobileLandWidth'] ) ? (string) $repeater_width['mobileLandWidth'] . '%' : '80%';
		$repeater_width['mobileWidth']     = ! empty( $repeater_width['mobileWidth'] ) ? (string) $repeater_width['mobileWidth'] . '%' : '100%';
	}

	$gap_size = '16px';
	if ( ! empty( $repeater_gap ) ) {
		$clamp     = ! empty( $repeater_gap['gapClamp'] ) ? $repeater_gap['gapClamp'] : null;
		$gap_size  = $clamp ? $clamp : '16px';
	}

	if ( 'current' === $post_source ) {
		$postid = get_the_ID();
	}

	if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
		$no_file = sprintf( '<div class="eac-repeater-empty">%s</div>', esc_html__( 'You do not have permission to use this block.', 'eac-components' ) );
		return $no_file;
	}

	if ( empty( $postid ) || empty( $repeater ) ) {
		$no_file = sprintf( '<div class="eac-repeater-empty">%s</div>', esc_html__( 'No repeater selected or post not found.', 'eac-components' ) );
		return $no_file;
	}

	// Préparer variables communes pour le template
	$wrapper_classes = sprintf(
		'cols-desktop-%1$d cols-tabletland-%2$d cols-tablet-%3$d cols-mobileland-%4$d cols-mobile-%5$d',
		! empty( $repeater_col['desktopCol'] ) ? absint( $repeater_col['desktopCol'] ) : 3,
		! empty( $repeater_col['tabletLandCol'] ) ? absint( $repeater_col['tabletLandCol'] ) : 3,
		! empty( $repeater_col['tabletCol'] ) ? absint( $repeater_col['tabletCol'] ) : 2,
		! empty( $repeater_col['mobileLandCol'] ) ? absint( $repeater_col['mobileLandCol'] ) : 2,
		! empty( $repeater_col['mobileCol'] ) ? absint( $repeater_col['mobileCol'] ) : 1
	);

	// Deux champs pour la FAQ : un pour la question, un pour la réponse
	if ( 'faq' === $display_type && 2 !== count( $sub_fields ) ) {
		$no_file = sprintf( '<div class="eac-repeater-empty">%s</div>', esc_html__( 'Two sub-fields are required for the FAQ.', 'eac-components' ) );
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
 * eac_register_route_repeater
 * Récupère les repeaters et subfields ACF du post pour l'API REST
 * Exemple d'URL pour tester dans le navigateur ou Postman (remplacer postId par un ID de post existant) :
 * http://127.0.0.1/eac234/wp-json/eac-blocks/v1/acf-repeater?postId=60932
 * 'permission_callback' => '__return_true' pour autoriser l'accès à tous les utilisateurs (y compris les non connectés)
 *
 * @return void
 */
function eac_register_route_repeater() {
	register_rest_route(
		'eac-blocks/v1',
		'/acf-repeater/(?P<postId>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE, // GET
			'callback'            => 'eac_rest_get_repeater',
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
		'/acf-subfield/(?P<postId>\d+)/(?P<fieldId>[^/]+)',
		array(
			'methods'             => WP_REST_Server::READABLE, // GET
			'callback'            => 'eac_rest_get_repeater_subfield',
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
				'fieldId' => array(
					'required' => true,
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && preg_match( '/^field_[a-z0-9]+$/i', $param );
					},
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}

/**
 * eac_rest_get_repeater
 * Récupère les repeaters ACF du post
 *
 * @param WP_REST_Request $request
 *
 * @return WP_REST_Response | WP_Error
 */
function eac_rest_get_repeater( WP_REST_Request $request ) {
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
				if ( \acf_field_type_exists( $field['type'] ) && 'eac_repeater' === $field['type'] ) {
					$result[] = array(
						'key'  => sanitize_text_field( (string) $field['key'] ),
						'label' => isset( $field['label'] ) ? html_entity_decode( sanitize_text_field( $field['label'] ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) : sanitize_text_field( $field['name'] ),
					);
				}
			}
		}
	}
	return rest_ensure_response( $result );
}

/**
 * eac_rest_get_repeater_subfield
 * Récupère les sous champs d'un repeater ACF du post pour l'API REST
 *
 * @param WP_REST_Request $request
 *
 * @return WP_REST_Response | WP_Error
 */
function eac_rest_get_repeater_subfield( WP_REST_Request $request ) {
	$postid       = $request->get_param( 'postId' ) ? absint( $request->get_param( 'postId' ) ) : 0;
	$repeater_key = $request->get_param( 'fieldId' ) ? sanitize_text_field( $request->get_param( 'fieldId' ) ) : '';
	$result       = array();

	if ( 0 === $postid ) {
		return new WP_Error( 'invalid_post_id', 'Invalid post ID', array( 'status' => 400 ) );
	}

	$post = get_post( $postid );
	if ( ! $post ) {
		return new WP_Error( 'rest_post_invalid', 'Post not found', array( 'status' => 404 ) );
	}

	if ( empty( $repeater_key ) ) {
		return new WP_Error( 'invalid_field_id', 'Invalid field ID', array( 'status' => 400 ) );
	}

	$repeater_obj = get_field_object( $repeater_key, $postid );

	if ( $repeater_obj && ! empty( $repeater_obj['sub_fields'] ) ) {
		$temp = array();
		$temp_image = array();
		$temp_other = array();
		foreach ( $repeater_obj['sub_fields'] as $sub_field ) {
			if ( isset( $sub_field['type'] ) && eac_is_supported_field_type( $sub_field['type'] ) ) {
				$key   = sanitize_text_field( (string) $sub_field['key'] );
				$label = isset( $sub_field['label'] ) ? html_entity_decode( sanitize_text_field( $sub_field['label'] ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ) : sanitize_text_field( $sub_field['name'] );
				$type  = sanitize_text_field( (string) $sub_field['type'] );

				if ( 'image' === $type ) {
					$temp_image[ $key ] = array(
						'key'   => $key,
						'label' => $label,
						'type'  => $type,
					);
				} else {
					$temp_other[ $key ] = array(
						'key'   => $key,
						'label' => $label,
						'type'  => $type,
					);
				}
			}
		}
		$temp = array_merge( $temp_image, $temp_other ); // les champs image en premier
		$result = array_values( $temp );
	}
	return rest_ensure_response( $result );
}

/**
 * eac_is_a_valid_pdf
 * Teste si c'est un fihcier PDF
 *
 * @param string $url
 *
 * @return bool
 */
function eac_is_a_valid_pdf( string $url = '' ): bool {
	if ( '' === $url || ! wp_http_validate_url( $url ) ) {
		return false;
	}

	$head = wp_remote_head(
		$url,
		array(
			'redirection' => 2,
			'timeout' => 10,
		),
	);

	if ( is_wp_error( $head ) ) {
		return false;
	}

	$code = wp_remote_retrieve_response_code( $head );
	if ( $code < 200 || $code >= 400 ) {
		return false;
	}

	$content_type = wp_remote_retrieve_header( $head, 'content-type' );
	if ( $content_type && stripos( $content_type, 'application/pdf' ) === false ) {
		return false;
	}

	$args = array(
		'method'      => 'GET',
		'timeout'     => 15,
		'redirection' => 2,
		'headers'     => array( 'Range' => 'bytes=0-1023' ),
	);
	$res = wp_remote_get( $url, $args );
	if ( is_wp_error( $res ) ) {
		return false;
	}

	$code = wp_remote_retrieve_response_code( $res );
	if ( $code < 200 || $code >= 400 ) {
		return false;
	}

	$body = wp_remote_retrieve_body( $res );
	if ( '' === $body ) {
		return false;
	}

	// Vérifie la signature %PDF- dans les premiers octets

	if ( strpos( substr( $body, 0, 8 ), '%PDF-' ) !== false ) {
		return true;
	}

	return false;
}
