<?php

namespace MasterAddons\Inc\Modules\Dynamic\Conditions\ContentProtection;

if (!defined('ABSPATH')) exit;

// Elementor classes used in this file are imported below (before the classes).
use MasterAddons\Inc\Classes\Helper;

class JLTMA_Protected_Content
{

	protected static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct()
	{
		$this->init_hooks();
	}

	public static function init_hooks()
	{
		add_action('elementor/element/section/section_advanced/after_section_end', [__CLASS__, 'add_section']);
		add_action('elementor/element/common/_section_style/after_section_end', [__CLASS__, 'add_section'], 1);
		add_filter('elementor/frontend/widget/should_render', [__CLASS__, 'add_filter'], 10, 3);
		add_filter('elementor/frontend/section/should_render', [__CLASS__, 'add_filter'], 10, 3);
		add_action('elementor/frontend/widget/before_render', [__CLASS__, 'before_render']);
		add_action('elementor/frontend/section/before_render', [__CLASS__, 'before_render']);
	}

	public static function add_filter($bool, $element)
	{
		$settings = $element->get_settings();
		if ($settings['jltma_protected_content_enable']) {
			if (($settings['jltma_protected_content_type'] == 'user_role') && (!empty($settings['jltma_protected_content_roles']))) {
				if (is_user_logged_in()) {
					$user = wp_get_current_user();
					$user_role = (array) $user->roles;
					if (array_intersect($user_role, $settings['jltma_protected_content_roles'])) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} elseif ($settings['jltma_protected_content_type'] == 'capability') {
				if (is_user_logged_in()) {
					if (current_user_can($settings['jltma_protected_content_capability'])) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} elseif ($settings['jltma_protected_content_type'] == 'login_status') {
				if ((is_user_logged_in()) && ($settings['jltma_protected_content_login_status'] == 'logged_in')) {
					return true;
				} elseif ((!is_user_logged_in()) && ($settings['jltma_protected_content_login_status'] == 'logged_out')) {
					return true;
				} else {
					return false;
				}
			} elseif ($settings['jltma_protected_content_type'] == 'password') {
				// Public front-end content gate: read-only password comparison with no
				// state change, so a nonce is not applicable (mirrors core post-password).
				$submitted_password = isset($_GET['password']) ? sanitize_text_field(wp_unslash($_GET['password'])) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if (null === $submitted_password) {
					return false;
				} elseif ($submitted_password !== $settings['jltma_protected_content_password']) {
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	public static function before_render(Element_Base $element)
	{
		$settings = $element->get_settings();
		if ($settings['jltma_protected_content_enable']) {
			// Public front-end content gate: read-only password comparison, no state
			// change, so a nonce is not applicable (mirrors core post-password).
			$submitted_password = isset($_GET['password']) ? sanitize_text_field(wp_unslash($_GET['password'])) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if (null === $submitted_password || $submitted_password !== $settings['jltma_protected_content_password']) {
				if ($settings['jltma_protected_content_type'] == 'password') { ?>
					<form class="tmea-password-protection" action="<?php the_permalink(); ?>" style="display:flex;flex-direction:column;width:100%;max-width:<?php echo esc_attr($settings['jltma_protected_content_password_width']['size']) . esc_attr($settings['jltma_protected_content_password_width']['unit']); ?>;margin:<?php echo esc_attr($settings['jltma_protected_content_password_spacing']['size']) . esc_attr($settings['jltma_protected_content_password_spacing']['unit']); ?> auto;">
						<?php
						if ($settings['jltma_protected_content_password_desc']) {
							echo '<label class="' . esc_attr($settings['jltma_protected_content_password_label_class']) . '">' . esc_html($settings['jltma_protected_content_password_desc']) . '</label>';
						} ?>
						<div class="tmea-password-protection-inner" style="display:flex;align-items:stretch;">
							<input id="password" name="password" type="text" class="<?php echo esc_attr($settings['jltma_protected_content_password_input_class']); ?>" placeholder="<?php esc_attr_e('Enter Password', 'master-addons'); ?>" maxlength="80" />
							<button id="submit" type="submit" class="<?php echo esc_attr($settings['jltma_protected_content_password_btn_class']); ?>"><?php esc_html_e('Submit', 'master-addons'); ?></button>
						</div>
					</form>
<?php }
			}
		}
	}

	public static function add_section(Element_Base $element)
	{

		$element->start_controls_section(
			'_section_protected_content',
			[
				'label' => esc_html__('TMEA Protected Content', 'master-addons'),
				'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'jltma_protected_content_enable',
			[
				'label' => esc_html__('Protect Content', 'master-addons'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'frontend_available' => true
			]
		);

		$element->add_control(
			'jltma_protected_content_type',
			[
				'label' => esc_html__('Protection Type', 'master-addons'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'login_status' => esc_html__('Login Status', 'master-addons'),
					'user_role' => esc_html__('User Role', 'master-addons'),
					'capability' => esc_html__('User Capability', 'master-addons'),
					'password' => esc_html__('Password', 'master-addons')
				],
				'default' => 'login_status',
				'condition' => [
					'jltma_protected_content_enable' => 'yes'
				]
			]
		);

		$element->add_control(
			'jltma_protected_content_login_status',
			[
				'label' => esc_html__('Login Status', 'master-addons'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'logged_in' => esc_html__('Logged In', 'master-addons'),
					'logged_out' => esc_html__('Logged Out', 'master-addons')
				],
				'default' => 'logged_in',
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'login_status',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_roles',
			[
				'label' => esc_html__('Roles (Required)', 'master-addons'),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => 'true',
				'description' => '<a href="https://wordpress.org/support/article/roles-and-capabilities/" target="_blank">' . esc_html__('Learn more about roles and capabilities', 'master-addons') . '</a>',
				'multiple' => true,
				'default' => '',
				'options' => Helper::jltma_get_user_roles(),
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'user_role',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_capability',
			[
				'label' => esc_html__('Capability (Required)', 'master-addons'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'manage_options',
				'description' => '<a href="https://wordpress.org/support/article/roles-and-capabilities/" target="_blank">' . esc_html__('Learn more about roles and capabilities', 'master-addons') . '</a>',
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'capability',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_password',
			[
				'label' => esc_html__('Password (Required)', 'master-addons'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '123456',
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_password_desc',
			[
				'label' => esc_html__('Description', 'master-addons'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_hr_1',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->add_responsive_control(
			'jltma_protected_content_password_width',
			[
				'label' => esc_html__('Maximum Form Width', 'master-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'rem' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 600,
				],
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->add_responsive_control(
			'jltma_protected_content_password_spacing',
			[
				'label' => esc_html__('Spacing', 'master-addons'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 80,
				],
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_password_input_class',
			[
				'label' => esc_html__('Input Class', 'master-addons'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'form-control',
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_password_btn_class',
			[
				'label' => esc_html__('Button Class', 'master-addons'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'btn btn-primary',
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->add_control(
			'jltma_protected_content_password_label_class',
			[
				'label' => esc_html__('Label Class', 'master-addons'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions'   => [
					'relation' => 'and',
					'terms' => [
						[
							'name'  => 'jltma_protected_content_enable',
							'value' => 'yes',
						],
						[
							'name'  => 'jltma_protected_content_type',
							'value' => 'password',
						]
					]
				],
			]
		);

		$element->end_controls_section();
	}
}

JLTMA_Protected_Content::instance();




















namespace MasterAddons\Modules;

/**
 * Content protection class
 *
 * @package MasterAddons\Modules
 */


use \Elementor\Controls_Manager;
use \Elementor\Frontend;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use MasterAddons\Inc\Classes\Helper;
use \MasterAddons\Inc\Classes\Extension_Prototype;

/**
 * Class Content_Protection
 *
 * @package MasterAddons\Modules
 */
if (!class_exists('MasterAddons\Modules\Extension_Content_Protection')) {
class Extension_Content_Protection extends Extension_Prototype
{

	private static $instance = null;
	public  $name            = 'Content Protection';
	/**
	 * Content_Protection constructor.
	 */
	public function __construct()
	{
		add_action('elementor/element/common/_section_style/after_section_end', array($this, 'register_controls'), 10);
		add_action('elementor/widget/render_content', array($this, 'render_content'), 10, 2);
	}

	/**
	 * Register Content Protection Controls.
	 *
	 * @param Object $element Elementor instance.
	 */
	public function register_controls($element)
	{
		$element->start_controls_section(
			'jltma_content_protection_section',
			[
				'label' => esc_html__('Content Protection', 'master-addons' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'jltma_content_protection',
			[
				'label'        => __('Enable Content Protection', 'master-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __('Yes', 'master-addons' ),
				'label_off'    => __('No', 'master-addons' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'jltma_content_protection_type',
			[
				'label'       => esc_html__('Protection Type', 'master-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'role'             => esc_html__('User role', 'master-addons' ),
					'password'         => esc_html__('Password protected', 'master-addons' ),
					'logged-in'        => esc_html__('User is logged', 'master-addons' ),
					'start-end-date'   => esc_html__('Start / End date', 'master-addons' ),
					'days-of-the-week' => esc_html__('Days of the week', 'master-addons' ),
				],
				'default'   => 'role',
				'condition' => [
					'jltma_content_protection' => 'yes',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_role',
			[
				'label'       => __('Select Roles', 'master-addons' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_user_roles(),
				'condition'   => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'role',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password',
			[
				'label'      => esc_html__('Set Password', 'master-addons' ),
				'type'       => Controls_Manager::TEXT,
				'input_type' => 'password',
				'condition'  => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_placeholder',
			[
				'label'     => esc_html__('Input Placeholder', 'master-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Enter Password',
				'condition' => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_submit_btn_txt',
			[
				'label'     => esc_html__('Submit Button Text', 'master-addons' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Submit',
				'condition' => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$date_format  = get_option('date_format');
		$time_format  = get_option('time_format');
		$current_time = gmdate($date_format . ' ' . $time_format);
		/* translators: %s is the current time */
		$description = sprintf(__('Current time: %s', 'master-addons' ), $current_time);

		$element->add_control(
			'server_time_note',
			[
				'type'       => Controls_Manager::RAW_HTML,
				'raw'        => $description,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'jltma_content_protection_type',
							'operator' => '===',
							'value'    => 'start-end-date',
						],
						[
							'name'     => 'jltma_content_protection_type',
							'operator' => '===',
							'value'    => 'days-of-the-week',
						],
					],
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_period_date',
			[
				'label'     => __('Period', 'master-addons' ),
				'type'      => Controls_Manager::DATE_TIME,
				'condition' => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'start-end-date',
				],
				'picker_options' => [
					'mode' => 'range',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_days_of_week',
			[
				'label'       => __('Every', 'master-addons' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_days_of_week(),
				'condition'   => [
					'jltma_content_protection'      => 'yes',
					'jltma_content_protection_type' => 'days-of-the-week',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_days_of_week_time_from',
			[
				'label'     => __('From', 'master-addons' ),
				'type'      => Controls_Manager::DATE_TIME,
				'condition' => [
					'jltma_content_protection'               => 'yes',
					'jltma_content_protection_type'          => 'days-of-the-week',
					'jltma_content_protection_days_of_week!' => '',
				],
				'picker_options' => [
					'noCalendar' => true,
					'enableTime' => true,
					'dateFormat' => 'h:i K',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_days_of_week_time_to',
			[
				'label'     => __('To', 'master-addons' ),
				'type'      => Controls_Manager::DATE_TIME,
				'condition' => [
					'jltma_content_protection'                         => 'yes',
					'jltma_content_protection_type'                    => 'days-of-the-week',
					'jltma_content_protection_days_of_week!'           => '',
					'jltma_content_protection_days_of_week_time_from!' => '',
				],
				'picker_options' => [
					'noCalendar' => true,
					'enableTime' => true,
					'dateFormat' => 'h:i K',
				],
			]
		);

		$element->start_controls_tabs(
			'jltma_content_protection_tabs',
			[
				'condition' => [
					'jltma_content_protection' => 'yes',
				],
			]
		);

		$element->start_controls_tab(
			'jltma_content_protection_tab_message',
			[
				'label' => __('Message', 'master-addons' ),
			]
		);

		$element->add_control(
			'jltma_content_protection_message_type',
			[
				'label'       => esc_html__('Message Type', 'master-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__('Set a message or a saved template when the content is protected.', 'master-addons' ),
				'options'     => [
					'none'     => esc_html__('None', 'master-addons' ),
					'text'     => esc_html__('Message', 'master-addons' ),
					'template' => esc_html__('Saved Templates', 'master-addons' ),
				],
				'default' => 'text',
			]
		);

		$element->add_control(
			'jltma_content_protection_message_text',
			[
				'label'   => esc_html__('Public Text', 'master-addons' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__('You do not have permission to see this content.', 'master-addons' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_message_template',
			[
				'label'     => __('Choose Template', 'master-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => Helper::get_page_template_options(),
				'condition' => [
					'jltma_content_protection_message_type' => 'template',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'jltma_content_protection_tab_style',
			[
				'label' => __('Style', 'master-addons' ),
			]
		);

		$element->add_control(
			'jltma_content_protection_message_styles',
			[
				'label'     => __('Message', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_message_text_color',
			[
				'label'     => esc_html__('Text Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .neb-protected-content-message' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'jltma_content_protection_message_text_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector'  => '{{WRAPPER}} .neb-protected-content-message, {{WRAPPER}} .protected-content-error-msg',
				'condition' => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_message_text_alignment',
			[
				'label'       => esc_html__('Text Alignment', 'master-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => Helper::jltma_content_alignment(),
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .neb-protected-content-message, {{WRAPPER}} .protected-content-error-msg' => 'text-align: {{VALUE}};',
				],
				'condition'   => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_message_text_padding',
			[
				'label'      => esc_html__('Padding', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .neb-protected-content-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'jltma_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_styles',
			[
				'label'     => __('Password Field', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_width',
			[
				'label' => esc_html__('Input Width', 'master-addons' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'width: {{SIZE}}px;',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_input_alignment',
			[
				'label'       => esc_html__('Input Alignment', 'master-addons' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => Helper::jltma_content_alignment(),
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .neb-password-protected-content-fields > form' => 'justify-content: {{VALUE}};',
				],
				'condition'   => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_password_input_padding',
			[
				'label'      => esc_html__('Padding', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'jltma_content_protection_password_input_margin',
			[
				'label'      => esc_html__('Margin', 'master-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_border_radius',
			[
				'label' => esc_html__('Border Radius', 'master-addons' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'border-radius: {{SIZE}}px;',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_input_color',
			[
				'label'     => esc_html__('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_password_input_bg_color',
			[
				'label'     => esc_html__('Background Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'jltma_content_protection_password_input_border',
				'label'     => esc_html__('Border', 'master-addons' ),
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'jltma_content_protection_password_input_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_input_styles_hover',
			[
				'label'     => __('Password Field Hover', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_protected_content_password_input_hover_color',
			[
				'label'     => esc_html__('Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_protected_content_password_input_hover_bg_color',
			[
				'label'     => esc_html__('Background Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'neb_protected_content_password_input_hover_border',
				'label'     => esc_html__('Border', 'master-addons' ),
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'neb_protected_content_password_input_hover_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_styles',
			[
				'label'     => __('Submit Button', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_color',
			[
				'label'     => esc_html__('Text Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_bg_color',
			[
				'label'     => esc_html__('Background Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit' => 'background: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_border',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_box_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_styles_hover',
			[
				'label'     => __('Submit Button Hover', 'master-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_hover_text_color',
			[
				'label'     => esc_html__('Text Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'jltma_content_protection_submit_button_hover_bg_color',
			[
				'label'     => esc_html__('Background Color', 'master-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover' => 'background: {{VALUE}};',
				],
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_hover_border',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'jltma_content_protection_submit_button_hover_box_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover',
				'condition' => [
					'jltma_content_protection_type' => 'password',
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();
	}

	/**
	 * Render Content Protection Message.
	 *
	 * @param array $settings Widget Settings.
	 *
	 * @return string
	 */
	protected function render_message($settings)
	{
		$html = '<div class="neb-protected-content-message">';

		if ($settings['jltma_content_protection_message_type'] === 'text') {
			$html .= '<div class="neb-protected-content-message-text">' . esc_attr($settings['jltma_content_protection_message_text']) . '</div>';
		} elseif ($settings['jltma_content_protection_message_type'] === 'template') {
			if (!empty($settings['jltma_content_protection_message_template'])) {
				$template_id = $settings['jltma_content_protection_message_template'];
				$frontend    = new Frontend();

				$html .= $frontend->get_builder_content($template_id, true);
			}
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render Content Protection form.
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return string
	 */
	public function password_protected_form($settings)
	{
		$html = '<div class="neb-password-protected-content-fields">
            <form method="post">
            <input type="password" name="jltma_content_protection_password" class="neb-password" placeholder="' . esc_attr($settings['jltma_content_protection_password_placeholder']) . '">
            <input type="submit" value="' . esc_attr($settings['jltma_content_protection_password_submit_btn_txt']) . '" class="neb-submit">
            </form>';

		// Public front-end content gate: read-only password comparison, no state
		// change, so a nonce is not applicable (mirrors core post-password).
		$submitted_password = isset($_POST['jltma_content_protection_password']) ? sanitize_text_field(wp_unslash($_POST['jltma_content_protection_password'])) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if (null !== $submitted_password) {
			if ($settings['jltma_content_protection_password'] !== $submitted_password) {
				/* translators: %s is Incorrect password message */
				$html .= sprintf(
					'<p class="">%s</p>',
					__('Password does not match.', 'master-addons' )
				);
			}
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render Content Protection.
	 *
	 * @param string $content Content.
	 * @param Object $widget Widget instance.
	 *
	 * @return string
	 */
	public function render_content($content, $widget)
	{
		$settings = $widget->get_settings_for_display();

		if ($settings['jltma_content_protection'] !== 'yes') {
			return $content;
		}

		if ($settings['jltma_content_protection_type'] === 'role') {
			if ($this->current_user_privileges($settings) === true) {
				return $content;
			}
			return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
		}

		if ($settings['jltma_content_protection_type'] === 'password') {
			if (empty($settings['jltma_content_protection_password'])) {
				return $content;
			}

			$html     = '';
			$unlocked = false;

			// Public front-end content gate: read-only password comparison, no state
			// change, so a nonce is not applicable (mirrors core post-password).
			$submitted_password = isset($_POST['jltma_content_protection_password']) ? sanitize_text_field(wp_unslash($_POST['jltma_content_protection_password'])) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if (null !== $submitted_password) {
				if ($settings['jltma_content_protection_password'] === $submitted_password) {
					$unlocked = true;

					$html .= "<script>
                        var expires = new Date();
                        expires.setTime( expires.getTime() + ( 60 * 60 * 1000 ) );
                        document.cookie = 'jltma_content_protection_password=true;expires=' + expires.toUTCString();
                    </script>";
				}
			}

			if (isset($_COOKIE['jltma_content_protection_password']) || $unlocked) {
				$html .= $content;
			} else {
				$html .= '<div class="neb-protected-content">' . $this->render_message($settings) . $this->password_protected_form($settings) . '</div>';
			}
			return $html;
		}

		if ($settings['jltma_content_protection_type'] === 'logged-in') {
			if (is_user_logged_in()) {
				return $content;
			}

			return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
		}

		$current_time = strtotime(gmdate('Y-m-d H:i'));

		if ($settings['jltma_content_protection_type'] === 'start-end-date') {
			$period = $settings['jltma_content_protection_period_date'];
			if (empty($period)) {
				return $content;
			}

			$start_end = explode(' to ', $period);
			if (sizeof($start_end) !== 2) {
				return $content;
			}

			$start_date = strtotime($start_end[0]);
			$end_date   = strtotime($start_end[1]);
			if ($start_date <= $current_time && $current_time <= $end_date) {
				return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
			}

			return $content;
		}

		if ($settings['jltma_content_protection_type'] === 'days-of-the-week') {
			$current_day  = gmdate('w', $current_time);
			$blocked_days = !empty($settings['jltma_content_protection_days_of_week']) ? $settings['jltma_content_protection_days_of_week'] : array();
			if (in_array($current_day, $blocked_days, true)) {
				if (isset($settings['jltma_content_protection_days_of_week_time_from']) && isset($settings['jltma_content_protection_days_of_week_time_to'])) {
					$start = strtotime('today ' . esc_attr($settings['jltma_content_protection_days_of_week_time_from']));
					$end   = strtotime('today ' . esc_attr($settings['jltma_content_protection_days_of_week_time_to']));
					if ($start <= $current_time && $current_time <= $end) {
						return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
					}

					return $content;
				}

				return '<div class="neb-protected-content">' . $this->render_message($settings) . '</div>';
			}

			return $content;
		}

		return $content;
	}

	/**
	 * Get user roles.
	 *
	 * @return array
	 */
	private function get_user_roles()
	{
		global $wp_roles;
		$roles = $wp_roles->roles;
		if (empty($roles)) {
			return array();
		}

		$all_roles = array();
		foreach ($roles as $key => $value) {
			$all_roles[$key] = $roles[$key]['name'];
		}

		return $all_roles;
	}

	/**
	 * Check current user role exists inside of the roles array.
	 *
	 * @param array $settings Current widget settings.
	 *
	 * @return bool
	 */
	private function current_user_privileges($settings)
	{
		if (!is_user_logged_in()) {
			return false;
		}

		$user_role = reset(wp_get_current_user()->roles);

		return in_array($user_role, (array) $settings['jltma_content_protection_role'], true);
	}

	/**
	 * Return an array with days of the week.
	 *
	 * @return array
	 */
	private function get_days_of_week()
	{
		return array(
			6 => __('Saturday', 'master-addons' ),
			0 => __('Sunday', 'master-addons' ),
			1 => __('Monday', 'master-addons' ),
			2 => __('Tuesday', 'master-addons' ),
			3 => __('Wednesday', 'master-addons' ),
			4 => __('Thursday', 'master-addons' ),
			5 => __('Friday', 'master-addons' ),
		);
	}

	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
}

if (class_exists('MasterAddons\Modules\Extension_Content_Protection')) {
	Extension_Content_Protection::get_instance();
}
