<?php

namespace MasterAddons\Inc\Traits;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// If this file is called directly, abort.
trait Widget_Notice
{
    /**
     * Adding Go Premium message to all widgets
     */
    public function upgrade_to_pro_message() {
        $this->start_controls_section( 'jltma_pro_section', [
            'label' => sprintf( 
                /* translators: %s: icon for the "Pro" section */
                __( '%s Unlock more possibilities', 'master-addons' ),
                '<i class="eicon-pro-icon"></i>'
             ),
        ] );
        $this->add_control( 'jltma_get_pro_style_tab', [
            'label'       => __( 'Unlock more possibilities', 'master-addons' ),
            'type'        => \Elementor\Controls_Manager::CHOOSE,
            'options'     => [
                '1' => [
                    'title' => '',
                    'icon'  => 'fa fa-unlock-alt',
                ],
            ],
            'default'     => '1',
            'toggle'      => false,
            'description' => '<span class="jltma-widget-pro-feature"> Get the  <a href="https://master-addons.com/pricing/" target="_blank">Pro version</a> for more awesome elements and powerful modules.</span>',
        ] );
        $this->end_controls_section();
    }

}