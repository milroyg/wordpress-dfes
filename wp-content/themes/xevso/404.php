<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package xevso
 */

get_header();
$xevso_error_page_banner = xevso_options('xevso_error_page_banner', true);
$xevso_error_page_title = xevso_options('xevso_error_page_title');
$xevso_error_bottom_image = xevso_options('xevso_error_bottom_image');
$xevso_not_found_text = xevso_options('xevso_not_found_text');
$xevso_go_back_home = xevso_options('xevso_go_back_home', true );
$xevso_error_page_button_text = xevso_options('xevso_error_page_button_text' );
?>
	<?php if($xevso_error_page_banner == true ) : ?>
		<div class="breadcroumb-boxs">
			<div class="container">
				<div class="breadcroumb-box">
					<div class="brea-title">
						<h2>
							<?php 
								if($xevso_error_page_title){
									echo esc_html($xevso_error_page_title);
								}
							?>
						</h2>
					</div>
					<?php if(function_exists('bcn_display')) : ?>
					<div class="breadcrumb-bcn">
						<?php bcn_display();?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div class="error-404 not-found">
		<div class="blog-details pt-120 pb-120">
			<div class="container">
			<main id="primary" class="site-main">
				<div class="error-404 not-found">
					<?php if($xevso_error_bottom_image['url']) : ?>
					<div class="error-image">
						<img src="<?php echo esc_url($xevso_error_bottom_image['url']); ?>" alt="<?php echo esc_attr($xevso_error_bottom_image['alt']) ?>">
					</div>
					<?php endif; ?>
					<div class="error-dec">
						<?php echo wp_kses_post(wpautop($xevso_not_found_text)); ?>
					</div>
					<?php if( $xevso_go_back_home == true ) : ?>
						<div class="error-button text-center">
						<a class="blob-btn" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($xevso_error_page_button_text); ?>
							<span class="blob-btn__inner">
								<span class="blob-btn__blobs">
									<span class="blob-btn__blob"></span>
									<span class="blob-btn__blob"></span>
									<span class="blob-btn__blob"></span>
									<span class="blob-btn__blob"></span>
								</span>
							</span>
						</a>
						</div>
					<?php endif; ?>
				</div>
				</main>
			</div>
		</div>
	</div><!-- .error-404 -->
<?php get_footer(); ?>