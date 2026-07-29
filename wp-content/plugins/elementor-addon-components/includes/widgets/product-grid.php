<?php
/**
 * Class: Product_Grid_Widget
 * Title: Grille de produits
 * Slug: eac-addon-product-grid
 *
 * Description: Affiche les produits créés avec woocommerce
 * dans différents modes, masonry, grille ou slider avec différents filtres
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;
use EACCustomWidgets\Core\Utils\Eac_Helper_Util;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;
use Elementor\Utils;

class Product_Grid_Widget extends Widget_Base {
	/** Les Traits */
	use \EACCustomWidgets\Includes\Traits\Slider_Trait;
	use \EACCustomWidgets\Includes\Traits\Badges_Trait;
	use \EACCustomWidgets\Includes\Traits\Button_Add_To_Cart_Trait;
	use \EACCustomWidgets\Includes\Traits\Button_Read_More_Trait;
	use \EACCustomWidgets\Includes\Traits\Icon_Svg_Trait;

	/** Constructeur */
	public function __construct( array $data = array(), ?array $args = null ) {
		parent::__construct( $data, $args );

		$this->helper_util = new Eac_Helper_Util();

		// Supprime les callbacks du filtre de la liste 'orderby'
		remove_all_filters( 'eac/tools/post_orderby' );

		// Filtre la liste 'orderby'
		add_filter(
			'eac/tools/post_orderby',
			function ( $exclude_orderby ) {
				unset( $exclude_orderby['comment_count'] );
				return $exclude_orderby;
			},
			10
		);
	}

	/**
	 * $helper_util
	 *
	 * @var null
	 */
	private $helper_util = null;

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'woo-product-grid';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return string widget name.
	 */
	public function get_name(): string {
		return Eac_Load_Config::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return string widget title.
	 */
	public function get_title(): string {
		return Eac_Load_Config::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon(): string {
		return Eac_Load_Config::get_widget_icon( $this->slug );
	}

	/**
	 * Affecte le composant à la catégorie définie dans plugin.php
	 *
	 * @access public
	 *
	 * @return array widget category.
	 */
	public function get_categories(): array {
		return Eac_Load_Config::get_widget_categories( $this->slug );
	}

	/**
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return array libraries list.
	 */
	public function get_script_depends(): array {
		return array( 'eac-post-grid' );
	}

	/**
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 *
	 * @access public
	 *
	 * @return array CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-product-grid' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords(): array {
		return Eac_Load_Config::get_widget_keywords( $this->slug );
	}

	/**
	 * Get help widget get_custom_help_url.
	 *
	 * @access public
	 *
	 * @return string URL help center
	 */
	public function get_custom_help_url(): string {
		return Eac_Load_Config::get_widget_help_url( $this->slug );
	}

	/**
	 * has_widget_inner_wrapper
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * is_dynamic_content
	 *
	 * @return bool
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls(): void {
		$catalog = \EACCustomWidgets\Includes\Woocommerce\Eac_Woo_Filters::instance()->is_catalog();
		$start   = is_rtl() ? 'right' : 'left';
		$end     = is_rtl() ? 'left' : 'right';
		// Récupère tous les breakpoints actifs
		$active_breakpoints     = Plugin::$instance->breakpoints->get_active_breakpoints();
		$has_active_breakpoints = Plugin::$instance->breakpoints->has_custom_breakpoints();
		// Add default values for all active breakpoints.
		$columns_device_args = array();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_instance ) {
			if ( Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_LAPTOP === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET_EXTRA === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '2' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '1' );
			}
		}

		$this->start_controls_section(
			'al_post_filter',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/**
			 * Champ caché pour déterminer si la page est un catalogue
			 * La valeur n'est pas enregistrée dans la BDD
			 */
			$this->add_control(
				'shop_is_a_catalog',
				array(
					'label'        => 'Catalogue hidden',
					'type'         => Controls_Manager::HIDDEN,
					'default'      => $catalog,
					'save_default' => false,
				)
			);

			$this->add_control(
				'al_post_filter_heading',
				array(
					'label'     => esc_html__( 'Query filter', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'al_article_type',
				array(
					'label'       => esc_html__( 'Product type', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => true,
					'options'     => Eac_Tools_Util::get_product_post_types(),
					'default'     => 'product',
				)
			);

			$this->add_control(
				'al_article_taxonomy',
				array(
					'label'       => esc_html__( 'Taxonomy filter', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'options'     => Eac_Tools_Util::get_product_taxonomies(),
					'default'     => array( 'product_cat' ),
					'multiple'    => true,
				)
			);

			$this->add_control(
				'al_article_term',
				array(
					'label'       => esc_html__( 'Tag filter', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'options'     => Eac_Tools_Util::get_product_terms(),
					'multiple'    => true,
				)
			);

			/**$this->add_control(
				'al_article_term',
				array(
					'label'          => esc_html__( 'Tag filter', 'eac-components' ),
					'type'           => 'eac-select2',
					'select2Options' => array(
						'object_type' => 'product',
						'query_type'  => 'term',
						//'query_taxo'  => array( 'product_cat', 'product_tag' ),
					),
					'multiple'       => true,
				)
			);*/

			$this->add_control(
				'al_content_user',
				array(
					'label'       => esc_html__( 'Author filter', 'eac-components' ),
					'description' => esc_html__( "Dynamic Tags 'Post/Authors'", 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
						),
					),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'al_content_user_alert',
				array(
					'type'       => Controls_Manager::ALERT,
					'alert_type' => 'warning',
					'heading'    => esc_html__( 'Security Alert', 'eac-components' ),
					'content'    => esc_html__( 'It is not recommended to expose the name (nickname) of your users, as this can facilitate targeting by malicious individuals, increasing the risk of intrusion attempts.', 'eac-components' ),
					'condition'  => array( 'al_content_user!' => '' ),
				)
			);

			$this->add_control(
				'al_display_content_args',
				array(
					'label'        => esc_html__( 'Display query content', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'al_post_settings_heading',
				array(
					'label'     => esc_html__( 'Product', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_layout_type!' => 'slider' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_article_nombre',
				array(
					'label'       => esc_html__( 'Product count', 'eac-components' ),
					'description' => esc_html__( '0 = All', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 10,
					'condition'   => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_article_orderby',
				array(
					'label'     => esc_html__( 'Sorted by', 'eac-components' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => Eac_Tools_Util::get_post_orderby(),
					'default'   => 'title',
				)
			);

			$this->add_control(
				'al_article_order',
				array(
					'label'   => esc_html__( 'Display', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'asc'  => esc_html__( 'Top-bottom', 'eac-components' ),
						'desc' => esc_html__( 'Bottom-up', 'eac-components' ),
					),
					'default' => 'asc',
				)
			);

			$this->add_control(
				'al_article_id',
				array(
					'label'        => esc_html__( 'Exclude IDs', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_article_exclude',
				array(
					'label'       => esc_html( 'IDs' ),
					'description' => esc_html__( 'ID separated by comma without space', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '',
					'condition'   => array(
						'al_article_id' => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_content_pagging_heading',
				array(
					'label'     => esc_html__( 'Paging', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'   => array(
						'al_article_nombre!' => array( -1, 0, '' ),
						'al_layout_type!'    => 'slider',
					),
				)
			);

			$this->add_control(
				'al_content_pagging_display',
				array(
					'label'        => esc_html__( 'Paging AJAX', 'eac-components' ),
					'description'  => esc_html__( 'Without page reload', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'   => array(
						'al_article_nombre!'      => array( -1, 0, '' ),
						'al_layout_type!'         => 'slider',
						'al_content_nav_display!' => 'yes',
					),
				)
			);

			$this->add_control(
				'al_content_nav_display',
				array(
					'label'        => esc_html__( 'Paging', 'eac-components' ),
					'description'  => esc_html__( 'With page reload', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'   => array(
						'al_article_nombre!'          => array( -1, 0, '' ),
						'al_layout_type!'             => 'slider',
						'al_content_pagging_display!' => 'yes',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_layout_settings',
			array(
				'label' => esc_html__( 'Layout', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'al_layout_type',
				array(
					'label'   => esc_html__( 'Mode', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => array(
						'masonry'     => esc_html__( 'Masonry', 'eac-components' ),
						'equalHeight' => esc_html__( 'Grid', 'eac-components' ),
						'slider'      => esc_html( 'Slider' ),
					),
					'assets' => array(
						'styles' => array(
							array(
								'name' => 'swiper-bundle',
								'conditions' => array(
									'terms' => array(
										array(
											'name'     => 'al_layout_type',
											'operator' => '===',
											'value'    => 'slider',
										),
									),
								),
							),
							array(
								'name' => 'eac-swiper',
								'conditions' => array(
									'terms' => array(
										array(
											'name'     => 'al_layout_type',
											'operator' => '===',
											'value'    => 'slider',
										),
									),
								),
							),
						),
						'scripts' => array(
							array(
								'name' => 'swiper-bundle',
								'conditions' => array(
									'terms' => array(
										array(
											'name'     => 'al_layout_type',
											'operator' => '===',
											'value'    => 'slider',
										),
									),
								),
							),
							array(
								'name' => 'isotope',
								'conditions' => array(
									'terms' => array(
										array(
											'name'     => 'al_layout_type',
											'operator' => 'in',
											'value'    => array( 'equalHeight', 'masonry' ),
										),
									),
								),
							),
							array(
								'name' => 'fit-rows',
								'conditions' => array(
									'terms' => array(
										array(
											'name'     => 'al_layout_type',
											'operator' => 'in',
											'value'    => array( 'equalHeight', 'masonry' ),
										),
									),
								),
							),
							array(
								'name' => 'infinite-scroll',
								'conditions' => array(
									'terms' => array(
										array(
											'name'     => 'al_layout_type',
											'operator' => 'in',
											'value'    => array( 'equalHeight', 'masonry' ),
										),
									),
								),
							),
						),
					),
				)
			);

			$this->add_responsive_control(
				'al_columns',
				array(
					'label'        => esc_html__( 'Columns count', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => '3',
					'device_args'  => $columns_device_args,
					'options'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class' => 'responsive%s-',
					'render_type'  => 'template',
					'condition'    => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_enable_animation',
				array(
					'label'     => esc_html__( 'Animation', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'no',
					'toggle'    => false,
					'condition' => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_layout_image',
				array(
					'label'     => esc_html__( 'Image layout', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'al_image_dimension',
					'label'   => esc_html__( 'Size', 'eac-components' ),
					'default' => 'medium',
					'exclude' => array( 'custom' ),
				)
			);

			$this->add_control(
				'al_enable_image_lazy',
				array(
					'label'     => esc_html( 'Lazy load' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => '',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'al_enable_image_ratio',
				array(
					'label'        => esc_html__( 'Enable image ratio', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'      => 'yes',
					'toggle'       => false,
					'render_type'  => 'template',
					'prefix_class' => 'al-post__ratio-',
					'condition' => array( 'al_layout_type' => array( 'equalHeight', 'fitRows' ) ),
				)
			);

			$this->add_responsive_control(
				'al_image_ratio',
				array(
					'label'       => esc_html( 'Ratio' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => '1 / 1',
					'tablet_default' => '1 / 1',
					'mobile_default' => '9 / 16',
					'options'        => array(
						'1 / 1'  => esc_html__( 'Default', 'eac-components' ),
						'9 / 16' => esc_html( '9-16' ),
						'4 / 3'  => esc_html( '4-3' ),
						'3 / 2'  => esc_html( '3-2' ),
						'16 / 9' => esc_html( '16-9' ),
						'21 / 9' => esc_html( '21-9' ),
					),
					'selectors'   => array( '{{WRAPPER}} .al-posts__wrapper .al-post__image-loaded' => 'aspect-ratio:{{SIZE}};' ),
					'condition'   => array(
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => array( 'equalHeight', 'fitRows' ),
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'al_image_ratio_position_y',
				array(
					'label'      => esc_html__( 'Vertical position', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .al-posts__wrapper .al-post__image-loaded' => 'object-position: 50% {{SIZE}}%;' ),
					'condition'  => array(
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => array( 'equalHeight', 'fitRows' ),
					),
				)
			);

			$this->add_control(
				'al_layout_content',
				array(
					'label'     => esc_html__( 'Content layout', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_layout_side_by_side',
				array(
					'label'     => esc_html__( 'Side by side', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'al_layout_texte',
				array(
					'label'        => esc_html__( 'Right', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Image on the left Content on the right', 'eac-components' ),
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'render_type'  => 'template',
					'prefix_class' => 'layout-text__right-',
					'condition'    => array( 'al_layout_texte_left!' => 'yes' ),
				)
			);

			$this->add_control(
				'al_layout_texte_left',
				array(
					'label'        => esc_html__( 'Left', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Content on the left Image on the right', 'eac-components' ),
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'render_type'  => 'template',
					'prefix_class' => 'layout-text__left-',
					'condition'    => array( 'al_layout_texte!' => 'yes' ),
				)
			);

			$this->add_responsive_control(
				'al_image_width',
				array(
					'label'          => esc_html__( 'Image width (%)', 'eac-components' ),
					'type'           => Controls_Manager::SLIDER,
					'size_units'     => array( '%' ),
					'default'        => array(
						'unit' => '%',
						'size' => 100,
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
					),
					'range'          => array(
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'      => array(
						'{{WRAPPER}}.layout-text__right-yes .al-post__inner-wrapper .al-post__image-wrapper,
						{{WRAPPER}}.layout-text__left-yes .al-post__inner-wrapper .al-post__image-wrapper' => 'inline-size: {{SIZE}}%;',
					),
					'conditions'     => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_layout_texte',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_layout_texte_left',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'al_layout_content_alignment',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'al_content_text_align_v',
				array(
					'label'       => esc_html__( 'Vertical', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Top', 'eac-components' ),
							'icon'  => 'eicon-justify-start-v',
						),
						'center'        => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-justify-center-v',
						),
						'flex-end'      => array(
							'title' => esc_html__( 'Bottom', 'eac-components' ),
							'icon'  => 'eicon-justify-end-v',
						),
						'space-between' => array(
							'title' => esc_html__( 'Space between', 'eac-components' ),
							'icon'  => 'eicon-justify-space-between-v',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Space around', 'eac-components' ),
							'icon'  => 'eicon-justify-space-around-v',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Space evenly', 'eac-components' ),
							'icon'  => 'eicon-justify-space-evenly-v',
						),
					),
					'default'     => 'flex-start',
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}} .al-post__text-wrapper' => 'justify-content: {{VALUE}};',
					),
					'condition'   => array( 'al_layout_type' => array( 'equalHeight', 'fitRows', 'slider' ) ),
				)
			);

			$this->add_control(
				'al_content_text_align_h',
				array(
					'label'     => esc_html__( 'Horizontal', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center'     => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end'   => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'center',
					'selectors_dictionary' => array(
						'flex-start' => 'start',
						'center'     => 'center',
						'flex-end'   => 'end',
					),
					'selectors' => array(
						'{{WRAPPER}} .al-post__text-wrapper' => 'align-items: {{VALUE}};',
						'{{WRAPPER}} .buttons-wrapper' => 'justify-content:  {{VALUE}};',
						'{{WRAPPER}} .shop-product__excerpt-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/** Les controls du slider Trait */
		$this->start_controls_section(
			'al_slider_settings',
			array(
				'label'     => 'Slider',
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_layout_type' => 'slider' ),
			)
		);

			$this->register_slider_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_product_content',
			array(
				'label' => esc_html__( 'Content', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'al_filter_heading',
				array(
					'label'     => esc_html__( 'Filter', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'al_layout_type',
								'operator' => '!==',
								'value' => 'slider',
							),
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name'     => 'al_article_nombre',
										'operator' => 'in',
										'value'    => array( -1, 0, '' ),
									),
									array(
										'name'     => 'al_content_nav_display',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				'al_filter',
				array(
					'label'        => esc_html__( 'Filter', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'al_layout_type',
								'operator' => '!==',
								'value' => 'slider',
							),
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name'     => 'al_article_nombre',
										'operator' => 'in',
										'value'    => array( -1, 0, '' ),
									),
									array(
										'name'     => 'al_content_nav_display',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				'al_filter_align',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'left',
					'selectors_dictionary' => array(
						'left'  => 'start',
						'right' => 'end',
					),
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper, {{WRAPPER}} .al-filters__wrapper-select' => 'text-align: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '!==',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_filter',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'al_article_nombre',
										'operator' => 'in',
										'value'    => array( -1, 0, '' ),
									),
									array(
										'name'     => 'al_content_nav_display',
										'operator' => '!==',
										'value'    => 'yes',
									),
								),
							),
						),
					),
				)
			);

			$this->start_controls_tabs(
				'al_content_settings',
				array(
					'separator' => 'before',
				)
			);

				$this->start_controls_tab(
					'al_content_product',
					array(
						'label' => sprintf( '<span class="eicon eicon-products-archive" title="%s"></span>', esc_html__( 'Product', 'eac-components' ) ),
					)
				);

					$this->add_control(
						'al_product_heading',
						array(
							'label' => esc_html__( 'Product', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'al_title',
						array(
							'label'        => esc_html__( 'Title', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'al_title_tag',
						array(
							'label'   => esc_html__( 'Title tag', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'default' => 'h2',
							'options' => array(
								'h2'   => 'H2',
								'h3'   => 'H3',
								'h4'   => 'H4',
								'h5'   => 'H5',
								'h6'   => 'H6',
								'div'  => 'div',
								'p'    => 'p',
							),
							'condition' => array( 'al_title' => 'yes' ),
						)
					);

					$this->add_control(
						'al_excerpt',
						array(
							'label'        => esc_html__( 'Description', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'al_excerpt_length',
						array(
							'label'   => esc_html__( 'Number of words', 'eac-components' ),
							'type'    => Controls_Manager::NUMBER,
							'min'     => 0,
							'max'     => 100,
							'step'    => 5,
							'default' => 25,
							'condition' => array( 'al_excerpt' => 'yes' ),
						)
					);

					$this->add_control(
						'al_reviews',
						array(
							'label'        => esc_html__( 'Reviews', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_reviews_format',
						array(
							'label'     => esc_html__( 'Review format', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'options'   => array(
								'average_rating'    => esc_html__( 'Average notes', 'eac-components' ),  // Average rating
								'average_html'      => esc_html__( 'Average HTML', 'eac-components' ),
								'average_html_long' => esc_html__( 'Average HTML + Reviews', 'eac-components' ),
								'rating_count'      => esc_html__( 'Number of notes', 'eac-components' ),      // Rating count
								'review_count'      => esc_html__( 'Review count', 'eac-components' ),        // Review count
							),
							'default'   => 'average_rating',
							'condition' => array( 'al_reviews' => 'yes' ),
						)
					);

					$this->add_control(
						'al_prices',
						array(
							'label'        => esc_html__( 'Price', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'al_prices_format',
						array(
							'label'     => esc_html__( 'Price format', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'options'   => array(
								'regular' => esc_html__( 'Regular', 'eac-components' ),
								'promo'   => esc_html__( 'Sale', 'eac-components' ),
								'both'    => esc_html__( 'Both', 'eac-components' ),
								'dateto'  => esc_html__( 'Sale end date', 'eac-components' ),
							),
							'default'   => 'regular',
							'condition' => array(
								'al_prices' => 'yes',
							),
						)
					);

					$this->add_control(
						'al_stock',
						array(
							'label'        => esc_html__( 'Stock', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'shop_is_a_catalog' => false ),
						)
					);

					$this->add_control(
						'al_stock_format',
						array(
							'label'     => esc_html__( 'Stock format', 'eac-components' ),
							'type'      => Controls_Manager::CHOOSE,
							'options'   => array(
								'yes' => array(
									'title' => esc_html__( 'Display', 'eac-components' ),
									'icon'  => 'eicon-check',
								),
								'no'  => array(
									'title' => esc_html__( 'Hide', 'eac-components' ),
									'icon'  => 'eicon-ban',
								),
							),
							'default'   => 'yes',
							'toggle'    => false,
							'condition' => array(
								'al_stock'          => 'yes',
								'shop_is_a_catalog' => false,
							),
						)
					);

					$this->add_control(
						'al_quantity_sold',
						array(
							'label'        => esc_html__( 'Quantity sold', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'shop_is_a_catalog' => false ),
						)
					);

					$this->add_control(
						'al_quantity_sold_fallback',
						array(
							'label'       => esc_html__( 'Alternate text', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'ai'          => array( 'active' => false ),
							'description' => esc_html__( 'If quantity is zero', 'eac-components' ),
							'placeholder' => esc_html__( 'Be the first to buy this product', 'eac-components' ),
							'label_block' => true,
							'condition'   => array(
								'al_quantity_sold'  => 'yes',
								'shop_is_a_catalog' => false,
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_metas',
					array(
						'label' => '<span class="eicon eicon-product-meta" title="Metas"></span>',
					)
				);

					$this->add_control(
						'al_terms_heading',
						array(
							'label' => esc_html( 'Metas' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'al_term',
						array(
							'label'        => esc_html__( 'Tags', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_author',
						array(
							'label'        => esc_html__( 'Author', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_author_alert',
						array(
							'type'       => Controls_Manager::ALERT,
							'alert_type' => 'warning',
							'heading'    => esc_html__( 'Security Alert', 'eac-components' ),
							'content'    => esc_html__( 'It is not recommended to expose the name (nickname) of your users, as this can facilitate targeting by malicious individuals, increasing the risk of intrusion attempts.', 'eac-components' ),
							'condition'  => array( 'al_author' => 'yes' ),
						)
					);

					$this->add_control(
						'al_avatar',
						array(
							'label'        => esc_html__( 'Author avatar', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_date',
						array(
							'label'        => esc_html__( 'Date', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_badges',
					array(
						'label'     => sprintf( '<span class="eicon eicon-product-info" title="%s"></span>', esc_html__( 'Badges', 'eac-components' ) ),
						'condition' => array( 'shop_is_a_catalog' => false ),
					)
				);

					$this->add_control(
						'al_badges_heading',
						array(
							'label' => esc_html__( 'Badges', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'promo_activate',
						array(
							'label'        => esc_html__( 'Badge sale', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-sale-',
							'render_type'  => 'template',
							'condition'    => array( 'shop_is_a_catalog' => false ),
						)
					);

					$this->add_control(
						'stock_activate',
						array(
							'label'        => esc_html__( 'Badge stock', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-stock-',
							'render_type'  => 'template',
							'condition'    => array( 'shop_is_a_catalog' => false ),
						)
					);

					$this->add_control(
						'new_activate',
						array(
							'label'        => esc_html__( 'Badge new product', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-new-',
							'render_type'  => 'template',
							'condition'    => array( 'shop_is_a_catalog' => false ),
						)
					);

					$this->add_control(
						'cart_quantity_activate',
						array(
							'label'        => esc_html__( 'Badge quantity in cart', 'eac-components' ),
							'description'  => esc_html__( "Adds to the 'Add to cart' button a badge indicating the quantity of the product in the cart", 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-cart-quantity-',
							'render_type'  => 'template',
							'condition'    => array(
								'button_cart'       => 'yes',
								'shop_is_a_catalog' => false,
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_buttons',
					array(
						'label' => sprintf( '<span class="eicon eicon-button" title="%s"></span>', esc_html__( 'Buttons', 'eac-components' ) ),
					)
				);

					$this->add_control(
						'al_buttons_heading',
						array(
							'label' => esc_html__( 'Buttons', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'button_more',
						array(
							'label'        => esc_html__( 'View product', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'button_cart',
						array(
							'label'        => esc_html__( 'Add to cart', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
							'condition'    => array( 'shop_is_a_catalog' => false ),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_links',
					array(
						'label' => sprintf( '<span class="eicon eicon-link" title="%s"></span>', esc_html__( 'Link', 'eac-components' ) ),
					)
				);

					$this->add_control(
						'al_links_heading',
						array(
							'label' => esc_html__( 'Link', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'al_title_link',
						array(
							'label'        => esc_html__( 'Product page link on title', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'al_title' => 'yes' ),
						)
					);

					$this->add_control(
						'al_image_link',
						array(
							'label'        => esc_html__( 'Product page link on image', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'al_lightbox!' => 'yes' ),
						)
					);

					$this->add_control(
						'al_article_link',
						array(
							'label'        => esc_html__( 'Enable the link globally', 'eac-components' ),
							'description'  => esc_html__( 'The link wraps each item', 'eac-components' ),
							'type'      => Controls_Manager::CHOOSE,
							'options'   => array(
								'yes' => array(
									'title' => esc_html__( 'Yes', 'eac-components' ),
									'icon'  => 'eicon-check',
								),
								'no'  => array(
									'title' => esc_html__( 'No', 'eac-components' ),
									'icon'  => 'eicon-ban',
								),
							),
							'default'   => 'no',
							'toggle'    => false,
							'conditions' => array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'button_more',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'relation' => 'and',
										'terms'    => array(
											array(
												'name'     => 'al_title',
												'operator' => '===',
												'value'    => 'yes',
											),
											array(
												'name'     => 'al_title_link',
												'operator' => '===',
												'value'    => 'yes',
											),
										),
									),
									array(
										'name'     => 'al_image_link',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						)
					);

					$this->add_control(
						'al_lightbox',
						array(
							'label'        => esc_html__( 'Lightbox on image', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array(
								'al_image_link!'  => 'yes',
								'al_article_link!' => 'yes',
							),
						)
					);

					$this->add_control(
						'al_avatar_link',
						array(
							'label'        => esc_html__( 'Link on avatar', 'eac-components' ),
							'description'  => esc_html__( 'Author archives', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'Yes', 'eac-components' ),
							'label_off'    => esc_html__( 'No', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'al_avatar' => 'yes' ),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_pagination_settings',
			array(
				'label'     => esc_html__( 'Button Pagination', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_content_pagging_display' => 'yes' ),
			)
		);

			$this->add_control(
				'al_pagination_label',
				array(
					'label'       => esc_html__( 'Label', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => esc_html__( 'More products', 'eac-components' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_more_settings',
			array(
				'label'     => esc_html__( 'Button View product', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'button_more' => 'yes' ),
			)
		);

			// Trait du contenu du bouton read more
			$this->register_button_more_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_cart_settings',
			array(
				'label'     => esc_html__( 'Button Add to cart', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'button_cart'       => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			// Le trait du bouton 'Add to cart'
			$this->register_button_cart_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_promo_settings',
			array(
				'label'     => esc_html__( 'Badge sale', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'promo_activate'    => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			/** Trait badge Promotion */
			$this->register_sale_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_stock_settings',
			array(
				'label'     => esc_html__( 'Badge stock', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'stock_activate'    => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			/** Trait badge Stock */
			$this->register_stock_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_cart_quantity_settings',
			array(
				'label'     => esc_html__( 'Badge cart quantity', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'cart_quantity_activate' => 'yes',
					'button_cart'            => 'yes',
					'shop_is_a_catalog'      => 'no',
				),
			)
		);

			$order_start = is_rtl() ? 'start' : 'end';
			$order_end   = is_rtl() ? 'end' : 'start';
			$this->add_control(
				'cart_quantity_position',
				array(
					'label'        => esc_html__( 'Position', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'  => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-order-{$order_start}",
						),
						'right' => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-order-{$order_end}",
						),
					),
					'default'      => 'left',
					'toggle'       => false,
					'prefix_class' => 'badge-cart-quantity-pos-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_new_settings',
			array(
				'label'     => esc_html__( 'Badge new product', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'new_activate'      => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			/** Trait badge nouveau produit */
			$this->register_new_content_controls();

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'al_general_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** Conteneur */
			$this->add_control(
				'al_container_style',
				array(
					'label'     => esc_html__( 'Container', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'al_wrapper_bg_color',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .swiper .swiper-slide, {{WRAPPER}} .al-posts__wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Produit */
			$this->add_control(
				'al_items_style',
				array(
					'label'     => esc_html__( 'Product', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_wrapper_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-0',
					'options'      => array(
						'style-0'  => esc_html__( 'Default', 'eac-components' ),
						'style-1'  => 'Style 1',
						'style-2'  => 'Style 2',
						'style-3'  => 'Style 3',
						'style-4'  => 'Style 4',
						'style-5'  => 'Style 5',
						'style-8'  => 'Style 6',
						'style-10' => 'Style 7',
						'style-11' => 'Style 8',
						'style-12' => 'Style 9',
					),
					'prefix_class' => 'al-post__wrapper-',
				)
			);

			$this->add_responsive_control(
				'al_wrapper_margin',
				array(
					'label'      => esc_html__( 'Margin between items', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 20,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 150,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__inner-wrapper' => 'block-size: calc(100% - {{SIZE}}px); margin: calc({{SIZE}}px / 2);',
						'(mobile) {{WRAPPER}} .al-post__inner-wrapper' => 'margin-block: 0 {{SIZE}}px !important; margin-inline: 0 !important;',
					),
				)
			);

			$this->add_control(
				'al_items_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__inner-wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'al_wrapper_border',
					'selector'  => '{{WRAPPER}} .al-post__inner-wrapper',
					'condition' => array( 'al_wrapper_style' => 'style-0' ),
				)
			);

			$this->add_control(
				'al_wrapper_radius',
				array(
					'label'              => esc_html__( 'Border radius', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__inner-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array( 'al_wrapper_style' => 'style-0' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'al_wrapper_shadow',
					'label'     => esc_html__( 'Shadow', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .al-post__inner-wrapper',
					'condition' => array( 'al_wrapper_style' => 'style-0' ),
				)
			);

			/** Filtre */
			$this->add_control(
				'al_filter_style',
				array(
					'label'     => esc_html__( 'Filter', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_filter_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_filter_typography',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector'  => '{{WRAPPER}} .al-filters__wrapper .al-filters__item a',
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_filter_background',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'background-color: {{VALUE}};' ),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_filter_outline',
				array(
					'label'     => esc_html__( 'Color of selected filter', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item.al-active:after' => 'border-block-end: 3px solid {{VALUE}};',
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item.al-active a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_responsive_control(
				'al_filter_padding',
				array(
					'label'     => esc_html__( 'Padding', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{LEFT}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'al_filter_border',
					'selector' => '{{WRAPPER}} .al-filters__wrapper .al-filters__item a',
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_filter_radius',
				array(
					'label'              => esc_html__( 'Border radius', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'selectors'          => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			/** Image */
			$this->add_control(
				'al_image_style',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'al_image_border',
					'selector' => '{{WRAPPER}} .al-post__image-wrapper img',
				)
			);

			$this->add_control(
				'al_image_radius',
				array(
					'label'              => esc_html__( 'Border radius', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			/** Titre */
			$this->add_control(
				'al_title_style',
				array(
					'label'     => esc_html__( 'Title', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_title' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_title_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__content-title, {{WRAPPER}} .al-post__content-title a' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_title' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'al_title_typo',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .al-post__content-title',
					'condition' => array( 'al_title' => 'yes' ),
				)
			);

			/** Description excerpt */
			$this->add_control(
				'al_excerpt_style',
				array(
					'label'     => esc_html__( 'Description', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_excerpt' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_excerpt_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
					'selectors' => array( '{{WRAPPER}} .shop-product__excerpt-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_excerpt' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_excerpt_typo',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
					'selector'  => '{{WRAPPER}} .shop-product__excerpt-wrapper',
					'condition' => array( 'al_excerpt' => 'yes' ),
				)
			);

			/** Avis */
			$this->add_control(
				'al_reviews_style',
				array(
					'label'     => esc_html__( 'Reviews', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_reviews' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_reviews_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array(
						'{{WRAPPER}} .shop-product__notes-wrapper,
						{{WRAPPER}} .al_post_customer-review,
						{{WRAPPER}} .woocommerce.shop-product__notes-wrapper .star-rating:before,
						{{WRAPPER}} .woocommerce.shop-product__notes-wrapper .star-rating span:before' => 'color: {{VALUE}};',
					),
					'condition' => array( 'al_reviews' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_reviews_typo',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__notes-wrapper,
						{{WRAPPER}} .al_post_customer-review,
						{{WRAPPER}} .woocommerce.shop-product__notes-wrapper .star-rating',
					'condition' => array( 'al_reviews' => 'yes' ),
				)
			);

			/** Prix */
			$this->add_control(
				'al_prices_style',
				array(
					'label'     => esc_html__( 'Price', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'al_prices' => 'yes',
					),
				)
			);

			$this->add_control(
				'al_prices_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .shop-product__prices-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array(
						'al_prices' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_prices_typo',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__prices-wrapper',
					'condition' => array(
						'al_prices' => 'yes',
					),
				)
			);

			/** Stock */
			$this->add_control(
				'al_stock_style',
				array(
					'label'     => esc_html__( 'Stock', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'al_stock'          => 'yes',
						'shop_is_a_catalog' => false,
					),
				)
			);

			$this->add_control(
				'al_stock_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .shop-product__stock-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array(
						'al_stock'          => 'yes',
						'shop_is_a_catalog' => false,
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_stock_typo',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__stock-wrapper',
					'condition' => array(
						'al_stock'          => 'yes',
						'shop_is_a_catalog' => false,
					),
				)
			);

			/** Quantité vendue */
			$this->add_control(
				'al_sold_style',
				array(
					'label'     => esc_html__( 'Sold', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'al_quantity_sold'  => 'yes',
						'shop_is_a_catalog' => false,
					),
				)
			);

			$this->add_control(
				'al_sold_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .shop-product__sold-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array(
						'al_quantity_sold'  => 'yes',
						'shop_is_a_catalog' => false,
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_sold_typo',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__sold-wrapper',
					'condition' => array(
						'al_quantity_sold'  => 'yes',
						'shop_is_a_catalog' => false,
					),
				)
			);

			/** Style de l'avatar */
			$this->add_control(
				'al_avatar_style',
				array(
					'label'     => esc_html( 'Avatar' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_avatar' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_avatar_size',
				array(
					'label'       => esc_html__( 'Size', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 40,
					'max'         => 150,
					'default'     => 60,
					'step'        => 10,
					'condition' => array( 'al_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'al_avatar_image_border',
					'selector'       => '{{WRAPPER}} .al-post__avatar-wrapper',
					'condition'      => array( 'al_avatar' => 'yes' ),
				)
			);

			$this->add_control(
				'al_avatar_border_radius',
				array(
					'label'              => esc_html__( 'Border radius', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__avatar-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'    => array( 'al_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'al_avatar_box_shadow',
					'label'     => esc_html__( 'Shadow', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .al-post__avatar-wrapper',
					'condition' => array( 'al_avatar' => 'yes' ),
				)
			);

			/** Meta tags */
			$this->add_control(
				'al_metas_style',
				array(
					'label'      => esc_html__( 'Meta tags', 'eac-components' ),
					'type'       => Controls_Manager::HEADING,
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_author',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_date',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '!==',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_filter',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_term',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'separator'  => 'before',
				)
			);

			$this->add_control(
				'al_metas_color',
				array(
					'label'      => esc_html__( 'Color', 'eac-components' ),
					'type'       => Controls_Manager::COLOR,
					'global'     => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_author',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_date',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '!==',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_filter',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_term',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'       => 'al_metas_typo',
					'label'      => esc_html__( 'Typography', 'eac-components' ),
					'global'     => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_author',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_date',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '!==',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_filter',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_term',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'selector'   => '{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date',
				)
			);

			$this->add_control(
				'al_navigation_style',
				array(
					'label'     => esc_html__( 'Navigation', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_nav_display' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_navigation_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__navigation .page-numbers:not(.current)' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_control(
				'al_navigation_color_current',
				array(
					'label'     => esc_html__( 'Color number current page', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__navigation .page-numbers.current' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_navigation_typography',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector'  => '{{WRAPPER}} .al-post__navigation',
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'al_navigation_border',
					'selector'  => '{{WRAPPER}} .al-post__navigation .page-numbers:not(.dots):not(.next):not(.prev):not(.current)',
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_control(
				'al_navigation_radius',
				array(
					'label'      => esc_html__( 'Border radius', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__navigation .page-numbers:not(.dots):not(.next):not(.prev):not(.current)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array( 'al_content_nav_display' => 'yes' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_slider_section_style',
			array(
				'label'      => esc_html__( 'Slider controls', 'eac-components' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'al_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_navigation',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'al_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_pagination',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

			/** Slider styles du trait */
			$this->register_slider_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_more_style',
			array(
				'label'     => esc_html__( 'Button View product', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'button_more' => 'yes' ),
			)
		);

			// Trait Style du bouton read more
			$this->register_button_more_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_cart_style',
			array(
				'label'     => esc_html__( 'Button Add to cart', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'button_cart'       => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			// Trait des styles du bouton 'Add to cart'
			$this->register_button_cart_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_promo_style',
			array(
				'label'     => esc_html__( 'Badge sale', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'promo_activate'    => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			// Trait des styles du badge 'promotion'
			$this->register_sale_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_stock_style',
			array(
				'label'     => esc_html__( 'Badge stock', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'stock_activate'    => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			// Trait des styles du badge 'stock'
			$this->register_stock_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_new_style',
			array(
				'label'     => esc_html__( 'Badge new product', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'new_activate'      => 'yes',
					'shop_is_a_catalog' => false,
				),
			)
		);

			// Trait des styles du badge nouveau produit
			$this->register_new_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render(): void {
		$settings       = $this->get_settings_for_display();
		$id             = 'slider_post_grid_' . $this->get_id();
		$has_swiper     = 'slider' === $settings['al_layout_type'] ? true : false;
		$has_navigation = $has_swiper && 'yes' === $settings['slider_navigation'] ? true : false;
		$has_pagination = $has_swiper && 'yes' === $settings['slider_pagination'] ? true : false;
		$has_scrollbar  = $has_swiper && 'yes' === $settings['slider_scrollbar'] ? true : false;

		if ( $has_swiper ) { ?>
		<div id="<?php echo esc_attr( $id ); ?>" class='eac-post-grid swiper'>
		<?php } else { ?>
		<div class='eac-post-grid'>
		<?php }
		/** Ajout d'un message avant la grille si mode catalog */
		if ( $settings['shop_is_a_catalog'] ) {
			$message = '';
			$message = apply_filters( 'eac_woo_catalog_product_message', $message );
			if ( ! empty( $message ) ) { ?>
				<div class='woocommerce-info'><?php echo esc_html( $message ); ?></div>
			<?php }
		}
		$this->render_products();
		if ( $has_navigation ) { ?>
			<div class='swiper-button-prev'></div>
			<div class='swiper-button-next'></div>
		<?php } ?>
		<?php if ( $has_scrollbar ) { ?>
			<div class='swiper-scrollbar'></div>
		<?php } ?>
		<?php if ( $has_pagination ) { ?>
			<div class='swiper-pagination-bullet'></div>
		<?php } ?>
		<div class='eac-skip-grid' tabindex='0'>
			<span class='visually-hidden'><?php esc_html_e( 'Exit grid', 'eac-components' ); ?></span>
		</div>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_products(): void {
		$settings   = $this->get_settings_for_display();
		$has_swiper = 'slider' === $settings['al_layout_type'] ? true : false;
		// Affichage du contenu du produit
		$has_avatar         = 'yes' === $settings['al_avatar'] ? true : false;
		$has_avatar_link    = $has_avatar && 'yes' === $settings['al_avatar_link'] ? true : false;
		$avatar_size        = ! empty( $settings['al_avatar_size'] ) ? $settings['al_avatar_size'] : 60;
		$has_image_lightbox = ! $has_swiper && 'yes' === $settings['al_lightbox'] ? true : false;
		$has_image_link     = ! $has_image_lightbox && 'yes' === $settings['al_image_link'] ? true : false;
		$lazy_load          = 'yes' === $settings['al_enable_image_lazy'] ? 'lazy' : 'eager';
		$has_term           = 'yes' === $settings['al_term'] ? true : false;
		$has_auteur         = 'yes' === $settings['al_author'] ? true : false;
		$has_date           = 'yes' === $settings['al_date'] ? true : false;
		$has_title          = 'yes' === $settings['al_title'] ? true : false;
		$has_title_link     = $has_title && 'yes' === $settings['al_title_link'] ? true : false;
		$has_global_link    = isset( $settings['al_article_link'] ) && 'yes' === $settings['al_article_link'] ? true : false;
		$max_num_pages      = 0;

		$has_resum                  = 'yes' === $settings['al_excerpt'] ? true : false;
		$has_promo                  = 'yes' === $settings['promo_activate'] ? true : false;
		$has_stock_badge_initial    = 'yes' === $settings['stock_activate'] ? true : false;
		$has_stock_initial          = 'yes' === $settings['al_stock'] ? true : false;
		$has_stock_format           = $has_stock_initial && 'yes' === $settings['al_stock_format'] ? true : false;
		$has_new_badge              = 'yes' === $settings['new_activate'] ? true : false;
		$has_date_expire            = $has_new_badge && ! empty( $settings['new_date'] ) ? absint( $settings['new_date'] ) : '';
		$has_quantity_sold          = 'yes' === $settings['al_quantity_sold'] ? true : false;
		$has_quantity_sold_fallback = $has_quantity_sold && ! empty( $settings['al_quantity_sold_fallback'] ) ? esc_html( $settings['al_quantity_sold_fallback'] ) : '';
		$has_reviews                = 'yes' === get_option( 'woocommerce_enable_reviews' ) && 'yes' === $settings['al_reviews'] ? true : false;
		$notes_format               = $settings['al_reviews_format'];
		$is_a_catalog               = $settings['shop_is_a_catalog'];
		/** Affiche le prix si le user est logué ou ce n'est pas un catalogue */
		$has_prices            = $settings['al_prices'] && ( is_user_logged_in() || ! $is_a_catalog );
		$prices_format         = $settings['al_prices_format'];
		$has_promo_percent     = 'yes' === $settings['sale_format'] ? true : false;
		$has_readmore          = 'yes' === $settings['button_more'] ? true : false;
		$has_more_button_picto = 'yes' === $settings['button_add_more_picto'] && ! empty( $settings['button_more_picto'] ) ? true : false;
		$has_cart_initial      = ! $is_a_catalog && 'yes' === $settings['button_cart'] ? true : false;
		$has_cart_button       = 'yes' === $settings['button_add_cart_picto'] && ! empty( $settings['button_cart_picto'] ) ? true : false;
		// Filtre Users. Champ TEXT
		$has_users        = ! empty( $settings['al_content_user'] ) ? true : false;
		$user_filters     = esc_html( $settings['al_content_user'] );
		$has_filters      = ! $has_swiper && isset( $settings['al_filter'] ) && 'yes' === $settings['al_filter'] ? true : false;

		// Filtre Taxonomie. Champ SELECT2
		$taxonomy_filter = $settings['al_article_taxonomy'];

		// Filtre Étiquettes, on prélève le slug. Champ SELECT2
		$term_filter = array();
		// Extrait les slugs du tableau de terms
		if ( ! empty( $settings['al_article_term'] ) ) {
			$term_filter = array_map( function ( $article_term ) {
				return \str_contains( $article_term, '::' ) ? explode( '::', $article_term, 2 )[1] : $article_term;
			}, $settings['al_article_term']);
		}

		// Pagination/Navigation
		$has_pagging  = ! $has_swiper && isset( $settings['al_content_pagging_display'] ) && 'yes' === $settings['al_content_pagging_display'] ? true : false;
		$has_navigate = ! $has_swiper && isset( $settings['al_content_nav_display'] ) && 'yes' === $settings['al_content_nav_display'] ? true : false;

		/** Formate le titre avec son tag */
		$title_tag   = ! empty( $settings['al_title_tag'] ) ? Utils::validate_html_tag( $settings['al_title_tag'] ) : 'div';
		// Ajoute l'ID de l'article au titre
		$has_id = 'yes' === $settings['al_article_id'] ? true : false;
		// Formate les arguments et exécute la requête WP_Query et mets en cache les résultats de la requête
		$post_args = $this->helper_util->build_post_args( $settings );
		$the_query = new \WP_Query( $post_args );

		// Wrapper de la liste des posts et du bouton de pagination avec l'ID du widget Elementor
		$widget_id     = $this->get_id();
		$wrapper_id    = 'al_posts_wrapper_' . $widget_id;
		$pagination_id = 'al_pagination_' . $widget_id;
		$navigation_id = 'al_navigation_' . $widget_id;

		// La div wrapper
		$layout = 'equalHeight' === $settings['al_layout_type'] ? 'fitRows' : $settings['al_layout_type'];
		$max_num_pages = absint( $the_query->max_num_pages );

		if ( ! $has_swiper ) {
			$class = sprintf( 'al-posts__wrapper shop-products__wrapper layout-type-%s', $layout );
		} else {
			$class = 'al-posts__wrapper shop-products__wrapper swiper-wrapper';
		}

		$this->add_render_attribute( 'posts_wrapper', 'class', esc_attr( $class ) );
		$this->add_render_attribute( 'posts_wrapper', 'id', esc_attr( $wrapper_id ) );
		if ( $has_filters || ( $has_pagging && $the_query->found_posts > 0 ) || $has_navigate ) {
			$this->add_render_attribute( 'posts_wrapper', 'role', 'region' );
			$this->add_render_attribute( 'posts_wrapper', 'aria-relevant', 'additions removals' );
			$this->add_render_attribute( 'posts_wrapper', 'aria-live', 'polite' );
			$this->add_render_attribute( 'posts_wrapper', 'aria-atomic', 'false' );
		}
		$this->add_render_attribute( 'posts_wrapper', 'data-settings', $this->get_settings_json( $max_num_pages, $the_query->found_posts ) );

		/** Affiche les arguments de la requête */
		if ( 'yes' === $settings['al_display_content_args'] && \Elementor\Plugin::$instance->editor->is_edit_mode() ) { ?>
			<div class='al-posts_query-args'>
				<?php highlight_string( "<?php\nQuery args =\n" . var_export( $this->helper_util->get_post_query_args(), true ) . ";\n?>" ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export ?>
			</div>
		<?php }

		ob_start( array( '\EACCustomWidgets\Core\Utils\Eac_Tools_Util', 'compress_html_full_output' ), 0, PHP_OUTPUT_HANDLER_REMOVABLE );
		if ( $the_query->have_posts() ) {
			/** Création et affichage des filtres avant le widget */
			if ( $has_filters ) {
				// phpcs:disable WordPress.Security.EscapeOutput
				if ( $has_users ) {
					echo $this->helper_util->get_user_filter( $user_filters, $widget_id, $wrapper_id );
				} elseif ( ! empty( $taxonomy_filter ) ) {
					echo $this->helper_util->get_tax_query_filter( $taxonomy_filter, $term_filter, $widget_id, $wrapper_id );
				}
				// phpcs:enable WordPress.Security.EscapeOutput
			} ?>
			<div <?php $this->print_render_attribute_string( 'posts_wrapper' ); ?>>
				<?php if ( ! $has_swiper ) { ?>
					<div class='al-posts__wrapper-sizer'></div>
				<?php }
				/** Le loop */
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$terms_slug = array(); // Tableau de slug concaténé avec la class de l'article
					$terms_name = array(); // Tableau du nom des slugs Concaténé pour les étiquettes
					$product = null;
					$product = wc_get_product( get_the_ID() );
					if ( ! is_a( $product, 'WC_Product' ) ) {
						continue;
					}

					$product_id    = $product->get_id();
					$product_title = $product->get_name();
					$product_url   = $product->get_permalink();
					$product_sold  = $product->get_total_sales();

					/**
					 * Gérer le stock de chaque produit
					 * Les variables sont reinitialisées à chaque boucle
					 *
					 * $has_stock       Affichage du libellé et du niveau de stock
					 * $has_stock_badge Affichage du badge 'Out of stock'
					 * $has_cart        Affichage du bouton 'Add to cart'
					 */
					$has_stock       = $has_stock_initial;
					$has_stock_badge = $has_stock_badge_initial;
					$has_cart        = $has_cart_initial;

					// Produit en stock
					if ( $product->is_in_stock() ) {
						$has_stock       = true === $has_stock ? true : false;
						$has_stock_badge = false;
						$has_cart        = true === $has_cart ? true : false;
					} else {
						$has_stock       = true === $has_stock ? true : false;
						$has_stock_badge = true;
						$has_cart        = false;
					}

					$product_taxo = '';
					if ( ! empty( $taxonomy_filter ) ) {
						$product_taxo = get_the_term_list( $product_id, $taxonomy_filter[0], '', ' | ' );
					}

					$product_promo = esc_html( $settings['sale_text'] );
					if ( $has_promo_percent && $product->is_on_sale() ) {
						$product_promo = '-' . round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 ) . '%';
					}

					/** Affichage du badge 'NEW' */
					$has_expired = true;
					if ( ! empty( $has_date_expire ) ) {
						$product_created   = $product->get_date_created(); // Date de création du produit
						$timestamp_created = $product_created->getTimestamp(); // Le timestamp de la date de création du produit
						$datetime_now      = new \WC_DateTime(); // La date du jour
						$timestamp_now     = $datetime_now->getTimestamp(); // Le timestamp de la date du jour

						// Création du produit jusqu'à aujourd'hui en nombre de jours
						$created_to_today = round( ( $timestamp_now - $timestamp_created ) / 86400 );

						$has_expired = $created_to_today > $has_date_expire ? true : false;
					}

					/** Champ user renseigné */
					if ( $has_users ) {
						$user                = get_the_author_meta( 'display_name' );
						$terms_slug[ $user ] = sanitize_title( $user );
						$terms_name[ $user ] = ucfirst( $user );
					} elseif ( ! empty( $taxonomy_filter ) ) {
						$terms_array = array();
						foreach ( $taxonomy_filter as $post_term ) {
							$terms_array = wp_get_post_terms( $product_id, $post_term );
							if ( ! is_wp_error( $terms_array ) && ! empty( $terms_array ) ) {
								foreach ( $terms_array as $term ) {
									if ( 0 !== $term->parent ) {
										$term_parent = get_term( $term->parent );
										if ( ! is_wp_error( $term_parent ) ) {
											if ( ! empty( $term_filter ) ) {
												if ( in_array( $term_parent->slug, $term_filter, true ) ) {
													$terms_slug[ $term_parent->slug ] = $term_parent->slug;
													$terms_name[ $term_parent->name ] = ucfirst( $term_parent->name );
												}
											} else {
												$terms_slug[ $term_parent->slug ] = $term_parent->slug;
												$terms_name[ $term_parent->name ] = ucfirst( $term_parent->name );
											}
										}
									}
									if ( ! empty( $term_filter ) ) {
										if ( in_array( $term->slug, $term_filter, true ) ) {
											$terms_slug[ $term->slug ] = $term->slug;
											$terms_name[ $term->name ] = ucfirst( $term->name );
										}
									} else {
										$terms_slug[ $term->slug ] = $term->slug;
										$terms_name[ $term->name ] = ucfirst( $term->name );
									}
								}
							}
						}
					}

					if ( ! $has_swiper ) {
						$post_class = sprintf( '%1$s %2$s %3$s %4$s', $widget_id, 'al-post__wrapper', implode( ' ', array_map( 'esc_attr', $terms_slug ) ), implode( ' ', get_post_class( '', get_the_ID() ) ) );
					} else {
						$post_class = sprintf( '%1$s %2$s %3$s', $widget_id, 'al-post__wrapper swiper-slide', implode( ' ', get_post_class( '', get_the_ID() ) ) );
					}
					$attachment = Eac_Tools_Util::wp_get_attachment_data( get_post_thumbnail_id( $product_id ), $settings['al_image_dimension_size'] );
					?>
					<article id="<?php echo 'post-' . esc_attr( $product_id ); ?>" class="<?php echo esc_attr( $post_class ); ?>">
						<div class='al-post__inner-wrapper'>
							<?php if ( $has_promo && $product->is_on_sale() && ! $is_a_catalog ) { ?>
								<span class='badge-sale' role='status' aria-live='polite' aria-atomic='true' dir='auto'><?php echo esc_html( $product_promo ); ?></span>
							<?php } ?>
							<?php if ( $has_stock_badge && ! $is_a_catalog ) { ?>
								<span class='badge-stock' role='status' aria-live='polite' aria-atomic='true'><?php echo esc_html( $settings['stock_text'] ); ?></span>
							<?php } ?>
							<?php if ( ! $has_expired ) { ?>
								<span class='badge-new' role='status' aria-live='polite' aria-atomic='true'><?php echo esc_html( $settings['new_text'] ); ?></span>
							<?php } ?>
							<?php if ( has_post_thumbnail() && ! empty( $attachment ) ) { ?>
								<div class='al-post__image-wrapper'>
									<?php
									$this->add_render_attribute(
										'post_image',
										array(
											'class'  => 'eac-accessible-img al-post__image-loaded',
											'src'    => esc_url( $attachment['src'] ),
											'srcset' => esc_attr( $attachment['srcset'] ),
											'sizes'  => esc_attr( $attachment['srcsize'] ),
											'width'  => esc_attr( $attachment['width'] ),
											'height' => esc_attr( $attachment['height'] ),
										)
									);
									if ( ! $has_image_lightbox ) {
										$this->add_render_attribute(
											'post_image',
											array(
												'alt'  => '',
											)
										);
									} elseif ( $has_image_lightbox ) {
										$this->add_render_attribute( 'post_image', 'alt', esc_attr( $attachment['alt'] ) );
									}
									if ( 'eager' === $lazy_load ) {
										$this->add_render_attribute( 'post_image', 'loading', $lazy_load );
									}
									if ( $has_image_lightbox ) { ?>
										<a class='eac-accessible-link' href="<?php echo esc_url( $attachment['image_url'] ); ?>" data-elementor-open-lightbox='no' data-fancybox="al-gallery-<?php echo esc_attr( $widget_id ); ?>" data-caption="<?php echo esc_html( $product_title ); ?>" role='button' aria-haspopup='dialog' aria-expanded='false' aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'View image', 'eac-components' ), esc_html( $product_title ) ); ?>">
									<?php } ?>
									<?php if ( $has_image_link && $product_url ) {
										$class_link = $has_global_link ? 'eac-accessible-link card-link' : 'eac-accessible-link'; ?>
										<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'View product page', 'eac-components' ), esc_html( $product_title ) ); ?>">
									<?php } ?>
										<img <?php $this->print_render_attribute_string( 'post_image' ); ?>>
									<?php if ( $has_image_lightbox || ( $has_image_link && $product_url ) ) { ?>
										</a>
									<?php } ?>
								</div>
							<?php } ?>

							<?php if ( $has_title || $has_reviews || $has_prices || $has_stock || $has_quantity_sold || $has_readmore || $has_cart ) { ?>
								<div class='al-post__text-wrapper'>
									<!-- Le titre -->
									<?php if ( $has_title ) {
										$class_link = $has_global_link ? 'eac-accessible-link card-link' : 'eac-accessible-link';
										?>
										<!-- Affiche les IDs -->
										<?php if ( $has_id && $has_title_link && $product_url ) { ?>
											<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $product_url ); ?>">
												<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s : %3$s</%1$s>', esc_attr( $title_tag ), esc_attr( intval( $product_id ) ), esc_html( $product_title ) ); ?>
											</a>
										<?php } elseif ( $has_id && ! $has_title_link ) { ?>
											<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s : %3$s</%1$s>', esc_attr( $title_tag ), esc_attr( intval( $product_id ) ), esc_html( $product_title ) ); ?>
										<?php } elseif ( ! $has_id && $has_title_link && $product_url ) { ?>
											<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'View product page', 'eac-components' ), esc_html( $product_title ) ); ?>">
												<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s</%1$s>', esc_attr( $title_tag ), esc_html( $product_title ) ); ?>
											</a>
										<?php } else { ?>
											<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s</%1$s>', esc_attr( $title_tag ), esc_html( $product_title ) ); ?>
										<?php } ?>
									<?php } ?>

									<!-- Le résumé de l'article -->
									<?php if ( $has_resum ) { ?>
										<div class='shop-product__excerpt-wrapper global__line-height'>
											<span dir='ltr'><?php echo esc_html( Eac_Tools_Util::get_post_excerpt( $product_id, absint( $settings['al_excerpt_length'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
										</div>
									<?php } ?>

									<!-- Avis -->
									<?php
									if ( $has_reviews ) {
										$notes       = '';
										$long_format = '';
										if ( absint( $product->get_review_count() ) > 0 ) {
											$long_format = '<span class="al_post_customer-review"> (' . absint( $product->get_review_count() ) . esc_html__( ' Customer reviews', 'eac-components' ) . ')</span>';
										}
										// Au moins une note
										// if (absint($product->get_review_count()) > 0) {
										switch ( $notes_format ) {
											case 'average_rating':
												$notes = esc_html__( 'Average notes', 'eac-components' ) . ' ' . $product->get_average_rating();
												break;
											case 'average_html':
												$notes = wc_get_rating_html( $product->get_average_rating() );
												break;
											case 'average_html_long':
												$notes = wc_get_rating_html( $product->get_average_rating() ) . $long_format;
												break;
											case 'rating_count':
												$notes = esc_html__( 'Number of notes', 'eac-components' ) . ' ' . absint( $product->get_rating_count() );
												break;
											case 'review_count':
												$notes = esc_html__( 'Review count', 'eac-components' ) . ' ' . absint( $product->get_review_count() );
												break;
										}

										if ( ! empty( $notes ) ) { ?>
											<div class="woocommerce shop-product__notes-wrapper"><?php echo $notes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
										<?php } ?>
									<?php } ?>

									<!-- Prix -->
									<?php
									if ( $has_prices && $product->get_regular_price() ) {
										$prices         = '';
										$sales_price_to = '';
										$sales_price_to = get_post_meta( $product_id, '_sale_price_dates_to', true );
										switch ( $prices_format ) {
											case 'both':
												$prices = $product->get_price_html();
												break;
											case 'dateto':
												$prices = $product->get_price_html();
												if ( $product->is_type( 'simple' ) && '' !== $sales_price_to ) {
													$sales_price_date_to = date_i18n( get_option( 'date_format' ), $sales_price_to );
													$prices              = str_replace( '</ins>', ' </ins> (' . esc_html__( 'Until ', 'eac-components' ) . $sales_price_date_to . ')', $prices );
												}
												break;
											case 'regular':
												$prices = wc_price( $product->get_regular_price() ) . $product->get_price_suffix();
												break;
											case 'promo' && $product->is_on_sale():
												$prices = wc_price( $product->get_sale_price() ) . $product->get_price_suffix();
												break;
										}
										if ( ! empty( $prices ) ) { ?>
											<div class='shop-product__prices-wrapper'><?php echo $prices; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
										<?php } ?>
									<?php } ?>

									<!-- Stock -->
									<?php
									if ( $has_stock ) {
										$stock = '';
										if ( $has_stock_format ) {
											$stock = wc_get_stock_html( $product );
										} else {
											$stock = esc_html__( 'Quantity', 'eac-components' ) . ' ' . (int) $product->get_stock_quantity();
										}
										?>
										<div class='woocommerce shop-product__stock-wrapper'><?php echo $stock; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
									<?php } ?>

									<!-- Quantité vendue -->
									<?php
									/** Affiche le fallback quand le produit n'a pas été vendu et qu'il n'est pas en rupture de stock */
									if ( $has_quantity_sold ) {
										$quantity_sold = '';
										if ( 0 !== absint( $product_sold ) ) {
											$quantity_sold = esc_html__( 'Quantity sold', 'eac-components' ) . ' ' . absint( $product_sold );
										} elseif ( 0 === absint( $product_sold ) && false === $has_stock_badge ) {
											$quantity_sold = esc_html( $has_quantity_sold_fallback );
										}
										if ( ! empty( $quantity_sold ) ) { ?>
											<div class='shop-product__sold-wrapper'><?php echo $quantity_sold; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
										<?php } ?>
									<?php } ?>

									<!-- Les boutons -->
									<?php if ( $has_readmore || $has_cart ) { ?>
										<div class='buttons-wrapper'>
											<?php if ( $has_readmore && $product_url ) {
												$this->render_button_more(
													array(
														'permalink'     => $product_url,
														'item_title'    => $product_title,
														'global_link'   => $has_global_link,
														'default_label' => esc_html__( 'View product page', 'eac-components' ),
													)
												);
											}

											if ( $has_cart && $product->get_regular_price() ) {
												$has_cart_quantity     = 'yes' === $settings['cart_quantity_activate'] ? true : false;
												$product_cart_quantity = 0;
												// Récupère la quantité dans le panier
												if ( $has_cart_quantity && ! is_null( WC()->cart ) && ! WC()->cart->is_empty() ) {
													foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
														if ( $cart_item['product_id'] === $product_id ) {
															$product_cart_quantity = $cart_item['quantity'];
														}
													}
												}
												// Redirection ?
												if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
													$product_cart_url = wc_get_cart_url() . '?add-to-cart=' . $product_id;
												} else {
													$product_cart_url = $product_url . '?add-to-cart=' . $product_id;
												}
												?>
												<a href="<?php echo esc_url( $product_cart_url ); ?>" class='button-cart eac-accessible-link' aria-label="<?php printf( '%1$s - %2$s %3$s', esc_html__( 'Add to cart', 'eac-components' ), esc_html( $product_title ), esc_html__( 'and view the cart', 'eac-components' ) ); ?>">
													<span class='button__cart-wrapper'>
														<?php if ( $has_cart_quantity && 0 !== absint( $product_cart_quantity ) ) { ?>
															<span class='badge-cart__quantity' role='status' aria-live='polite' aria-atomic='true' aria-label="<?php printf( '%1$d %2$s', absint( $product_cart_quantity ), esc_html__( 'article(s) dans le panier', 'eac-components' ) ); ?>">
																<span aria-hidden='true'><?php echo absint( $product_cart_quantity ); ?></span>
															</span>
														<?php }
														if ( $has_cart_button && 'before' === $settings['button_cart_position'] ) { ?>
															<span class='button-icon eac-icon-svg'>
															<?php Icons_Manager::render_icon( $settings['button_cart_picto'], array( 'aria-hidden' => 'true' ) ); ?>
															</span>
														<?php }
														printf( '<span class="label-icon">%s</span>', esc_html( trim( $settings['button_cart_label'] ) ) );
														if ( $has_cart_button && 'after' === $settings['button_cart_position'] ) { ?>
															<span class='button-icon eac-icon-svg'>
															<?php Icons_Manager::render_icon( $settings['button_cart_picto'], array( 'aria-hidden' => 'true' ) ); ?>
															</span>
														<?php } ?>
													</span>
												</a>
											<?php } ?>
										</div>
									<?php } ?>

									<?php if ( $has_avatar || $has_term || $has_auteur || $has_date ) { ?>
										<div class="al-post__meta-wrapper">
											<!-- Ajout de l'attribut 'loading' à l'avatar -->
											<?php if ( $has_avatar ) { ?>
												<?php
												$author_url      = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => absint( $avatar_size ) ) );
												$author_archives = get_author_posts_url( get_the_author_meta( 'ID' ) );
												$author_name     = get_the_author_meta( 'display_name' );
												?>
												<div class="al-post__avatar-wrapper">
													<?php if ( $has_avatar_link ) { ?>
														<a href="<?php echo esc_url( $author_archives ); ?>" class='eac-accessible-link avatar-link' aria-label="<?php printf( '%1$s %2$s', esc_attr__( 'View posts from', 'eac-components' ), esc_attr( $author_name ) ); ?>">
													<?php } ?>
														<img class='avatar photo' src="<?php echo esc_url( $author_url ); ?>" alt="Avatar of <?php echo esc_html( $author_name ); ?>" loading='lazy' width="<?php echo absint( $avatar_size ); ?>" height="<?php echo absint( $avatar_size ); ?>" />
													<?php if ( $has_avatar_link ) { ?>
														</a>
													<?php } ?>
												</div>
											<?php } ?>

											<?php if ( $has_term || $has_auteur || $has_date ) { ?>
												<div class='al-post__meta'>
													<!-- Les étiquettes -->
													<?php if ( $has_term ) {
														$terms = $this->get_svg_icon_terms() . '<span>' . implode( '|', array_map( 'esc_html', $terms_name ) ) . '</span>'; ?>
														<div class='al-post__meta-tags eac-icon-svg'>
															<?php echo wp_kses_post( $terms ); ?>
														</div>
													<?php } ?>

													<!-- L'auteur de l'article -->
													<?php if ( $has_auteur ) {
														$auteur = $this->get_svg_icon_user() . '<span>' . esc_html( get_the_author_meta( 'display_name' ) ) . '</span>'; ?>
														<div class='al-post__meta-author eac-icon-svg'>
															<?php echo wp_kses_post( $auteur ); ?>
														</div>
													<?php } ?>

													<!-- Le date de création ou de dernière modification -->
													<?php if ( $has_date ) {
														if ( 'modified' === $settings['al_article_orderby'] ) {
															$calendar = $this->get_svg_icon_calendar() . '<span>' . date_i18n( get_option( 'date_format' ), strtotime( $product->get_date_modified( 'Y-m-d' ) ) ) . '</span>';
														} else {
															$calendar = $this->get_svg_icon_calendar() . '<span>' . date_i18n( get_option( 'date_format' ), strtotime( $product->get_date_created( 'Y-m-d' ) ) ) . '</span>';
														} ?>
														<div class='al-post__meta-date eac-icon-svg'>
															<?php echo wp_kses_post( $calendar ); ?>
														</div>
													<?php } ?>
												</div>
											<?php } ?>
										</div> <!-- Fin al-post__meta-wrapper -->
									<?php } ?>
								</div> <!-- Fin al-post__text-wrapper -->
							<?php } ?>
						</div> <!-- Fin al-post__inner-wrapper -->
					</article>
					<?php
					$this->remove_render_attribute( 'post_image' );
				} ?>
			</div>
			<?php if ( $has_pagging && $the_query->post_count < $the_query->found_posts ) {
				$this->add_render_attribute( 'read_button', 'class', 'eac-accessible-link button__readmore-wrapper' );
				$this->add_render_attribute( 'read_button', 'type', 'button' );
				$this->add_render_attribute( 'read_button', 'aria-label', esc_attr__( 'Pagination load next page', 'eac-components' ) );

				$this->add_inline_editing_attributes( 'al_pagination_label', 'none' );
				$this->add_render_attribute( 'al_pagination_label', 'class', 'label-icon' );

				$label        = esc_html__( 'View more product', 'eac-components' );
				$label_button = sprintf( '<span %1$s>%2$s</span><span class="al-more-button-paged">%3$s/%4$s</span>', $this->get_render_attribute_string( 'al_pagination_label' ), $label, $the_query->post_count, $the_query->found_posts );
				$button       = sprintf( '<button %1$s>%2$s</button>', $this->get_render_attribute_string( 'read_button' ), $label_button );
				?>
				<div class='al-post__pagination' id="<?php echo esc_attr( $pagination_id ); ?>">
					<div class='buttons-wrapper'><?php echo wp_kses_post( $button ); ?></div>
					<div class='al-page-load-status'>
						<div class='infinite-scroll-request eac__loader-spin'></div>
						<p class='infinite-scroll-last'><?php esc_html_e( 'View more product', 'eac-components' ); ?></p>
						<p class='infinite-scroll-error'><?php esc_html_e( 'No more product', 'eac-components' ); ?></p>
					</div>
				</div>
			<?php } elseif ( $has_navigate ) {
				$page_links = paginate_links(
					array(
						'format'             => '?paged=%#%',
						'prev_text'          => '&laquo;',
						'next_text'          => '&raquo;',
						'mid_size'           => 1,
						'total'              => $max_num_pages,
						'current'            => max( 1, get_query_var( 'paged' ) ),
					)
				);
				if ( $page_links ) { ?>
					<nav class='al-post__navigation' id="<?php echo esc_attr( $navigation_id ); ?>" aria-label="<?php esc_attr_e( 'Paging', 'eac-components' ); ?>" itemprop='pagination'>
						<div class='al-post__navigation-digit'><?php echo wp_kses_post( $page_links ); ?></div>
					</nav>
				<?php }
			}

			wp_reset_postdata();
		}
		ob_end_flush();
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 * Modification de la règles 'data_filtre'
	 *
	 * @uses      wp_json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 */
	protected function get_settings_json( $max_num_pages, $found_posts ): string {
		$settings      = $this->get_settings_for_display();
		$unique_id     = esc_attr( $this->get_id() );
		$wrapper_id    = '#al_posts_wrapper_' . $unique_id;
		$pagination_id = '#al_pagination_' . $unique_id;
		$navigation_id = '#al_navigation_' . $unique_id;

		$effect = $settings['slider_effect'];
		if ( in_array( $effect, array( 'fade', 'creative' ), true ) ) {
			$nb_images = 1;
		} elseif ( isset( $settings['slider_images_centered'] ) && 'yes' === $settings['slider_images_centered'] ) {
			$nb_images = 2;
		} elseif ( empty( $settings['slider_images_number'] ) ) {
			$nb_images = 3;
		} elseif ( 0 === absint( $settings['slider_images_number'] ) ) {
			$nb_images = 'auto';
			$effect    = 'slide';
		} else {
			$nb_images = absint( $settings['slider_images_number'] );
		}

		$has_swiper = 'slider' === $settings['al_layout_type'] ? true : false;

		$module_settings = array(
			'data_id'                  => $wrapper_id,
			'data_pagination'          => ! $has_swiper && 'yes' === $settings['al_content_pagging_display'] ? true : false,
			'data_pagination_id'       => $pagination_id,
			'data_navigation'          => ! $has_swiper && 'yes' === $settings['al_content_nav_display'] ? true : false,
			'data_navigation_id'       => $navigation_id,
			'data_layout'              => 'equalHeight' === $settings['al_layout_type'] ? 'fitRows' : $settings['al_layout_type'],
			'data_equalheight'         => in_array( $settings['al_layout_type'], array( 'equalHeight', 'fitRows' ), true ) ? true : false,
			'data_article'             => $unique_id,
			'data_filtre'              => ! $has_swiper && isset( $settings['al_filter'] ) && 'yes' === $settings['al_filter'] ? true : false,
			'data_fancybox'            => 'yes' === $settings['al_lightbox'] ? true : false,
			'data_max_pages'           => absint( $max_num_pages ),
			'data_found_posts'         => absint( $found_posts ),
			'data_sw_id'               => 'eac_post_grid_' . $unique_id,
			'data_sw_swiper'           => $has_swiper,
			'data_sw_autoplay'         => 'yes' === $settings['slider_autoplay'] ? true : false,
			'data_sw_loop'             => 'yes' === $settings['slider_loop'] ? true : false,
			'data_sw_delay'            => ! empty( $settings['slider_delay'] ) ? absint( $settings['slider_delay'] ) : 2000,
			'data_sw_imgs'             => $nb_images,
			'data_sw_centered'         => 'yes' === $settings['slider_images_centered'] ? true : false,
			'data_sw_dir'              => 'horizontal',
			'data_sw_rtl'              => 'right' === $settings['slider_rtl'] ? true : false,
			'data_sw_effect'           => $effect,
			'data_sw_free'             => true,
			'data_sw_pagination_click' => 'yes' === $settings['slider_pagination'] && 'yes' === $settings['slider_pagination_click'] ? true : false,
			'data_sw_scroll'           => 'yes' === $settings['slider_scrollbar'] ? true : false,
			'data_animate'             => 'yes' === $settings['al_enable_animation'] ? true : false,
			'data_lazy'                => 'yes' === $settings['al_enable_image_lazy'] ? true : false,
		);

		return wp_json_encode( $module_settings );
	}

	protected function content_template(): void {}
}
