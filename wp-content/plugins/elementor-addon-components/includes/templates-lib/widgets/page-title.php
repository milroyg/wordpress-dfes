<?php
/**
 * Class: Page_Title_Widget
 * Name: Page title
 * Slug: eac-addon-page-title
 *
 * Description: Création et affichage du titre de la page courante
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Utils;

class Page_Title_Widget extends Widget_Base {
	use \EACCustomWidgets\Includes\Traits\Page_Title_Trait;

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'page-title';

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
	 * @return widget category.
	 */
	public function get_categories(): array {
		return Eac_Load_Config::get_widget_categories( $this->slug );
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

		$this->start_controls_section(
			'page_title_settings_fields',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'page_title_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( esc_html__( 'The title of an archive page is only visible on the frontend.', 'eac-components' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);

			$this->add_control(
				'page_title_tag',
				array(
					'label'   => esc_html__( 'Tag', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'h1' => array(
							'title' => 'H1',
							'icon'  => 'eicon-editor-h1',
						),
						'h2' => array(
							'title' => 'H2',
							'icon'  => 'eicon-editor-h2',
						),
						'h3' => array(
							'title' => 'H3',
							'icon'  => 'eicon-editor-h3',
						),
						'h4' => array(
							'title' => 'H4',
							'icon'  => 'eicon-editor-h4',
						),
						'h5' => array(
							'title' => 'H5',
							'icon'  => 'eicon-editor-h5',
						),
						'h6' => array(
							'title' => 'H6',
							'icon'  => 'eicon-editor-h6',
						),
						'p'  => array(
							'title' => esc_html__( 'Paragraph', 'eac-components' ),
							'icon'  => 'eicon-editor-paragraph',
						),
					),
					'default' => 'h2',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'page_title_context',
				array(
					'label'   => esc_html__( 'Include context', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default' => 'no',
				)
			);

			$this->add_control(
				'page_title_type_link',
				array(
					'label'       => esc_html__( 'Link type', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Default Site URL', 'eac-components' ),
					'options'     => array(
						'none'    => esc_html__( 'None', 'eac-components' ),
						'default' => esc_html__( 'Default', 'eac-components' ),
						'custom'  => esc_html( 'URL' ),
					),
					'default'     => 'none',
				)
			);

			$this->add_control(
				'page_title_link',
				array(
					'label'        => esc_html( 'URL' ),
					'type'         => Controls_Manager::URL,
					'placeholder'  => esc_html__( 'Type or paste your URL', 'eac-components' ),
					'dynamic'      => array(
						'active' => true,
					),
					'default'      => array(
						'url' => get_home_url(),
					),
					'autocomplete' => true,
					'condition'    => array(
						'page_title_type_link' => 'custom',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'page_title_style',
			array(
				'label' => esc_html__( 'Title', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'page_title_alignment',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-text-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-text-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'  => 'start',
						'right' => 'end',
					),
					'default'   => 'left',
					'selectors' => array(
						'{{WRAPPER}} .eac-page-title-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'page_title_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array(
						'default' => Global_Colors::COLOR_TEXT,
					),
					'default'   => '#000000',
					'selectors' => array(
						'{{WRAPPER}} .elementor-heading-title,
                        {{WRAPPER}} .eac-page-title a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'page_title_typography',
					'label'          => esc_html__( 'Typography', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size' => array(
							'default' => array(
								'unit' => 'px',
								'size' => 30,
							),
						),
					),
					'selector'       => '{{WRAPPER}} .elementor-heading-title, {{WRAPPER}} .eac-page-title a',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Stroke::get_type(),
				array(
					'name'     => 'page_title_stroke',
					'label'    => esc_html__( 'Text stroke', 'eac-components' ),
					'selector' => '{{WRAPPER}} .elementor-heading-title',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name'     => 'page_title_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .elementor-heading-title',
				)
			);

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
		$page_title_tag = ! empty( $settings['page_title_tag'] ) ? Utils::validate_html_tag( $settings['page_title_tag'] ) : 'div';
		$head_type_link = $settings['page_title_type_link'];
		$head_link_url  = false;
		$has_context    = 'yes' === $settings['page_title_context'] ? true : false;
		$title          = $this->get_page_title( $has_context );

		if ( 'custom' === $head_type_link && ! empty( $settings['page_title_link']['url'] ) ) {
			$head_link_url = true;
			$this->add_link_attributes( 'pt_link_to', $settings['page_title_link'] );
			$this->add_render_attribute( 'pt_link_to', 'title', esc_attr( $title ) );
			$this->add_render_attribute( 'pt_link_to', 'aria-label', esc_attr__( 'Page title', 'eac-components' ) );

			if ( $settings['page_title_link']['is_external'] ) {
				$this->add_render_attribute( 'pt_link_to', 'rel', 'noopener noreferrer' );
			}
		}
		?>		
		<div class='eac-page-title-wrapper elementor-widget-heading' itemprop='headline'>
			<?php if ( $head_link_url ) { ?>
				<<?php echo esc_attr( $page_title_tag ); ?> class='eac-page-title elementor-heading-title'>
				<a <?php $this->print_render_attribute_string( 'pt_link_to' ); ?>><?php echo esc_html( $title ); ?></a>
				</<?php echo esc_attr( $page_title_tag ); ?>>
			<?php } elseif ( 'default' === $head_type_link ) { ?>
				<<?php echo esc_attr( $page_title_tag ); ?> class='eac-page-title elementor-heading-title'>
				<a href="<?php echo esc_url( get_home_url() ); ?>" rel='home' itemprop='url'><?php echo esc_html( $title ); ?></a>
				</<?php echo esc_attr( $page_title_tag ); ?>>
			<?php } else { ?>
				<<?php echo esc_attr( $page_title_tag ); ?> class='eac-page-title elementor-heading-title'><?php echo esc_html( $title ); ?></<?php echo esc_attr( $page_title_tag ); ?>>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render page title output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template(): void {
		$home_url = get_home_url();
		if ( is_archive() || is_home() ) {
			$title = get_the_archive_title();
		} else {
			$title = get_the_title();
		}
		?>
		<#
		if ( '' != settings.page_title_link.url ) {
			view.addRenderAttribute( 'url', 'href', settings.page_title_link.url );
		}
		var pageTitleTag = settings.page_title_tag;
		if ( typeof elementor.helpers.validateHTMLTag === 'function' ) { 
			pageTitleTag = elementor.helpers.validateHTMLTag( pageTitleTag );
		}
		#>
		<div class='eac-page-title-wrapper elementor-widget-heading' itemprop='headline'>
			<# if ( 'custom' === settings.page_title_type_link && '' !== settings.page_title_link.url ) { #>
				<a {{{ view.getRenderAttributeString( 'url' ) }}} >
			<# } else if ( 'default' === settings.page_title_type_link ) { #>
				<a href="<?php echo esc_url( $home_url ); ?>">
			<# } #>
			<{{{ pageTitleTag }}} class='eac-page-title elementor-heading-title'>		
				<?php echo esc_html( $title ); ?>
			</{{{ pageTitleTag }}}>
			<# if ( 'default' === settings.page_title_type_link || ( 'custom' === settings.page_title_type_link && '' !== settings.page_title_link.url ) ) { #>
				</a>
			<# } #>			
		</div>
		<?php
	}
}
