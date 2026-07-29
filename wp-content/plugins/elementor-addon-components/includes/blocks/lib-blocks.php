<?php
/**
 * lib.php Fonctions partagées par tous les blocs
 * @since 2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/** Définir si c'est un block theme */
add_filter( 'body_class', function ( $classes ) {
	/**if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {*/
		$classes[] = 'eac-block-theme';
	/**}*/
	return $classes;
}, 1000 );

/**
 * Crée la route REST pour les tailles d'images disponibles dans WP
 *
 * @return void
 */
add_action( 'rest_api_init', function () {
	register_rest_route(
		'eac-blocks/v1',
		'/image-sizes',
		array(
			'methods'             => WP_REST_Server::READABLE, // GET
			'callback'            => 'eac_rest_get_image_sizes',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}, 25 );

/**
 * eac_get_registered_image_size
 *
 * @return array
 */
if ( ! function_exists( 'eac_get_registered_image_size' ) ) {
	function eac_get_registered_image_size(): array {
		$sizes = array(
			'thumbnail'    => esc_html__( 'Thumbnail', 'eac-components' ),
			'medium'       => esc_html__( 'Medium', 'eac-components' ),
			'medium_large' => esc_html__( 'Medium-large', 'eac-components' ),
			'large'        => esc_html__( 'Large', 'eac-components' ),
		);

		// Tailles additionnelles via WP core helper (WP 4.7+)
		$additional = wp_get_additional_image_sizes(); // retourne array size_name => [width,height,crop]

		if ( is_array( $additional ) && ! empty( $additional ) ) {
			foreach ( $additional as $name => $props ) {
				$label = apply_filters( 'wp_image_size_label_' . $name, null );
				if ( ! $label ) {
					/* translators: %s = image size name */
					$label = sprintf( esc_html__( 'Custom: %s', 'eac-components' ), esc_html( $name ) );
				}
				$sizes[ esc_html( $name ) ] = esc_html( $label );
			}
		}

		return $sizes;
	}
}

/**
 * eac_rest_get_image_sizes
 *
 * @param WP_REST_Request $request
 *
 * @return WP_REST_Response
 */
if ( ! function_exists( 'eac_rest_get_image_sizes' ) ) {
	function eac_rest_get_image_sizes( WP_REST_Request $request ): WP_REST_Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		/** unset( $request ); */ // pour éviter l'erreur de paramètre non utilisé

		$sizes = eac_get_registered_image_size();

		if ( is_wp_error( $sizes ) ) {
			return rest_ensure_response( $sizes );
		}

		if ( empty( $sizes ) || ! is_array( $sizes ) ) {
			return rest_ensure_response( array() );
		}

		return rest_ensure_response( (array) $sizes );
	}
}

/**
 * eac_get_attachment_data
 * Les information globales d'une image jointe (attachment)
 *
 * @param string $id ID de l'attachment
 * @param string $attachment_size la taille de l'image (thumbnail, medium, large, full, etc.)
 * @param int $nb_words nombre de mots du champ description
 *
 * @return array
 */
if ( ! function_exists( 'eac_get_attachment_data' ) ) {
	function eac_get_attachment_data( string $id, string $attachment_size, int $nb_words = 40 ): array {
		$attachment_array = array();
		$attachment_id    = absint( $id );
		$attachment       = get_post( $attachment_id );

		if ( 0 === $attachment_id || ! $attachment || ! wp_attachment_is_image( $attachment_id ) ) {
			return $attachment_array;
		}

		$srcset      = wp_get_attachment_image_srcset( $attachment_id, $attachment_size );
		$srcsize     = wp_get_attachment_image_sizes( $attachment_id, $attachment_size );
		$image_data  = wp_get_attachment_image_src( $attachment_id, $attachment_size );
		$image_url   = wp_get_attachment_image_url( $attachment_id, $attachment_size );
		$width       = $image_data ? $image_data[1] : 300;
		$height      = $image_data ? $image_data[2] : 300;
		$target_url  = '';
		$attach_alt  = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ? get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) : $attachment->post_title;
		$parent_id   = ! empty( $attachment->post_parent ) && 0 !== $attachment->post_parent ? $attachment->post_parent : false;
		$description = ! empty( $attachment->post_content ) ? wp_trim_words( $attachment->post_content, $nb_words, '...' ) : '';
		$title       = $attachment->post_title;
		$caption     = $attachment->post_excerpt;

		/** Le parent existe colonne 'Uploaded to' de la vue des médias */
		if ( $parent_id ) {
			$post_parent = get_post( $parent_id );
			if ( $post_parent ) {
				$target_url = get_permalink( $parent_id );
				if ( 'product' === get_post_type( $post_parent ) && function_exists( 'wc_get_product' ) ) {
					$product = wc_get_product( $parent_id );
					if ( $product && is_a( $product, 'WC_Product' ) ) {
						$target_url  = $product->get_permalink();
					}
				}
				$excerpt     = eac_get_post_excerpt( $parent_id, $nb_words );
				$description = ! empty( $excerpt ) ? $excerpt : $description;
			}
			/** Pas de parent, on cherche les publications où l'image est utilisée */
		} elseif ( ! $parent_id ) {
			$posts = get_posts(
				array(
					'post_type'      => array( 'product', 'post', 'page' ),
					'post_status'    => 'publish',
					'meta_key'       => '_thumbnail_id',
					'meta_value'     => $attachment_id,
					'posts_per_page' => 1,
				)
			);
			if ( ! is_wp_error( $posts ) && is_array( $posts ) && isset( $posts[0]->ID ) ) {
				$post_parent    = $posts[0];
				$post_parent_id = $post_parent->ID;
				if ( 'product' === get_post_type( $post_parent_id ) && function_exists( 'wc_get_product' ) ) {
					$product = wc_get_product( absint( $post_parent_id ) );
					if ( $product && is_a( $product, 'WC_Product' ) ) {
						$target_url  = $product->get_permalink();
					}
				} else {
					$target_url  = get_permalink( $post_parent_id );
				}
				$excerpt     = eac_get_post_excerpt( $post_parent_id, $nb_words );
				$description = ! empty( $excerpt ) ? $excerpt : $description;
			}
		}

		/** Un champ ACF URL ajouté dans les médias images ? */
		$url = eac_get_acf_url( $attachment_id );
		$target_url = ! empty( $url ) ? $url : $target_url;

		$attachment_array = array(
			'src'         => wp_get_attachment_url( $attachment_id ),
			'srcset'      => $srcset,
			'srcsize'     => $srcsize,
			'alt'         => $attach_alt,
			'caption'     => $caption,
			'description' => $description,
			'image_url'   => $image_url,
			'title'       => $title,
			'width'       => $width,
			'height'      => $height,
			'media_url'   => $target_url,
		);

		return $attachment_array;
	}
}

