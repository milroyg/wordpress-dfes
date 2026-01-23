<?php

namespace MasterAddons\Admin\Dashboard\Icons_Library;

use MasterAddons\Admin\Dashboard\Addons\Elements\JLTMA_Icons_Library;
use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 22/10/21
 */
?>

<div class="jltma-master-addons-tab-panel" id="jltma-master-addons-icons-library" style="display: none;">

    <div class="jltma-master-addons-features">

        <div class="jltma-tab-dashboard-wrapper">

            <form action="" method="POST" id="jltma-master-addons-icons-settings" class="jltma-addons-tab-settings" name="jltma-master-addons-icons-settings">

                <?php wp_nonce_field('jltma_icons_library_settings_nonce_action'); ?>

                <div class="jltma-addons-dashboard-tabs-wrapper">

                    <div id="jltma-master-addons-icons" class="jltma-addons-dashboard-header-left">

                        <div class="jltma-master-addons-features-list">

                            <div class="jltma-master-addons-dashboard-filter float-right">

                                <div class="jltma-filter-right">
                                    <button class="jltma-addons-enable-all">
                                        <?php echo esc_html__('Enable All', 'master-addons'); ?>
                                    </button>
                                    <button class="jltma-addons-disable-all">
                                        <?php echo esc_html__('Disable All', 'master-addons'); ?>
                                    </button>

                                    <div class="jltma-tab-dashboard-header-wrapper inline-block">
                                        <div class="jltma-tab-dashboard-header-right">
                                            <button type="submit" class="jltma-button jltma-tab-element-save-setting">
                                                <?php _e('Save Settings', 'master-addons'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.jltma_master_addons-dashboard-filter -->

                            <!-- Master Addons Extensions -->

                            <h3 class="mt-0"><?php echo esc_html__('Icons Library', 'master-addons'); ?></h3>

                            <div class="jltma-master-addons-features-container mt-0 is-flex">

                                <?php foreach (JLTMA_Icons_Library::$jltma_icons_library['jltma-icons-library']['libraries'] as $key => $icons_library) { ?>

                                    <div class="jltma-master-addons-dashboard-checkbox">
                                        <div class="jltma-master-addons-dashboard-checkbox-content">

                                            <div class="jltma-master-addons-features-ribbon">
                                                <?php echo apply_filters('master_addons/addons/pro_ribbon', !empty($icons_library['is_pro']) ? '<span class="jltma-pro-ribbon">Pro</span>' : '', $icons_library); ?>
                                            </div>

                                            <div class="jltma-master-addons-content-inner">
                                                <div class="jltma-master-addons-features-title">
                                                    <?php echo esc_html($icons_library['title']); ?>
                                                </div> <!-- jltma_master_addons-features-title -->
                                                <div class="jltma-addons-tooltip inline-block">
                                                    <?php
                                                    Master_Addons_Helper::jltma_admin_tooltip_info('Demo', $icons_library['demo_url'], 'eicon-device-desktop');
                                                    Master_Addons_Helper::jltma_admin_tooltip_info('Documentation', $icons_library['docs_url'], 'eicon-info-circle-o');
                                                    Master_Addons_Helper::jltma_admin_tooltip_info('Video Tutorial', $icons_library['tuts_url'], 'eicon-video-camera');
                                                    ?>
                                                </div>
                                            </div> <!-- .jltma_master_addons-content-inner -->


                                            <div class="jltma-master-addons_feature-switchbox <?php echo apply_filters('master_addons/addons/pro_switchbox_class', '', $icons_library); ?>">
                                                <label for="<?php echo esc_attr($icons_library['key']); ?>" class="switch switch-text switch-primary switch-pill <?php echo apply_filters('master_addons/addons/pro_label_class', '', $icons_library); ?>">
                                                    <?php
                                                    echo apply_filters(
                                                        'master_addons/addons/pro_checkbox_render',
                                                        '<input type="checkbox" id="' . esc_attr($icons_library['key']) . '" class="jltma-switch-input" name="' . esc_attr($icons_library['key']) . '" ' .
                                                            (!empty($icons_library['is_pro']) ? ' disabled' : checked(1, $this->jltma_get_icons_library_settings[$icons_library['key']], false)) . '>',
                                                        $icons_library,
                                                        $this->jltma_get_icons_library_settings[$icons_library['key']]
                                                    );
                                                    ?>
                                                    <span data-on="On" data-off="Off" class="jltma-switch-label"></span>
                                                    <span class="jltma-switch-handle"></span>
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                <?php } ?>

                            </div>


                        </div> <!--  .master_addons_extensions-->

                    </div>
                </div> <!-- .master-addons-el-dashboard-tabs-wrapper-->
            </form>
        </div>
    </div>
</div>
