<?php
/**
 * EAC Repeater Block (fonctions) — inline-localize version
 * Template: grid.php — rendu pour displayType = 'grid'
 *
 * @since 2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';

/* Préparer wrappers et styles */
$unid          = uniqid();
$wrapper_space = sprintf( 'eac-repeater__block-grid acf-repeater__container-%1$s %2$s %3$s %4$s %5$s', $unid, $container_width, $item_style, $class_name, $display_type );
$wrapper       = preg_replace( '/\s+/', ' ', $wrapper_space );
$wrapper_id    = 'acf-repeater__container-' . $unid;

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'gap:' . esc_attr( $gap_size );
$wrapper_style_parts[] = 'padding: calc(' . esc_attr( $gap_size ) . ' / 2)';
$wrapper_style_parts[] = 'background-color:' . esc_attr( $block_bg );
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

$article_style_parts = array();
if ( '' === $item_style ) { // Les styles s'appliquent si aucun style personnaliés n'est choisi.
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
?>
<div class="<?php echo esc_attr( trim( $wrapper ) . ( $wrapper_classes ? ' ' . $wrapper_classes : '' ) ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( have_rows( $repeater, $postid ) ) : ?>
		<?php while ( have_rows( $repeater, $postid ) ) :
			the_row(); ?>
			<article class='acf-repeater__wrapper' <?php echo $article_attr; // phpcs:ignore ?>>
				<?php
				$has_repeater_content = false;

				foreach ( $sub_fields as $field_key ) :
					$sub_field = get_sub_field_object( $field_key );
					if ( ! $sub_field ) {
						continue;
					}

					if ( is_array( $sub_fields ) && ! empty( $sub_fields ) && ! in_array( $sub_field['key'], $sub_fields, true ) ) {
						continue;
					}

					$field_type  = $sub_field['type'];
					$field_value = eac_normalize_field_by_type( $sub_field );
					$field_label = $sub_field['label'] ?? $field_key;
					$field_name  = $sub_field['_name'] ?? $field_key;

					if ( 'image' === $field_type && ! empty( $field_value ) ) :
						if ( $has_repeater_content ) :
							$has_repeater_content = false; ?>
							</div> <!-- .acf-repeater__wrapper-content -->
						<?php endif;
						$attachment = eac_get_attachment_data( $field_value, $image_size );
						if ( ! empty( $attachment ) ) : ?>
							<div class="acf-repeater__wrapper-img <?php echo esc_attr( $field_name ); ?>">
								<img class='acf-repeater__img' <?php echo $img_attr; // phpcs:ignore ?> src="<?php echo esc_url( $attachment['src'] ); ?>" srcset="<?php echo esc_attr( $attachment['srcset'] ); ?>" sizes="<?php echo esc_attr( $attachment['srcsize'] ); ?>" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" alt=''/>
							</div>
						<?php endif; ?>

					<?php elseif ( in_array( $field_type, array( 'text', 'number' ), true ) && ! empty( $field_value ) ) :
						if ( ! $has_repeater_content ) :
							$has_repeater_content = true; ?>
							<div class='acf-repeater__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
						<?php endif; ?>
						<div class="acf-repeater__<?php echo esc_attr( $field_type ); ?> <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>

					<?php elseif ( 'textarea' === $field_type && ! empty( $field_value ) ) :
						if ( ! $has_repeater_content ) :
							$has_repeater_content = true; ?>
							<div class='acf-repeater__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
						<?php endif; ?>
						<div class="acf-repeater__textarea <?php echo esc_attr( $field_name ); ?>"><?php echo nl2br( esc_html( $field_value ) ); ?></div>

					<?php elseif ( 'select' === $field_type && ! empty( $field_value ) ) :
						if ( ! $has_repeater_content ) :
							$has_repeater_content = true; ?>
							<div class='acf-repeater__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
						<?php endif; ?>
						<div class="acf-repeater__select <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>

					<?php elseif ( in_array( $field_type, array( 'url', 'link', 'page_link' ), true ) && ! empty( $field_value ) ) :
						if ( ! $has_repeater_content ) :
							$has_repeater_content = true; ?>
							<div class='acf-repeater__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
						<?php endif;
						if ( 'link' === $field_type ) {
							$field_label = 'array' === $sub_field['return_format'] && isset( $sub_field['value']['title'] ) ? $sub_field['value']['title'] : $field_label;
						}
						$classes = array( 'eac-accessible-link' );
						if ( true === $card_link ) {
							$classes[] = 'card-link';
						}
						if ( true === $button_link ) {
							$classes[] = 'button-wrapper';
						}
						$rel = $nofollow_link ? 'rel=nofollow' : '';
						$class_attr = implode( ' ', $classes );
						$aria_label = sprintf( '%1$s - %2$s', esc_html__( 'Open link', 'eac-components' ), esc_attr( $field_label ) ); ?>
						<div class="acf-repeater__url <?php echo esc_attr( $field_name ); ?>">
							<a class="<?php echo esc_attr( $class_attr ); ?>" href="<?php echo esc_url( $field_value ); ?>" <?php echo esc_attr( $rel ); ?> aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php echo esc_html( $field_label ); ?></a>
						</div>

					<?php elseif ( 'email' === $field_type && ! empty( $field_value ) ) :
						if ( ! $has_repeater_content ) :
							$has_repeater_content = true; ?>
							<div class='acf-repeater__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
						<?php endif; ?>
						<div class="acf-repeater__email <?php echo esc_attr( $field_name ); ?>">
							<a class='eac-accessible-link obfuscated-link' href='#' data-link="<?php echo esc_attr( $field_value ); ?>" rel='nofollow' aria-label="<?php echo esc_attr( $field_label ); ?>"><?php echo esc_html( $field_label ); ?></a>
						</div>

					<?php elseif ( 'date_picker' === $field_type && ! empty( $field_value ) ) :
						if ( ! $has_repeater_content ) :
							$has_repeater_content = true; ?>
							<div class='acf-repeater__wrapper-content' <?php echo $item_attr; // phpcs:ignore ?>>
						<?php endif; ?>
						<div class="acf-repeater__date <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>

					<?php elseif ( 'file' === $field_type && ! empty( $field_value ) ) :
						$file_info = pathinfo( $field_value )['basename'];
						$args = eac_is_a_valid_pdf( $field_value ) ? array(
							'class'         => 'eac-accessible-link',
							'href'          => 'javascript:;',
							'data-fancybox' => '',
							'role'          => 'button',
							'aria-expanded' => 'false',
							'aria-haspopup' => 'dialog',
							'aria-label'    => sprintf( '%1$s %2$s %3$s', esc_html__( 'Open file', 'eac-components' ), esc_attr( $file_info ), esc_attr__( 'in a modal box', 'eac-components' ) ),
							'data-options'  => wp_json_encode(
								array(
									'type'    => 'iframe',
									'caption' => esc_attr( $file_info ),
									'src'     => EAC_PLUGIN_URL . 'assets/js/pdfjs/viewer.html?file=' . esc_url( $field_value ) . '#page=1&pagemode=none&zoom=page-fit',
								)
							),
						) : array(
							'class'      => 'eac-accessible-link',
							'href'       => esc_url( $field_value ),
							'aria-label' => sprintf( '%1$s %2$s', esc_attr__( 'Open file', 'eac-components' ), esc_attr( $file_info ) ),
						);
						$default_img = includes_url( 'images/media/document.png' ); ?>
						<div class="acf-repeater__file <?php echo esc_attr( $field_name ); ?>" style="padding-block-start:10px; background-color:<?php echo esc_attr( $item_bg ); ?>">
							<a <?php printf( '%s', eac_array_to_html_attrs( $args ) ); // phpcs:ignore ?>>
								<img src="<?php echo esc_url( $default_img ); ?>" width='48' height='64' style='inline-size:auto;' alt='' />
							</a>
						</div>

					<?php endif;
				endforeach;

				if ( $has_repeater_content ) : ?>
					</div> <!-- .acf-repeater__wrapper-content -->
				<?php endif; ?>
			</article>
		<?php endwhile;
	endif; ?>
</div> <!-- Fin div container eac-repeater__block-grid-->
<style>
	/* Mobile (par défaut) */
	.list#<?php echo esc_attr( $wrapper_id ); ?> article {
		flex-direction: column;
		block-size: fit-content;
	}
	.list#<?php echo esc_attr( $wrapper_id ); ?> article .acf-repeater__img {
		block-size: auto;
	}

	/* Tablettes / desktop — appliquer les changements pour >= 576px */
	@media (min-width: 576px) {
		.list#<?php echo esc_attr( $wrapper_id ); ?> article {
			/* valeurs desktop — exemple : ligne et hauteur automatique */
			flex-direction: row;
			block-size: auto;
		}
		.list#<?php echo esc_attr( $wrapper_id ); ?> article .acf-repeater__img {
			/* valeurs desktop */
			block-size: 100%;
		}
	}
</style>
<script>
	(function ($) {
		'use strict';

		const $targets = $('.eac-repeater__block-grid .acf-repeater__file a[data-fancybox]');

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
		}

		$(window).on('load', function () {
			setTimeout(initFancybox, 1000);
		});
	})(jQuery);
</script>
<?php
