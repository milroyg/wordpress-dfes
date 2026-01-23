<?php
/**
 * Template Library Modal Header
 */
?>
<?php
    global $pagenow;
    $library_title = 'Template Library';
    if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {
        $post_id = absint( $_GET['post'] );
        if ( $post_id ) {
                $current_post_type = get_post_type( $post_id );
            if( 'jltma_popup' === $current_post_type ){
                $library_title = 'Popup Builder';
            }
        }
    }
?>
<div class="ma-el-modal-header-logo-icon">
    <img src="<?php echo JLTMA_IMAGE_DIR . 'logo.svg'; ?>">
    <div class="jltma-template-lib-title">
        <!-- Templates Library -->
        <?php echo $library_title; ?>
    </div>
</div>
