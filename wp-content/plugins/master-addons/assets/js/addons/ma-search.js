(function(){
//#region dev/js/addons/free/ma-search.js
/**
* MA Search Widget Script
* Handles both form and icon popup search types
*/
(function($, elementor) {
	"use strict";
	/**
	* Initialize search events
	* @param {jQuery} $scope - The widget scope element
	*/
	function initEvents($scope) {
		var searchType = $scope.find(".jltma-search-wrapper").eq(0).data("search-type"), mainContainer = $scope.find(".jltma-search-main-wrap"), openCtrl = $scope.find(".jltma-btn--search"), closeCtrl = $scope.find(".jltma-btn--search-close"), searchContainer = $scope.find(".jltma-search"), inputSearch = searchContainer.find(".jltma-search__input");
		if (searchType !== "icon") return;
		openCtrl.on("click", function(e) {
			e.preventDefault();
			mainContainer.addClass("main-wrap--move");
			searchContainer.addClass("search--open");
			setTimeout(function() {
				inputSearch.focus();
			}, 600);
		});
		closeCtrl.on("click", function(e) {
			e.preventDefault();
			closeSearch($scope);
		});
		$(document).on("keyup.jltmaSearch", function(ev) {
			if (ev.keyCode === 27 && searchContainer.hasClass("search--open")) closeSearch($scope);
		});
	}
	/**
	* Close the search panel
	* @param {jQuery} $scope - The widget scope element
	*/
	function closeSearch($scope) {
		var mainContainer = $scope.find(".jltma-search-main-wrap"), searchContainer = $scope.find(".jltma-search"), inputSearch = searchContainer.find(".jltma-search__input");
		mainContainer.removeClass("main-wrap--move");
		searchContainer.removeClass("search--open");
		inputSearch.blur();
		inputSearch.val("");
	}
	/**
	* Main handler for MA Search widget
	*/
	var JLTMA_HeaderSearch = function($scope, $) {
		$("body").addClass("js");
		initEvents($scope);
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-search.default", JLTMA_HeaderSearch);
	});
})(jQuery, window.elementorFrontend);
//#endregion
})();
