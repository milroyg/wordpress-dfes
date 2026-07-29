(function(){
//#region dev/js/addons/free/ma-image-comparison.js
/**
* Start image comparison widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_ImageComparison = function($scope, $) {
		var $jltma_image_comp_wrap = $scope.find(".jltma-image-comparison").eq(0), $jltma_image_data = $jltma_image_comp_wrap.data("image-comparison-settings");
		$jltma_image_comp_wrap.twentytwenty({
			default_offset_pct: $jltma_image_data.visible_ratio,
			orientation: $jltma_image_data.orientation,
			before_label: $jltma_image_data.before_label,
			after_label: $jltma_image_data.after_label,
			move_slider_on_hover: $jltma_image_data.slider_on_hover,
			move_with_handle_only: $jltma_image_data.slider_with_handle,
			click_to_move: $jltma_image_data.slider_with_click,
			no_overlay: $jltma_image_data.no_overlay
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-el-image-comparison.default", JLTMA_ImageComparison);
	});
})(jQuery, window.elementorFrontend);
/**
* End image comparison widget script
*/
//#endregion
})();
