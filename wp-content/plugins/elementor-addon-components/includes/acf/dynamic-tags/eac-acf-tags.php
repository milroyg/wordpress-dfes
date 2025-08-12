<?php
/**
 * Class: Eac_Acf_Tags
 *
 * Description: Module de base pour construire les balises dynamques ACF
 * aux balises dynamiques ACF
 *
 * @since 1.7.5
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Eac_Acf_Tags {

	const TAG_NAMESPACE = __NAMESPACE__ . '\\tags\\';

	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'Eac_Acf_Color',
		'Eac_Acf_Date',
		'Eac_Acf_File',
		'Eac_Acf_Gallery',
		'Eac_Acf_Image',
		'Eac_Acf_Number',
		'Eac_Acf_Relational',
		'Eac_Acf_Text',
		'Eac_Acf_Url',
		'Eac_Acf_Group_Color',
		'Eac_Acf_Group_Date',
		'Eac_Acf_Group_File',
		'Eac_Acf_Group_Gallery',
		'Eac_Acf_Group_Image',
		'Eac_Acf_Group_Number',
		'Eac_Acf_Group_Text',
		'Eac_Acf_Group_Url',
		'Eac_Post_Acf_Keys',
		'Eac_Post_Acf_Values',
	);

	/**
	 * Constructeur de la class
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );
	}

	/** Enregistre le groupe et les balises dynamiques des champs ACF */
	public function register_tags( $dynamic_tags ) {
		/** Chargement de la Lib de gestion des balises ACF */
		if ( ! class_exists( \EACCustomWidgets\Includes\Acf\DynamicTags\Eac_Acf_Lib::class, false ) ) {
			new \EACCustomWidgets\Includes\Acf\DynamicTags\Eac_Acf_Lib();
		}

		// Enregistre le nouveau groupe avant d'enregistrer les Tags
		$dynamic_tags->register_group( 'eac-acf-groupe', array( 'title' => esc_html__( 'EAC ACF', 'eac-components' ) ) );

		foreach ( $this->tags_list as $class_name ) {
			$full_class_name = self::TAG_NAMESPACE . $class_name;
			$dynamic_tags->register( new $full_class_name() );
		}
	}
}
