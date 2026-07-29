<?php

namespace MasterAddons\Addons;

use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Admin\Config;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 1/1/20
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Mailchimp extends Master_Widget
{
	use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

	public function get_name()
	{
		return 'ma-el-mailchimp';
	}
	public function get_title()
	{
		return __('Mailchimp', 'master-addons' );
	}

	public function get_icon()
	{
		return 'jltma-icon ' . Config::get_addon_icon('ma-mailchimp');
	}

	public function jltma_get_forms()
	{

		$options = array(0 => __('Select Form', 'master-addons' ));

		if (!function_exists('mc4wp_get_forms')) {
			return $options;
		}
		$forms = mc4wp_get_forms();
		foreach ($forms as $form) {
			$options[$form->ID] = $form->name;
		}

		return $options;
	}

	protected function register_controls()
	{

		/*
			 * Content Tab
			 */
		$this->start_controls_section(
			'jltma_mailchimp_form_section',
			[
				'label'      => __('Form', 'master-addons' )
			]
		);

		//			You can edit your sign-up form in the Mailchimp for WordPress form settings.

		$this->add_control(
			'jltma_mailchimp_form_type',
			[
				'label'       => __('Form Type', 'master-addons' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'default',
				'options'     => array(
					'default' => __('Defaults', 'master-addons' ),
					'custom'  => __('Custom', 'master-addons' )
				)
			]
		);

		$this->add_control(
			'jltma_mailchimp_form_id',
			array(
				'label'       => __('MailChimp Sign-Up Form', 'master-addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 0,
				'options'     => $this->jltma_get_forms(),
				'condition'   => array(
					'jltma_mailchimp_form_type' => array('default')
				)
			)
		);

		$this->add_control(
			'jltma_mailchimp_html',
			array(
				'label'       => __('Custom Form', 'master-addons' ),
				'type'        => Controls_Manager::CODE,
				'language'    => 'html',
				'description' => __('Enter your custom form markup', 'master-addons' ),
				'condition'   => array(
					'jltma_mailchimp_form_type' => array('custom')
				)
			)
		);

		$this->end_controls_section();

		// Help Docs section (links from config.php)
		$this->jltma_help_docs();

		$this->upgrade_to_pro_message();

	}

	public function jltma_render_custom_form($content)
	{
		$settings = $this->get_settings_for_display();

		if (!empty($settings['jltma_mailchimp_html'])) {
			$content = $settings['jltma_mailchimp_html'];
		}
		return $content;
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		// Check whether required resources are available
		if (!function_exists('mc4wp_show_form')) {
			Helper::jltma_elementor_plugin_missing_notice(array(
				'title'       => esc_html__('MailChimp for WordPress is Not Activated!', 'master-addons'),
				'description' => esc_html__('To use this widget, please install and activate the "MC4WP: Mailchimp for WordPress" plugin.', 'master-addons'),
			));
			return;
		}

		if ($settings['jltma_mailchimp_form_type'] === 'custom') {
			add_filter('mc4wp_form_content', array($this, 'jltma_render_custom_form'), 10, 1);
			$settings['jltma_mailchimp_form_id'] = 0;
		} elseif (get_post_type($settings['jltma_mailchimp_form_id']) !== 'mc4wp-form') {
			$settings['jltma_mailchimp_form_id'] = 0;
		}

		echo '<div class="jltma-mailchimp">';
		mc4wp_show_form($settings['jltma_mailchimp_form_id']);
		echo '</div>';
	}
}
