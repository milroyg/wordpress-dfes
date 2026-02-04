<?php
/**
 * The template for displaying 404 pages (not found) for the Fire and Emergency Services theme.
 *
 * @package xevso-child
 */

get_header();

// Consistent with parent theme's options but with emergency services fallback
$xevso_error_page_banner = xevso_options('xevso_error_page_banner', true);
$xevso_error_page_title  = xevso_options('xevso_error_page_title') ?: esc_html__('404 - Dispatch Error', 'xevso');
$xevso_go_back_home      = xevso_options('xevso_go_back_home', true);
$xevso_error_page_button_text = xevso_options('xevso_error_page_button_text') ?: esc_html__('Return to Station', 'xevso');
?>

<style>
    .emergency-404 {
        padding: 100px 0;
        text-align: center;
    }
    .emergency-icon {
        font-size: 120px;
        color: #DC4D01; /* Matching the orange/red from child theme style.css */
        margin-bottom: 30px;
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    .error-title {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
    }
    .error-message {
        font-size: 20px;
        margin-bottom: 40px;
        color: #666;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    .emergency-btn {
        display: inline-block;
        padding: 15px 40px;
        background-color: #DC4D01;
        color: #fff !important;
        font-weight: 600;
        border-radius: 5px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .emergency-btn:hover {
        background-color: #b33e01;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(220, 77, 1, 0.3);
    }
</style>

<?php if ($xevso_error_page_banner == true) : ?>
    <div class="breadcroumb-boxs">
        <div class="container">
            <div class="breadcroumb-box">
                <div class="brea-title">
                    <h2><?php echo esc_html($xevso_error_page_title); ?></h2>
                </div>
                <?php if (function_exists('bcn_display')) : ?>
                    <div class="breadcrumb-bcn">
                        <?php bcn_display(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="error-404 not-found">
    <div class="container">
        <main id="primary" class="site-main">
            <div class="emergency-404">
                <div class="emergency-icon">
                    <i class="fa fa-fire-extinguisher"></i>
                </div>
                
                <h1 class="error-title"><?php esc_html_e('Signal Lost!', 'xevso'); ?></h1>
                
                <div class="error-message">
                    <p><?php esc_html_e('The page you are looking for has been extinguished or never existed. Our dispatchers couldn\'t locate the requested coordinates.', 'xevso'); ?></p>
                </div>

                <?php if ($xevso_go_back_home == true) : ?>
                    <div class="error-button">
                        <a class="emergency-btn" href="<?php echo esc_url(home_url('/')); ?>">
                            <i class="fa fa-home" style="margin-right: 8px;"></i>
                            <?php echo esc_html($xevso_error_page_button_text); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php get_footer(); ?>
