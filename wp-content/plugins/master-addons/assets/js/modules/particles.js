(function(){
//#region dev/js/modules/free/particles.js
/**
* Start particles background extension script
*/
(function($, elementor) {
	"use strict";
	function isElementorEditor() {
		return typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode();
	}
	var JLTMA_ParticlesBG = function($scope, $) {
		if ($scope.hasClass("jltma-particle-yes") || $scope.attr("data-jltma-particle") || $scope.find(".jltma-particle-wrapper").attr("data-jltma-particles-editor")) {
			let element_type = $scope.data("element_type");
			let sectionID = encodeURIComponent($scope.data("id"));
			let particlesJSON;
			if (!isElementorEditor()) particlesJSON = $scope.attr("data-jltma-particle");
			else particlesJSON = $scope.find(".jltma-particle-wrapper").attr("data-jltma-particles-editor");
			if (("section" === element_type || "column" === element_type || "container" === element_type) && particlesJSON) if (!isElementorEditor()) {
				$scope.prepend("<div class=\"jltma-particle-wrapper\" id=\"jltma-particle-" + sectionID + "\"></div>");
				try {
					let parsedData = JSON.parse(particlesJSON);
					particlesJS("jltma-particle-" + sectionID, parsedData);
					setTimeout(function() {
						window.dispatchEvent(new Event("resize"));
					}, 500);
					setTimeout(function() {
						window.dispatchEvent(new Event("resize"));
					}, 1500);
				} catch (e) {}
			} else if ($scope.hasClass("jltma-particle-yes")) try {
				let parsedData = JSON.parse(particlesJSON);
				particlesJS("jltma-particle-" + sectionID, parsedData);
				$scope.find(".elementor-column").css("z-index", 9);
				setTimeout(function() {
					window.dispatchEvent(new Event("resize"));
				}, 500);
				setTimeout(function() {
					window.dispatchEvent(new Event("resize"));
				}, 1500);
			} catch (e) {}
			else $scope.find(".jltma-particle-wrapper").remove();
		}
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/global", JLTMA_ParticlesBG);
		elementorFrontend.hooks.addAction("frontend/element_ready/container", JLTMA_ParticlesBG);
	});
})(jQuery, window.elementorFrontend);
/**
* End particles background extension script
*/
//#endregion
})();
