(function(){
//#region dev/js/addons/free/ma-progressbar.js
/**
* Start progressbar widget script
*/
(function($, elementor) {
	"use strict";
	/**
	* Animated progress bar helper
	*
	* @param {string} id - Progress bar ID
	* @param {string} type - Progress bar type (line, line-bubble, circle, fan)
	* @param {number} value - Progress value
	* @param {string} strokeColor - Stroke color
	* @param {string} trailColor - Trail color
	* @param {number} strokeWidth - Stroke width
	* @param {number} strokeTrailWidth - Trail stroke width
	*/
	function animatedProgressbar(id, type, value, strokeColor, trailColor, strokeWidth, strokeTrailWidth) {
		var triggerClass = ".jltma-progress-bar-" + id;
		if ("line" == type) new ldBar(triggerClass, {
			"type": "stroke",
			"path": "M0 10L100 10",
			"aspect-ratio": "none",
			"stroke": strokeColor,
			"stroke-trail": trailColor,
			"stroke-width": strokeWidth,
			"stroke-trail-width": strokeTrailWidth
		}).set(value);
		if ("line-bubble" == type) {
			new ldBar(triggerClass, {
				"type": "stroke",
				"path": "M0 10L100 10",
				"aspect-ratio": "none",
				"stroke": strokeColor,
				"stroke-trail": trailColor,
				"stroke-width": strokeWidth,
				"stroke-trail-width": strokeTrailWidth
			}).set(value);
			jQuery(jQuery(".jltma-progress-bar-" + id).find(".ldBar-label")).animate({ left: value + "%" }, 1e3, "swing");
		}
		if ("circle" == type) new ldBar(triggerClass, {
			"type": "stroke",
			"path": "M50 10A40 40 0 0 1 50 90A40 40 0 0 1 50 10",
			"stroke-dir": "normal",
			"stroke": strokeColor,
			"stroke-trail": trailColor,
			"stroke-width": strokeWidth,
			"stroke-trail-width": strokeTrailWidth
		}).set(value);
		if ("fan" == type) new ldBar(triggerClass, {
			"type": "stroke",
			"path": "M10 90A40 40 0 0 1 90 90",
			"stroke": strokeColor,
			"stroke-trail": trailColor,
			"stroke-width": strokeWidth,
			"stroke-trail-width": strokeTrailWidth
		}).set(value);
	}
	var JLTMA_ProgressBar = function($scope, $) {
		var id = $scope.data("id"), $progressBarWrapper = $scope.find(".jltma-progress-bar-" + id), type = $progressBarWrapper.data("type"), value = $progressBarWrapper.data("progress-bar-value"), strokeWidth = $progressBarWrapper.data("progress-bar-stroke-width"), strokeTrailWidth = $progressBarWrapper.data("progress-bar-stroke-trail-width"), color = $progressBarWrapper.data("stroke-color"), trailColor = $progressBarWrapper.data("stroke-trail-color");
		$progressBarWrapper.find("svg").remove();
		$progressBarWrapper.find(".ldBar-label").remove();
		$progressBarWrapper.removeClass("ldBar");
		animatedProgressbar(id, type, value, color, trailColor, strokeWidth, strokeTrailWidth);
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-progressbar.default", JLTMA_ProgressBar);
	});
})(jQuery, window.elementorFrontend);
/**
* End progressbar widget script
*/
//#endregion
})();
