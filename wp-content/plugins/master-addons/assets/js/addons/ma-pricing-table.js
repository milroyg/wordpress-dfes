(function(){
//#region dev/js/addons/free/ma-pricing-table.js
/**
* Start pricing table widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_PricingTable = function($scope, $) {
		var $jltma_pricing_table = $scope.find(".jltma-price-table-details ul");
		if (!$jltma_pricing_table.length) return;
		var $tooltip = $jltma_pricing_table.find("> .jltma-tooltip-item"), widgetID = $scope.data("id");
		$tooltip.each(function(index) {
			tippy(this, {
				allowHTML: false,
				theme: "jltma-pricing-table-tippy-" + widgetID,
				appendTo: document.body
			});
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-pricing-table.default", JLTMA_PricingTable);
	});
})(jQuery, window.elementorFrontend);
/**
* End pricing table widget script
*/
//#endregion
})();
