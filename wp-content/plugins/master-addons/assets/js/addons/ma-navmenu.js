(function(){
//#region dev/js/addons/free/ma-navmenu.js
/**
* Start nav menu widget script
*/
(function($) {
	"use strict";
	/**
	* Collapse every open in-flow level inside $context.
	*/
	function jltmaCollapse($context) {
		$context.filter(".jltma-submenu-open").add($context.find(".jltma-submenu-open")).removeClass("jltma-submenu-open").children("a").attr("aria-expanded", "false");
	}
	/**
	* Vertical Toggle / Accordion types.
	*
	* Every parent item becomes the trigger for its own level: the click opens
	* the level in flow instead of following the link. Toggle leaves the levels
	* the visitor opened alone; Accordion keeps a single open branch per level
	* by collapsing the siblings of the item that was just opened.
	*
	* The mega menu panel is handled here too -- in these types it expands in
	* flow like any other level, so the .jltma-megamenu-open click trigger used
	* by the flyout types is not bound at all.
	*/
	function jltmaInitExpand($nav, ns, isAccordion) {
		var $triggers = $nav.find("li.jltma-menu-has-children").children("a");
		$triggers.off(ns);
		$triggers.each(function() {
			var $li = $(this).parent("li");
			if (!$li.children("ul.jltma-dropdown, ul.jltma-megamenu").length) return;
			$(this).attr("aria-expanded", $li.hasClass("jltma-submenu-open") ? "true" : "false");
			$(this).on("click" + ns, function(e) {
				var wasOpen = $li.hasClass("jltma-submenu-open");
				e.preventDefault();
				if (wasOpen) {
					jltmaCollapse($li);
					return;
				}
				if (isAccordion) jltmaCollapse($li.siblings("li"));
				$li.addClass("jltma-submenu-open");
				$(this).attr("aria-expanded", "true");
			});
		});
	}
	/**
	* Mega menu click trigger, for the layouts where the panel is a flyout
	* (horizontal, and vertical type Normal).
	*
	* Items saved with Trigger Effect = "Hover" (or never saved) open purely
	* from CSS -- see ma-navmenu.scss. Items saved as "Click" carry
	* .jltma-megamenu-click from Megamenu_Nav_Walker::start_el() and are
	* opened here by toggling .jltma-megamenu-open on the li.
	*/
	function jltmaInitMegaClick($scope, ns) {
		var $megaClickItems = $scope.find("li.jltma-has-megamenu.jltma-megamenu-click");
		function closeMega() {
			$scope.find("li.jltma-megamenu-open").removeClass("jltma-megamenu-open").children("a").attr("aria-expanded", "false");
		}
		if (!$megaClickItems.length) return;
		$megaClickItems.children("a").attr("aria-expanded", "false");
		$megaClickItems.children("a").on("click" + ns, function(e) {
			var $li = $(this).parent("li");
			var wasOpen = $li.hasClass("jltma-megamenu-open");
			e.preventDefault();
			e.stopPropagation();
			closeMega();
			if (!wasOpen) {
				$li.addClass("jltma-megamenu-open");
				$(this).attr("aria-expanded", "true");
				jltmaPlacePanel($li);
			}
		});
		$(document).on("click" + ns, function(e) {
			if (!$(e.target).closest("li.jltma-megamenu-open").length) closeMega();
		});
		$(document).on("keydown" + ns, function(e) {
			if (e.key === "Escape") closeMega();
		});
	}
	/**
	* Panel placement.
	*
	* A panel is positioned in CSS against the item it hangs off, which covers
	* every case the CSS can know about on its own. Two cannot be expressed
	* there, because both need the item's distance from the edge of the screen:
	*
	*   - Full Width, which spans the viewport rather than the item, so it has
	*     to be pulled back out of the item it is nested in.
	*   - A panel opening off screen because its menu sits near the right edge
	*     of a narrow window, which is corrected by .jltma-flip-x -- see the
	*     rules of that name in ma-navmenu.scss, each mirroring the placement
	*     rule it belongs to.
	*
	* Panels are hidden with visibility, not display, so a closed panel still
	* has a box to measure and both are settled before the visitor ever sees
	* the panel.
	*/
	var JLTMA_FLIP = "jltma-flip-x";
	var JLTMA_FULL_WIDTH = "jltma-megamenu-full-width";
	var JLTMA_EDGE_GAP = 2;
	/**
	* How far a box hangs outside the viewport, on either side.
	*/
	function jltmaOverflow(rect, viewportWidth) {
		return Math.max(0, rect.right - (viewportWidth - JLTMA_EDGE_GAP)) + Math.max(0, JLTMA_EDGE_GAP - rect.left);
	}
	/**
	* Mega Menu Width = Full Width: the panel is as wide as the screen and
	* starts at its left edge, whichever menu item it belongs to.
	*
	* The panel is positioned against that item, so the offset back to the
	* edge is measured here and written inline -- CSS cannot see how far along
	* the row the item sits. Width is taken from clientWidth rather than
	* 100vw, which counts the scrollbar and would push the page sideways.
	*
	* Returns whether the panel was a Full Width one, since it then spans the
	* screen by definition and has no side left to be flipped to.
	*/
	function jltmaSizeFullWidth($panel) {
		var el = $panel[0];
		var viewportWidth = document.documentElement.clientWidth;
		var computed, offsetParent, borderLeft, barOffset;
		if (!$panel.hasClass(JLTMA_FULL_WIDTH)) return false;
		computed = window.getComputedStyle(el);
		if (computed.position === "fixed") {
			barOffset = parseFloat(computed.left !== "auto" ? computed.left : computed.right) || 0;
			el.style.width = viewportWidth - barOffset + "px";
			return true;
		}
		el.style.width = viewportWidth + "px";
		offsetParent = el.offsetParent;
		if (offsetParent) {
			borderLeft = parseFloat(window.getComputedStyle(offsetParent).borderLeftWidth) || 0;
			el.style.left = -(offsetParent.getBoundingClientRect().left + borderLeft) + "px";
		}
		return true;
	}
	function jltmaPlacePanel($li) {
		var $panel = $li.children("ul.jltma-dropdown, ul.jltma-megamenu").first();
		var el = $panel[0];
		var viewportWidth, before, after, transform;
		if (!el) return;
		if (window.getComputedStyle(el).position === "static") return;
		$panel.removeClass(JLTMA_FLIP);
		if (jltmaSizeFullWidth($panel)) return;
		transform = el.style.transform;
		el.style.transform = "translateX(var(--jltma-mm-x, 0px))";
		viewportWidth = document.documentElement.clientWidth;
		before = jltmaOverflow(el.getBoundingClientRect(), viewportWidth);
		if (before > 0) {
			$panel.addClass(JLTMA_FLIP);
			after = jltmaOverflow(el.getBoundingClientRect(), viewportWidth);
			if (after >= before) $panel.removeClass(JLTMA_FLIP);
		}
		if (transform) el.style.transform = transform;
		else el.style.removeProperty("transform");
	}
	/**
	* Re-place every level, parents first: a nested panel hangs off its parent
	* panel, so it can only be measured once the parent has settled.
	*/
	function jltmaPlaceAll($nav) {
		$nav.find("li.jltma-menu-has-children, li.jltma-has-megamenu").each(function() {
			jltmaPlacePanel($(this));
		});
	}
	function jltmaInitPlacement($nav, ns) {
		var resizeTimer = null;
		if ($nav.hasClass("jltma-vertical-type-toggle") || $nav.hasClass("jltma-vertical-type-accordion")) return;
		$nav.find("li.jltma-menu-has-children, li.jltma-has-megamenu").on("mouseenter" + ns + " focusin" + ns, function() {
			jltmaPlacePanel($(this));
		});
		jltmaPlaceAll($nav);
		$(window).on("resize" + ns, function() {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function() {
				jltmaPlaceAll($nav);
			}, 100);
		});
	}
	/**
	* Hamburger layout.
	*
	* The whole menu sits behind one toggle: .jltma-menu-open on the nav is what
	* opens it, and the stylesheet reads that for both the list and the icon
	* swap (see .jltma-layout-dropdown in ma-navmenu.scss). The toggle is a div
	* with role="button", so the keys a real button would handle are handled
	* here.
	*
	* Closing collapses the levels the visitor opened underneath it, so the
	* menu does not spring open half expanded the next time.
	*/
	/**
	* The widget's frontend_available settings.
	*
	* On the page these are printed on the wrapper as data-settings, and a
	* switcher that is off is left out of them altogether -- so a missing key
	* is the off state, never a reason to assume the on one.
	*
	* In the editor that attribute is not kept up to date: the settings live in
	* the element's model, which is where a control the visitor just changed
	* shows up. Reading the model there is what makes a setting behave the same
	* in the editor as it does on the page.
	*/
	function jltmaSettings($scope) {
		var isEditMode = typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode && elementorFrontend.isEditMode();
		var modelCid = $scope.data("model-cid");
		var elements = isEditMode && elementorFrontend.config ? elementorFrontend.config.elements : null;
		var model = elements && elements.data ? elements.data[modelCid] : null;
		var settings = {};
		var type, keys;
		if (!model || !model.controls || !model.getActiveControls) return $scope.data("settings") || {};
		type = model.attributes.widgetType || model.attributes.elType;
		keys = elements.keys[type];
		if (!keys) {
			keys = elements.keys[type] = [];
			$.each(model.controls, function(name, control) {
				if (control.frontend_available) keys.push(name);
			});
		}
		$.each(model.getActiveControls(), function(name) {
			if (keys.indexOf(name) !== -1) settings[name] = model.attributes[name];
		});
		return settings;
	}
	/**
	* Put a Popup or an Offcanvas canvas back on the screen.
	*
	* Both are placed with position: fixed, which stops meaning "the screen"
	* the moment an ancestor carries a transform, filter or perspective -- a
	* container with motion effects, a themed sticky header, an editor preview.
	* The browser then measures them against that ancestor instead, and a popup
	* meant for the middle of the screen lands wherever the top of that box is.
	*
	* The overlay is the probe: it asks to cover the screen, so wherever it
	* actually landed is where this coordinate system starts. When that is the
	* screen -- the ordinary case -- nothing is written here at all.
	*/
	/**
	* Which screen edge a popup is pinned to, from the Vertical Alignment
	* control's class on the widget wrapper. The stylesheet reads the same
	* class; this is only for the pass that has to redo its work in numbers.
	*/
	function jltmaPopupAlignment($nav) {
		if ($nav.closest(".jltma-popup-offcanvas-ver-alignment-center").length) return "center";
		if ($nav.closest(".jltma-popup-offcanvas-ver-alignment-flex-end").length) return "flex-end";
		return "flex-start";
	}
	function jltmaAnchorToViewport($nav) {
		var overlay = $nav.children(".jltma-nav-menu__overlay")[0];
		var list = $nav.children("ul.jltma-nav-menu__container-inner")[0];
		var close = $nav.children(".jltma-nav-menu__dropdown-close-container")[0];
		var isPopup = $nav.hasClass("jltma-menu-dropdown-type-popup");
		var isOffcanvas = $nav.hasClass("jltma-menu-dropdown-type-offcanvas");
		var opensLeft = $nav.closest(".jltma-offcanvas-position-left").length > 0;
		var viewportWidth, viewportHeight, box, alignment;
		function reset(el) {
			if (!el) return;
			[
				"top",
				"right",
				"bottom",
				"left",
				"width",
				"height"
			].forEach(function(property) {
				el.style.removeProperty(property);
			});
		}
		reset(overlay);
		reset(close);
		if (list) [
			"top",
			"right",
			"bottom",
			"left",
			"height"
		].forEach(function(property) {
			list.style.removeProperty(property);
		});
		if (!overlay || !list || !isPopup && !isOffcanvas) return;
		viewportWidth = document.documentElement.clientWidth;
		viewportHeight = document.documentElement.clientHeight;
		box = overlay.getBoundingClientRect();
		if (Math.abs(box.left) < 1 && Math.abs(box.top) < 1 && Math.abs(box.width - viewportWidth) < 1 && Math.abs(box.height - viewportHeight) < 1) return;
		overlay.style.left = -box.left + "px";
		overlay.style.top = -box.top + "px";
		overlay.style.width = viewportWidth + "px";
		overlay.style.height = viewportHeight + "px";
		if (isPopup) {
			alignment = jltmaPopupAlignment($nav);
			list.style.left = viewportWidth / 2 - box.left + "px";
			if ("center" === alignment) list.style.top = viewportHeight / 2 - box.top + "px";
			else if ("flex-end" === alignment) {
				list.style.top = "auto";
				list.style.bottom = box.top + box.height - viewportHeight + JLTMA_POPUP_TOP + "px";
			} else list.style.top = JLTMA_POPUP_TOP - box.top + "px";
		} else {
			list.style.top = -box.top + "px";
			list.style.bottom = "auto";
			list.style.height = viewportHeight + "px";
			list.style.right = "auto";
			list.style.left = (opensLeft ? -box.left : viewportWidth - list.offsetWidth - box.left) + "px";
		}
		if (close) {
			close.style.right = "auto";
			if (isPopup) {
				close.style.top = JLTMA_POPUP_CLOSE_GAP - box.top + "px";
				close.style.left = JLTMA_POPUP_CLOSE_GAP - box.left + "px";
				close.style.width = viewportWidth - JLTMA_POPUP_CLOSE_GAP * 2 + "px";
			} else {
				close.style.top = JLTMA_OFFCANVAS_CLOSE_GAP - box.top + "px";
				close.style.left = (opensLeft ? -box.left : viewportWidth - close.offsetWidth - box.left) + "px";
			}
		}
	}
	/**
	* The Default panel, across the screen rather than across the column the
	* widget was dropped into.
	*
	* The panel hangs off the nav (absolute, so opening it cannot grow the
	* layout), which makes the nav's own offset the thing standing between it
	* and the edge of the screen -- so that offset is measured rather than
	* assumed. `width: 100vw` in the stylesheet would be centred on the nav
	* instead of on the screen, and would count the scrollbar as screen; this
	* counts neither.
	*
	* @param {jQuery} $nav
	*/
	function jltmaStretchDefaultPanel($nav) {
		var list = $nav.children("ul.jltma-nav-menu__container-inner")[0];
		var box;
		if (!list || !$nav.hasClass("jltma-menu-dropdown-type-default")) return;
		[
			"left",
			"right",
			"width"
		].forEach(function(property) {
			list.style.removeProperty(property);
		});
		if (!$nav.hasClass("jltma-menu-open")) return;
		box = $nav[0].getBoundingClientRect();
		list.style.left = -box.left + "px";
		list.style.right = "auto";
		list.style.width = document.documentElement.clientWidth + "px";
	}
	function jltmaInitHamburger($nav, $scope, ns) {
		var $toggle = $nav.children(".jltma-nav-menu__toggle-container");
		var $close = $nav.find(".jltma-nav-menu__dropdown-close");
		var $overlay = $nav.children(".jltma-nav-menu__overlay");
		var coversPage = $nav.hasClass("jltma-menu-dropdown-type-popup") || $nav.hasClass("jltma-menu-dropdown-type-offcanvas");
		var settings = jltmaSettings($scope);
		var anchorTimer = null;
		function setOpen(open) {
			$nav.toggleClass("jltma-menu-open", open);
			$toggle.attr("aria-expanded", open ? "true" : "false");
			if (coversPage) {
				if (open) jltmaAnchorToViewport($nav);
			} else jltmaStretchDefaultPanel($nav);
			if (coversPage && "yes" === settings.disable_scroll) $("body").toggleClass("jltma-menu-scroll-lock", open);
			if (!open) jltmaCollapse($nav.find("li.jltma-submenu-open"));
		}
		if (!$toggle.length) return;
		setOpen(false);
		$(window).on("resize" + ns, function() {
			clearTimeout(anchorTimer);
			anchorTimer = setTimeout(function() {
				if ($nav.hasClass("jltma-menu-open")) if (coversPage) jltmaAnchorToViewport($nav);
				else jltmaStretchDefaultPanel($nav);
			}, 100);
		});
		$toggle.on("click" + ns, function(e) {
			e.preventDefault();
			e.stopPropagation();
			setOpen(!$nav.hasClass("jltma-menu-open"));
		});
		$close.on("click" + ns, function(e) {
			e.preventDefault();
			e.stopPropagation();
			setOpen(false);
			$toggle.trigger("focus");
		});
		$toggle.add($close).on("keydown" + ns, function(e) {
			if (e.key === "Enter" || e.key === " " || e.key === "Spacebar") {
				e.preventDefault();
				$(this).trigger("click" + ns);
			}
		});
		if (coversPage) $overlay.on("click" + ns, function() {
			if ("yes" === settings.overlay_close) setOpen(false);
		});
		else $(document).on("click" + ns, function(e) {
			if (!$(e.target).closest($nav).length) setOpen(false);
		});
		$(document).on("keydown" + ns, function(e) {
			var escCloses = !coversPage || "yes" === settings.esc_close;
			if (e.key === "Escape" && escCloses && $nav.hasClass("jltma-menu-open")) {
				setOpen(false);
				$toggle.trigger("focus");
			}
		});
	}
	var JLTMA_POPUP_TOP = 0;
	var JLTMA_POPUP_CLOSE_GAP = 20;
	var JLTMA_OFFCANVAS_CLOSE_GAP = 15;
	var JLTMA_LAYOUTS = [
		"horizontal",
		"vertical",
		"dropdown"
	];
	var JLTMA_VERTICAL_TYPES = [
		"normal",
		"toggle",
		"accordion",
		"side"
	];
	var JLTMA_DROPDOWN_TYPES = [
		"default",
		"popup",
		"offcanvas"
	];
	/**
	* Which of Elementor's device modes the page is being viewed at.
	*
	* Elementor knows this itself, breakpoints the site has customised
	* included, and in the editor it also tracks the device being previewed --
	* which a window width cannot see. The width is the fallback for a menu
	* rendered outside all that, with Elementor's own default breakpoints.
	*/
	function jltmaDeviceMode() {
		var width;
		if (typeof elementorFrontend !== "undefined" && elementorFrontend.getCurrentDeviceMode) return elementorFrontend.getCurrentDeviceMode();
		width = document.documentElement.clientWidth;
		if (width <= 767) return "mobile";
		return width <= 1024 ? "tablet" : "desktop";
	}
	/**
	* Put the layout for the current width on the nav.
	*
	* The Layout control is responsive, so one menu can be horizontal on a
	* desktop and a hamburger on a phone. Only one of the three layout classes
	* may be on the nav at a time -- each is a whole open/close contract of its
	* own, and two at once would fight over every panel -- so the class is
	* swapped rather than added to. The saved values come from the data
	* attributes Nav_Menu::render() writes, already cascaded.
	*
	* Returns whether the layout changed, which is what tells the caller the
	* menu has to be wired up again for it.
	*/
	/**
	* The value of one responsive data attribute for the device in view, with
	* the same cascade Nav_Menu::get_device_values() already applied to it.
	*/
	function jltmaDeviceValue($nav, name, device, allowed, fallback) {
		var value = $nav.attr("data-" + name + "-" + device) || $nav.attr("data-" + name) || fallback;
		return allowed.indexOf(value) === -1 ? fallback : value;
	}
	/**
	* Swap one of the nav's mutually exclusive classes for the value in force.
	* Returns whether it changed.
	*/
	function jltmaSwapClass($nav, prefix, values, value) {
		if ($nav.attr("data-jltma-active-" + prefix) === value) return false;
		$nav.removeClass("jltma-" + prefix + "-" + values.join(" jltma-" + prefix + "-")).addClass("jltma-" + prefix + "-" + value).attr("data-jltma-active-" + prefix, value);
		return true;
	}
	/**
	* Swap the layout classes without playing the transitions between them.
	*
	* Each layout is its own open/close contract, so the swap moves every panel
	* from one resting state to another -- a horizontal row's visible list
	* becomes a closed popup. Transitioned, that reads as a panel fading out
	* over the page. The class turns transitions off for the frame the swap
	* takes; see .jltma-nav-layout-switching in ma-navmenu.scss.
	*/
	function jltmaWithoutTransitions($nav, swap) {
		var el = $nav[0];
		var changed;
		el.classList.add("jltma-nav-layout-switching");
		changed = swap();
		el.offsetWidth;
		(window.requestAnimationFrame || function(fn) {
			return setTimeout(fn, 16);
		})(function() {
			el.classList.remove("jltma-nav-layout-switching");
		});
		return changed;
	}
	function jltmaApplyLayout($nav) {
		var mode = jltmaDeviceMode();
		var device = "desktop";
		var changed;
		if (mode.indexOf("mobile") !== -1) device = "mobile";
		else if (mode.indexOf("tablet") !== -1) device = "tablet";
		changed = jltmaSwapClass($nav, "layout", JLTMA_LAYOUTS, jltmaDeviceValue($nav, "layout", device, JLTMA_LAYOUTS, "horizontal"));
		changed = jltmaSwapClass($nav, "vertical-type", JLTMA_VERTICAL_TYPES, jltmaDeviceValue($nav, "vertical-type", device, JLTMA_VERTICAL_TYPES, "normal")) || changed;
		changed = jltmaSwapClass($nav, "menu-dropdown-type", JLTMA_DROPDOWN_TYPES, jltmaDeviceValue($nav, "dropdown-type", device, JLTMA_DROPDOWN_TYPES, "default")) || changed;
		return changed;
	}
	var jltmaLayoutTimers = {};
	var JLTMA_NavMenu = function($scope) {
		var widgetId = $scope.attr("data-id") || "";
		var ns = ".jltmaNav-" + widgetId;
		var layoutNs = ".jltmaNavLayout-" + widgetId;
		var $nav = $scope.find(".jltma-nav-menu__container").first();
		$scope.find("li.jltma-menu-has-children, li.jltma-has-megamenu").off(ns).children("a").off(ns);
		$(document).off(ns);
		$(window).off(ns);
		if (!$nav.length) return;
		jltmaWithoutTransitions($nav, function() {
			return jltmaApplyLayout($nav);
		});
		$(window).off("resize" + layoutNs).on("resize" + layoutNs, function() {
			if (!jltmaWithoutTransitions($nav, function() {
				return jltmaApplyLayout($nav);
			})) return;
			clearTimeout(jltmaLayoutTimers[widgetId]);
			jltmaLayoutTimers[widgetId] = setTimeout(function() {
				JLTMA_NavMenu($scope);
			}, 150);
		});
		$nav.find(".jltma-submenu-open").removeClass("jltma-submenu-open");
		$nav.find(".jltma-megamenu-open").removeClass("jltma-megamenu-open");
		$nav.find("." + JLTMA_FLIP).removeClass(JLTMA_FLIP);
		$nav.removeClass("jltma-menu-open");
		$nav.children(".jltma-nav-menu__toggle-container, .jltma-nav-menu__overlay").off(ns);
		$nav.find(".jltma-nav-menu__dropdown-close").off(ns);
		$("body").removeClass("jltma-menu-scroll-lock");
		if ($nav.hasClass("jltma-layout-dropdown")) {
			jltmaInitHamburger($nav, $scope, ns);
			jltmaInitExpand($nav, ns, false);
			return;
		}
		if ($nav.hasClass("jltma-vertical-type-toggle")) {
			jltmaInitExpand($nav, ns, false);
			return;
		}
		if ($nav.hasClass("jltma-vertical-type-accordion")) {
			jltmaInitExpand($nav, ns, true);
			return;
		}
		jltmaInitMegaClick($scope, ns);
		jltmaInitPlacement($nav, ns);
	};
	$(window).on("elementor/frontend/init", function() {
		if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) elementorFrontend.hooks.addAction("frontend/element_ready/ma-navmenu.default", JLTMA_NavMenu);
	});
	$(function() {
		$(".jltma-nav-menu__container").each(function() {
			var $scope = $(this).closest(".elementor-element, .elementor-widget, [data-widget_type]").first();
			if (!$scope.length) $scope = $(this).parent();
			JLTMA_NavMenu($scope);
		});
	});
})(jQuery);
/**
* End nav menu widget script
*/
//#endregion
})();
