<?php

namespace MasterAddons\Inc\Traits;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

trait Widget_Notice
{
    /**
     * Adding Go Premium message to all widgets
     */
    public function upgrade_to_pro_message()
    {
        if (!Master_Addons_Helper::jltma_premium()) {
            $this->start_controls_section(
                'jltma_pro_section',
                [
                    'label' => sprintf(
                        /* translators: %s: icon for the "Pro" section */
                        __('%s Unlock more possibilities', 'master-addons'),
                        '<i class="eicon-pro-icon"></i>'
                    ),
                ]
            );

            $this->add_control(
                'jltma_get_pro_style_tab',
                [
                    'label' => __('Unlock more possibilities', 'master-addons'),
                    'type' => \Elementor\Controls_Manager::CHOOSE,
                    'options' => [
                        '1' => [
                            'title' => '',
                            'icon' => 'eicon-lock',
                        ],
                    ],
                    'default' => '1',
                    'toggle'    => false,
                    'description' => Master_Addons_Helper::unlock_pro_feature(),
                ]
            );

            $this->end_controls_section();
        }
    }
}
