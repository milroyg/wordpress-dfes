<?php
/**
 * Class: Chart_Widget
 * Name: Diagrammes
 * Slug: eac-addon-chart
 *
 * Description: Chart_Widget
 *
 * @since 1.5.4
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Repeater;

class Chart_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'chart';

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
		return array( 'eac-chart' );
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
		return array( 'eac-chart' );
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
			'chart_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'chart_file_import',
				array(
					'label'        => esc_html__( 'Import file', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'chart_file_url',
				array(
					'label'       => esc_html( 'URL' ),
					'description' => esc_html__( 'Copy/Paste the absolute path of the file: JSON format', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => array( 'active' => true ),
					'placeholder' => 'http://your-link.com/file-json.txt',
					'condition'   => array( 'chart_file_import' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_name',
				array(
					'label'       => esc_html__( 'Chart title', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'default'     => 'Chart title',
					'label_block' => true,
					'condition'   => array( 'chart_file_import!' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_type',
				array(
					'label'   => esc_html__( 'Chart type', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'bar',
					'options' => array(
						'bar'           => esc_html__( 'Bar', 'eac-components' ),
						'horizontalBar' => esc_html__( 'Horizontal bar', 'eac-components' ),
						'line'          => esc_html__( 'Line', 'eac-components' ),
						'pie'           => esc_html__( 'Pie', 'eac-components' ),
						'doughnut'      => esc_html__( 'Doughnut', 'eac-components' ),
						'radar'         => esc_html( 'Radar' ),
						'polarArea'     => esc_html__( 'Polar', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		// -------------- Axe horizontal X ------------------

		$this->start_controls_section(
			'chart_settings_x',
			array(
				'label'     => esc_html__( 'X Axis', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'chart_file_import!' => 'yes' ),
			)
		);

			$this->add_control(
				'chart_x_title',
				array(
					'label'       => esc_html__( 'Axis title', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'X Axis', 'eac-components' ),
					'placeholder' => esc_html__( 'X Axis', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'chart_x_data',
				array(
					'label'       => esc_html__( 'Data', 'eac-components' ),
					'description' => esc_html__( 'Comma to separate values', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'default'     => esc_html( 'One,Two,Three,Four,Five,Six' ),
					'placeholder' => esc_html( 'One,Two,Three,Four,Five,Six' ),
					'label_block' => true,
				)
			);

		$this->end_controls_section();

		// -------------- Axe de gauche Y ------------------

		$this->start_controls_section(
			'chart_settings_y',
			array(
				'label'     => esc_html__( 'Y Axis', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'chart_file_import!' => 'yes' ),
			)
		);

			$this->add_control(
				'chart_y_title',
				array(
					'label'       => esc_html__( 'Axis title', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Y Axis', 'eac-components' ),
					'placeholder' => esc_html__( 'Y Axis', 'eac-components' ),
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'condition'   => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_y_suffix',
				array(
					'label'        => esc_html__( 'Add suffix', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_y_suffix_carac',
				array(
					'label'       => esc_html__( 'Suffix', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'default'     => esc_html( ' €' ),
					'placeholder' => esc_html( ' €' ),
					'condition'   => array( 'chart_y_suffix' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_stacked',
				array(
					'label'        => esc_html__( 'Stacked', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_stepped',
				array(
					'label'        => esc_html__( 'Stepped', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'condition'    => array( 'chart_type' => 'line' ),
				)
			);

			/**
			$this->add_control('chart_y_100',
				[
					'label' => esc_html__("Forcer à 100%", 'eac-components'),
					'description' => esc_html__("Forcer la valeur de l'axe à 100%",'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							['name' => 'chart_type', 'operator' => '===', 'value' => 'bar'],
							['name' => 'chart_stacked', 'operator' => '!==', 'value' => 'yes'],
						],
					],
				]
			);
			*/

			$this->add_control(
				'chart_add_line',
				array(
					'label'        => esc_html__( 'Add line (Next section)', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_type' => 'bar' ),
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'chart_y_axis_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'raw'             => esc_html__( 'Each series must have as many values as those defined for the X-Axis', 'eac-components' ),
					'separator'       => 'before',
				)
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'chart_y_legend',
				array(
					'label'       => esc_html__( 'Serie label', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'placeholder' => esc_html__( 'Serie', 'eac-components' ),
				)
			);

			$repeater->add_control(
				'chart_y_data',
				array(
					'label'       => esc_html__( 'Data', 'eac-components' ),
					'description' => esc_html__( 'Comma to separate values. Point as decimal separator', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'default'     => '12,19,3,5,2,3',
					'placeholder' => '12,19,3,5,2,3',
					'label_block' => true,
				)
			);

			$this->add_control(
				'chart_y_data_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(
						array(
							'chart_y_legend' => 'Serie 1',
							'chart_y_data'   => '12.5,19,3,5.7,2,3',
						),
						array(
							'chart_y_legend' => 'Serie 2',
							'chart_y_data'   => '32,10,9.3,5,21,13',
						),
						array(
							'chart_y_legend' => 'Serie 3',
							'chart_y_data'   => '22,11,9,15,41.6,7',
						),
						array(
							'chart_y_legend' => 'Serie 4',
							'chart_y_data'   => '2.9,29,8,55,6,17',
						),
					),
					'title_field' => '{{{ chart_y_legend }}}',
					'button_text' => esc_html__( 'Add serie', 'eac-components' ),
				)
			);

		$this->end_controls_section();

		// -------------- Right axis Y2 ------------------

		$this->start_controls_section(
			'chart_settings_y2',
			array(
				'label'      => esc_html__( 'Line', 'eac-components' ),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'chart_type',
							'operator' => '===',
							'value'    => 'bar',
						),
						array(
							'name'     => 'chart_add_line',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'name'     => 'chart_file_import',
							'operator' => '!==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

			$this->add_control(
				'chart_y2_addscale',
				array(
					'label'        => esc_html__( 'Right axis', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'chart_y2_title',
				array(
					'label'       => esc_html__( 'Axis title', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'default'     => 'Y Axis 2',
					'placeholder' => esc_html__( 'Y Axis 2', 'eac-components' ),
					'label_block' => true,
					'condition'   => array( 'chart_y2_addscale' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_y2_samescale',
				array(
					'label'        => esc_html__( 'Align scales', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_y2_addscale' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_y2_suffix',
				array(
					'label'        => esc_html__( 'Add suffix', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_y2_addscale' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_y2_suffix_carac',
				array(
					'label'       => esc_html__( 'Suffix', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'default'     => esc_html( ' %' ),
					'placeholder' => esc_html( ' %' ),
					'condition'   => array(
						'chart_y2_addscale' => 'yes',
						'chart_y2_suffix'   => 'yes',
					),
				)
			);

			$this->add_control(
				'chart_y2_label',
				array(
					'label'       => esc_html__( 'Serie label', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'default'     => esc_html__( 'Line serie', 'eac-components' ),
					'placeholder' => esc_html__( 'Line serie', 'eac-components' ),
					'label_block' => true,
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'chart_y2_data',
				array(
					'label'       => esc_html__( 'Data', 'eac-components' ),
					'description' => esc_html__( 'Comma to separate values. Point as decimal separator', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'default'     => '10,15,17,16.3,17.4,14.2',
					'placeholder' => '10,15,17,16.3,17.4,14.2',
					'label_block' => true,
				)
			);

			$this->add_control(
				'chart_order_line',
				array(
					'label'   => esc_html__( 'Position', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1' => esc_html__( 'In front', 'eac-components' ),
						'2' => esc_html__( 'Behind', 'eac-components' ),
					),
				)
			);

		$this->end_controls_section();

		// Affichage des composants
		$this->start_controls_section(
			'chart_content',
			array(
				'label' => esc_html__( 'Content', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'chart_content_legend',
				array(
					'label'        => esc_html__( 'Legend', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'chart_grid_xaxis',
				array(
					'label'        => esc_html__( 'X axis grid', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_grid_yaxis',
				array(
					'label'        => esc_html__( 'Y axis grid', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'chart_type' => array( 'bar', 'line', 'horizontalBar' ) ),
				)
			);

			$this->add_control(
				'chart_grid_yaxis2',
				array(
					'label'        => esc_html__( 'Y2 axis grid', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array(
						'chart_type' => array( 'bar', 'line', 'horizontalBar' ),
						'chart_add_line' => 'yes',
					),
				)
			);

			$this->add_control(
				'chart_content_value',
				array(
					'label'        => esc_html__( 'Display values', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'chart_general_style',
			array(
				'label' => esc_html__( 'General', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'chart_wrapper',
				array(
					'label'     => esc_html__( 'Container', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'chart_wrapper_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .chart__wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'chart_global_fontsize',
				array(
					'label'   => esc_html__( 'Font size', 'eac-components' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => array(
						'size' => 15,
						'unit' => 'px',
					),
					'range'   => array(
						'px' => array(
							'min'  => 6,
							'max'  => 20,
							'step' => 1,
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'chart_global_border',
					'selector'  => '{{WRAPPER}} .chart__wrapper',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'chart_global_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .chart__wrapper',
				)
			);

			$this->add_control(
				'chart_palette_series',
				array(
					'label'     => esc_html__( 'Series', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			/** Selection des couleurs globales (Saved Color) */
			$this->add_control(
				'chart_palette_color',
				array(
					'label'        => esc_html__( 'Global colors', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_random_color!' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_random_color',
				array(
					'label'        => esc_html__( 'Random color', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_palette_color!' => 'yes' ),
				)
			);

			$this->add_control(
				'chart_transparence_color',
				array(
					'label'   => esc_html__( 'Color transparency', 'eac-components' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => array(
						'size' => 1,
						'unit' => 'px',
					),
					'range'   => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1,
							'step' => .1,
						),
					),
				)
			);

			$this->add_control(
				'chart_palette_legend',
				array(
					'label'     => esc_html__( 'Legend', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'chart_legend_color',
				array(
					'label'       => esc_html__( 'Labels color', 'eac-components' ),
					'type'        => Controls_Manager::COLOR,
					'global'      => array( 'default' => Global_Colors::COLOR_TEXT ),
					'render_type' => 'template',
				)
			);

			$this->add_control(
				'chart_gridline_color',
				array(
					'label'       => esc_html__( 'Grid color', 'eac-components' ),
					'type'        => Controls_Manager::COLOR,
					'global'      => array( 'default' => Global_Colors::COLOR_ACCENT ),
					'render_type' => 'template',
					'condition'   => array( 'chart_type!' => array( 'pie', 'doughnut' ) ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'chart_values_style',
			array(
				'label'     => esc_html__( 'Values', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'chart_content_value' => 'yes' ),
			)
		);

			$this->add_control(
				'chart_position_value',
				array(
					'label'   => esc_html__( 'Label position', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '0',
					'options' => array(
						'0' => esc_html__( 'Inside', 'eac-components' ),
						'1' => esc_html__( 'Outside', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'chart_percent_value',
				array(
					'label'        => esc_html__( 'Display percent', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'chart_type' => array( 'pie', 'doughnut', 'polarArea' ) ),
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
		/**
		 * highlight_string("<?php\n\$settings =\n" . var_export(explode("=", explode(" ", str_replace(array("[","]"), '', $settings['__dynamic__']['chart_file_url']))[3])[1], true) . ";\n?>");
		 * highlight_string("<?php\n\$settings =\n" . var_export($settings, true) . ";\n?>");
		 */

		if ( 'yes' === $settings['chart_file_import'] && empty( $settings['chart_file_url']['url'] ) ) {
			return;
		}
		if ( 'yes' !== $settings['chart_file_import'] && empty( $settings['chart_y_data_list'] ) ) {
			return;
		}
		$json_data = array();

		if ( 'yes' === $settings['chart_file_import'] ) {
			$url = filter_var( $settings['chart_file_url']['url'], FILTER_SANITIZE_URL );

			$json_source = @file_get_contents( $url );
			if ( false === $json_source ) {
				$error = error_get_last()['message'];
				echo wp_kses_post( $error );
				return;
			}

			/**
			 * Les clés/valeurs doivent être entourés de guillemets doubles
			 * La virgule de fin n'est pas autorisée
			 * json_encode attend un format UTF8 pour le contenu
			 */
			if ( mb_detect_encoding( $json_source, 'UTF-8', true ) === false ) {
				$json_source = mb_convert_encoding( $json_source, 'UTF-8', 'ISO-8859-1' );
			}

			// Supprime les octets BOM du fichier
			$json_source = str_replace( "\xEF\xBB\xBF", '', $json_source );

			$json_data = json_decode( $json_source, true );
			if ( ! $json_data ) {
				$error = 'JSON::' . json_last_error() . '::' . json_last_error_msg() . '::' . $url;
				echo $error . '<br />'; // phpcs:ignore
				return;
			}
		}
		?>
		<div class="eac-chart">
			<?php $this->render_chart( $json_data ); ?>
		</div>
		<?php
	}

	/**
	 * render_chart
	 *
	 * Affiche la div wrapper ainsi que les icones pour le téléchargement de l'image et le retournement du graphe
	 *
	 * @access protected
	 */
	protected function render_chart( $datajson ): void {
		$settings = $this->get_settings_for_display();

		// Wrapper de la liste des posts et data-settings avec un ID unique
		$id = $this->get_id();

		$container_id = 'chart__wrapper-' . $id;
		$canvas_id    = 'canvas__wrapper-' . $id;
		$download_id  = 'ddl__wrapper-' . $id;
		$title        = ! empty( $datajson ) && isset( $datajson['title'] ) ? $datajson['title'] : $settings['chart_name'];

		$this->add_render_attribute( 'chart_wrapper', 'class', 'chart__wrapper' );
		$this->add_render_attribute( 'chart_wrapper', 'id', esc_attr( $container_id ) );
		$this->add_render_attribute( 'chart_wrapper', 'data-settings', $this->get_settings_json( esc_attr( $container_id ), esc_attr( $canvas_id ), esc_attr( $download_id ), $datajson ) );
		$label = sprintf( '%1$s %2$s - %3$s', esc_html__( 'Chart type', 'eac-components' ), ucfirst( $settings['chart_type'] ), esc_html( $title ) );
		?>
		<div <?php $this->print_render_attribute_string( 'chart_wrapper' ); ?>>
			<div class='chart__wrapper-download eac-icon-svg'>
				<a id='<?php echo esc_attr( $download_id ); ?>' download='eac-media_chart.png' href='#' aria-label='<?php echo esc_attr__( 'Save as image', 'eac-components' ); ?>'>
				<?php
				Icons_Manager::render_icon(
					array(
						'value'   => 'fas fa-download',
						'library' => 'fa-solid',
					),
					array( 'aria-hidden' => 'true' )
				); ?>
				</a>
			</div>
			<div id='chart__wrapper-swap' class='chart__wrapper-swap eac-icon-svg'  aria-label='<?php echo esc_attr__( 'Swap axis', 'eac-components' ); ?>' tabindex='0'>
				<?php
				Icons_Manager::render_icon(
					array(
						'value'   => 'fas fa-sync-alt',
						'library' => 'fa-solid',
					),
					array( 'aria-hidden' => 'true' )
				); ?>
			</div>
			<canvas id='<?php echo esc_attr( $canvas_id ); ?>' aria-label='<?php echo esc_attr( $label ); ?>' role='img'></canvas>
		</div>
		<?php
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
	protected function get_settings_json( $rid, $sid, $did, $data_json = array() ): string {
		$settings          = $this->get_settings_for_display();
		$array_label       = array();
		$array_data_series = array();
		$dboolean          = array();
		$suffix_y          = 0;
		$suffix_y2         = 0;

		// Ajout d'un ligne
		$addline   = 'yes' === $settings['chart_add_line'] ? 1 : 0;
		$orderline = 1 === $addline ? $settings['chart_order_line'] : 0;
		$addscale  = 1 === $addline ? 'yes' === $settings['chart_y2_addscale'] ? 1 : 0 : 0;
		$samescale = 1 === $addline ? 'yes' === $settings['chart_y2_samescale'] ? 1 : 0 : 0;
		$y2title   = 1 === $addline ? esc_html( $settings['chart_y2_title'] ) : '';
		$y2label   = 1 === $addline ? esc_html( $settings['chart_y2_label'] ) : '';
		$y2data    = 1 === $addline && ! empty( $settings['chart_y2_data'] ) ? esc_html( $settings['chart_y2_data'] ) : '';

		// C'est un fichier JSON. Boucle sur les données
		if ( ! empty( $data_json ) ) {
			foreach ( $data_json['datasets'] as $item ) {
				// type de chart === bar et key/value 'type: line' pour l'ajout d'une ligne
				if ( 'bar' === $settings['chart_type'] && isset( $item['type'] ) && 'line' === $item['type'] ) {
					$addline   = 1;
					$orderline = 1;
					$addscale  = isset( $data_json['options']['y_axis2']['display'] ) ? $data_json['options']['y_axis2']['display'] : 0; // Axe Y de droite
					$samescale = 1;
					$y2title   = isset( $data_json['options']['y_axis2']['title'] ) ? esc_html( $data_json['options']['y_axis2']['title'] ) : ''; // Le titre
					$y2label   = isset( $item['label'] ) ? esc_html( $item['label'] ) : '';
					$y2data    = isset( $item['data'] ) ? $item['data'] : '';
				} else {
					array_push( $array_label, esc_html( $item['label'] ) );
					array_push( $array_data_series, $item['data'] );
				}
			}
		} else { // Champs standards
			foreach ( $settings['chart_y_data_list'] as $item ) {
				if ( ! empty( $item['chart_y_data'] ) ) {
					array_push( $array_label, esc_html( $item['chart_y_legend'] ) );
					array_push( $array_data_series, esc_html( $item['chart_y_data'] ) );
				}
			}
		}

		// Suffixe sur l'axe Y & Y2
		if ( ! empty( $data_json ) ) {
			$suffix_y  = isset( $data_json['options']['y_axis']['suffix'] ) ? esc_html( $data_json['options']['y_axis']['suffix'] ) : 0;
			$suffix_y2 = isset( $data_json['options']['y_axis2']['suffix'] ) ? esc_html( $data_json['options']['y_axis2']['suffix'] ) : 0;
		} elseif ( 'yes' === $settings['chart_y_suffix'] || 'yes' === $settings['chart_y2_suffix'] ) {
			$suffix_y  = ! empty( $settings['chart_y_suffix_carac'] ) ? esc_html( $settings['chart_y_suffix_carac'] ) : 0;
			$suffix_y2 = ! empty( $settings['chart_y2_suffix_carac'] ) ? esc_html( $settings['chart_y2_suffix_carac'] ) : 0;
		}

		array_push( $dboolean, $addline ); // Rang 0
		array_push( $dboolean, $orderline );
		array_push( $dboolean, $addscale );
		array_push( $dboolean, $samescale );

		array_push( $dboolean, 'yes' === $settings['chart_content_legend'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $settings['chart_grid_xaxis'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $settings['chart_grid_yaxis'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $settings['chart_grid_yaxis2'] ? 1 : 0 );

		array_push( $dboolean, 'yes' === $settings['chart_content_value'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $settings['chart_content_value'] ? $settings['chart_position_value'] : 0 );
		array_push( $dboolean, 'yes' === $settings['chart_percent_value'] ? 1 : 0 );

		// Unparenthesized deprecated(a ? b : c) ? d : e` or `a ? b : (c ? d : e)
		array_push( $dboolean, ( ! empty( $data_json ) && isset( $data_json['options']['stacked'] ) ? $data_json['options']['stacked'] : 'yes' === $settings['chart_stacked'] ) ? 1 : 0 );
		array_push( $dboolean, ( ! empty( $data_json ) && isset( $data_json['options']['stepped'] ) ? $data_json['options']['stepped'] : 'yes' === $settings['chart_stepped'] ) ? 1 : 0 );

		array_push( $dboolean, 0 ); // Y Forced 100%
		array_push( $dboolean, $settings['chart_transparence_color']['size'] );
		array_push( $dboolean, 'yes' === $settings['chart_random_color'] ? 1 : 0 );
		array_push( $dboolean, 'yes' === $settings['chart_palette_color'] ? 1 : 0 );
		array_push( $dboolean, $settings['chart_global_fontsize']['size'] );

		array_push( $dboolean, $suffix_y );
		array_push( $dboolean, $suffix_y2 );

		$module_settings = array(
			'data_sid'     => esc_attr( $sid ),
			'data_rid'     => esc_attr( $rid ),
			'data_did'     => esc_attr( $did ),

			'data_type'    => $settings['chart_type'],
			'data_title'   => ! empty( $data_json ) && isset( $data_json['title'] ) ? esc_html( $data_json['title'] ) : esc_html( $settings['chart_name'] ),
			'data_labels'  => ! empty( $data_json ) && isset( $data_json['labels'] ) ? esc_html( $data_json['labels'] ) : esc_html( $settings['chart_x_data'] ),

			// Plusieurs séries. Séparateur = virgule pour chaque label
			'x_label'      => implode( ',', $array_label ),
			'x_title'      => ! empty( $data_json ) && isset( $data_json['options']['x_axis']['title'] ) ? esc_html( $data_json['options']['x_axis']['title'] ) : esc_html( $settings['chart_x_title'] ),

			// Plusieurs séries de données. Séparateur = point-virgule pour chaque série de données
			'y_data'       => implode( ';', $array_data_series ),
			'y_title'      => ! empty( $data_json ) && isset( $data_json['options']['y_axis']['title'] ) ? esc_html( $data_json['options']['y_axis']['title'] ) : esc_html( $settings['chart_y_title'] ),

			'y2_data'      => $y2data,
			'y2_title'     => $y2title,
			'y2_label'     => $y2label,

			'color_legend' => $settings['chart_legend_color'],      // Couleur légende, labels et titre
			'color_grid'   => $settings['chart_gridline_color'],    // Couleur de la grille

			'data_boolean' => implode( ',', $dboolean ),

			'data_color'   => implode( ',', Eac_Tools_Util::get_palettes_color() ),
		);

		return wp_json_encode( $module_settings );
	}

	protected function content_template(): void {}
}
