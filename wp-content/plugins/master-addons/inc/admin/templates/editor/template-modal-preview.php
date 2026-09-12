<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Templates Item Preview
 *
 * The iframe's src is filled in by ModalPreviewView::onRender, which is where
 * the template's URL is known. The Live Preview link lives in the modal header
 * (see template-modal-insert-button.php) — don't add a second one here.
 */
?>
<div class="ma-el-item-notice"></div>
<div class="ma-el-item-preview-iframe">
    <iframe title="<?php echo esc_attr__( 'Template preview', 'master-addons' ); ?>" loading="lazy"></iframe>
    <div class="ma-el-item-preview-empty" hidden>
        <p><?php echo esc_html__( 'This template has no preview page yet.', 'master-addons' ); ?></p>
    </div>
</div>
