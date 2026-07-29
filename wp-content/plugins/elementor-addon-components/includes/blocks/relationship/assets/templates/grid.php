<?php
/**
 * EAC Relationship Block (fonctions) — inline-localize version
 * Template: grid.php — rendu pour displayType = 'grid et list'
 *
 * @since 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';

/* Préparer wrappers et styles */
$unid          = uniqid();
$wrapper_space = sprintf( 'eac-relationship__block-grid acf-relationship__container-%1$s %2$s %3$s %4$s %5$s', $unid, $container_width, $item_style, $class_name, $display_type );
$wrapper       = preg_replace( '/\s+/', ' ', $wrapper_space );
$wrapper_id    = 'acf-relationship__container-' . $unid;
$title_tag     = eac_validate_html_tag( $heading_title );

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'gap:' . esc_attr( $gap_size );
$wrapper_style_parts[] = 'padding: calc(' . esc_attr( $gap_size ) . ' / 2)';
$wrapper_style_parts[] = 'background-color:' . esc_attr( $block_bg );
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

$article_style_parts = array();
if ( '' === $item_style ) {
	$article_style_parts[] = 'border:' . esc_attr( $item_border );
	$article_style_parts[] = 'border-radius:' . esc_attr( $item_radius['width'] );
}
$article_style_parts[] = 'background-color:' . esc_attr( $item_bg );
$article_attr = 'style="' . implode( '; ', $article_style_parts ) . ';"';

$item_style_parts = array();
$color = ! empty( $color_text ) ? esc_attr( $color_text ) : '#000000';
$item_style_parts[] = 'color:' . esc_attr( $color );
$item_style_parts[] = 'align-items:' . esc_attr( $align_hrz_text );
$item_style_parts[] = 'text-align:' . esc_attr( $align_hrz_text );
$item_style_parts[] = 'justify-content:' . esc_attr( $align_vrt_text );
$item_style_parts[] = 'font-size:' . esc_attr( $font_size );
$item_attr = 'style="' . implode( '; ', $item_style_parts ) . ';"';

$img_style_parts = array();
if ( ! empty( $image_ratio ) ) {
	$img_style_parts[] = 'aspect-ratio:' . esc_attr( $image_ratio );
	$img_style_parts[] = 'object-fit:cover';
	$img_style_parts[] = 'object-position:50% ' . esc_attr( $image_pos );
}
$img_attr = ! empty( $img_style_parts ) ? 'style="' . implode( '; ', $img_style_parts ) . ';"' : '';

/**
 * get_field
 * Impérativement 3ème param à false
 * sinon renvoie le contenu des articles au lieu des IDs
 * et ceci quelque soit l'option 'Format de retour' sélectionnée
 */
if ( 'user' === $type ) {
	$relationship_ids = get_field( $relationship, 'user_' . $source_id, false );
} else {
	$relationship_ids = get_field( $relationship, $source_id, false );
}

// Convertir en array si c'est une string
if ( $relationship_ids && ! is_array( $relationship_ids ) ) {
	$relationship_ids = array( $relationship_ids );
}

// Transformer la liste des champs sélectionnés en un tableau associatif pour un accès plus facile
$post_fields_data = array_combine( $post_fields, array_fill( 0, count( $post_fields ), true ) );
// Il y a un titre et le lien publication sur le titre
$link_on_title = false !== array_search( 'title', $post_fields, true ) && $title_link;

// Définir le mappage entre les clés et les fonctions d'extraction
$field_getters = array(
	'image'        => function ( $post_id ) use ( $image_size ) {
		return eac_get_attachment_data( get_post_thumbnail_id( $post_id ), $image_size );
	},
	'title'        => function ( $post_id ) {
		return get_the_title( $post_id );
	},
	'excerpt'      => function ( $post_id ) {
		return eac_get_post_excerpt( $post_id, 40 );
	},
	'createdDate'  => function ( $post_id ) {
		return get_the_date( '', $post_id );
	},
	'modifiedDate' => function ( $post_id ) {
		return get_the_modified_date( '', $post_id );
	},
	'authorName'   => function ( $post_id ) {
		$post = get_post( $post_id );
		return get_the_author_meta( 'display_name', $post->post_author );
	},
	'authorAvatar' => function ( $post_id ) {
		$post = get_post( $post_id );
		return get_avatar_url( $post->post_author, array( 'size' => 60 ) );
	},
	'category'     => function ( $post_id ) {
		// C'est un produit
		if ( 'product' === get_post_type( $post_id ) ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$categories = wc_get_product_terms( $post_id, 'product_cat' );
				return ! empty( $categories ) ? $categories : array();
			} else {
				return array();
			}
		}
		$categories = get_the_category( $post_id );
		return ! empty( $categories ) ? $categories : array();
	},
	'link'         => function ( $post_id ) {
		return get_permalink( $post_id );
	},
);

