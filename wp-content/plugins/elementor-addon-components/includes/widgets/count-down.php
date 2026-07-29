<?php
/**
 * Class: Count_Down_Widget
 * Name: Compte à rebours
 *
 * Slug: eac-addon-count-down
 *
 * Description: Gestion et affichage d'un compte à rebours
 *
 * @since 2.2.7
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;
use EACCustomWidgets\Includes\TemplatesLib\Documents\Site_Header;
use EACCustomWidgets\Includes\TemplatesLib\Documents\Site_Footer;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Count_Down_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'count-down';

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
	 * @return libraries Array.
	 */
	public function get_script_depends(): array {
		return array( 'eac-count-down' );
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
			'cd_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'cd_type',
				array(
					'label'   => esc_html__( 'Type', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'due_date'  => esc_html__( 'Due date', 'eac-components' ),
						'evergreen' => esc_html( 'Evergreen' ),
					),
					'default' => 'due_date',
				)
			);

			$default_date = gmdate( 'Y-m-d H:i', strtotime( '+1 week' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

			$this->add_control(
				'cd_due_date',
				array(
					'label'          => esc_html__( 'Due date', 'eac-components' ),
					'type'           => Controls_Manager::DATE_TIME,
					'picker_options' => array(
						'dateFormat'        => 'Y-m-d H:i',
						'enableTime'        => true,
						'minuteIncrement'   => 1,
						'time_24hr'         => true,
						'monthSelectorType' => 'dropdown',
					),
					'default'        => $default_date,
					/* translators: %s: Time zone. */
					'description'    => sprintf( esc_html__( 'Your timezone: %s.', 'eac-components' ), wp_timezone_string() ),
					'dynamic'        => array( 'active' => true ),
					'condition'      => array( 'cd_type' => 'due_date' ),
				)
			);

			$this->add_control(
				'cd_evg_hours',
				array(
					'label'     => esc_html__( 'Hours', 'eac-components' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 25,
					'condition'   => array( 'cd_type' => 'evergreen' ),
				)
			);

			$this->add_control(
				'cd_evg_minutes',
				array(
					'label'     => esc_html__( 'Minutes', 'eac-components' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 59,
					'condition'   => array( 'cd_type' => 'evergreen' ),
				)
			);

			$this->add_control(
				'cd_skin',
				array(
					'label'        => esc_html__( 'Skin', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'default',
					'options'      => array(
						'default'  => esc_html__( 'Default', 'eac-components' ),
						'skin-1' => 'Skin 1',
						'skin-2' => 'Skin 2',
						'skin-3' => 'Skin 3',
					),
					'prefix_class' => 'count-down_skin-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'cd_expire_action',
				array(
					'label'       => esc_html__( 'Action after expiration', 'eac-components' ),
					'type'        => Controls_Manager::SELECT2,
					'options'     => $this->get_options_action(),
					'default'     => array( 'message' ),
					'multiple'    => true,
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'cd_action_message',
				array(
					'label'       => esc_html__( 'Message', 'eac-components' ),
					'type'        => Controls_Manager::TEXTAREA,
					'default'     => esc_html__( 'Offer ended — check out our new arrivals', 'eac-components' ),
					'dynamic'     => array(
						'active' => true,
					),
					'ai'          => array(
						'active' => false,
					),
					'render_type' => 'none',
					'condition'   => array( 'cd_expire_action' => 'message' ),
				)
			);

			$this->add_responsive_control(
				'cd_message_alignment',
				array(
					'label'     => esc_html__( 'Alignment', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'start'   => array(
							'title' => is_rtl() ? esc_html__( 'Right', 'eac-components' ) : esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-text-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'eac-components' ),
							'icon'  => 'eicon-text-align-center',
						),
						'end'  => array(
							'title' => is_rtl() ? esc_html__( 'Left', 'eac-components' ) : esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-text-align-{$end}",
						),
					),
					'default'   => 'center',
					'selectors' => array(
						'{{WRAPPER}} .count-down_expire-message' => 'text-align: {{VALUE}};',
					),
					'condition'   => array( 'cd_expire_action' => 'message' ),
				)
			);

			$this->add_control(
				'cd_action_redirect',
				array(
					'label'       => esc_html( 'URL' ),
					'type'        => 'eac-select2',
					'select2Options' => array(
						'query_type' => 'url',
					),
					'render_type' => 'none',
					'condition'   => array( 'cd_expire_action' => 'redirect' ),
				)
			);

			/**$this->add_control(
				'cd_action_redirect',
				array(
					'label'        => esc_html( 'URL' ),
					'type'         => Controls_Manager::URL,
					'placeholder'  => esc_html__( 'Type or paste your URL', 'eac-components' ),
					'options'      => false,
					'dynamic'      => array(
						'active' => true,
					),
					'label_block'  => true,
					'render_type'  => 'none',
					'autocomplete' => true,
					'condition'    => array( 'cd_expire_action' => 'redirect' ),
				)
			);*/

			$this->add_control(
				'cd_action_tempo',
				array(
					'label'       => esc_html__( 'Time delay (s)', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 2,
					'step'        => 1,
					'default'     => 5,
					'condition'   => array( 'cd_expire_action' => 'redirect' ),
					'render_type' => 'none',
				)
			);

			$this->add_control(
				'cd_show_action',
				array(
					'label'     => esc_html__( 'Display action', 'eac-components' ),
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
					'prefix_class' => 'show-action-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'cd_content',
			array(
				'label' => esc_html__( 'Content', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'cd_display_content',
				array(
					'label'        => esc_html__( 'Display', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'options'      => array(
						'block'  => esc_html__( 'Block', 'eac-components' ),
						'inline' => esc_html__( 'Inline', 'eac-components' ),
					),
					'default'      => 'block',
					'prefix_class' => 'cd-display-',
				)
			);

			$this->add_responsive_control(
				'cd_container_width',
				array(
					'label'       => esc_html__( 'Width', 'eac-components' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px', 'em', '%' ),
					'default'     => array(
						'size' => 80,
						'unit' => '%',
					),
					'tablet_default' => array(
						'unit' => '%',
					),
					'mobile_default' => array(
						'unit' => '%',
					),
					'range'       => array(
						'px' => array(
							'max'  => 1140,
							'step' => 50,
						),
						'em' => array(
							'max'  => 200,
							'step' => 10,
						),
						'%'  => array(
							'max'  => 100,
							'step' => 10,
						),
					),
					'label_block' => true,
					'selectors'   => array( '{{WRAPPER}} .count-down_container' => 'inline-size: {{SIZE}}{{UNIT}};' ),
				)
			);

			$this->add_responsive_control(
				'cd_alignment',
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
					'selectors' => array( '{{WRAPPER}} .count-down_container' => 'margin-inline: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'cd_show_title',
				array(
					'label'     => esc_html__( 'Add title', 'eac-components' ),
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
					'separator' => 'before',
				)
			);

			$this->add_control(
				'cd_label_title',
				array(
					'label'      => esc_html__( 'Title', 'eac-components' ),
					'type'       => Controls_Manager::TEXT,
					'default'    => esc_html__( 'Get 50% off the entire catalog', 'eac-components' ),
					'dynamic'    => array(
						'active' => true,
					),
					'ai'         => array(
						'active' => false,
					),
					'label_block' => true,
					'condition'   => array(
						'cd_show_title' => 'yes',
					),
				)
			);

			$this->add_control(
				'cd_show_labels',
				array(
					'label'     => esc_html__( 'Display labels', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'    => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => 'yes',
					'toggle'    => false,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'cd_show_days',
				array(
					'label'     => esc_html__( 'Days', 'eac-components' ),
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
				'cd_label_days',
				array(
					'label'   => esc_html__( 'Label days', 'eac-components' ),
					'type'    => Controls_Manager::TEXT,
					'default' => esc_html__( 'Days', 'eac-components' ),
					'condition' => array(
						'cd_show_labels' => 'yes',
					),
					'dynamic' => array(
						'active' => true,
					),
					'ai' => array(
						'active' => false,
					),
				)
			);

			$this->add_control(
				'cd_show_hours',
				array(
					'label'   => esc_html__( 'Hours', 'eac-components' ),
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
				'cd_label_hours',
				array(
					'label' => esc_html__( 'Label hours', 'eac-components' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Hours', 'eac-components' ),
					'condition' => array(
						'cd_show_labels' => 'yes',
						'cd_show_hours' => 'yes',
					),
					'dynamic' => array(
						'active' => true,
					),
					'ai' => array(
						'active' => false,
					),
				)
			);

			$this->add_control(
				'cd_show_minutes',
				array(
					'label'       => esc_html__( 'Minutes', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'yes' => array(
							'title' => esc_html__( 'Yes', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'No', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'     => 'yes',
					'toggle'      => false,
				)
			);

			$this->add_control(
				'cd_label_minutes',
				array(
					'label' => esc_html__( 'Label minutes', 'eac-components' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Minutes', 'eac-components' ),
					'condition' => array(
						'cd_show_labels' => 'yes',
						'cd_show_minutes' => 'yes',
					),
					'dynamic' => array(
						'active' => true,
					),
					'ai' => array(
						'active' => false,
					),
				)
			);

			$this->add_control(
				'cd_show_seconds',
				array(
					'label'     => esc_html__( 'Seconds', 'eac-components' ),
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
				'cd_label_seconds',
				array(
					'label' => esc_html__( 'Label secondes', 'eac-components' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Seconds', 'eac-components' ),
					'condition' => array(
						'cd_show_labels'  => 'yes',
					),
					'dynamic' => array(
						'active' => true,
					),
					'ai' => array(
						'active' => false,
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'cd_general_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'cd_container_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .count-down_container' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'cd_container_border',
					'selector' => '{{WRAPPER}} .count-down_container',
				)
			);

			$this->add_control(
				'cd_container_radius',
				array(
					'label'      => esc_html__( 'Border radius', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .count-down_container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'cd_container_padding',
				array(
					'label'     => esc_html__( 'Padding', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .count-down_container' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'cd_content_style',
			array(
				'label' => esc_html__( 'Content', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cd_style_title',
			array(
				'label'     => esc_html__( 'Title', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array( 'cd_show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'cd_title_color',
			array(
				'label'     => esc_html__( 'Color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
				'selectors' => array(
					'{{WRAPPER}} .count-down_container-title' => 'color: {{VALUE}};',
				),
				'condition' => array( 'cd_show_title' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cd_title_typography',
				'label'     => esc_html__( 'Typography', 'eac-components' ),
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
				'selector'  => '{{WRAPPER}} .count-down_container-title',
				'condition' => array( 'cd_show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'cd_style_item',
			array(
				'label'     => esc_html__( 'Timer', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'cd_items_margin',
			array(
				'label' => esc_html__( 'Margin', 'eac-components' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default' => array(
					'size' => 10,
				),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
					'em' => array(
						'max' => 10,
					),
				),
				'selectors' => array( '{{WRAPPER}} .count-down_container-items' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'cd_item_color',
			array(
				'label'     => esc_html__( 'Color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
				'selectors' => array(
					'{{WRAPPER}} div[class^="count-down_digit-"]' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'cd_item_typography',
				'label'          => esc_html__( 'Typography', 'eac-components' ),
				'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
				'selector'       => '{{WRAPPER}} div[class^="count-down_digit-"]',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'cd_item_text_shadow',
				'label'    => esc_html__( 'Text shadow', 'eac-components' ),
				'selector' => '{{WRAPPER}} div[class^="count-down_digit-"]',
			)
		);

		$this->add_control(
			'cd_item_bgcolor',
			array(
				'label'     => esc_html__( 'Background color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
				'selectors' => array(
					'{{WRAPPER}} .count-down_container-item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cd_item_border',
				'selector' => '{{WRAPPER}} .count-down_container-item',
			)
		);

		$this->add_control(
			'cd_item_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'eac-components' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .count-down_container-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cd_item_shadow',
				'label'    => esc_html__( 'Shadow', 'eac-components' ),
				'selector' => '{{WRAPPER}} .count-down_container-item',
			)
		);

		$this->add_control(
			'cd_style_label',
			array(
				'label'     => esc_html__( 'Label', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'cd_show_labels' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'cd_label_margin',
			array(
				'label'      => esc_html__( 'Margin top', 'eac-components' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'unit' => 'px',
				),
				'selectors'  => array( '{{WRAPPER}} div[class*="count-down_label-"]' => 'margin-block-start: {{SIZE}}{{UNIT}};' ),
				'condition'  => array(
					'cd_show_labels' => 'yes',
					'cd_display_content' => 'block',
				),
			)
		);

		$this->add_control(
			'cd_label_color',
			array(
				'label'     => esc_html__( 'Color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
				'selectors' => array(
					'{{WRAPPER}} div[class*="count-down_label-"]' => 'color: {{VALUE}};',
				),
				'condition' => array( 'cd_show_labels' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cd_label_typography',
				'label'     => esc_html__( 'Typography', 'eac-components' ),
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
				'selector'  => '{{WRAPPER}} div[class*="count-down_label-"]',
				'condition' => array( 'cd_show_labels' => 'yes' ),
			)
		);

		$this->add_control(
			'cd_style_message',
			array(
				'label'     => esc_html__( 'Expiration message', 'eac-components' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => array( 'cd_expire_action' => 'message' ),
			)
		);

		$this->add_control(
			'cd_message_color',
			array(
				'label'     => esc_html__( 'Color', 'eac-components' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
				'selectors' => array(
					'{{WRAPPER}} .count-down_expire-message' => 'color: {{VALUE}};',
				),
				'condition'   => array( 'cd_expire_action' => 'message' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'cd_message_typography',
				'label'     => esc_html__( 'Typography', 'eac-components' ),
				'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
				'selector'  => '{{WRAPPER}} .count-down_expire-message',
				'condition'   => array( 'cd_expire_action' => 'message' ),
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
		?>
		<div class='eac-count-down' style='visibility: hidden;'>
			<?php $this->render_countdown(); ?>
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
	protected function render_countdown(): void {
		$document = \Elementor\Plugin::$instance->documents->get_current();

		/**if ( ! $document ) {
			$main_id = $document->get_main_id();
			$post    = $document->get_post();
			$data = array(
				'main_id'        => $main_id,
				'document_id'    => $document->get_id(),
				'title'          => $document->get_title(),
				//'post_type'      => $document->get_post_type(),
				'status'         => $post ? $post->post_status : null,
				//'post'           => $post,
				'settings'       => $document->get_settings(),
				//'settings_display'=> $document->get_settings_for_display(),
				//'elements_raw'   => $document->get_elements_raw(), // structure des widgets/sections
				//'css'            => $document->get_css(),
				'meta_type_key'  => get_post_meta( $main_id, \Elementor\Core\Base\Document::TYPE_META_KEY, true ),
				'all_post_meta'  => get_post_meta( $main_id ),
				'properties'     => $document->get_properties(),
			);
			write_log( $data );
		}*/

		$settings       = $this->get_settings_for_display();
		$days           = 0;
		$hours          = 0;
		$minutes        = 0;
		$seconds        = 0;
		$cd_type        = $settings['cd_type'];
		$show_days      = 'yes' === $settings['cd_show_days'] ? true : false;
		$show_hours     = 'yes' === $settings['cd_show_hours'] ? true : false;
		$show_minutes   = 'yes' === $settings['cd_show_minutes'] ? true : false;
		$show_seconds   = 'yes' === $settings['cd_show_seconds'] ? true : false;
		$due_date       = 0;
		$now_tz         = new \DateTime( 'now', new \DateTimeZone( wp_timezone_string() ) );

		if ( 'due_date' === $cd_type ) {
			$due_date_tz = \DateTime::createFromFormat( 'Y-m-d H:i', $settings['cd_due_date'], new \DateTimeZone( wp_timezone_string() ) );
			$due_date    = $due_date_tz instanceof \DateTime ? $due_date_tz->getTimestamp() - $now_tz->getTimestamp() : 0;
		} elseif ( 'evergreen' === $settings['cd_type'] ) {
			$hours    = ! empty( $settings['cd_evg_hours'] ) ? $settings['cd_evg_hours'] : 0;
			$minutes  = ! empty( $settings['cd_evg_minutes'] ) ? $settings['cd_evg_minutes'] : 0;
			$now      = new \DateTime( 'now', new \DateTimeZone( wp_timezone_string() ) );
			$today    = $now->add( \DateInterval::createFromDateString( "{$hours} hours {$minutes} minutes" ) );
			$due_date = $today instanceof \DateTime ? $today->getTimestamp() - $now_tz->getTimestamp() : 0;
		}

		// Calculate days, hours, minutes, seconds.
		if ( 0 < $due_date ) {
			$days = floor( $due_date / DAY_IN_SECONDS );
			$due_date -= $days * DAY_IN_SECONDS;
			$hours = floor( $due_date / HOUR_IN_SECONDS );
			$due_date -= $hours * HOUR_IN_SECONDS;
			$minutes = floor( $due_date / MINUTE_IN_SECONDS );
			$due_date -= $minutes * MINUTE_IN_SECONDS;
			$seconds = floor( $due_date );
			$due_date -= $seconds;
		}

		$this->add_render_attribute(
			'container_wrapper',
			array(
				'class'         => 'count-down_container',
				'role'          => 'timer',
				'aria-live'     => 'polite',
				'aria-atomic'   => 'true',
				'aria-interval' => '60',
				'aria-label'    => esc_attr__( 'Countdown', 'eac-components' ),
				'tabindex'      => '0',
				'data-settings' => $this->get_settings_json( $days, $hours, $minutes, $seconds ),
			)
		);

		/** Titre */
		$this->add_inline_editing_attributes( 'cd_label_title', 'none' );
		$this->add_render_attribute( 'cd_label_title', 'class', 'count-down_container-title' );
		$this->add_render_attribute( 'cd_label_title', 'id', 'count-down_container-title' );
		/** Jour */
		$this->add_inline_editing_attributes( 'cd_label_days', 'none' );
		$this->add_render_attribute( 'cd_label_days', 'class', 'count-down_label-days' );
		$this->add_render_attribute( 'cd_label_days', 'id', 'count-down_label-days' );
		/** Heure */
		$this->add_inline_editing_attributes( 'cd_label_hours', 'none' );
		$this->add_render_attribute( 'cd_label_hours', 'class', 'count-down_label-hours' );
		$this->add_render_attribute( 'cd_label_hours', 'id', 'count-down_label-hours' );
		/** Minute */
		$this->add_inline_editing_attributes( 'cd_label_minutes', 'none' );
		$this->add_render_attribute( 'cd_label_minutes', 'class', 'count-down_label-minutes' );
		$this->add_render_attribute( 'cd_label_minutes', 'id', 'count-down_label-minutes' );
		/** Seconde */
		$this->add_inline_editing_attributes( 'cd_label_seconds', 'none' );
		$this->add_render_attribute( 'cd_label_seconds', 'class', 'count-down_label-seconds' );
		$this->add_render_attribute( 'cd_label_seconds', 'id', 'count-down_label-seconds' );
		/** Items */
		$this->add_render_attribute( 'items_wrapper', 'class', 'count-down_container-items' );
		$this->add_render_attribute( 'items_wrapper', 'dir', 'ltr' ); ?>

		<div <?php $this->print_render_attribute_string( 'container_wrapper' ); ?>>
			<?php if ( 'yes' === $settings['cd_show_title'] ) : ?>
				<div <?php $this->print_render_attribute_string( 'cd_label_title' ); ?>><?php echo esc_html( $settings['cd_label_title'] ); ?></div>
			<?php endif; ?>
			<div <?php $this->print_render_attribute_string( 'items_wrapper' ); ?>>
				<?php if ( $show_days ) : ?>
					<div class='count-down_container-item'>
						<div class='count-down_digit-days' aria-labelledby='count-down_label-days'><?php printf( '%02d', absint( $days ) ); ?></div>
						<?php if ( 'yes' === $settings['cd_show_labels'] ) : ?>
							<div <?php $this->print_render_attribute_string( 'cd_label_days' ); ?>><?php echo esc_html( $settings['cd_label_days'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $show_hours ) : ?>
					<div class='count-down_container-item'>
						<div class='count-down_digit-hours' aria-labelledby='count-down_label-hours'><?php printf( '%02d', absint( $hours ) ); ?></div>
						<?php if ( 'yes' === $settings['cd_show_labels'] ) : ?>
							<div <?php $this->print_render_attribute_string( 'cd_label_hours' ); ?>><?php echo esc_html( $settings['cd_label_hours'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $show_minutes ) : ?>
					<div class='count-down_container-item'>
						<div class='count-down_digit-minutes' aria-labelledby='count-down_label-minutes'><?php printf( '%02d', absint( $minutes ) ); ?></div>
						<?php if ( 'yes' === $settings['cd_show_labels'] ) : ?>
							<div <?php $this->print_render_attribute_string( 'cd_label_minutes' ); ?>><?php echo esc_html( $settings['cd_label_minutes'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $show_seconds ) : ?>
					<div class='count-down_container-item'>
						<div class='count-down_digit-seconds' aria-labelledby='count-down_label-seconds' aria-hidden='true'><?php printf( '%02d', absint( $seconds ) ); ?></div>
						<?php if ( 'yes' === $settings['cd_show_labels'] ) : ?>
							<div <?php $this->print_render_attribute_string( 'cd_label_seconds' ); ?>><?php echo esc_html( $settings['cd_label_seconds'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $settings['cd_expire_action'] ) ) : ?>
				<div class='count-down_container-expire'>
					<?php if ( ! empty( $settings['cd_action_message'] ) && in_array( 'message', $settings['cd_expire_action'], true ) ) :
						printf( '<span class="count-down_expire-message">%s</span>', nl2br( esc_html( $settings['cd_action_message'] ) ) );
					endif;
					if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() && in_array( 'redirect', $settings['cd_expire_action'], true ) && ! empty( $settings['cd_action_redirect'] ) ) :
						$is_valid_url = $this->is_valid_url( get_permalink( $settings['cd_action_redirect'] ) );
						if ( $is_valid_url ) :
							$url = get_permalink( $settings['cd_action_redirect'] );
							printf( '<span class="count-down_expire-redirect">%s</span>', esc_url( $url ) );
						endif;
					endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * is_valid_url
	 *
	 * @param mixed $url
	 *
	 * @return bool
	 */
	private function is_valid_url( $url ): bool {
		return ! preg_match( '/\bjavascript\b/i', $url ) && filter_var( $url, FILTER_VALIDATE_URL );
	}

	/**
	 * get_settings_json()
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 *
	 * @uses      wp_json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 */

	protected function get_settings_json( $d, $h, $m, $s ): string {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_type'        => $module_settings['cd_type'],
			'data_days'        => isset( $module_settings['cd_show_days'] ) && 'yes' === $module_settings['cd_show_days'] ? true : false,
			'data_hours'       => isset( $module_settings['cd_show_hours'] ) && 'yes' === $module_settings['cd_show_hours'] ? true : false,
			'data_minutes'     => isset( $module_settings['cd_show_minutes'] ) && 'yes' === $module_settings['cd_show_minutes'] ? true : false,
			'data_seconds'     => isset( $module_settings['cd_show_seconds'] ) && 'yes' === $module_settings['cd_show_seconds'] ? true : false,
			'data_days_val'    => $d,
			'data_hours_val'   => $h,
			'data_minutes_val' => $m,
			'data_seconds_val' => $s,
			'data_actions'     => $module_settings['cd_expire_action'],
			'data_tempo'       => ! empty( $module_settings['cd_action_tempo'] ) ? absint( $module_settings['cd_action_tempo'] ) : 5,
		);

		return wp_json_encode( $settings );
	}

	/**
	 * get_options_action
	 *
	 * @return array
	 */
	protected function get_options_action(): array {
		$options = array(
			'message' => esc_html__( 'Display message', 'eac-components' ),
			'hide'    => esc_html__( 'Hide', 'eac-components' ),
		);

		$document = \Elementor\Plugin::$instance->documents->get_current();
		if ( null !== $document ) {
			$main_id = $document->get_main_id();
			$template_name = get_post_meta( $main_id, \Elementor\Core\Base\Document::TYPE_META_KEY, true );
			if ( ! in_array( $template_name, array( Site_Header::TYPE, Site_Footer::TYPE ), true ) ) {
				$options['redirect'] = esc_html__( 'Redirect', 'eac-components' );
			}
		}
		return $options;
	}

	protected function content_template(): void {}
}
