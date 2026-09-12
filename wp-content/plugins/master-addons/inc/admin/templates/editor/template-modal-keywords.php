<?php
/**
 * Template Library Keywords Filter
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<#
/**
 * The categories dropdown next to this one is a CollectionView, which fills its
 * own <ul>. This one is an ItemView handed the whole keyword map, so the list
 * has to be written here -- it used to ship an empty <ul>, which is why the
 * control only ever said "All Keywords" and opened onto nothing.
 *
 * With no keywords for the tab there is nothing to filter by, so the control is
 * not rendered at all rather than offering an empty menu.
 */
#>
<# if ( ! _.isEmpty( keywords ) ) { #>
<div class="ma-el-keywords-filters-wrap">
    <button type="button" class="ma-el-keywords-filter-action">
        <span id="ma-el-keywords-selected-filter"><?php esc_html_e( 'All Keywords', 'master-addons' ); ?></span>
        <i class="dashicons dashicons-arrow-down-alt2"></i>
    </button>
    <div class="ma-el-keywords-filters-container">
        <ul>
            <li class="ma-el-keywords-filter-item" data-keyword=""><?php esc_html_e( 'All Keywords', 'master-addons' ); ?></li>
            <# _.each( keywords, function( title, slug ) { #>
                <li class="ma-el-keywords-filter-item" data-keyword="{{ slug }}">{{{ title }}}</li>
            <# } ); #>
        </ul>
    </div>
</div>
<# } #>
