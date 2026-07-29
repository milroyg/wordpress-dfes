<?php
/**
 * Class: Reader_Progress_Bar_Widget
 * Slug: eac-addon-reader-progress
 *
 * Description: Ajoute une barre de progression à la lecture du contenu
 *
 * @since 2.1.1
 */

namespace EACCustomWidgets\Includes\TemplatesLib\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

class Reader_Progress_Bar_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'reader-progress';

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
		return array( 'eac-reader-progress' );
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
			'rpb_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'rpb_content_height',
				array(
					'label'       => esc_html__( 'Height', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 2,
					'max'         => 50,
					'step'        => 1,
					'default'     => 15,
					'render_type' => 'none',
					'selectors'   => array(
						'{{WRAPPER}} .progress' => 'height: {{SIZE}}px;',
					),
				)
			);

			$this->add_control(
				'rpb_content_badge',
				array(
					'label'   => esc_html__( 'Add badge', 'eac-components' ),
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
				)
			);

			$this->add_control(
				'rpb_content_rtl',
				array(
					'label'        => esc_html__( 'Display direction', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'  => array(
							'title' => esc_html__( 'Left', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'right' => array(
							'title' => esc_html__( 'Right', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'      => 'right',
					'toggle'       => false,
					'prefix_class' => 'progress-',
					'render_type'  => 'template',
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'rpb_style_barre',
			array(
				'label' => esc_html__( 'Progress bar', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				array(
					'name'           => 'rpb_bg',
					'types'          => array( 'classic', 'gradient' ),
					'fields_options' => array(
						'color'          => array(
							'default' => '#84fab0',
						),
						'color_b'        => array(
							'default' => '#8fd3f4',
						),
						'gradient_type'  => array(
							'default' => 'linear',
						),
						'gradient_angle' => array(
							'default' => array(
								'unit' => 'deg',
								'size' => 120,
							),
						),
					),
					'exclude'        => array( 'image' ),
					'selector'       => '{{WRAPPER}} .progress',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'rpb_style_badge',
			array(
				'label'     => esc_html__( 'Badge', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'rpb_content_badge' => 'yes' ),
			)
		);

			$this->add_control(
				'rpb_content_badge_position',
				array(
					'label'       => esc_html__( 'Position', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
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
					'default'     => 'center',
					'selectors'   => array(
						'{{WRAPPER}}.progress-left .eac-reader-progress .progress, {{WRAPPER}}.progress-right .eac-reader-progress .progress' => 'justify-content: {{VALUE}};',
					),
					'condition'   => array( 'rpb_content_badge' => 'yes' ),
				)
			);

			$this->add_control(
				'rpb_style_badge_color',
				array(
					'label'     => esc_html__( 'Color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
					'default'   => '#000',
					'selectors' => array(
						'{{WRAPPER}} .progress-badge' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'           => 'rpb_style_badge_typography',
					'label'          => esc_html__( 'Typography', 'eac-components' ),
					'global'         => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'fields_options' => array(
						'font_size'   => array(
							'default' => array(
								'unit' => 'px',
								'size' => 12,
							),
						),
						'font_weight' => array(
							'default' => 600,
						),
					),
					'selector'       => '{{WRAPPER}} .progress-badge',
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
		<div class='eac-reader-progress'>
			<div class='progress' role='progressbar' aria-label="<?php esc_html_e( 'Reading progress bar', 'eac-components' ); ?>" aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'>
				<?php if ( isset( $settings['rpb_content_badge'] ) && 'yes' === $settings['rpb_content_badge'] ) { ?>
					<span class='progress-badge'></span>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	protected function content_template(): void {}
}
