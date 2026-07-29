<?php
/**
 * EAC Gallery Block (fonctions) — inline-localize version
 * Template: grid.php — rendu pour displayType = 'grid'
 *
 * @since 2.4.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';

/* Préparer wrappers et styles */
$unid          = uniqid();
$wrapper_space = sprintf( 'eac-gallery__block-grid acf-gallery__container-%1$s %2$s %3$s %4$s', $unid, $container_width, $item_style, $class_name );
$wrapper       = preg_replace( '/\s+/', ' ', $wrapper_space );
$wrapper_id    = 'acf-gallery__container-' . $unid;
$caption_tag   = eac_validate_html_tag( $heading_caption );

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
 * sinon renvoie le contenu de l'attachment au lieu des ID
 */
if ( 'user' === $type ) {
	$attachment_ids = get_field( $gallery, 'user_' . $source_id, false );
} else {
	$attachment_ids = get_field( $gallery, $source_id, false );
}

if ( $attachment_ids && is_array( $attachment_ids ) && ! empty( $attachment_ids ) ) : ?>
	<div class="<?php echo esc_attr( trim( $wrapper ) . ( $wrapper_classes ? ' ' . $wrapper_classes : '' ) ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
		foreach ( $attachment_ids as $attachment_id ) :
			$attachment = eac_get_attachment_data( $attachment_id, $image_size );
			if ( empty( $attachment ) ) {
				continue;
			}
			$attach_title = ucfirst( $attachment['title'] );
			$attach_descr = ! empty( $attachment['description'] ) ? $attachment['description'] : false;
			$media_url    = ! empty( $attachment['media_url'] ) ? $attachment['media_url'] : false; ?>
			<div class='acf-gallery__wrapper' <?php echo $article_attr; // phpcs:ignore ?>>
				
				<div class='acf-gallery__wrapper-img'>
					<?php if ( $add_fancybox ) :
						$aria_label = sprintf( '%1$s - %2$s', esc_html__( 'View image', 'eac-components' ), $attach_title ); ?>
						<a class='eac-accessible-link' href="<?php echo esc_url( $attachment['src'] ); ?>" data-elementor-open-lightbox='no' data-fancybox="acf-gallery-<?php echo esc_attr( $unid ); ?>" data-caption="<?php echo esc_attr( $attach_title ); ?>" role='button' aria-haspopup='dialog' aria-expanded='false' aria-label="<?php echo esc_attr( $aria_label ); ?>">
					<?php endif; ?>
						<img class='eac-accessible-img acf-gallery__img' <?php echo $img_attr; // phpcs:ignore ?> src="<?php echo esc_url( $attachment['src'] ); ?>" srcset="<?php echo esc_attr( $attachment['srcset'] ); ?>" sizes="<?php echo esc_attr( $attachment['srcsize'] ); ?>" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" alt="" />
					<?php if ( $add_fancybox ) : ?>
						</a>
					<?php endif; ?>
				</div>
				<div class='acf-gallery__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
					<?php if ( $media_url ) :
						$aria_label = sprintf( '%1$s - %2$s', esc_html__( 'Read post', 'eac-components' ), $attach_title );
						$classes = array( 'eac-accessible-link' );
						if ( true === $card_link ) {
							$classes[] = 'card-link';
						}
						$class_attr = implode( ' ', $classes );
						$rel = $nofollow_link ? 'rel=nofollow' : '';
						?>
						<a class="<?php echo esc_attr( $class_attr ); ?>" href="<?php echo esc_url( $media_url ); ?>" <?php echo esc_attr( $rel ); ?> aria-label="<?php echo esc_attr( $aria_label ); ?>">
					<?php endif; ?>
						<?php printf( '<%1$s class="acf-gallery__caption">%2$s</%1$s>', esc_attr( $caption_tag ), esc_html( $attach_title ) ); ?>
					<?php if ( $media_url ) : ?>
						</a>
					<?php endif;
					if ( $add_description && $attach_descr ) : ?>
						<div class='acf-gallery__description'><?php echo esc_html( $attach_descr ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
		<div class='eac-skip-grid' tabindex='0'>
			<span class='visually-hidden'><?php esc_html_e( 'Exit gallery', 'eac-components' ); ?></span>
		</div>
	</div> <!-- Fin div container eac-gallery__block-grid -->
<?php else :
	printf( '<div class="eac-gallery-empty">%s</div>', esc_html__( 'Gallery is empty.', 'eac-components' ) );
endif; ?>
<script>
	(function ($) {
		'use strict';

		const $targets = $('.eac-gallery__block-grid .acf-gallery__wrapper-img a[data-fancybox]');

		function initFancybox() {
			if (!$targets.length) return;

			$targets.fancybox({
				afterShow: function (instance, current) {
					const $content = current && current.$content ? current.$content : null;
					if ($content && $content.length) {
						$content.attr('aria-modal', 'true');
						$content.attr('role', 'dialog');
					}
					if (current && current.opts && current.opts.$orig) {
						current.opts.$orig.attr('aria-expanded', 'true');
					}
				},
				afterClose: function (instance, current) {
					$targets.attr('aria-expanded', 'false');
				}
			});
			return false;
		}

		$(window).on('load', function () {
			setTimeout(initFancybox, 1000);
		});
	})(jQuery);
</script>
<?php
