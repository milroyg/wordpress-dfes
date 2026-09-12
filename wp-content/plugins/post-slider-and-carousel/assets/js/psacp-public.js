( function($) {

	'use strict';

	/* Slider */
	psacp_init_post_slider();

	/* Carousel Slider */
	psacp_init_post_carousel();
	
	/* Widget Scrolling */
	psacp_init_vertical_scrolling_wdgt();

})( jQuery );

/* Slider */
function psacp_init_post_slider() {
	jQuery( '.psacp-post-slider-wrap' ).each(function( index ) {

		if( jQuery(this).hasClass('owl-loaded') ) {
			return;
		}

		var slider_id 	= jQuery(this).attr('id');
		var slider		= jQuery('#'+slider_id);
		var conf 		= JSON.parse( jQuery(this).attr('data-conf') );

		if( Psacp.fix_owl_conflict == 1 && jQuery('#'+slider_id).hasClass('owl-loaded') ) {
			jQuery('#'+slider_id).data('owl.carousel').destroy();
			jQuery('#'+slider_id).removeClass('owl-theme');
		}

		slider.owlCarousel({
				loop 				: conf.loop,
				margin 				: 5,
				items 				: 1,
				nav 				: conf.arrows,
				dots 				: conf.dots,
				autoplay 			: conf.autoplay,
				autoplayTimeout		: parseInt( conf.autoplay_interval ),
				autoplaySpeed		: (conf.speed == 'false') ? false : parseInt( conf.speed ),
				navElement 			: 'span',
				rtl					: conf.rtl,
				autoplayHoverPause	: ( conf.autoplay == false ) ? false : true,
		});
	});
}

/* Carousel Slider */
function psacp_init_post_carousel() {
	jQuery( '.psacp-post-carousel-wrap' ).each(function( index ) {

		if( jQuery(this).hasClass('owl-loaded') ) {
			return;
		}

		var carousel_id 	= jQuery(this).attr('id');		
		var conf 			= JSON.parse( jQuery(this).attr('data-conf') );
		var items			= parseInt( conf.slide_show );
		var slide_scroll	= parseInt( conf.slide_scroll );

		if( Psacp.fix_owl_conflict == 1 && jQuery('#'+carousel_id).hasClass('owl-loaded') ) {
			jQuery('#'+carousel_id).data('owl.carousel').destroy();
			jQuery('#'+carousel_id).removeClass('owl-theme');
		}

		jQuery('#'+carousel_id).owlCarousel({
			items 				: items,
			loop 				: conf.loop,
			slideBy 			: slide_scroll,
			margin				: 20,
			nav 				: conf.arrows,
			dots 				: conf.dots,
			autoplay 			: conf.autoplay,
			autoplayTimeout		: parseInt( conf.autoplay_interval ),
			autoplaySpeed		: (conf.speed == 'false') ? false : parseInt( conf.speed ),
			navElement 			: 'span',
			rtl					: conf.rtl,
			autoplayHoverPause	: ( conf.autoplay == false ) ? false : true,
			responsiveClass		: true,
			responsive:{
				0:{
					items 		: 1,
					slideBy 	: 1,
					dots 		: false,
				},
				568:{
					slideBy	 	: ( slide_scroll >= 2 ) ? 2 : slide_scroll,
					items 		: ( items >= 2 ) ? 2 : items,
				},
				768:{
					slideBy	: ( slide_scroll >= 2 ) ? 2 : slide_scroll,
					items	: ( items >= 2 ) ? 2 : items,
				},
				1024:{
					slideBy	: ( slide_scroll >= 3 ) ? 3 : slide_scroll,
					items	: ( items >= 3 ) ? 3 : items,
				},
				1100:{
					slideBy	: slide_scroll,
					items	: items,
				}
			}
		});
	});
}

/* Vertical Scrolling Widget */
function psacp_init_vertical_scrolling_wdgt() {
	jQuery( '.psacp-post-scroling-wdgt-js' ).each(function( index ) {

		var ticker_id	= jQuery(this).attr('id');
		var conf		= JSON.parse( jQuery(this).attr('data-conf') );

		if( typeof(ticker_id) != 'undefined' && ticker_id != '' ) {

			var ticker = jQuery('#'+ticker_id+' .psacp-vticker-scroling-wdgt-js').easyTicker({
				easing		: 'swing',
				height		: conf.height,
				speed		: parseInt(conf.speed),
				interval	: parseInt(conf.pause),
				mousePause	: false,
				autoplay	: true,
			});
		}
	});
}