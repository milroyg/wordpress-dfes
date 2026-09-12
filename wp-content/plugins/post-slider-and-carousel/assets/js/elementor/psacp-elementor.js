(function ($) {
	"use strict";

	var PsacElementorInit = function () {

		/* Slider */
		psacp_init_post_slider();

		/* Carousel Slider */
		psacp_init_post_carousel();
	};

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/psacp_layout_elementor_widget.default', PsacElementorInit);
		elementorFrontend.hooks.addAction('frontend/element_ready/shortcode.default', PsacElementorInit);
		elementorFrontend.hooks.addAction('frontend/element_ready/text-editor.default', PsacElementorInit);
		elementorFrontend.hooks.addAction('frontend/element_ready/tabs.default', PsacElementorInit);

		elementorFrontend.hooks.addAction('frontend/element_ready/wp-widget-psacp-post-scrolling-widget.default', function( $scope ) {
			psacp_init_vertical_scrolling_wdgt();
		});
	});
}(jQuery));