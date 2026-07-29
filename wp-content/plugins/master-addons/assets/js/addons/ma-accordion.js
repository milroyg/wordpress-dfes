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
//#region dev/js/addons/free/ma-accordion.js
/**
* Start accordion widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_Accordion = function($scope, $) {
		var elementSettings = getElementSettings($scope), $accordionHeader = $scope.find(".jltma-accordion-header"), $accordionType = elementSettings.accordion_type, $accordionSpeed = elementSettings.toggle_speed || 300;
		$accordionHeader.each(function() {
			if ($(this).hasClass("active-default")) {
				$(this).addClass("show active");
				$(this).next().slideDown($accordionSpeed);
			}
		});
		$accordionHeader.unbind("click");
		$accordionHeader.click(function(e) {
			e.preventDefault();
			var $this = $(this);
			if ($accordionType === "accordion") if ($this.hasClass("show")) {
				$this.removeClass("show active");
				$this.next().slideUp($accordionSpeed);
			} else {
				$this.parent().parent().find(".jltma-accordion-header").removeClass("show active");
				$this.parent().parent().find(".jltma-accordion-tab-content").slideUp($accordionSpeed);
				$this.toggleClass("show active");
				$this.next().slideDown($accordionSpeed);
			}
			else if ($this.hasClass("show")) {
				$this.removeClass("show active");
				$this.next().slideUp($accordionSpeed);
			} else {
				$this.addClass("show active");
				$this.next().slideDown($accordionSpeed);
			}
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-advanced-accordion.default", JLTMA_Accordion);
	});
})(jQuery, window.elementorFrontend);
/**
* End accordion widget script
*/
//#endregion
})();
