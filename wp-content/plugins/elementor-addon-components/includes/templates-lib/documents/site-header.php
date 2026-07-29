<?php
/**
 * Class: Site_Header
 *
 * Description: Implémentation les propriétés de 'Library_Document'
 * Ajoute les controls des conditions d'affichage dans les paramétrages du document
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Documents;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\TemplateLibrary\Source_Local;

/**
 * Site_Header
 */
final class Site_Header extends Library_Document {

	/**
	 * @var string
	 */
	const TYPE = 'siteheader';

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @return array Document properties.
	 * Ajout de la propriété 'cpt' pour l'import du template
	 */
	public static function get_properties(): array {
		return array(
			'has_elements'              => true,
			'is_editable'               => true,
			'edit_capability'           => '',
			'show_in_finder'            => true,
			'show_on_admin_bar'         => true,
			'admin_tab_group'           => 'library',
			'show_in_library'           => true,
			'register_type'             => true,
			'support_kit'               => true,
			'support_wp_page_templates' => false,
			'cpt'                       => array( Source_Local::CPT ),
			'export_group'              => Library_Document::EXPORT_GROUP,
		);
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @return string Document name.
	 */
	public function get_name(): string {
		return self::TYPE;
	}

	/**
	 * @return string Document title.
	 */
	public static function get_title(): string {
		return esc_html__( 'Header', 'eac-components' );
	}

	/**
	 * @return string
	 */
	public function get_css_wrapper_selector(): string {
		return '.eac-site-header';
	}

	/**
	 * Override container attributes
	 */
	public function get_container_attributes(): array {
		$id = $this->get_main_id();

		$settings = $this->get_frontend_settings();

		$attributes = array(
			'data-elementor-type' => self::TYPE,
			'data-elementor-id'   => $id,
			'class'               => 'elementor elementor-' . $id . ' eac-site-header',
			'role'                => 'banner',
			'itemscope'           => 'itemscope',
			'itemtype'            => 'https://schema.org/WPHeader',
		);
		if ( ! empty( $settings ) ) {
			$attributes['data-elementor-settings'] = wp_json_encode( $settings );
		}

		return $attributes;
	}

	/**
	 * Override default wrapper.
	 * Check feature active
	 */
	public function print_elements_with_wrapper( $data = null ): void {
		if ( ! $data ) {
			$data = $this->get_elements_data();
		}

		do_action( 'before_print_eac_site_header', $data );
		?>
		<header <?php Utils::print_html_attributes( $this->get_container_attributes() ); ?>>
			<?php $this->print_elements( $data ); ?>
		</header>
		<?php
		do_action( 'after_print_eac_site_header', $data );
	}

	/**
	 * Register controls
	 */
	protected function register_controls(): void {
		$this->register_document_controls();

		$this->start_controls_section(
			'display_condition',
			array(
				'label' => esc_html__( 'Display conditions', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'meta_block_select',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'If multiple templates have the same display condition, the last updated one will be used.', 'eac-components' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'show_on',
			array(
				'label'       => esc_html__( 'Show on', 'eac-components' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'none',
				'options'     => array(
					'none'     => esc_html__( 'None', 'eac-components' ),
					'global'   => esc_html__( 'Entire site', 'eac-components' ),
					'blog'     => esc_html__( 'Blog page', 'eac-components' ),
					'front'    => esc_html__( 'Front page', 'eac-components' ),
					'archive'  => esc_html__( 'Archive pages', 'eac-components' ),
					'singular' => esc_html__( 'Singular pages', 'eac-components' ),
					'err404'   => esc_html__( 'Error 404 page', 'eac-components' ),
					'search'   => esc_html__( 'Search result page', 'eac-components' ),
					'privacy'  => esc_html__( 'Privacy policy page', 'eac-components' ),
					'wc_shop'  => esc_html__( 'WooCommerce shop page', 'eac-components' ),
					'custom'   => esc_html__( 'Custom', 'eac-components' ),
				),
			)
		);

		$this->add_control(
			'singular_pages',
			array(
				'label'       => esc_html__( 'Select singular type(s)', 'eac-components' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_singular_pages_options(),
				'condition'   => array(
					'show_on' => array( 'singular', 'custom' ),
				),
			)
		);

		$this->add_control(
			'archive_pages',
			array(
				'label'       => esc_html__( 'Select archive type(s)', 'eac-components' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => $this->get_archive_pages_options(),
				'condition'   => array(
					'show_on' => array( 'archive', 'custom' ),
				),
			)
		);

		$this->end_controls_section();

		/** @since 2.3.7 */
		$feature_css = Eac_Load_Config::is_feature_active( 'custom-css' ) && ! Eac_Load_Config::is_feature_active( 'editor-role' );
		$design_css  = Eac_Load_Config::is_feature_active( 'custom-css' ) && Eac_Load_Config::is_feature_active( 'editor-role' ) && \Elementor\Plugin::$instance->role_manager->user_can( 'design' );
		if ( $feature_css || $design_css ) {
			$this->start_controls_section(
				'header_css',
				array(
					'label' => esc_html__( 'Custom CSS', 'eac-components' ),
					'tab'   => Controls_Manager::TAB_ADVANCED,
				)
			);

				$this->add_control(
					'custom_css',
					array(
						'type'        => Controls_Manager::CODE,
						'label'       => esc_html__( 'Add your own CSS', 'eac-components' ),
						'language'    => 'css',
						'render_type' => 'ui',
						'separator'   => 'none',
					)
				);

				$this->add_control(
					'header_usage',
					array(
						'type' => Controls_Manager::RAW_HTML,
						'raw' => sprintf(
							/* translators: 1: Link opening tag, 2: Content tag, 3: Link closing tag. */
							esc_html__( 'Customize content with %1$syour CSS%3$s and use %2$sthe keyword "selector"%3$s to target specific elements', 'eac-components' ),
							'<a href="https://elementor-addon-components.com/elementor-custom-css/" target="_blank" rel="noopener noreferrer">',
							'<a href="https://elementor-addon-components.com/elementor-custom-css/#use-the-selector-keyword-to-target-an-element" target="_blank" rel="noopener noreferrer">',
							'</a>',
						),
						'content_classes' => 'elementor-descriptor',
					)
				);

			$this->end_controls_section();
		}
	}

	/**
	 * get_singular_pages_options
	 *
	 * @return array
	 */
	private function get_singular_pages_options(): array {
		global $wp_post_types;

		$options = array(
			'post'       => esc_html__( 'Post', 'eac-components' ),
			'page'       => esc_html__( 'Page', 'eac-components' ),
			'attachment' => esc_html__( 'Attachment', 'eac-components' ),
		);

		foreach ( $wp_post_types as $type => $object ) {
			if ( $object->public && ! $object->_builtin && Source_Local::CPT !== $type ) {
				$options[ esc_attr( $type ) ] = esc_html( $object->labels->singular_name );
			}
		}
		asort( $options, SORT_NATURAL | SORT_FLAG_CASE );
		return $options;
	}

	/**
	 * get_archive_pages_options
	 *
	 * @return array
	 */
	private function get_archive_pages_options(): array {
		global $wp_taxonomies, $wp_post_types;

		$options = array(
			'author'   => esc_html__( 'Author', 'eac-components' ),
			'date'     => esc_html__( 'Date', 'eac-components' ),
			'post_tag' => esc_html__( 'Tag', 'eac-components' ),
			'category' => esc_html__( 'Category', 'eac-components' ),
		);

		foreach ( $wp_taxonomies as $type => $object ) {
			if ( $object->public && ! $object->_builtin && 'product_shipping_class' !== $type ) {
				$options[ esc_attr( $type ) ] = esc_html( $object->labels->name );
			}
		}

		foreach ( $wp_post_types as $type => $object ) {
			if ( $object->public && ! $object->_builtin && Source_Local::CPT !== $type ) {
				$options[ esc_attr( $type ) ] = esc_html( $object->labels->name );
			}
		}
		asort( $options, SORT_NATURAL | SORT_FLAG_CASE );
		return $options;
	}
}
