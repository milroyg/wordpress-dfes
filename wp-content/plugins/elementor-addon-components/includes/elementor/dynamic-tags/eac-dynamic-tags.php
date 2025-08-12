<?php
/**
 * Class: Eac_Dynamic_Tags
 *
 * Description: Enregistre les Balises Dynamiques (Dynamic Tags)
 * Met à disposition un ensemble de méthodes pour valoriser les options des listes de Tag
 * Ref: https://gist.github.com/iqbalrony/7ee129379965082fb6c62cf5db372752
 *
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Dynamic_Tags {

	const TAG_NAMESPACE  = __NAMESPACE__ . '\\tags\\';

	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'Url_Audio',
		'Url_Post',
		'Url_Cpt',
		'Url_Page',
		'Url_Chart',
		'Url_All',
		'Featured_Image_Url',
		'Author_Website_Url',
		'Url_External_Image',
		'Post_By_User',
		'Post_Custom_Field_Keys',
		'Post_Custom_Field_Values',
		'Post_Elementor_Tmpl',
		'Post_Title',
		'Post_Excerpt',
		'Post_Gallery',
		'Featured_Image',
		'User_Info',
		'Page_Title',
		'Site_Email',
		'Site_URL',
		'Site_Server',
		'Site_Title',
		'Site_Tagline',
		'Site_Logo',
		'Site_Stats',
		'Author_Info',
		'Author_Name',
		'Author_Picture',
		'Author_Social_Media',
		'Featured_Image_Data',
		'User_Picture',
		'Cookies_Tag',
		'Shortcode_Tag',
		'Lightbox_Tag',
	);

	/** Constructeur de la class */
	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );
	}

	/** Enregistre les groupes et les balises dynamiques (Dynamic Tags) */
	public function register_tags( $dynamic_tags ) {
		// Enregistre les nouveaux groupes avant d'enregistrer les Tags
		$dynamic_tags->register_group( 'eac-action', array( 'title' => esc_html__( 'EAC Actions', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-author-groupe', array( 'title' => esc_html__( 'EAC Auteur', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-post', array( 'title' => esc_html__( 'EAC Article', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-site-groupe', array( 'title' => esc_html__( 'EAC Site', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-url', array( 'title' => esc_html__( 'EAC URLs', 'eac-components' ) ) );

		foreach ( $this->tags_list as $class_name ) {
			$full_class_name = self::TAG_NAMESPACE . $class_name;
			$dynamic_tags->register( new $full_class_name() );
		}
	}
}
