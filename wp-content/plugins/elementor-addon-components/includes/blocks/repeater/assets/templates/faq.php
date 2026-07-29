<?php
/**
 * EAC Repeater Block (fonctions) — inline-localize version
 * Template: faq.php — rendu pour displayType = 'faq'
 *
 * @since 2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once EAC_INCLUDES_PATH . 'blocks/lib-blocks.php';
require_once __DIR__ . '/faq-schema.php';

$unid          = uniqid();
$wrapper_space = sprintf( 'eac-repeater__block-faq acf-repeater__container-%1$s %2$s %3$s', $unid, $class_name, $item_style );
$wrapper       = preg_replace( '/\s+/', ' ', $wrapper_space );
$wrapper_id    = 'acf-repeater__container-' . $unid;
$question_tag  = eac_validate_html_tag( $heading_faq );
$faq_schema    = array();

$wrapper_style_parts = array();
$wrapper_style_parts[] = 'row-gap:' . esc_attr( $gap_size );
$wrapper_style_parts[] = 'padding: calc(' . esc_attr( $gap_size ) . ' / 2)';
$wrapper_style_parts[] = 'background-color:' . esc_attr( $block_bg );
$wrapper_style_parts[] = 'margin-block-start:' . esc_attr( $block_spacing['marginSup'] );
$wrapper_style_parts[] = 'margin-block-end:' . esc_attr( $block_spacing['marginInf'] );
$wrapper_style_parts[] = 'inline-size:' . esc_attr( $repeater_width['desktopWidth'] );
$wrapper_attr = 'style="' . implode( '; ', $wrapper_style_parts ) . ';"';

$article_style_parts = array();
if ( '' === $item_style ) { // Les styles s'appliquent si aucun style personnaliés n'est choisi.
	$article_style_parts[] = 'border:' . esc_attr( $item_border );
	$article_style_parts[] = 'border-radius:' . esc_attr( $item_radius['width'] );
}
$article_attr = 'style="' . implode( '; ', $article_style_parts ) . ';"';

$question_style_parts = array();
$question_style_parts[] = 'color:' . esc_attr( $color_title_faq );
$question_attr = 'style="' . implode( '; ', $question_style_parts ) . ';"';

$answer_style_parts = array();
$color_response = ! empty( $color_text ) ? esc_attr( $color_text ) : '#000000';
$answer_style_parts[] = 'background-color:' . esc_attr( $item_bg );
$answer_style_parts[] = 'color:' . esc_attr( $color_response );
$answer_style_parts[] = 'font-size:' . esc_attr( $font_size );
$answer_attr = 'style="' . implode( '; ', $answer_style_parts ) . ';"';
?>
<div class="<?php echo esc_attr( trim( $wrapper ) ); ?>" id="<?php echo esc_attr( $wrapper_id ); ?>" <?php echo $wrapper_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( have_rows( $repeater, $postid ) ) {
		while ( have_rows( $repeater, $postid ) ) {
			the_row();
			$the_row = get_row();
			$question_id = 'acf-repeater__faq-question-' . get_row_index();
			$answer_id = 'acf-repeater__faq-answer-' . get_row_index();
			?>
			<article class='acf-repeater_container-wrapper' <?php echo $article_attr; // phpcs:ignore ?>>
				<?php
				foreach ( (array) $the_row as $field_key => $any_value ) {
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

					$repeater_faq = array(
						'class'         => 'acf-repeater__faq-question',
						'style'         => 'background-color:' . esc_attr( $color_bg_faq ),
						'id'            => esc_attr( $question_id ),
						'role'          => 'button',
						'aria-expanded' => 'false',
						'aria-controls' => esc_attr( $answer_id ),
						'aria-label'    => sprintf( '%1$s - %2$s', esc_attr__( 'Show/Hide answer for', 'eac-components' ), esc_attr( $field_value ) ),
						'tabindex'      => '0',
					);

					if ( 'text' === $field_type && ! empty( $field_value ) ) { ?>
						<div <?php printf( '%s', eac_array_to_html_attrs( $repeater_faq ) ); // phpcs:ignore ?>>
							<?php printf( '<%1$s class="acf-repeater__faq-title" %2$s>%3$s</%1$s>', esc_attr( $question_tag ), $question_attr, esc_html( $field_value ) ); // phpcs:ignore ?>
							<span id='acf-repeater__faq-toggler' class='acf-repeater__faq-toggler eac-icon-svg' <?php echo $question_attr; // phpcs:ignore ?>><?php eac_print_svg_icon( 'arrow-tb' ); ?></span>
						</div>
						<?php
					} elseif ( 'textarea' === $field_type && ! empty( $field_value ) ) { ?>
						<div class='acf-repeater__faq-answer' id="<?php echo esc_attr( $answer_id ); ?>" role='region' aria-labelledby="<?php echo esc_attr( $question_id ); ?>" <?php echo $answer_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo nl2br( esc_html( $field_value ) ); ?>
						</div>
						<?php
					}
					// Enregistre Question/Rponse pour LD+JSON: indice commence à 1 pour get_row_index()
					$faq_schema[ get_row_index() - 1 ][ $field_name ] = $field_value;
				} // Fin boucle foreach ?>
			</article>
			<?php
		} // Fin boucle while
	} ?>
</div> <!-- Fin div container eac-repeater__block-faq -->
<style>
	/** Mode responsive éditeur */
	div.eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
		inline-size: <?php echo esc_attr( $repeater_width['mobileWidth'] ); ?> !important;
	}
	@media (min-width: 768px) {
		div.eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
			inline-size: <?php echo esc_attr( $repeater_width['tabletWidth'] ); ?> !important;
		}
	}
	@media (min-width: 992px) {
		div.eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
			inline-size: <?php echo esc_attr( $repeater_width['desktopWidth'] ); ?> !important;
		}
	}
			
	/** Mode responsive frontend */
	body:not(.block-editor-iframe__body) .eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
		inline-size: <?php echo esc_attr( $repeater_width['mobileWidth'] ); ?> !important;
	}
	@media (min-width: 576px) {
		body:not(.block-editor-iframe__body) .eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
			inline-size: <?php echo esc_attr( $repeater_width['mobileLandWidth'] ); ?> !important;
		}
	}
	@media (min-width: 768px) {
		body:not(.block-editor-iframe__body) .eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
			inline-size: <?php echo esc_attr( $repeater_width['tabletWidth'] ); ?> !important;
		}
	}
	@media (min-width: 992px) {
		body:not(.block-editor-iframe__body) .eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
			inline-size: <?php echo esc_attr( $repeater_width['tabletLandWidth'] ); ?> !important;
		}
	}
	@media (min-width: 1200px) {
		body:not(.block-editor-iframe__body) .eac-repeater__block-faq.<?php echo esc_attr( $wrapper_id ); ?> {
			inline-size: <?php echo esc_attr( $repeater_width['desktopWidth'] ); ?> !important;
		}
	}
</style>
<?php
// Construit le shema de la FAQ en dehors du wrapper pour éviter les problèmes de performance liés à l'encodage JSON dans une boucle
if ( ! empty( $faq_schema ) ) {
	eac_repeater_render_faq_schema( $faq_schema );
}
