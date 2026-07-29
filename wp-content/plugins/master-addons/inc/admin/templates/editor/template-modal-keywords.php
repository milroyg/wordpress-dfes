<?php
/**
 * Template Library Keywords Filter
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="ma-el-keywords-filters-wrap">
    <button type="button" class="ma-el-keywords-filter-action">
        <span id="ma-el-keywords-selected-filter"><?php esc_html_e( 'All Keywords', 'master-addons' ); ?></span>
        <i class="dashicons dashicons-arrow-down-alt2"></i>
    </button>
    <div class="ma-el-keywords-filters-container">
        <ul></ul>
    </div>
</div>
