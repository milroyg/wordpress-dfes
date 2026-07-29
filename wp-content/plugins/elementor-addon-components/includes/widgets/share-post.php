<?php
/**
 * Class: Share_Post_Widget
 * Name: Partager
 * Slug: eac-addon-reseaux-sociaux
 *
 * Description: Share_Post_Widget affiche une liste de réseaux sociaux
 * sur une page, qui peut être partager
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

class Share_Post_Widget extends Widget_Base {

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'reseaux-sociaux';

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
		return array( 'eac-share-post' );
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
		return array( 'eac-share-post' );
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

		$this->start_controls_section(
			'rs_share_select',
			array(
				'label' => esc_html__( 'Social medias', 'eac-components' ),
			)
		);
			$this->add_control(
				'rs_share_with',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => esc_html__( 'Share this post on social medias', 'eac-components' ),
				)
			);

			$this->add_control(
				'rs_item_facebook',
				array(
					'label'        => 'Facebook',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'rs_item_twitter',
				array(
					'label'        => 'Twitter',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'rs_item_google_plus',
				array(
					'label'        => 'Google+',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'rs_item_linkedin',
				array(
					'label'        => 'Linkedin',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'rs_item_odnoklassniki',
				array(
					'label'        => 'Odnoklassniki',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'rs_item_pinterest',
				array(
					'label'        => 'Pinterest',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'rs_item_reddit',
				array(
					'label'        => 'Reddit',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'rs_item_telegram',
				array(
					'label'        => 'Telegram',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'rs_item_tumblr',
				array(
					'label'        => 'Tumblr',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'rs_item_whatsapp',
				array(
					'label'        => 'Whatsapp',
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'rs_item_mail',
				array(
					'label'        => esc_html__( 'E-mail', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'rs_share_settings',
			array(
				'label' => esc_html__( 'Settings', 'eac-components' ),
			)
		);

			$this->add_control(
				'rs_share_place',
				array(
					'label'   => esc_html__( 'Position', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'top-left',
					'options' => array(
						'top-left'  => esc_html__( 'Left', 'eac-components' ),
						'top-right' => esc_html__( 'Right', 'eac-components' ),
					),
				)
			);

			$this->add_control(
				'rs_share_text',
				array(
					'label'       => esc_html__( 'Text', 'eac-components' ),
					'description' => esc_html__( 'Shared text', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Share with', 'eac-components' ),
					'placeholder' => esc_html__( 'Share with', 'eac-components' ),
				)
			);

			$this->add_control(
				'rs_item_target',
				array(
					'label'        => esc_html__( 'Open on a new window', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'eac-components' ),
					'label_off'    => esc_html__( 'No', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
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

		$this->add_render_attribute( 'rs_items_list', 'class', 'rs-items-list' );
		$this->add_render_attribute( 'rs_items_list', 'data-settings', $this->get_settings_json() );

		?>
		<div class="eac-reseaux-sociaux">
			<div <?php $this->print_render_attribute_string( 'rs_items_list' ); ?>></div>
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
	protected function get_settings_json(): string {
		$module_settings = $this->get_settings_for_display();
		$networks        = array();
		if ( 'yes' === $module_settings['rs_item_facebook'] ) {
			$networks[] = 'facebook';
		}
		if ( 'yes' === $module_settings['rs_item_twitter'] ) {
			$networks[] = 'twitter';
		}
		if ( 'yes' === $module_settings['rs_item_google_plus'] ) {
			$networks[] = 'google-plus';
		}
		if ( 'yes' === $module_settings['rs_item_linkedin'] ) {
			$networks[] = 'linkedin';
		}
		if ( 'yes' === $module_settings['rs_item_odnoklassniki'] ) {
			$networks[] = 'odnoklassniki';
		}
		if ( 'yes' === $module_settings['rs_item_pinterest'] ) {
			$networks[] = 'pinterest';
		}
		if ( 'yes' === $module_settings['rs_item_reddit'] ) {
			$networks[] = 'reddit';
		}
		if ( 'yes' === $module_settings['rs_item_telegram'] ) {
			$networks[] = 'telegram';
		}
		if ( 'yes' === $module_settings['rs_item_tumblr'] ) {
			$networks[] = 'tumblr';
		}
		if ( 'yes' === $module_settings['rs_item_whatsapp'] ) {
			$networks[] = 'whatsapp';
		}
		if ( 'yes' === $module_settings['rs_item_mail'] ) {
			$networks[] = 'mail';
		}

		$settings = array(
			'data_place'   => $module_settings['rs_share_place'],
			'data_text'    => esc_html( $module_settings['rs_share_text'] ),
			'data_buttons' => $networks,
			'data_popup'   => 'yes' === $module_settings['rs_item_target'] ? true : false,
		);

		return wp_json_encode( $settings );
	}

	protected function content_template(): void {}
}
