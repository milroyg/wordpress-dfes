(function(){
//#region dev/js/common/utils.js
/**
* Master Addons - Shared Utilities
*
* Common helper functions used across all widget handlers.
*/
/**
* Get element settings from Elementor
*
* @param {jQuery} $element - The jQuery element
* @param {string} setting - Optional specific setting key
* @returns {*} Settings object or specific setting value
*/
function getElementSettings($element, setting) {
	var elementSettings = {}, modelCID = $element.data("model-cid");
	if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode() && modelCID) {
		var settings = elementorFrontend.config.elements.data[modelCID], type = settings.attributes.widgetType || settings.attributes.elType, settingsKeys = elementorFrontend.config.elements.keys[type];
		if (!settingsKeys) {
			settingsKeys = elementorFrontend.config.elements.keys[type] = [];
			jQuery.each(settings.controls, function(name, control) {
				if (control.frontend_available) settingsKeys.push(name);
			});
		}
		jQuery.each(settings.getActiveControls(), function(controlKey) {
			if (-1 !== settingsKeys.indexOf(controlKey)) elementSettings[controlKey] = settings.attributes[controlKey];
		});
	} else elementSettings = $element.data("settings") || {};
	return getItems(elementSettings, setting);
}
/**
* Get nested items from an object
*
* @param {Object} items - The items object
* @param {string} itemKey - Dot-notation key path
* @returns {*} The value at the key path
*/
function getItems(items, itemKey) {
	if (itemKey) {
		var keyStack = itemKey.split("."), currentKey = keyStack.splice(0, 1);
		if (!keyStack.length) return items[currentKey];
		if (!items[currentKey]) return;
		return getItems(items[currentKey], keyStack.join("."));
	}
	return items;
}
/**
* Get unique loop scope ID
*
* @param {jQuery} $scope - The scope element
* @returns {string} The unique ID
*/
function getUniqueLoopScopeId($scope) {
	if ($scope.data("jltma-template-widget-id")) return $scope.data("jltma-template-widget-id");
	return $scope.data("id");
}
/**
* Observe target element with IntersectionObserver
*
* @param {Element} target - The target element to observe
* @param {Function} callback - Callback when element intersects
* @param {Object} options - IntersectionObserver options
*/
function jltMAObserveTarget(target, callback, options = {}) {
	new IntersectionObserver(function(entries, observer) {
		entries.forEach(function(entry) {
			if (entry.isIntersecting) callback(entry);
		});
	}, options).observe(target);
}
/**
* Strip HTML tags from text
*
* @param {string} text - Text to strip
* @returns {string} Text without HTML tags
*/
function stripTags(text) {
	return text.replace(/<\/?[^>]+(>|$)/g, "");
}
/**
* Check if in Elementor edit mode
*
* @returns {boolean} True if in edit mode
*/
function isEditMode() {
	return typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode();
}
//#endregion
//#region dev/js/addons/free/ma-image-filter-gallery.js
/**
* Start image filter gallery widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_ImageFilterGallery = function($scope, $) {
		getElementSettings($scope);
		getUniqueLoopScopeId($scope);
		var $galleryWrapper = $scope.find(".jltma-image-filter-gallery");
		if (!$galleryWrapper.length) return;
		if (!$galleryWrapper.hasClass("jltma-editor-mode")) {
			var $filterButtons = $scope.find(".jltma-image-filter-nav li");
			$galleryWrapper.find(".jltma-image-filter-item");
			var $isotope = $galleryWrapper.isotope({
				itemSelector: ".jltma-image-filter-item",
				layoutMode: "fitRows",
				percentPosition: true
			});
			$filterButtons.on("click", function() {
				var $this = $(this), filterValue = $this.attr("data-filter");
				$filterButtons.removeClass("active");
				$this.addClass("active");
				$isotope.isotope({ filter: filterValue });
			});
			$galleryWrapper.imagesLoaded(function() {
				$isotope.isotope("layout");
			});
		}
		if ($.isFunction($.fn.fancybox)) $scope.find("[data-fancybox]").fancybox({});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-image-filter-gallery.default", JLTMA_ImageFilterGallery);
	});
})(jQuery, window.elementorFrontend);
/**
* End image filter gallery widget script
*/
//#endregion
})();
