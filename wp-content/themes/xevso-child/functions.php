<?php
// Load parent theme stylesheet
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('xevso-parent-style', get_template_directory_uri() . '/style.css');
});


//ACF Delete pdf
add_filter('acf/update_value/name=upload_pdf', function($value, $post_id, $field) {
    // Get the old attachment ID
    $old_value = get_field('upload_pdf', $post_id, false); // false = raw ID

    // If a new file was uploaded, and it's different from old, delete old
    if ($old_value && $value && $value != $old_value) {
        wp_delete_attachment($old_value, true);
    }

    // If the file is removed (i.e., $value is empty) but old file exists, delete it
    if (empty($value) && $old_value) {
        wp_delete_attachment($old_value, true);
    }

    return $value;
}, 10, 3);

//Cookie expiration
// Set session timeout to 30 minutes of inactivity
function dfes_auto_logout_after_inactivity() {
    if ( is_user_logged_in() && is_admin() ) {
        $timeout = 1800; // 30 minutes in seconds

        ?>
        <script>
            let timer;
            const timeout = <?php echo esc_js( $timeout * 1000 ); ?>; // JS milliseconds

            function resetTimer() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    alert("Session expired due to inactivity.");
                    window.location.href = "<?php echo wp_logout_url(); ?>";
                }, timeout);
            }

            // Reset timer on user activity
            window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;
            document.onscroll = resetTimer;
            document.onclick = resetTimer;
        </script>
        <?php
    }
}
add_action( 'admin_footer', 'dfes_auto_logout_after_inactivity' );
//Remove Fetch-Priority in images STQC
add_filter( 'wp_get_loading_optimization_attributes', function( $attributes, $context ) {
    if ( isset( $attributes['fetchpriority'] ) && $attributes['fetchpriority'] === 'high' ) {
        unset( $attributes['fetchpriority'] );
    }
    return $attributes;
}, 10, 2 );
