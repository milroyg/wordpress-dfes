<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.7.1
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( '', $product ); ?>>
	<div class="xevso-product-item">
		<div class="xevso-product-bg" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>)">
			<?php woocommerce_show_product_loop_sale_flash(); ?>
			<div class="xevso-pro-item-btns">
				<div class="xevso-pro-ibtn">
					<div class="xevso-pro-addtocart">
					<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
					<div class="xevso-pro-compare">
						<a href="<?php echo esc_url(home_url()); ?>/?action=yith-woocompare-add-product&amp;id=<?php echo get_the_ID();?>" class="compare" data-product_id="<?php echo get_the_ID();?>" rel="nofollow"><span class="fa fa-retweet"></span></a>
					</div>
				</div>
			</div>
		</div>
		<div class="xevso-pro-item-bottom">
			<div class="xevso-pro-ib-top">
				<div class="xevso-pro-item-title">
					<a href="<?php the_permalink(); ?>"><?php woocommerce_template_loop_product_title(); ?></a>
				</div>
				<div class="xevso-pro-item-wishlist">
					<?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
				</div>
			</div>
			<div class="xevso-pro-ib-bottom row">
				<?php woocommerce_template_loop_rating(); ?>
				<?php woocommerce_template_loop_price(); ?>
			</div>
			<div class="xevso-plist-dec">
				<p><?php echo wp_trim_words( get_the_content(), 25 ); ?></p>
			</div>
			<div class="xevso-plist-btn">
				<div class="xevso-pro-addtocart">
				<?php woocommerce_template_loop_add_to_cart(); ?>
				</div>
				<div class="xevso-pro-compare">
					<a href="<?php echo esc_url(home_url()); ?>/?action=yith-woocompare-add-product&amp;id=<?php echo get_the_ID();?>" class="compare" data-product_id="<?php echo get_the_ID();?>" rel="nofollow"><span class="fa fa-retweet"></span></a>
				</div>
				<div class="xevso-pro-listitem-wishlist xevso-pro-compare">
					<?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
				</div>
			</div>
		</div>
	</div>
</li>
