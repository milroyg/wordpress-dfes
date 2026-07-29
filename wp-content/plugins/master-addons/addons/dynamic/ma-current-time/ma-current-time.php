<?php

namespace MasterAddons\Addons;

// Elementor Classes
use MasterAddons\Inc\Classes\Base\Master_Widget;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Text_Shadow;

use MasterAddons\Inc\Classes\Helper;
use MasterAddons\Inc\Admin\Config;

/**
 * Author Name: Liton Arefin
 * Author URL : https: //jeweltheme.com
 * Date       : 02/04/2020
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Current_Time extends Master_Widget
{
    use \MasterAddons\Inc\Traits\Widget_Notice;
	use \MasterAddons\Inc\Traits\Widget_Assets_Trait;

    public function get_name()
    {
        return 'ma-el-current-time';
    }

        public function get_title()
    {
        return esc_html__('Current Time', 'master-addons' );
    }

    public function get_icon()
    {
        return 'jltma-icon ' . Config::get_addon_icon($this->get_name());
    }

    protected function register_controls()
    {

        /**
         * Current Time
         */
        $this->start_controls_section(
            'ma_el_current_time_content',
            [
                'label' => esc_html__('General', 'master-addons' ),
            ]
        );

        $this->add_control(
            'ma_el_current_time_type',
            array(
                'label'   => __('Type of time', 'master-addons' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => array(
                    'custom'    => __('Custom', 'master-addons' ),
                    'mysql'     => __('MySql', 'master-addons' ),
                    'timestamp' => __('TimeStamp', 'master-addons' )
                )
            )
        );

        $this->add_control(
            'ma_el_current_time_date_format',
            array(
                'label'       => __('Date Format String', 'master-addons' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => get_option('date_format'),
                'description' => '<span class="pro-feature"> <a href="' . esc_url_raw('https://wordpress.org/support/article/formatting-date-and-time/') . '" target="_blank">Date Time Format Examples </a> </span>',
                'condition'   => array(
                    'ma_el_current_time_type' => array('custom'),
                )
            )
        );

        $this->add_responsive_control(
            'ma_el_current_time_date_alignment',
            array(
                'label'     => __('Alignment', 'master-addons' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => Helper::jltma_content_alignment(),
                'toggle'    => true,
                'selectors' => array(
                    '{{WRAPPER}}' => 'text-align: {{VALUE}}',
                )
            )
        );

        $this->end_controls_section();

        /*
            * Style for Current Time
            */
        $this->start_controls_section(
            'ma_el_current_time_style',
            array(
                'label' => __('Text', 'master-addons' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'ma_el_current_time_text_color',
            array(
                'label'     => __('Color', 'master-addons' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .jltma-current-time' => 'color: {{VALUE}};',
                )
            )
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            array(
                'name'     => 'ma_el_current_time_text_shadow',
                'label'    => __('Text Shadow', 'master-addons' ),
                'selector' => '{{WRAPPER}} .jltma-current-time',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'ma_el_current_time_text_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'selector' => '{{WRAPPER}} .jltma-current-time'
            )
        );

        $this->end_controls_section();

        // Help Docs section (links from config.php)
        $this->jltma_help_docs();

        $this->upgrade_to_pro_message();

    }

    protected function render()
    {
        $settings          = $this->get_settings_for_display();
        $date_format       = $settings['ma_el_current_time_date_format'];
        $time_type         = $settings['ma_el_current_time_type'];

        if( $time_type === 'custom' ){
            $date = date_i18n( $date_format, strtotime(current_time( $time_type) ) );
        }else{
            $date = current_time($time_type);
        }
        echo '<div class="jltma-current-time">' . esc_html( $date ) . '</div>';
    }
}
