(function(){
//#region dev/js/addons/free/ma-team-slider.js
/**
* Start team slider widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_TeamSlider = function($scope, $) {
		var $carousel = $scope.find(".jltma-team-members-carousel-slider");
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
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-team-members-slider.default", JLTMA_TeamSlider);
	});
})(jQuery, window.elementorFrontend);
/**
* End team slider widget script
*/
//#endregion
})();
