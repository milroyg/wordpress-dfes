(function(){
//#region dev/js/admin/popup-builder/popup-frontend.js
(function($) {
	"use strict";
	var MAPopupFrontend = {
		popups: [],
		activePopups: [],
		settings: {},
		init: function() {
			if (typeof jltma_popup_frontend === "undefined") return;
			this.popups = jltma_popup_frontend.popups || [];
			this.settings = jltma_popup_frontend;
			this.bindEvents();
			this.initPopups();
		},
		bindEvents: function() {
			var self = this;
			$(document).on("click", ".jltma-popup-close-btn", function(e) {
				e.preventDefault();
				var $popup = $(this).closest(".jltma-template-popup");
				self.closePopup($popup);
			});
			$(document).on("click", ".jltma-popup-overlay", function(e) {
				var $popup = $(this).closest(".jltma-template-popup");
				var popupData = self.getPopupData($popup.data("popup-id"));
				if (popupData && popupData.close_on_overlay) self.closePopup($popup);
			});
			$(document).on("keydown", function(e) {
				if (e.keyCode === 27) $(".ma-popup.ma-popup-active").each(function() {
					var $popup = $(this);
					var popupData = self.getPopupData($popup.data("popup-id"));
					if (popupData && popupData.close_on_esc) self.closePopup($popup);
				});
			});
			$(window).on("scroll", function() {
				self.checkScrollTriggers();
			});
			$(document).on("mouseleave", function(e) {
				if (e.clientY <= 0) self.triggerExitIntent();
			});
			this.initInactivityTimer();
			$(document).on("submit", ".ma-popup form", function() {
				var $popup = $(this).closest(".ma-popup");
				self.trackConversion($popup.data("popup-id"));
			});
			$(document).on("click", ".ma-popup a, .ma-popup button", function(e) {
				var $popup = $(this).closest(".ma-popup");
				var href = $(this).attr("href");
				if (href && (href.indexOf("http") === 0 || href.indexOf("mailto:") === 0 || href.indexOf("tel:") === 0)) self.trackConversion($popup.data("popup-id"));
				else if ($(this).is("button") && !$(this).hasClass("ma-popup-close")) self.trackConversion($popup.data("popup-id"));
			});
		},
		initPopups: function() {
			var self = this;
			this.popups.forEach(function(popup) {
				self.initSinglePopup(popup);
			});
		},
		initSinglePopup: function(popup) {
			var self = this;
			switch (popup.trigger) {
				case "load":
					setTimeout(function() {
						self.showPopup(popup.id);
					}, popup.trigger_delay);
					break;
				case "scroll": break;
				case "element-scroll": break;
				case "exit": break;
				case "inactivity":
					setTimeout(function() {
						if (!self.hasUserInteracted()) self.showPopup(popup.id);
					}, popup.trigger_delay);
					break;
				case "click": break;
			}
		},
		checkScrollTriggers: function() {
			var self = this;
			var scrollPercent = this.getScrollPercent();
			this.popups.forEach(function(popup) {
				if (popup.trigger === "scroll" && !self.isPopupShown(popup.id)) {
					if (scrollPercent >= popup.trigger_scroll_percent) self.showPopup(popup.id);
				} else if (popup.trigger === "element-scroll" && !self.isPopupShown(popup.id)) {
					var $element = $(popup.trigger_element);
					if ($element.length && self.isElementInView($element)) self.showPopup(popup.id);
				}
			});
		},
		triggerExitIntent: function() {
			var self = this;
			this.popups.forEach(function(popup) {
				if (popup.trigger === "exit" && !self.isPopupShown(popup.id)) self.showPopup(popup.id);
			});
		},
		showPopup: function(popupId) {
			var self = this;
			var $popup = $("#ma-popup-" + popupId);
			var popupData = this.getPopupData(popupId);
			if (!$popup.length || this.isPopupShown(popupId) || !this.canShowPopup(popupData)) return;
			this.trackView(popupId);
			this.activePopups.push(popupId);
			if (popupData.prevent_scroll) $("body").addClass("ma-popup-no-scroll");
			$popup.addClass("ma-popup-active").show();
			if (popupData.auto_close) setTimeout(function() {
				self.closePopup($popup);
			}, popupData.auto_close_delay);
			this.setPopupShown(popupId);
			$(document).trigger("ma_popup_shown", [popupId, $popup]);
		},
		closePopup: function($popup) {
			var popupId = $popup.data("popup-id");
			$popup.addClass("ma-popup-closing");
			setTimeout(function() {
				$popup.removeClass("ma-popup-active ma-popup-closing").hide();
				var index = this.activePopups.indexOf(popupId);
				if (index > -1) this.activePopups.splice(index, 1);
				if (this.activePopups.length === 0) $("body").removeClass("ma-popup-no-scroll");
				$(document).trigger("ma_popup_closed", [popupId, $popup]);
			}.bind(this), 300);
		},
		canShowPopup: function(popupData) {
			if (!popupData) return false;
			switch (popupData.show_frequency) {
				case "once_session":
					if (sessionStorage.getItem("jltma_popup_shown_" + popupData.id)) return false;
					break;
				case "once_day":
				case "once_week":
				case "once":
					var cookieName = "jltma_popup_shown_" + popupData.id;
					if (this.getCookie(cookieName)) return false;
					break;
			}
			return true;
		},
		setPopupShown: function(popupId) {
			var popupData = this.getPopupData(popupId);
			if (!popupData) return;
			switch (popupData.show_frequency) {
				case "once_session":
					sessionStorage.setItem("jltma_popup_shown_" + popupId, "1");
					break;
				case "once_day":
				case "once_week":
				case "once": break;
			}
		},
		isPopupShown: function(popupId) {
			return this.activePopups.indexOf(popupId) !== -1;
		},
		getPopupData: function(popupId) {
			return this.popups.find(function(popup) {
				return popup.id == popupId;
			});
		},
		getScrollPercent: function() {
			var scrollPercent = $(window).scrollTop() / ($(document).height() - $(window).height());
			return Math.round(scrollPercent * 100);
		},
		isElementInView: function($element) {
			var elementTop = $element.offset().top;
			var elementBottom = elementTop + $element.outerHeight();
			var viewportTop = $(window).scrollTop();
			var viewportBottom = viewportTop + $(window).height();
			return elementBottom > viewportTop && elementTop < viewportBottom;
		},
		initInactivityTimer: function() {
			var lastActivity = Date.now();
			$(document).on("mousemove keypress scroll touchstart", function() {
				lastActivity = Date.now();
			});
			this.inactivityInterval = setInterval(function() {
				var inactiveTime = Date.now() - lastActivity;
				this.popups.forEach(function(popup) {
					if (popup.trigger === "inactivity" && !this.isPopupShown(popup.id)) {
						if (inactiveTime >= popup.trigger_delay) this.showPopup(popup.id);
					}
				}.bind(this));
			}.bind(this), 1e3);
		},
		hasUserInteracted: function() {
			return $(document).data("ma-user-interacted") === true;
		},
		trackView: function(popupId) {
			$.ajax({
				url: this.settings.ajax_url,
				type: "POST",
				data: {
					action: "jltma_popup_track_view",
					popup_id: popupId,
					nonce: this.settings.nonce
				}
			});
		},
		trackConversion: function(popupId) {
			$.ajax({
				url: this.settings.ajax_url,
				type: "POST",
				data: {
					action: "jltma_popup_track_conversion",
					popup_id: popupId,
					nonce: this.settings.nonce
				}
			});
		},
		getCookie: function(name) {
			var parts = ("; " + document.cookie).split("; " + name + "=");
			if (parts.length == 2) return parts.pop().split(";").shift();
			return null;
		},
		openPopup: function(popupId) {
			this.showPopup(popupId);
		},
		closePopupById: function(popupId) {
			var $popup = $("#ma-popup-" + popupId);
			if ($popup.length) this.closePopup($popup);
		}
	};
	$(document).ready(function() {
		MAPopupFrontend.init();
		$(document).one("mousemove keypress scroll touchstart", function() {
			$(document).data("ma-user-interacted", true);
		});
	});
	window.MAPopupFrontend = MAPopupFrontend;
})(jQuery);
//#endregion
})();