/** Début gestion du contenu des blocks */

/**
 * eac_get_post_excerpt
 *
 * @param int $post_id
 * @param int $excerpt_length
 *
 * @return string
 */
function eac_get_post_excerpt( int $post_id, int $excerpt_length ): string {
	$the_post      = get_post( $post_id );
	$the_excerpt   = '';
	$the_post_type = $the_post->post_type;

	if ( 'product' === $the_post_type && function_exists( 'wc_get_product' ) ) {
		$product     = wc_get_product( $post_id );
		$the_excerpt = ! empty( $product->get_description() ) ? $product->get_description() : $product->get_short_description();
		return wp_trim_words( $the_excerpt, $excerpt_length, '...' );
	} elseif ( $the_post ) {
		$the_excerpt = eac_get_gutenberg_content( $the_post );
		if ( empty( $the_excerpt ) ) {
			return '';
		}
		// On supprime tous les shortcode du contenu
		$the_excerpt = strip_shortcodes( $the_excerpt );
		return wp_trim_words( $the_excerpt, $excerpt_length, '...' ); // wp_trim_words fait un strip_all_tags en interne
	}

	return '';
}

/**
 * eac_get_gutenberg_content
 * Extraction du résumé ou du contenu d'un article standard ou Gutenberg
 *
 * @param \WP_post $post
 *
 * @return string
 */
function eac_get_gutenberg_content( \WP_post $post ): string {
	$blocks  = parse_blocks( $post->post_content );
	$excerpt = $post->post_excerpt;
	if ( ! empty( $excerpt ) ) {
		return $excerpt;
	}

	// C'est pas une page gutenberg
	if ( 1 === count( $blocks ) && null === $blocks[0]['blockName'] ) {
		return $post->post_content;
	} else {
		$the_excerpt = eac_get_block_recursively( $blocks, 'core/paragraph' );
		return is_array( $the_excerpt ) && ! empty( $the_excerpt ) ? implode( ' ', $the_excerpt ) : '';
	}
}

/**
 * eac_get_block_recursively
 * Extraction du contenu du block_name avec recherche récursive pour les types de block group
 *
 * @param array $blocks
 * @param string $block_name
 *
 * @return array
 */
function eac_get_block_recursively( array $blocks, string $block_name ): array {
	$block_content = array();
	foreach ( $blocks as $block ) {
		if ( isset( $block['blockName'] ) && $block_name === $block['blockName'] ) {
			$block_content[] = $block['innerHTML'];
		} elseif ( is_array( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) ) {
			$block_content = array_merge( $block_content, eac_get_block_recursively( $block['innerBlocks'], $block_name ) );
		}
	}

	return $block_content;
}
/** Fin gestion du contenu des blocks */

/**
 * eac_get_acf_url
 *
 * @param string $attachment_id
 *
 * @return string
 */
