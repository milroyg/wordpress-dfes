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
//#region dev/js/addons/free/ma-advanced-image.js
/**
* Start advanced image widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_AdvancedImage = function($scope, $) {
		getElementSettings($scope);
		$scope.find(".jltma-img-dynamic-dropshadow").each(function() {
			var imgFrame, clonedImg, img;
			if (this instanceof jQuery) if (this && this[0]) img = this[0];
			else return;
			else img = this;
			if (!img.classList.contains("jltma-img-has-shadow")) {
				imgFrame = document.createElement("div");
				clonedImg = img.cloneNode();
				clonedImg.classList.add("jltma-img-dynamic-dropshadow-cloned");
				clonedImg.classList.remove("jltma-img-dynamic-dropshadow");
				img.classList.add("jltma-img-has-shadow");
				imgFrame.classList.add("jltma-img-dynamic-dropshadow-frame");
				img.parentNode.appendChild(imgFrame);
				imgFrame.appendChild(img);
				imgFrame.appendChild(clonedImg);
			}
		});
		$scope.find(".jltma-tilt-box").tilt({
			maxTilt: $(this).data("max-tilt"),
			easing: "cubic-bezier(0.23, 1, 0.32, 1)",
			speed: $(this).data("time"),
			perspective: 2e3
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-advanced-image.default", JLTMA_AdvancedImage);
	});
})(jQuery, window.elementorFrontend);
/**
* End advanced image widget script
*/
//#endregion
})();
