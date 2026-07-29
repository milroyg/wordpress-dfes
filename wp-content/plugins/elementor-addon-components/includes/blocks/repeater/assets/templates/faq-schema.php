<?php
/**
 * EAC Repeater Block (fonctions) — inline-localize version
 * Template: faq.php — génération du LD+JSON FAQPage
 *
 * @since 2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * eac_repeater_render_faq_schema
 * Génère le script LD+JSON pour le schema de la FAQ
 *
 * @param array $schemas
 *
 * @return void
 */
if ( ! function_exists( 'eac_repeater_render_faq_schema' ) ) {
	function eac_repeater_render_faq_schema( array $schemas ): void {
		$faq_schema = array();

		if ( ! empty( $schemas ) ) {
			foreach ( $schemas as $index => $schema ) {
				$faq_schema[] = array(
					'@type' => 'Question',
					'name' => (string) reset( $schema ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text' => (string) array_values( $schema )[1],
					),
				);
			}

			// Convertir le tableau en JSON-LD
			$json_ld = wp_json_encode( array(
				'@context' => 'https://schema.org',
				'@type' => 'FAQPage',
				'mainEntity' => $faq_schema,
			), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

			// Afficher le JSON-LD
			echo '<script type="application/ld+json">' . $json_ld . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
