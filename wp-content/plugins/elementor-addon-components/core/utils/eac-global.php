<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * eac_register_shortcode
 *
 * Crée le point d'accès Shortcode pour les images externes 'eac_img_shortcode'
 * Crée le point d'accès pour l'intégration des Templates Elementor
 *
 * @since 1.5.3
 */
/**
global $shortcode_tags;
write_log( $shortcode_tags );
*/
function eac_register_shortcode() {
	add_shortcode( 'eac_img', 'eac_img_shortcode' );
	add_shortcode( 'eac_elementor_tmpl', 'eac_elementor_add_tmpl' );
	if ( \EACCustomWidgets\Core\Eac_Config_Elements::is_widget_active( 'breadcrumbs' ) ) {
		add_shortcode( 'eac_breadcrumb', 'eac_shortcode_breadcrumb' );
	}
	if ( class_exists( 'WooCommerce' ) ) {
		add_shortcode( 'eac_product_rating', 'eac_display_product_rating' );
		add_shortcode( 'eac_widget_mini_cart', 'eac_display_widget_mini_cart' );
	}
	if ( class_exists( 'ACF' ) ) {
		add_shortcode( 'eac_image_gallery', 'eac_display_acf_image_gallery' );
	}
}
add_action( 'init', 'eac_register_shortcode', 25 );

/** Affiche le mini-cart */
if ( ! function_exists( 'eac_display_widget_mini_cart' ) ) {
	function eac_display_widget_mini_cart( $params = array() ) {
		$args = shortcode_atts(
			array(
				'title' => '',
			),
			$params,
			'eac_widget_mini_cart'
		);
		/**$has_cart = ! is_null( WC()->cart && WC()->cart->get_cart_contents_count() !== 0 );*/
		$title = ! empty( $args['title'] ) ? sanitize_text_field( trim( $args['title'] ) ) : esc_html__( 'Mon panier', 'eac-components' );
		ob_start();
		?>
		<div class="eac_widget_mini_cart">
		<?php the_widget( 'WC_Widget_Cart', array( 'title' => $title ) ); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}

// WooCommerce product rating
if ( ! function_exists( 'eac_display_product_rating' ) ) {
	function eac_display_product_rating( $params = array() ) {
		$args = shortcode_atts(
			array(
				'id' => '',
			),
			$params,
			'eac_product_rating'
		);

		if ( isset( $args['id'] ) && $args['id'] > 0 ) {
			// Get an instance of the WC_Product Object
			$product = wc_get_product( sanitize_text_field( $args['id'] ) );

			// The product average rating (or how many stars this product has)
			$average = $product->get_average_rating();
		}

		if ( isset( $average ) ) {
			return wc_get_rating_html( $average );
		}
	}
}

/**
 * eac_img_shortcode
 * Shortcode d'intégration d'une image avec lien externe, fancybox et caption
 *
 * Ex:  [eac_img src="https://www.cestpascommode.fr/wp-content/uploads/2019/04/fauteuil-louis-philippe-zebre-01.jpg" fancybox="yes" caption="Fauteuil Zèbre"]
 *      [eac_img src="https://www.cestpascommode.fr/wp-content/uploads/2020/04/chaise-victoria-01.jpg" link="https://www.cestpascommode.fr/realisations/chaise-victoria" caption="Chaise Victoria"]
 *      [eac_img link="https://www.cestpascommode.fr/realisations/bergere-louis-xv-et-sa-chaise" embed="yes"]
 *
 * @since 1.6.0
 */
function eac_img_shortcode( $params = array() ) {
	$args = shortcode_atts(
		array(
			'src'      => '',
			'link'     => '',
			'fancybox' => 'no',
			'caption'  => '',
			'embed'    => 'no',
		),
		$params,
		'eac_img'
	);

	$html_default = '';
	$source       = esc_url( $args['src'] );
	$linked       = esc_url( $args['link'] );
	$fancy_box    = in_array( trim( $args['fancybox'] ), array( 'yes', 'no' ), true ) ? trim( $args['fancybox'] ) : 'no';
	$fig_caption  = esc_html( $args['caption'] );
	$embed_link   = in_array( trim( $args['embed'] ), array( 'yes', 'no' ), true ) ? trim( $args['embed'] ) : 'no';

	if ( empty( $source ) ) {
		return $html_default; }

	if ( 'yes' === $embed_link ) {
		// print_r($linked); // Embed le lien
	} elseif ( ! empty( $linked ) ) { // Lien externe
		$html_default =
			'<figure>
                <a href="' . $linked . '">
                    <img src="' . $source . '" alt="' . $fig_caption . '" />
                    <figcaption>' . $fig_caption . '</figcaption>
                </a>
            </figure>';
		// @since 1.6.2 Fancybox
	} elseif ( 'yes' === $fancy_box ) {
		$html_default =
			'<figure>
                <a href="' . $source . '" data-elementor-open-lightbox="no" data-fancybox="eac-img-shortcode" data-caption="' . $fig_caption . '">
                    <img src="' . $source . '" alt="' . $fig_caption . '"/>
                    <figcaption>' . $fig_caption . '</figcaption>
                </a>
            </figure>';
	} else {
		$html_default =
			'<figure>
                <img src="' . $source . '" alt="' . $fig_caption . '"/>
                <figcaption>' . $fig_caption . '</figcaption>
            </figure>';
	}
	return $html_default;
}

/**
 * eac_elementor_tmpl
 * Shortcode d'intégration d'un modèle Elementor
 *
 * Ex: [eac_elementor_tmpl id="XXXXX"]
 *
 * @since 1.6.0
 */
function eac_elementor_add_tmpl( $params = array() ) {
	$args = shortcode_atts(
		array(
			'id'  => '',
			'css' => 'false',
		),
		$params,
		'eac_elementor_tmpl'
	);

	$id_tmpl  = absint( sanitize_text_field( trim( $args['id'] ) ) );
	if ( empty( $id_tmpl ) ) {
		return '';
	}
	$css_tmpl = 'false' === sanitize_text_field( trim( $args['css'] ) ) ? false : true;
	$post_tmpl = get_posts(
		array(
			'post_type' => get_post_type( $id_tmpl ),
			'post__in' => array( $id_tmpl ),
		)
	);

	if ( is_wp_error( $post_tmpl ) || empty( $post_tmpl ) ) {
		return '';
	}

	// Évite la récursivité
	if ( get_the_ID() === $id_tmpl ) {
		return esc_html__( 'ID du modèle ne peut pas être le même que le modèle actuel', 'eac-components' );
	}

	$id_tmpl = apply_filters( 'wpml_object_id', $id_tmpl, \Elementor\TemplateLibrary\Source_Local::CPT, true );

	return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id_tmpl );
}

