<?php
/**
 * Widget Statistics Cards
 * Element Pack Style - Horizontal cards with donut charts
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get widget statistics
$widget_stats = \MasterAddons\Inc\Helper\Master_Addons_Helper::jltma_get_widget_stats();
?>
<div class="jltma-widget-stats">
    <!-- All Widgets Card -->
    <div class="jltma-stat-card">
        <div class="jltma-stat-content">
            <h3 class="jltma-stat-title"><?php esc_html_e('All Widgets', 'master-addons'); ?></h3>
            <ul class="jltma-stat-list">
                <li><span class="jltma-stat-label"><?php esc_html_e('USED:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['all']['active']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('UNUSED:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['all']['inactive']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('TOTAL:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['all']['total']); ?></span></li>
            </ul>
        </div>
        <div class="jltma-stat-chart yellow">
            <svg width="80" height="80" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="14" fill="none" stroke="#fef3c7" stroke-width="4"/>
                <circle cx="18" cy="18" r="14" fill="none" stroke="#fbbf24" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?php echo esc_attr($widget_stats['all']['percentage']); ?>, 100" transform="rotate(-90 18 18)"/>
            </svg>
        </div>
    </div>

    <!-- Core Card -->
    <div class="jltma-stat-card">
        <div class="jltma-stat-content">
            <h3 class="jltma-stat-title"><?php esc_html_e('Core', 'master-addons'); ?></h3>
            <ul class="jltma-stat-list">
                <li><span class="jltma-stat-label"><?php esc_html_e('USED:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['core']['active']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('UNUSED:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['core']['inactive']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('TOTAL:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['core']['total']); ?></span></li>
            </ul>
        </div>
        <div class="jltma-stat-chart pink">
            <svg width="80" height="80" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="14" fill="none" stroke="#fce7f3" stroke-width="4"/>
                <circle cx="18" cy="18" r="14" fill="none" stroke="#f472b6" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?php echo esc_attr($widget_stats['core']['percentage']); ?>, 100" transform="rotate(-90 18 18)"/>
            </svg>
        </div>
    </div>

    <!-- 3rd Party Card -->
    <div class="jltma-stat-card">
        <div class="jltma-stat-content">
            <h3 class="jltma-stat-title"><?php esc_html_e('3rd Party', 'master-addons'); ?></h3>
            <ul class="jltma-stat-list">
                <li><span class="jltma-stat-label"><?php esc_html_e('USED:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['third_party']['active']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('UNUSED:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['third_party']['inactive']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('TOTAL:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['third_party']['total']); ?></span></li>
            </ul>
        </div>
        <div class="jltma-stat-chart teal">
            <svg width="80" height="80" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="14" fill="none" stroke="#ccfbf1" stroke-width="4"/>
                <circle cx="18" cy="18" r="14" fill="none" stroke="#2dd4bf" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?php echo esc_attr($widget_stats['third_party']['percentage']); ?>, 100" transform="rotate(-90 18 18)"/>
            </svg>
        </div>
    </div>

    <!-- Active Card -->
    <div class="jltma-stat-card">
        <div class="jltma-stat-content">
            <h3 class="jltma-stat-title"><?php esc_html_e('Active', 'master-addons'); ?></h3>
            <ul class="jltma-stat-list">
                <li><span class="jltma-stat-label"><?php esc_html_e('CORE:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['core']['active']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('3RD PARTY:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['third_party']['active']); ?></span></li>
                <li><span class="jltma-stat-label"><?php esc_html_e('TOTAL:', 'master-addons'); ?></span> <span class="jltma-stat-value"><?php echo esc_html($widget_stats['all']['active']); ?></span></li>
            </ul>
        </div>
        <div class="jltma-stat-chart blue">
            <svg width="80" height="80" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="14" fill="none" stroke="#dbeafe" stroke-width="4"/>
                <circle cx="18" cy="18" r="14" fill="none" stroke="#3b82f6" stroke-width="4" stroke-linecap="round" stroke-dasharray="<?php echo esc_attr($widget_stats['active']['percentage']); ?>, 100" transform="rotate(-90 18 18)"/>
            </svg>
        </div>
    </div>
</div>
