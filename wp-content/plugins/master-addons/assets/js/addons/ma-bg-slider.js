(function(){
//#region dev/js/addons/free/ma-bg-slider.js
/**
* Start background slider extension script
*/
(function($, elementor) {
	"use strict";
	function isElementorEditor() {
		return typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode();
	}
	var JLTMA_BgSlider = function($scope, $) {
		if (!isElementorEditor()) {
			if (!$scope.hasClass("has_ma_el_bg_slider")) return;
		} else if (!$scope.find(".ma-el-section-bs").length) return;
		var ma_el_slides = [], ma_el_slides_json = [], ma_el_transition, ma_el_animation, ma_el_custom_overlay, ma_el_overlay, ma_el_cover, ma_el_delay, ma_el_timer;
		var slider_images;
		if (!isElementorEditor()) slider_images = $scope.attr("data-ma-el-bg-slider-images");
		else {
			var slider_wrapper = $scope.find(".ma-el-section-bs-inner");
			if (slider_wrapper.length) slider_images = slider_wrapper.attr("data-ma-el-bg-slider");
		}
		if (!slider_images) return;
		if (!isElementorEditor()) {
			ma_el_transition = $scope.attr("data-ma-el-bg-slider-transition");
			ma_el_animation = $scope.attr("data-ma-el-bg-slider-animation");
			ma_el_custom_overlay = $scope.attr("data-ma-el-bg-custom-overlay");
			ma_el_cover = $scope.attr("data-ma-el-bg-slider-cover");
			ma_el_delay = $scope.attr("data-ma-el-bs-slider-delay");
			ma_el_timer = $scope.attr("data-ma-el-bs-slider-timer");
			if (ma_el_custom_overlay == "yes") ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
			else {
				var overlay_file = $scope.attr("data-ma-el-bg-slider-overlay");
				if (overlay_file) ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/" + overlay_file + ".png";
				else ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
			}
		} else {
			var slider_wrapper = $scope.find(".ma-el-section-bs-inner");
			ma_el_transition = slider_wrapper.attr("data-ma-el-bg-slider-transition");
			ma_el_animation = slider_wrapper.attr("data-ma-el-bg-slider-animation");
			ma_el_custom_overlay = slider_wrapper.attr("data-ma-el-bg-custom-overlay");
			ma_el_cover = slider_wrapper.attr("data-ma-el-bg-slider-cover");
			ma_el_delay = slider_wrapper.attr("data-ma-el-bs-slider-delay");
			ma_el_timer = slider_wrapper.attr("data-ma-el-bs-slider-timer");
			if (ma_el_custom_overlay == "yes") ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
			else {
				var overlay_file = slider_wrapper.attr("data-ma-el-bg-slider-overlay");
				if (overlay_file && overlay_file !== "00.png") ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/" + overlay_file;
				else ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
			}
		}
		ma_el_slides = slider_images.split(",");
		$.each(ma_el_slides, function(key, value) {
			var slide = [];
			slide.src = value;
			ma_el_slides_json.push(slide);
		});
		var slider_container;
		if (!$scope.find(".ma-el-section-bs").length) $scope.prepend("<div class=\"ma-el-section-bs\"><div class=\"ma-el-section-bs-inner\"></div></div>");
		slider_container = $scope.find(".ma-el-section-bs-inner");
		slider_container.vegas({
			slides: ma_el_slides_json,
			transition: ma_el_transition,
			animation: ma_el_animation,
			overlay: ma_el_overlay,
			cover: ma_el_cover == "true" ? true : false,
			delay: parseInt(ma_el_delay) || 5e3,
			timer: ma_el_timer == "true" ? true : false,
			init: function() {
				if (ma_el_custom_overlay == "yes") slider_container.children(".vegas-overlay").css("background-image", "");
			}
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/global", JLTMA_BgSlider);
		elementorFrontend.hooks.addAction("frontend/element_ready/container", JLTMA_BgSlider);
	});
})(jQuery, window.elementorFrontend);
/**
* End background slider extension script
*/
//#endregion
})();
