<?php
/**
 * Welcome Page Settings
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_welcome_settings() { 

// Taking some variables
$show_on_front		= get_option( 'show_on_front' );
$page_for_posts_id	= get_option( 'page_for_posts' );
$page_on_front_id	= get_option( 'page_on_front' );
$reading_page_url	= admin_url( 'options-reading.php' );
$new_layout_url		= add_query_arg( array('page' => 'psacp-layout'), 'admin.php' );
?>

<div id="psacp-welcome-sett-wrp" class="post-box-container psacp-welcome-sett-wrp">
	<div class="metabox-holder">
		<div id="psacp-welcome-sett" class="postbox psacp-postbox">
			<div class="inside">
					<div class="psacp-welcome-panel">
						<div class="psacp-welcome-panel-content psacp-pro-main-wrap" style="background:#f1f1f1; padding:20px;">
							<h2 class="psacp-custom-size"><?php esc_html_e('Success, The Post Slider and Carousel is now activated!', 'post-slider-and-carousel'); ?> 😊</h2>
							<p class="psacp-about-description"><?php esc_html_e('Would you like to create one layout to check usage of Post Slider and Carousel plugin?', 'post-slider-and-carousel'); ?></p>
							<div class="psacp-cnt-grid psacp-clearfix">
								<div class="psacp-cnt-grid-3 psacp-columns">
									<h3 class="psacp-custom-size"><?php esc_html_e('Get Started', 'post-slider-and-carousel'); ?></h3>
									<p><a class="button button-primary button-hero" href="<?php echo esc_url( $new_layout_url ); ?>"><?php esc_html_e('Create Your First Layout', 'post-slider-and-carousel'); ?></a></p>
									
								</div>
								<div class="psacp-cnt-grid-3 psacp-columns">
									<h3 class="psacp-custom-size"><?php esc_html_e('Next Steps', 'post-slider-and-carousel'); ?></h3> 
									<ul>
										<li><a href="#usages-of-psacp"><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php esc_html_e('Usages', 'post-slider-and-carousel'); ?></a></li>	
										<li><a href="https://premium.infornweb.com/post-slider-and-carousel-pro/" target="_blank"><span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e('Premium Demo', 'post-slider-and-carousel'); ?></a></li>
									</ul>
								</div>
								<div class="psacp-cnt-grid-3 psacp-columns">
									<h3 class="psacp-custom-size"><?php esc_html_e('Documentation & Support', 'post-slider-and-carousel'); ?></h3>
									<ul>
										<li><a href="https://docs.infornweb.com/resources/post-slider-and-carousel/" target="_blank"><span class="dashicons dashicons-welcome-learn-more"></span> <?php esc_html_e('Documentation', 'post-slider-and-carousel'); ?></a></li>
										<li><a href="https://wordpress.org/support/plugin/post-slider-and-carousel/" target="_blank"><span class="dashicons dashicons-format-aside"></span> <?php esc_html_e('Support Forum', 'post-slider-and-carousel'); ?></a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div id="dashboard-widgets-wrap">
					<div id="dashboard-widgets" class="metabox-holder columns-1">
						<div class="postbox-container">
							<div id="usages-of-psacp" class="meta-box-sortables" style="margin-right:0px;">
								<div class="postbox">
									<div class="postbox-header">
										<h2 class="hndle">
											<span><?php esc_html_e( 'Usage of Post Slider and Carousel', 'post-slider-and-carousel' ); ?></span>
										</h2>
									</div>	
									<div class="inside">
										<div class="psacp-getting-started psacp-box">
											<h4 class="psacp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Create a Post Slider and Carousel in your Website', 'post-slider-and-carousel'); ?></h4>
											<div class="psacp-box-content">
												<p><?php esc_html_e('This is very helpful plugin to create a Post Slider and Carousel in a website. Just use the layouts builder or shortcode builder and design your page.', 'post-slider-and-carousel'); ?></p>												
											</div>
										</div>	
										<div class="psacp-getting-started psacp-box">
											<h4 class="psacp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Display latest post on home page from blog', 'post-slider-and-carousel'); ?></h4>
											<div class="psacp-box-content">
												<p><?php esc_html_e('You can display latest post from your blog on home page. You can use 5+ layout for this e.g. slider view OR Carousel View etc.', 'post-slider-and-carousel'); ?></p>
												<p><?php echo sprintf( __('Check sample %sSlider%s, %sCarousel%s and %sPartial Slide%s created with Post Slider and Carousel.', 'post-slider-and-carousel'), '<a href="https://premium.infornweb.com/post-slider-and-carousel-pro-slider-demo/" target="_blank">', '</a>', '<a href="https://premium.infornweb.com/post-slider-and-carousel-pro-carousel-demo/" target="_blank">', '</a>', '<a href="https://premium.infornweb.com/post-slider-and-carousel-pro-partial-slide-demo/" target="_blank">', '</a>' ); ?></p>
											</div>
										</div>
										<div class="psacp-getting-started psacp-box">
											<h4 class="psacp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Display Featured and Trending Post', 'post-slider-and-carousel'); ?></h4>
											<div class="psacp-box-content">
												<p><?php esc_html_e('Highlights your Featured and most Popular/Trending post. You can use 5+ layout for this e.g. slider view OR Carousel View and Grid Box Slider View etc.', 'post-slider-and-carousel'); ?></p>
												<p><?php echo sprintf( __('Check sample %sDemo%s created with Blog Designer Pack.', 'post-slider-and-carousel'), '<a href="https://premium.infornweb.com/post-slider-and-carousel-pro-featured-and-trending-post/" target="_blank">', '</a>' ); ?></p>
											</div>
										</div>
										<div class="psacp-getting-started psacp-box">
											<h4 class="psacp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Widget Demo', 'post-slider-and-carousel'); ?></h4>
											<div class="psacp-box-content">
												<p><?php esc_html_e('Display widget demo created with  Post Slider and Carousel.', 'post-slider-and-carousel'); ?></p>
												<p><?php echo sprintf( __('Check widget %sDemo%s created with  Post Slider and Carousel.', 'post-slider-and-carousel'), '<a href="https://premium.infornweb.com/post-slider-and-carousel-pro-widget-demo/" target="_blank">', '</a>' ); ?></p>
											</div>
										</div>
										
									</div><!-- .inside -->
								</div><!-- .postbox -->
							</div><!-- .meta-box-sortables -->
						</div><!-- .postbox-container -->
						
					</div><!-- #dashboard-widgets -->
				</div><!-- #dashboard-widgets-wrap -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #psacp-welcome-sett-wrp -->

<?php }

// Action to add welcome settings
add_action( 'psac_settings_tab_welcome', 'psac_render_welcome_settings' );