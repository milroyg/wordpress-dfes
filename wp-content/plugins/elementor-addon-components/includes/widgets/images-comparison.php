<?php
/**
 * Class: Images_Comparison_Widget
 * Name: Image comparison
 * Slug: eac-addon-images-comparison
 *
 * Description: Images_Comparison_Widget affiche deux images à titre de comparaison
 *
 * @since 1.0.0
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Load_Config;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class Images_Comparison_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'images-comparison';

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
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return array libraries list.
	 */
	public function get_script_depends(): array {
		return array( 'eac-images-comparison' );
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
		return array( 'eac-images-comparison' );
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
			'ic_gallery_content_left',
			array(
				'label' => esc_html__( 'Left image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ic_img_content_modified',
				array(
					'name'      => 'img_modified',
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array( 'active' => true ),
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'ic_img_name_original',
				array(
					'name'        => 'name_original',
					'label'       => esc_html__( 'Tag', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'default'     => esc_html__( 'Left tag', 'eac-components' ),
					'placeholder' => esc_html__( 'Left', 'eac-components' ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'ic_img_name_original_pos',
				array(
					'label'        => esc_html__( 'Position', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'top',
					'options'      => array(
						'top'    => esc_html__( 'Top', 'eac-components' ),
						'middle' => esc_html__( 'Middle', 'eac-components' ),
						'bottom' => esc_html__( 'Bottom', 'eac-components' ),
					),
					'prefix_class' => 'b-diff__title_after-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ic_gallery_content_right',
			array(
				'label' => esc_html__( 'Right image', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'ic_img_content_original',
				array(
					'name'      => 'img_original',
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::MEDIA,
					'dynamic'   => array( 'active' => true ),
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'ic_img_name_modified',
				array(
					'name'        => 'name_modified',
					'label'       => esc_html__( 'Tag', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'default'     => esc_html__( 'Right tag', 'eac-components' ),
					'placeholder' => esc_html__( 'Right', 'eac-components' ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'ic_img_name_modified_pos',
				array(
					'label'        => esc_html__( 'Position', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'top',
					'options'      => array(
						'top'    => esc_html__( 'Top', 'eac-components' ),
						'middle' => esc_html__( 'Middle', 'eac-components' ),
						'bottom' => esc_html__( 'Bottom', 'eac-components' ),
					),
					'prefix_class' => 'b-diff__title_before-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ic_gallery_content_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'ic_image_size',
					'default' => 'medium',
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'ic_image_alignment',
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
						'{{WRAPPER}}' => 'margin-inline: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'ic_container_section_style',
			array(
				'label' => esc_html__( 'Container', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'ic_container_border',
					'separator' => 'before',
					'selector'  => '{{WRAPPER}} .b-diff',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'ic_container_shadow',
					'label'    => esc_html__( 'Shadow', 'eac-components' ),
					'selector' => '{{WRAPPER}} .b-diff',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'ic_etiquette_section_style',
			array(
				'label' => esc_html__( 'Tags', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'ic_etiquette_color',
				array(
					'label'     => esc_html__( 'Text color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#FFF',
					'selectors' => array(
						'{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after' => 'color: {{VALUE}};',
					),
					'separator' => 'none',
				)
			);

			$this->add_control(
				'ic_etiquette_bgcolor',
				array(
					'label'     => esc_html__( 'Background color', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#919ca7',
					'selectors' => array(
						'{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'ic_etiquette_typography',
					'label'    => esc_html__( 'Typography', 'eac-components' ),
					'selector' => '{{WRAPPER}} .b-diff__title_before, {{WRAPPER}} .b-diff__title_after',
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

		if ( empty( $settings['ic_img_content_original']['url'] ) || empty( $settings['ic_img_content_modified']['url'] ) ) {
			return;
		}

		$id = 'a' . uniqid();
		$this->add_render_attribute( 'data_diff', 'class', 'images-comparison' );
		$this->add_render_attribute( 'data_diff', 'data-diff', esc_attr( $id ) );
		$this->add_render_attribute( 'data_diff', 'data-settings', $this->get_settings_json( $id ) );
		?>
		<div class='eac-images-comparison'>
			<div <?php $this->print_render_attribute_string( 'data_diff' ); ?>>
				<?php $this->render_galerie(); ?>
			</div>
		</div>

		<?php
	}

	protected function render_galerie(): void {
		$settings = $this->get_settings_for_display();
		?>
		<div>
			<?php
			$image_original = Group_Control_Image_Size::get_attachment_image_html( $settings, 'ic_image_size', 'ic_img_content_original' );
			$image_original = preg_replace( '/<img\b/i', '<img role="presentation" aria-hidden="true"', $image_original, 1 );
			$image_original = preg_replace( '/\s+title=(["\']).*?\1/i', '', $image_original );
			if ( preg_match( '/\salt=(["\'])(.*?)\1/i', $image_original ) ) {
				$image_original = preg_replace( '/\salt=(["\'])(.*?)\1/i', ' alt=""', $image_original, 1 );
			}
			echo $image_original; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div>
			<?php
			$image_modified = Group_Control_Image_Size::get_attachment_image_html( $settings, 'ic_image_size', 'ic_img_content_modified' );
			$image_modified = preg_replace( '/<img\b/i', '<img role="presentation" aria-hidden="true"', $image_modified, 1 );
			$image_modified = preg_replace( '/\s+title=(["\']).*?\1/i', '', $image_modified );
			if ( preg_match( '/\salt=(["\'])(.*?)\1/i', $image_modified ) ) {
				$image_modified = preg_replace( '/\salt=(["\'])(.*?)\1/i', ' alt=""', $image_modified, 1 );
			}
			echo $image_modified; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
	 * @access  protected
	 */
	protected function get_settings_json( $ordre ): string {
		$module_settings = $this->get_settings_for_display();

		$settings = array(
			'data_diff'        => '[data-diff=' . $ordre . ']',
			'data_title_left'  => esc_html( $module_settings['ic_img_name_original'] ),
			'data_title_right' => esc_html( $module_settings['ic_img_name_modified'] ),
		);

		return wp_json_encode( $settings );
	}

	protected function content_template(): void {}
}
