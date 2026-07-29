(function(){
//#region dev/js/addons/free/ma-fancybox-popup.js
/**
* Start fancybox popup helper script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_FancyboxPopup = function($scope, $) {
		if ($.isFunction($.fn.fancybox)) $("[data-fancybox]").fancybox({});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/global", JLTMA_FancyboxPopup);
	});
})(jQuery, window.elementorFrontend);
/**
* End fancybox popup helper script
*/
//#endregion
})();
