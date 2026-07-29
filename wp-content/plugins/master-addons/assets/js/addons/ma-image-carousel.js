(function(){
//#region dev/js/addons/free/ma-image-carousel.js
/**
* Start image carousel widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_ImageCarousel = function($scope, $) {
		var $carousel = $scope.find(".jltma-image-carousel-slider");
		if (!$carousel.length) return;
		var $carouselContainer = $scope.find(".swiper"), $settings = $carousel.data("settings"), Swiper = elementorFrontend.utils.swiper;
		async function initSwiper() {
			await new Swiper($carouselContainer[0], $settings);
			if ($settings.pauseOnHover) $carouselContainer.hover(function() {
				this.swiper.autoplay.stop();
			}, function() {
				this.swiper.autoplay.start();
			});
		}
		initSwiper();
		if ($.isFunction($.fn.fancybox)) $scope.find("[data-fancybox]").fancybox({});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-image-carousel.default", JLTMA_ImageCarousel);
	});
})(jQuery, window.elementorFrontend);
/**
* End image carousel widget script
*/
//#endregion
})();
