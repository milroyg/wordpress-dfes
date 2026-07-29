<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="jltma-pop-contents-body" id="jltma_hf_modal_body">

    <div class="jltma-spinner"></div>

    <div class="jltma-pop-contents-padding">
        <form action="" mathod="get" id="jltma_hf_modal_form" data-open-editor="0" data-editor-url="<?php echo esc_url(get_admin_url()); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('wp_rest')); ?>">
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
                                <?php
                                // All template types shown. Free shows "(Pro)" suffix on pro types.
                                // Pro filter removes the "(Pro)" suffix.
                                $template_types = apply_filters('master_addons/theme_builder/template_types', [
                                    'theme' => [
                                        'label' => __('Theme', 'master-addons'),
                                        'options' => [
                                            'header'  => __('Header', 'master-addons'),
                                            'footer'  => __('Footer', 'master-addons'),
                                            'comment' => __('Comment', 'master-addons'),
                                        ],
                                    ],
                                    'post' => [
                                        'label' => __('Post', 'master-addons'),
                                        'options' => [
                                            'single'   => __('Single (Pro)', 'master-addons'),
                                            'archive'  => __('Archive (Pro)', 'master-addons'),
                                            'category' => __('Category (Pro)', 'master-addons'),
                                            'tag'      => __('Tag (Pro)', 'master-addons'),
                                            'author'   => __('Author (Pro)', 'master-addons'),
                                            'date'     => __('Date (Pro)', 'master-addons'),
                                        ],
                                    ],
                                    'page' => [
                                        'label' => __('Page', 'master-addons'),
                                        'options' => [
                                            'page_single' => __('Single (Pro)', 'master-addons'),
                                            'search'      => __('Search (Pro)', 'master-addons'),
                                            '404'         => __('Error 404 (Pro)', 'master-addons'),
                                        ],
                                    ],
                                ]);

                                // WooCommerce group
                                if (class_exists('WooCommerce')) {
                                    $template_types['woocommerce'] = [
                                        'label' => __('WooCommerce', 'master-addons'),
                                        'options' => apply_filters('master_addons/theme_builder/woocommerce_types', [
                                            'product'         => __('Product (Pro)', 'master-addons'),
                                            'product_archive' => __('Product Archive (Pro)', 'master-addons'),
                                        ]),
                                    ];
                                }
                                ?>
                                <select name="type" class="jltma-form-control jltma_hfc_type">
                                    <?php foreach ($template_types as $group_key => $group) :
                                        if (empty($group['options'])) continue;
                                    ?>
                                        <optgroup label="<?php echo esc_attr($group['label']); ?>">
                                            <?php foreach ($group['options'] as $value => $label) : ?>
                                                <option value="<?php echo esc_attr($value); ?>" <?php echo ($value === 'header') ? 'selected="selected"' : ''; ?>>
                                                    <?php echo esc_html($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
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
                                <?php $pro_conditions = apply_filters('master_addons/theme_builder/pro_conditions', false); ?>
                                <button type="button" class="jltma-add-condition jltma-btn-add-condition<?php echo !$pro_conditions ? ' jltma-pro-locked' : ''; ?>" id="jltma-add-condition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                                    <?php esc_html_e('Add Condition', 'master-addons'); ?>
                                    <?php if (!$pro_conditions) : ?>
                                        <span class="jltma-badge-pro">Pro</span>
                                    <?php endif; ?>
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

                                        <?php do_action('master_addons/theme_builder/singular_id_field'); ?>
                                    </div>

                                    <?php do_action('master_addons/theme_builder/post_types_id_field'); ?>

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
            <button type="submit" class="jltma-hf-save jltma-save-btn jltma-color-two">
                <?php esc_html_e('Save Settings', 'master-addons'); ?>
            </button>
        </div>
        </form>

    </div>
</div>
