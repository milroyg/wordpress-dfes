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
//#region dev/js/addons/free/ma-tooltip.js
/**
* Start tooltip widget script
*/
(function($, elementor) {
	"use strict";
	var activeTooltipWidgets = {};
	var initTooltip = function($scope, editorSettings) {
		if (!$scope || !$scope.length) return;
		if (typeof tippy === "undefined") {
			var retryCount = ($scope.data("ma-tooltip-retry") || 0) + 1;
			if (retryCount <= 10) {
				$scope.data("ma-tooltip-retry", retryCount);
				setTimeout(function() {
					initTooltip($scope, editorSettings);
				}, 100);
			}
			return;
		}
		$scope.removeData("ma-tooltip-retry");
		var elementSettings = editorSettings || getElementSettings($scope), scopeId = $scope.data("id"), currentTooltipElement = null;
		if (!scopeId || typeof scopeId !== "string") return;
		try {
			currentTooltipElement = document.getElementById("jltma-tooltip-" + scopeId);
			if (!currentTooltipElement) {
				var $fallbackElement = $scope.find("#jltma-tooltip-" + scopeId);
				if ($fallbackElement && $fallbackElement.length > 0) currentTooltipElement = $fallbackElement[0];
			}
			if (!currentTooltipElement || currentTooltipElement.nodeType !== 1) return;
		} catch (error) {
			return;
		}
		var tooltipText = elementSettings.ma_el_tooltip_text;
		if (!tooltipText || typeof tooltipText !== "string") return;
		var $jltma_el_tooltip_text = stripTags(tooltipText), $jltma_el_tooltip_direction = elementSettings.ma_el_tooltip_direction || "top", $jltma_tooltip_animation = elementSettings.jltma_tooltip_animation || "shift-away", $jltma_tooltip_arrow = elementSettings.jltma_tooltip_arrow !== false, $jltma_tooltip_duration = parseInt(elementSettings.jltma_tooltip_duration) || 300, $jltma_tooltip_delay = parseInt(elementSettings.jltma_tooltip_delay) || 300, $jltma_tooltip_trigger = elementSettings.jltma_tooltip_trigger || "mouseenter", $animateFill = elementSettings.jltma_tooltip_animation === "fill";
		var $jltma_el_tooltip_text_width = 200;
		if (elementSettings.ma_el_tooltip_text_width && elementSettings.ma_el_tooltip_text_width.size) $jltma_el_tooltip_text_width = parseInt(elementSettings.ma_el_tooltip_text_width.size) || 200;
		if (currentTooltipElement._tippy) currentTooltipElement._tippy.destroy();
		var appendToTarget = typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode() ? document.body : currentTooltipElement.parentNode || document.body;
		var tooltipConfig = {
			content: $jltma_el_tooltip_text,
			animation: $jltma_tooltip_animation,
			arrow: $jltma_tooltip_arrow,
			duration: [$jltma_tooltip_duration, $jltma_tooltip_delay],
			trigger: $jltma_tooltip_trigger,
			animateFill: $animateFill,
			flipOnUpdate: true,
			maxWidth: Math.max(50, Math.min(1e3, $jltma_el_tooltip_text_width)),
			zIndex: 999,
			allowHTML: false,
			theme: "jltma-tooltip-tippy-" + scopeId,
			interactive: true,
			hideOnClick: true,
			placement: $jltma_el_tooltip_direction,
			appendTo: appendToTarget
		};
		if (elementSettings.jltma_tooltip_follow_cursor === "yes") tooltipConfig.followCursor = true;
		tippy(currentTooltipElement, tooltipConfig);
	};
	var JLTMA_Tooltip = function($scope, $) {
		var scopeId = $scope.data("id");
		if (scopeId) activeTooltipWidgets[scopeId] = $scope;
		initTooltip($scope);
	};
	var editorHandlerRetries = 0;
	var setupEditorHandler = function() {
		if (typeof elementor === "undefined" || !elementor.channels || !elementor.channels.editor) {
			if (editorHandlerRetries < 10) {
				editorHandlerRetries++;
				setTimeout(setupEditorHandler, 500);
			}
			return;
		}
		elementor.channels.editor.on("change", function(view) {
			var model = view.model;
			if (model.get("widgetType") !== "ma-tooltip") return;
			var $scope = activeTooltipWidgets[model.get("id")];
			if ($scope && $scope.length) {
				var settings = model.get("settings");
				var liveSettings = settings ? settings.toJSON() : {};
				clearTimeout($scope.data("tooltip-update-timer"));
				$scope.data("tooltip-update-timer", setTimeout(function() {
					initTooltip($scope, liveSettings);
				}, 100));
			}
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-tooltip.default", JLTMA_Tooltip);
		if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode()) setupEditorHandler();
	});
})(jQuery, window.elementorFrontend);
/**
* End tooltip widget script
*/
//#endregion
})();
