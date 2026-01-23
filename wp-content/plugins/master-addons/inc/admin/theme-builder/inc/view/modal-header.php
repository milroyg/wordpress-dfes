<div class="jltma-pop-contents-head">
    <!-- Header top section with logo and close button -->
    <div class="jltma-header-top">
        <div class="jltma-popup-head-content" style="display: flex; align-items:center; gap:8px">
            <span>
                <img src="<?php echo JLTMA_IMAGE_DIR . 'logo.svg'; ?>" style="width: 30px;">
            </span>
            <h3>
                <?php echo esc_html__(' Theme Builder', 'master-addons' );?>
            </h3>
        </div>

        <!-- Tab Navigation inside header -->
        <div class="jltma-tab-nav">
            <button type="button" class="jltma-tab-button active" data-tab="general">
                <?php esc_html_e('General', 'master-addons'); ?>
            </button>
            <button type="button" class="jltma-tab-button" data-tab="conditions">
                <?php esc_html_e('Conditions', 'master-addons'); ?>
            </button>
        </div>

        <div class="jltma-pop-close">
            <button class="close-btn" data-dismiss="modal"><span class="dashicons dashicons-no-alt"></span></button>
        </div>
    </div>
</div>
