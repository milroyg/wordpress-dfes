(function(){
(function($) {
	"use strict";
	var JLTMA_ModalPopups = {
		init: function() {
			if (!$("body").hasClass("elementor-editor-jltma_popup")) return;
			window.elementor.on("preview:loaded", JLTMA_ModalPopups.onPreviewLoad);
			window.elementor.on("preview:loaded", JLTMA_ModalPopups.onPreviewChange);
			elementor.settings.page.model.on("change", JLTMA_ModalPopups.onControlChange);
		},
		onPreviewLoad: function() {
			setTimeout(function() {
				if ($("#elementor-panel-footer-settings").length) $("#elementor-panel-footer-settings").trigger("click");
				else setTimeout(function() {
					$("#elementor-panel-footer-settings").trigger("click");
				}, 5e3);
			}, 2e3);
			JLTMA_ModalPopups.settingsNotification();
			window.elementorFrontend.hooks.addAction("frontend/element_ready/global", function($scope) {
				var popup = $scope.closest(".jltma-template-popup");
				JLTMA_ModalPopups.fixPopupLayout(popup);
			});
		},
		onPreviewChange: function() {},
		onControlChange: function(model) {
			var iframe = document.getElementById("elementor-preview-iframe");
			var popup = $(".jltma-template-popup", iframe.contentDocument || iframe.contentWindow.document);
			if (model.changed.hasOwnProperty("popup_display_as")) if ("notification" === model.changed["popup_display_as"]) popup.addClass("jltma-popup-notification");
			else popup.removeClass("jltma-popup-notification");
			if (model.changed.hasOwnProperty("popup_animation")) {
				var popupContainer = popup.find(".jltma-popup-container");
				popupContainer.removeAttr("class");
				popupContainer.addClass("jltma-popup-container animated " + model.changed["popup_animation"]);
			}
		},
		fixPopupLayout: function(popup) {
			var settings = JLTMA_ModalPopups.getDocumentSettings();
			if (!popup.find(".jltma-popup-container-inner").hasClass("ps")) {
				if (typeof PerfectScrollbar !== "undefined") new PerfectScrollbar(popup.find(".jltma-popup-container-inner")[0], { suppressScrollX: true });
			}
			if ("notification" === settings.popup_display_as) popup.addClass("jltma-popup-notification");
		},
		getDocumentSettings: function() {
			var documentSettings = {}, settings = elementor.settings.page.model;
			jQuery.each(settings.getActiveControls(), function(controlKey) {
				documentSettings[controlKey] = settings.attributes[controlKey];
			});
			return documentSettings;
		},
		settingsNotification: function() {
			var version = "";
			var scriptSrc = $("#master-addons-scripts-js").attr("src");
			if (scriptSrc && scriptSrc.length > 6) version = "v-" + scriptSrc.substring(scriptSrc.length - 6, scriptSrc.length);
			else version = "v-default";
			if ((JSON.parse(localStorage.getItem("JLTMAPopupEditorNotification" + version)) || {}) + 6048e5 >= Date.now()) return;
			var nHTML;
			if ($("body").find("#elementor-editor-wrapper-v2").length > 0) nHTML = "                    <div id=\"jltma-template-settings-notification\" class=\"jltma-new-editor-bar\">                        <h4><i class=\"eicon-info-circle\"></i><span>Please Note</span></h4>                        <p>Click here to access <strong>Popup Settings</strong>.<br>Click Master Addons Logo to the bottom to open a <strong>Popup Library</strong>.</p>                        <i class=\"eicon-close close-notice\"></i>                    </div>                ";
			else nHTML = "                    <div id=\"jltma-template-settings-notification\">                        <h4><i class=\"eicon-info-circle\"></i><span>Please Note</span></h4>                        <p>Click here to access <strong>Popup Settings</strong>.<br>Click Master Addons Logo to the right to open a <strong>Popup Library</strong>.</p>                        <i class=\"eicon-close close-notice\"></i>                    </div>                ";
			setTimeout(function() {
				if ($("body").find("#elementor-editor-wrapper-v2").length > 0) {
					var $settingsBtn = $("button[value=\"MA Popup Settings\"]");
					if ($settingsBtn.length) {
						$settingsBtn.append(nHTML);
						$("#jltma-template-settings-notification").hide().fadeIn();
					}
				} else {
					$("body").append(nHTML);
					$("#jltma-template-settings-notification").hide().fadeIn();
				}
			}, 1e3);
			$(document).on("click", "#jltma-template-settings-notification .eicon-close", function() {
				$("#jltma-template-settings-notification").fadeOut();
			});
		}
	};
	$(window).on("elementor:init", JLTMA_ModalPopups.init);
})(jQuery);
//#endregion
})();
