<?php
/**
 * Template Insert Button
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
    <# if ( '' !=url ) { #>
        <a class="template-library-live-preview jltma-live-preview-link" href="{{{ url }}}" target="_blank" style="text-transform: capitalize; font-weight: 400;">
            <i class="eicon-editor-external-link" aria-hidden="true"></i>
            <?php esc_html_e('Live Preview', 'master-addons' ); ?>
        </a>
    <# } #>

    <#
    /* A status of "valid" with no key behind it is not a licence, and testing
       the status alone is why a pro template still offered Insert -- which
       then failed against a library that will not serve it. */
    var jltmaLicensed = 'valid' === window.MasterAddonsData.license.status
        && window.MasterAddonsData.license.hasKey;
    #>
    <# if ( jltmaLicensed || ! pro ) { #>
        <a class="elementor-template-library-template-action ma-el-template-insert elementor-button">
            <i class="eicon-file-download"></i>
            <span class="elementor-button-title">
                <?php echo esc_html__('Insert', 'master-addons' ); ?>
            </span>
        </a>
    <# } else { #>
        <a class="template-library-activate-license elementor-button elementor-button-go-pro" href="{{{ window.MasterAddonsData.license.activateLink }}}" target="_blank">
            <i class="eicon-editor-external-link" aria-hidden="true"></i>
            {{{ window.MasterAddonsData.license.proMessage }}}
        </a>
    <# } #>
