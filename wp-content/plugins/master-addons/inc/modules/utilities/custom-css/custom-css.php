<?php

namespace MasterAddons\Modules\Utilities;

use \Elementor\Controls_Manager;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 1/2/20
 */

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly.

class CustomCss
{

	/*
	 * Instance of this class
	 */
	private static $instance = null;


	public function __construct()
	{
		// Add new controls to advanced tab globally
		add_action("elementor/element/after_section_end", array($this, 'jltma_add_section_custom_css_controls'), 25, 3);

		// Render the custom CSS
		add_action('elementor/element/parse_css', array($this, 'jltma_add_post_css'), 10, 2);

		// Security: sanitize Custom CSS on save. The control is registered only for
		// edit_posts users, but Elementor saves can be crafted directly via admin-ajax
		// (elementor_ajax), so the stored value must be cleaned of tag-breakout vectors
		// (e.g. "</style><script>") before it is persisted and later added to the
		// (possibly inline) stylesheet.
		add_filter('elementor/document/save/data', array($this, 'jltma_sanitize_custom_css_on_save'), 10, 2);
	}

	/**
	 * Sanitize the 'custom_css' element settings in the data being saved.
	 *
	 * @param array $data     Document data ('settings' + 'elements').
	 * @param mixed $document  Elementor document instance.
	 * @return array
	 */
	public function jltma_sanitize_custom_css_on_save($data, $document)
	{
		if (isset($data['settings']['custom_css'])) {
			$data['settings']['custom_css'] = $this->jltma_sanitize_css($data['settings']['custom_css']);
		}

		if (!empty($data['elements']) && is_array($data['elements'])) {
			$data['elements'] = $this->jltma_sanitize_element_custom_css($data['elements']);
		}

		return $data;
	}

	/**
	 * Recursively sanitize the 'custom_css' setting in element data.
	 *
	 * @param array $elements
	 * @return array
	 */
	private function jltma_sanitize_element_custom_css($elements)
	{
		foreach ($elements as &$element) {
			if (isset($element['settings']['custom_css'])) {
				$element['settings']['custom_css'] = $this->jltma_sanitize_css($element['settings']['custom_css']);
			}
			if (!empty($element['elements']) && is_array($element['elements'])) {
				$element['elements'] = $this->jltma_sanitize_element_custom_css($element['elements']);
			}
		}
		unset($element);

		return $elements;
	}

	/**
	 * Strip tag-breakout and active vectors from a CSS string. Valid CSS never needs
	 * HTML tags, PHP tags, expression(), @import or javascript:/behavior: — removing
	 * them prevents breaking out of an inline <style> block (XSS) while leaving normal
	 * declarations intact.
	 *
	 * @param string $css
	 * @return string
	 */
	private function jltma_sanitize_css($css)
	{
		if (!is_string($css) || '' === $css) {
			return '';
		}

		$css = str_replace(chr(0), '', $css);

		// PHP tags.
		$css = preg_replace('/<\?php/i', '', $css);
		$css = str_replace(array('<?=', '<?', '?>'), '', $css);

		// Any HTML tag (kills </style>/<script> breakout).
		$css = preg_replace('#</?[a-z!][^>]*>#i', '', $css);

		// Dangerous CSS constructs.
		$css = preg_replace('/expression\s*\(/i', '', $css);
		$css = preg_replace('/(javascript|vbscript)\s*:/i', '', $css);
		$css = preg_replace('/behavior\s*:/i', '', $css);
		$css = preg_replace('/@import\b/i', '', $css);

		return $css;
	}



	public function jltma_add_section_custom_css_controls($widget, $section_id, $args)
	{

		if ('section_custom_css_pro' !== $section_id) {
			return;
		}

		// Skip when Elementor Pro is active — Pro provides its own Custom CSS
		if (defined('ELEMENTOR_PRO_VERSION')) {
			return;
		}

		if (!current_user_can('edit_posts')) {
			return;
		}

			$widget->start_controls_section(
				'jltma_custom_css_section',
				array(
					'label' => __(' Custom CSS', 'master-addons') . JLTMA_EXTENSION_BADGE,
					'tab' => Controls_Manager::TAB_ADVANCED
				)
			);

			$widget->add_control(
				'custom_css',
				array(
					'type' => Controls_Manager::CODE,
					'label' => __('Custom CSS', 'master-addons'),
					'label_block' => true,
					'language' => 'css'
				)
			);
			ob_start(); ?>
			<pre>
																																																															Examples:
																																																															// To target main element
																																																															selector { color: red; }
																																																															// For child element
																																																															selector .child-element{ margin: 10px; }
																																																															</pre>
			<?php
			$output = ob_get_clean();

			$widget->add_control(
				'custom_css_description',
				array(
					'raw' => __('Use "selector" keyword to target wrapper element.', 'master-addons') . $output,
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor',
					'separator' => 'none'
				)
			);

			$widget->end_controls_section();

	}



	public function jltma_add_post_css($post_css, $element)
	{
		if (defined('ELEMENTOR_PRO_VERSION')) {
			return;
		}

		$element_settings = $element->get_settings();

		if (empty($element_settings['custom_css'])) {
			return;
		}

		$css = trim($element_settings['custom_css']);

		// Defense in depth: strip tag-breakout/active vectors before adding the CSS to
		// the (possibly inline) stylesheet — covers values saved before this fix.
		$css = $this->jltma_sanitize_css($css);

		if (empty($css)) {
			return;
		}

		// Replace 'selector' keyword with the element's unique selector
		$css = str_replace('selector', $post_css->get_element_unique_selector($element), $css);

		// Add a css comment for debugging
		$css = sprintf(
			'/* Start custom CSS for %s, class: %s */ %s /* End custom CSS */',
			$element->get_name(),
			$element->get_unique_selector(),
			$css
		);

		$post_css->get_stylesheet()->add_raw_css($css);
	}



	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

CustomCss::get_instance();
