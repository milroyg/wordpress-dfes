<div class="jltma-pop-contents-body" id="jltma_hf_modal_body">

    <div class="jltma-spinner"></div>

    <div class="jltma-pop-contents-padding">
        <form action="" mathod="get" id="jltma_hf_modal_form" data-open-editor="0" data-editor-url="<?php echo get_admin_url(); ?>" data-nonce="<?php echo wp_create_nonce('wp_rest'); ?>">
        <!-- Tab Contents Container -->
        <div class="jltma-modal-content-area">
            <!-- General Tab Content -->
            <div class="jltma-tab-content jltma-tab-general active">
                    <div class="jltma-label-container">
                        <div class="jltma-row">

                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <label for="jltma-mobile-submenu-type">
                                    <strong>
                                        <?php esc_html_e('Template Title', 'master-addons'); ?>
                                    </strong>
                                </label>
                            </div>
                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <input required type="text" name="title" class="jltma_hf_modal-title jltma-form-control" placeholder="<?php echo esc_html__('Template Title here', 'master-addons'); ?>">
                            </div>

                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <label for="jltma-hf-trigger-effect">
                                    <strong>
                                        <?php esc_html_e('Template Type', 'master-addons'); ?>
                                    </strong>
                                </label>
                            </div>
                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <select name="type" class="jltma-form-control jltma_hfc_type">
                                    <!-- Theme Templates Group -->
                                    <optgroup label="<?php esc_attr_e('Theme', 'master-addons'); ?>">
                                        <option value="header" selected="selected">
                                            <?php esc_html_e('Header', 'master-addons'); ?>
                                        </option>
                                        <option value="footer">
                                            <?php esc_html_e('Footer', 'master-addons'); ?>
                                        </option>
                                        <option value="comment">
                                            <?php esc_html_e('Comment', 'master-addons'); ?>
                                        </option>
                                    </optgroup>

                                    <!-- Post Templates Group -->
                                    <optgroup label="<?php esc_attr_e('Post', 'master-addons'); ?>">
                                        <?php if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) { ?>
                                            <option value="single">
                                                <?php esc_html_e('Single', 'master-addons'); ?>
                                            </option>
                                            <option value="archive">
                                                <?php esc_html_e('Archive', 'master-addons'); ?>
                                            </option>
                                            <option value="category">
                                                <?php esc_html_e('Category', 'master-addons'); ?>
                                            </option>
                                            <option value="tag">
                                                <?php esc_html_e('Tag', 'master-addons'); ?>
                                            </option>
                                            <option value="author">
                                                <?php esc_html_e('Author', 'master-addons'); ?>
                                            </option>
                                            <option value="date">
                                                <?php esc_html_e('Date', 'master-addons'); ?>
                                            </option>
                                        <?php } else { ?>
                                            <option value="single_pro">
                                                <?php esc_html_e('Single (Pro)', 'master-addons'); ?>
                                            </option>
                                            <option value="archive_pro">
                                                <?php esc_html_e('Archive (Pro)', 'master-addons'); ?>
                                            </option>
                                            <option value="category_pro">
                                                <?php esc_html_e('Category (Pro)', 'master-addons'); ?>
                                            </option>
                                            <option value="tag_pro">
                                                <?php esc_html_e('Tag (Pro)', 'master-addons'); ?>
                                            </option>
                                            <option value="author_pro">
                                                <?php esc_html_e('Author (Pro)', 'master-addons'); ?>
                                            </option>
                                            <option value="date_pro">
                                                <?php esc_html_e('Date (Pro)', 'master-addons'); ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>

                                    <!-- Page Templates Group -->
                                    <optgroup label="<?php esc_attr_e('Page', 'master-addons'); ?>">
                                        <?php if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) { ?>
                                            <option value="page_single">
                                                <?php esc_html_e('Single', 'master-addons'); ?>
                                            </option>
                                            <option value="search">
                                                <?php esc_html_e('Search', 'master-addons'); ?>
                                            </option>
                                            <option value="404">
                                                <?php esc_html_e('Error 404', 'master-addons'); ?>
                                            </option>
                                        <?php } else { ?>
                                            <option value="page_single_pro">
                                                <?php esc_html_e('Single (Pro)', 'master-addons'); ?>
                                            </option>
                                            <option value="search_pro">
                                                <?php esc_html_e('Search (Pro)', 'master-addons'); ?>
                                            </option>
                                            <option value="404_pro">
                                                <?php esc_html_e('Error 404 (Pro)', 'master-addons'); ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>

                                    <?php if (class_exists('WooCommerce')) { ?>
                                        <!-- WooCommerce Templates Group -->
                                        <optgroup label="<?php esc_attr_e('WooCommerce', 'master-addons'); ?>">
                                            <?php if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) { ?>
                                                <option value="product">
                                                    <?php esc_html_e('Product', 'master-addons'); ?>
                                                </option>
                                                <option value="product_archive">
                                                    <?php esc_html_e('Product Archive', 'master-addons'); ?>
                                                </option>
                                            <?php } else { ?>
                                                <option value="product_pro">
                                                    <?php esc_html_e('Product (Pro)', 'master-addons'); ?>
                                                </option>
                                                <option value="product_archive_pro">
                                                    <?php esc_html_e('Product Archive (Pro)', 'master-addons'); ?>
                                                </option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="jltma-form-group mb-2 jltma-col-6">
                                <label for="jltma-hf-hide-item-label">
                                    <strong>
                                        <?php esc_html_e('Activation', 'master-addons'); ?>
                                    </strong>
                                </label>
                            </div>

                            <div class="jltma-form-group mb-2 jltma-col-6 jtlma-mega-switcher">
                                <input checked="" type="checkbox" value="yes" class="jltma-admin-control-input jltma-enable-switcher" name="activation" id="jltma_activation_modal_input">
                                <label class="jltma-admin-control-label" for="jltma_activation_modal_input">
                                    <span class="jltma-admin-control-label-switch" data-active="ON" data-inactive="OFF"></span>
                                </label>
                            </div>

                        </div>
                    </div>
            </div> <!-- General tab-content -->

            <!-- Conditions Tab Content -->
            <div class="jltma-tab-content jltma-tab-conditions">
                    <div class="jltma-label-container">
                        
                        <!-- Elementor-style Conditions Repeater -->
                        <div class="jltma-conditions-section">
                            <div class="jltma-conditions-header">
                                <h3><?php esc_html_e('Where Do You Want to Display Your Template?', 'master-addons'); ?></h3>
                                <p class="jltma-conditions-description">
                                    <?php esc_html_e('Set the conditions that determine where your Template is used throughout your site.', 'master-addons'); ?><br>
                                    <?php esc_html_e('For example, choose \'Entire Site\' to display the template across your site.', 'master-addons'); ?>
                                </p>
                            </div>
                            
                            <div class="jltma-conditions-repeater" id="jltma-conditions-repeater">
                                <!-- Conditions will be added dynamically via JavaScript -->
                            </div>
                            
                            <div class="jltma-add-condition-container">
                                <button type="button" class="jltma-add-condition jltma-btn-add-condition" id="jltma-add-condition">
                                    <?php esc_html_e('ADD CONDITIONS', 'master-addons'); ?>
                                </button>
                            </div>
                        </div>

                        <!-- Legacy support - keep for backward compatibility but hidden -->
                        <div style="display: none;">
                            <div class="jltma_hf_modal-jltma_hfc_singular-container">
                                    <br>
                                    <div class="jltma-input-group">
                                        <label class="jltma-attr-input-label"></label>
                                        <select name="jltma_hfc_singular" class="jltma_hf_modal-jltma_hfc_singular jltma-orm-control">
                                            <option value="all"><?php esc_html_e('All Singulars', 'master-addons'); ?></option>
                                            <option value="front_page"><?php esc_html_e('Front Page', 'master-addons'); ?></option>
                                            <option value="all_posts"><?php esc_html_e('All Posts', 'master-addons'); ?></option>
                                            <option value="all_pages"><?php esc_html_e('All Pages', 'master-addons'); ?></option>
                                            <option value="selective"><?php esc_html_e('Specific Page', 'master-addons'); ?></option>
                                            <option value="404page"><?php esc_html_e('404 Page', 'master-addons'); ?></option>
                                        </select>
                                    </div>

                                    <?php if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) { ?>
                                        <br>
                                        <div class="jltma_hf_modal-jltma_hfc_singular_id-container jltma_multipile_ajax_search_filed">
                                            <div class="jltma-input-group">
                                                <label class="jltma-attr-input-label"></label>
                                                <select multiple name="jltma_hfc_singular_id[]" class="jltma_hf_modal-jltma_hfc_singular_id"></select>
                                            </div>
                                            <br />
                                        </div>
                                        <?php } ?>
                                    </div>

                                    <?php if (\MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_premium()) { ?>
                                        <br>
                                        <div class="jltma_hf_modal-jltma_hfc_post_types_id-container jltma_multipile_ajax_search_filed">
                                            <div class="jltma-input-group">
                                                <label class="jltma-attr-input-label"></label>
                                                <select name="jltma_hfc_post_types_id[]" class="jltma_hf_modal-jltma_hfc_post_types_id" selected="selected"></select>
                                            </div>
                                            <br />
                                        </div>
                                    <?php } ?>

                            </div>

                        </div>
                    </div>
            </div> <!-- Conditions tab-content -->
        </div> <!-- Tab Contents Container -->

        <!-- Modal Footer - Outside tabs, always visible -->
        <div class="jltma-modal-footer">
            <button type="button" class="jltma-btn-editor jltma-save-btn jltma-color-three">
                <img class="mr-1 mb-1" src="<?php echo JLTMA_IMAGE_DIR . 'icon.png'; ?>" alt="Master Addons Logo">
                <?php esc_html_e('Edit with Elementor', 'master-addons'); ?>
            </button>
            <button type="submit" class="jltma-hf-save jltma-save-btn jltma-color-two">
                <?php esc_html_e('Save Settings', 'master-addons'); ?>
            </button>
        </div>
        </form>

    </div>
</div>
