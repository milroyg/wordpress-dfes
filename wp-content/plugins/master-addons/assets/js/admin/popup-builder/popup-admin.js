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
//#region dev/js/admin/popup-builder/popup-admin.js
/**
* Master Addons Popup Builder Admin Script
* Based on Theme Builder but adapted for Popups
*/
(function($) {
	"use strict";
	var isPremium = typeof jltmaPopupAdmin !== "undefined" && jltmaPopupAdmin.is_premium;
	var JLTMA_Popup_Toaster = {
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
				JLTMA_Popup_Toaster.dismiss(toaster);
			});
			if (duration > 0) setTimeout(function() {
				JLTMA_Popup_Toaster.dismiss(toaster);
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
	var Popup_Builder_Admin = {
		_templatesLoaded: false,
		_templates: [],
		_filteredTemplates: [],
		_categories: [],
		_activeCategory: "",
		_searchTerm: "",
		_visibleCount: 0,
		_ITEMS_PER_BATCH: 12,
		_loadingMore: false,
		_scrollHandler: null,
		init: function() {
			this.bindEvents();
			this.initConditionsRepeater();
			this.setupNativeCPTList();
		},
		/**
		* Setup native CPT list table interception
		* Intercept WP's native "Add New" button and inject "Templates" button
		*/
		setupNativeCPTList: function() {
			var self = this;
			$(document).on("click", ".wrap .page-title-action:not(.jltma-cpt-btn)", function(e) {
				e.preventDefault();
				self.openModal(e);
			});
			var $addNewBtn = $(".wrap .page-title-action").first();
			if ($addNewBtn.length) {
				$addNewBtn.text("Add New Popup");
				if (!$(".jltma-popup-templates").length) $addNewBtn.after("<a href=\"#\" class=\"jltma-cpt-btn jltma-cpt-btn-secondary jltma-popup-templates\"><svg width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect x=\"3\" y=\"3\" width=\"7\" height=\"7\"/><rect x=\"14\" y=\"3\" width=\"7\" height=\"7\"/><rect x=\"3\" y=\"14\" width=\"7\" height=\"7\"/><rect x=\"14\" y=\"14\" width=\"7\" height=\"7\"/></svg> " + (jltmaPopupAdmin.strings.templates_btn || "Templates") + "</a><a href=\"https://www.youtube.com/watch?v=NYJlA5E7H2Y\" target=\"_blank\" class=\"jltma-cpt-btn jltma-cpt-btn-youtube\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"#ff0000\"><path d=\"M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.546 12 3.546 12 3.546s-7.505 0-9.377.504A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.504 9.376.504 9.376.504s7.505 0 9.377-.504a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z\"/></svg> Video Tutorial</a>");
			}
			$(document).on("click", ".wp-list-table .row-title", function(e) {
				var $row = $(e.currentTarget).closest("tr");
				var popupId = self.getPopupIdFromRow($row);
				if (popupId) {
					e.preventDefault();
					$(e.currentTarget).data("popup-id", popupId);
					self.openModal(e);
				}
			});
		},
		/**
		* Extract popup ID from a native WP list table row
		*/
		getPopupIdFromRow: function($row) {
			var popupId = $row.data("popup-id");
			if (popupId) return popupId;
			var rowId = $row.attr("id");
			if (rowId && rowId.indexOf("post-") === 0) return parseInt(rowId.replace("post-", ""), 10);
			return 0;
		},
		bindEvents: function() {
			$(document).on("click", ".jltma-add-new-popup, .jltma-edit-popup", this.openModal.bind(this));
			$(document).on("click", ".jltma-popup-edit-conditions", this.editConditions.bind(this));
			$(document).on("click", ".jltma-pop-close, .close-btn, .jltma-modal-backdrop", this.closeModal.bind(this));
			$(document).on("click", ".jltma-delete-popup", this.deletePopup.bind(this));
			$(document).on("click", ".jltma-popup-templates", this.openTemplatesModal.bind(this));
			$(document).on("click", ".jltma-use-template", this.useTemplate.bind(this));
			$(document).on("input", "#jltma-popup-template-search", this.filterTemplates.bind(this));
			$(document).on("click", ".jltma-template-category-tab", this.filterByCategory.bind(this));
			$(document).on("click", "#jltma-popup-templates-retry", this.loadTemplates.bind(this));
			$(document).on("click", ".jltma-tab-button", this.switchTab.bind(this));
			$(document).on("submit", "#jltma_popup_modal_form", this.savePopup.bind(this));
			$(document).on("click", ".jltma-btn-editor", this.editWithElementor.bind(this));
			$(document).on("click", ".jltma-copy-shortcode", this.copyShortcode.bind(this));
			$(document).on("keydown", this.handleEscKey.bind(this));
			$(document).on("click", "#jltma_popup_templates_modal, #jltma_popup_builder_modal", this.handleOutsideClick.bind(this));
			$(document).on("click", ".jltma-pro-condition-upgrade", function(e) {
				e.preventDefault();
				$(".jltma-upgrade-popup").fadeIn(200);
			});
		},
		openModal: function(e) {
			e.preventDefault();
			var popupId = $(e.currentTarget).data("popup-id") || 0;
			if (!popupId) {
				var $row = $(e.currentTarget).closest("tr");
				popupId = this.getPopupIdFromRow($row);
			}
			var modal = $("#jltma_popup_builder_modal");
			modal.addClass("loading");
			modal.addClass("show");
			if (popupId > 0) this.loadPopupData(popupId);
			else {
				this.resetForm();
				modal.removeClass("loading");
			}
			modal.find("form").attr("data-popup-id", popupId);
		},
		editConditions: function(e) {
			e.preventDefault();
			var popupId = $(e.currentTarget).data("popup-id");
			$(e.currentTarget).data("popup-id", popupId);
			this.openModal(e);
			setTimeout(function() {
				$(".jltma-tab-button[data-tab=\"conditions\"]").trigger("click");
			}, 100);
		},
		closeModal: function(e) {
			if ($(e.target).hasClass("jltma-modal-backdrop") || $(e.target).hasClass("close-btn") || $(e.target).closest(".jltma-pop-close").length) {
				$("#jltma_popup_builder_modal").removeClass("show");
				$("#jltma_popup_templates_modal").removeClass("show");
				this.teardownInfiniteScroll();
				e.preventDefault();
			}
		},
		handleEscKey: function(e) {
			if (e.key === "Escape" || e.keyCode === 27) {
				if ($("#jltma_popup_templates_modal").hasClass("show")) {
					if ($(".jltma-import-progress-overlay").is(":visible")) return;
					$("#jltma_popup_templates_modal").removeClass("show");
					this.teardownInfiniteScroll();
				} else if ($("#jltma_popup_builder_modal").hasClass("show")) $("#jltma_popup_builder_modal").removeClass("show");
			}
		},
		handleOutsideClick: function(e) {
			var $target = $(e.target);
			if ($target.attr("id") === "jltma_popup_templates_modal" || $target.attr("id") === "jltma_popup_builder_modal" || $target.hasClass("jltma-modal-backdrop")) {
				if ($(".jltma-import-progress-overlay").is(":visible")) return;
				$target.closest(".jltma_popup_builder_modal").removeClass("show");
				this.teardownInfiniteScroll();
				e.preventDefault();
			}
		},
		openTemplatesModal: function(e) {
			e.preventDefault();
			$("#jltma_popup_templates_modal").addClass("show");
			if (!this._templatesLoaded) this.loadTemplates();
		},
		loadTemplates: function() {
			var self = this;
			$("#jltma-popup-templates-loading").show();
			$("#jltma-popup-templates-error").hide();
			$("#jltma-popup-templates-empty").hide();
			$("#jltma-popup-templates-grid").hide();
			$.ajax({
				url: jltmaPopupAdmin.ajax_url,
				type: "POST",
				data: {
					action: "jltma_popup_get_templates",
					_nonce: jltmaPopupAdmin.popup_nonce
				},
				success: function(response) {
					$("#jltma-popup-templates-loading").hide();
					if (response.success && response.data) {
						self._templates = response.data.templates || [];
						self._categories = response.data.categories || [];
						self._templatesLoaded = true;
						self._activeCategory = "";
						self._searchTerm = "";
						self._filteredTemplates = self._templates.slice();
						self._visibleCount = 0;
						$("#jltma-popup-template-search").val("");
						self.renderCategoryTabs();
						self.renderTemplatesBatch(true);
					} else $("#jltma-popup-templates-error").show();
				},
				error: function() {
					$("#jltma-popup-templates-loading").hide();
					$("#jltma-popup-templates-error").show();
				}
			});
		},
		renderCategoryTabs: function() {
			var container = $("#jltma-popup-template-categories");
			var cats = this._categories;
			if (!cats || !Array.isArray(cats) || cats.length <= 1) {
				container.empty().hide();
				return;
			}
			var html = "";
			var strings = jltmaPopupAdmin.strings || {};
			html += "<button type=\"button\" class=\"jltma-template-category-tab active\" data-category=\"\">" + (strings.all_categories || "All") + "</button>";
			for (var i = 0; i < cats.length; i++) {
				var cat = cats[i];
				var catName = cat.name || cat.title || cat;
				var catSlug = cat.slug || catName.toLowerCase().replace(/\s+/g, "-");
				html += "<button type=\"button\" class=\"jltma-template-category-tab\" data-category=\"" + catSlug + "\">" + catName + "</button>";
			}
			container.html(html).show();
		},
		/**
		* Build HTML for a single template card
		*/
		buildTemplateCardHTML: function(t) {
			var strings = jltmaPopupAdmin.strings || {};
			var templateId = t.template_id || t.id || "";
			var title = t.title || "Untitled";
			var thumbnail = t.thumbnail || "";
			var preview = t.preview_url || t.url || "";
			var categories = "";
			if (t.categories && Array.isArray(t.categories)) categories = t.categories.join(",");
			else if (t.category) categories = t.category;
			var html = "<div class=\"jltma-template-item\" data-template-id=\"" + templateId + "\" data-categories=\"" + categories + "\">";
			html += "<div class=\"jltma-template-preview\">";
			html += "<div class=\"jltma-template-thumbnail\">";
			if (thumbnail) {
				html += "<img src=\"" + thumbnail + "\" alt=\"" + title + "\" loading=\"lazy\">";
				html += "<div class=\"scroll-icon\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100%\" height=\"100%\" viewBox=\"0 0 66 124\" fill-rule=\"nonzero\" stroke-linejoin=\"round\" stroke-miterlimit=\"2\" fill=\"currentColor\" stroke=\"currentColor\" stroke-width=\"2\"><path d=\"M7 49v25.6c0 14 11.5 25.6 25.6 25.6s25.6-11.5 25.6-25.6V49c0-14-11.5-25.6-25.6-25.6S7 35 7 49zm25.6-20a20 20 0 0 1 19.9 20v25.6a20 20 0 0 1-19.9 19.9 20 20 0 0 1-19.9-19.9V49a20 20 0 0 1 19.9-19.9zm0 24.3c1.6 0 2.8-1.3 2.8-2.8V42c0-1.6-1.3-2.8-2.8-2.8s-2.8 1.3-2.8 2.8v8.5c0 1.6 1.3 2.8 2.8 2.8zm-2 68.7c.6.6 1.3 1 2 1s1.4-.3 2-1l7-7c1-1 1-2.8 0-4s-2.8-1-4 0l-5 5-5-5c-1-1-2.8-1-4 0s-1 2.8 0 4l7 7zm0-120.5l-7 7c-1 1-1 2.8 0 4 .6.6 1.3 1 2 1s1.4-.3 2-1l5-5 5 5c.6.6 1.3 1 2 1s1.4-.3 2-1c1-1 1-2.8 0-4l-7-7c-1-1-2.8-1-4 0z\"></path></svg></div>";
			} else html += "<div class=\"jltma-template-placeholder\"><span class=\"dashicons dashicons-layout\"></span></div>";
			html += "</div>";
			html += "<div class=\"jltma-template-info\">";
			html += "<h4>" + title;
			if (t.pro || t.is_pro) html += " <span class=\"jltma-template-badge jltma-badge-pro\">PRO</span>";
			else html += " <span class=\"jltma-template-badge jltma-badge-free\">FREE</span>";
			html += "</h4>";
			html += "</div>";
			html += "</div>";
			html += "<div class=\"jltma-template-actions\">";
			if (!isPremium && (t.pro || t.is_pro)) html += "<button type=\"button\" class=\"jltma-use-template jltma-pro-locked\" disabled><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect x=\"3\" y=\"11\" width=\"18\" height=\"11\" rx=\"2\" ry=\"2\"/><path d=\"M7 11V7a5 5 0 0 1 10 0v4\"/></svg><span>" + (strings.upgrade || "Upgrade") + "</span></button>";
			else html += "<button type=\"button\" class=\"jltma-use-template\" data-template-id=\"" + templateId + "\" data-template-title=\"" + title + "\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"12\" y1=\"8\" x2=\"12\" y2=\"16\"/><line x1=\"8\" y1=\"12\" x2=\"16\" y2=\"12\"/></svg><span>" + (strings.use_template || "Import") + "</span></button>";
			if (preview) html += "<a href=\"" + preview + "\" target=\"_blank\" class=\"jltma-preview-template\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z\"/><circle cx=\"12\" cy=\"12\" r=\"3\"/></svg><span>" + (strings.preview || "Preview") + "</span></a>";
			html += "</div>";
			html += "</div>";
			return html;
		},
		/**
		* Initialize hover-scroll on newly added thumbnail elements
		*/
		initThumbnailScroll: function($items) {
			$items.find(".jltma-template-thumbnail").each(function() {
				var $thumb = $(this);
				var $img = $thumb.find("img");
				if (!$img.length) return;
				var initScroll = function() {
					var imgHeight = $img[0].naturalHeight ? $img[0].offsetHeight || $img[0].naturalHeight : 0;
					var maxTranslate = Math.max(0, imgHeight - 200);
					$img.css("--translate-y", -maxTranslate + "px");
				};
				if ($img[0].complete) initScroll();
				else $img.on("load", initScroll);
				$thumb.on("mouseenter", function() {
					initScroll();
					$img.addClass("image-hovering");
				}).on("mouseleave", function() {
					$img.removeClass("image-hovering");
				});
			});
		},
		/**
		* Render a batch of templates (first batch replaces, subsequent append)
		*/
		renderTemplatesBatch: function(isReset) {
			var grid = $("#jltma-popup-templates-grid");
			var templates = this._filteredTemplates;
			if (!templates || templates.length === 0) {
				grid.empty().hide();
				$(".jltma-load-more-sentinel").remove();
				$("#jltma-popup-templates-empty").show();
				this.teardownInfiniteScroll();
				return;
			}
			$("#jltma-popup-templates-empty").hide();
			if (isReset) {
				grid.empty();
				this._visibleCount = 0;
			}
			var start = this._visibleCount;
			var end = Math.min(start + this._ITEMS_PER_BATCH, templates.length);
			var html = "";
			for (var i = start; i < end; i++) html += this.buildTemplateCardHTML(templates[i]);
			var $newItems = $(html);
			grid.append($newItems).show();
			this.initThumbnailScroll($newItems);
			this._visibleCount = end;
			this._loadingMore = false;
			var hasMore = this._visibleCount < templates.length;
			$(".jltma-load-more-sentinel").remove();
			if (hasMore) {
				var pluginUrl = (jltmaPopupAdmin.plugin_url || "").replace(/\/$/, "");
				grid.after("<div class=\"jltma-load-more-sentinel\"><div class=\"jltma-load-more-indicator\"><img src=\"" + pluginUrl + "/assets/images/logo.svg\" alt=\"\" class=\"ma-el-loading-logo spinning\"><span>Loading more templates...</span></div></div>");
			}
			this.setupInfiniteScroll();
		},
		/**
		* Set up scroll listener on the templates container for infinite scroll
		*/
		setupInfiniteScroll: function() {
			var self = this;
			this.teardownInfiniteScroll();
			var scrollContainer = document.querySelector("#jltma_popup_templates_modal .jltma-modal-body");
			if (!scrollContainer) return;
			this._scrollHandler = function() {
				if (self._loadingMore) return;
				if (self._visibleCount >= self._filteredTemplates.length) return;
				var scrollTop = scrollContainer.scrollTop;
				var scrollHeight = scrollContainer.scrollHeight;
				if (scrollTop + scrollContainer.clientHeight >= scrollHeight - 300) {
					self._loadingMore = true;
					setTimeout(function() {
						self.renderTemplatesBatch(false);
					}, 150);
				}
			};
			scrollContainer.addEventListener("scroll", this._scrollHandler, { passive: true });
		},
		/**
		* Remove scroll listener
		*/
		teardownInfiniteScroll: function() {
			if (this._scrollHandler) {
				var scrollContainer = document.querySelector("#jltma_popup_templates_modal .jltma-modal-body");
				if (scrollContainer) scrollContainer.removeEventListener("scroll", this._scrollHandler);
				this._scrollHandler = null;
			}
		},
		filterTemplates: function(e) {
			this._searchTerm = $(e.target).val().toLowerCase().trim();
			this.applyFilters();
		},
		filterByCategory: function(e) {
			e.preventDefault();
			var $btn = $(e.currentTarget);
			$(".jltma-template-category-tab").removeClass("active");
			$btn.addClass("active");
			this._activeCategory = $btn.data("category") || "";
			this.applyFilters();
		},
		applyFilters: function() {
			var self = this;
			this._filteredTemplates = this._templates.filter(function(t) {
				if (self._activeCategory) {
					var tCats = [];
					if (t.categories && Array.isArray(t.categories)) tCats = t.categories.map(function(c) {
						return (typeof c === "string" ? c : c.slug || "").toLowerCase();
					});
					else if (t.category) tCats = [t.category.toLowerCase()];
					if (tCats.indexOf(self._activeCategory.toLowerCase()) === -1) return false;
				}
				if (self._searchTerm) {
					if ((t.title || "").toLowerCase().indexOf(self._searchTerm) === -1) return false;
				}
				return true;
			});
			this.renderTemplatesBatch(true);
		},
		useTemplate: function(e) {
			e.preventDefault();
			var $btn = $(e.currentTarget);
			if ($btn.hasClass("jltma-pro-locked")) return;
			var self = this;
			var templateId = $btn.data("template-id");
			var templateTitle = $btn.data("template-title") || "Popup Template";
			var strings = jltmaPopupAdmin.strings || {};
			if (!templateId) {
				JLTMA_Popup_Toaster.error("Invalid template");
				return;
			}
			$(".jltma-use-template").prop("disabled", true);
			self.showImportProgress();
			self.updateImportStep(0, "active");
			$.ajax({
				url: jltmaPopupAdmin.ajax_url,
				type: "POST",
				data: {
					action: "jltma_popup_import_template",
					template_id: templateId,
					title: templateTitle,
					activation: "no",
					_nonce: jltmaPopupAdmin.popup_nonce
				},
				success: function(response) {
					if (response.success && response.data) {
						self.updateImportStep(0, "completed");
						self.updateImportStep(1, "active");
						setTimeout(function() {
							self.updateImportStep(1, "completed");
							self.updateImportStep(2, "active");
						}, 600);
						setTimeout(function() {
							self.updateImportStep(2, "completed");
							self.updateImportStep(3, "active");
						}, 1200);
						setTimeout(function() {
							self.updateImportStep(3, "completed");
							self.updateImportStep(4, "active");
						}, 1800);
						setTimeout(function() {
							self.updateImportStep(4, "completed");
							setTimeout(function() {
								if (response.data.edit_url) window.location.href = response.data.edit_url;
								else location.reload();
							}, 500);
						}, 2400);
					} else {
						self.hideImportProgress();
						$(".jltma-use-template").not(".jltma-pro-locked").prop("disabled", false);
						JLTMA_Popup_Toaster.error(response.data && response.data.message || strings.import_failed || "Failed to import template.");
					}
				},
				error: function() {
					self.hideImportProgress();
					$(".jltma-use-template").not(".jltma-pro-locked").prop("disabled", false);
					JLTMA_Popup_Toaster.error(strings.import_failed || "Failed to import template.");
				}
			});
		},
		showImportProgress: function() {
			$(".jltma-import-progress-overlay").remove();
			$("body").append("<div class=\"jltma-import-progress-overlay\"><div class=\"jltma-import-progress-card\"><h2>We're setting up your popup, please wait...</h2><p class=\"jltma-import-subtitle\">Please wait while we set up your popup. This may take a few moments.</p><div class=\"jltma-import-steps\"><div class=\"jltma-import-step\" data-step=\"0\"><div class=\"jltma-step-icon\"><svg viewBox=\"0 0 24 24\" fill=\"none\"><path d=\"M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z\" fill=\"currentColor\"/></svg></div><div class=\"jltma-step-info\"><strong>Creating page</strong><span class=\"jltma-step-status\">Waiting...</span></div></div><div class=\"jltma-import-step\" data-step=\"1\"><div class=\"jltma-step-icon\"><svg viewBox=\"0 0 24 24\" fill=\"none\"><path d=\"M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z\" fill=\"currentColor\"/></svg></div><div class=\"jltma-step-info\"><strong>Importing content</strong><span class=\"jltma-step-status\">Waiting...</span></div></div><div class=\"jltma-import-step\" data-step=\"2\"><div class=\"jltma-step-icon\"><svg viewBox=\"0 0 24 24\" fill=\"none\"><path d=\"M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z\" fill=\"currentColor\"/></svg></div><div class=\"jltma-step-info\"><strong>Importing images</strong><span class=\"jltma-step-status\">Waiting...</span></div></div><div class=\"jltma-import-step\" data-step=\"3\"><div class=\"jltma-step-icon\"><svg viewBox=\"0 0 24 24\" fill=\"none\"><path d=\"M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z\" fill=\"currentColor\"/></svg></div><div class=\"jltma-step-info\"><strong>Importing widgets</strong><span class=\"jltma-step-status\">Waiting...</span></div></div><div class=\"jltma-import-step\" data-step=\"4\"><div class=\"jltma-step-icon\"><svg viewBox=\"0 0 24 24\" fill=\"none\"><path d=\"M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z\" fill=\"currentColor\"/></svg></div><div class=\"jltma-step-info\"><strong>Finalizing</strong><span class=\"jltma-step-status\">Waiting...</span></div></div></div><div class=\"jltma-import-warning\"><span class=\"dashicons dashicons-info-outline\"></span>Please do not close this window or refresh the page.</div></div></div>");
		},
		updateImportStep: function(stepIndex, status) {
			var $step = $(".jltma-import-step[data-step='" + stepIndex + "']");
			$step.removeClass("waiting active completed").addClass(status);
			if (status === "active") $step.find(".jltma-step-status").text("In progress...");
			else if (status === "completed") $step.find(".jltma-step-status").text("Completed");
		},
		hideImportProgress: function() {
			$(".jltma-import-progress-overlay").fadeOut(300, function() {
				$(this).remove();
			});
		},
		switchTab: function(e) {
			e.preventDefault();
			var targetTab = $(e.currentTarget).data("tab");
			$(".jltma-tab-button").removeClass("active");
			$(".jltma-tab-content").removeClass("active");
			$(e.currentTarget).addClass("active");
			$(".jltma-tab-" + targetTab).addClass("active");
		},
		resetForm: function() {
			$("#jltma_popup_modal_form")[0].reset();
			$(".jltma_popup_modal-title").val("");
			$(".jltma-enable-switcher").prop("checked", false);
			$("#jltma-popup-conditions-repeater").empty();
			this.addConditionRow({
				type: "include",
				rule: "entire_site",
				specific: "",
				posts: []
			});
			$(".jltma-modal-footer").show();
			$("#jltma-popup-pro-condition-notice").remove();
		},
		loadPopupData: function(popupId) {
			var self = this;
			var modal = $("#jltma_popup_builder_modal");
			$.ajax({
				url: jltmaPopupAdmin.ajax_url,
				type: "GET",
				data: {
					action: "jltma_popup_get_data",
					popup_id: popupId,
					_nonce: jltmaPopupAdmin.popup_nonce
				},
				success: function(response) {
					if (response.success && response.data) self.populateForm(response.data);
					modal.removeClass("loading");
				},
				error: function() {
					JLTMA_Popup_Toaster.error("Failed to load popup data");
					modal.removeClass("loading");
				}
			});
		},
		populateForm: function(data) {
			$(".jltma_popup_modal-title").val(data.title || "");
			var activationInput = $(".jltma-enable-switcher");
			if (data.activation === "yes") activationInput.prop("checked", true);
			else activationInput.prop("checked", false);
			activationInput.trigger("change");
			$("#jltma-popup-conditions-repeater").empty();
			let conditionData = [];
			if (data.conditions_data && Array.isArray(data.conditions_data) && data.conditions_data.length > 0) conditionData = data.conditions_data;
			else if (data.jltma_hf_conditions && data.jltma_hf_conditions !== "entire_site") {
				let specificValue = "";
				let posts = [];
				if (data.jltma_hf_conditions === "singular") {
					if (data.jltma_hfc_singular === "selective" && data.jltma_hfc_singular_id) specificValue = data.jltma_hfc_singular_id;
				} else if (data.jltma_hf_conditions === "archive") {
					if (data.jltma_hfc_post_types_id) specificValue = data.jltma_hfc_post_types_id;
				}
				conditionData.push({
					type: "include",
					rule: data.jltma_hf_conditions,
					specific: specificValue,
					posts
				});
			} else conditionData.push({
				type: "include",
				rule: "entire_site",
				specific: "",
				posts: []
			});
			let maxConditionIndex = 0;
			conditionData.forEach(function(condition, index) {
				Popup_Builder_Admin.addConditionRow(condition, index, maxConditionIndex);
			});
			this.checkProConditionLock();
		},
		savePopup: function(e) {
			e.preventDefault();
			var self = this;
			var form = $(e.currentTarget);
			var modal = $("#jltma_popup_builder_modal");
			var popupId = form.attr("data-popup-id") || 0;
			var openEditor = form.attr("data-open-editor") || "0";
			if (!$(".jltma_popup_modal-title").val()) {
				JLTMA_Popup_Toaster.error(jltmaPopupAdmin.strings.popup_name_required);
				return false;
			}
			var conditionSignatures = [];
			var duplicateFound = false;
			$(".jltma-validation-message").remove();
			$("#jltma-popup-conditions-repeater .jltma-condition-row").removeClass("jltma-field-error");
			$("#jltma-popup-conditions-repeater .jltma-condition-row").each(function() {
				var $row = $(this);
				var rowType = $row.find(".jltma-condition-type").val() || "";
				var rowRule = $row.find(".jltma-condition-rule").val() || "";
				var rowSpecific = $row.find(".jltma-condition-specific-select").val() || "";
				var rowPosts = $row.find(".jltma-condition-sub-select").val() || [];
				var signature = [
					rowType,
					rowRule,
					rowSpecific,
					[].concat(rowPosts).join(",")
				].join("|");
				if (conditionSignatures.indexOf(signature) !== -1) {
					duplicateFound = true;
					$row.addClass("jltma-field-error");
				}
				conditionSignatures.push(signature);
			});
			if (duplicateFound) {
				var duplicateMessage = jltmaPopupAdmin.strings.duplicate_condition || "Duplicate condition found. Each condition must be unique.";
				$(".jltma-conditions-section").prepend("<div class=\"jltma-validation-message jltma-validation-message--error\">" + duplicateMessage + "</div>");
				$(".jltma-tab-button[data-tab=\"conditions\"]").trigger("click");
				return false;
			}
			modal.addClass("loading");
			var formData = form.serialize() + "&popup_id=" + popupId;
			$.ajax({
				url: jltmaPopupAdmin.ajax_url,
				type: "POST",
				data: formData + "&action=jltma_popup_save_data&_nonce=" + jltmaPopupAdmin.popup_nonce,
				success: function(response) {
					if (response.success) {
						JLTMA_Popup_Toaster.success(jltmaPopupAdmin.strings.saved);
						modal.removeClass("show");
						modal.removeClass("loading");
						if (openEditor === "1" && response.data.edit_url) window.location.href = response.data.edit_url;
						else if (popupId == 0) location.reload();
						else self.updateTableRow(response.data);
					} else {
						JLTMA_Popup_Toaster.error(response.data.message || jltmaPopupAdmin.strings.error);
						modal.removeClass("loading");
					}
				},
				error: function() {
					JLTMA_Popup_Toaster.error(jltmaPopupAdmin.strings.error);
					modal.removeClass("loading");
				}
			});
		},
		editWithElementor: function(e) {
			e.preventDefault();
			e.stopPropagation();
			var form = $("#jltma_popup_modal_form");
			var popupId = form.attr("data-popup-id") || 0;
			var editorUrl = form.attr("data-editor-url") || jltmaPopupAdmin.admin_url;
			if (popupId && popupId != 0) {
				window.location.href = editorUrl + "?post=" + popupId + "&action=elementor";
				return false;
			}
			form.attr("data-open-editor", "1");
			form.submit();
			return false;
		},
		deletePopup: function(e) {
			e.preventDefault();
			if (!confirm(jltmaPopupAdmin.strings.confirm_delete)) return;
			var popupId = $(e.currentTarget).data("popup-id");
			$.ajax({
				url: jltmaPopupAdmin.ajax_url,
				type: "POST",
				data: {
					action: "jltma_popup_delete",
					popup_id: popupId,
					_nonce: jltmaPopupAdmin.popup_nonce
				},
				success: function(response) {
					if (response.success) {
						JLTMA_Popup_Toaster.success("Popup deleted successfully");
						$("#post-" + popupId).fadeOut(function() {
							$(this).remove();
						});
					} else JLTMA_Popup_Toaster.error(response.data.message || "Failed to delete popup");
				},
				error: function() {
					JLTMA_Popup_Toaster.error("An error occurred");
				}
			});
		},
		copyShortcode: function(e) {
			e.preventDefault();
			var shortcode = $(e.currentTarget).data("shortcode");
			var button = $(e.currentTarget);
			var originalIcon = button.find(".dashicons");
			if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(shortcode).then(function() {
				originalIcon.removeClass("dashicons-clipboard").addClass("dashicons-yes-alt");
				button.css("background", "#46b450");
				JLTMA_Popup_Toaster.success("Shortcode copied to clipboard!", 2e3);
				setTimeout(function() {
					originalIcon.removeClass("dashicons-yes-alt").addClass("dashicons-clipboard");
					button.css("background", "");
				}, 2e3);
			}).catch(function() {
				Popup_Builder_Admin.copyToClipboardFallback(shortcode, button, originalIcon);
			});
			else Popup_Builder_Admin.copyToClipboardFallback(shortcode, button, originalIcon);
		},
		copyToClipboardFallback: function(text, button, originalIcon) {
			var textarea = $("<textarea>").css({
				position: "fixed",
				top: "0",
				left: "0",
				opacity: "0",
				pointerEvents: "none"
			}).val(text);
			$("body").append(textarea);
			textarea[0].select();
			try {
				if (document.execCommand("copy")) {
					originalIcon.removeClass("dashicons-clipboard").addClass("dashicons-yes-alt");
					button.css("background", "#46b450");
					JLTMA_Popup_Toaster.success("Shortcode copied to clipboard!", 2e3);
					setTimeout(function() {
						originalIcon.removeClass("dashicons-yes-alt").addClass("dashicons-clipboard");
						button.css("background", "");
					}, 2e3);
				} else JLTMA_Popup_Toaster.error("Failed to copy shortcode. Please copy manually.", 3e3);
			} catch (err) {
				JLTMA_Popup_Toaster.error("Copy not supported. Please copy manually.", 3e3);
			}
			textarea.remove();
		},
		updateTableRow: function(data) {
			var row = $("#post-" + data.id);
			if (row.length > 0) {
				row.find(".row-title").text(data.title);
				row.find("a.row-title").text(data.title);
				row.find(".post_title").text(data.title);
				if (data.activation) {
					var statusSpan = row.find(".jltma-popup-status");
					if (data.activation === "yes") statusSpan.removeClass("jltma-popup-status-inactive").addClass("jltma-popup-status-active").text("Active");
					else statusSpan.removeClass("jltma-popup-status-active").addClass("jltma-popup-status-inactive").text("Inactive");
				}
			}
		},
		initConditionsRepeater: function() {
			var self = this;
			window.popupConditionIndex = window.popupConditionIndex || 1;
			var isPremium = typeof jltmaPopupAdmin !== "undefined" && jltmaPopupAdmin.is_premium;
			$(document).on("click", "#jltma-popup-add-condition", function(e) {
				if (!isPremium) {
					e.preventDefault();
					$(".jltma-upgrade-popup").fadeIn(200);
					return;
				}
				self.addConditionRow();
			});
			$(document).on("click", ".jltma-remove-condition", function(e) {
				if (!isPremium) {
					e.preventDefault();
					$(".jltma-upgrade-popup").fadeIn(200);
					return;
				}
				self.removeConditionRow($(this));
			});
			$(document).on("change", ".jltma-condition-rule", function() {
				self.handleConditionRuleChange($(this));
				self.checkProConditionLock();
			});
			$(document).on("change", ".jltma-condition-type", function() {
				self.checkProConditionLock();
			});
			$(document).on("change", ".jltma-condition-specific-select", function() {
				self.handleSpecificSelectChange($(this));
			});
		},
		/**
		* Check if any condition has a Pro option selected and lock/unlock footer accordingly
		*/
		checkProConditionLock: function() {
			if (isPremium) return;
			var hasProCondition = false;
			$(".jltma-condition-row").each(function() {
				var typeSelect = $(this).find(".jltma-condition-type");
				var ruleSelect = $(this).find(".jltma-condition-rule");
				if (typeSelect.find("option:selected").data("pro") || ruleSelect.find("option:selected").data("pro")) {
					hasProCondition = true;
					return false;
				}
			});
			var $footer = $(".jltma-modal-footer");
			if (hasProCondition) {
				$footer.hide();
				if (!$("#jltma-popup-pro-condition-notice").length) $(".jltma-conditions-section").append("<div id=\"jltma-popup-pro-condition-notice\" class=\"jltma-pro-type-notice\"><span class=\"jltma-badge-pro\">Pro</span> <span>This condition requires Pro. <a href=\"#\" class=\"jltma-pro-condition-upgrade\">Upgrade Now</a></span></div>");
			} else {
				$footer.show();
				$("#jltma-popup-pro-condition-notice").remove();
			}
		},
		addConditionRow: function(condition, index, maxConditionIndex = 0) {
			var repeater = $("#jltma-popup-conditions-repeater");
			var conditionIndex = index !== void 0 ? index : window.popupConditionIndex++;
			var isPremium = typeof jltmaPopupAdmin !== "undefined" && jltmaPopupAdmin.is_premium;
			var woocommerceOptions = "";
			if (typeof jltmaPopupAdmin !== "undefined" && jltmaPopupAdmin.woocommerce_active) woocommerceOptions = `
                    <option value="product">Product</option>
                    <option value="product_archive">Product Archive</option>
                `;
			var conditionTypeOptions = "";
			if (typeof jltmaPopupAdmin !== "undefined" && jltmaPopupAdmin.condition_type_options) jltmaPopupAdmin.condition_type_options.forEach(function(opt) {
				var selected = condition && condition.type === opt.value ? "selected" : "";
				var proAttr = opt.pro ? "data-pro=\"true\"" : "";
				conditionTypeOptions += `<option value="${opt.value}" ${selected} ${proAttr}>${opt.label}</option>`;
			});
			else conditionTypeOptions = `<option value="include" ${condition && condition.type === "include" ? "selected" : ""}>Include</option>`;
			var conditionRuleOptions = "";
			if (typeof jltmaPopupAdmin !== "undefined" && jltmaPopupAdmin.condition_rule_options) jltmaPopupAdmin.condition_rule_options.forEach(function(opt) {
				var selected = condition && condition.rule === opt.value ? "selected" : "";
				var proAttr = opt.pro ? "data-pro=\"true\"" : "";
				conditionRuleOptions += `<option value="${opt.value}" ${selected} ${proAttr}>${opt.label}</option>`;
			});
			else conditionRuleOptions = `<option value="entire_site" ${condition && condition.rule === "entire_site" ? "selected" : ""}>Entire Site</option>`;
			var newRow = `
                <div class="jltma-condition-row" data-index="${conditionIndex}">
                    <div class="jltma-condition-controls">
                        <div class="jltma-condition-field">
                            <select name="jltma_condition_type[]" class="jltma-condition-select jltma-condition-type">
                                ${conditionTypeOptions}
                            </select>
                        </div>
                        <div class="jltma-condition-field">
                            <select name="jltma_condition_rule[]" class="jltma-condition-select jltma-condition-rule">
                                ${conditionRuleOptions}
                                ${woocommerceOptions}
                            </select>
                        </div>
                        <div class="jltma-condition-field jltma-condition-specific-field" style="${condition && (condition.rule === "singular" || condition.rule === "archive") ? "display: block;" : "display: none;"}">
                            <select name="jltma_condition_specific[]" class="jltma-condition-select jltma-condition-specific-select">
                                <option value="">All</option>
                            </select>
                        </div>
                        <button type="button" class="jltma-remove-condition${!isPremium ? " jltma-pro-locked" : ""}" title="${!isPremium ? "Pro Feature" : "Remove Condition"}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            ${!isPremium ? "<span class=\"jltma-lock-badge\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"10\" height=\"10\" viewBox=\"0 0 24 24\" fill=\"currentColor\"><path d=\"M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z\"/></svg></span>" : ""}
                        </button>
                    </div>
                </div>
            `;
			repeater.append(newRow);
			const addedRow = repeater.find(".jltma-condition-row").last();
			const specificSelect = addedRow.find(".jltma-condition-specific-select");
			if ((condition === null || condition === void 0 ? void 0 : condition.rule) === "singular") {
				Popup_Builder_Admin.loadPostTypes(specificSelect);
				if (condition.specific) {
					setTimeout(function() {
						specificSelect.val(condition.specific);
					}, 200);
					if (condition.posts && condition.posts.length > 0) setTimeout(function() {
						specificSelect.trigger("change");
						addedRow.data("selected-posts", condition.posts);
						setTimeout(function() {
							Popup_Builder_Admin.populatePostSelection(addedRow, condition.posts);
						}, 800);
					}, 300);
				}
			} else if ((condition === null || condition === void 0 ? void 0 : condition.rule) === "archive") {
				Popup_Builder_Admin.loadArchiveTypes(specificSelect);
				if (condition.specific) setTimeout(function() {
					specificSelect.val(condition.specific);
				}, 200);
			}
			maxConditionIndex = Math.max(maxConditionIndex, index + 1);
			repeater.show();
		},
		removeConditionRow: function(button) {
			var row = button.closest(".jltma-condition-row");
			var repeater = $("#jltma-popup-conditions-repeater");
			row.remove();
			if (repeater.find(".jltma-condition-row").length === 0) repeater.hide();
		},
		handleConditionRuleChange: function(select) {
			var row = select.closest(".jltma-condition-row");
			var specificField = row.find(".jltma-condition-specific-field");
			var specificSelect = row.find(".jltma-condition-specific-select");
			var value = select.val();
			var isPremium = typeof jltmaPopupAdmin !== "undefined" && jltmaPopupAdmin.is_premium;
			var isProOption = select.find("option:selected").data("pro");
			row.find(".jltma-condition-sub-select").each(function() {
				if ($(this).hasClass("select2-hidden-accessible")) $(this).select2("destroy");
			});
			row.find(".jltma-condition-search-input").remove();
			row.find(".jltma-condition-sub-select").remove();
			specificSelect.empty().append("<option value=\"\">All</option>");
			if (!isPremium && isProOption) {
				specificField.hide();
				return;
			}
			if (value === "singular") {
				specificField.show();
				this.loadPostTypes(specificSelect);
			} else if (value === "archive") {
				specificField.show();
				this.loadArchiveTypes(specificSelect);
			} else specificField.hide();
		},
		handleSpecificSelectChange: function(select) {
			const row = $(select).closest(".jltma-condition-row");
			const ruleSelect = row.find(".jltma-condition-rule");
			const value = $(select).val();
			const selectElement = $(select);
			row.find(".jltma-condition-sub-select").each(function() {
				if ($(this).hasClass("select2-hidden-accessible")) $(this).select2("destroy");
			});
			row.find(".jltma-condition-search-input").remove();
			row.find(".jltma-condition-sub-select").remove();
			if (ruleSelect.val() === "singular" && value && value !== "") {
				const postTypeLabel = selectElement.find("option:selected").text();
				const postSelect = $(`
                  <select name="jltma_condition_posts[${$("#jltma-popup-conditions-repeater .jltma-condition-row").index(row)}][]"
                    id="${"post-select-" + Date.now() + "-" + Math.random().toString(36).substr(2, 9)}"
                    class="jltma-form-control jltma-condition-sub-select"
                    data-post-type="${value}"
                    multiple>
                  </select>
                `);
				row.find(".jltma-remove-condition").before(postSelect);
				setTimeout(function() {
					postSelect.select2({
						ajax: {
							url: window.masteraddons.resturl + "select2/singular_list",
							type: "get",
							dataType: "json",
							delay: 250,
							headers: { "X-WP-Nonce": window.masteraddons.rest_nonce },
							data: function(params) {
								return {
									s: params.term || "",
									post_type: value
								};
							},
							processResults: function(data, params) {
								const results = [];
								if (!params.term || params.term.length === 0) results.push({
									id: "",
									text: `All ${postTypeLabel}`
								});
								if (data && Array.isArray(data)) data.forEach((item) => {
									results.push({
										id: item.id,
										text: item.text
									});
								});
								return { results };
							},
							error: function(xhr, status, error) {}
						},
						cache: true,
						placeholder: `All ${postTypeLabel}`,
						dropdownParent: $("#jltma_popup_modal_body"),
						allowClear: true,
						multiple: true,
						minimumInputLength: 0,
						escapeMarkup: function(markup) {
							return markup;
						},
						language: {
							inputTooShort: function() {
								return `Select specific ${postTypeLabel.toLowerCase()} or leave empty for all`;
							},
							noResults: function() {
								return `No ${postTypeLabel.toLowerCase()} found`;
							},
							searching: function() {
								return `Searching ${postTypeLabel.toLowerCase()}...`;
							}
						}
					});
					const savedPosts = row.data("selected-posts");
					if (savedPosts && savedPosts.length > 0) setTimeout(function() {
						Popup_Builder_Admin.populatePostSelection(row, savedPosts);
					}, 300);
				}, 100);
			}
		},
		populatePostSelection: function(row, selectedPosts) {
			try {
				const postSelect = row.find(".jltma-condition-sub-select");
				if (postSelect.length && postSelect.hasClass("select2-hidden-accessible")) {
					let postsArray = Array.isArray(selectedPosts) ? selectedPosts : [];
					postsArray = postsArray.map((id) => parseInt(id, 10)).filter((id) => !isNaN(id));
					if (postsArray.length > 0) {
						const postType = postSelect.data("post-type");
						$.ajax({
							url: window.masteraddons.resturl + "select2/singular_list",
							type: "get",
							dataType: "json",
							headers: { "X-WP-Nonce": $("#jltma_popup_modal_form").attr("data-nonce") },
							data: {
								s: "",
								post_type: postType,
								ids: postsArray.join(",")
							},
							success: function(data) {
								if (data && Array.isArray(data)) {
									data.forEach(function(item) {
										if (postsArray.includes(parseInt(item.id))) {
											const existingOption = postSelect.find(`option[value="${item.id}"]`);
											if (existingOption.length === 0) {
												const option = new Option(item.text, item.id, true, true);
												postSelect.append(option);
											} else existingOption.prop("selected", true);
										}
									});
									postSelect.val(postsArray).trigger("change");
								}
							},
							error: function(xhr, status, error) {
								postSelect.val(postsArray).trigger("change");
							}
						});
					}
				}
			} catch (error) {
				console.warn("Error populating post selection:", error);
			}
		},
		loadPostTypes: function(selectElement) {
			try {
				$.ajax({
					url: window.masteraddons.resturl + "ma-template/post-types",
					type: "GET",
					headers: { "X-WP-Nonce": $("#jltma_popup_modal_form").attr("data-nonce") },
					success: function(postTypes) {
						if (postTypes && Array.isArray(postTypes)) postTypes.forEach(function(postType) {
							selectElement.append(`<option value="${postType.value}">${postType.label}</option>`);
						});
					},
					error: function() {
						const fallbackTypes = [{
							value: "post",
							label: "Post"
						}, {
							value: "page",
							label: "Page"
						}];
						if (typeof masteraddons !== "undefined" && masteraddons.woocommerce_active) fallbackTypes.push({
							value: "product",
							label: "Product"
						});
						fallbackTypes.forEach(function(postType) {
							selectElement.append(`<option value="${postType.value}">${postType.label}</option>`);
						});
					}
				});
			} catch (e) {
				console.error("Error loading post types:", e);
			}
		},
		loadArchiveTypes: function(selectElement) {
			try {
				$.ajax({
					url: window.masteraddons.resturl + "ma-template/archive-types",
					type: "GET",
					headers: { "X-WP-Nonce": $("#jltma_popup_modal_form").attr("data-nonce") },
					success: function(archiveTypes) {
						if (archiveTypes && Array.isArray(archiveTypes)) archiveTypes.forEach(function(archiveType) {
							selectElement.append(`<option value="${archiveType.value}">${archiveType.label}</option>`);
						});
					},
					error: function() {
						const fallbackTypes = [
							{
								value: "author",
								label: "Author Archive"
							},
							{
								value: "date",
								label: "Date Archive"
							},
							{
								value: "category",
								label: "Category Archive"
							},
							{
								value: "tag",
								label: "Tag Archive"
							}
						];
						if (typeof masteraddons !== "undefined" && masteraddons.woocommerce_active) {
							fallbackTypes.push({
								value: "product_cat",
								label: "Product Category"
							});
							fallbackTypes.push({
								value: "product_tag",
								label: "Product Tag"
							});
						}
						fallbackTypes.forEach(function(archiveType) {
							selectElement.append(`<option value="${archiveType.value}">${archiveType.label}</option>`);
						});
					}
				});
			} catch (e) {
				console.error("Error loading archive types:", e);
			}
		}
	};
	jQuery(document).ready(function($) {
		Popup_Builder_Admin.init();
	});
})(jQuery);
//#endregion
})();
