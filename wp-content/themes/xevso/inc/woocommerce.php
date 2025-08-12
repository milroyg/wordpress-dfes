<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package xevso
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)-in-3.0.0
 *
 * @return void
 */
function xevso_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
	
}
add_action( 'after_setup_theme', 'xevso_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */

function xevso_woocommerce_scripts() {
	wp_enqueue_style( 'xevso-woocommerce-style', get_template_directory_uri() . '/woocommerce.css' );
	wp_enqueue_style( 'magnific-popup-css', get_template_directory_uri() . '/assets/gallery/magnific-popup.css' );
	wp_enqueue_script( 'jquery-magnific-popup', get_template_directory_uri() . '/assets/gallery/jquery-magnific-popup-min.js', array('jquery') );
	wp_enqueue_script( 'xevso-woojs', get_template_directory_uri() . '/assets/gallery/woojs.js', array('jquery-magnific-popup') );

	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'xevso-woocommerce-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'xevso_woocommerce_scripts' );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );*/

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function xevso_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'xevso_woocommerce_active_body_class' );

/**
 * Products per page.
 *
 * @return integer number of products.
 */
function xevso_woocommerce_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'xevso_woocommerce_products_per_page' );

/**
 * Product gallery thumnbail columns.
 *
 * @return integer number of columns.
 */
function xevso_woocommerce_thumbnail_columns() {
	return 4;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'xevso_woocommerce_thumbnail_columns' );

/**
 * Default loop columns on product archives.
 *
 * @return integer products per row.
 */

function xevso_woocommerce_loop_columns() {
	if( is_active_sidebar('tuchno-shop') && ! is_product_category() ){
		$retrun = '3';
	}else{
		$retrun = '4';
	}
	return $retrun;
}
add_filter( 'loop_shop_columns', 'xevso_woocommerce_loop_columns' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function xevso_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 4,
		'columns'        => 4,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'xevso_woocommerce_related_products_args' );

if ( ! function_exists( 'xevso_woocommerce_product_columns_wrapper' ) ) {
	/**
	 * Product columns wrapper.
	 *
	 * @return  void
	 */
	function xevso_woocommerce_product_columns_wrapper() {
		$columns = xevso_woocommerce_loop_columns();
		echo '<div class="columns-' . absint( $columns ) . '">';
	}
}
add_action( 'woocommerce_before_shop_loop', 'xevso_woocommerce_product_columns_wrapper', 40 );

if ( ! function_exists( 'xevso_woocommerce_product_columns_wrapper_close' ) ) {
	/**
	 * Product columns wrapper close.
	 *
	 * @return  void
	 */
	function xevso_woocommerce_product_columns_wrapper_close() {
		echo '</div>';
	}
}
add_action( 'woocommerce_after_shop_loop', 'xevso_woocommerce_product_columns_wrapper_close', 40 );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'xevso_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function xevso_woocommerce_wrapper_before() {
		?>
		<div class="default-page-section xevso-woo-columns">
			<div class="container">
			<?php
	}
}
add_action( 'woocommerce_before_main_content', 'xevso_woocommerce_wrapper_before' );

if ( ! function_exists( 'xevso_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function xevso_woocommerce_wrapper_after() {
			?>
			</div><!-- #main -->
		</div><!-- #primary -->
		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'xevso_woocommerce_wrapper_after' );

/**
 * Sample implementation of the WooCommerce Mini Cart.
 *
 * You can add the WooCommerce Mini Cart to header.php like so ...
 *
	<?php
		if ( function_exists( 'xevso_woocommerce_header_cart' ) ) {
			xevso_woocommerce_header_cart();
		}
	?>
 */

if ( ! function_exists( 'xevso_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function xevso_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		xevso_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'xevso_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'xevso_woocommerce_cart_link' ) ) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function xevso_woocommerce_cart_link() {
		?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'xevso' ); ?>">
			<span class="fa fa-shopping-basket"></span>
			<?php
			$item_count_text = sprintf(
				/* translators: number of items in the mini cart. */
				_n( '%d', '%d', WC()->cart->get_cart_contents_count(), 'xevso' ),
				WC()->cart->get_cart_contents_count()
			);
			?>
			<span class="count"><?php echo esc_html( $item_count_text ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'xevso_woocommerce_header_cart' ) ) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function xevso_woocommerce_header_cart() {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
		<ul id="site-header-cart" class="xevso-hmini">
				<?php xevso_woocommerce_cart_link(); ?>
			<li class="xevso-mini-cart-items">
				<?php
				$instance = array(
					'title' => '',
				);
				the_widget( 'WC_Widget_Cart', $instance );
				?>
			</li>
		</ul>
		<?php
	}
}

// Remove woocommerce defaults breadcrumb
add_action( 'init', 'xevso_remove_wc_breadcrumbs' );
function xevso_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}