/**
 * eac_display_acf_image_gallery
 *
 * Affiche le contenu d'une galerie créée avec le champ personnalisé 'eac_gallery'
 * [eac_image_gallery type="post|user" field="field_name" id="get_theID()|Option page ID" size="medium" title="true|false" fb="true|false"]
 *
 * @since 2.3.0
 * @since 2.3.1 Ajout de la fancybox
 */
function eac_display_acf_image_gallery( $params = array() ) {
	$args = shortcode_atts(
		array(
			'field' => '',
			'id'    => '',
			'size'  => 'medium',
			'title' => 'true',
			'fb'    => 'false',
			'type'  => 'post',
			'gap'   => '20',
		),
		$params,
		'eac_image_gallery'
	);

	$field = sanitize_text_field( trim( $args['field'] ) );
	$id    = ! empty( $args['id'] ) ? absint( sanitize_text_field( trim( $args['id'] ) ) ) : get_the_ID();
	$size  = sanitize_text_field( trim( $args['size'] ) );
	$title = filter_var( $args['title'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	$fb    = filter_var( $args['fb'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	$type  = sanitize_text_field( trim( $args['type'] ) );
	$gap   = absint( sanitize_text_field( trim( $args['gap'] ) ) ) . 'px';
	if ( empty( $field ) ) {
		return '';
	}

	/**
	 * get_field
	 * Impérativement 3ème param à false
	 * sinon renvoie les posts attachments au lieu des ID
	 */
	if ( 'user' === $type ) {
		$attachment_ids = get_field( $field, 'user_' . get_the_author_meta( 'ID' ), false );
	} else {
		$attachment_ids = get_field( $field, $id, false );
	}

	ob_start();
	if ( $attachment_ids && is_array( $attachment_ids ) ) : ?>
		<div class='acf-gallery__container'>
			<?php
			foreach ( $attachment_ids as $attachment_id ) :
				$attachment = \EACCustomWidgets\Core\Utils\Eac_Tools_Util::wp_get_attachment_data( $attachment_id, $size );
				if ( ! $attachment || empty( $attachment ) ) {
					continue;
				}
				$attach_title = ucfirst( $attachment['title'] );
				$media_url    = ! empty( $attachment['media_url'] ) ? $attachment['media_url'] : false; ?>
				<div class='acf-gallery__container-image'>
					<?php if ( $fb ) :
						$aria_label = sprintf( '%1$s %2$s', esc_html__( "Voir l'image", 'eac-components' ), $attach_title ); ?>
						<a class='eac-accessible-link' href="<?php echo esc_url( $attachment['src'] ); ?>" data-elementor-open-lightbox='no' data-fancybox='acf-field-gallery' data-caption="<?php echo esc_attr( $attach_title ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
					<?php elseif ( $media_url ) :
						$aria_label = sprintf( '%1$s %2$s', esc_html__( "Voir l'article", 'eac-components' ), $attach_title ); ?>
						<a class='eac-accessible-link' href="<?php echo esc_url( $media_url ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
					<?php endif; ?>
						<img class='img-focusable acf-gallery__image' src="<?php echo esc_url( $attachment['src'] ); ?>" srcset="<?php echo esc_attr( $attachment['srcset'] ); ?>" sizes="<?php echo esc_attr( $attachment['srcsize'] ); ?>" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>"/>
						<?php if ( $title ) : ?>
							<div class='acf-gallery__caption'><?php echo esc_html( $attach_title ); ?></div>
						<?php endif; ?>
					<?php if ( $media_url || $fb ) : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach;
			?>
			<div class='eac-skip-grid' tabindex='0'>
				<span class='visually-hidden'><?php esc_html_e( 'Sortir de la galerie', 'eac-components' ); ?></span>
			</div>
		</div>
		<script>
			/** Accessibility */
			jQuery('.acf-gallery__container').on('keydown', (evt) => {
				const selecteur = 'button, a';
				const id = evt.code || evt.key || 0;
				const $targetArticleFirst = jQuery('.acf-gallery__container').find(selecteur).first();
				const $targetArticleLast = jQuery('.acf-gallery__container').find(selecteur).last();

				if (('Tab' === id && !evt.shiftKey) || ('Tab' === id && evt.shiftKey)) {
					return true;
				} else if ('Home' === id) {
					evt.preventDefault();
					$targetArticleFirst.trigger('focus');
				} else if ('End' === id) {
					evt.preventDefault();
					$targetArticleLast.trigger('focus');
				} else if ('Escape' === id) {
					jQuery('.acf-gallery__container .eac-skip-grid').trigger('focus');
				}
			});
		</script>
		<style>
			/* Gallery wrapper class */
			.acf-gallery__container {
				display: grid;
				grid-template-columns: repeat(5, 1fr);
				gap: <?php echo esc_html( $gap ); ?>;
				margin-block: 20px;
			}
			/* Image wrapper class */
			.acf-gallery__container-image {
				position: relative;
				display: block;
				background-color: #fff;
				border: 2px solid antiquewhite;
				border-radius: 4px;
				text-align: center;
				overflow: hidden;
			}
			.acf-gallery__container-image a {
				position: relative;
				display: block;
				block-size: 100%;
			}
			/* Image class */
			.acf-gallery__image {
				display: block;
				position: relative;
				block-size: auto;
				inline-size: 100%;
				aspect-ratio: 1 / 1;
				object-fit: cover;
			}
			/* Title class */
			.acf-gallery__caption {
				position: relative;
				padding-block: 10px;
				padding-inline: 5px;
				font-size: 1rem;
				color: #1e73be;
				word-wrap: break-word;
				line-height: 1.2;
			}
			.acf-gallery__container a:not([data-fancybox]) .acf-gallery__caption {
				color: red;
				font-weight: 500;
			}
			.acf-gallery__container a:hover .acf-gallery__caption {
				color: #1e73be;
			}
			/* Mode responsive */
			@media (max-width: 880px) {
				.acf-gallery__container {
					grid-template-columns: repeat(4, 1fr);
				}
			}
			@media (max-width: 767px) {
				.acf-gallery__container {
					grid-template-columns: repeat(2, 1fr);
				}
			}
		</style>
	<?php endif;
	return ob_get_clean();
}

/**
 * eac_shortcode_breadcrumb
 *
 * @param array $params
 *
 * @return string
 */
function eac_shortcode_breadcrumb( $params = array() ) {
	$kses_defaults = wp_kses_allowed_html( 'post' );
	$content_args = array(
		'style' => array(),
	);
	$allowed_content = array_merge( $kses_defaults, $content_args );
	$attr = shortcode_atts(
		array(
			'sep'   => '',
			'home'  => '',
			'color' => '',
			'fs'    => '',
		),
		$params,
		'eac_breadcrumb'
	);
	$sep   = ! empty( $attr['sep'] ) ? sanitize_text_field( $attr['sep'] ) : '|';
	$home  = ! empty( $attr['home'] ) ? sanitize_text_field( $attr['home'] ) : esc_html__( 'Accueil', 'eac-components' );
	$color = ! empty( $attr['color'] ) ? sanitize_hex_color( $attr['color'] ) : '#000000';
	$fs    = ! empty( $attr['fs'] ) ? sanitize_text_field( $attr['fs'] ) : '1em';
	$style = '<style>
		.eac-breadcrumbs nav .eac-breadcrumbs-item,
		.eac-breadcrumbs nav .eac-breadcrumbs-item a,
		.eac-breadcrumbs nav .eac-breadcrumbs-separator { color:' . esc_attr( $color ) . '; font-size:' . esc_attr( $fs ) . '; }</style>';

	$args = array(
		'style'         => $style,
		'separator'     => $sep,
		'item_tag'      => 'span',
		'show_title'    => true,
		'trunk_title'   => 0,
		'post_taxonomy' => array(
			'post' => '',
		),
		'labels'        => array(
			'home'       => $home,
			'page_title' => '',
		),
	);

	$breadcrumb = new \EACCustomWidgets\Includes\TemplatesLib\Widgets\Classes\Class_Breadcrumb( $args );
	return wp_kses( $breadcrumb->trail(), $allowed_content );
}

if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( ?string $haystack, ?string $needle ): bool {
		$haystack = $haystack ?? '';
		$needle   = $needle ?? '';

		return '' === $needle || false !== strpos( $haystack, $needle );
	}
}

if ( ! function_exists( 'str_starts_with' ) ) {
	function str_starts_with( ?string $haystack, ?string $needle ): bool {
		$haystack = $haystack ?? '';
		$needle   = $needle ?? '';

		return 0 === strncmp( $haystack, $needle, \strlen( $needle ) );
	}
}

if ( ! function_exists( 'str_ends_with' ) ) {
	function str_ends_with( ?string $haystack, ?string $needle ): bool {
		$haystack = $haystack ?? '';
		$needle   = $needle ?? '';

		if ( '' === $needle || $needle === $haystack ) {
			return true;
		}

		if ( '' === $haystack ) {
			return false;
		}

		$needle_length = \strlen( $needle );

		return $needle_length <= \strlen( $haystack ) && 0 === substr_compare( $haystack, $needle, -$needle_length );
	}
}

/**
 * console_log
 * Affiche des traces dans la console du navigateur
 *
 * @since 1.6.5
 */
if ( ! function_exists( 'console_log' ) ) {
	function console_log( $output, $with_script_tags = true ) {
		$js_code = 'console.log(' . wp_json_encode( $output, JSON_HEX_TAG ) . ');';
		if ( $with_script_tags ) {
			$js_code = '<script>' . $js_code . '</script>';
		}
		echo $js_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/** https://gist.github.com/bahiirwa */
// phpcs:disable WordPress.PHP.DevelopmentFunctions
if ( ! function_exists( 'write_log' ) ) {
	function write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}
// phpcs:enable WordPress.PHP.DevelopmentFunctions

/**
 * eac_add_author_infobox
 *
 * Ajoute le contenu du template Author infobox au contenu d'un post_type/posts
 *
 * @since 1.9.1
 */
function eac_embed_author_infobox( $content ) {
	$options = get_option( 'eac_options_infobox' );

	// Check if we're inside the main loop in a single Post.
	/**if ( is_singular() && in_the_loop() && is_main_query() ) {
		return $content;
	}*/

	// Le composant n'est pas actif, page d'accueil ou pas d'option pour l'infobox
	if ( is_front_page() || false === $options ) {
		return $content;
	}

	/**
	 * Les options de l'infobox
	 *
	 * @since 2.1.0 Sanitize les options
	 */
	$template_id         = absint( $options['post_id'] );     // ID du modèle Elementor
	$template_post_types = esc_html( $options['post_type'] ); // Le post_type qui peut afficher le contenu du template
	$template_position   = esc_html( $options['position'] );  // La position du contenu du template
	$template_post_ids   = array_map( 'absint', $options['post_ids'] ); // La liste des IDs qui peuvent afficher le contenu du template

	// L'article courant
	$current_id        = get_the_ID();
	$current_post_type = get_post_type( $current_id );

	// ID de l'article courant n'est pas dans la liste des articles qui peuvent afficher le template
	if ( is_array( $template_post_ids ) && ! empty( $template_post_ids ) && ! in_array( $current_id, $template_post_ids, true ) ) {
		return $content;
	}

	/**
	$categories = get_the_category($current_id);
	$category_list = wp_list_pluck($categories, 'name');
	console_log($category_list);
	*/

	// Le template Elementor est publié ou le post_type de l'article courant n'est pas le post_type attendu et évite la récursivité
	$template = get_post( $template_id );
	if ( null === $template || 'publish' !== $template->post_status || $current_post_type !== $template_post_types || $current_id === $template_id ) {
		return $content;
	}

	// Filtre wpml
	$template_id = apply_filters( 'wpml_object_id', $template_id, \Elementor\TemplateLibrary\Source_Local::CPT, true );

	// Ajoute le contenu du template selon sa position
	if ( 'before' === $template_position ) {
		return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id ) . $content;
	} else {
		return $content . \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id );
	}
}
/** Priorité 99 pour que le contenu des shortcodes soit affiché avant */
if ( \EACCustomWidgets\Core\Eac_Config_Elements::is_widget_active( 'author-infobox' ) ) {
	add_filter( 'the_content', 'eac_embed_author_infobox', 99 );
}
