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
//#region dev/js/addons/free/ma-navmenu.js
/**
* Start nav menu widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_NavMenu = function($scope, $) {
		getElementSettings($scope);
		var $menuContainer = $scope.find(".jltma-nav-menu-element"), $menuID = $menuContainer.data("menu-id"), $menu_type = $menuContainer.data("menu-layout");
		$menuContainer.data("menu-trigger");
		$menuContainer.data("menu-offcanvas");
		var $menu_toggletype = $menuContainer.data("menu-toggletype"), $submenu_animation = $menuContainer.data("menu-animation"), $menu_container_id = $menuContainer.data("menu-container-id"), $sticky_type = $menuContainer.data("sticky-type"), navbar_height = $("#" + $menu_container_id).outerHeight(), menu_container_selector = $("#" + $menu_container_id);
		if ($menu_type == "onepage") {
			$(document).on("click", ".jltma-navbar-nav li a", function(e) {
				if ($(this).attr("href")) {
					var self = $(this), el = self.get(0), href = el.href, hasHash = href.indexOf("#"), enable = self.parents(".jltma-navbar-nav-default").hasClass("jltma-one-page-enabled");
					if (hasHash !== -1 && href.length > 1 && enable && el.pathname == window.location.pathname) {
						e.preventDefault();
						self.parents(".jltma-menu-container").find(".jltma-close").trigger("click");
					}
				}
			});
			$(document).on("click", function(e) {
				$(e.target);
				if ($(".navbar-collapse").hasClass("show") === true) $(".jltma-one-page-enabled").removeClass("show");
			});
		} else {
			"" + $submenu_animation;
			var submenu_selector = $(".jltma-dropdown.jltma-sub-menu");
			$("#" + $menuID + " .jltma-menu-has-children").hover(function() {
				if (submenu_selector.hasClass("fade-up")) submenu_selector.removeClass("fade-up");
				if (submenu_selector.hasClass("fade-down")) submenu_selector.removeClass("fade-down");
				$(".jltma-dropdown.jltma-sub-menu").addClass($submenu_animation);
			});
			if ($sticky_type == "fixed-onscroll") {
				if ($(window).width() > 768) $(function() {
					$(window).scroll(function() {
						if ($(window).scrollTop() >= 10) menu_container_selector.removeClass("" + $menu_container_id).addClass("jltma-on-scroll-fixed");
						else menu_container_selector.removeClass("jltma-on-scroll-fixed").addClass("" + $menu_container_id);
					});
				});
			}
			if ($sticky_type == "sticky-top") {
				if ($(window).width() > 768) $(function() {
					$(window).scroll(function() {
						if ($(window).scrollTop() >= 10) menu_container_selector.removeClass("" + $menu_container_id).addClass("sticky-top");
						else menu_container_selector.removeClass("sticky-top").addClass("" + $menu_container_id);
					});
				});
			}
			if ($sticky_type == "smart-scroll") {
				$("body").css("padding-top", navbar_height + "px");
				menu_container_selector.addClass("jltma-smart-scroll");
				if ($(".jltma-smart-scroll").length > 0) {
					var last_scroll_top = 0;
					$(window).on("scroll", function() {
						var scroll_top = $(this).scrollTop();
						if (scroll_top < last_scroll_top) $(".jltma-smart-scroll").removeClass("scrolled-down").addClass("scrolled-up");
						else $(".jltma-smart-scroll").removeClass("scrolled-up").addClass("scrolled-down");
						last_scroll_top = scroll_top;
					});
				}
			}
			if ($sticky_type == "nav-fixed-top") {
				if ($(window).width() > 768) $(function() {
					$("body").css("padding-top", navbar_height + "px");
					menu_container_selector.addClass("jltma-fixed-top");
				});
			}
			if ($menu_toggletype == "toggle") $("#" + $menuID + " .navbar-nav.toggle .jltma-menu-dropdown-toggle").click(function(e) {
				$(this).parents(".dropdown").toggleClass("open");
				e.stopPropagation();
			});
			var $widget = $scope;
			var $toggle = $scope.find(".jltma-nav-menu__toggle-container");
			var $dropdown = $scope.find(".jltma-nav-menu__dropdown");
			var $closeBtn = $scope.find(".jltma-nav-menu__dropdown-close");
			var $backdrop = $scope.find(".jltma-nav-menu__backdrop");
			var dropdownType = $dropdown.data("menu-type");
			var lockBody = dropdownType === "offcanvas" || dropdownType === "popup";
			var keyNs = "keydown.jltmaNav-" + ($widget.attr("data-id") || "");
			$toggle.off(".jltmaNav");
			$closeBtn.off(".jltmaNav");
			$backdrop.off(".jltmaNav");
			$dropdown.off(".jltmaNav");
			$(document).off(keyNs);
			if ($toggle.length === 0) return;
			function jltmaOpen() {
				$widget.addClass("jltma-is-open");
				$toggle.attr("aria-expanded", "true");
				if (lockBody) {
					$dropdown.attr("aria-hidden", "false");
					$("body").addClass("jltma-menu-locked");
				}
			}
			function jltmaClose() {
				$widget.removeClass("jltma-is-open");
				$toggle.attr("aria-expanded", "false");
				if (lockBody) {
					$dropdown.attr("aria-hidden", "true");
					$("body").removeClass("jltma-menu-locked");
				}
			}
			function jltmaIsOpen() {
				return $widget.hasClass("jltma-is-open");
			}
			$toggle.on("click.jltmaNav", function(e) {
				e.preventDefault();
				e.stopPropagation();
				if (jltmaIsOpen()) jltmaClose();
				else jltmaOpen();
			});
			$toggle.on("keydown.jltmaNav", function(e) {
				if (e.key === "Enter" || e.key === " ") {
					e.preventDefault();
					if (jltmaIsOpen()) jltmaClose();
					else jltmaOpen();
				}
			});
			$closeBtn.on("click.jltmaNav", function(e) {
				e.preventDefault();
				jltmaClose();
			});
			if (lockBody) {
				$backdrop.on("click.jltmaNav", jltmaClose);
				$(document).on(keyNs, function(e) {
					if (e.key === "Escape" && jltmaIsOpen()) jltmaClose();
				});
				$dropdown.on("click.jltmaNav", "a[href]:not([href=\"#\"]):not([href=\"\"])", function() {
					jltmaClose();
				});
			}
			if (dropdownType === "default" || dropdownType === "icon") $dropdown.on("click.jltmaNav", "a", function(e) {
				var $li = $(this).parent("li.menu-item-has-children");
				if (!$li.length) return;
				if (!$li.children("ul").first().length) return;
				if (!$li.hasClass("jltma-submenu-open")) {
					e.preventDefault();
					e.stopPropagation();
					$li.addClass("jltma-submenu-open");
				}
			});
		}
	};
	$(window).on("elementor/frontend/init", function() {
		if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) elementorFrontend.hooks.addAction("frontend/element_ready/ma-navmenu.default", JLTMA_NavMenu);
	});
	$(function() {
		$(".jltma-nav-menu__toggle-container").each(function() {
			var $scope = $(this).closest(".elementor-element, .elementor-widget, [data-widget_type]").first();
			if (!$scope.length) $scope = $(this).parent();
			JLTMA_NavMenu($scope, $);
		});
	});
})(jQuery, window.elementorFrontend);
/**
* End nav menu widget script
*/
//#endregion
})();
