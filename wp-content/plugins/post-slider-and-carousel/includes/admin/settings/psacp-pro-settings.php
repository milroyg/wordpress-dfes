<?php
/**
 * Premium Feature Setting Page
 * 
 * @package Post Slider and Carousel
 * @since 3.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psac_render_pro_settings() { ?>

<div id="psacp-pro-sett-wrp" class="post-box-container psacp-pro-sett-wrp">
	<div class="metabox-holder">
		<div id="psacp-css-sett" class="postbox psacp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Premium Features', 'post-slider-and-carousel' ); ?></span> <a class="pro-badge psacp-right" href="<?php echo esc_url( PSAC_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'post-slider-and-carousel' ); ?></a>
				</h2>
			</div>

			<div class="inside psacp-postbox-inside">
				<div class="psacp-pro-main-wrap">
					<h2 class="psacp-custom-size psacp-text-center">The Best Minimalist  Post Slider and Carousel Plugin</h2>
					<h3 class="psacp-text-center">Join 10,000+ Websites who are using Post Slider and Carousel to change face of<br />
static websites into new and improved online news sites, magazine hubs, online blogs, portals, and more!</h3>
					<div class="psacp-img-wrp psacp-text-center">
						<img src="<?php echo esc_url( PSAC_URL."/assets/images/pro/main-banner-1536x768.png" ); ?>" alt="main banner image" />
					</div>
					<div class="psacp-pro-button psacp-pro-large-button"><a href="<?php echo esc_url( PSAC_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'post-slider-and-carousel' ); ?></a></div>
				</div>
				<div class="psacp-cnt-row psacp-pro-main-wrap">
					<div class="psacp-cnt-wrp">
						<h3 class="psacp-custom-size psacp-text-center">Works with your favorite page builders <br />Elementor, WPBakery, Visual Composer, Gutenberg and etc.</h3>
						<h3 class="psacp-text-center">And Much More Advanced Options…</h3>
					</div>
					<div class="psacp-img-wrp psacp-text-center">
						<img src="<?php echo esc_url( PSAC_URL."/assets/images/pro/pro-features.png" ); ?>" alt="Pro features" />
					</div>
					<div class="psacp-pro-button psacp-pro-large-button"><a href="<?php echo esc_url( PSAC_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'post-slider-and-carousel' ); ?></a></div>
				</div>
				<div class="psacp-pro-main-wrap">
					<div class="psacp-cnt-wrp">
						<h3 class="psacp-custom-size psacp-text-center">5 Layouts, 2 Widgets and 30+ Designs</h3>
						<h3 class="psacp-text-center">Create unlimited layouts with more than 30+ predefined designs includes  <br /> Post Sliders, Post Carousel, GridBox Slider, Partially Sliders and many more… </h3>
					</div>
					<div class="psacp-img-wrp psacp-text-center">
						<img src="<?php echo esc_url( PSAC_URL."/assets/images/pro/pro-layout-1.png" ); ?>" alt="Pro features" />
						<img src="<?php echo esc_url( PSAC_URL."/assets/images/pro/pro-layout-2.png" ); ?>" alt="Pro features" />
						<img src="<?php echo esc_url( PSAC_URL."/assets/images/pro/pro-layout-3.png" ); ?>" alt="Pro features" />
					</div>
					<div class="psacp-pro-button psacp-pro-large-button"><a href="<?php echo esc_url( PSAC_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'post-slider-and-carousel' ); ?></a></div>
				</div>
				<div class="psacp-cnt-row psacp-pro-main-wrap">
					<div class="psacp-cnt-wrp">
						<h3 class="psacp-custom-size psacp-text-center">Post Slider and Carousel Pro comes with more features</h3>
						<h3 class="psacp-text-center">Everything you need to build a post slider & carousel.</h3>
					</div>
					<div class="psacp-cnt-grid psacp-clearfix">
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-welcome-widgets-menus"></i>
							<h3>30+ Designs  </h3>
							<p>Each layout comes with predefined designs and can be customized in order to fit your website design.</p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-star-filled"></i>
							<h3>Featured and Trending Post Support  </h3>
							<p>Each layout and design support Featured and Trending Post features.</p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-admin-post"></i>
							<h3>Post Type Support </h3>
							<p>Plugin support WordPress Post type as well custom post type created by you or with any plugin.</p>
						</div>
						
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-category"></i>
							<h3>Custom Taxonomy Support  </h3>
							<p>Plugin support WordPress category as well custom taxonomy type created by you or with any plugin. Plugin also enable option to upload the image for category.</p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-filter"></i>
							<h3>Advanced Query Builder   </h3>
							<p>Customize queries as you want. You can easily display your posts according to different criteria. Number of posts, Category, Tag, Order By, Order, Exclude, Offset etc..</p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-art"></i>
							<h3>Ready made Design Library </h3>
							<p>Plugin provide you ready made designs where you just need to add the shortcode with design number. same option given with Elementor, WPBakery page builder. </p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-controls-repeat"></i>
							<h3>Template Overriding  </h3>
							<p>Created with WordPress Template Functionality – Modify plugin design from your theme.  </p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-media-code"></i>
							<h3>No Coding Required </h3>
							<p>You can use our plugins with your favorite themes without any coding.  </p>
						</div>
							
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-embed-generic"></i>
							<h3>Layout Builder  </h3>
							<p>Layout builder  with Live Preview Panel – No hassles for documentation.   </p>
						</div>
						
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-translation"></i>
							<h3>Translation Ready   </h3>
							<p>This plugin is translation ready. We provided translation files: .po and .mo so you can easily translate it with your favorite translation tools.​    </p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-image-rotate-left"></i>
							<h3>Responsive & Light weight  </h3>
							<p>All designs are Responsive. Light weight and Fast – Created with ground level with WordPress Coding Standard. </p>
						</div>
						<div class="psacp-cnt-grid-3 psacp-columns">
							<i class="dashicons dashicons-testimonial"></i>
							<h3>Support & Documentation </h3>
							<p>We provide online & offline detailed documentation and also dedicated support to help you with whatever issue or questions you might have.  </p>
						</div>
					</div>
					<div class="psacp-pro-button psacp-pro-large-button"><a href="<?php echo esc_url( PSAC_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'post-slider-and-carousel' ); ?></a></div>
				</div>
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- end .metabox-holder -->
</div><!-- #psacp-pro-sett-wrp -->

<?php }

add_action( 'psac_settings_tab_pro', 'psac_render_pro_settings' );