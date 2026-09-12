<?php

/**
 * Template Library Header
 *
 * Rendered as an Underscore/Backbone view template on editor load, so no cache
 * lookups happen here. The cache count is fetched over AJAX only when the
 * Master Addons templates modal is actually opened.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div id="ma-el-template-modal-header-logo-area"></div>
<div id="ma-el-template-modal-header-tabs"></div>
<div id="ma-el-template-modal-header-actions">
    <?php if (class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')): ?>
        <?php
        /**
         * The cache-count badge that used to sit here counted templates held on
         * the site. Templates are served from the remote API now and nothing is
         * kept on the client, so the number only reported whatever happened to
         * be in a transient -- a figure nobody could act on, next to a tooltip
         * claiming the site was storing them. The refresh control stays: it
         * still clears the cached listing and refetches.
         */
        ?>
        <div id="ma-el-template-cache-refresh" class="elementor-template-library-header-item" title="<?php echo esc_attr__('Refresh Cache', 'master-addons'); ?>">
            <i class="eicon-sync"></i>
        </div>
    <?php endif; ?>
</div>
<div id="ma-el-template-modal-header-close-modal" class="elementor-template-library-header-item" title="<?php echo esc_attr__( 'Close', 'master-addons' ); ?>">
        <i class="eicon-close" title="Close"></i>
</div>
