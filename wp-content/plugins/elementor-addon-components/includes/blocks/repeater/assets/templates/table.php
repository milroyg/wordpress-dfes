<?php
/**
 * EAC Repeater Block (fonctions) — inline-localize version
 * Template: table.php — rendu pour displayType = 'table'
 *
 * @since 2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';

$unid       = uniqid();
$wrapper    = sprintf( 'eac-repeater__block-table acf-repeater__container-%1$s %2$s %3$s', $unid, $container_width, $class_name );
$wrapper_id = 'acf-repeater__container-' . $unid;

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_style_parts[] = 'background-color:' . esc_attr( $block_bg );
$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

$body_style_parts = array();
$body_style_parts[] = 'background-color:' . esc_attr( $item_bg );
$body_attr = 'style="' . implode( '; ', $body_style_parts ) . ';"';

$td_style_parts = array();
$vrt_text = ( 'start' === $align_vrt_text ) ? 'top' : ( 'end' === $align_vrt_text ? 'bottom' : 'middle' );
$color = ! empty( $color_text ) ? esc_attr( $color_text ) : '#000000';
$td_style_parts[] = 'color:' . esc_attr( $color );
$td_style_parts[] = 'font-size:' . esc_attr( $font_size );
$td_style_parts[] = 'text-align:' . esc_attr( $align_hrz_text );
$td_style_parts[] = 'vertical-align:' . esc_attr( $vrt_text );
$td_style_parts[] = 'border:' . esc_attr( $item_border );
$td_attr = 'style="' . implode( '; ', $td_style_parts ) . ';"';
?>
<div class="<?php echo esc_attr( trim( $wrapper ) ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( have_rows( $repeater, $postid ) ) {
		/** Extraction du thead */
		$repeater_field    = get_field_object( $repeater, $postid );
		$repeater_subfield = is_array( $repeater_field['sub_fields'] ) ? $repeater_field['sub_fields'] : array();

		// Normaliser et filtrer les sous-champs
		$filtered = array();
		foreach ( $repeater_subfield as $sub_field ) {
			if ( ! is_array( $sub_field ) ) {
				continue;
			}

			$sub_type = isset( $sub_field['type'] ) ? $sub_field['type'] : '';
			if ( ! eac_is_supported_field_type( $sub_type ) ) {
				continue;
			}

			if ( is_array( $sub_fields ) && ! empty( $sub_fields ) && ! in_array( $sub_field['key'], $sub_fields, true ) ) {
				continue;
			}

			// Nettoyage des valeurs de label/name
			$name  = isset( $sub_field['name'] ) ? sanitize_key( $sub_field['name'] ) : '';
			$label = isset( $sub_field['label'] ) && '' !== $sub_field['label'] ? wp_strip_all_tags( $sub_field['label'] ) : $name;

			// Sauter si pas de nom utile
			if ( empty( $label ) ) {
				continue;
			}

			$filtered[] = array(
				'label' => $label,
				'type'  => $sub_type,
			);
		}

		// Séparer les images et les autres (préserve l'ordre d'origine)
		$image_headers = array();
		$other_headers = array();
		foreach ( $filtered as $h ) {
			if ( 'image' === $h['type'] ) {
				$image_headers[] = $h;
			} else {
				$other_headers[] = $h;
			}
		}

		// Concaténation : images d'abord
		$headers = array_values( array_merge( $image_headers, $other_headers ) );
		?>
		<table class='widefat' cellspacing='0'>
			<thead style="background-color:<?php echo esc_attr( $color_title_bg_table ); ?>">
				<tr>
					<?php foreach ( $headers as $label ) { ?>
						<th style="color:<?php echo esc_attr( $color_title_table ); ?>"><?php echo esc_html( $label['label'] ); ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody <?php echo $body_attr; // phpcs:ignore ?>>
				<?php while ( have_rows( $repeater, $postid ) ) { ?>
					<tr>
						<?php the_row();
						foreach ( $sub_fields as $field_key ) {
							$sub_field = get_sub_field_object( $field_key );
							if ( ! $sub_field ) {
								continue;
							}
							if ( is_array( $sub_fields ) && ! empty( $sub_fields ) && ! in_array( $sub_field['key'], $sub_fields, true ) ) {
								continue;
							}
							if ( ! eac_is_supported_field_type( $sub_field['type'] ) ) {
								continue;
							}

							$field_type  = $sub_field['type'];
							$field_value = eac_normalize_field_by_type( $sub_field );
							$field_label = $sub_field['label'] ?? $field_key;
							$field_name  = $sub_field['_name'] ?? $field_key;

							if ( 'image' === $field_type && ! empty( $field_value ) ) {
								$attachment = eac_get_attachment_data( $field_value, $image_size );
								if ( ! empty( $attachment ) ) { ?>
									<td class='image-cell' <?php echo $td_attr; // phpcs:ignore ?>>
										<img class="acf-repeater__img" src="<?php echo esc_url( $attachment['src'] ); ?>" srcset="<?php echo esc_attr( $attachment['srcset'] ); ?>" sizes="<?php echo esc_attr( $attachment['srcsize'] ); ?>" width="<?php echo esc_attr( $attachment['width'] ); ?>" height="<?php echo esc_attr( $attachment['height'] ); ?>" alt='' />
									</td>
								<?php } ?>
							<?php } elseif ( 'textarea' === $field_type && ! empty( $field_value ) ) { ?>
								<td <?php echo $td_attr; // phpcs:ignore ?>>
									<div class="acf-repeater__textarea <?php echo esc_attr( $field_name ); ?>"><?php echo nl2br( esc_html( $field_value ) ); ?></div>
								</td>
							<?php } elseif ( 'select' === $field_type && ! empty( $field_value ) ) { ?>
								<td <?php echo $td_attr; // phpcs:ignore ?>>
									<div class="acf-repeater__select <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>
								</td>
							<?php } elseif ( in_array( $field_type, array( 'url', 'link', 'page_link' ), true ) && ! empty( $field_value ) ) {
								if ( 'link' === $field_type ) {
									$field_label = 'array' === $sub_field['return_format'] && isset( $sub_field['value']['title'] ) ? $sub_field['value']['title'] : $field_label;
								}
								$aria_label = sprintf( '%1$s - %2$s', esc_html__( 'Open link', 'eac-components' ), esc_html( $field_label ) );
								$rel = $nofollow_link ? 'rel=nofollow' : ''; ?>
								<td <?php echo $td_attr; // phpcs:ignore ?>>
									<div class="acf-repeater__url <?php echo esc_attr( $field_name ); ?>">
										<a class='eac-accessible-link' href="<?php echo esc_url( $field_value ); ?>" <?php echo esc_attr( $rel ); ?> aria-label="<?php echo esc_attr( $aria_label ); ?>"><?php echo esc_html( $field_label ); ?></a>
									</div>
								</td>
							<?php } elseif ( 'email' === $field_type && ! empty( $field_value ) ) { ?>
								<td <?php echo $td_attr; // phpcs:ignore ?>>
									<div class="acf-repeater__email <?php echo esc_attr( $field_name ); ?>">
										<a class='eac-accessible-link obfuscated-link' href='#' data-link="<?php echo esc_attr( $field_value ); ?>" rel="nofollow" aria-label="<?php echo esc_attr( $field_label ); ?>"><?php echo esc_html( $field_label ); ?></a>
									</div>
								</td>
							<?php } elseif ( 'file' === $field_type && ! empty( $field_value ) ) {
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
								<td <?php echo $td_attr; // phpcs:ignore ?>>
									<div class="acf-repeater__file <?php echo esc_attr( $field_name ); ?>">
										<a <?php printf( '%s', eac_array_to_html_attrs( $args ) ); // phpcs:ignore ?>>
											<img src="<?php echo esc_url( $default_img ); ?>" width='48' height='64' style='inline-size:auto;' alt='' />
										</a>
									</div>
								</td>
							<?php } else { ?>
								<td <?php echo $td_attr; // phpcs:ignore ?>>
									<div class="acf-repeater__<?php echo esc_attr( $field_type ); ?> <?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_value ); ?></div>
								</td>
							<?php } ?>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } ?>
</div>
<script>
	(function ($) {
		'use strict';

		const $targets = $('.eac-repeater__block-table .acf-repeater__file a[data-fancybox]');

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
