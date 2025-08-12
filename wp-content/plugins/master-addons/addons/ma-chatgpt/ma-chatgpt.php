<?php

namespace MasterAddons\Addons;

/**
 * Author Name: Liton Arefin
 * Author URL : https: //jeweltheme.com
 */

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CHATGPT extends Widget_Base {

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

        wp_register_script( 'jltma-chatgpt',
        	JLTMA_URL . '/assets/vendors/ai/widget-chatgpt.js',
        	[ 'jquery' ],
            null
        );

        wp_register_style( 'jltma-chatgpt',
        	JLTMA_URL . '/assets/vendors/ai/widget-chatgpt.css',
        	[],
            null
        );
	}

	public function get_name() {
		return 'jltma-chatgpt';
	}

	public function get_title() {
		return __( 'Liquid ChatGPT', 'master-addons' );
	}

	public function get_icon() {
		return 'eicon-ai jltma-element';
	}

	public function get_categories() {
		return [ 'master-addons' ];
	}

	public function get_keywords() {
		return [ 'image', 'ai', 'generator', 'dall', 'open' ];
	}

	public function get_script_depends() {
		return [ 'jltma-chatgpt' ];
	}

	public function get_style_depends() {
		return [ 'lqd-chatgpt' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'data_section',
			[
				'label' => __( 'Data', 'master-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		if (
			empty( jltma_core_helper()->get_kit_option('jltma_ai_chatgpt') ) ||
			empty( jltma_core_helper()->get_kit_option('jltma_ai_chatgpt_api_key') )
		) {
			$this->add_control(
				'api_key_info',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( 'Go to the <strong><u>Elementor Site Settings > Liquid AI</u></strong> to add your API Key.', 'master-addons' ) ),
					'separator' => 'after',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}

		$this->add_control(
			'limit_options',
			[
				'label' => esc_html__( 'Limitation', 'master-addons' ),
				//'description' => esc_html__( 'You can set a request limit. For example, allow 2 prompt requests by hours or days', 'master-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'is_user_logged_in',
			[
				'label' => esc_html__( 'Enable Login required?', 'master-addons' ),
				'description' => esc_html__( 'Disable usage for non-login users.', 'master-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'master-addons' ),
				'label_off' => esc_html__( 'Off', 'master-addons' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'request_by',
			[
				'label' => esc_html__( 'Request by', 'master-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'HOUR_IN_SECONDS',
				'options' => [
					'HOUR_IN_SECONDS' => esc_html__( 'Hour', 'master-addons' ),
					'DAY_IN_SECONDS' => esc_html__( 'Day', 'master-addons' ),
				],
			]
		);

		$this->add_control(
			'request_limit',
			[
				'label' => esc_html__( 'Message limit', 'master-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 10,
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__( 'Icon', 'master-addons' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'selected_icon_order',
			[
				'label' => esc_html__( 'Icon order', 'master-addons' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -2,
				'max' => 2,
				'step' => 1,
				'default' => 0,
				'selectors' => [
					'{{WRAPPER}} .lqd-chatgpt--icon' => 'order: {{VALUE}}',
				],
				'condition' => [
					'selected_icon[value]!' => ''
				]
			]
		);

		$this->add_control(
			'container_height',
			[
				'label' => esc_html__( 'Container Max Height', 'master-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .lqd-chatgpt--results-messages' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'input_position',
			[
				'label' => esc_html__( 'Input position', 'master-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					''  => [
						'title' => esc_html__( 'Top', 'master-addons' ),
						'icon' => 'eicon-arrow-up',
					],
					'column-reverse'  => [
						'title' => esc_html__( 'Bottom', 'master-addons' ),
						'icon' => 'eicon-arrow-down',
					],
				],
				'separator' => 'before',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .lqd-chatgpt' => 'flex-direction: {{VALUE}}'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'label_section',
			[
				'label' => __( 'Labels', 'master-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'label_input',
			[
				'label' => esc_html__( 'Input placeholder', 'master-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'How can I assist today?', 'master-addons' ),
				'label_block' => true,
				'ai' => [
					'active' => false
				]
			]
		);

		$this->add_control(
			'label_login',
			[
				'label' => esc_html__( 'Login alert', 'master-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'You should login first!', 'master-addons' ),
				'label_block' => true,
				'ai' => [
					'active' => false
				]
			]
		);

		$this->add_control(
			'label_limit',
			[
				'label' => esc_html__( 'Reached limit', 'master-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'You have reached your request limit. Please try again 1 hour later.', 'master-addons' ),
				'label_block' => true,
				'ai' => [
					'active' => false
				]
			]
		);

		$this->add_control(
			'label_typing',
			[
				'label' => esc_html__( 'Typing', 'master-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Typing...', 'master-addons' ),
				'label_block' => true,
				'ai' => [
					'active' => false
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'effects_section',
			[
				'label' => __( 'Effects <span style="font-size: 1.5em; vertical-align:middle; margin-inline-start:0.35em;">⚡️<span>', 'master-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'lqd_outline_glow_effect_form',
			[
				'label' => esc_html__( 'Form glow effect style', 'master-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'master-addons' ),
					'effect-1' => esc_html__( 'Effect 1', 'master-addons' ),
					'effect-2' => esc_html__( 'Effect 2', 'master-addons' ),
				],
				'default' => '',
			]
		);

		$this->add_control(
			'lqd_outline_glow_effect_input',
			[
				'label' => esc_html__( 'Input glow effect style', 'master-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'master-addons' ),
					'effect-1' => esc_html__( 'Effect 1', 'master-addons' ),
					'effect-2' => esc_html__( 'Effect 2', 'master-addons' ),
				],
				'default' => '',
			]
		);

		$this->end_controls_section();

		\LQD_Elementor_Helper::add_style_controls(
			$this,
			'chatgpt',
			[
				'search_bar' => [
					'label' => 'Form',
					'controls' => [
						[
							'type' => 'margin',
							'selector' => '.lqd-chatgpt--form'
						],
						[
							'type' => 'padding',
							'selector' => '.lqd-chatgpt--form'
						],
						[
							'type' => 'liquid_background_css',
							'selector' => '.lqd-chatgpt--form'
						],
						[
							'type' => 'border',
							'selector' => '.lqd-chatgpt--form'
						],
						[
							'type' => 'border_radius',
							'selector' => '.lqd-chatgpt--form'
						],
						[
							'type' => 'box_shadow',
							'selector' => '.lqd-chatgpt--form'
						],
					],
					'plural_heading' => false,
					'state_tabs' => [ 'normal', 'hover' ],
				],
				'input' => [
					'controls' => [
						[
							'type' => 'typography',
							'selector' => '.lqd-chatgpt--input-wrap'
						],
						[
							'type' => 'padding',
							'selector' => '.lqd-chatgpt--input-wrap'
						],
						[
							'type' => 'liquid_color',
							'selector' => '.lqd-chatgpt--input-wrap'
						],
						[
							'type' => 'liquid_background_css',
							'selector' => '.lqd-chatgpt--input-wrap'
						],
						[
							'type' => 'border',
							'selector' => '.lqd-chatgpt--input-wrap'
						],
						[
							'type' => 'border_radius',
							'selector' => '.lqd-chatgpt--input-wrap'
						],
						[
							'type' => 'box_shadow',
							'selector' => '.lqd-chatgpt--input-wrap'
						],
					],
					'plural_heading' => false,
					'state_tabs' => [ 'normal', 'focus-within' ],
				],
				'icon' => [
					'controls' => [
						[
							'type' => 'font_size',
							'name' => 'icon_size',
							'selector' => '.lqd-chatgpt--icon'
						],
						[
							'type' => 'liquid_linked_dimensions',
							'selector' => '.lqd-chatgpt--icon'
						],
						[
							'type' => 'margin',
							'selector' => '.lqd-chatgpt--icon'
						],
						[
							'type' => 'liquid_color',
							'selector' => '.lqd-chatgpt--icon'
						],
						[
							'type' => 'liquid_background_css',
							'selector' => '.lqd-chatgpt--icon'
						],
						[
							'type' => 'border',
							'selector' => '.lqd-chatgpt--icon'
						],
						[
							'type' => 'border_radius',
							'selector' => '.lqd-chatgpt--icon'
						],
						[
							'type' => 'box_shadow',
							'selector' => '.lqd-chatgpt--icon'
						],
					],
					'condition' => [
						'selected_icon[value]!' => ''
					],
					'plural_heading' => false,
					'state_tabs' => [ 'normal', 'hover' ],
				],
				'loader' => [
					'controls' => [
						[
							'type' => 'liquid_color',
							'selectors' => [
								'{{WRAPPER}} .lqd-chatgpt--loader .lds-ripple div' => 'border-color: {{VALUE}}',
								'{{WRAPPER}} .lqd-chatgpt--loader .text' => 'color: {{VALUE}}'
							],
						],
						[
							'type' => 'liquid_background_css',
							'selector' => '.lqd-chatgpt--loader'
						],
						[
							'type' => 'margin',
							'selector' => '.lqd-chatgpt--loader'
						],
						[
							'type' => 'padding',
							'selector' => '.lqd-chatgpt--loader'
						],
						[
							'type' => 'border',
							'selector' => '.lqd-chatgpt--loader'
						],
						[
							'type' => 'border_radius',
							'selector' => '.lqd-chatgpt--loader'
						],
						[
							'type' => 'box_shadow',
							'selector' => '.lqd-chatgpt--loader'
						],
					],
					'plural_heading' => false,
					'state_tabs' => [ 'normal', 'hover' ],
				],
				'message' => [
					'controls' => [
						[
							'type' => 'width',
							'default' => [
								'size' => 30,
								'unit' => '%'
							],
							'selectors' => [
								'{{WRAPPER}} .lqd-chatgpt--results-message' => 'width: {{SIZE}}{{UNIT}}'
							]
						],
						[
							'type' => 'gap',
							'label' => 'Gap between messages',
							'default' => [
								'size' => 5,
								'unit' => '%'
							],
							'selector' => '.lqd-chatgpt--results-messages'
						],
						[
							'type' => 'typography',
							'selector' => '.lqd-chatgpt--results-message'
						],
						[
							'type' => 'padding',
							'selector' => '.lqd-chatgpt--results-message'
						],
						[
							'type' => 'margin',
							'selector' => '.lqd-chatgpt--results-message',
							'default' => [
								'unit' => 'px',
								'top' => '30',
								'right' => '0',
								'bottom' => '0',
								'left' => '0',
								'isLinked' => false
							],
						],
						[
							'type' => 'liquid_color',
							'selector' => '.lqd-chatgpt--results-message'
						],
						[
							'type' => 'liquid_background_css',
							'selector' => '.lqd-chatgpt--results-message'
						],
						[
							'type' => 'border',
							'selector' => '.lqd-chatgpt--results-message'
						],
						[
							'type' => 'border_radius',
							'selector' => '.lqd-chatgpt--results-message'
						],
						[
							'type' => 'box_shadow',
							'selector' => '.lqd-chatgpt--results-message'
						],
					],
				],
				'alert' => [
					'controls' => [
						[
							'type' => 'padding',
							'selector' => '.lqd-chatgpt--alert'
						],
						[
							'type' => 'margin',
							'selector' => '.lqd-chatgpt--alert',
						],
						[
							'type' => 'typography',
							'selector' => '.lqd-chatgpt--alert'
						],
						[
							'type' => 'liquid_color',
							'selector' => '.lqd-chatgpt--alert'
						],
						[
							'type' => 'liquid_background_css',
							'selector' => '.lqd-chatgpt--alert'
						],
						[
							'type' => 'border',
							'selector' => '.lqd-chatgpt--alert'
						],
						[
							'type' => 'border_radius',
							'selector' => '.lqd-chatgpt--alert'
						],
						[
							'type' => 'box_shadow',
							'selector' => '.lqd-chatgpt--alert'
						],
					],
				],
				'glow_form' => [
					'label' => 'Form glow',
					'controls' => [
						[
							'type' => 'width',
							'css_var' => '--lqd-outline-glow-w',
						],
						[
							'type' => 'slider',
							'name' => 'duration',
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 1,
									'max' => 10,
								]
							],
							'unit' => 's',
							'css_var' => '--lqd-outline-glow-duration',
						],
						[
							'type' => 'liquid_color',
							'name' => 'color',
							'types' => [ 'solid' ],
							'css_var' => '--lqd-outline-glow-color',
						],
						[
							'type' => 'liquid_color',
							'name' => 'color_secondary',
							'types' => [ 'solid' ],
							'css_var' => '--lqd-outline-glow-color-secondary',
						],
					],
					'plural_heading' => false,
					'apply_css_var_to_el' => true,
					'selector' => '.lqd-chatgpt--form',
					'condition' => [
						'lqd_outline_glow_effect_form!' => ''
					],
				],
				'glow_input' => [
					'label' => 'Input glow',
					'controls' => [
						[
							'type' => 'width',
							'css_var' => '--lqd-outline-glow-w',
						],
						[
							'type' => 'slider',
							'name' => 'duration',
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 1,
									'max' => 10,
								]
							],
							'unit' => 's',
							'css_var' => '--lqd-outline-glow-duration',
						],
						[
							'type' => 'liquid_color',
							'name' => 'color',
							'types' => [ 'solid' ],
							'css_var' => '--lqd-outline-glow-color',
						],
						[
							'type' => 'liquid_color',
							'name' => 'color_secondary',
							'types' => [ 'solid' ],
							'css_var' => '--lqd-outline-glow-color-secondary',
						],
					],
					'plural_heading' => false,
					'apply_css_var_to_el' => true,
					'selector' => '.lqd-chatgpt--input-wrap',
					'condition' => [
						'lqd_outline_glow_effect_input!' => ''
					],
				],
			],
		);

		lqd_elementor_add_button_controls( $this, 'ib_', [], true, 'all', true, 'submit' );

	}

	protected function request_limit() {

		$request_by = $this->get_settings_for_display( 'request_by' ); // HOUR_IN_SECONDS, DAY_IN_SECONDS
		$request_limit = $this->get_settings_for_display( 'request_limit' );
		$expiration = constant($request_by);

		$IP = $_SERVER['REMOTE_ADDR'];
		$cache = get_transient( 'liquid_chatgpt__' . $IP );

		if ( false === $cache ) {
			$cache = [ 'limit' => $request_limit, 'expiration' => $request_by ];
			set_transient( 'liquid_chatgpt__' . $IP, $cache, $expiration );
		}

	}

	protected function get_outline_glow_markup( $part ) {

		if ( !$part ) return;

		$settings = $this->get_settings_for_display();
		$glow_effect = $settings[ 'lqd_outline_glow_effect_' . $part ];

		if ( empty( $glow_effect ) ) return;

		$glow_attrs = [
			'class' => [ 'lqd-outline-glow', 'lqd-outline-glow-' . $part, 'lqd-outline-glow-' . $glow_effect, 'inline-block', 'rounded-inherit', 'absolute', 'pointer-events-none' ]
		];

		$this->add_render_attribute( 'outline_glow_' . $part, $glow_attrs );

		?>
			<span <?php $this->print_render_attribute_string( 'outline_glow_' . $part ); ?>>
				<span class="lqd-outline-glow-inner inline-block min-w-full min-h-full rounded-inherit aspect-square absolute top-1/2 start-1/2"></span>
			</span>
		<?php

	}

	protected function render() {

		$this->request_limit();

		$settings = $this->get_settings_for_display();
		$options = [
			'l' => $settings['is_user_logged_in'] ? $settings['is_user_logged_in'] : '',
			'label_typing' => $settings['label_typing'] ? $settings['label_typing'] : __( 'Typing', 'master-addons' ),
		];

		$this->add_render_attribute(
			'lqd_chatgpt_form',
			[
				'class' => [ 'lqd-chatgpt--form', 'flex', 'items-center', 'relative' ],
				'action' => 'lqd-chatgpt',
				'medhod' => 'post',
				'data-options' => wp_json_encode( $options )
			]
		);

		?>
		<div class="lqd-chatgpt flex flex-col">
			<form <?php $this->print_render_attribute_string( 'lqd_chatgpt_form' ); ?>>
				<?php $this->get_outline_glow_markup( 'form' ); ?>
				<div class="lqd-chatgpt--input-wrap flex w-full relative">
					<?php $this->get_outline_glow_markup( 'input' ); ?>
					<input class="lqd-chatgpt--input w-full relative" type="text" id="prompt" name="prompt" maxlength="1000" autocomplete="off" placeholder="<?php echo esc_attr( $settings['label_input'] ); ?>" required>
				</div>
				<?php wp_nonce_field( 'lqd-chatgpt', 'security' ); ?>
				<?php \LQD_Elementor_Render_Button::get_button( $this, 'ib_', '', 'submit' ); ?>
				<?php if ( ! empty( $settings['selected_icon']['value'] ) ) { ?>
					<div class="lqd-chatgpt--icon flex items-center justify-center">
						<?php \LQD_Elementor_Helper::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true', 'class' => 'w-1em h-auto align-middle fill-current relative' ] ); ?>
					</div>
				<?php } ?>
			</form>

			<div class="lqd-chatgpt--results flex flex-col justify-center">
				<div class="lqd-chatgpt--results-messages w-full flex flex-col relative overflow-auto"></div>
				<div class="lqd-chatgpt--alert lqd-chatgpt--results-error_login"><?php echo esc_html( $settings['label_login'] ); ?></div>
				<div class="lqd-chatgpt--alert lqd-chatgpt--results-error_limit"><?php echo esc_html( $settings['label_limit'] ); ?></div>
			</div>

		</div>

		<?php

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new CHATGPT() );
