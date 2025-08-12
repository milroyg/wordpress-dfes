<?php
function acc_userway_enqueue_styles() {
    // Register the stylesheet
    wp_register_style(
        'acc-userway-styles', // Handle
        plugins_url('assets/css/style.css', __FILE__) // Path to the stylesheet
    );

    // Enqueue the stylesheet
    wp_enqueue_style('acc-userway-styles');
}
add_action('admin_enqueue_scripts', 'acc_userway_enqueue_styles');

function acc_userway_enqueue_script() {
    // Enqueue the JavaScript file with dependencies on jQuery and any other scripts
    wp_enqueue_script('api-script', plugin_dir_url(__FILE__) . 'api-script.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'acc_userway_enqueue_script');

function enqueue_frontend_script() {
    // Enqueue the JavaScript file on the front-end
    wp_enqueue_script('plugin-frontend-js', plugin_dir_url(__FILE__) . 'frontend.js', array('jquery'), '1.0', true);

    // Pass the site URL to the script
    wp_localize_script('plugin-frontend-js', 'AccessibleWPData', array(
        'siteUrl' => get_site_url(),
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_frontend_script');

// Hook into plugin activation to add custom admin notice
function acc_userway_activation_notice() {
    add_option( 'acc_userway_activation_notice', true );
}

// Hook into admin_init to display the notice
function acc_userway_admin_notice() {
    global $pagenow;
    // Generate the URL for the image
    $banner_logo = plugins_url('assets/images/banner-logo.svg', __FILE__);
    if ( $pagenow != 'admin.php' && get_option( 'acc_userway_activation_notice' ) && ! isset( $_COOKIE['acc_userway_activation_notice_dismissed'] ) ) {
        ?>
        <div class="notice notice-warning is-dismissible" id="plugin-activation-notice">
            <div class="notice__wrap">
                <div class="notice__wrap-col notice__wrap-col--first">
                    <img src="<?php echo $banner_logo; ?>" alt="UserWay Logo" style="width: 174px; margin-right: 10px; margin-top: 8px; margin-left: 7px;">
                    <div class="notice__wrap--text">
                        <p class="usw_banner_title"><?php _e("Enhance Your Website's Accessibility with UserWay!", "userway"); ?></p>
                        <p class="usw_banner_text usw_banner_text--mobile-hide"><?php _e("We're excited to offer AccessibleWP users advanced features and dedicated support with UserWay. </br>Upgrade now to take advantage of our comprehensive accessibility tools.", "userway"); ?></p>
                    </div>
                </div>
                
                <div class="notice__wrap-col notice__wrap-col--last">
                    <!-- <a class="lm_button"  href="https://userway.org/" target="_blank"><?php _e('Learn more', 'userway'); ?></a> -->
                    <a class="uw_button" id="plugin-button-notice" href="https://wordpress.org/plugins/userway-accessibility-widget/" target="_blank" aria-label="Install UserWay Plugin"><?php _e('Install UserWay', 'userway'); ?></a>
                </div>
            </div>
        </div>
    <?php }
}
add_action( 'admin_notices', 'acc_userway_admin_notice' );

// Hook into admin_footer to add JavaScript to set cookie on dismissal
function acc_userway_admin_script() {
    ?>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Wrap the code in a setTimeout to ensure it's executed after the DOM is fully loaded
        setTimeout(function() {
            var notice = document.getElementById('plugin-activation-notice');
            if (notice) {
                var dismissButton = notice.querySelector('.notice-dismiss');
                if (dismissButton) {
                    dismissButton.addEventListener('click', function(event) {
                        // Set a timeout of 0.5 seconds before setting the cookie
                        setTimeout(function() {
                            // Set a cookie that the notice was dismissed
                            document.cookie = "acc_userway_activation_notice_dismissed=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
                        }, 500);
                    });
                } else {
                }
            } else {
            }
        }, 1000); // Delay execution by 1 second to ensure the DOM is fully loaded
    });
    </script>
    <?php
}
add_action('admin_footer', 'acc_userway_admin_script');

// Function to set cookie value to "false" on plugin deactivation
function acc_userway_deactivation_notice() {
    // Set cookie value to "false"
    setcookie( 'acc_userway_activation_notice_dismissed', '', time() - 3600, '/'); // Set expiry time far in the future
}
