<?php
/**
 * Enhanced Elementor Widget for Location Weather
 *
 * @package     Location_Weather
 * @subpackage  Location_Weather/Admin/PageBuilders
 * @since       3.2.0
 */

namespace ShapedPlugin\Weather\Admin\PageBuilders\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use ShapedPlugin\Weather\Admin\PageBuilders\Base\Base_Page_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor widget for Location Weather saved templates.
 *
 * @since 3.2.0
 */
class Elementor_Widget extends Widget_Base {

	use Base_Page_Builder;

	/**
	 * Get widget name.
	 *
	 * @since 3.2.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'location_weather_saved_template';
	}

	/**
	 * Get widget title.
	 *
	 * @since 3.2.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Location Weather Saved Template', 'location-weather' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 3.2.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'splwp-icon-lw-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 3.2.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'basic' );
	}

	/**
	 * Enqueue scripts for editor preview.
	 *
	 * @since 3.2.0
	 *
	 * @return array Script handles.
	 */
	public function get_script_depends() {
		return array(
			'splw-scripts',
			'splw-swiper-scripts',
		);
	}

	/**
	 * Enqueue styles for editor preview.
	 *
	 * @since 3.2.0
	 *
	 * @return array Style handles.
	 */
	public function get_style_depends() {
		return array(
			'splw-styles',
			'splw-fontello',
			'splw-swiper-styles',
		);
	}

	/**
	 * Controls register.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Settings', 'location-weather' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'splw_saved_template',
			array(
				'label'       => __( 'Saved Template', 'location-weather' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => '0',
				'options'     => $this->get_saved_templates_list(),
			)
		);

		// Edit This Template button.
		$this->add_control(
			'splw_edit_template',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => $this->get_edit_template_button(),
				'content_classes' => 'splw-elementor-template-actions',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 *
	 * @since 3.2.0
	 *
	 * @return void
	 */
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$splw_template = $settings['splw_saved_template'];
		$template_id   = (int) $splw_template;

		if ( empty( $template_id ) || 0 === $template_id ) {
			echo '<div style="
				text-align: center;
				padding: 20px;
				border: 2px dashed #ccc;
				color: #999;
				font-size: 14px;
			">
				' . esc_html__( 'Please Select a Saved Template', 'location-weather' ) . '
			</div>';
			return;
		}

		// Get template post.
		$template_post = get_post( $template_id );
		if ( ! $template_post || 'publish' !== $template_post->post_status ) {
			echo '<div style="
				text-align: center;
				padding: 20px;
				border: 2px dashed #ccc;
				color: #999;
				font-size: 14px;
			">
				' . esc_html__( 'Template not found or not published.', 'location-weather' ) . '
			</div>';
			return;
		}

		$content = $template_post->post_content;
		if ( empty( $content ) ) {
			echo '<div style="
				text-align: center;
				padding: 20px;
				border: 2px dashed #ccc;
				color: #999;
				font-size: 14px;
			">
				' . esc_html__( 'Template content is empty.', 'location-weather' ) . '
			</div>';
			return;
		}

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			// In Elementor editor, enqueue CSS for preview.
			$this->enqueue_editor_css( $template_id, $content );

			echo '<div class="splw-elementor-weather-wrapper" data-builder-template-id="' . esc_attr( $template_id ) . '">';

			echo do_shortcode( '[location_weather id="' . absint( $template_id ) . '"]' );

			echo '</div>';
		} else {
			// On frontend, just render the shortcode.
			echo do_shortcode( '[location_weather id="' . absint( $template_id ) . '"]' );
		}
	}

	/**
	 * Enqueue CSS for Elementor editor.
	 *
	 * @since 3.2.0
	 *
	 * @param int    $template_id Template post ID.
	 * @param string $content     Template content.
	 * @return void
	 */
	protected function enqueue_editor_css( $template_id, $content = '' ) {
		// Prevent duplicate CSS generation for the same template in current request.
		static $generated_templates = array();
		if ( isset( $generated_templates[ $template_id ] ) ) {
			return;
		}

		$upload_dir = wp_upload_dir();
		$css_file   = trailingslashit( $upload_dir['basedir'] ) . 'spl-weather-css/spl-weather-' . $template_id . '.css';
		$css_url    = trailingslashit( $upload_dir['baseurl'] ) . 'spl-weather-css/spl-weather-' . $template_id . '.css';

		// Mark this template as processed.
		$generated_templates[ $template_id ] = true;

		if ( file_exists( $css_file ) ) {
			// Echo CSS link for Elementor editor.
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<link rel="stylesheet" href="' . esc_url( $css_url . '?v=' . LOCATION_WEATHER_VERSION ) . '">'; // phpcs:ignore
			}

			$font_lists = get_post_meta( $template_id, '_spl_weather_fonts', true );
		} else {
			// Fallback: CSS from post meta.
			$css = get_post_meta( $template_id, '_spl_weather_css', true );
			if ( ! empty( $css ) ) {
				// Echo inline style for Elementor editor.
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					echo '<style id="splw-elementor-dynamic-css-' . esc_attr( $template_id ) . '">' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
			$font_lists = get_post_meta( $template_id, '_spl_weather_fonts', true );
		}

		// Enqueue Google Fonts.
		if ( ! empty( $font_lists ) && is_array( $font_lists ) ) {
			$font_lists = array_unique( $font_lists );

			// Echo Google Fonts link for Elementor editor.
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				foreach ( $font_lists as $font ) {
					if ( ! empty( $font ) ) {
						echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=' . esc_attr( $font ) . '">'; // phpcs:ignore
					}
				}
			}
		}
	}

	/**
	 * Get edit template and add new template buttons HTML.
	 *
	 * @since 3.2.0
	 *
	 * @return string Buttons HTML.
	 */
	protected function get_edit_template_button() {
		$template_url = admin_url( 'edit.php?post_type=location_weather&page=splw_admin_dashboard#saved_templates' );

		$new_template_url = admin_url( 'post-new.php?post_type=spl_weather_template&splwblock_inserter' );

		ob_start();
		?>

		<div class="splw-elementor-template-buttons">
			<a class="splw-edit-template-btn" href="<?php echo esc_url( $template_url ); ?>" style="color:#fff; background-color:#3e3e40; padding:12px 24px; border-radius:4px; display:inline-block; font-size: 14px" onmouseover="this.style.backgroundColor='#4b4b4d'" onmouseout="this.style.backgroundColor='#3e3e40'">
				<span style="display:inline-block; transform: rotate(70deg); margin-right: 4px">✎</span>
				<span><?php echo esc_html__( 'Edit This Template', 'location-weather' ); ?></span>
			</a>
			<a href="<?php echo esc_url( $new_template_url ); ?>" class="splw-add-template-btn" style="color:#fff; background-color:#F26C0D; padding: 10px 23px; border-radius:4px; display:inline-block; margin-top: 15px; font-size: 14px" onmouseover="this.style.backgroundColor='#f27b26'" onmouseout="this.style.backgroundColor='#F26C0D'">
				<span style="display:inline-block; font-size: 18px; margin-right: 4px;">+</span>
				<span><?php echo esc_html__( 'Add New Template', 'location-weather' ); ?></span>
			</a>
		</div>
		<?php
		return ob_get_clean();
	}
}
