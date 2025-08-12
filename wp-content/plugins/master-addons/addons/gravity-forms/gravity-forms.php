<?php

namespace MasterAddons\Addons;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use MasterAddons\Inc\Helper\Master_Addons_Helper;
/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/27/19
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// If this file is called directly, abort.
class JLTMA_Gravity_Forms extends Widget_Base {
    use \MasterAddons\Inc\Traits\Widget_Notice;
    public function get_name() {
        return 'ma-gravity-forms';
    }

    public function get_title() {
        return esc_html__( 'Gravity Forms', 'master-addons' );
    }

    public function get_icon() {
        return 'jltma-icon eicon-mail';
    }

    public function get_categories() {
        return ['master-addons'];
    }

    protected function is_dynamic_content() : bool {
        return false;
    }

    protected function register_controls() {
        $this->upgrade_to_pro_message();
        //Premium Code use block end
    }

    protected function render() {
        $settings = $this->get_settings();
        if ( !class_exists( 'GFCommon' ) ) {
            Master_Addons_Helper::jltma_elementor_plugin_missing_notice( array(
                'plugin_name' => esc_html__( 'Gravity Form', 'master-addons' ),
            ) );
            return;
        }
        $this->add_render_attribute( 'jltma-gf', 'class', [
            'jltma-gf',
            'ma-cf',
            'jltma-gravity-form',
            'ma-cf-' . esc_attr( $settings['jltma_gf_layout_style'] ),
            'jltma-gf-' . esc_attr( $this->get_id() )
        ] );
        if ( $settings['labels_switch'] != 'yes' ) {
            $this->add_render_attribute( 'jltma-gf', 'class', 'labels-hide' );
        }
        if ( $settings['placeholder_switch'] != 'yes' ) {
            $this->add_render_attribute( 'jltma-gf', 'class', 'placeholder-hide' );
        }
        if ( $settings['custom_title_description'] == 'yes' ) {
            $this->add_render_attribute( 'jltma-gf', 'class', 'title-description-hide' );
        }
        if ( $settings['custom_radio_checkbox'] == 'yes' ) {
            $this->add_render_attribute( 'jltma-gf', 'class', 'ma-el-custom-radio-checkbox' );
        }
        if ( class_exists( 'GFCommon' ) ) {
            if ( !empty( $settings['contact_form_list'] ) ) {
                ?>
				<div <?php 
                echo $this->get_render_attribute_string( 'jltma-gf' );
                ?>>
					<?php 
                if ( $settings['custom_title_description'] == 'yes' ) {
                    ?>
						<div class="jltma-gravity-form-heading">
							<?php 
                    if ( $settings['form_title_custom'] != '' ) {
                        ?>
								<h3 class="jltma-gravity-form-title">
									<?php 
                        echo esc_attr( $settings['form_title_custom'] );
                        ?>
								</h3>
							<?php 
                    }
                    ?>
							<?php 
                    if ( $settings['form_description_custom'] != '' ) {
                        ?>
								<div class="jltma-gravity-form-description">
									<?php 
                        echo $this->parse_text_editor( $settings['form_description_custom'] );
                        ?>
								</div>
							<?php 
                    }
                    ?>
						</div>
					<?php 
                }
                ?>
					<?php 
                $jltma_form_id = $settings['contact_form_list'];
                $jltma_form_title = $settings['form_title'];
                $jltma_form_description = $settings['form_description'];
                $jltma_form_ajax = $settings['form_ajax'];
                gravity_form(
                    $jltma_form_id,
                    $jltma_form_title,
                    $jltma_form_description,
                    $display_inactive = false,
                    $field_values = null,
                    $jltma_form_ajax,
                    '',
                    $echo = true
                );
                ?>
				</div>
<?php 
            } else {
                esc_html__e( 'Please select a Contact Form!', 'master-addons' );
            }
        }
    }

    protected function content_template() {
    }

}
