(function(){
//#region dev/js/admin/admin-settings.js
(function($) {
	"use strict";
	var JLTMA_Toaster = {
		container: null,
		init: function() {
			if (!this.container) {
				this.container = $("<div class=\"jltma-toaster-container\"></div>");
				$("body").append(this.container);
			}
		},
		show: function(message, type, duration) {
			type = type || "success";
			duration = duration === void 0 ? 3e3 : duration;
			this.init();
			var toaster = $("<div class=\"jltma-toaster " + type + "\"><span class=\"jltma-toaster-icon " + type + "-icon\"></span><span class=\"jltma-toaster-content\">" + message + "</span><button class=\"jltma-toaster-close\"></button><div class=\"jltma-toaster-progress\"></div></div>");
			this.container.append(toaster);
			toaster.find(".jltma-toaster-close").on("click", function() {
				JLTMA_Toaster.dismiss(toaster);
			});
			if (duration > 0) setTimeout(function() {
				JLTMA_Toaster.dismiss(toaster);
			}, duration);
			return toaster;
		},
		dismiss: function(toaster) {
			toaster.addClass("jltma-toaster-exit");
			setTimeout(function() {
				toaster.remove();
			}, 300);
		},
		success: function(message, duration) {
			return this.show(message, "success", duration);
		},
		error: function(message, duration) {
			return this.show(message, "error", duration);
		},
		warning: function(message, duration) {
			return this.show(message, "warning", duration);
		},
		info: function(message, duration) {
			return this.show(message, "info", duration);
		}
	};
	jQuery(document).ready(function($) {
		"use strict";
		var saveHeaderAction = $(".jltma-tab-dashboard-header-wrapper .jltma-tab-element-save-setting");
		$(".jltma-master-addons-features-list input").on("click", function() {
			saveHeaderAction.addClass("jltma-addons-save-now");
			saveHeaderAction.removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-api-forms-settings input, #jltma-addons-white-label-settings input").on("keyup", function() {
			saveHeaderAction.addClass("jltma-addons-save-now");
			saveHeaderAction.removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-addons-white-label-settings input[type=\"checkbox\"]").on("change", function() {
			saveHeaderAction.addClass("jltma-addons-save-now");
			saveHeaderAction.removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-addons-elements .jltma-addons-enable-all, a.jltma-wl-plugin-logo, a.jltma-remove-button").on("click", function(e) {
			e.preventDefault();
			$("#jltma-addons-elements .jltma-master-addons_feature-switchbox input:enabled").each(function(i) {
				$(this).prop("checked", true).change();
			});
			saveHeaderAction.addClass("jltma-addons-save-now").removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-addons-elements .jltma-addons-disable-all").on("click", function(e) {
			e.preventDefault();
			$("#jltma-addons-elements .jltma-master-addons_feature-switchbox input:enabled").each(function(i) {
				$(this).prop("checked", false).change();
			});
			saveHeaderAction.addClass("jltma-addons-save-now").removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-addons-extensions .jltma-addons-enable-all").on("click", function(e) {
			e.preventDefault();
			$("#jltma-addons-extensions .jltma-master-addons_feature-switchbox input:enabled").each(function(i) {
				$(this).prop("checked", true).change();
			});
			saveHeaderAction.addClass("jltma-addons-save-now").removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-addons-extensions .jltma-addons-disable-all").on("click", function(e) {
			e.preventDefault();
			$("#jltma-addons-extensions .jltma-master-addons_feature-switchbox input:enabled").each(function(i) {
				$(this).prop("checked", false).change();
			});
			saveHeaderAction.addClass("jltma-addons-save-now").removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-master-addons-icons .jltma-addons-enable-all").on("click", function(e) {
			e.preventDefault();
			$("#jltma-master-addons-icons .jltma-master-addons_feature-switchbox input:enabled").each(function(i) {
				$(this).prop("checked", true).change();
			});
			saveHeaderAction.addClass("jltma-addons-save-now").removeAttr("disabled").css("cursor", "pointer");
		});
		$("#jltma-master-addons-icons .jltma-addons-disable-all").on("click", function(e) {
			e.preventDefault();
			$("#jltma-master-addons-icons .jltma-master-addons_feature-switchbox input:enabled").each(function(i) {
				$(this).prop("checked", false).change();
			});
			saveHeaderAction.addClass("jltma-addons-save-now").removeAttr("disabled").css("cursor", "pointer");
		});
		$(".master-addons-posts a.rsswidget").attr("target", "_blank");
		$("jltma-master-addons-tabs-navbar a:not(.jltma-upgrade-pro)").on("click", function(event) {
			event.preventDefault();
			var context = $(this).closest("jltma-master-addons-tabs-navbar").parent();
			var url = $(this).attr("href"), target = $(this).attr("target");
			if (target == "_blank") window.open(url, target);
			else {
				$("jltma-master-addons-tabs-navbar li", context).removeClass("jltma-admin-tab-active");
				$(this).closest("li").addClass("jltma-admin-tab-active");
				$(".jltma-master-addons-tab-panel", context).hide();
				$($(this).attr("href"), context).show();
			}
		});
		$("jltma-master-addons-tabs-navbar").each(function() {
			if ($(".jltma-admin-tab-active", this).length) $(".jltma-admin-tab-active", this).click();
			else $("a", this).first().click();
		});
		$(".jltma-upgrade-pro").not(".elementor-pro-conflict").on("click", function(event) {
			event.preventDefault();
			event.stopPropagation();
			var $popup = $("#jltma-popup");
			if ($popup.length) $popup.fadeIn(300);
		});
		$(document).on("click", ".jltma-popup-overlay, .popup-dismiss", function() {
			$("#jltma-popup").fadeOut(300);
		});
		$(document).on("keyup", function(e) {
			if (e.key === "Escape") $("#jltma-popup").fadeOut(300);
		});
		$(".elementor-pro-conflict:parent").on("click", function(event) {
			event.preventDefault();
			var extensionKey = $(this).find("input[type=\"checkbox\"]").attr("id");
			var message = "This feature is disabled because Elementor Pro is active.";
			if (extensionKey === "dynamic-tags") message = "This feature is disabled because Elementor Pro is active. Elementor Pro already provides Dynamic Tags functionality.";
			else if (extensionKey === "custom-css") message = "This feature is disabled because Elementor Pro is active. Elementor Pro already provides Custom CSS functionality.";
			swal({
				title: "Feature Disabled",
				text: message,
				type: "info",
				showCancelButton: false,
				confirmButtonColor: "#3085d6",
				confirmButtonText: "Understood"
			});
		});
		$("body").on("click", ".jltma-wl-plugin-logo", function(e) {
			e.preventDefault();
			var button = $(this), custom_uploader = wp.media({
				title: "Insert image",
				library: { type: "image" },
				button: { text: "Use this image" },
				multiple: false
			}).on("select", function() {
				var attachment = custom_uploader.state().get("selection").first().toJSON();
				button.html("<img src=\"" + attachment.url + "\">").next().show();
				$(".jltma-whl-selected-image").val(attachment.id);
			}).open();
		});
		$("body").on("click", ".jltma-remove-button", function(e) {
			e.preventDefault();
			var button = $(this);
			button.next().val("");
			button.hide().prev().html("<i class=\"dashicons dashicons-cloud-upload\"></i> <span>Upload image</span>");
		});
		$(".jltma-tab-element-save-setting").on("click", function(e) {
			e.preventDefault();
			let $this = $(this);
			if ($(this).hasClass("jltma-addons-save-now")) {
				const ajaxPromises = [];
				ajaxPromises.push($.ajax({
					url: JLTMA_OPTIONS.ajaxurl,
					type: "post",
					data: {
						action: "jltma_save_elements_settings",
						security: JLTMA_OPTIONS.ajax_nonce,
						fields: $("#jltma-addons-tab-settings").serialize()
					}
				}));
				ajaxPromises.push($.ajax({
					url: JLTMA_OPTIONS.ajaxurl,
					type: "post",
					data: {
						action: "master_addons_save_extensions_settings",
						security: JLTMA_OPTIONS.ajax_extensions_nonce,
						fields: $("#jltma-addons-extensions-settings").serialize()
					}
				}));
				ajaxPromises.push($.ajax({
					url: JLTMA_OPTIONS.ajaxurl,
					type: "post",
					data: {
						action: "jltma_save_api_settings",
						security: JLTMA_OPTIONS.ajax_api_nonce,
						fields: $("#jltma-api-forms-settings").serializeArray()
					}
				}));
				ajaxPromises.push($.ajax({
					url: JLTMA_OPTIONS.ajaxurl,
					type: "post",
					data: {
						action: "jltma_save_icons_library_settings",
						security: JLTMA_OPTIONS.ajax_icons_library_nonce,
						fields: $("#jltma-master-addons-icons-settings").serialize()
					}
				}));
				if ("valid" === $(this).data("lic")) ajaxPromises.push($.ajax({
					url: JLTMA_OPTIONS.ajaxurl,
					type: "post",
					data: {
						action: "jltma_save_white_label_settings",
						security: JLTMA_OPTIONS.ajax_nonce,
						fields: $("form#jltma-addons-white-label-settings").serialize()
					}
				}));
				Promise.all(ajaxPromises).then(function(responses) {
					$this.html("Save Settings");
					saveHeaderAction.removeClass("jltma-addons-save-now");
					JLTMA_Toaster.success("Settings saved successfully!");
				}).catch(function(error) {
					$this.html("Save Settings");
					saveHeaderAction.removeClass("jltma-addons-save-now");
					JLTMA_Toaster.error("Failed to save settings. Please try again.");
				});
			} else $(this).attr("disabled", "true").css("cursor", "not-allowed");
		});
		$("select.master-addons-rollback-select").on("change", function() {
			var $this = $(this), $rollbackButton = $this.next(".jltma-rollback-button"), placeholderText = $rollbackButton.data("placeholder-text"), placeholderUrl = $rollbackButton.data("placeholder-url");
			$rollbackButton.html(placeholderText.replace("{VERSION}", $this.val()));
			$rollbackButton.attr("href", placeholderUrl.replace("VERSION", $this.val()));
		}).trigger("change");
		$(".jltma-rollback-button").on("click", function(event) {
			event.preventDefault();
			var $this = $(this);
			new DialogsManager.Instance().createWidget("confirm", {
				headerMessage: JLTMA_OPTIONS.rollback.rollback_to_previous_version,
				message: JLTMA_OPTIONS.rollback.rollback_confirm,
				strings: {
					cancel: JLTMA_OPTIONS.rollback.cancel,
					confirm: JLTMA_OPTIONS.rollback.yes
				},
				onConfirm: function() {
					$this.addClass("loading");
					location.href = $this.attr("href");
				}
			}).show();
		});
		(function(n) {
			n.fn.copiq = function(e) {
				var t = n.extend({
					parent: "body",
					content: "",
					onSuccess: function() {},
					onError: function() {}
				}, e);
				return this.each(function() {
					var e = n(this);
					e.on("click", function() {
						var n = e.parents(t.parent).find(t.content);
						var o = document.createRange();
						var c = window.getSelection();
						o.selectNodeContents(n[0]);
						c.removeAllRanges();
						c.addRange(o);
						try {
							t[document.execCommand("copy") ? "onSuccess" : "onError"](e, n, c.toString());
						} catch (i) {}
						c.removeAllRanges();
					});
				});
			};
		})(jQuery);
		$(".jltma-copy-btn").copiq({
			parent: ".copy-section",
			content: ".api-element-inner",
			onSuccess: function($element, source, selection) {
				$("span", $element).text($element.attr("data-text-copied"));
				setTimeout(function() {
					$("span", $element).text($element.attr("data-text"));
				}, 2e3);
			}
		});
	});
})(jQuery);
//#endregion
})();
