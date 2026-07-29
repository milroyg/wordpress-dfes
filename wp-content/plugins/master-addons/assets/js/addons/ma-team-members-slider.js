(function(){
//#region dev/js/addons/free/ma-team-members-slider.js
/**
* Start team members slider widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_TeamSlider = function($scope, $) {
		if ($scope.find(".jltma-team-carousel-wrapper").eq(0).data("team-preset") == "-content-drawer") try {
			(function($) {
				$(".gridder").gridderExpander({
					scroll: false,
					scrollOffset: 0,
					scrollTo: "panel",
					animationSpeed: 400,
					animationEasing: "easeInOutExpo",
					showNav: true,
					nextText: "<span></span>",
					prevText: "<span></span>",
					closeText: "",
					onStart: function() {},
					onContent: function() {},
					onClosed: function() {}
				});
			})(jQuery);
		} catch (e) {}
		else {
			var $carousel = $scope.find(".jltma-team-carousel-slider");
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
		}
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-team-members-slider.default", JLTMA_TeamSlider);
	});
})(jQuery, window.elementorFrontend);
/**
* End team members slider widget script
*/
//#endregion
})();