if ( ! function_exists( 'eac_get_acf_url' ) ) {
	function eac_get_acf_url( string $attachment_id = '' ): string {
		$field_value = '';
		if ( empty( $attachment_id ) ) {
			return $field_value;
		}

		$values = get_fields( $attachment_id );

		if ( $values && is_array( $values ) ) {
			foreach ( $values as $name => $value ) {
				$field = get_field_object( $name, $attachment_id );
				if ( ! $field ) {
					continue;
				}

				if ( in_array( $field['type'], array( 'url', 'link', 'page_link' ), true ) ) {
					$val = $field['value'];

					if ( in_array( $field['type'], array( 'url', 'page_link' ), true ) ) {
						// url ou page_link : si tableau, prendre le premier élément
						$field_value = is_array( $val ) ? reset( $val ) : $val;
					} else { // link
						$field_value = is_array( $val ) && isset( $val['url'] ) ? $val['url'] : ( is_string( $val ) ? $val : '' );
					}

					if ( ! empty( $field_value ) ) {
						return (string) $field_value;
					}
				}
			}
		}

		return $field_value;
	}
}

/**
 * eac_is_supported_field_type
 *
 * @param String $type
 *
 * @return bool
 */
if ( ! function_exists( 'eac_is_supported_field_type' ) ) {
	function eac_is_supported_field_type( string $type ): bool {
		if ( empty( $type ) ) {
			return false;
		}
		$supported = array(
			'image',
			'text',
			'textarea',
			'email',
			'url',
			'link',
			'page_link',
			'select',
			'number',
			'date_picker',
			'file',
		);

		return in_array( $type, $supported, true );
	}
}

/**
 * eac_normalize_field_by_type
 *
 * @param array|null $field_obj
 *
 * @return string
 */
if ( ! function_exists( 'eac_normalize_field_by_type' ) ) {
	function eac_normalize_field_by_type( ?array $field_obj = null ): string {
		if ( ! is_array( $field_obj ) || empty( $field_obj['type'] ) ) {
			return $field_value;
		}
		$field_value   = $field_obj['value'];
		$field_type    = $field_obj['type'];
		$return_format = isset( $field_obj['return_format'] ) ? $field_obj['return_format'] : '';
		$prepend       = isset( $field_obj['prepend'] ) ? $field_obj['prepend'] : '';
		$append        = isset( $field_obj['append'] ) ? $field_obj['append'] : '';

		switch ( $field_type ) {
			case 'image':
				switch ( $return_format ) {
					case 'array':
						$field_value = $field_obj['value']['ID'] ?? $field_value;
						break;
					case 'url':
						$field_value = attachment_url_to_postid( $field_obj['value'] ) ? attachment_url_to_postid( $field_obj['value'] ) : $field_value;
						break;
				}
				break;
			case 'text':
			case 'number':
				$field_value = trim( sprintf( '%1$s %2$s %3$s', $prepend ?? '', is_scalar( $field_value ) ? $field_value : '', $append ?? '' ) );
				break;
			case 'select':
				$values = array();
				foreach ( (array) $field_value as $value ) {
					if ( 'array' === $return_format && is_array( $value ) ) {
						$values[] = $value['value'] ?? ( is_scalar( $value ) ? $value : '' );
					} else {
						$values[] = is_scalar( $value ) ? $value : '';
					}
				}
				$field_value = implode( ', ', $values );
				break;
			case 'url':
			case 'page_link':
			case 'link':
				if ( in_array( $field_type, array( 'url', 'page_link' ), true ) ) {
					$field_value = is_array( $field_value ) ? reset( $field_value ) : $field_value;
				} else { // link
					$field_value = is_array( $field_value ) && isset( $field_obj['value']['url'] ) ? $field_obj['value']['url'] : $field_value;
				}
				break;
			case 'file':
				switch ( $return_format ) {
					case 'array':
						$field_value = $field_value['url'] ?? $field_value;
						break;
					case 'id':
						$field_value = wp_get_attachment_url( $field_value ) ? wp_get_attachment_url( $field_value ) : $field_value;
						break;
				}
				break;
			case 'email':
				$email = sanitize_email( $field_value );
				$field_value = ( false !== strpos( $email, '@' ) ) ? sprintf( '%1$s#actus.%2$s', strstr( $email, '@', true ), ltrim( strstr( $email, '@' ), '@' ) ) : '';
				break;
		}
		return $field_value;
	}
}

/**
 * eac_validate_html_tag
 *
 * @param string $tag
 *
 * @return string
 */
if ( ! function_exists( 'eac_validate_html_tag' ) ) {
	function eac_validate_html_tag( string $tag ): string {
		$allowed_html_tags = array(
			'a',
			'article',
			'aside',
			'button',
			'div',
			'footer',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'header',
			'main',
			'nav',
			'p',
			'section',
			'span',
		);

		return $tag && in_array( strtolower( $tag ), $allowed_html_tags, true ) ? $tag : 'div';
	}
}

