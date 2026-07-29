<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Template Item
 */
?>

<div class="elementor-template-library-template-body">
    <div class="elementor-template-library-template-screenshot">
        <div class="elementor-template-library-template-preview">
            <i class="fa fa-search-plus"></i>
        </div>
        <img src="{{ thumbnail }}" alt="{{ title }}" class="ma-el-template-image" loading="lazy" onload="this.classList.add('is-loaded');this.closest('.elementor-template-library-template-screenshot').classList.add('is-loaded');" onerror="this.src='<?php echo esc_url( JLTMA_IMAGE_DIR . 'placeholder.png' ); ?>';this.classList.add('is-loaded');this.closest('.elementor-template-library-template-screenshot').classList.add('is-loaded');">
        <div class="elementor-template-library-template-name">{{{ title }}}</div>
    </div>
</div>
<div class="elementor-template-library-template-controls">
    <# if ( 'valid'===window.MasterAddonsData.license.status || ! pro ) { #>
        <button class="elementor-template-library-template-action ma-el-template-insert elementor-button elementor-button-success">
            <i class="eicon-file-download"></i>
            <span class="elementor-button-title"><?php echo esc_html__('Insert', 'master-addons' ); ?></span>
        </button>
        <# } else if ( pro ) { #>
            <a class="template-library-activate-license" href="{{{ window.MasterAddonsData.license.activateLink }}}" target="_blank">
                <i class="fa fa-external-link" aria-hidden="true"></i>
                {{{ window.MasterAddonsData.license.proMessage }}}
            </a>
            <# } #>
</div>

<!--<div class="elementor-template-library-template-name">{{{ title }}}</div>-->
