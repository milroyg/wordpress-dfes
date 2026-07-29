<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="jltma-pop-contents-body" id="jltma_popup_modal_body">

    <div class="jltma-spinner"></div>

    <div class="jltma-pop-contents-padding">
        <form action="" method="get" id="jltma_popup_modal_form" data-open-editor="0" data-editor-url="<?php echo esc_url( get_admin_url() ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce('wp_rest') ); ?>">
        <!-- Tab Contents Container -->
        <div class="jltma-modal-content-area">
            <!-- General Tab Content -->
            <div class="jltma-tab-content jltma-tab-general active">
                    <div class="jltma-label-container">
                        <div class="jltma-row">

                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <label for="jltma-popup-title">
                                    <strong>
                                        <?php esc_html_e('Popup Name', 'master-addons'); ?>
                                    </strong>
                                </label>
                            </div>
                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <input required type="text" name="title" class="jltma_popup_modal-title jltma-form-control" placeholder="<?php echo esc_html__('Popup Name here', 'master-addons'); ?>">
                            </div>

                            <input type="hidden" name="trigger_type" value="page_load">
                            <input type="hidden" name="trigger_delay" value="0">

                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <label for="jltma-popup-activation-label">
                                    <strong>
                                        <?php esc_html_e('Activation', 'master-addons'); ?>
                                    </strong>
                                </label>
                            </div>

                            <div class="jltma-form-group mb-2 jltma-col-6 jtlma-mega-switcher">
                                <input type="checkbox" value="yes" class="jltma-admin-control-input jltma-enable-switcher" name="activation" id="jltma_popup_activation_modal_input">
                                <label class="jltma-admin-control-label" for="jltma_popup_activation_modal_input">
                                    <span class="jltma-admin-control-label-switch" data-active="ON" data-inactive="OFF"></span>
                                </label>
                            </div>

                        </div>
                    </div>
            </div> <!-- General tab-content -->

            <!-- Conditions Tab Content - Exact same as Theme Builder -->
            <div class="jltma-tab-content jltma-tab-conditions">
                    <div class="jltma-label-container">
                        
                        <!-- Elementor-style Conditions Repeater -->
                        <div class="jltma-conditions-section">
                            <div class="jltma-conditions-header">
                                <h3><?php esc_html_e('Where Do You Want to Display Your Popup?', 'master-addons'); ?></h3>
                                <p class="jltma-conditions-description">
                                    <?php esc_html_e('Set the conditions that determine where your Popup is used throughout your site.', 'master-addons'); ?><br>
                                    <?php esc_html_e('For example, choose \'Entire Site\' to display the popup across your site.', 'master-addons'); ?>
                                </p>
                            </div>
                            
                            <div class="jltma-conditions-repeater" id="jltma-popup-conditions-repeater">
                                <!-- Conditions will be added dynamically via JavaScript -->
                            </div>
                            
                            <div class="jltma-add-condition-container">
                                <?php
                                $add_btn_class = apply_filters('master_addons/popup_builder/add_condition_btn_class', 'jltma-add-condition jltma-btn-add-condition');
                                $add_btn_badge = apply_filters('master_addons/popup_builder/add_condition_btn_badge', '<span class="jltma-badge-pro"></span>');
                                ?>
                                <button type="button" class="<?php echo esc_attr($add_btn_class); ?>" id="jltma-popup-add-condition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                                    <?php esc_html_e('Add Condition', 'master-addons'); ?>
                                    <?php echo wp_kses_post( $add_btn_badge ); ?>
                                </button>
                            </div>
                        </div>

                        <!-- Legacy support - keep for backward compatibility but hidden -->
                        <div style="display: none;">
                            <div class="jltma_popup_modal-jltma_popup_singular-container">
                                    <br>
                                    <div class="jltma-input-group">
                                        <label class="jltma-attr-input-label"></label>
                                        <select name="jltma_popup_singular" class="jltma_popup_modal-jltma_popup_singular jltma-orm-control">
                                            <option value="all"><?php esc_html_e('All Singulars', 'master-addons'); ?></option>
                                            <option value="front_page"><?php esc_html_e('Front Page', 'master-addons'); ?></option>
                                            <option value="all_posts"><?php esc_html_e('All Posts', 'master-addons'); ?></option>
                                            <option value="all_pages"><?php esc_html_e('All Pages', 'master-addons'); ?></option>
                                            <option value="selective"><?php esc_html_e('Specific Page', 'master-addons'); ?></option>
                                            <option value="404page"><?php esc_html_e('404 Page', 'master-addons'); ?></option>
                                        </select>
                                    </div>

                                    <?php
                                        // Pro feature: Singular ID select field
                                        do_action('master_addons/popup_builder/singular_id_field');
                                    ?>
                                    </div>

                                    <?php
                                        // Pro feature: Post Types ID select field
                                        do_action('master_addons/popup_builder/post_types_id_field');
                                    ?>

                            </div>

                        </div>
                    </div>
            </div> <!-- Conditions tab-content -->
        </div> <!-- Tab Contents Container -->

        <!-- Modal Footer - Outside tabs, always visible -->
        <div class="jltma-modal-footer">
            <button type="button" class="jltma-btn-editor jltma-save-btn jltma-color-three">
                <img class="mr-1 mb-1" src="<?php echo esc_url( JLTMA_IMAGE_DIR . 'icon.png' ); ?>" alt="Master Addons Logo">
                <?php esc_html_e('Edit with Elementor', 'master-addons'); ?>
            </button>
            <button type="submit" class="jltma-popup-save jltma-save-btn jltma-color-two">
                <?php esc_html_e('Save Settings', 'master-addons'); ?>
            </button>
        </div>
        </form>

    </div>
</div>