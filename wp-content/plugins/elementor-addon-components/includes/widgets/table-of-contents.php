<?php
/**
 * Class: Table_Of_Content_Widget
 * Name: Table des matières
 * Slug: eac-addon-toc
 *
 * Description: Génère et formate automatiquement une Table des matières
 *
 * @since 1.8.0
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Plugin;
use Elementor\Utils;

class Table_Of_Contents_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'table-content';

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
		return array( 'eac-table-content' );
	}

	/**
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return array CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-table-content' );
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
	 * Get Frontend Settings
	 *
	 * In the TOC widget, this implementation is used to pass a pre-rendered version of the icon to the front end,
	 * which is required in case the FontAwesome SVG experiment is active.
	 *
	 * @since 2.3.2
	 *
	 * @return array
	 */
	public function get_frontend_settings(): array {
		$frontend_settings = parent::get_frontend_settings();

		if ( Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' ) && ! empty( $frontend_settings['toc_content_picto']['value'] ) ) {
			$frontend_settings['toc_content_picto']['rendered_tag'] = Icons_Manager::render_font_icon( $frontend_settings['toc_content_picto'] );
		}
		return $frontend_settings;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls(): void {

		/**
		 * Generale Content Section
		 */
		$this->start_controls_section(
			'toc_content_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'toc_header_title',
				array(
					'label'       => esc_html__( 'Title', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Table of Contents', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'toc_header_title_tag',
				array(
					'label'   => esc_html__( 'Tag', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'options' => array(
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
						'span'  => array(
							'title' => esc_html__( 'Paragraph', 'eac-components' ),
							'icon'  => 'eicon-editor-paragraph',
						),
					),
					'default' => 'h2',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'toc_content_target',
				array(
					'label'       => esc_html__( 'Target', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'description' => esc_html__( 'Target of analysis', 'eac-components' ),
					'options'     => array(
						'body'                   => 'Body',
						'.site-content'          => 'Site content',
						'.site-main'             => 'Site main',
						'.entry-content'         => 'Entry content',
						'.page-content'          => 'Page content',
						'.entry-content article' => 'Entry article',
						'.site-main article'     => 'Page article',
						'custom'                 => esc_html__( 'Custom', 'eac-components' ),
					),
					'label_block' => true,
					'default'     => 'body',
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'toc_content_target_custom',
				array(
					'label'       => esc_html__( 'Target class', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'description' => esc_html__( 'Add your custom class WITHOUT the dot. e.g. my-class', 'eac-components' ),
					'dynamic'     => array( 'active' => false ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'condition'   => array( 'toc_content_target' => 'custom' ),
					'render_type' => 'ui',
					'separator'   => 'after',
				)
			);

			$this->start_controls_tabs( 'toc_content_include_exclude' );

				$this->start_controls_tab(
					'toc_content_heading_include',
					array(
						'label' => esc_html__( 'Include', 'eac-components' ),
					)
				);

					$this->add_control(
						'toc_content_heading',
						array(
							'label'       => esc_html__( 'Analysis title tags', 'eac-components' ),
							'type'        => Controls_Manager::SELECT2,
							'options'     => array(
								'h1' => 'H1',
								'h2' => 'H2',
								'h3' => 'H3',
								'h4' => 'H4',
								'h5' => 'H5',
								'h6' => 'H6',
							),
							'label_block' => true,
							'default'     => array( 'h2', 'h3', 'h4' ),
							'multiple'    => true,
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'toc_content_heading_exclude',
					array(
						'label' => esc_html__( 'Exclude', 'eac-components' ),
					)
				);

					$this->add_control(
						'toc_header_exclude',
						array(
							'label'       => esc_html__( 'Exclude titles', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'description' => esc_html__( 'CSS class for titles without dot, separated by a comma', 'eac-components' ),
							'dynamic'     => array( 'active' => true ),
							'ai'          => array( 'active' => false ),
							'label_block' => true,
							'render_type' => 'ui',
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'toc_content_anchor_trailer',
				array(
					'label'        => esc_html__( 'Add rank number', 'eac-components' ),
					'description'  => esc_html__( 'If titles are not unique on the page', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'toc_content_content',
			array(
				'label' => esc_html__( 'Content', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'toc_content_toggle',
				array(
					'label'   => esc_html__( 'Collapse content on load', 'eac-components' ),
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
					'default' => 'yes',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'toc_content_block',
				array(
					'label'        => esc_html__( 'Display block', 'eac-components' ),
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
					'prefix_class' => 'toctoc-block-',
				)
			);

			$this->add_control(
				'toc_content_word_wrap',
				array(
					'label'        => esc_html__( 'Word wrap', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'nowrap'   => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'wrap' => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'      => 'nowrap',
					'toggle'       => false,
					'render_type'  => 'template',
					'prefix_class' => 'toctoc-word-',
				)
			);

			$this->add_responsive_control(
				'toc_content_width',
				array(
					'label'       => esc_html__( 'Width', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%' ),
					'default'     => array(
						'unit' => '%',
						'size' => 40,
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'       => array(
						'px' => array(
							'min'  => 200,
							'max'  => 1000,
							'step' => 10,
						),
						'%' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 1,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} #toctoc' => 'inline-size: {{SIZE}}{{UNIT}};' ),
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'toc_content_align',
				array(
					'label'   => esc_html__( 'Alignment', 'eac-components' ),
					'type'    => Controls_Manager::CHOOSE,
					'default' => 'center',
					'options' => array(
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
					'selectors_dictionary' => array(
						'left'   => '0 auto',
						'center' => 'auto',
						'right'  => 'auto 0',
					),
					'selectors'            => array( '{{WRAPPER}} #toctoc' => 'margin-inline: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'toc_content_list',
				array(
					'label'       => esc_html__( 'Marker view', 'eac-components' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => array(
						'none'     => esc_html__( 'Default', 'eac-components' ),
						'decimal'  => esc_html__( 'Numeric', 'eac-components' ),
						'numbered' => esc_html__( 'Hierarchical', 'eac-components' ),
						'disc'     => esc_html__( 'Bullet', 'eac-components' ),
						'picto'    => esc_html__( 'Pictogram', 'eac-components' ),
					),
					'label_block' => true,
					'default'     => 'picto',
				)
			);

			$this->add_control(
				'toc_content_picto',
				array(
					'label'    => esc_html__( 'Pictogram', 'eac-components' ),
					'type'     => Controls_Manager::ICONS,
					'default'  => array(
						'value'   => 'fas fa-arrow-down',
						'library' => 'fa-solid',
					),
					'skin'               => 'inline',
					'frontend_available' => true,
					'condition'          => array( 'toc_content_list' => 'picto' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'toc_general_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'toc_header_style',
				array(
					'label'     => esc_html__( 'TOC Header', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'toc_header_color',
				array(
					'label'     => esc_html__( 'Title color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} #toctoc #toctoc-head,
						{{WRAPPER}} #toctoc #toctoc-head .toctoc-title,
						{{WRAPPER}} #toctoc #toctoc-head span' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'tox_header_typography',
					'label'    => esc_html__( 'Title typography', 'eac-components' ),
					'selector' => '{{WRAPPER}} #toctoc #toctoc-head, {{WRAPPER}} #toctoc #toctoc-head .toctoc-title, {{WRAPPER}} #toctoc #toctoc-head span',
				)
			);

			$this->add_control(
				'toc_header_background_color',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} #toctoc #toctoc-head' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'toc_header_padding',
				array(
					'label'     => esc_html__( 'Padding', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} #toctoc #toctoc-head' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'toc_header_border',
					'fields_options' => array(
						'border' => array( 'default' => '' ),
					),
					'selector'       => '{{WRAPPER}} #toctoc #toctoc-head',
				)
			);

			$this->add_control(
				'toc_body_style',
				array(
					'label'     => esc_html__( 'TOC Content', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'toc_body_color',
				array(
					'label'     => esc_html__( 'Entries color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} #toctoc #toctoc-body__list li,
						{{WRAPPER}} #toctoc #toctoc-body__list .link i,
						{{WRAPPER}} #toctoc #toctoc-body__list li a,
						{{WRAPPER}} #toctoc #toctoc-body__list li a span' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'tox_body_typography',
					'label'    => esc_html__( 'Entries typography', 'eac-components' ),
					'selector' => '{{WRAPPER}} #toctoc #toctoc-body__list li,
						{{WRAPPER}} #toctoc #toctoc-body__list .link i,
						{{WRAPPER}} #toctoc #toctoc-body__list li a,
						{{WRAPPER}} #toctoc #toctoc-body__list li a span',
				)
			);

			$this->add_control(
				'toc_body_background_color',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} #toctoc #toctoc-body__list' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'toc_body_border',
					'fields_options' => array(
						'border' => array( 'default' => '' ),
					),
					'selector'       => '{{WRAPPER}} #toctoc #toctoc-body__list',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'toc_body_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} #toctoc',
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
		$expanded = 'yes' === $settings['toc_content_toggle'] ? 'false' : 'true';

		$this->add_render_attribute( 'wrapper', 'class', 'eac-table-of-content' );
		$this->add_render_attribute( 'wrapper', 'data-settings', $this->get_settings_json() );

		$this->add_inline_editing_attributes( 'toc_header_title', 'none' );
		$this->add_render_attribute( 'toc_header_title', 'id', 'toctoc-title' );
		$this->add_render_attribute( 'toc_header_title', 'class', 'toctoc-title' );
		$title_tag = Utils::validate_html_tag( $settings['toc_header_title_tag'] );

		$this->add_render_attribute(
			'toctoc_head',
			array(
				'id'            => 'toctoc-head',
				'class'         => 'toctoc-head',
				'role'          => 'button',
				'aria-expanded' => esc_attr( $expanded ),
				'aria-controls' => 'toctoc-body__list',
				'aria-label'    => sprintf( '%1$s - %2$s', esc_attr__( 'Open/Close', 'eac-components' ), esc_attr( $settings['toc_header_title'] ) ),
				'tabindex'      => '0',
			)
		);
		ob_start();
		Icons_Manager::render_icon(
			array(
				'value'   => 'fas fa-chevron-down',
				'library' => 'fa-solid',
			),
			array( 'aria-hidden' => 'true' ),
		);
		$icon = ob_get_clean();
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<nav id='toctoc' class='toctoc' aria-labelledby='toctoc-title'>
				<div <?php $this->print_render_attribute_string( 'toctoc_head' ); ?>>
					<?php printf( '<%1$s %2$s>%3$s</%1$s>', esc_attr( $title_tag ), $this->get_render_attribute_string( 'toc_header_title' ), esc_attr( $settings['toc_header_title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span id='toctoc-head__toggler' class='toctoc-head__toggler eac-icon-svg'><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</div>
				<ol id='toctoc-body__list' class='toctoc-body__list'></ol>
			</nav>
		</div>
		<?php
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 *
	 * @uses wp_json_encode()
	 *
	 * @return JSON oject
	 *
	 * @access protected
	 */
	protected function get_settings_json(): string {
		$module_settings = $this->get_settings_for_display();
		$target          = 'body';
		if ( 'custom' === $module_settings['toc_content_target'] && ! empty( $module_settings['toc_content_target_custom'] ) ) {
			$target = '.' . esc_html( $module_settings['toc_content_target_custom'] );
		} elseif ( 'custom' !== $module_settings['toc_content_target'] ) {
			$target = esc_html( $module_settings['toc_content_target'] );
		}
		$exclude = ! empty( $module_settings['toc_header_exclude'] ) ? array_map( 'trim', array_map( 'esc_html', explode( ',', $module_settings['toc_header_exclude'] ) ) ) : array();

		$settings = array(
			'data_opened'   => 'yes' === $module_settings['toc_content_toggle'] ? false : true,
			'data_target'   => $target,
			'data_type'     => $module_settings['toc_content_list'],
			'data_headings' => ! empty( $module_settings['toc_content_heading'] ) ? implode( ',', $module_settings['toc_content_heading'] ) : 'h2',
			'data_trailer'  => 'yes' === $module_settings['toc_content_anchor_trailer'] ? true : false,
			'data_label'    => esc_html__( 'Jump to', 'eac-components' ) . ' ',
			'data_exclude'  => $exclude,
		);

		return wp_json_encode( $settings );
	}

	protected function content_template(): void {}
}
