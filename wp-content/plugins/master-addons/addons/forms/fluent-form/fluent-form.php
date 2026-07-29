<?php

namespace MasterAddons\Addons;

// Elementor Classes.
use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Background;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Admin\Config;

if (!defined('ABSPATH')) {
    exit;   // Exit if accessed directly.
}

class Fluent_Form extends Master_Widget
{
    use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

    public function get_name()
    {
        return 'ma-fluent-form';
    }

    public function get_title()
    {
        return esc_html__('Fluent Form', 'master-addons');
    }

    public function get_icon()
    {
        return 'jltma-icon ' . Config::get_addon_icon('fluent-form');
    }

    public function get_keywords()
    {
        return ['fluent', 'form', 'contact', 'styler', 'fluent form', 'wp fluent'];
    }

            

    /**
     * Get all forms of WP Fluent Forms plugin.
     */
    public static function get_fluent_forms()
    {
        $forms = array();

        if (function_exists('wpFluentForm')) {
            $ff_list = wpFluent()->table('fluentform_forms')
                ->select(array('id', 'title'))
                ->orderBy('id', 'DESC')
                ->get();

            if ($ff_list) {
                $forms[0] = esc_html__('Select', 'master-addons');
                foreach ($ff_list as $form) {
                    $forms[$form->id] = $form->title . ' (' . absint($form->id) . ')';
                }
            } else {
                $forms[0] = esc_html__('No Forms Found!', 'master-addons');
            }
        }

        return $forms;
    }

    /**
     * Validate HTML tag
     */
    public static function jltma_validate_html_tag($tag)
    {
        $allowed_tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
        return in_array($tag, $allowed_tags, true) ? $tag : 'h3';
    }

    protected function register_controls()
    {
        // content tab.
        $this->register_general_content_controls();
        $this->register_input_style_controls();
        $this->register_radio_checkbox_content_controls();
        $this->register_star_rating_controls();
        $this->register_section_controls();
        $this->register_button_content_controls();
        $this->register_error_style_controls();

        // Style tab.
        $this->register_spacing_controls();
        $this->register_typography_controls();

        // Upgrade to Pro
        $this->upgrade_to_pro_message();
    }

