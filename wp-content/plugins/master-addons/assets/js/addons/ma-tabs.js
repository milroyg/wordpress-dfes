(function(){
//#region dev/js/addons/free/ma-tabs.js
/**
* Start tabs widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_Tabs = function($scope, $) {
		try {
			var $tabsWrapper = $scope.find("[data-tabs]"), $tabEffect = $tabsWrapper.data("tab-effect");
			$tabsWrapper.each(function() {
				var tab = $(this);
				var isContentActive = false;
				tab.find("[data-tab]").each(function() {
					if ($(this).hasClass("active")) {}
				});
				tab.find(".jltma--advance-tab-content").each(function() {
					if ($(this).hasClass("active")) isContentActive = true;
				});
				if (!isContentActive) tab.find(".jltma--advance-tab-content").eq(0).addClass("active");
				if ($tabEffect == "hover") tab.find("[data-tab]").hover(function() {
					var $data_tab_id = $(this).data("tab-id");
					$(this).siblings().removeClass("active");
					$(this).addClass("active");
					$(this).closest("[data-tabs]").find(".jltma--advance-tab-content").removeClass("active");
					$("#" + $data_tab_id).addClass("active");
				});
				else tab.find("[data-tab]").click(function() {
					var $data_tab_id = $(this).data("tab-id");
					$(this).siblings().removeClass("active");
					$(this).addClass("active");
					$(this).closest("[data-tabs]").find(".jltma--advance-tab-content").removeClass("active");
					$("#" + $data_tab_id).addClass("active");
				});
			});
		} catch (e) {}
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-tabs.default", JLTMA_Tabs);
	});
})(jQuery, window.elementorFrontend);
/**
* End tabs widget script
*/
//#endregion
})();
