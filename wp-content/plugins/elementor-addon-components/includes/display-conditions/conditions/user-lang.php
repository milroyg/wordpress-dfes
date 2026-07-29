<?php
/**
 * Class: Date_Compare
 *
 * Description:
 *
 * @since 2.1.7
 */

namespace EACCustomWidgets\Includes\DisplayConditions\Conditions;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class User_Lang extends Condition_Base {
	/**
	 * $code_lang
	 *
	 * @var string
	 */
	private $code_lang = 'en';

	/**
	 * $supported_languages
	 *
	 * @var array
	 */
	private $supported_languages = array( 'en', 'fr', 'es', 'it', 'hi' );

	public function get_target_control(): array {
		return array(
			'label'          => esc_html__( 'List of languages', 'eac-components' ),
			'type'           => 'eac-select2',
			'options'        => $this->get_all_languages(),
			'multiple'       => true,
			'render_type'    => 'none',
			'condition'      => array(
				'element_condition_key' => 'user_lang',
			),
		);
	}

	public function get_called_classname(): string {
		return get_called_class();
	}

	public function check( $settings, $value, $operateur = '', $tz = '' ): bool {
		if ( ! is_array( $value ) ) {
			return true;
		}

		switch ( $operateur ) {
			case 'in':
				$etat = in_array( $this->code_lang, $value, true ) ? false : true;
				break;
			case 'not_in':
				$etat = ! in_array( $this->code_lang, $value, true ) ? false : true;
				break;
		}

		return $etat;
	}

	private function get_all_languages(): array {
		$languages = array();
		$langue    = $this->code_lang;

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$locale = determine_locale(); // ex: 'fr_FR' ou 'fr'
			$langue = ( strpos( $locale, '_' ) !== false ) ? substr( $locale, 0, strpos( $locale, '_' ) ) : $locale;
		} elseif ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() && isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$langue = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );
		}

		if ( in_array( $langue, $this->supported_languages, true ) ) {
			$this->code_lang = $langue;
		}

		$languages = $this->get_langs( $this->code_lang );
		uasort( $languages, function ( $a, $b ) {
			$na = mb_strtolower( $this->remove_accents( $a ) );
			$nb = mb_strtolower( $this->remove_accents( $b ) );
			return $na <=> $nb;
		} );

		return $languages;
	}

	private function get_langs( string $client_lang ): array {
		$languages = include __DIR__ . '/../languages.php';
		$langs     = array();

		foreach ( $languages as $language => $properties ) {
			$val = $properties[ $client_lang ];
			$langs[ esc_attr( strtolower( $language ) ) ] = esc_html( $val );
		}
		return $langs;
	}

	private function remove_accents( string $str ): string {
		if ( function_exists( 'transliterator_transliterate' ) ) {
			return transliterator_transliterate( 'Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove', $str );
		}

		$chars = array(
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'Å' => 'A',
			'Æ' => 'AE',
			'Ç' => 'C',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ð' => 'D',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ø' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ý' => 'Y',
			'Þ' => 'Th',
			'ß' => 'ss',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'æ' => 'ae',
			'ç' => 'c',
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'd',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'o',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ü' => 'u',
			'ý' => 'y',
			'þ' => 'th',
			'ÿ' => 'y',
		);

		return strtr( $str, $chars );
	}
}
