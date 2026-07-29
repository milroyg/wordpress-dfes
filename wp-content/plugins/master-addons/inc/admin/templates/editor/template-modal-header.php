<?php

/**
 * Template Library Header
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div id="ma-el-template-modal-header-logo-area"></div>
<div id="ma-el-template-modal-header-tabs"></div>
<div id="ma-el-template-modal-header-actions">
    <?php if (class_exists('MasterAddons\Inc\Classes\Template_Library_Cache')): 
        $cache_manager = \MasterAddons\Inc\Classes\Template_Library_Cache::get_instance();
        $cache_stats = $cache_manager->get_cache_stats();
        $total_templates = isset($cache_stats['total_templates']) ? $cache_stats['total_templates'] : 0;
        
        // Only show cache status if templates are cached
        if ($total_templates > 0):
    ?>
        <div id="ma-el-template-cache-status" class="elementor-template-library-header-item" title="Cache Status: <?php echo esc_attr($total_templates); ?> templates cached">
            <i class="eicon-database-solid"></i>
            <span class="cache-count"><?php echo esc_html($total_templates); ?></span>
        </div>
    <?php endif; ?>
        <div id="ma-el-template-cache-refresh" class="elementor-template-library-header-item" title="Refresh Cache">
            <i class="eicon-sync"></i>
        </div>
    <?php endif; ?>
</div>
<div id="ma-el-template-modal-header-close-modal" class="elementor-template-library-header-item" title="<?php echo esc_attr__( 'Close', 'master-addons' ); ?>">
        <i class="eicon-close" title="Close"></i>
</div>