    /**
     * Register WP Fluent Forms Styler General Controls.
     */
    protected function register_general_content_controls()
    {
        $this->start_controls_section(
            'section_button',
            array(
                'label' => __('General', 'master-addons'),
            )
        );

        $this->add_control(
            'form_id',
            array(
                'label'   => __('Select Form', 'master-addons'),
                'type'    => Controls_Manager::SELECT,
                'options' => $this->get_fluent_forms(),
                'default' => '0',
            )
        );

        $this->add_control(
            'form_title_option',
            array(
                'label'       => __('Title & Description', 'master-addons'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'yes',
                'label_block' => false,
                'options'     => array(
                    'yes' => __('Enter Your Own', 'master-addons'),
                    'no'  => __('None', 'master-addons'),
                ),
            )
        );

        $this->add_control(
            'form_title',
            array(
                'label'     => __('Form Title', 'master-addons'),
                'type'      => Controls_Manager::TEXT,
                'condition' => array(
                    'form_title_option' => 'yes',
                ),
                'dynamic'   => array(
                    'active' => true,
                ),

            )
        );

        $this->add_control(
            'form_desc',
            array(
                'label'     => __('Form Description', 'master-addons'),
                'type'      => Controls_Manager::TEXTAREA,
                'condition' => array(
                    'form_title_option' => 'yes',
                ),
                'dynamic'   => array(
                    'active' => true,
                ),
            )
        );

        $this->add_responsive_control(
            'form_title_desc_align',
            array(
                'label'     => __('Title & Description Alignment', 'master-addons'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => Helper::jltma_content_alignment(),
                'default'   => 'left',
                'condition' => array(
                    'form_title_option' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-form-desc,
					{{WRAPPER}} .jltma-ff-form-title' => 'text-align: {{VALUE}};',
                ),
                'toggle'    => false,
            )
        );

        $this->end_controls_section();

		// Help Docs section (links from config.php)
		$this->jltma_help_docs('fluent-form');

		$this->upgrade_to_pro_message();
    }

    /**
     * Register WP Fluent Forms Styler Input Style Controls.
     */
    protected function register_input_style_controls()
    {
        $this->start_controls_section(
            'form_input_style',
            array(
                'label' => __('Form Fields', 'master-addons'),
            )
        );

        $this->add_control(
            'ff_style',
            array(
                'label'        => __('Style', 'master-addons'),
                'type'         => Controls_Manager::SELECT,
                'default'      => 'box',
                'options'      => array(
                    'box'       => __('Box', 'master-addons'),
                    'underline' => __('Underline', 'master-addons'),
                ),
                'prefix_class' => 'jltma-ff-style-',
            )
        );

        $this->add_control(
            'form_input_size',
            array(
                'label'        => __('Size', 'master-addons'),
                'type'         => Controls_Manager::SELECT,
                'default'      => 'sm',
                'options'      => array(
                    'xs' => __('Extra Small', 'master-addons'),
                    'sm' => __('Small', 'master-addons'),
                    'md' => __('Medium', 'master-addons'),
                    'lg' => __('Large', 'master-addons'),
                    'xl' => __('Extra Large', 'master-addons'),
                ),
                'prefix_class' => 'jltma-ff-input-size-',
            )
        );

        $this->add_responsive_control(
            'form_input_padding',
            array(
                'label'      => __('Padding', 'master-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input' => 'height: {{BOTTOM}}{{UNIT}}; width: {{BOTTOM}}{{UNIT}}; font-size: calc( {{BOTTOM}}{{UNIT}} / 1.2 );',
                    '{{WRAPPER}} .jltma-ff-style .fluentform select.ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'padding-top: calc( {{TOP}}{{UNIT}} - 2{{UNIT}} ); padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: calc( {{BOTTOM}}{{UNIT}} - 2{{UNIT}} ); padding-left: {{LEFT}}{{UNIT}};',
                ),
                'separator'  => 'after',
            )
        );

        $this->add_control(
            'form_input_bgcolor',
            array(
                'label'     => __('Background Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fafafa',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-net-label,
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'background-color:{{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_label_color',
            array(
                'label'     => __('Label Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'global'    => array(
                    'default' => Global_Colors::COLOR_TEXT,
                ),
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-input--label label,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input + span,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-section-title,
					{{WRAPPER}} .jltma-ff-style .ff-section_break_desk,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff_tc_checkbox +  div.ff_t_c' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_input_color',
            array(
                'label'     => __('Input Text / Placeholder Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'global'    => array(
                    'default' => Global_Colors::COLOR_TEXT,
                ),
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control::-webkit-input-placeholder, {{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform input[type=checkbox]:checked:before,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-net-label span,
					{{WRAPPER}} .jltma-ff-style .jltma-ff-select-custom:after' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings label.active svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .jltma-ff-style .fluentform input[type=radio]:checked:before' => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{form_input_bgcolor.VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_required_color',
            array(
                'label'     => __('Required Asterisk Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .ff-el-input--label.ff-el-is-required.asterisk-right label:after' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_border_style',
            array(
                'label'       => __('Border Style', 'master-addons'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'solid',
                'label_block' => false,
                'options'     => array(
                    'none'   => __('None', 'master-addons'),
                    'solid'  => __('Solid', 'master-addons'),
                    'double' => __('Double', 'master-addons'),
                    'dotted' => __('Dotted', 'master-addons'),
                    'dashed' => __('Dashed', 'master-addons'),
                ),
                'selectors'   => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'border-style: {{VALUE}};',
                ),
                'condition'   => array(
                    'ff_style' => 'box',
                ),
            )
        );

        $this->add_control(
            'input_border_size',
            array(
                'label'      => __('Border Width', 'master-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'default'    => array(
                    'top'    => '1',
                    'bottom' => '1',
                    'left'   => '1',
                    'right'  => '1',
                    'unit'   => 'px',
                ),
                'condition'  => array(
                    'input_border_style!' => 'none',
                    'ff_style'            => 'box',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'input_border_color',
            array(
                'label'     => __('Border Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'input_border_style!' => 'none',
                    'ff_style'            => 'box',
                ),
                'default'   => '#eaeaea',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'ff_border_bottom',
            array(
                'label'      => __('Border Size', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range'      => array(
                    'px' => array(
                        'min' => 1,
                        'max' => 20,
                    ),
                ),
                'default'    => array(
                    'size' => '2',
                    'unit' => 'px',
                ),
                'condition'  => array(
                    'ff_style' => 'underline',
                ),
                'selectors'  => array(
                    '{{WRAPPER}}.jltma-ff-style-underline .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'border-width: 0 0 {{SIZE}}{{UNIT}} 0; border-style: solid;',
                    '{{WRAPPER}}.jltma-ff-style-underline .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff_net_table tbody tr td' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid; box-sizing: content-box;',
                ),
            )
        );

        $this->add_control(
            'ff_border_color',
            array(
                'label'     => __('Border Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'ff_style' => 'underline',
                ),
                'default'   => '#c4c4c4',
                'selectors' => array(
                    '{{WRAPPER}}.jltma-ff-style-underline .fluentform .ff-el-form-control,
					{{WRAPPER}}.jltma-ff-style-underline .fluentform .ff-el-form-check-input,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff_net_table tbody tr td,
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ff_border_active_color',
            array(
                'label'     => __('Border Active Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'input_border_style!' => 'none',
                    'ff_style'            => 'box',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform input:focus,
					{{WRAPPER}} .jltma-ff-style .fluentform select:focus,
					{{WRAPPER}} .jltma-ff-style .fluentform textarea:focus,
					{{WRAPPER}} .jltma-ff-style .fluentform input[type=checkbox]:checked:before' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ff_border_active_color_underline',
            array(
                'label'     => __('Border Active Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'ff_style' => 'underline',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform input:focus,
					 {{WRAPPER}} .jltma-ff-style .fluentform textarea:focus,
					 {{WRAPPER}}.jltma-ff-style-underline .fluentform input[type="checkbox"]:checked' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'form_border_radius',
            array(
                'label'      => __('Rounded Corners', 'master-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px'),
                'default'    => array(
                    'top'    => '0',
                    'bottom' => '0',
                    'left'   => '0',
                    'right'  => '0',
                    'unit'   => 'px',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-control,
					{{WRAPPER}} .jltma-ff-style .fluentform input[type=checkbox],
					{{WRAPPER}} .jltma-ff-style .fluentform .select2-selection' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_net_table tbody tr td:first-of-type' => 'border-radius: {{TOP}}{{UNIT}} 0 0 {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_net_table tbody tr td:last-child' => 'border-radius: 0 {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0;',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Register WP Fluent Forms Styler Radio & Checkbox Controls.
     */
    protected function register_radio_checkbox_content_controls()
    {
        $this->start_controls_section(
            'ff_radio_check_style',
            array(
                'label' => __('Radio & Checkbox', 'master-addons'),
            )
        );

        $this->add_control(
            'ff_radio_check_custom',
            array(
                'label'        => __('Override Current Style', 'master-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'master-addons'),
                'label_off'    => __('No', 'master-addons'),
                'return_value' => 'yes',
                'default'      => '',
                'prefix_class' => 'jltma-ff-check-',
            )
        );

        $this->add_control(
            'ff_radio_check_size',
            array(
                'label'      => __('Size', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'condition'  => array(
                    'ff_radio_check_custom!' => '',
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 20,
                ),
                'range'      => array(
                    'px' => array(
                        'min' => 15,
                        'max' => 50,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input'  => 'width: {{SIZE}}{{UNIT}}!important; height:{{SIZE}}{{UNIT}}; font-size: calc( {{SIZE}}{{UNIT}} / 1.2 );',
                ),
                'separator'  => 'after',
            )
        );

        $this->add_control(
            'ff_radio_check_bgcolor',
            array(
                'label'     => __('Background Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'ff_radio_check_custom!' => '',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input' => 'background-color: {{VALUE}};',
                ),
                'default'   => '#fafafa',
            )
        );

        $this->add_control(
            'ff_selected_color',
            array(
                'label'     => __('Selected Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'global'    => array(
                    'default' => Global_Colors::COLOR_TEXT,
                ),
                'condition' => array(
                    'ff_radio_check_custom!' => '',
                ),
                'selectors' => array(
                    '{{WRAPPER}}.jltma-ff-check-yes .jltma-ff-style .fluentform input[type=checkbox]:checked:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.jltma-ff-check-yes .jltma-ff-style .fluentform input[type=radio]:checked:before' => 'background-color: {{VALUE}}; box-shadow:inset 0px 0px 0px 4px {{ff_radio_check_bgcolor.VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ff_select_color',
            array(
                'label'     => __('Label Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'condition' => array(
                    'ff_radio_check_custom!' => '',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input + span,
					{{WRAPPER}}.jltma-ff-check-yes .jltma-ff-style .fluentform .ff_tc_checkbox +  div.ff_t_c' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ff_check_border_color',
            array(
                'label'     => __('Border Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#eaeaea',
                'condition' => array(
                    'ff_radio_check_custom!' => '',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ff_check_border_width',
            array(
                'label'      => __('Border Width', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 20,
                    ),
                ),
                'default'    => array(
                    'size' => '1',
                    'unit' => 'px',
                ),
                'condition'  => array(
                    'ff_radio_check_custom!' => '',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
                ),
            )
        );

        $this->add_control(
            'ff_check_border_radius',
            array(
                'label'      => __('Checkbox Rounded Corners', 'master-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'condition'  => array(
                    'ff_radio_check_custom!' => '',
                ),
                'selectors'  => array(
                    '{{WRAPPER}}.jltma-ff-check-yes .jltma-ff-style .fluentform input[type=checkbox]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'default'    => array(
                    'top'    => '0',
                    'bottom' => '0',
                    'left'   => '0',
                    'right'  => '0',
                    'unit'   => 'px',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Register WP Fluent Forms Styler Button Controls.
     */
    protected function register_button_content_controls()
    {
        $this->start_controls_section(
            'section_style',
            array(
                'label' => __('Button', 'master-addons'),
            )
        );

        $this->add_control(
            'ff_buttons',
            array(
                'label' => __('Submit And Navigation Button', 'master-addons'),
                'type'  => Controls_Manager::HEADING,
            )
        );

        $this->add_responsive_control(
            'button_align',
            array(
                'label'        => __('Submit Button Alignment', 'master-addons'),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => Helper::jltma_content_alignment(),
                'default'      => 'left',
                'condition'    => array(
                    'form_title_option' => 'yes',
                ),
                'prefix_class' => 'jltma-ff-button-align-',
                'selectors'    => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper' => 'text-align: {{VALUE}};',
                ),
                'toggle'       => false,
            )
        );

        $this->add_control(
            'btn_size',
            array(
                'label'        => __('Size', 'master-addons'),
                'type'         => Controls_Manager::SELECT,
                'default'      => 'sm',
                'options'      => array(
                    'xs' => __('Extra Small', 'master-addons'),
                    'sm' => __('Small', 'master-addons'),
                    'md' => __('Medium', 'master-addons'),
                    'lg' => __('Large', 'master-addons'),
                    'xl' => __('Extra Large', 'master-addons'),
                ),
                'prefix_class' => 'jltma-ff-btn-size-',
            )
        );

        $this->add_responsive_control(
            'ff_button_padding',
            array(
                'label'      => __('Padding', 'master-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
					{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
            'tab_button_normal',
            array(
                'label' => __('Normal', 'master-addons'),
            )
        );

        $this->add_control(
            'button_text_color',
            array(
                'label'     => __('Text Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
					{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'           => 'btn_background_color',
                'label'          => __('Background Color', 'master-addons'),
                'types'          => array('classic', 'gradient'),
                'fields_options' => array(
                    'color' => array(
                        'global' => array(
                            'default' => Global_Colors::COLOR_ACCENT,
                        ),
                    ),
                ),
                'selector'       => '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'        => 'btn_border',
                'label'       => __('Border', 'master-addons'),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary',
            )
        );

        $this->add_responsive_control(
            'btn_border_radius',
            array(
                'label'      => __('Border Radius', 'master-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
					{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            array(
                'label' => __('Hover', 'master-addons'),
            )
        );

        $this->add_control(
            'btn_hover_color',
            array(
                'label'     => __('Text Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit:hover,
					{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ff_button_hover_border_color',
            array(
                'label'     => __('Border Hover Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit:hover,
					{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary:hover' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'button_background_hover_color',
                'label'    => __('Background Color', 'master-addons'),
                'types'    => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .jltma-ff-style .fluentform .ff_submit_btn_wrapper button.ff-btn-submit:hover,
				{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary:hover',
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * Register WP Fluent Forms Styler Error Style Controls.
     */
    protected function register_error_style_controls()
    {
        $this->start_controls_section(
            'form_error_field',
            array(
                'label' => __('Success / Error Message', 'master-addons'),
            )
        );

        $this->add_control(
            'form_error',
            array(
                'label' => __('Field Validation', 'master-addons'),
                'type'  => Controls_Manager::HEADING,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'ff_message_typo',
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ),
                'selector' => '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-is-error .error',
            )
        );

        $this->add_control(
            'form_error_msg_color',
            array(
                'label'     => __('Message Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ff0000',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-is-error .error' => 'color: {{VALUE}}',
                ),
            )
        );

        $this->add_responsive_control(
            'field_validation_padding',
            array(
                'label'      => __('Padding', 'master-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-is-error .error' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'form_success_message',
            array(
                'label'     => __('Form Success Validation', 'master-addons'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_responsive_control(
            'success_align',
            array(
                'label'        => __('Alignment', 'master-addons'),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => Helper::jltma_content_alignment(),
                'default'      => 'left',
                'selectors'    => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-message-success' => 'text-align: {{VALUE}};',
                ),
                'toggle'       => false,
                'prefix_class' => 'jltma-ff-message-align-',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'cf7_success_validation_typo',
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ),
                'selector' => '{{WRAPPER}} .jltma-ff-style .fluentform .ff-message-success',
            )
        );

        $this->add_control(
            'form_success_message_color',
            array(
                'label'     => __('Message Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#008000',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-message-success'   => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_valid_bgcolor',
            array(
                'label'     => __('Message Background Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-message-success' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Register WP Fluent Forms Styler Star Rating Controls.
     */
    protected function register_star_rating_controls()
    {
        $this->start_controls_section(
            'star_rating_field',
            array(
                'label' => __('Star Rating', 'master-addons'),
            )
        );

        $this->add_control(
            'ff_star_rating_custom',
            array(
                'label'        => __('Override Current Style', 'master-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'master-addons'),
                'label_off'    => __('No', 'master-addons'),
                'return_value' => 'yes',
                'default'      => '',
                'prefix_class' => 'jltma-ff-star-',
            )
        );

        $this->add_responsive_control(
            'ff_star_rating_size',
            array(
                'label'      => __('Size', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 200,
                    ),
                ),
                'condition'  => array(
                    'ff_star_rating_custom' => 'yes',
                ),
                'selectors'  => array(
                    '{{WRAPPER}}.jltma-ff-star-yes .jltma-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ),
                'separator'  => 'after',
            )
        );

        $this->add_control(
            'active_stars_color',
            array(
                'label'     => __('Selected Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}}.jltma-ff-star-yes .jltma-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings label.active svg' => 'fill: {{VALUE}};',
                ),
                'condition' => array(
                    'ff_star_rating_custom' => 'yes',
                ),
            )
        );

        $this->add_control(
            'inactive_stars_color',
            array(
                'label'     => __('Inactive Stars Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}}.jltma-ff-star-yes .jltma-ff-style .fluentform .ff-el-ratings.jss-ff-el-ratings svg' => 'fill: {{VALUE}};',
                ),
                'condition' => array(
                    'ff_star_rating_custom' => 'yes',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Register WP Fluent Forms Styler Section Break Controls.
     */
    protected function register_section_controls()
    {
        $this->start_controls_section(
            'section_field',
            array(
                'label' => __('Section Break', 'master-addons'),
            )
        );

        $this->add_control(
            'form_section_title_style',
            array(
                'label' => __('Title', 'master-addons'),
                'type'  => Controls_Manager::HEADING,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'section_title_typography',
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ),
                'selector' => '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-section-title',
            )
        );

        $this->add_control(
            'form_section_title_color',
            array(
                'label'     => __('Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'global'    => array(
                    'default' => Global_Colors::COLOR_PRIMARY,
                ),
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-section-title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_section_desc_style',
            array(
                'label'     => __('Description', 'master-addons'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'section_desc_typography',
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ),
                'selector' => '{{WRAPPER}} .jltma-ff-style .ff-section_break_desk',
            )
        );

        $this->add_control(
            'form_section_desc_color',
            array(
                'label'     => __('Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'global'    => array(
                    'default' => Global_Colors::COLOR_SECONDARY,
                ),
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-style .ff-section_break_desk' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Register WP Fluent Forms Styler Spacing Controls.
     */
    protected function register_spacing_controls()
    {
        $this->start_controls_section(
            'form_spacing',
            array(
                'label' => __('Spacing', 'master-addons'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'form_title_margin_bottom',
            array(
                'label'      => __('Form Title Bottom Margin', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 200,
                    ),
                ),
                'condition'  => array(
                    'form_title_option!' => 'no',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-form-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'form_desc_margin_bottom',
            array(
                'label'      => __('Form Description Bottom Margin', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 200,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-form-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
                'condition'  => array(
                    'form_title_option!' => 'no',
                ),
            )
        );

        $this->add_responsive_control(
            'form_fields_margin',
            array(
                'label'      => __('Between Two Fields', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 200,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-input--content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'form_label_margin_bottom',
            array(
                'label'      => __('Label Bottom Spacing', 'master-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', 'rem'),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 200,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-input--label' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Register WP Fluent Forms Styler Typography Controls.
     */
    protected function register_typography_controls()
    {
        $this->start_controls_section(
            'form_typo',
            array(
                'label' => __('Typography', 'master-addons'),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'form_title_typo',
            array(
                'label'     => __('Form Title', 'master-addons'),
                'type'      => Controls_Manager::HEADING,
                'condition' => array(
                    'form_title_option!' => 'no',
                ),
            )
        );

        $this->add_control(
            'form_title_tag',
            array(
                'label'     => __('HTML Tag', 'master-addons'),
                'type'      => Controls_Manager::SELECT,
                'options'   => array(
                    'h1'  => __('H1', 'master-addons'),
                    'h2'  => __('H2', 'master-addons'),
                    'h3'  => __('H3', 'master-addons'),
                    'h4'  => __('H4', 'master-addons'),
                    'h5'  => __('H5', 'master-addons'),
                    'h6'  => __('H6', 'master-addons'),
                    'div' => __('div', 'master-addons'),
                    'p'   => __('p', 'master-addons'),
                ),
                'condition' => array(
                    'form_title_option!' => 'no',
                ),
                'default'   => 'h3',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'title_typography',
                'global'    => array(
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ),
                'selector'  => '{{WRAPPER}} .jltma-ff-form-title',
                'condition' => array(
                    'form_title_option!' => 'no',
                ),
            )
        );

        $this->add_control(
            'form_title_color',
            array(
                'label'     => __('Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'global'    => array(
                    'default' => Global_Colors::COLOR_PRIMARY,
                ),
                'condition' => array(
                    'form_title_option!' => 'no',
                ),
                'default'   => '',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-form-title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_desc_typo',
            array(
                'label'     => __('Form Description', 'master-addons'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array(
                    'form_title_option!' => 'no',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'desc_typography',
                'global'    => array(
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ),
                'selector'  => '{{WRAPPER}} .jltma-ff-form-desc',
                'condition' => array(
                    'form_title_option!' => 'no',
                ),
            )
        );

        $this->add_control(
            'form_desc_color',
            array(
                'label'     => __('Color', 'master-addons'),
                'type'      => Controls_Manager::COLOR,
                'global'    => array(
                    'default' => Global_Colors::COLOR_TEXT,
                ),
                'condition' => array(
                    'form_title_option!' => 'no',
                ),
                'default'   => '',
                'separator' => 'after',
                'selectors' => array(
                    '{{WRAPPER}} .jltma-ff-form-desc' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'form_input_typo',
            array(
                'label' => __('Form Fields', 'master-addons'),
                'type'  => Controls_Manager::HEADING,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'form_label_typography',
                'label'    => 'Label Typography',
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ),
                'selector' => '{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-input--label label,
					{{WRAPPER}} .jltma-ff-style .fluentform .ff-el-form-check-input + span',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'input_typography',
                'label'    => 'Text Typography',
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ),
                'selector' => '{{WRAPPER}} .jltma-ff-style .ff-el-input--content input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="image"]):not([type="file"]),
				{{WRAPPER}} .jltma-ff-style .ff-el-input--content textarea,
				{{WRAPPER}} .jltma-ff-style .fluentform select,
				{{WRAPPER}} .jltma-ff-style .jltma-ff-select-custom',
            )
        );

        $this->add_control(
            'btn_typography_label',
            array(
                'label'     => __('Button', 'master-addons'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'btn_typography',
                'label'    => __('Typography', 'master-addons'),
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ),
                'selector' => '{{WRAPPER}} .jltma-ff-style .ff_submit_btn_wrapper button.ff-btn-submit,
				{{WRAPPER}} .jltma-ff-style .fluentform .step-nav button.ff-btn-secondary,
				{{WRAPPER}} .jltma-ff-style .fluentform .ff_upload_btn',
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     */
    protected function render()
    {
        $settings    = $this->get_settings_for_display();
        $form_title  = '';
        $description = '';

        if ('yes' === $settings['form_title_option']) {
            $form_title  = $this->get_settings_for_display('form_title');
            $description = $this->get_settings_for_display('form_desc');
        }
?>
        <div class="jltma-ff-style elementor-clickable">
            <?php

            if ('' !== $form_title) {
                $title_size_tag = self::jltma_validate_html_tag($settings['form_title_tag']);
            ?>

                <<?php echo esc_attr($title_size_tag); ?> class="jltma-ff-form-title"><?php echo wp_kses_post($form_title); ?></<?php echo esc_attr($title_size_tag); ?>>
            <?php
            }

            if ('' !== $description) {
            ?>

                <p class="jltma-ff-form-desc"><?php echo wp_kses_post($description); ?></p>

            <?php
            }

            if ('0' === $settings['form_id']) {

                esc_attr_e('Please select a WP Fluent Form', 'master-addons');
            } elseif ($settings['form_id']) {

                $shortcode_extra = '';
                $shortcode_extra = apply_filters('jltma_ff_shortcode_extra_param', '', absint($settings['form_id']));

                echo do_shortcode('[fluentform id=' . absint($settings['form_id']) . $shortcode_extra . ']');
            }
            ?>

        </div>
<?php
    }
}
