<?php
/**
 * Template Library Modal Header
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<?php
    global $pagenow;
    $library_title = 'Template Library';
    if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only admin context detection for display
        $post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only post ID for display label
        if ( $post_id ) {
                $current_post_type = get_post_type( $post_id );
            if( 'jltma_popup' === $current_post_type ){
                $library_title = 'Popup Builder';
            }
        }
    }
?>
<div class="ma-el-modal-header-logo-icon">
    <img src="<?php echo esc_url( JLTMA_IMAGE_DIR . 'logo.svg' ); ?>">
    <div class="jltma-template-lib-title">
        <!-- Templates Library -->
        <?php echo esc_html( $library_title ); ?>
    </div>
</div>
