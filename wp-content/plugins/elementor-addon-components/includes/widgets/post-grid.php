<?php
/**
 * Class: Post_Grid_Widget
 * Name: Grille d'articles
 * Slug: eac-addon-articles-liste
 *
 * Description: Affiche les articles, les CPT et les pages
 * dans différents modes, masonry ou grille et avec différents filtres
 *
 * @since 1.0.0
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
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;
use Elementor\Utils;

class Post_Grid_Widget extends Widget_Base {
	/** Le slider Trait */
	use \EACCustomWidgets\Includes\Traits\Slider_Trait;
	use \EACCustomWidgets\Includes\Traits\Button_Read_More_Trait;
	use \EACCustomWidgets\Includes\Traits\Icon_Svg_Trait;

	/** Constructeur */
	public function __construct( array $data = array(), ?array $args = null ) {
		parent::__construct( $data, $args );

		$this->helper_util = new Eac_Helper_Util();

		remove_all_filters( 'eac/tools/post_orderby' );
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
	private $slug = 'articles-liste';

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
		return array( 'eac-post-grid' );
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
		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';
		$active_breakpoints     = Plugin::$instance->breakpoints->get_active_breakpoints();
		$has_active_breakpoints = Plugin::$instance->breakpoints->has_custom_breakpoints();

		$this->start_controls_section(
			'al_post_filter',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
					'label'       => esc_html__( 'Post type', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'options'     => Eac_Tools_Util::get_filter_post_types( array( 'product' ) ),
					'default'     => array( 'post' ),
					'multiple'    => true,
				)
			);

			$this->add_control(
				'al_article_taxonomy',
				array(
					'label'       => esc_html__( 'Taxonomy filter', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'options'     => Eac_Tools_Util::get_all_taxonomies( array( 'product' ) ),
					'default'     => array( 'category' ),
					'multiple'    => true,
				)
			);

			$this->add_control(
				'al_article_term',
				array(
					'label'       => esc_html__( 'Tag filter', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'options'     => Eac_Tools_Util::get_all_terms( array( 'product' ) ),
					'multiple'    => true,
				)
			);

			/**$this->add_control(
				'al_article_term',
				array(
					'label'          => esc_html__( 'Tag filter', 'eac-components' ),
					'type'           => 'eac-select2',
					'select2Options' => array(
						'object_type' => Eac_Tools_Util::get_filter_post_types( array( 'product' ), false ),
						'query_type'  => 'term',
						//'query_taxo'  => array( 'category', 'post_tag' ),
					),
					'multiple'    => true,
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
					'label'     => esc_html__( 'Post', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_layout_type!' => 'slider' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_article_nombre',
				array(
					'label'       => esc_html__( 'Post count', 'eac-components' ),
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
					'label'       => esc_html__( 'IDs', 'eac-components' ),
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
				'al_article_include',
				array(
					'label'        => esc_html__( 'Include children', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name'     => 'al_article_type',
								'operator' => '!contains',
								'value'    => 'post',
							),
							array(
								'name'     => 'al_layout_type',
								'operator' => '!==',
								'value'    => 'slider',
							),
						),
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
			'al_layout_type_settings',
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
					'condition' => array(
						'al_content_image' => 'yes',
						'al_layout_type!'  => 'slider',
					),
				)
			);

			$this->add_control(
				'al_image_heading',
				array(
					'label'     => esc_html__( 'Image layout', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_image' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_image_dimension',
				array(
					'label'   => esc_html__( 'Size', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options' => array(
						'thumbnail'    => esc_html__( 'Thumbnail', 'eac-components' ),
						'medium'       => esc_html__( 'Medium', 'eac-components' ),
						'medium_large' => esc_html__( 'Medium-large', 'eac-components' ),
						'large'        => esc_html__( 'Large', 'eac-components' ),
						'full'         => esc_html__( 'Original', 'eac-components' ),
					),
					'condition' => array( 'al_content_image' => 'yes' ),
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
					'default'   => 'no',
					'toggle'    => false,
					'condition' => array( 'al_content_image' => 'yes' ),
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
					'condition' => array(
						'al_layout_type'   => 'equalHeight',
						'al_content_image' => 'yes',
					),
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
						'al_content_image'      => 'yes',
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => 'equalHeight',
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
						'al_content_image'      => 'yes',
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => 'equalHeight',
					),
				)
			);

			$this->add_control(
				'al_content_layout',
				array(
					'label'     => esc_html__( 'Content layout', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'al_content_image' => 'yes' ),
				)
			);

			$this->add_control(
				'al_layout_side_by_side',
				array(
					'label'     => esc_html__( 'Side by side', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_image' => 'yes' ),
				)
			);

			$this->add_control(
				'al_layout_texte',
				array(
					'label'        => esc_html__( 'Right', 'eac-components' ),
					'description'  => esc_html__( 'Image on the left Content on the right', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'render_type'  => 'template',
					'prefix_class' => 'layout-text__right-',
					'condition'    => array(
						'al_layout_texte_left!' => 'yes',
						'al_content_image'      => 'yes',
					),
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
					'condition'    => array(
						'al_layout_texte!' => 'yes',
						'al_content_image' => 'yes',
					),
				)
			);

			$this->add_responsive_control(
				'al_layout_image_width',
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
				'al_content_align_v',
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
					'condition'   => array( 'al_layout_type' => array( 'equalHeight', 'slider' ) ),
				)
			);

			$this->add_responsive_control(
				'al_content_align_h',
				array(
					'label'     => esc_html__( 'Horizontal', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'end'    => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'center',
					'toggle'    => false,
					'selectors' => array(
						'{{WRAPPER}} .al-post__text-wrapper' => 'align-items: {{VALUE}};',
						'{{WRAPPER}} .buttons-wrapper' => 'justify-content: {{VALUE}};',
						'{{WRAPPER}} .al-post__content-title, {{WRAPPER}} .al-post__excerpt-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

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
			'al_article_content',
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
				'al_content_filter_display',
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
				'al_content_filter_align',
				array(
					'label'     => esc_html__( 'Filter alignment', 'eac-components' ),
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
										'name'     => 'al_content_filter_display',
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

			$this->add_control(
				'al_post_heading',
				array(
					'label'     => esc_html__( 'Post', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_content_image',
				array(
					'label'        => esc_html__( 'Featured image', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'al_content_title',
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
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'h5'  => 'H5',
						'h6'  => 'H6',
						'div' => 'div',
						'p'   => 'p',
					),
					'condition' => array( 'al_content_title' => 'yes' ),
				)
			);

			$this->add_control(
				'al_content_excerpt',
				array(
					'label'        => esc_html__( 'Excerpt', 'eac-components' ),
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
					'condition' => array( 'al_content_excerpt' => 'yes' ),
				)
			);

			$this->add_control(
				'al_meta_heading',
				array(
					'label'     => esc_html__( 'Meta tags', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_content_term',
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
				'al_content_author',
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
				'al_content_author_alert',
				array(
					'type'       => Controls_Manager::ALERT,
					'alert_type' => 'warning',
					'heading'    => esc_html__( 'Security Alert', 'eac-components' ),
					'content'    => esc_html__( 'It is not recommended to expose the name (nickname) of your users, as this can facilitate targeting by malicious individuals, increasing the risk of intrusion attempts.', 'eac-components' ),
					'condition'  => array( 'al_content_author' => 'yes' ),
				)
			);

			$this->add_control(
				'al_content_avatar',
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
				'al_content_date',
				array(
					'label'        => esc_html__( 'Date', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'al_content_comment',
				array(
					'label'        => esc_html__( 'Comments', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'al_links_heading',
				array(
					'label'     => esc_html__( 'Link', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_content_readmore',
				array(
					'label'        => esc_html__( 'Button', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'al_content_title_link',
				array(
					'label'        => esc_html__( 'Post link on title', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'al_content_title' => 'yes' ),
				)
			);

			$this->add_control(
				'al_image_link',
				array(
					'label'        => esc_html__( 'Post link on image', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'al_image_lightbox!'       => 'yes',
						'al_content_image'         => 'yes',
					),
				)
			);

			$this->add_control(
				'al_content_article_link',
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
								'name'     => 'al_content_readmore',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => 'al_content_title',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_content_title_link',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'relation' => 'and',
								'terms'    => array(
									array(
										'name'     => 'al_content_image',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_image_link',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				'al_image_lightbox',
				array(
					'label'        => esc_html__( 'Lightbox on image', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'al_image_link!'           => 'yes',
						'al_content_image'         => 'yes',
						'al_content_article_link!' => 'yes',
					),
				)
			);

			$this->add_control(
				'al_content_avatar_link',
				array(
					'label'        => esc_html__( 'Link on avatar', 'eac-components' ),
					'description'  => esc_html__( 'Author archives', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'al_content_avatar' => 'yes' ),
				)
			);

			$this->add_control(
				'al_readmore_settings',
				array(
					'label'     => esc_html__( 'Button', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_readmore' => 'yes' ),
					'separator' => 'before',
				)
			);

			// Trait du contenu du bouton read more
			$this->register_button_more_content_controls( array( 'control_condition' => array( 'al_content_readmore' => 'yes' ) ) );

			$this->add_control(
				'al_pagination_settings',
				array(
					'label'     => esc_html__( 'Button Pagination', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_pagging_display' => 'yes' ),
					'separator' => 'before',
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
					'default'     => esc_html__( 'More posts', 'eac-components' ),
					'condition'   => array( 'al_content_pagging_display' => 'yes' ),
				)
			);

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
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .swiper .swiper-slide, {{WRAPPER}} .al-posts__wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Articles */
			$this->add_control(
				'al_items_style',
				array(
					'label'     => esc_html__( 'Post', 'eac-components' ),
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
				'al_items_bg_color',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__inner-wrapper, {{WRAPPER}} .al-post__text-wrapper' => 'background-color: {{VALUE}};' ),
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
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
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
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item, {{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_filter_typography',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector'  => '{{WRAPPER}} .al-filters__wrapper .al-filters__item, {{WRAPPER}} .al-filters__wrapper .al-filters__item a',
					'condition' => array(
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
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
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
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
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
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
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'al_filter_border',
					'selector' => '{{WRAPPER}} .al-filters__wrapper .al-filters__item a',
					'condition' => array(
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
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
						'al_content_filter_display' => 'yes',
						'al_layout_type!'           => 'slider',
					),
				)
			);

			/** Image */
			$this->add_control(
				'al_image_style',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_image' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'al_image_border',
					'selector'  => '{{WRAPPER}} .al-post__image-wrapper img',
					'condition' => array( 'al_content_image' => 'yes' ),
				)
			);

			$this->add_control(
				'al_image_border_radius',
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
					'condition'          => array( 'al_content_image' => 'yes' ),
				)
			);

			/** Titre */
			$this->add_control(
				'al_title_style',
				array(
					'label'     => esc_html__( 'Title', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_title' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_titre_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__content-title a, {{WRAPPER}} .al-post__content-title' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_content_title' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_titre_typography',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector'  => '{{WRAPPER}} .al-post__content-title',
					'condition' => array( 'al_content_title' => 'yes' ),
				)
			);

			/** Résumé */
			$this->add_control(
				'al_excerpt_style',
				array(
					'label'     => esc_html__( 'Excerpt', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_excerpt' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_excerpt_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
					'selectors' => array(
						'{{WRAPPER}} .al-post__excerpt-wrapper' => 'color: {{VALUE}};',
					),
					'condition' => array( 'al_content_excerpt' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_excerpt_typography',
					'label'     => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
					'selector'  => '{{WRAPPER}} .al-post__excerpt-wrapper',
					'condition' => array( 'al_content_excerpt' => 'yes' ),
				)
			);

			$this->add_control(
				'al_readmore_style',
				array(
					'label'     => esc_html__( 'Button', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_readmore' => 'yes' ),
					'separator' => 'before',
				)
			);

			// Trait Style du bouton read more
			$this->register_button_more_style_controls( array( 'control_condition' => array( 'al_content_readmore' => 'yes' ) ) );

			$this->add_control(
				'al_avatar_style',
				array(
					'label'     => esc_html( 'Avatar' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_avatar' => 'yes' ),
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
					'condition'   => array( 'al_content_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'al_avatar_image_border',
					'selector'       => '{{WRAPPER}} .al-post__avatar-wrapper',
					'condition'      => array( 'al_content_avatar' => 'yes' ),
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
					'condition'          => array( 'al_content_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'al_avatar_box_shadow',
					'label'     => esc_html__( 'Shadow', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .al-post__avatar-wrapper',
					'condition' => array( 'al_content_avatar' => 'yes' ),
				)
			);

			/** Pictogrammes */
			$this->add_control(
				'al_icone_style',
				array(
					'label'      => esc_html__( 'Meta tags', 'eac-components' ),
					'type'       => Controls_Manager::HEADING,
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_content_author',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_date',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_comment',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_term',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
					'separator'  => 'before',
				)
			);

			/** Meta tags */
			$this->add_control(
				'al_icone_color',
				array(
					'label'      => esc_html__( 'Color', 'eac-components' ),
					'type'       => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date,
						{{WRAPPER}} .al-post__meta-comment' => 'color: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_content_author',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_date',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_comment',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_term',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'       => 'al_icone_typography',
					'label'      => esc_html__( 'Typography', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'   => '{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date,
						{{WRAPPER}} .al-post__meta-comment',
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_content_author',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_date',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_comment',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_content_term',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
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
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$raw_data = $this->get_raw_data();

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
		$this->render_posts();
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
	protected function render_posts(): void {
		$settings = $this->get_settings_for_display();
		$has_swiper = 'slider' === $settings['al_layout_type'] ? true : false;
		$has_title          = 'yes' === $settings['al_content_title'] ? true : false;
		$has_title_link     = $has_title && 'yes' === $settings['al_content_title_link'] ? true : false;
		$has_image          = 'yes' === $settings['al_content_image'] ? true : false;
		$lazy_load          = $has_image && 'yes' === $settings['al_enable_image_lazy'] ? 'lazy' : 'eager';
		$has_avatar         = 'yes' === $settings['al_content_avatar'] ? true : false;
		$has_avatar_link    = $has_avatar && 'yes' === $settings['al_content_avatar_link'] ? true : false;
		$avatar_size        = ! empty( $settings['al_avatar_size'] ) ? $settings['al_avatar_size'] : 60;
		$has_image_lightbox = ! $has_swiper && 'yes' === $settings['al_image_lightbox'] ? true : false;
		$has_image_link     = ! $has_image_lightbox && 'yes' === $settings['al_image_link'] ? true : false;
		$has_term           = 'yes' === $settings['al_content_term'] ? true : false;
		$has_auteur         = 'yes' === $settings['al_content_author'] ? true : false;
		$has_date           = 'yes' === $settings['al_content_date'] ? true : false;
		$has_resum          = 'yes' === $settings['al_content_excerpt'] ? true : false;
		$has_readmore       = 'yes' === $settings['al_content_readmore'] ? true : false;
		$has_readmore_picto = $has_readmore && 'yes' === $settings['button_add_more_picto'] ? true : false;
		$has_comment        = 'yes' === $settings['al_content_comment'] ? true : false;
		$has_global_link    = isset( $settings['al_content_article_link'] ) && 'yes' === $settings['al_content_article_link'] ? true : false;
		$max_num_pages      = 0;

		// Filtre Users. Champ TEXT
		$has_users    = ! empty( $settings['al_content_user'] ) ? true : false;
		$user_filters = esc_html( $settings['al_content_user'] );

		// Filtre Taxonomie. Champ SELECT2
		$has_filters = ! $has_swiper && isset( $settings['al_content_filter_display'] ) && 'yes' === $settings['al_content_filter_display'] ? true : false;

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

		if ( $has_swiper ) {
			$class = 'al-posts__wrapper swiper-wrapper';
		} else {
			$class = sprintf( 'al-posts__wrapper layout-type-%s', $layout );
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
		if ( 'yes' === $settings['al_display_content_args'] && Plugin::$instance->editor->is_edit_mode() ) { ?>
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

					if ( $has_users ) {
						$user                = get_the_author_meta( 'display_name' );
						$terms_slug[ $user ] = sanitize_title( $user );
						$terms_name[ $user ] = ucfirst( $user );
					} elseif ( ! empty( $taxonomy_filter ) ) {
						$terms_array = array();
						foreach ( $taxonomy_filter as $post_term ) {
							$terms_array = wp_get_post_terms( get_the_ID(), $post_term );
							if ( ! is_wp_error( $terms_array ) && ! empty( $terms_array ) ) {
								foreach ( $terms_array as $term ) {
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

					/**
					 * Ajout de l'ID Elementor du widget et de la liste des slugs dans la class pour gérer les filtres et le pagging.
					 * Voir eac-post-grid.js:selectedItems
					 */
					if ( ! $has_swiper ) {
						$post_class = sprintf( '%1$s %2$s %3$s %4$s', $widget_id, 'al-post__wrapper', implode( ' ', array_map( 'esc_attr', $terms_slug ) ), implode( ' ', get_post_class( '', get_the_ID() ) ) );
					} else {
						$post_class = sprintf( '%1$s %2$s %3$s', $widget_id, 'al-post__wrapper swiper-slide', implode( ' ', get_post_class( '', get_the_ID() ) ) );
					}
					$permalink  = get_permalink( get_the_ID() );
					$size_image = isset( $settings['al_image_dimension'] ) ? $settings['al_image_dimension'] : 'medium';
					$attachment = Eac_Tools_Util::wp_get_attachment_data( get_post_thumbnail_id( get_the_ID() ), $size_image );
					?>
					<article id="<?php echo 'post-' . esc_attr( get_the_ID() ); ?>" class="<?php echo esc_attr( $post_class ); ?>">
						<div class='al-post__inner-wrapper'>
							<?php if ( $has_image && has_post_thumbnail() && ! empty( $attachment ) ) : ?>
								<div class='al-post__image-wrapper'>
									<?php
									$this->add_render_attribute(
										'post_image',
										array(
											'class'   => 'eac-accessible-img al-post__image-loaded',
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
									if ( $has_image_lightbox ) : ?>
										<a class='eac-accessible-link' href="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" data-elementor-open-lightbox='no' data-fancybox="al-gallery-<?php echo esc_attr( $widget_id ); ?>" data-caption="<?php echo esc_html( get_the_title() ); ?>" role='button' aria-haspopup='dialog' aria-expanded='false' aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'View image', 'eac-components' ), esc_html( get_the_title() ) ); ?>">
									<?php endif; ?>
									<?php if ( $has_image_link && $permalink ) :
										$class_link = $has_global_link ? 'eac-accessible-link card-link' : 'eac-accessible-link'; ?>
										<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Read post', 'eac-components' ), esc_html( get_the_title() ) ); ?>">
									<?php endif; ?>
										<img <?php $this->print_render_attribute_string( 'post_image' ); ?>>
									<?php if ( $has_image_lightbox || ( $has_image_link && $permalink ) ) : ?>
										</a>
									<?php endif;
									?>
								</div>
							<?php endif; ?>

							<?php if ( $has_title || $has_resum || $has_readmore ) : ?>
								<div class='al-post__text-wrapper'>
									<!-- Le titre -->
									<?php if ( $has_title ) :
										$class_link = $has_global_link ? 'eac-accessible-link card-link' : 'eac-accessible-link';
										?>
										<!-- Affiche les IDs -->
										<?php if ( $has_id && $has_title_link && $permalink ) : ?>
											<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $permalink ); ?>">
												<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s : %3$s</%1$s>', esc_attr( $title_tag ), esc_attr( get_the_ID() ), esc_html( get_the_title() ) ); ?>
											</a>
										<?php elseif ( $has_id && ! $has_title_link ) : ?>
											<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s : %3$s</%1$s>', esc_attr( $title_tag ), esc_attr( get_the_ID() ), esc_html( get_the_title() ) ); ?>
										<?php elseif ( ! $has_id && $has_title_link && $permalink ) : ?>
											<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php printf( '%1$s - %2$s', esc_html__( 'Read post', 'eac-components' ), esc_html( get_the_title() ) ); ?>">
												<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s</%1$s>', esc_attr( $title_tag ), esc_html( get_the_title() ) ); ?>
											</a>
										<?php else : ?>
											<?php printf( '<%1$s class="al-post__content-title global__line-height">%2$s</%1$s>', esc_attr( $title_tag ), esc_html( get_the_title() ) ); ?>
										<?php endif; ?>
									<?php endif; ?>

									<!-- Le résumé de l'article. fonction dans helper.php -->
									<?php if ( $has_resum ) : ?>
										<div class='al-post__excerpt-wrapper global__line-height'>
											<span dir='ltr'><?php echo esc_html( Eac_Tools_Util::get_post_excerpt( get_the_ID(), absint( $settings['al_excerpt_length'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
										</div>
									<?php endif; ?>

									<!-- Le bouton pour ouvrir l'article/page -->
									<?php
									if ( $has_readmore && $permalink ) { ?>
										<div class='buttons-wrapper'>
											<?php $this->render_button_more(
												array(
													'permalink'     => $permalink,
													'item_title'    => get_the_title(),
													'global_link'   => $has_global_link,
													'default_label' => esc_html__( 'Read post', 'eac-components' ),
												)
											); ?>
										</div>
									<?php } ?>

									<?php if ( $has_avatar || $has_term || $has_auteur || $has_date || $has_comment ) : ?>
										<div class='al-post__meta-wrapper'>
											<?php if ( $has_avatar ) :
												$author_url      = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => absint( $avatar_size ) ) );
												$author_archives = get_author_posts_url( get_the_author_meta( 'ID' ) );
												$author_name     = get_the_author_meta( 'display_name' );
												?>
												<div class='al-post__avatar-wrapper'>
													<?php if ( $has_avatar_link ) : ?>
														<a href="<?php echo esc_url( $author_archives ); ?>" class='eac-accessible-link avatar-link' aria-label="<?php printf( '%1$s %2$s', esc_attr__( 'View posts from', 'eac-components' ), esc_attr( $author_name ) ); ?>">
													<?php endif; ?>
														<img class='avatar photo' src="<?php echo esc_url( $author_url ); ?>" alt="Avatar of <?php echo esc_attr( $author_name ); ?>" loading='lazy' width="<?php echo absint( $avatar_size ); ?>" height="<?php echo absint( $avatar_size ); ?>" />
													<?php if ( $has_avatar_link ) : ?>
														</a>
													<?php endif; ?>
												</div>
											<?php endif;

											if ( $has_term || $has_auteur || $has_date || $has_comment ) : ?>
												<div class='al-post__meta'>
													<!-- Les étiquettes -->
													<?php if ( $has_term ) :
														$terms = $this->get_svg_icon_terms() . '<span>' . implode( '|', array_map( 'esc_html', $terms_name ) ) . '</span>'; ?>
														<div class='al-post__meta-tags eac-icon-svg'>
															<?php echo wp_kses_post( $terms ); ?>
														</div>
													<?php endif; ?>

													<!-- L'auteur de l'article -->
													<?php if ( $has_auteur ) :
														$auteur = $this->get_svg_icon_user() . '<span>' . esc_html( get_the_author_meta( 'display_name' ) ) . '</span>'; ?>
														<div class='al-post__meta-author eac-icon-svg'>
															<?php echo wp_kses_post( $auteur ); ?>
														</div>
													<?php endif; ?>

													<!-- Le date de création ou de dernière modification -->
													<?php if ( $has_date ) :
														if ( 'modified' === $settings['al_article_orderby'] ) :
															$calendar = $this->get_svg_icon_calendar() . '<span>' . date_i18n( get_option( 'date_format' ), strtotime( get_the_modified_date( 'Y-m-d' ) ) ) . '</span>';
														else :
															$calendar = $this->get_svg_icon_calendar() . '<span>' . date_i18n( get_option( 'date_format' ), strtotime( get_the_date( 'Y-m-d' ) ) ) . '</span>';
														endif; ?>
														<div class='al-post__meta-date eac-icon-svg'>
															<?php echo wp_kses_post( $calendar ); ?>
														</div>
													<?php endif; ?>

													<!-- Le nombre de commentaire -->
													<?php if ( $has_comment ) :
														$comments = $this->get_svg_icon_comments() . '<span>' . absint( get_comments_number() ) . '</span>'; ?>
														<div class='al-post__meta-comment eac-icon-svg'>
															<?php echo wp_kses_post( $comments ); ?>
														</div>
													<?php endif; ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div> <!-- Fin al-post__inner-wrapper -->
					</article>
					<?php
					$this->remove_render_attribute( 'post_image' );
				} ?>
			</div>
			<?php if ( $has_pagging && $the_query->post_count < $the_query->found_posts ) :
				$this->add_render_attribute( 'read_button', 'class', 'eac-accessible-link button__readmore-wrapper' );
				$this->add_render_attribute( 'read_button', 'type', 'button' );
				$this->add_render_attribute( 'read_button', 'aria-label', esc_attr__( 'Pagination load next page', 'eac-components' ) );

				$this->add_inline_editing_attributes( 'al_pagination_label', 'none' );
				$this->add_render_attribute( 'al_pagination_label', 'class', 'label-icon' );

				$label        = esc_html__( 'View more post', 'eac-components' );
				$label_button = sprintf( '<span %1$s>%2$s</span><span class="al-more-button-paged">%3$s/%4$s</span>', $this->get_render_attribute_string( 'al_pagination_label' ), $label, $the_query->post_count, $the_query->found_posts );
				$button       = sprintf( '<button %1$s>%2$s</button>', $this->get_render_attribute_string( 'read_button' ), $label_button );
				?>
				<div class='al-post__pagination' id="<?php echo esc_attr( $pagination_id ); ?>">
					<div class='buttons-wrapper'><?php echo wp_kses_post( $button ); ?></div>
					<div class='al-page-load-status'>
						<div class='infinite-scroll-request eac__loader-spin'></div>
						<p class='infinite-scroll-last'><?php esc_html_e( 'View more post', 'eac-components' ); ?></p>
						<p class='infinite-scroll-error'><?php esc_html_e( 'No more post', 'eac-components' ); ?></p>
					</div>
				</div>
			<?php elseif ( $has_navigate ) :
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
				if ( $page_links ) : ?>
					<nav class='al-post__navigation' id="<?php echo esc_attr( $navigation_id ); ?>" aria-label="<?php esc_attr_e( 'Paging', 'eac-components' ); ?>" itemprop='pagination'>
						<div class='al-post__navigation-digit'><?php echo wp_kses_post( $page_links ); ?></div>
					</nav>
				<?php endif;
			endif;

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
			'data_article'             => $unique_id,
			'data_filtre'              => ! $has_swiper && 'yes' === $settings['al_content_filter_display'] ? true : false,
			'data_fancybox'            => 'yes' === $settings['al_image_lightbox'] ? true : false,
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
			'data_lazy'                => 'yes' === $settings['al_content_image'] && 'yes' === $settings['al_enable_image_lazy'] ? true : false,
		);

		return wp_json_encode( $module_settings );
	}

	protected function content_template(): void {}
}
