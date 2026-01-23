<?php

/**
 * Template Library Header
 */
?>
<div id="ma-el-template-modal-header-logo-area"></div>
<div id="ma-el-template-modal-header-tabs"></div>
<div id="ma-el-template-modal-header-actions">
    <?php if (class_exists('MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager')): 
        $cache_manager = \MasterAddons\Inc\Templates\Classes\Master_Addons_Templates_Cache_Manager::get_instance();
        $cache_stats = $cache_manager->get_cache_stats();
        $total_templates = isset($cache_stats['total_templates']) ? $cache_stats['total_templates'] : 0;
        
        // Only show cache status if templates are cached
        if ($total_templates > 0):
    ?>
        <div id="ma-el-template-cache-status" class="elementor-template-library-header-item" title="Cache Status: <?php echo $total_templates; ?> templates cached">
            <i class="eicon-database-solid"></i>
            <span class="cache-count"><?php echo $total_templates; ?></span>
        </div>
    <?php endif; ?>
        <div id="ma-el-template-cache-refresh" class="elementor-template-library-header-item" title="Refresh Cache">
            <i class="eicon-sync"></i>
        </div>
    <?php endif; ?>
</div>
<div id="ma-el-template-modal-header-close-modal" class="elementor-template-library-header-item" title="<?php echo __( 'Close', 'master-addons' ); ?>">
        <i class="eicon-close" title="Close"></i>
</div>
