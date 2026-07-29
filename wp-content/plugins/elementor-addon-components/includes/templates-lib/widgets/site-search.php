<?php
/**
 * Class: Site_Search_Widget
 * Name: Rechercher
 * Slug: eac-addon-site-search
 *
 * Description: Affichage du formulaire de recherche
 *
 * @since 2.1.0
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Site_Search_Widget extends Widget_Base {
	use \EACCustomWidgets\Includes\Traits\Icon_Svg_Trait;

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'site-search';

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
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return array libraries list.
	 */
	public function get_script_depends(): array {
		return array( 'eac-site-search' );
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

		$this->start_controls_section(
			'site_search_settings_fields',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ss_content_placeholder',
				array(
					'label'   => esc_html__( 'Placeholder', 'eac-components' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'At least 3 characters', 'eac-components' ),
					'dynamic' => array( 'active' => true ),
				)
			);

			$this->add_control(
				'ss_autocomplete',
				array(
					'label'     => esc_html__( 'Enable autocomplete', 'eac-components' ),
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
				)
			);

			$this->add_control(
				'ss_button_hidden',
				array(
					'label'     => esc_html__( 'Hide button', 'eac-components' ),
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
					'default'   => 'yes',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'ss_button_position',
				array(
					'label'     => esc_html__( 'Button position', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'left',
					'toggle'    => false,
					'condition' => array( 'ss_button_hidden' => 'no' ),
				)
			);

			$this->add_control(
				'ss_button_icon',
				array(
					'label'       => esc_html__( 'Icon', 'eac-components' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => 'true',
					'default'     => array(
						'value'   => 'fas fa-search',
						'library' => 'fa-solid',
					),
					'skin'        => 'inline',
					'condition'   => array( 'ss_button_hidden' => 'no' ),
				)
			);

			$this->add_responsive_control(
				'ss_content_width',
				array(
					'label'          => esc_html__( 'Width (%)', 'eac-components' ),
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
						'size' => 100,
						'unit' => '%',
					),
					'range'          => array(
						'px' => array(
							'min'  => 10,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'      => array(
						'{{WRAPPER}} .eac-search_form-wrapper' => 'inline-size: {{SIZE}}%;',
					),
					'separator'      => 'before',
				)
			);

			$this->add_responsive_control(
				'ss_button_icon_align',
				array(
					'label'                => esc_html__( 'Alignment', 'eac-components' ),
					'type'                 => Controls_Manager::CHOOSE,
					'default'              => 'center',
					'options'              => array(
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
					'selectors'            => array(
						'{{WRAPPER}} .eac-search_form-wrapper,
						{{WRAPPER}} .eac-search_form-wrapper button[type="button"].eac-search_button-toggle' => 'margin-block: 0; margin-inline: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'ss_text_field_style',
			array(
				'label' => esc_html__( 'Input field', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ss_text_field_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .eac-search_form-input' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ss_text_field_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .eac-search_form-input',
				)
			);

			$this->add_control(
				'ss_text_field_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_form-input' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_text_field_icon_color',
				array(
					'label'     => esc_html__( 'Icon color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .eac-search_form-container svg' => 'fill: {{VALUE}};' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ss_button_style',
			array(
				'label'     => esc_html__( 'Button', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'ss_button_hidden' => 'no' ),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ss_style_button_typo',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .eac-search_button-toggle',
				)
			);

			$this->add_control(
				'ss_style_button_color',
				array(
					'label'     => esc_html__( 'Icon color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_style_button_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'ss_style_button_bgcolor_hover',
				array(
					'label'     => esc_html__( 'Hover background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .eac-search_button-toggle:hover, {{WRAPPER}} .eac-search_button-toggle:focus' => 'background-color: {{VALUE}};',
					),
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
		$settings         = $this->get_settings_for_display();
		$is_button_hidden = 'yes' === $settings['ss_button_hidden'] ? true : false;
		$has_autocomplete = 'yes' === $settings['ss_autocomplete'] ? true : false;
		$this->add_render_attribute(
			'input',
			array(
				'placeholder'       => esc_html( $settings['ss_content_placeholder'] ),
				'class'             => 'eac-search_form-input',
				'type'              => 'search',
				'name'              => 's',
				'id'                => 'eac-search',
				'aria-labelledby'   => 'eac-search-label',
				'autocapitalize'    => 'none',
				'autocorrect'       => 'off',
				'spellcheck'        => 'false',
				'maxlength'         => '50',
				'value'             => get_search_query(),
			)
		);
		if ( 'yes' === $settings['ss_autocomplete'] ) {
			$this->add_render_attribute( 'input', 'aria-autocomplete', 'list' );
			$this->add_render_attribute( 'input', 'aria-expanded', 'false' );
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'             => 'eac-search_form-wrapper',
				'data-hide-button'  => $is_button_hidden,
				'data-autocomplete' => $has_autocomplete,
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php
			if ( ! $is_button_hidden && 'left' === $settings['ss_button_position'] ) { ?>
				<button class='eac-search_button-toggle' type='button' aria-expanded='false' aria-controls='eac-search_form' aria-label="<?php esc_html_e( 'Open search form', 'eac-components' ); ?>">
					<?php Icons_Manager::render_icon( $settings['ss_button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</button>
			<?php } ?>
			<form id='eac-search_form' class='eac-search_form' role='search' action='<?php echo esc_url( home_url() ); ?>' method='get'>
				<input class='eac-search_form-post-type' type='hidden' name='post_type' value='any' />
				<div class='eac-search_form-container'>
					<label id='eac-search-label' for='eac-search' class='visually-hidden'>Search form field</label>
					<input <?php $this->print_render_attribute_string( 'input' ); ?>>
					<span class='search-icon'> <!-- Les icones SVG sont safe -->
						<?php echo $this->get_svg_icon_search(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class='clear-icon' tabindex='0'>
						<?php echo $this->get_svg_icon_clear(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</div>
			</form>
			<?php
			if ( ! $is_button_hidden && 'right' === $settings['ss_button_position'] ) { ?>
				<button class='eac-search_button-toggle' type='button' aria-expanded='false' aria-controls='eac-search_form' aria-label="<?php esc_html_e( 'Open search form', 'eac-components' ); ?>">
					<?php Icons_Manager::render_icon( $settings['ss_button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</button>
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
	protected function content_template(): void {}
}
