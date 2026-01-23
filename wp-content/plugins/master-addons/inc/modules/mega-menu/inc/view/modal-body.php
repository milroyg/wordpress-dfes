<?php
// Ensure Font Awesome is loaded for icon preview
if (!wp_style_is('jltma-font-awesome', 'enqueued') && !wp_style_is('font-awesome', 'enqueued') && !wp_style_is('font-awesome-5-all', 'enqueued')) {
    $fa_url = defined('ELEMENTOR_ASSETS_URL') ? ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css' : 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css';
    echo '<link rel="stylesheet" href="' . esc_url($fa_url) . '" />';
}

// Pro features availability - filtered through Pro_Modules.php
$jltma_megamenu_pro_features = apply_filters('master_addons/modules/mega_menu/pro_features', [
    'menu_label'  => false,
    'description' => false,
    'icon_picker' => false,
    'icon_color'  => false,
]);
?>
<div class="jltma-pop-contents-body">
    <div class="jltma-pop-contents-padding">

        <div class="jltma-modal-header">
            <div class="jltma-row">
                <ul class="jltma-tabs jltma_menu_control_nav jltma-col-4">
                    <li id="content_nav" class="jltma-nav-item">
                        <a class="jltma-nav-link active" href="#content_tab">
                            <?php esc_html_e('Content', 'master-addons' ); ?>
                        </a>
                    </li>

                    <li id="general_nav" class="jltma-nav-item">
                        <a class="jltma-nav-link" href="#general_tab">
                            <?php esc_html_e('Settings', 'master-addons' ); ?>
                        </a>
                    </li>

                    <li id="icon_nav" class="jltma-nav-item">
                        <a class="jltma-nav-link" href="#icon_tab">
                            <?php esc_html_e('Icon', 'master-addons' ); ?>
                        </a>
                    </li>

                    <li id="badge_nav" class="jltma-nav-item">
                        <a class="jltma-nav-link" href="#badge_tab">
                            <?php esc_html_e('Badge', 'master-addons' ); ?>
                        </a>
                    </li>

                </ul>

                <div class="jltma-tab-content jltma-col-8">
                    <div class="jltma-tab-pane active" id="content_tab">
                        <?php if (defined('ELEMENTOR_VERSION')) : ?>

                            <div class="jltma-pop-content-inner">
                                <div id="jltma-menu-builder-wrapper">
                                    <div class="jltma-custom-switch">
                                        <span class="jltma-switch-title jltma-menu-mega-submenu enabled_item">
                                            <?php esc_html_e('Megamenu Enabled'); ?>
                                        </span>
                                        <span class="jltma-switch-title jltma-menu-mega-submenu disabled_item">
                                            <?php esc_html_e('Megamenu Disabled'); ?>
                                        </span>

                                        <label for="jltma-menu-item-enable" class="jltma-switch">
                                            <input type="checkbox" value="1" id="jltma-menu-item-enable" />
                                            <span class="jltma-switch-slider round"></span>
                                            <span class="jltma-absolute-no"><?php esc_html_e('NO'); ?></span>
                                        </label>
                                    </div>
                                </div>

                                <button disabled type="button" id="jltma-menu-builder-trigger" class="jltma-menu-elementor-button content-edit-btn" data-toggle="modal" data-target="#jltma-mega-menu-builder-modal">
                                    <?php esc_html_e('Edit Megamenu Content'); ?>
                                </button>
                            </div>


                        <?php else : ?>
                            <p class="no-elementor-notice">
                                <?php esc_html_e('Elementor Page Builder required to Edit Megamenu Content', 'master-addons' ); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="jltma-tab-pane" id="general_tab">

                        <div class="option-table jltma-label-container">
                            <div class="jltma-row">

                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="jltma-megamenu-width-type">
                                        <strong>
                                            <?php esc_html_e('Mega Menu Width', 'master-addons' ); ?>
                                        </strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <select class="jltma-form-control" id="jltma-megamenu-width-type" style="min-width: 210px; border: 1px solid #e2e8f0; outline: none; font-size: 13px; padding: 8px 10px;">
                                        <option value="default" selected="selected"><?php esc_html_e('Default Width', 'master-addons' ); ?></option>
                                        <option value="full_width"><?php esc_html_e('Full Width', 'master-addons' ); ?></option>
                                        <option value="custom_width"><?php esc_html_e('Custom Width', 'master-addons' ); ?></option>
                                    </select>
                                    <input id="jltma-megamenu-width" class="jltma-form-control hidden" type="text" placeholder="1000px" style="min-width: 210px; border: 1px solid #e2e8f0; outline: none; font-size: 13px; padding: 8px 10px; margin-top: 10px;" />
                                </div>
                            </div>

                            <div class="jltma-row">

                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="jltma-mobile-submenu-type">
                                        <strong>
                                            <?php esc_html_e('Mobile Menu', 'master-addons' ); ?>
                                        </strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <select class="jltma-form-control" id="jltma-mobile-submenu-type" style="min-width: 210px; border: 1px solid #e2e8f0; outline: none; font-size: 13px; padding: 8px 10px;">
                                        <option value="submenu_list"><?php esc_html_e('WP Menu List', 'master-addons' ); ?></option>
                                        <option value="builder_content" selected="selected"><?php esc_html_e('Builder Content', 'master-addons' ); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="jltma-row">
                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="mega-menu-trigger-effect">
                                        <strong>
                                            <?php esc_html_e('Trigger Effect', 'master-addons' ); ?>
                                        </strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <select class="jltma-form-control" id="mega-menu-trigger-effect" style="min-width: 210px; border: 1px solid #e2e8f0; outline: none; font-size: 13px; padding: 8px 10px;">
                                        <option value="" selected="selected"><?php esc_html_e('Hover', 'master-addons' ); ?></option>
                                        <option value="click"><?php esc_html_e('Click', 'master-addons' ); ?></option>
                                    </select>
                                </div>
                            </div>


                            <div class="jltma-row">
                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="mega-menu-transition-effect">
                                        <strong>
                                        <?php esc_html_e('Transition', 'master-addons' ); ?>
                                        </strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <select class="jltma-form-control" id="mega-menu-transition-effect">
                                        <option value="" selected="selected">
                                            <?php echo esc_html__('Fade', 'master-addons' ); ?>
                                        </option>
                                        <option value="slide">
                                            <?php esc_html_e('Slide Left', 'master-addons' );?>
                                        </option>
                                        <option value="slide-left">
                                            <?php esc_html_e('Slide Right', 'master-addons' );?>
                                        </option>
                                        <option value="slide-down">
                                            <?php esc_html_e('Slide Down', 'master-addons' );?>
                                        </option>
                                        <option value="slide-up">
                                            <?php esc_html_e('Slide Up', 'master-addons' );?>
                                        </option>
                                        <option value="slide-up-fade">
                                            <?php esc_html_e('Slide Up With Fade', 'master-addons' );?>
                                        </option>
                                        <option value="slide-down-fade">
                                            <?php esc_html_e('Slide Down With Fade', 'master-addons' );?>
                                        </option>
                                        <option value="super-slidedown">
                                            <?php esc_html_e('Super SlideDown', 'master-addons' );?>
                                        </option>
                                        <option value="zoom-inout">
                                            <?php esc_html_e('Zoom In/Out', 'master-addons' );?>
                                        </option>
                                        <option value="flip-effect">
                                            <?php esc_html_e('Flip Effect', 'master-addons' );?>
                                        </option>
                                    </select>
                                </div>
                            </div>


                            <div class="jltma-row">
                                <div class="jltma-form-group mb-2 jltma-col-7 <?php echo !$jltma_megamenu_pro_features['menu_label'] ? 'jltma-disabled' : ''; ?>">
                                    <label for="mega-menu-hide-item-label">
                                        <strong><?php esc_html_e('Show Menu Label', 'master-addons'); ?></strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5 jtlma-mega-switcher <?php echo !$jltma_megamenu_pro_features['menu_label'] ? 'jltma-disabled' : ''; ?>">
                                    <input type='checkbox' id="mega-menu-hide-item-label" class='mega-menu-hide-item-label' name='mega-menu-hide-item-label' value='1' />
                                    <label for="mega-menu-hide-item-label"><?php _e("NO", "master-addons"); ?></label>
                                </div>
                                <?php if (!$jltma_megamenu_pro_features['menu_label']) : ?>
                                    <span class="jltma-pro-badge eicon-pro-icon"></span>
                                <?php endif; ?>
                            </div>

                            <div class="jltma-row">
                                <div class="jltma-form-group mb-2 jltma-col-7 <?php echo !$jltma_megamenu_pro_features['description'] ? 'jltma-disabled' : ''; ?>">
                                    <label for="jltma-menu-disable-description">
                                        <strong><?php esc_html_e('Show Description', 'master-addons'); ?></strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5 jtlma-mega-switcher <?php echo !$jltma_megamenu_pro_features['description'] ? 'jltma-disabled' : ''; ?>">
                                    <input type='checkbox' id="jltma-menu-disable-description" class='jltma-menu-disable-description' name='jltma-menu-disable-description' value='1' />
                                    <label for="jltma-menu-disable-description"><?php _e("NO", "master-addons"); ?></label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="jltma-tab-pane" id="icon_tab">

                        <div class="option-table jltma-label-container">

                            <!-- Menu Icon Section - Compact Row -->
                            <!-- <div class="jltma-form-row" style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;"> -->

                            <div class="jltma-row">
                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="jltma-menu-icon-field" style="min-width: 120px; font-size: 14px; font-weight: 600; color: #374151; margin: 0;">
                                        <?php esc_html_e('Menu Icon', 'master-addons' ); ?>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <div class="jltma-modern-icon-picker-wrapper" style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                    <div id="jltma-icon-preview-box" style="width: 48px; height: 48px; background: #2d3748; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 2px solid #2d3748; flex-shrink: 0; position: relative;">
                                        <i id="jltma-menu-icon-preview" class="fas fa-chevron-down" style="font-size: 20px; color: #fff; display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px;"></i>
                                        <button type="button" class="jltma-icon-delete-btn" style="display: none; align-items: center; justify-content: center; position: absolute; top: -6px; right: -6px; width: 22px; height: 22px; background: #ef4444; color: #fff; border: 2px solid #fff; border-radius: 50%; cursor: pointer; padding: 0; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.2); z-index: 999; font-size: 16px; font-weight: bold; line-height: 1;">×</button>
                                    </div>
                                    <button type="button" class="jltma-modern-icon-picker-button icon-picker" style="flex: 1; max-width: 180px; height: 48px; padding: 0 20px; background: #6366f1; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.2s; <?php echo !$jltma_megamenu_pro_features['icon_picker'] ? 'opacity: 0.6; pointer-events: auto;' : ''; ?>" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                                        <span>Change Icon</span>
                                    </button>
                                    <input id="jltma-menu-icon-field" class="icon-picker-input" type="hidden" value="fas fa-chevron-down" />
                                </div>
                                <?php if (!$jltma_megamenu_pro_features['icon_picker']) : ?>
                                    <span class="jltma-pro-badge eicon-pro-icon" style="position: absolute; top: 50%; right: 0; transform: translateY(-50%);"></span>
                                <?php endif; ?>
                                </div>

                            </div>

                            <!-- Icon Color Section - Compact Row -->

                            <div class="jltma-row">
                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="jltma-menu-icon-color-field" style="min-width: 120px; font-size: 14px; font-weight: 600; color: #374151; margin: 0;">
                                        <?php esc_html_e('Icon Color', 'master-addons' ); ?>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <input type="text" value="" class="jltma-menu-wpcolor-picker" id="jltma-menu-icon-color-field" />
                                    <?php if (!$jltma_megamenu_pro_features['icon_color']) : ?>
                                        <span class="jltma-pro-badge eicon-pro-icon" style="position: absolute; top: 50%; right: 0; transform: translateY(-50%);"></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="jltma-tab-pane" id="badge_tab">


                        <div class="option-table jltma-label-container">
                            <div class="jltma-row">

                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="jltma-menu-badge-text-field">
                                        <strong>
                                            <?php esc_html_e('Badge Text', 'master-addons' ); ?>
                                        </strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <input type="text" placeholder="<?php esc_html_e('Badge Text', 'master-addons' ); ?>" id="jltma-menu-badge-text-field" style="min-width: 210px;border: 1px solid #e2e8f0;outline: none;font-size: 13px;padding: 8px 10px;"/>
                                </div>


                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="jltma-menu-badge-color-field">
                                        <strong>
                                            <?php esc_html_e('Badge Color', 'master-addons' ); ?>
                                        </strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <input type="text" class="jltma-menu-wpcolor-picker" value="#6f10b5" id="jltma-menu-badge-color-field" />
                                </div>


                                <div class="jltma-form-group mb-2 jltma-col-7">
                                    <label for="jltma-menu-badge-background-field">
                                        <strong>
                                            <?php esc_html_e('Background', 'master-addons' ); ?>
                                        </strong>
                                    </label>
                                </div>
                                <div class="jltma-form-group mb-2 jltma-col-5">
                                    <input type="text" class="jltma-menu-wpcolor-picker" value="#6f10b5" id="jltma-menu-badge-background-field" />
                                </div>


                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
