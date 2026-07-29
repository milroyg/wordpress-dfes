(function(){
//#region dev/js/common/dialog.js
/**
* Master Addons — shared confirm/alert/loading dialog.
*
* Framework-agnostic vanilla-JS modal. Works identically from React apps
* (template-library, template-kits-app, admin-settings, setup-wizard) and
* from jQuery bundles (popup-builder, widget-builder, theme-builder,
* master-addons-scripts, etc.).
*
* Usage:
*   // ES module
*   import { confirmDialog, alertDialog, loadingDialog } from '../../../common/dialog';
*   const ok = await confirmDialog({ title, message, tone: 'danger' });
*   await alertDialog({ title, message, tone: 'success' });
*   const loader = loadingDialog({ message: 'Deleting…' });
*   // ... async work ...
*   loader.close();
*
*   // Global (jQuery / vanilla / legacy bundles)
*   const ok = await window.JLTMA_Dialog.confirm({ title, message });
*   await window.JLTMA_Dialog.alert({ title, message });
*   const loader = window.JLTMA_Dialog.loading({ message: 'Saving…' });
*
* Styles are auto-injected on first use. Colors reference the admin
* design-token CSS variables (--jltma-primary, --jltma-destructive,
* etc.) with hex fallbacks so the dialog renders correctly in contexts
* where the design tokens aren't loaded (legacy admin pages, generic
* frontend previews, non-React bundles).
*
* Tones (button color + focus ring hue):
*   primary | danger | success | warning
*/
var STYLE_ID = "jltma-dialog-styles";
var PORTAL_ID = "jltma-dialog-portal";
var CSS = [
	".jltma-dialog{position:fixed;inset:0;z-index:9999999;display:flex;align-items:center;justify-content:center;animation:jltma-dialog-fade-in 0.15s ease-out;}",
	".jltma-dialog__overlay{position:absolute;inset:0;background:rgba(15,23,42,0.55);backdrop-filter:blur(1.5px);-webkit-backdrop-filter:blur(1.5px);}",
	".jltma-dialog__content{position:relative;z-index:1;width:min(460px,calc(100% - 32px));background:var(--jltma-popover,#ffffff);color:var(--jltma-popover-foreground,#0f172a);border:1px solid var(--jltma-border,#e2e8f0);border-radius:var(--jltma-radius,10px);box-shadow:0 20px 48px rgba(15,23,42,0.24),0 8px 16px rgba(15,23,42,0.12);padding:24px;box-sizing:border-box;animation:jltma-dialog-scale-in 0.18s cubic-bezier(0.16,1,0.3,1);font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,sans-serif;}",
	".jltma-dialog__title{margin:0 0 10px;font-size:18px;font-weight:600;line-height:1.35;letter-spacing:-0.01em;color:var(--jltma-foreground,#0f172a);}",
	".jltma-dialog__message{margin:0 0 24px;font-size:14px;line-height:1.55;color:var(--jltma-muted-foreground,#475569);white-space:pre-line;}",
	".jltma-dialog__actions{display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;}",
	".jltma-dialog__btn{appearance:none;border:1px solid transparent;border-radius:calc(var(--jltma-radius,10px) - 2px);height:36px;padding:0 16px;font-size:13px;font-weight:600;line-height:1;cursor:pointer;transition:background-color 0.15s ease,border-color 0.15s ease,color 0.15s ease,box-shadow 0.15s ease,opacity 0.15s ease;font-family:inherit;display:inline-flex;align-items:center;justify-content:center;gap:6px;}",
	".jltma-dialog__btn:focus-visible{outline:2px solid var(--jltma-ring,rgba(104,20,205,0.45));outline-offset:2px;}",
	".jltma-dialog__btn:disabled{opacity:0.6;cursor:not-allowed;}",
	".jltma-dialog__btn--secondary{background:var(--jltma-background,#ffffff);border-color:var(--jltma-border,#e2e8f0);color:var(--jltma-foreground,#334155);}",
	".jltma-dialog__btn--secondary:hover:not(:disabled){background:var(--jltma-accent,#f1f5f9);border-color:var(--jltma-border,#cbd5e1);}",
	".jltma-dialog__btn--primary{background:var(--jltma-primary,#6814cd);color:var(--jltma-primary-foreground,#ffffff);}",
	".jltma-dialog__btn--primary:hover:not(:disabled){opacity:0.9;}",
	".jltma-dialog__btn--danger{background:var(--jltma-destructive,#dc2626);color:#ffffff;}",
	".jltma-dialog__btn--danger:hover:not(:disabled){opacity:0.9;}",
	".jltma-dialog__btn--success{background:#16a34a;color:#ffffff;}",
	".jltma-dialog__btn--success:hover:not(:disabled){opacity:0.9;}",
	".jltma-dialog__btn--warning{background:#d97706;color:#ffffff;}",
	".jltma-dialog__btn--warning:hover:not(:disabled){opacity:0.9;}",
	".jltma-dialog--loading .jltma-dialog__content{padding:32px 28px;width:min(340px,calc(100% - 32px));text-align:center;}",
	".jltma-dialog--loading .jltma-dialog__message{margin:0;color:var(--jltma-foreground,#0f172a);font-weight:500;font-size:14px;}",
	".jltma-dialog__spinner{width:36px;height:36px;border:3px solid var(--jltma-border,#e2e8f0);border-top-color:var(--jltma-primary,#6814cd);border-radius:50%;margin:0 auto 16px;animation:jltma-dialog-spin 0.8s linear infinite;}",
	"@keyframes jltma-dialog-fade-in{from{opacity:0;}to{opacity:1;}}",
	"@keyframes jltma-dialog-scale-in{from{opacity:0;transform:scale(0.94) translateY(4px);}to{opacity:1;transform:scale(1) translateY(0);}}",
	"@keyframes jltma-dialog-spin{to{transform:rotate(360deg);}}"
].join("");
function ensureStyles() {
	if (typeof document === "undefined") return;
	if (document.getElementById(STYLE_ID)) return;
	var style = document.createElement("style");
	style.id = STYLE_ID;
	style.textContent = CSS;
	document.head.appendChild(style);
}
function ensurePortal() {
	var portal = document.getElementById(PORTAL_ID);
	if (!portal) {
		portal = document.createElement("div");
		portal.id = PORTAL_ID;
		document.body.appendChild(portal);
	}
	return portal;
}
function openDialog(opts) {
	return new Promise(function(resolve) {
		ensureStyles();
		var portal = ensurePortal();
		var tone = opts.tone || "primary";
		var showCancel = !!opts.showCancel;
		var root = document.createElement("div");
		root.className = "jltma-dialog";
		var overlay = document.createElement("div");
		overlay.className = "jltma-dialog__overlay";
		var content = document.createElement("div");
		content.className = "jltma-dialog__content jltma-dialog-tone-" + tone;
		content.setAttribute("role", "alertdialog");
		content.setAttribute("aria-modal", "true");
		if (opts.title) {
			var titleEl = document.createElement("h3");
			titleEl.className = "jltma-dialog__title";
			titleEl.textContent = opts.title;
			content.appendChild(titleEl);
		}
		if (opts.message) {
			var msgEl = document.createElement("p");
			msgEl.className = "jltma-dialog__message";
			msgEl.textContent = opts.message;
			content.appendChild(msgEl);
		}
		var actions = document.createElement("div");
		actions.className = "jltma-dialog__actions";
		var cleanup = function(value) {
			document.removeEventListener("keydown", onKey);
			overlay.removeEventListener("click", onOverlay);
			if (root.parentNode) root.parentNode.removeChild(root);
			resolve(value);
		};
		var onKey = function(e) {
			if (e.key === "Escape") {
				e.preventDefault();
				cleanup(false);
			} else if (e.key === "Enter") {
				e.preventDefault();
				cleanup(true);
			}
		};
		document.addEventListener("keydown", onKey);
		var onOverlay = function() {
			cleanup(false);
		};
		overlay.addEventListener("click", onOverlay);
		if (showCancel) {
			var cancelBtn = document.createElement("button");
			cancelBtn.type = "button";
			cancelBtn.className = "jltma-dialog__btn jltma-dialog__btn--secondary";
			cancelBtn.textContent = opts.cancelText || "Cancel";
			cancelBtn.addEventListener("click", function() {
				cleanup(false);
			});
			actions.appendChild(cancelBtn);
		}
		var confirmBtn = document.createElement("button");
		confirmBtn.type = "button";
		confirmBtn.className = "jltma-dialog__btn jltma-dialog__btn--" + tone;
		confirmBtn.textContent = opts.confirmText || "OK";
		confirmBtn.addEventListener("click", function() {
			cleanup(true);
		});
		actions.appendChild(confirmBtn);
		content.appendChild(actions);
		root.appendChild(overlay);
		root.appendChild(content);
		portal.appendChild(root);
		try {
			confirmBtn.focus({ preventScroll: true });
		} catch (e) {
			confirmBtn.focus();
		}
	});
}
function confirmDialog(options) {
	var opts = options || {};
	return openDialog({
		title: opts.title || "Are you sure?",
		message: opts.message || "",
		confirmText: opts.confirmText || "Confirm",
		cancelText: opts.cancelText || "Cancel",
		tone: opts.tone || "primary",
		showCancel: true
	});
}
function alertDialog(options) {
	var opts = options || {};
	return openDialog({
		title: opts.title || "Notice",
		message: opts.message || "",
		confirmText: opts.confirmText || "OK",
		tone: opts.tone || "primary",
		showCancel: false
	});
}
/**
* Blocking loading overlay without action buttons.
*
* Returns `{ close }` — call `close()` when the async work completes.
* Intended for "Deleting…", "Saving…", "Importing…" transitions where
* reload follows success and a second success modal would be redundant.
*
*   const loader = loadingDialog({ message: 'Deleting…' });
*   try {
*     await doTheWork();
*     window.location.reload();
*   } catch (err) {
*     loader.close();
*     await alertDialog({ title: 'Failed', message: err.message, tone: 'danger' });
*   }
*/
function loadingDialog(options) {
	var opts = options || {};
	ensureStyles();
	var portal = ensurePortal();
	var root = document.createElement("div");
	root.className = "jltma-dialog jltma-dialog--loading";
	var overlay = document.createElement("div");
	overlay.className = "jltma-dialog__overlay";
	var content = document.createElement("div");
	content.className = "jltma-dialog__content";
	content.setAttribute("role", "status");
	content.setAttribute("aria-live", "polite");
	var spinner = document.createElement("div");
	spinner.className = "jltma-dialog__spinner";
	spinner.setAttribute("aria-hidden", "true");
	content.appendChild(spinner);
	var msgEl = document.createElement("p");
	msgEl.className = "jltma-dialog__message";
	msgEl.textContent = opts.message || "Working…";
	content.appendChild(msgEl);
	root.appendChild(overlay);
	root.appendChild(content);
	portal.appendChild(root);
	var closed = false;
	return {
		close: function() {
			if (closed) return;
			closed = true;
			if (root.parentNode) root.parentNode.removeChild(root);
		},
		setMessage: function(next) {
			msgEl.textContent = next;
		}
	};
}
if (typeof window !== "undefined") {
	window.JLTMA_Dialog = window.JLTMA_Dialog || {};
	window.JLTMA_Dialog.confirm = confirmDialog;
	window.JLTMA_Dialog.alert = alertDialog;
	window.JLTMA_Dialog.loading = loadingDialog;
}
//#endregion
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
		show: function(message, type = "success", duration = 3e3) {
			this.init();
			var toaster = $(`
                <div class="jltma-toaster ${type}">
                    <span class="jltma-toaster-icon ${type}-icon"></span>
                    <span class="jltma-toaster-content">${message}</span>
                    <button class="jltma-toaster-close"></button>
                    <div class="jltma-toaster-progress"></div>
                </div>
            `);
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
	const WidgetAdmin = {
		init: function() {
			this.bindEvents();
			this.setupNativeCPTList();
		},
		/**
		* Setup native CPT list table interception
		* Intercept WP's native "Add New" button and inject extra buttons
		*/
		setupNativeCPTList: function() {
			var self = this;
			$(document).on("click", ".wrap .page-title-action:not(.jltma-cpt-btn)", function(e) {
				e.preventDefault();
				self.openModal(e);
			});
			var $addNewBtn = $(".wrap .page-title-action").first();
			if ($addNewBtn.length) {
				$addNewBtn.text("Add New Widget");
				if (!$(".jltma-import-widget").length) $addNewBtn.after("<a href=\"#\" class=\"jltma-cpt-btn jltma-cpt-btn-secondary jltma-import-widget\"><svg width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4\"/><polyline points=\"17 8 12 3 7 8\"/><line x1=\"12\" y1=\"3\" x2=\"12\" y2=\"15\"/></svg> Import Widget</a><a href=\"https://master-addons.com/widget-builder/\" target=\"_blank\" class=\"jltma-cpt-btn jltma-cpt-btn-youtube\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"#ff0000\"><path d=\"M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.546 12 3.546 12 3.546s-7.505 0-9.377.504A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.504 9.376.504 9.376.504s7.505 0 9.377-.504a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z\"/></svg> Video Tutorial</a>");
			}
		},
		bindEvents: function() {
			$(document).on("click", ".jltma-add-new-widget", this.openModal.bind(this));
			$(document).on("change", "#jltma_widget_category", this.handleCategoryChange.bind(this));
			$(document).on("click", ".jltma-cancel-inline-category", this.hideInlineCategoryAdd.bind(this));
			$(document).on("click", ".jltma-save-inline-category", this.saveInlineCategory.bind(this));
			$(document).on("click", ".jltma-pop-close, .close-btn, .jltma-modal-backdrop", this.closeModal.bind(this));
			$(document).on("click", ".jltma-import-widget", this.openImportModal.bind(this));
			$(document).on("click", ".jltma-import-close, .jltma-import-backdrop", this.closeImportModal.bind(this));
			$(document).on("change", "#jltma-import-file", this.handleFileSelect.bind(this));
			$(document).on("keydown", function(e) {
				if (e.key === "Escape" || e.keyCode === 27) {
					if ($("#jltma_import_widget_modal").is(":visible")) {
						$("#jltma_import_widget_modal").fadeOut(300);
						$("body").removeClass("jltma-modal-open");
						WidgetAdmin.resetImportModal();
					}
				}
			});
			$(document).on("dragover", "#jltma-dropzone", this.handleDragOver.bind(this));
			$(document).on("dragleave", "#jltma-dropzone", this.handleDragLeave.bind(this));
			$(document).on("drop", "#jltma-dropzone", this.handleDrop.bind(this));
			$(document).on("keypress", "#jltma_new_category_title", function(e) {
				if (e.which === 13) {
					e.preventDefault();
					$(".jltma-save-inline-category").trigger("click");
				}
			});
			$(document).on("keydown", "#jltma_new_category_title", function(e) {
				if (e.which === 27) {
					e.preventDefault();
					$(".jltma-cancel-inline-category").trigger("click");
				}
			});
			$(document).on("submit", "#jltma-widget-form", this.saveWidget.bind(this));
			$(document).on("click", ".jltma-save-widget", function(e) {
				e.preventDefault();
				$("#jltma-widget-form").trigger("submit");
			});
			$(document).on("click", ".jltma-delete-widget", this.deleteWidget.bind(this));
			$(document).on("click", ".jltma-copy-shortcode-widget", this.copyShortcode.bind(this));
			$(document).on("click", ".jltma-widget-edit-category, .jltma-widget-edit-cond", this.openModalFromConditions.bind(this));
		},
		openModal: function(e) {
			e.preventDefault();
			var widgetId = $(e.currentTarget).data("widget-id") || 0;
			var modal = $("#jltma_widget_builder_modal");
			modal.addClass("loading");
			modal.addClass("active");
			$("body").addClass("jltma-modal-open");
			if (widgetId > 0) {
				modal.find(".jltma-modal-title").text("Edit Widget");
				$("#jltma_widget_id").val(widgetId);
				this.loadWidgetData(widgetId);
			} else {
				modal.find(".jltma-modal-title").text("Create New Widget");
				this.resetForm();
				modal.removeClass("loading");
			}
			modal.find("form").attr("data-widget-id", widgetId);
		},
		closeModal: function(e) {
			if ($(e.target).hasClass("jltma-modal-backdrop") || $(e.target).hasClass("close-btn") || $(e.target).closest(".jltma-pop-close").length) {
				$("#jltma_widget_builder_modal").removeClass("active");
				$("#jltma_category_modal").removeClass("active");
				$("body").removeClass("jltma-modal-open");
				e.preventDefault();
			}
		},
		resetForm: function() {
			$("#jltma-widget-form")[0].reset();
			$("#jltma_widget_id").val("");
			var categorySelect = $("select#jltma_widget_category");
			var initialValue = categorySelect.val() || "general";
			categorySelect.data("previous-value", initialValue);
			$("#jltma-inline-category-add").hide();
			$("#jltma_new_category_title").val("");
		},
		loadWidgetData: function(widgetId) {
			var modal = $("#jltma_widget_builder_modal");
			$.ajax({
				url: jltmaWidgetAdmin.ajax_url,
				type: "GET",
				data: {
					action: "jltma_widget_get_data",
					widget_id: widgetId,
					_nonce: jltmaWidgetAdmin.widget_nonce
				},
				success: function(response) {
					if (response.success) {
						$("#jltma_widget_id").val(widgetId);
						$("#jltma_widget_title").val(response.data.title);
						var categoryDropdown = $("select#jltma_widget_category");
						var categoryValue = response.data.category || "general";
						categoryDropdown.val(categoryValue);
						categoryDropdown.data("previous-value", categoryValue);
						$("#jltma-inline-category-add").hide();
						$("#jltma_new_category_title").val("");
					}
					modal.removeClass("loading");
				},
				error: function(xhr, status, error) {
					modal.removeClass("loading");
					JLTMA_Toaster.error(jltmaWidgetAdmin.strings.error);
				}
			});
		},
		handleCategoryChange: function(e) {
			var $select = $(e.currentTarget);
			var selectedValue = $select.val();
			if (selectedValue === "__add_new__") {
				var previousValue = $select.data("previous-value") || "general";
				$select.data("previous-value", previousValue);
				$("#jltma-inline-category-add").slideDown(200);
				$("#jltma_new_category_title").val("").focus();
			} else $select.data("previous-value", selectedValue);
		},
		showInlineCategoryAdd: function(e) {
			e.preventDefault();
			$("#jltma-inline-category-add").slideDown(200);
			$("#jltma_new_category_title").focus();
		},
		hideInlineCategoryAdd: function(e) {
			e.preventDefault();
			var categorySelect = $("select#jltma_widget_category");
			var previousValue = categorySelect.data("previous-value") || "general";
			$("#jltma-inline-category-add").slideUp(200);
			$("#jltma_new_category_title").val("");
			categorySelect.val(previousValue);
		},
		saveInlineCategory: function(e) {
			e.preventDefault();
			var title = $("#jltma_new_category_title").val().trim();
			var categorySelect = $("select#jltma_widget_category");
			var saveBtn = $(".jltma-save-inline-category");
			if (!title) {
				JLTMA_Toaster.error("Category title is required.");
				return;
			}
			var originalText = saveBtn.text();
			saveBtn.prop("disabled", true).text("Saving...");
			var slug = title.toLowerCase().replace(/[^a-z0-9\s-]/g, "").replace(/\s+/g, "-").replace(/-+/g, "-").trim();
			$.ajax({
				url: jltmaWidgetAdmin.admin_url + "admin-ajax.php?rest_route=/jltma/v1/categories",
				type: "POST",
				headers: { "X-WP-Nonce": jltmaWidgetAdmin.widget_nonce },
				contentType: "application/json",
				data: JSON.stringify({
					name: title,
					slug
				}),
				success: function(response) {
					if (response.success && response.data) {
						var newSlug = response.data.slug;
						var newTitle = response.data.title;
						if (categorySelect.find("option[value=\"" + newSlug + "\"]").length === 0) categorySelect.find("option[value=\"__add_new__\"]").before("<option value=\"" + newSlug + "\">" + newTitle + "</option>");
						categorySelect.val(newSlug);
						categorySelect.data("previous-value", newSlug);
						$("#jltma-inline-category-add").slideUp(200);
						$("#jltma_new_category_title").val("");
						JLTMA_Toaster.success(jltmaWidgetAdmin.strings.category_added);
					} else {
						JLTMA_Toaster.error("Failed to create category. Please try again.");
						categorySelect.val(categorySelect.data("previous-value") || "general");
					}
					saveBtn.prop("disabled", false).text(originalText);
				},
				error: function(xhr, status, error) {
					JLTMA_Toaster.error("Failed to create category. Please try again.");
					categorySelect.val(categorySelect.data("previous-value") || "general");
					saveBtn.prop("disabled", false).text(originalText);
				}
			});
		},
		saveWidget: function(e) {
			e.preventDefault();
			$("#jltma-widget-form");
			var submitBtn = $(".jltma-save-widget");
			var widgetId = $("#jltma_widget_id").val();
			var title = $("#jltma_widget_title").val().trim();
			var category = $("select#jltma_widget_category").val();
			if (!title) {
				JLTMA_Toaster.error(jltmaWidgetAdmin.strings.widget_title_required);
				return;
			}
			var originalText = submitBtn.text();
			submitBtn.prop("disabled", true).text(jltmaWidgetAdmin.strings.saving);
			$.ajax({
				url: jltmaWidgetAdmin.ajax_url,
				type: "POST",
				data: {
					action: "jltma_widget_save_data",
					widget_id: widgetId,
					widget_title: title,
					widget_category: category,
					_nonce: jltmaWidgetAdmin.widget_nonce
				},
				success: function(response) {
					if (response.success) if (widgetId) {
						var categoryName = $("select#jltma_widget_category option:selected").text();
						$("#jltma_widget_builder_modal").removeClass("active");
						$("body").removeClass("jltma-modal-open");
						$("#post-" + widgetId).find("td.column-jltma_widget_category").html("<span class=\"jltma-widget-category\">" + categoryName + "</span><br><a href=\"#\" class=\"jltma-widget-edit-category\" data-post-id=\"" + widgetId + "\" data-category=\"" + category + "\"><span class=\"dashicons dashicons-edit\"></span> Edit Category</a>");
						JLTMA_Toaster.success(jltmaWidgetAdmin.strings.saved);
					} else window.location.href = response.data.edit_url;
					else {
						JLTMA_Toaster.error(response.data.message || jltmaWidgetAdmin.strings.error);
						submitBtn.prop("disabled", false).text(originalText);
					}
				},
				error: function(xhr, status, error) {
					JLTMA_Toaster.error(jltmaWidgetAdmin.strings.error);
					submitBtn.prop("disabled", false).text(originalText);
				}
			});
		},
		deleteWidget: function(e) {
			e.preventDefault();
			var widgetId = $(e.currentTarget).data("widget-id");
			if (!confirm(jltmaWidgetAdmin.strings.confirm_delete)) return;
			$.ajax({
				url: jltmaWidgetAdmin.ajax_url,
				type: "POST",
				data: {
					action: "jltma_widget_delete",
					widget_id: widgetId,
					_nonce: jltmaWidgetAdmin.widget_nonce
				},
				success: function(response) {
					if (response.success) location.reload();
					else JLTMA_Toaster.error(response.data.message || jltmaWidgetAdmin.strings.error);
				},
				error: function() {
					JLTMA_Toaster.error(jltmaWidgetAdmin.strings.error);
				}
			});
		},
		copyShortcode: function(e) {
			e.preventDefault();
			var shortcode = $(e.currentTarget).data("shortcode");
			var button = $(e.currentTarget);
			var temp = $("<textarea>");
			$("body").append(temp);
			temp.val(shortcode).select();
			document.execCommand("copy");
			temp.remove();
			var originalHtml = button.html();
			button.html("<span class=\"dashicons dashicons-yes\"></span> " + jltmaWidgetAdmin.strings.copied);
			setTimeout(function() {
				button.html(originalHtml);
			}, 2e3);
		},
		openModalFromConditions: function(e) {
			e.preventDefault();
			var widgetId = $(e.currentTarget).data("post-id") || $(e.currentTarget).attr("id");
			var modal = $("#jltma_widget_builder_modal");
			modal.addClass("loading");
			modal.addClass("active");
			$("body").addClass("jltma-modal-open");
			if (widgetId > 0) {
				modal.find(".jltma-modal-title").text("Edit Widget");
				$("#jltma_widget_id").val(widgetId);
				this.loadWidgetData(widgetId);
			}
			modal.find("form").attr("data-widget-id", widgetId);
		},
		openImportModal: function(e) {
			e.preventDefault();
			$("#jltma_import_widget_modal").fadeIn(300);
			$("body").addClass("jltma-modal-open");
		},
		closeImportModal: function(e) {
			if ($(e.target).hasClass("jltma-import-backdrop") || $(e.target).hasClass("jltma-import-close") || $(e.target).closest(".jltma-import-close").length) {
				$("#jltma_import_widget_modal").fadeOut(300);
				$("body").removeClass("jltma-modal-open");
				this.resetImportModal();
				e.preventDefault();
			}
		},
		resetImportModal: function() {
			$("#jltma-dropzone").removeClass("dragging");
			$(".jltma-dropzone-content").show();
			$(".jltma-importing").hide();
			$("#jltma-import-file").val("");
		},
		handleDragOver: function(e) {
			e.preventDefault();
			e.stopPropagation();
			$("#jltma-dropzone").addClass("dragging");
		},
		handleDragLeave: function(e) {
			e.preventDefault();
			e.stopPropagation();
			$("#jltma-dropzone").removeClass("dragging");
		},
		handleDrop: function(e) {
			e.preventDefault();
			e.stopPropagation();
			$("#jltma-dropzone").removeClass("dragging");
			var files = e.originalEvent.dataTransfer.files;
			if (files.length > 0) this.processImportFile(files[0]);
		},
		handleFileSelect: function(e) {
			var file = e.target.files[0];
			if (file) this.processImportFile(file);
		},
		processImportFile: function(file) {
			var self = this;
			if (!file.name.endsWith(".json")) {
				JLTMA_Toaster.error("Please select a valid JSON file.", 5e3);
				return;
			}
			$(".jltma-dropzone-content").hide();
			$(".jltma-importing").show();
			var reader = new FileReader();
			reader.onload = function(event) {
				try {
					var jsonData = JSON.parse(event.target.result);
					var isValid = true;
					if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) isValid = window.jltmaWidgetBuilder.hooks.applyFilters("jltma_cwb_import_validate", isValid, jsonData);
					if (!jsonData.widget || !jsonData.version) throw new Error("Invalid widget file format");
					if (!isValid) throw new Error("Import validation failed");
					var importedWidget = jsonData.widget;
					if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) window.jltmaWidgetBuilder.hooks.doAction("jltma_cwb_before_import", importedWidget);
					var processedWidget = importedWidget;
					if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) processedWidget = window.jltmaWidgetBuilder.hooks.applyFilters("jltma_cwb_import_data", importedWidget, jsonData);
					self.createWidgetFromImport(processedWidget, jsonData);
				} catch (parseError) {
					console.error("Failed to parse widget file:", parseError);
					JLTMA_Toaster.error("Failed to import widget. Invalid JSON file format.", 5e3);
					self.resetImportModal();
				}
			};
			reader.onerror = function() {
				console.error("Failed to read file");
				JLTMA_Toaster.error("Failed to read file. Please try again.", 5e3);
				self.resetImportModal();
			};
			reader.readAsText(file);
		},
		createWidgetFromImport: function(widgetData, originalJsonData) {
			var self = this;
			var CUSTOM_CATEGORY_PREFIX = "jltma_cwb_custom_";
			var categorySlug = widgetData.category || "general";
			var isCustomCategory = widgetData.isCustomCategory || categorySlug.startsWith(CUSTOM_CATEGORY_PREFIX);
			var createWidget = function() {
				console.log("Creating widget via REST API with data:", widgetData);
				$.ajax({
					url: jltmaWidgetAdmin.rest_url + "/widgets",
					type: "POST",
					headers: { "X-WP-Nonce": jltmaWidgetAdmin.rest_nonce },
					contentType: "application/json",
					data: JSON.stringify(widgetData),
					success: function(response) {
						console.log("Widget created successfully:", response);
						if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) window.jltmaWidgetBuilder.hooks.doAction("jltma_cwb_imported", widgetData, originalJsonData);
						JLTMA_Toaster.success("Widget imported successfully! Redirecting...", 3e3);
						setTimeout(function() {
							$("#jltma_import_widget_modal").fadeOut(300);
							$("body").removeClass("jltma-modal-open");
							setTimeout(function() {
								var newId = response && response.data ? response.data.id : null;
								var editUrl = response && response.data && response.data.edit_url ? response.data.edit_url : jltmaWidgetAdmin.admin_url + "admin.php?page=jltma-widget-editor&widget_id=" + newId;
								window.location.href = editUrl;
							}, 300);
						}, 1500);
					},
					error: function(xhr, status, error) {
						console.error("Failed to create widget:", error);
						console.error("XHR Response:", xhr);
						console.error("Status:", xhr.status);
						console.error("Response Text:", xhr.responseText);
						console.error("Request Data:", widgetData);
						var errorMessage = "Failed to import widget. Please try again.";
						try {
							var errorData = JSON.parse(xhr.responseText);
							if (errorData.message) errorMessage = errorData.message;
						} catch (e) {}
						JLTMA_Toaster.error(errorMessage + " Check console for details.", 5e3);
						self.resetImportModal();
					}
				});
			};
			if (isCustomCategory) $.ajax({
				url: jltmaWidgetAdmin.rest_url + "/categories",
				type: "GET",
				headers: { "X-WP-Nonce": jltmaWidgetAdmin.rest_nonce },
				success: function(categories) {
					if (!categories.some(function(cat) {
						return cat.slug === categorySlug;
					})) self.createCategoryIfNotExists(categorySlug, createWidget);
					else createWidget();
				},
				error: function(xhr, status, error) {
					console.warn("Failed to check categories, proceeding with widget creation:", error);
					createWidget();
				}
			});
			else createWidget();
		},
		createCategoryIfNotExists: function(categorySlug, callback) {
			var CUSTOM_CATEGORY_PREFIX = "jltma_cwb_custom_";
			var categoryName = categorySlug.replace(CUSTOM_CATEGORY_PREFIX, "").split("_").map(function(word) {
				return word.charAt(0).toUpperCase() + word.slice(1);
			}).join(" ");
			var shouldCreate = true;
			if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) shouldCreate = window.jltmaWidgetBuilder.hooks.applyFilters("jltma_cwb_before_create_category", shouldCreate, categorySlug, categoryName);
			if (!shouldCreate) {
				if (callback) callback();
				return;
			}
			$.ajax({
				url: jltmaWidgetAdmin.rest_url + "/categories",
				type: "POST",
				headers: { "X-WP-Nonce": jltmaWidgetAdmin.rest_nonce },
				contentType: "application/json",
				data: JSON.stringify({
					name: categoryName,
					slug: categorySlug,
					custom_prefix: CUSTOM_CATEGORY_PREFIX,
					auto_created: true
				}),
				success: function(result) {
					if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) window.jltmaWidgetBuilder.hooks.doAction("jltma_cwb_category_created", result.data, categorySlug, categoryName);
					if (callback) callback();
				},
				error: function(xhr, status, error) {
					console.error("Failed to create category:", categorySlug, error);
					JLTMA_Toaster.warning("Could not create category \"" + categoryName + "\". Widget will use \"general\" category.", 5e3);
					if (callback) callback();
				}
			});
		}
	};
	$(document).ready(function() {
		WidgetAdmin.init();
	});
})(jQuery);
//#endregion
})();
