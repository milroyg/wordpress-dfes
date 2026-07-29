(function(){
//#region dev/js/admin/popup-builder/modal-popup.js
(function($, elementor) {
	"use strict";
	var ANIMATION_LEGACY_MAP = {
		"fade": "jltma-anim-fade-in",
		"fade-slide": "jltma-anim-fade-in-down",
		"zoom": "jltma-anim-zoom-in",
		"slide-from-left": "jltma-anim-slide-from-left",
		"slide-from-right": "jltma-anim-slide-from-right",
		"slide-from-top": "jltma-anim-slide-from-top",
		"slide-from-bottom": "jltma-anim-slide-from-bot"
	};
	function resolveAnimClass(value) {
		if (!value) return "jltma-anim-fade-in";
		if (value.indexOf("jltma-anim-") === 0) return value;
		if (ANIMATION_LEGACY_MAP[value]) return ANIMATION_LEGACY_MAP[value];
		return "jltma-anim-fade-in";
	}
	var triggerHandlers = {};
	var JltmaPopups = {
		/**
		* Register a trigger handler. Pro JS calls this to add on-scroll, on-click, etc.
		* @param {string} name - Trigger key (e.g. 'on-scroll')
		* @param {function} handler - function(popup, settings, JltmaPopups)
		*/
		registerTrigger: function(name, handler) {
			triggerHandlers[name] = handler;
		},
		init: function() {
			$(document).ready(function() {
				try {
					if (!$(".jltma-template-popup").length) return;
					if (JltmaPopups.editorCheck()) return;
					JltmaPopups.openPopupInit();
					JltmaPopups.closePopupInit();
				} catch (error) {
					console.error("JltmaPopups: Error during initialization", error);
				}
			});
		},
		openPopupInit: function() {
			$(".jltma-template-popup").each(function() {
				var popup = $(this), popupID = JltmaPopups.getID(popup);
				if (!JltmaPopups.checkAvailability(popupID)) return;
				if (!JltmaPopups.checkStopShowingAfterDate(popup)) return;
				if (!JltmaPopups.checkAutoDisableExpiration(popup)) return;
				JltmaPopups.setLocalStorage(popup, "show");
				var settings = JSON.parse(localStorage.getItem("JltmaPopupSettings"))[popupID];
				if (!JltmaPopups.checkAvailableDevice(popup, settings)) return false;
				JltmaPopups.popupTriggerInit(popup);
				if ("page-load" === settings.popup_trigger) {
					var loadDelay = settings.popup_load_delay * 1e3;
					$(window).on("load", function() {
						setTimeout(function() {
							JltmaPopups.openPopup(popup, settings);
						}, loadDelay);
					});
				} else if (triggerHandlers[settings.popup_trigger]) triggerHandlers[settings.popup_trigger](popup, settings, JltmaPopups);
				if ("0px" !== popup.find(".jltma-popup-container-inner").css("height")) {
					if (typeof PerfectScrollbar !== "undefined") new PerfectScrollbar(popup.find(".jltma-popup-container-inner")[0], { suppressScrollX: true });
				}
			});
		},
		openPopup: function(popup, settings) {
			var animDuration = parseFloat(settings.popup_animation_duration) || 400;
			if (animDuration < 10) animDuration = animDuration * 1e3;
			if ("notification" === settings.popup_display_as) {
				popup.addClass("jltma-popup-notification");
				setTimeout(function() {
					var notificationHeight = popup.find(".jltma-popup-container").outerHeight();
					$("body").animate({ "padding-top": notificationHeight + "px" }, animDuration, "linear");
				}, 10);
			}
			if (settings.popup_disable_page_scroll === "yes" && "modal" === settings.popup_display_as) $("body").css("overflow", "hidden");
			popup.addClass("jltma-popup-open").show();
			var animClass = resolveAnimClass(settings.popup_animation);
			popup.find(".jltma-popup-container").addClass("jltma-animate " + animClass).css("animation-duration", animDuration + "ms");
			$(window).trigger("resize");
			if (settings.popup_show_overlay !== "no") popup.find(".jltma-popup-overlay").hide().fadeIn();
			else popup.find(".jltma-popup-overlay").hide();
			if ((settings.popup_show_close_button === void 0 || settings.popup_show_close_button === "yes") && popup.find(".jltma-popup-close-btn").length) {
				var closeDelay = (parseFloat(settings.popup_close_button_display_delay) || 0) * 1e3;
				popup.find(".jltma-popup-close-btn").css("opacity", "0");
				setTimeout(function() {
					popup.find(".jltma-popup-close-btn").animate({ "opacity": "1" }, 500);
				}, closeDelay);
			}
			if (settings.popup_automatic_close_switch === "yes" || settings.popup_automatic_close_switch === true) {
				var autoCloseDelay = (parseFloat(settings.popup_automatic_close_delay) || 10) * 1e3;
				setTimeout(function() {
					JltmaPopups.closePopup(popup);
				}, autoCloseDelay);
			}
		},
		closePopupInit: function() {
			$(document).on("click", ".jltma-popup-close-btn", function() {
				JltmaPopups.closePopup($(this).closest(".jltma-template-popup"));
			});
			$(document).on("click", ".jltma-popup-overlay", function() {
				var popup = $(this).closest(".jltma-template-popup"), popupID = JltmaPopups.getID(popup), settings = JltmaPopups.getLocalStorage(popupID);
				if (settings && settings.popup_close_on_overlay === "yes") JltmaPopups.closePopup(popup);
			});
		},
		closePopup: function(popup) {
			var popupID = JltmaPopups.getID(popup), settings = JltmaPopups.getLocalStorage(popupID);
			if (settings && "notification" === settings.popup_display_as) $("body").css("padding-top", 0);
			JltmaPopups.setLocalStorage(popup, "hide");
			if (settings && "modal" === settings.popup_display_as) popup.fadeOut();
			else popup.hide();
			popup.removeClass("jltma-popup-open");
			var container = popup.find(".jltma-popup-container");
			container.removeClass("jltma-animate");
			container.attr("class", container.attr("class").replace(/jltma-anim-\S+/g, "").trim());
			$("body").css("overflow", "");
			$(window).trigger("resize");
		},
		popupTriggerInit: function(popup) {
			var popupTrigger = popup.find(".jltma-popup-trigger-button");
			if (!popupTrigger.length) return;
			popupTrigger.on("click", function() {
				var settings = JSON.parse(localStorage.getItem("JltmaPopupSettings")) || {};
				var popupTriggerType = $(this).attr("data-trigger"), popupShowDelay = $(this).attr("data-show-delay"), popupRedirect = $(this).attr("data-redirect"), popupRedirectURL = $(this).attr("data-redirect-url"), popupID = JltmaPopups.getID(popup);
				if ("close" === popupTriggerType) {
					settings[popupID].popup_show_again_delay = parseInt(popupShowDelay, 10);
					settings[popupID].popup_close_time = Date.now();
				} else if ("close-permanently" === popupTriggerType) {
					settings[popupID].popup_show_again_delay = parseInt(popupShowDelay, 10);
					settings[popupID].popup_close_time = Date.now();
				} else if ("back" === popupTriggerType) window.history.back();
				JltmaPopups.closePopup(popup);
				localStorage.setItem("JltmaPopupSettings", JSON.stringify(settings));
				if ("back" !== popupTriggerType && "yes" === popupRedirect) setTimeout(function() {
					window.location.href = popupRedirectURL;
				}, 100);
			});
		},
		getLocalStorage: function(id) {
			var getLocalStorage = JSON.parse(localStorage.getItem("JltmaPopupSettings"));
			if (null == getLocalStorage) return false;
			var settings = getLocalStorage[id];
			if (null == settings) return false;
			return settings;
		},
		setLocalStorage: function(popup, display) {
			var popupID = JltmaPopups.getID(popup);
			var dataSettings = JSON.parse(popup.attr("data-settings")), settings = JSON.parse(localStorage.getItem("JltmaPopupSettings")) || {};
			settings[popupID] = dataSettings;
			if ("hide" === display) settings[popupID].popup_close_time = Date.now();
			else settings[popupID].popup_close_time = false;
			localStorage.setItem("JltmaPopupSettings", JSON.stringify(settings));
		},
		checkStopShowingAfterDate: function(popup) {
			var settings = JSON.parse(popup.attr("data-settings"));
			var currentDate = Date.now();
			if ("yes" === settings.popup_stop_after_date) {
				if (currentDate >= Date.parse(settings.popup_stop_after_date_select)) return false;
			}
			return true;
		},
		checkAutoDisableExpiration: function(popup) {
			var settings = JSON.parse(popup.attr("data-settings"));
			if ("yes" === settings.popup_disable_automatic && settings.popup_disable_after) {
				if (Date.now() >= Date.parse(settings.popup_disable_after)) {
					var popupID = JltmaPopups.getID(popup);
					if (typeof jltma_popup_frontend !== "undefined") $.ajax({
						url: jltma_popup_frontend.ajax_url,
						type: "POST",
						data: {
							action: "jltma_popup_disable_expired",
							popup_id: popupID,
							nonce: jltma_popup_frontend.nonce
						},
						success: function(response) {}
					});
					return false;
				}
			}
			return true;
		},
		getDelayMs: function(delay) {
			switch (delay) {
				case "1-minute": return 6e4;
				case "3-minutes": return 18e4;
				case "5-minutes": return 3e5;
				default: return 0;
			}
		},
		checkAvailability: function(id) {
			var popup = $("#jltma-popup-id-" + id), dataSettings = JSON.parse(popup.attr("data-settings")), currentURL = window.location.href;
			if ("yes" === dataSettings.popup_show_via_referral && -1 === currentURL.indexOf("jltma_templates=user-popup")) {
				if (currentURL.indexOf(dataSettings.popup_referral_keyword) == -1) return;
			}
			if (false === JltmaPopups.getLocalStorage(id)) return true;
			var settings = JltmaPopups.getLocalStorage(id);
			var closeDate = settings.popup_close_time || 0;
			if (!closeDate) return true;
			if (settings.popup_show_again_delay != dataSettings.popup_show_again_delay) return true;
			var delayMs = JltmaPopups.getDelayMs(settings.popup_show_again_delay);
			if (delayMs === Infinity) return false;
			if (delayMs === 0) return true;
			if (Date.now() >= closeDate + delayMs) return true;
			return false;
		},
		checkAvailableDevice: function(popup, settings) {
			var viewport = $("body").prop("clientWidth");
			if (viewport > 1024) return settings.popup_show_on_device !== "" && settings.popup_show_on_device !== "no";
			else if (viewport > 768) return settings.popup_show_on_device_tablet !== "" && settings.popup_show_on_device_tablet !== "no";
			else return settings.popup_show_on_device_mobile !== "" && settings.popup_show_on_device_mobile !== "no";
		},
		getID: function(popup) {
			return popup.attr("id").replace("jltma-popup-id-", "");
		},
		editorCheck: function() {
			return $("body").hasClass("elementor-editor-active") ? true : false;
		}
	};
	window.JltmaPopups = JltmaPopups;
	JltmaPopups.init();
})(jQuery, window.elementorFrontend);
//#endregion
})();
