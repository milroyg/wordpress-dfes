<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function eac_register_shortcode() {
	add_shortcode( 'eac_img', 'eac_display_image' );

	if ( \EACCustomWidgets\Core\Eac_Load_Config::is_widget_active( 'breadcrumbs' ) ) {
		add_shortcode( 'eac_breadcrumb', 'eac_display_breadcrumb' );
	}
	if ( class_exists( 'WooCommerce', false ) ) {
		add_shortcode( 'eac_product_rating', 'eac_display_product_rating' );
		add_shortcode( 'eac_widget_mini_cart', 'eac_display_widget_mini_cart' );
	}
	if ( class_exists( 'ACF', false ) ) {
		add_shortcode( 'eac_image_gallery', 'eac_display_acf_gallery' );
		add_shortcode( 'eac_gallery', 'eac_display_acf_gallery' );
		add_shortcode( 'eac_repeater', 'eac_display_acf_repeater' );
	}
}
add_action( 'init', 'eac_register_shortcode', 25 );

/** Affiche le mini-cart */
if ( ! function_exists( 'eac_display_widget_mini_cart' ) ) {
	function eac_display_widget_mini_cart( $params = array() ): string {
		$args = shortcode_atts(
			array(
				'title' => '',
			),
			$params,
			'eac_widget_mini_cart'
		);
		/**$has_cart = ! is_null( WC()->cart && WC()->cart->get_cart_contents_count() !== 0 );*/
		$title = ! empty( $args['title'] ) ? sanitize_text_field( trim( $args['title'] ) ) : esc_html__( 'My cart', 'eac-components' );
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
 * eac_display_image
 * Shortcode d'intégration d'une image avec lien externe, fancybox et caption
 *
 * Ex:  [eac_img src="https://www.cestpascommode.fr/wp-content/uploads/2019/04/fauteuil-louis-philippe-zebre-01.jpg" fancybox="yes" caption="Fauteuil Zèbre"]
 *      [eac_img src="https://www.cestpascommode.fr/wp-content/uploads/2020/04/chaise-victoria-01.jpg" link="https://www.cestpascommode.fr/realisations/chaise-victoria" caption="Chaise Victoria"]
 *      [eac_img link="https://www.cestpascommode.fr/realisations/bergere-louis-xv-et-sa-chaise" embed="yes"]
 *
 * @param array $params
 *
 * @return string
 */
function eac_display_image( $params = array() ): string {
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
 * eac_display_acf_gallery
 * Affiche le contenu d'une galerie créée avec le champ personnalisé 'eac_gallery'
 * [eac_image_gallery type="post|user" field="field_name" id="get_theID()|Option page ID" size="medium" title="true|false" fb="true|false"]
 *
 * @param array $params
 *
 * @return string
 */
function eac_display_acf_gallery( $params = array() ): string {
	$args = shortcode_atts(
		array(
			'field' => '',
			'id'    => '',
			'size'  => 'medium',
			'title' => 'true',
			'fb'    => 'false',
			'type'  => 'post',
			'gap'   => '20',
			'col'   => '5',
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
	$col   = absint( sanitize_text_field( trim( $args['col'] ) ) );
	if ( empty( $field ) ) {
		return '';
	}

	/**
	 * get_field
	 * Impérativement 3ème param à false
	 * sinon renvoie le contenu de l'attachment au lieu des ID
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
				$attachment = \EACCustomWidgets\Core\Utils\Eac_Tools_Util::wp_get_attachment_data( intval( $attachment_id ), $size );
				if ( empty( $attachment ) ) {
					continue;
				}
				$attach_title = ucfirst( $attachment['title'] );
				/**$media_url = get_post_meta( $attachment_id, 'eac_media_url', true );*/
				$media_url    = ! empty( $attachment['media_url'] ) ? $attachment['media_url'] : false; ?>
				<div class='acf-gallery__container-image'>
					<?php if ( $fb ) :
						$aria_label = sprintf( '%1$s - %2$s', esc_html__( 'View image', 'eac-components' ), $attach_title ); ?>
						<a class='eac-accessible-link' href="<?php echo esc_url( $attachment['src'] ); ?>" data-elementor-open-lightbox='no' data-fancybox='acf-field-gallery' data-caption="<?php echo esc_attr( $attach_title ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
					<?php elseif ( $media_url ) :
						$aria_label = sprintf( '%1$s - %2$s', esc_html__( 'Read post', 'eac-components' ), $attach_title ); ?>
						<a class='eac-accessible-link' href="<?php echo esc_url( $media_url ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>">
					<?php endif; ?>
						<img class='eac-accessible-img acf-gallery__image' src="<?php echo esc_url( $attachment['src'] ); ?>" srcset="<?php echo esc_attr( $attachment['srcset'] ); ?>" sizes="<?php echo esc_attr( $attachment['srcsize'] ); ?>" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>"/>
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
				<span class='visually-hidden'><?php esc_html_e( 'Exit gallery', 'eac-components' ); ?></span>
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
				grid-template-columns: repeat(<?php echo esc_attr( $col ); ?>, 1fr);
				gap: <?php echo esc_attr( $gap ); ?>; /** clamp(1rem, 3vw, 10rem); */
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
 * eac_display_acf_repeater
 *
 * @param array $params
 *
 * @return string
 */
function eac_display_acf_repeater( $params = array() ): string {
	$acf_supported_fields = array(
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

	$args = shortcode_atts(
		array(
			'field' => '',
			'id'    => get_the_ID(),
			'col'   => '4',
			'mode'  => 'grid',
			'gap'   => '20',
		),
		$params,
		'eac_repeater'
	);

	$field = sanitize_text_field( trim( $args['field'] ) );
	$id    = absint( sanitize_text_field( trim( $args['id'] ) ) );
	$col   = absint( sanitize_text_field( trim( $args['col'] ) ) );
	$mode  = sanitize_text_field( trim( $args['mode'] ) );
	$gap   = absint( sanitize_text_field( trim( $args['gap'] ) ) ) . 'px';
	if ( empty( $field ) ) {
		return '';
	}

	$count_of_row  = is_countable( get_field( $field, $id ) ) ? count( get_field( $field, $id ) ) : 0;
	if ( 0 === $count_of_row ) {
		return '';
	}
	$wrapper     = 'acf-repeater__container-' . uniqid();
	$wrapper_dot = '.' . $wrapper;
	$inner_wrapper = 'acf-repeater__inner-wrapper ' . $mode;

	ob_start();
	?>
	<div class="<?php echo esc_attr( $wrapper ); ?>">
		<?php
		while ( have_rows( $field, $id ) ) :
			$the_row = the_row();
			$has_repeater_content = false; ?>
			<article class='acf-repeater__wrapper'>
				<div class="<?php echo esc_attr( $inner_wrapper ); ?>">
					<?php foreach ( $the_row as $field_key => $any_value ) :
						$sub_field = get_sub_field_object( $field_key );
						if ( $sub_field && in_array( $sub_field['type'], $acf_supported_fields, true ) ) :
							$field_value = $sub_field['value'];
							$field_label = $sub_field['label'];
							$field_name  = $sub_field['_name'];
							if ( 'image' === $sub_field['type'] && ! empty( $field_value ) ) :
								if ( $has_repeater_content ) :
									$has_repeater_content = false; ?>
									</div>
								<?php endif;
								switch ( $sub_field['return_format'] ) {
									case 'array':
										$field_value = $sub_field['value']['ID'];
										break;
									case 'url':
										$field_value = attachment_url_to_postid( $sub_field['value'] );
										break;
								}

								$atributs = array(
									'class'    => 'attachment-large size-large acf-repeater__img',
									'loading'  => 'lazy',
									'decoding' => 'async',
								);
								/**<?php echo wp_get_attachment_image( intval( $field_value ), 'large', false, $atributs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>*/
								$attachment = \EACCustomWidgets\Core\Utils\Eac_Tools_Util::wp_get_attachment_data( intval( $field_value ), 'large' );
								if ( ! empty( $attachment ) ) : ?>
									<div class="acf-repeater__wrapper-img <?php echo esc_attr( $field_name ); ?>">
										<img class='acf-repeater__img' src="<?php echo esc_url( $attachment['src'] ); ?>" srcset="<?php echo esc_attr( $attachment['srcset'] ); ?>" sizes="<?php echo esc_attr( $attachment['srcsize'] ); ?>" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>"/>
									</div>
								<?php endif;
							elseif ( 'text' === $sub_field['type'] && ! empty( $field_value ) ) :
								if ( ! $has_repeater_content ) :
									$has_repeater_content = true; ?>
									<div class='acf-repeater__wrapper-content'>
								<?php endif;
								$field_value = sprintf( '%1$s %2$s %3$s', $sub_field['prepend'], $sub_field['value'], $sub_field['append'] ); ?>
								<div class="acf-repeater__text <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>
							<?php elseif ( 'textarea' === $sub_field['type'] && ! empty( $field_value ) ) :
								if ( ! $has_repeater_content ) :
									$has_repeater_content = true; ?>
									<div class='acf-repeater__wrapper-content'>
								<?php endif;
								?>
								<div class="acf-repeater__text <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>
							<?php elseif ( 'select' === $sub_field['type'] && ! empty( $field_value ) ) :
								if ( ! $has_repeater_content ) :
									$has_repeater_content = true; ?>
									<div class='acf-repeater__wrapper-content'>
								<?php endif;
								$values = array();
								foreach ( $field_value as $value ) :
									if ( 'array' === $sub_field['return_format'] ) :
										$values[] = $value['value'];
									else :
										$values[] = $value;
									endif;
								endforeach;
								$field_value = implode( ', ', $values ); ?>
								<div class="acf-repeater__select <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>
							<?php elseif ( 'number' === $sub_field['type'] && ! empty( $field_value ) ) :
								if ( ! $has_repeater_content ) :
										$has_repeater_content = true; ?>
										<div class='acf-repeater__wrapper-content'>
								<?php endif;
								$field_value = sprintf( '%1$s %2$s %3$s', $sub_field['prepend'], $sub_field['value'], $sub_field['append'] ); ?>
								<div class="acf-repeater__number <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>
							<?php elseif ( in_array( $sub_field['type'], array( 'url', 'link', 'page_link' ), true ) && ! empty( $field_value ) ) :
								if ( ! $has_repeater_content ) :
									$has_repeater_content = true; ?>
									<div class='acf-repeater__wrapper-content'>
								<?php endif;
								if ( in_array( $sub_field['type'], array( 'url', 'page_link' ), true ) ) :
									$field_value = is_array( $sub_field['value'] ) ? $sub_field['value'][0] : $sub_field['value'];
								elseif ( 'link' === $sub_field['type'] ) :
									$field_value  = 'array' === $sub_field['return_format'] ? $sub_field['value']['url'] : $sub_field['value'];
									$field_label  = 'array' === $sub_field['return_format'] ? $sub_field['value']['title'] : $sub_field['label'];
								endif;
								$aria_label = sprintf( '%1$s - %2$s', esc_html__( 'Open link', 'eac-components' ), esc_html( $field_label ) );
								?>
								<div class="acf-repeater__url <?php echo esc_attr( $field_name ); ?>">
									<a href="<?php echo esc_url( $field_value ); ?>" aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php echo esc_html( $field_label ); ?></a>
								</div>
							<?php elseif ( 'email' === $sub_field['type'] && ! empty( $field_value ) ) :
								if ( ! $has_repeater_content ) :
									$has_repeater_content = true; ?>
									<div class='acf-repeater__wrapper-content'>
								<?php endif;
								$label_email = $field_label;
								$email       = sanitize_email( $field_value );
								$email_obf   = \str_contains( $email, '@' ) ? sprintf( '%1$s#actus.%2$s', explode( '@', $email )[0], explode( '@', $email )[1] ) : '';
								?>
								<div class="acf-repeater__email <?php echo esc_attr( $field_name ); ?>">
									<a class='eac-accessible-link obfuscated-link' href='#' data-link="<?php echo esc_attr( $email_obf ); ?>" rel='nofollow' aria-label="<?php echo esc_attr( $label_email ); ?>"><?php echo esc_html( $label_email ); ?></a>
								</div>
							<?php elseif ( 'date_picker' === $sub_field['type'] && ! empty( $field_value ) ) :
								if ( ! $has_repeater_content ) :
									$has_repeater_content = true; ?>
									<div class='acf-repeater__wrapper-content'>
								<?php endif;
								?>
								<div class="acf-repeater__date <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>
							<?php elseif ( 'file' === $sub_field['type'] && ! empty( $field_value ) ) :
								switch ( $sub_field['return_format'] ) {
									case 'array':
										$field_value = $field_value['url'];
										break;
									case 'id':
										$field_value = wp_get_attachment_url( $field_value );
										break;
								}
								$default_img = includes_url( 'images/media/document.png' ); ?>
								<div  class="acf-repeater__file <?php echo esc_attr( $field_name ); ?>">
									<a class='eac-accessible-link' href="<?php echo esc_url( $field_value ); ?>" aria-label="<?php printf( '%1$s %2$s', esc_attr__( 'Open file', 'eac-components' ), esc_attr( $field_label ) ); ?>">
										<img src="<?php echo esc_url( $default_img ); ?>" alt='Default image file' />
									</a>
								</div>
							<?php endif;
						endif;
					endforeach;
					if ( $has_repeater_content ) : ?>
						</div> <!-- Fin div acf-repeater__wrapper-content -->
					<?php endif; ?>
				</div>
			</article>
		<?php endwhile;
		?>
	</div>
	<style>
		/* Repeater wrapper class */
		<?php echo esc_attr( $wrapper_dot ); ?> {
			display: grid;
			grid-template-columns: repeat(<?php echo esc_attr( $col ); ?>, 1fr);
			gap: <?php echo esc_attr( $gap ); ?>;
			margin-block: 20px;
		}
		.acf-repeater__wrapper {
			border: 2px solid antiquewhite;
		}
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper {
			position: relative;
			display: flex;
			flex-direction: column;
			flex-wrap: nowrap;
			overflow: hidden;
			block-size: 100%;
		}
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper.list {
			flex-direction: row;
		}
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__wrapper-content {
			position: relative;
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			align-items: center;
			block-size: 100%;
			inline-size: 100%;
			margin-block: 10px;
			padding-inline: 10px;
			font-size: 1rem;
		}
		<?php echo esc_attr( $wrapper_dot ); ?>  .acf-repeater__inner-wrapper.list .acf-repeater__wrapper-content {
			align-items: flex-start;
		}
		/* type file A et IMG */
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper .acf-repeater__file {
			position: relative;
			max-width: fit-content;
			margin-block-start: 10px;
			margin-inline: auto;
		}
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper.list .acf-repeater__file {
			/*margin-block: auto;*/
		}
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper .acf-repeater__file img {
			display: block;
			position: relative;
		}
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper.list .acf-repeater__file img {
			padding-inline-start: 10px;
		}
		/* Image wrapper class */
		.acf-repeater__wrapper-img {
			position: relative;
			display: block;
		}
		<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper.list .acf-repeater__wrapper-img {
			inline-size: 100%;
		}
		.acf-repeater__wrapper-img a {
			position: relative;
			display: block;
			block-size: 100%;
		}
		/* Image class */
		.acf-repeater__img {
			display: block;
			position: relative;
			block-size: auto;
			inline-size: 100%;
			aspect-ratio: 1 / 1;
			object-fit: cover;
			object-position: 50% 15%;
		}
		/* Mode responsive */
		@media (max-width: 880px) {
			div[class^="acf-repeater__container-"] {
				grid-template-columns: repeat(3, 1fr);
			}
		}
		@media (max-width: 767px) {
			div[class^="acf-repeater__container-"] {
				grid-template-columns: repeat(2, 1fr);
			}
		}
		@media (max-width: 640px) {
			div[class^="acf-repeater__container-"] {
				grid-template-columns: repeat(1, 1fr);
			}
			<?php echo esc_attr( $wrapper_dot ); ?> .acf-repeater__inner-wrapper.list {
				flex-direction: column;
			}
		}
	</style>
	<?php
	return ob_get_clean();
}

/**
 * eac_display_breadcrumb
 *
 * @param array $params
 *
 * @return string
 */
function eac_display_breadcrumb( $params = array() ): string {
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
	$home  = ! empty( $attr['home'] ) ? sanitize_text_field( $attr['home'] ) : esc_html__( 'Home', 'eac-components' );
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
		if ( true === WP_DEBUG && ! is_null( $log ) ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}
// phpcs:enable WordPress.PHP.DevelopmentFunctions

if ( ! function_exists( 'eac_retrieve_all_registered_styles' ) ) {
	function eac_retrieve_all_registered_styles() {
		global $wp_styles;

		// Check if there are any registered styles
		if ( ! empty( $wp_styles->registered ) ) {
			foreach ( $wp_styles->registered as $handle => $style ) {
				write_log( 'Handle: ' . esc_html( $handle ) );
				write_log( 'Src: ' . esc_url( $style->src ) );
				write_log( 'Dependencies: ' . implode( ', ', array_map( 'esc_html', $style->deps ) ) );
				write_log( 'Version: ' . esc_html( $style->ver ) );
				write_log( 'Media: ' . esc_html( $style->args ) );
			}
		} else {
			write_log( 'No styles registered.' );
		}
	}
}

if ( ! function_exists( 'eac_retrieve_all_registered_scripts' ) ) {
	function eac_retrieve_all_registered_scripts() {
		global $wp_scripts;

		// Vérifiez s'il y a des scripts enregistrés
		if ( ! empty( $wp_scripts->registered ) ) {
			foreach ( $wp_scripts->registered as $handle => $script ) {
				write_log( 'Handle: ' . esc_html( $handle ) );
				write_log( 'Src: ' . esc_url( $script->src ) );
				write_log( 'Dependencies: ' . implode( ', ', array_map( 'esc_html', $script->deps ) ) );
				write_log( 'Version: ' . esc_html( $script->ver ) );
				write_log( 'In Footer: ' . ( isset( $script->in_footer ) && $script->in_footer ? 'Yes' : 'No' ) );
			}
		} else {
			write_log( 'No scripts registered.' );
		}
	}
}
