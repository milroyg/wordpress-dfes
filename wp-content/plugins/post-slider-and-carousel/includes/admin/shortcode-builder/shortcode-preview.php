<?php
/**
 * Shortcode Preview 
 *
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$authenticated = false;

// Getting shortcode value
if( ! empty( $_POST['psacp_customizer_shrt'] ) ) {
	$shortcode_val = wp_unslash( $_POST['psacp_customizer_shrt'] ); // WPCS: input var ok, CSRF ok.
} else {
	$shortcode_val = '';
}

// For authentication so no one can use page via URL
if( isset( $_SERVER['HTTP_REFERER'] ) ) {
	$url_query  = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
	parse_str( $url_query, $referer );

	if( ! empty( $referer['page'] ) && ( 'psacp-shrt-builder' == $referer['page'] || 'psacp-layout' == $referer['page'] ) ) {
		$authenticated = true;
	}
}

// Check Authentication else exit
if( ! $authenticated ) {
	wp_die( esc_html__('Sorry, you are not allowed to access this page.', 'post-slider-and-carousel') );
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta http-equiv="Imagetoolbar" content="No" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php esc_html_e("Shortcode Preview", "post-slider-and-carousel"); ?></title>

		<?php wp_print_styles('common'); ?>
		<link rel="stylesheet" href="<?php echo esc_url( PSAC_URL."assets/css/font-awesome.min.css?ver=".PSAC_VERSION ); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo esc_url( PSAC_URL."assets/css/owl.carousel.min.css?ver=".PSAC_VERSION ); ?>" type="text/css" />
		<link rel="stylesheet" href="<?php echo esc_url( PSAC_URL."assets/css/psacp-public.css?ver=".PSAC_VERSION ); ?>" type="text/css" />
		<?php do_action( 'psacp_shortcode_preview_head', $shortcode_val ); ?>

		<style type="text/css">
			body{background: #fff; overflow-x: hidden;}
			.psacp-customizer-container{padding:0 16px;}
			.psacp-customizer-container a[href^="http"]{cursor:not-allowed !important;}
			a:focus, a:active{box-shadow: none; outline: none;}
			.psacp-link-notice{display: none; position: fixed; color: #a94442; background-color: #f2dede; border:1px solid #ebccd1; max-width:300px; width: 100%; left:0; right:0; bottom:30%; margin:auto; padding:10px; text-align: center; z-index: 1050;}
		</style>
		<?php wp_print_scripts( array('jquery') ); ?>
	</head>
	<body>
		<div id="psacp-customizer-container" class="psacp-customizer-container">
			<?php if( $shortcode_val ) {
				echo do_shortcode( $shortcode_val );
			} ?>
		</div>
		<div class="psacp-link-notice"><?php esc_html_e('Sorry, You can not visit the link in preview mode.', 'post-slider-and-carousel'); ?></div>

		<script type='text/javascript'> 
		/*<![CDATA[*/
		var Psacp = <?php echo wp_json_encode(array(
												'ajax_url'		=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
												'is_mobile'		=> ( wp_is_mobile() ) ? 1 : 0,
												'is_rtl'		=> ( is_rtl() )       ? 1 : 0,
											)); ?>;
		/*]]>*/
		</script>
		<script type="text/javascript" src="<?php echo esc_url( PSAC_URL."assets/js/owl.carousel.min.js?ver=".PSAC_VERSION ); ?>"></script>
		<script type="text/javascript" src="<?php echo esc_url( PSAC_URL."assets/js/psacp-public.js?ver=".PSAC_VERSION ); ?>"></script>
		<?php do_action( 'psacp_shortcode_preview_footer', $shortcode_val ); ?>
		<script type="text/javascript">
		( function($) {

			'use strict';

			/* To avoid the browser POST data resend warning when we refresh the page */
			if ( window.history.replaceState ) {
				window.history.replaceState( null, null, window.location.href );
			}
			
			$(document).on('click', 'a', function(event) {

				var href_val = $(this).attr('href');

				if( href_val.indexOf('javascript:') < 0 ) {
					$('.psacp-link-notice').fadeIn();
				}
				event.preventDefault();

				setTimeout(function() {
					$(".psacp-link-notice").fadeOut('normal');
				}, 4000 );
			});
		})( jQuery );
		</script>
	</body>
</html>