/**
 * eac_array_to_html_attrs
 *
 * @param array $attrs
 *
 * @return string
 */
if ( ! function_exists( 'eac_array_to_html_attrs' ) ) {
	function eac_array_to_html_attrs( array $attrs ): string {
		$parts = array();
		foreach ( $attrs as $key => $value ) {
			if ( null === $value || false === $value ) {
				continue;
			}
			// Pour les attributs booléens HTML5 (ex: disabled) on pourrait gérer différemment si nécessaire.
			$parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return implode( ' ', $parts );
	}
}

/**
 * eac_print_svg_icon
 *
 * @param string $svg_name
 *
 * @return string
 */
if ( ! function_exists( 'eac_print_svg_icon' ) ) {
	function eac_print_svg_icon( string $svg_name ): void {
		$picto = array(
			'author'   => '<svg aria-hidden="true" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M313.6 304c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 304 0 364.2 0 438.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-25.6c0-74.2-60.2-134.4-134.4-134.4zM400 464H48v-25.6c0-47.6 38.8-86.4 86.4-86.4 14.6 0 38.3 16 89.6 16 51.7 0 74.9-16 89.6-16 47.6 0 86.4 38.8 86.4 86.4V464zM224 288c79.5 0 144-64.5 144-144S303.5 0 224 0 80 64.5 80 144s64.5 144 144 144zm0-240c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z"></path></svg>',
			'calendar' => '<svg aria-hidden="true" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z"></path></svg>',
			'category' => '<svg aria-hidden="true" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M497.941 225.941L286.059 14.059A48 48 0 0 0 252.118 0H48C21.49 0 0 21.49 0 48v204.118a48 48 0 0 0 14.059 33.941l211.882 211.882c18.744 18.745 49.136 18.746 67.882 0l204.118-204.118c18.745-18.745 18.745-49.137 0-67.882zM112 160c-26.51 0-48-21.49-48-48s21.49-48 48-48 48 21.49 48 48-21.49 48-48 48zm513.941 133.823L421.823 497.941c-18.745 18.745-49.137 18.745-67.882 0l-.36-.36L527.64 323.522c16.999-16.999 26.36-39.6 26.36-63.64s-9.362-46.641-26.36-63.64L331.397 0h48.721a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882z"></path></svg>',
			'arrow-tb' => '<svg aria-hidden="true" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zM273 369.9l135.5-135.5c9.4-9.4 9.4-24.6 0-33.9l-17-17c-9.4-9.4-24.6-9.4-33.9 0L256 285.1 154.4 183.5c-9.4-9.4-24.6-9.4-33.9 0l-17 17c-9.4 9.4-9.4 24.6 0 33.9L239 369.9c9.4 9.4 24.6 9.4 34 0z"></path></svg>',
			'sunrise'  => '<svg aria-hidden="true" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><line x1="32" y1="8" x2="32" y2="16" stroke-width="3" stroke-linecap="round"/><line x1="46" y1="12" x2="41" y2="19" stroke-width="3" stroke-linecap="round"/><line x1="54" y1="20" x2="47" y2="25" stroke-width="3" stroke-linecap="round"/><line x1="18" y1="12" x2="23" y2="19" stroke-width="3" stroke-linecap="round"/><line x1="10" y1="20" x2="17" y2="25" stroke-width="3" stroke-linecap="round"/><path d="M 16 36 A 16 16 0 0 1 48 36" fill="none" stroke-width="4"/><line x1="5" y1="40" x2="59" y2="40" stroke-width="4"/><path d="M 8 50 Q 12 48 16 50 T 24 50" fill="none" stroke-width="3" stroke-linecap="round"/><path d="M 40 50 Q 44 48 48 50 T 56 50" fill="none" stroke-width="3" stroke-linecap="round"/></svg>',
			'sunset'   => '<svg aria-hidden="true" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><line x1="32" y1="8" x2="32" y2="16" stroke-width="3" stroke-linecap="round"/><line x1="46" y1="12" x2="41" y2="19" stroke-width="3" stroke-linecap="round"/><line x1="54" y1="20" x2="47" y2="25" stroke-width="3" stroke-linecap="round"/><line x1="18" y1="12" x2="23" y2="19" stroke-width="3" stroke-linecap="round"/><line x1="10" y1="20" x2="17" y2="25" stroke-width="3" stroke-linecap="round"/><path d="M 16 28 A 16 16 0 0 0 48 28" fill="none" stroke-width="4"/><line x1="5" y1="32" x2="59" y2="32" stroke-width="4"/><path d="M 8 42 Q 12 44 16 42 T 24 42" fill="none" stroke-width="3" stroke-linecap="round"/><path d="M 40 42 Q 44 44 48 42 T 56 42" fill="none" stroke-width="3" stroke-linecap="round"/><path d="M 10 50 Q 14 52 18 50 T 26 50" fill="none" stroke-width="3" stroke-linecap="round"/><path d="M 38 50 Q 42 52 46 50 T 54 50" fill="none" stroke-width="3" stroke-linecap="round"/></svg>',
			'thermo'   => '<svg aria-hidden="true" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M11,30.079c0,0 -0.022,0 -0.03,0c-2.746,-0.016 -4.97,-2.25 -4.97,-5c-0,-1.629 0.78,-3.077 1.988,-3.99l-0,-16.07c-0,-1.66 1.347,-3.007 3.006,-3.007c1.659,0 3.006,1.347 3.006,3.007c-0,-0 -0,16.061 -0,16.061c1.214,0.912 2,2.365 2,3.999c0,2.717 -2.171,4.93 -4.871,4.999l-0.129,0.001m0,-2c1.656,0 3,-1.344 3,-3c0,-1.11 -0.604,-2.079 -1.501,-2.598c-0.309,-0.179 -0.499,-0.509 -0.499,-0.866l0,-16.596c0,-0.556 -0.451,-1.007 -1.006,-1.007c-0.556,0 -1.006,0.451 -1.006,1.007l-0,16.603c-0,0.356 -0.189,0.685 -0.496,0.864c-0.892,0.52 -1.492,1.487 -1.492,2.593c0,1.65 1.335,2.991 2.983,3c0.001,0 0.003,0 0.004,0"/><path d="M17.006,8l7.994,0.001c0.552,0 1,-0.448 1,-1c0,-0.552 -0.448,-1 -1,-1l-7.993,-0.001c-0.552,-0 -1,0.448 -1.001,1c0,0.552 0.448,1 1,1Z"/><path d="M17.006,11.987l6.217,0.001c0.552,-0 1.001,-0.448 1.001,-1c-0,-0.552 -0.448,-1 -1,-1l-6.217,-0.001c-0.552,-0 -1,0.448 -1.001,0.999c0,0.552 0.448,1.001 1,1.001Z"/><path d="M17.006,15.973l7.994,0.001c0.552,0 1,-0.448 1,-1c0,-0.552 -0.448,-1 -1,-1l-7.993,-0.001c-0.552,0 -1,0.448 -1.001,1c0,0.552 0.448,1 1,1Z"/><path d="M17.006,19.96l6.217,0.001c0.552,-0 1.001,-0.448 1.001,-1c-0,-0.552 -0.448,-1 -1,-1l-6.217,-0.001c-0.552,-0 -1,0.448 -1.001,1c0,0.552 0.448,1 1,1Z"/><circle cx="11.001" cy="25.008" r="1"/></svg>',
			'cloud'    => '<svg aria-hidden="true" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg"><path d="M 12 38 Q 12 28 20 26 Q 22 16 32 16 Q 42 16 44 26 Q 52 28 52 38 Q 52 46 46 50 L 18 50 Q 12 46 12 38 Z" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M 8 50 Q 8 42 14 40 Q 15 32 24 30 Q 26 22 34 22 Q 42 22 44 30 Q 50 32 50 42 Q 50 48 46 52 L 16 52 Q 8 48 8 50 Z" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" opacity="0.6"/></svg>',
			'rain'     => '<svg aria-hidden="true" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xml:space="preserve"><g><path class="st0" d="M212.969,278.609c15.938-44.594,56.344-76.75,103.688-82.141c-15.469-44.016-57.375-75.5-106.656-75.5 c-62.438,0-113.109,50.594-113.109,113.047c0,29.781,11.531,56.859,30.375,77.078c21.672-20.156,50.734-32.547,82.672-32.547 C210.938,278.547,211.906,278.609,212.969,278.609z" fill="none" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" opacity="1"></path><rect x="193.516" y="24.047" class="st0" width="32.938" height="63.406"></rect><polygon class="st0" points="117.984,118.734 73.156,73.906 49.859,97.188 94.688,142.031"></polygon><rect y="217.563" class="st0" width="63.406" height="32.938"></rect><path class="st0" d="M49.859,370.844l23.266,23.328l17.578-17.594c2.766-14.109,7.969-27.344,15.219-39.266l-11.266-11.266 L49.859,370.844z" fill="none" stroke-width="25" stroke-linecap="round" stroke-linejoin="round" opacity="1"></path><polygon class="st0" points="370.125,97.188 346.813,73.891 302,118.734 325.281,142.031"></polygon><path class="st0" d="M422.578,304.344c-9.234-42.828-47.281-74.922-92.859-74.922c-46.063,0-84.438,32.75-93.156,76.25 c-5.156-0.891-10.438-1.453-15.844-1.453c-50.75,0-91.875,41.125-91.875,91.859c0,50.75,41.125,91.875,91.875,91.875 c43.359,0,156.75,0,199.406,0c50.75,0,91.875-41.125,91.875-91.875C512,346.156,472.188,305.641,422.578,304.344z" fill="none" stroke-width="30" stroke-linecap="round" stroke-linejoin="round" opacity="1"></path></g></svg>',
			'wind'     => '<svg aria-hidden="true" viewBox="0 0 63 48" xmlns="http://www.w3.org/2000/svg"><path d="M34.8,8.5 C34.8,3.8 38.7,0 43.4,0 C48.2,0 52,3.8 52,8.5 C52,13.2 48.1,17 43.4,17 L4,17" id="Shape" sketch:type="MSShapeGroup"></path><path d="M47,30.5 C47,34.6 50.3,38 54.5,38 C58.6,38 62,34.6 62,30.5 C62,26.4 58.7,23 54.5,23 L20,23" id="Shape" sketch:type="MSShapeGroup"></path><path d="M27,38.5 C27,42.6 30.3,46 34.4,46 C38.5,46 41.8,42.7 41.8,38.5 C41.8,34.4 38.5,31 34.4,31 L0,31" id="Shape" sketch:type="MSShapeGroup"></path></svg>',
			'gust'     => '<svg aria-hidden="true" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M29.3164,8.0513l-18-6A1,1,0,0,0,10.4,2.2L4,7V2H2V30H4V11l6.4,4.8a1,1,0,0,0,.9165.1489l18-6a1,1,0,0,0,0-1.8974ZM10,13,4.6665,9,10,5Zm4-.0542-2,.667V4.3872l2,.667Zm4-1.333-2,.6665V5.7207l2,.6665Zm2-.667V7.0542L25.8379,9Z" transform="translate(0 0)"/><path d="M20,22a4,4,0,0,0-8,0h2a2,2,0,1,1,2,2H8v2h8A4.0045,4.0045,0,0,0,20,22Z" transform="translate(0 0)"/><path d="M26,22a4.0045,4.0045,0,0,0-4,4h2a2,2,0,1,1,2,2H12v2H26a4,4,0,0,0,0-8Z" transform="translate(0 0)"/></svg>',
			'precip'   => '<svg aria-hidden="true" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg" <g><path d="M49.0430717,18.8657532c-1.3300781-1.5900269-3.1901855-2.6700439-5.2202148-3.0300303   c0.4301758-2.1300049-0.0297852-4.3400269-1.3198242-6.1799927c-1.1201172-1.6300049-2.8601074-2.7999873-4.7700195-3.2200313   c-0.9799805-0.2199707-1.9501953-0.25-2.8901367-0.1199951c-1.5297852-0.2099609-3.0400391,0.0100098-4.3999023,0.6300049   c-2.5-2.3899536-5.8601074-3.549988-9.3300781-3.2399905c-1.6398926-0.1499634-3.3398438,0.0300293-5.0100098,0.5800173   c-4.9199219,1.5999756-8.1098633,6.2499995-7.9499512,11.6099854c-4.0800786,0.8200073-7.0800786,4.2999878-7.2399907,8.5300293   c-0.0800781,2.3199463,0.7800294,4.5499878,2.4499512,6.2799683c1.75,1.8200073,4.2001953,2.8599854,6.7099614,2.8599854   h31.6201172c4.7700195,0,8.8701172-3.5599976,9.3500977-8.1199951   C51.2930717,23.0357361,50.5828667,20.705719,49.0430717,18.8657532z M39.5127983,32.0657043H10.0728569   c-2.099854,0-4.1599126-0.8699951-5.6298833-2.3999634c-1.3798828-1.4400024-2.1000977-3.2800293-2.0300293-5.1799927   c0.1398926-3.7300415,2.9299316-6.7700195,6.6301274-7.2200317c0.1899414-0.0200195,0.3798828-0.1199951,0.5-0.2799683   c0.119873-0.1600342,0.1799316-0.3600464,0.1499023-0.5599976c-0.0200195-0.1600342-0.0300293-0.3200073-0.0400391-0.4900522   c-0.1699219-4.7099609,2.6000977-8.8299561,6.9101563-10.2299805c1.1499023-0.3699951,2.3198242-0.5499878,3.4697266-0.5499878   c3.2299805,0,6.2900391,1.4500122,8.3100586,4.1099844c0.130127,0.1699829,0.3200684,0.2700195,0.5200195,0.2900391   C29.0728569,9.585722,29.2828178,9.5157146,29.4429741,9.3757c1.5698242-1.4099727,3.6799316-1.940002,5.7900391-1.47998   c1.5400391,0.3499751,2.9499512,1.2999873,3.8598633,2.6099849c1.1899414,1.710022,1.5200195,3.8099976,0.9001465,5.7399921   c-0.0700684,0.2200317-0.0300293,0.460022,0.0998535,0.6500244c0.1201172,0.1900024,0.3300781,0.3099976,0.5600586,0.3300171   c1.9799805,0.1400146,3.7800293,1.0700073,5.0600586,2.5999756c1.2800293,1.5300293,1.8598633,3.4700317,1.6599121,5.4700317   C46.9730034,29.0957336,43.5230522,32.0657043,39.5127983,32.0657043z"/><path d="M24.909771,35.6574402c-0.7734375,0-1.480957,0.3920898-1.8925781,1.0498047   c-1.9956055,3.1865234-3.0073242,5.4536133-3.0073242,6.737793c0,2.7016602,2.1982422,4.8999023,4.8999023,4.8999023   s4.8999023-2.1982422,4.8999023-4.8999023c0-1.2851563-1.0117188-3.5522461-3.0073242-6.737793   C26.390728,36.04953,25.6832085,35.6574402,24.909771,35.6574402z M28.3096733,43.4450378   c0,1.8745117-1.5253906,3.3999023-3.3999023,3.3999023s-3.3999023-1.5253906-3.3999023-3.3999023   c0-0.9560547,1.0126953-3.121582,2.7788086-5.9418945c0.1376953-0.2197266,0.3637695-0.3457031,0.6210938-0.3457031   s0.4833984,0.1259766,0.6210938,0.3457031C27.296978,40.3224792,28.3096733,42.4880066,28.3096733,43.4450378z"/><path d="M13.5030327,37.5257263c2.0898438,3.3200073,2.0898438,4.3599854,2.0898438,4.710022   c0,1.9699707-1.6098633,3.5700073-3.5800781,3.5700073c-1.9599609,0-3.5698242-1.6000366-3.5698242-3.5700073   c0-0.3500366,0-1.3900146,2.0800781-4.710022c0.329834-0.5200195,0.8898926-0.8300171,1.4897461-0.8300171   C12.6229057,36.6957092,13.1829643,37.0057068,13.5030327,37.5257263z"/><path d="M41.5127983,43.7357483c0,1.9699707-1.5998535,3.5700073-3.5698242,3.5700073s-3.5700684-1.6000366-3.5700684-3.5700073   c0-0.3500366,0-1.3900146,2.0800781-4.710022c0.329834-0.5200195,0.8798828-0.8300171,1.4899902-0.8300171   c0.6098633,0,1.1699219,0.3099976,1.4899902,0.8300171C41.5127983,42.3457336,41.5127983,43.3857117,41.5127983,43.7357483z"/></g></svg>',
			'press'    => '<svg aria-hidden="true" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" <g><path d="M53,24c0-11.6-9.4-21-21-21s-21,9.4-21,21c0,8,4.5,14.9,11,18.5V47c0,1.7,1.3,3,3,3h2v10c0,0.6,0.4,1,1,1h8    c0.6,0,1-0.4,1-1V50h2c1.7,0,3-1.3,3-3v-4.5C48.5,38.9,53,32,53,24z M35,59h-6v-9h6V59z M40,47c0,0.6-0.4,1-1,1H25    c-0.6,0-1-0.4-1-1v-3.6c2.5,1,5.2,1.6,8,1.6s5.5-0.6,8-1.6V47z M32,43c-10.5,0-19-8.5-19-19S21.5,5,32,5s19,8.5,19,19    S42.5,43,32,43z"/><path d="M32,7c-9.4,0-17,7.6-17,17s7.6,17,17,17s17-7.6,17-17S41.4,7,32,7z M32,39c-8.3,0-15-6.7-15-15S23.7,9,32,9s15,6.7,15,15    S40.3,39,32,39z"/><path d="M32,11c-7.2,0-13,5.8-13,13c0,0.6,0.4,1,1,1s1-0.4,1-1c0-6.1,4.9-11,11-11s11,4.9,11,11c0,0.6,0.4,1,1,1s1-0.4,1-1    C45,16.8,39.2,11,32,11z"/><path d="M40.2,20.3l-6.9,7.9C32.9,28.1,32.5,28,32,28c-2.2,0-4,1.8-4,4s1.8,4,4,4s4-1.8,4-4c0-1-0.4-1.9-1-2.6l6.8-7.7    c0.4-0.4,0.3-1-0.1-1.4C41.2,19.9,40.6,19.9,40.2,20.3z M32,34c-1.1,0-2-0.9-2-2s0.9-2,2-2s2,0.9,2,2S33.1,34,32,34z"/></g></svg>',
			'humid'    => '<svg aria-hidden="true" viewBox="0 0 85.504 85.504" xmlns="http://www.w3.org/2000/svg"><path d="M42.7522,84.8517A34.2725,34.2725,0,0,1,8.5964,50.54c0-16.4932,14.4585-33.3033,28.6021-47.5718a7.8165,7.8165,0,0,1,11.054-.053l.0529.053C62.4485,17.2369,76.9075,34.047,76.9075,50.54A34.2723,34.2723,0,0,1,42.7522,84.8517Zm-.0005-73.2774C34.4373,20.0153,18.5964,36.8478,18.5964,50.54a24.1561,24.1561,0,1,0,48.3111.3256q.0012-.1628,0-.3256C66.9075,36.8478,51.0667,20.0153,42.7517,11.5743Z"></path><path d="M40.1223,61.9142a3.5,3.5,0,0,1,0-7,8.4161,8.4161,0,0,0,8.375-8.4385,3.5,3.5,0,0,1,7,0A15.4242,15.4242,0,0,1,40.1223,61.9142Z"></path></svg>',
			'warrow'   => '<svg aria-hidden="true" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="M 50 5 L 20 90 L 50 65 L 80 90 Z" fill="currentColor" stroke="currentColor" stroke-width="5" stroke-linejoin="miter" stroke-linecap="butt"></path></svg>',
			'marker'   => '<svg aria-hidden="true" viewBox="0 0 24 28" width="22" height="26" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><line x1="12" y1="23" x2="12" y2="28"></line><circle fill="currentColor" cx="12" cy="10" r="3"></circle></svg>',
			'uv'       => '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="currentColor"><path d="M13,30H9a2.0027,2.0027,0,0,1-2-2V20H9v8h4V20h2v8A2.0027,2.0027,0,0,1,13,30Z" transform="translate(0 0)"/><polygon points="25 20 23.25 20 21 29.031 18.792 20 17 20 19.5 30 22.5 30 25 20"/><rect x="15" y="2" width="2" height="5"/><rect x="21.6675" y="6.8536" width="4.958" height="1.9998" transform="translate(1.5191 19.3744) rotate(-45)"/><rect x="25" y="15" width="5" height="2"/><rect x="2" y="15" width="5" height="2"/><rect x="6.8536" y="5.3745" width="1.9998" height="4.958" transform="translate(-3.253 7.8535) rotate(-45)"/><path d="M22,17H20V16a4,4,0,0,0-8,0v1H10V16a6,6,0,0,1,12,0Z" transform="translate(0 0)"/></svg>',
		);

		$svg = array_key_exists( $svg_name, $picto ) ? $picto[ $svg_name ] : '';
		echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * eac_get_prefered_fluid_font
 *
 * Génère l'expression responsive (offset en rem + slope * vw) et la clamp complète.
 * L'offset est converti en rem (via $baseFontSize) ; la pente est exprimée en vw.
 *
 * @param string|float|int $min           Valeur min (ex: '0.9rem' ou 0.9).
 * @param string|float|int $max           Valeur max (ex: '1.3rem' ou 1.3).
 *
 * @return array{middle: string, clamp: string}
 */
if ( ! function_exists( 'eac_get_prefered_fluid_font' ) ) {
	function eac_get_prefered_fluid_font( string $min, string $max ): array {
		$vp_min = 320; // viewport min et max
		$vp_max = 1200;
		$base_font_size = 16.0;
		$precision = 4;
		$trim = static fn( $s ) => trim( (string) $s );

		$min_val = $trim( $min );
		$max_val = $trim( $max );

		if ( '' === $min_val || '' === $max_val ) {
			return array(
				'middle' => '',
				'clamp' => '',
			);
		}

		if ( ! is_numeric( $min_val ) || ! is_numeric( $max_val ) ) {
			return array(
				'middle' => '',
				'clamp' => '',
			);
		}

		$min_num = (float) $min_val; // en rem
		$max_num = (float) $max_val; // en rem

		// convertir en px pour les calculs
		$min_px = $min_num * $base_font_size;
		$max_px = $max_num * $base_font_size;

		$delta_vp = (float) $vp_max - (float) $vp_min;
		if ( 0.0 === $delta_vp ) {
			return array(
				'middle' => '',
				'clamp' => '',
			);
		}

		$slope_per_px = ( $max_px - $min_px ) / $delta_vp;
		$slope_vw = $slope_per_px * 100.0;

		// offset en px pour que à vp_min la formule donne min_px
		$offset_px = $min_px - ( $slope_per_px * (float) $vp_min );

		// convertir offset px en rem
		$offset_rem = $offset_px / $base_font_size;

		$fmt = static function ( $n ) use ( $precision ) {
			$s = number_format( (float) $n, $precision, '.', '' );
			return rtrim( rtrim( $s, '0' ), '.' );
		};

		$offset_str = $fmt( $offset_rem ) . 'rem';
		$slope_str  = $fmt( $slope_vw ) . 'vw';
		$middle     = $offset_str . ' + ' . $slope_str;

		$fmt_rem = static function ( $n ) use ( $precision ) {
			$s = number_format( (float) $n, $precision, '.', '' );
			$s = rtrim( rtrim( $s, '0' ), '.' );
			return $s . 'rem';
		};

		$min_str = $fmt_rem( $min_num );
		$max_str = $fmt_rem( $max_num );

		$clamp = 'clamp(' . $min_str . ', ' . $middle . ', ' . $max_str . ')';

		return array(
			'middle' => $middle,
			'clamp' => $clamp,
		);
	}
}