if ( $relationship_ids && is_array( $relationship_ids ) && ! empty( $relationship_ids ) ) : ?>
	<div class="<?php echo esc_attr( trim( $wrapper ) . ( $wrapper_classes ? ' ' . $wrapper_classes : '' ) ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
		foreach ( $relationship_ids as $current_id ) :
			$current_post = get_post( $current_id );
			if ( empty( $current_post ) ) :
				continue;
			endif;

			// Boucler sur les champs sélectionnés dans l'ordre
			$post_data = array();
			foreach ( $post_fields_data as $field_key => $field_info ) :
				if ( isset( $field_getters[ $field_key ] ) ) :
					$post_data[ $field_key ] = $field_getters[ $field_key ]( $current_post->ID );
				endif;
			endforeach;
			?>
			<article class='acf-relationship__wrapper' <?php echo $article_attr; // phpcs:ignore ?>>
				<?php if ( isset( $post_data['image'] ) && ! empty( $post_data['image'] ) ) : ?>
					<div class='acf-relationship__wrapper-img'>
						<img class='acf-relationship__img' <?php echo $img_attr; // phpcs:ignore ?> src="<?php echo esc_url( $post_data['image']['src'] ); ?>" srcset="<?php echo esc_attr( $post_data['image']['srcset'] ); ?>" sizes="<?php echo esc_attr( $post_data['image']['srcsize'] ); ?>" width="<?php echo esc_attr( $post_data['image']['width'] ); ?>" height="<?php echo esc_attr( $post_data['image']['height'] ); ?>" alt="" />
					</div>
				<?php endif; ?>
				<div class='acf-relationship__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
					<?php foreach ( $post_fields_data as $field_key => $field_info ) : ?>
						<?php if ( ! isset( $post_data[ $field_key ] ) || empty( $post_data[ $field_key ] ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>

						<?php if ( 'image' === $field_key ) : ?>
							<!-- Image déjà affichée plus haut -->
						<?php elseif ( 'authorAvatar' === $field_key ) : ?>
							<div class='acf-relationship__<?php echo esc_attr( $field_key ); ?>'>
								<img class='eac-accessible-img acf-relationship__author-avatar-img' src="<?php echo esc_url( $post_data[ $field_key ] ); ?>" loading='lazy' alt=''/>
							</div>
						<?php elseif ( 'authorName' === $field_key ) : ?>
							<div class='acf-relationship__<?php echo esc_attr( $field_key ); ?>'>
								<span class='eac-icon-svg'><?php eac_print_svg_icon( 'author' ); ?></span><span><?php echo esc_html( $post_data[ $field_key ] ); ?></span>
							</div>
						<?php elseif ( 'category' === $field_key ) : ?>
							<div class='acf-relationship__<?php echo esc_attr( $field_key ); ?>'>
								<span class='eac-icon-svg'><?php eac_print_svg_icon( 'category' ); ?></span>
								<?php foreach ( $post_data['category'] as $index => $category ) : ?>
									<a href="<?php echo esc_url( get_term_link( $category->term_id, $category->taxonomy ) ); ?>" aria-label="<?php printf( '%1$s %2$s', esc_attr__( 'View publications in category', 'eac-components' ), esc_attr( $category->name ) ); ?>">
										<?php echo esc_html( $category->name ); ?>
									</a>
									<?php if ( $index < count( $post_data['category'] ) - 1 ) : ?>
										<span aria-hidden="true"> | </span>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php elseif ( 'link' === $field_key && ! $link_on_title ) : ?>
							<div class='acf-relationship__<?php echo esc_attr( $field_key ); ?>'>
								<?php
								$classes = array( 'eac-accessible-link' );
								if ( true === $card_link ) {
									$classes[] = 'card-link';
								}
								$rel = $nofollow_link ? 'rel=nofollow' : '';
								$class_attr = implode( ' ', $classes );
								$aria_label = sprintf( '%1$s %2$s', esc_html__( 'Read the post', 'eac-components' ), $post_data['title'] ?? '' );
								?>
								<a href="<?php echo esc_url( $post_data[ $field_key ] ); ?>" class="<?php echo esc_attr( $class_attr ); ?>" <?php echo esc_attr( $rel ); ?> aria-label="<?php echo esc_attr( $aria_label ); ?>">
									<?php echo esc_html__( 'Continue reading', 'eac-components' ); ?>
								</a>
							</div>
						<?php elseif ( 'title' === $field_key ) :
							if ( $title_link && false !== array_search( 'link', $post_fields, true ) ) :
								$classes = array( 'eac-accessible-link' );
								if ( true === $card_link ) {
									$classes[] = 'card-link';
								}
								$rel = $nofollow_link ? 'rel=nofollow' : '';
								$class_attr = implode( ' ', $classes );
								$aria_label = sprintf( '%1$s %2$s', esc_html__( 'Read the post', 'eac-components' ), $post_data['title'] ?? '' );
								?>
								<a href="<?php echo esc_url( $post_data['link'] ); ?>" class="<?php echo esc_attr( $class_attr ); ?>" <?php echo esc_attr( $rel ); ?> aria-label="<?php echo esc_attr( $aria_label ); ?>">
								<?php
							endif;
							printf( '<%1$s class="acf-relationship__%2$s">%3$s</%1$s>', esc_attr( $title_tag ), esc_attr( $field_key ), esc_html( $post_data[ $field_key ] ) );
							if ( $title_link && false !== array_search( 'link', $post_fields, true ) ) : ?>
								</a>
							<?php endif; ?>
						<?php elseif ( 'createdDate' === $field_key ) : ?>
							<div class='acf-relationship__<?php echo esc_attr( $field_key ); ?>'>
								<span class='eac-icon-svg'><?php eac_print_svg_icon( 'calendar' ); ?></span><span><?php printf( '%s %s', esc_html__( 'Created', 'eac-components' ), esc_html( $post_data[ $field_key ] ) ); ?></span>
							</div>
						<?php elseif ( 'modifiedDate' === $field_key ) : ?>
							<div class='acf-relationship__<?php echo esc_attr( $field_key ); ?>'>
								<span class='eac-icon-svg'><?php eac_print_svg_icon( 'calendar' ); ?></span><span><?php printf( '%s %s', esc_html__( 'Modified', 'eac-components' ), esc_html( $post_data[ $field_key ] ) ); ?></span>
							</div>
						<?php elseif ( 'excerpt' === $field_key ) : ?>
							<div class='acf-relationship__<?php echo esc_attr( $field_key ); ?>'>
								<?php echo esc_html( $post_data[ $field_key ] ); ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</article>
		<?php endforeach; ?>
		<div class='eac-skip-grid' tabindex='0'>
			<span class='visually-hidden'><?php esc_html_e( 'Exit relationship', 'eac-components' ); ?></span>
		</div>
	</div> <!-- Fin div container eac-relationship__block-grid -->
	<style>
		/* Mobile (par défaut) */
		.list#<?php echo esc_attr( $wrapper_id ); ?> article {
			flex-direction: column;
			block-size: fit-content;
		}
		.list#<?php echo esc_attr( $wrapper_id ); ?> article .acf-relationship__img {
			block-size: auto;
		}

		/* Tablettes / desktop — appliquer les changements pour >= 576px */
		@media (min-width: 576px) {
			.list#<?php echo esc_attr( $wrapper_id ); ?> article {
				/* valeurs desktop — exemple : ligne et hauteur automatique */
				flex-direction: row;
				block-size: auto;
			}
			.list#<?php echo esc_attr( $wrapper_id ); ?> article .acf-relationship__img {
				/* valeurs desktop */
				block-size: 100%;
			}
		}
	</style>
<?php else :
	printf( '<div class="eac-relationship-empty">%s</div>', esc_html__( 'Relationship is empty.', 'eac-components' ) );
endif; ?>
<?php
