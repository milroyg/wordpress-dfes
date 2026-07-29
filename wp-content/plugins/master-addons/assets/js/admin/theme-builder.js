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
//#region dev/js/admin/theme-builder.js
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
	var TEMPLATE_TYPE_CONDITIONS = {
		header: {
			rule: "entire_site",
			specific: ""
		},
		footer: {
			rule: "entire_site",
			specific: ""
		},
		comment: {
			rule: "singular",
			specific: ""
		},
		single: {
			rule: "singular",
			specific: "post"
		},
		archive: {
			rule: "archive",
			specific: ""
		},
		category: {
			rule: "archive",
			specific: "category"
		},
		tag: {
			rule: "archive",
			specific: "post_tag"
		},
		author: {
			rule: "archive",
			specific: "author"
		},
		date: {
			rule: "archive",
			specific: "date"
		},
		page_single: {
			rule: "singular",
			specific: "page"
		},
		search: {
			rule: "search",
			specific: ""
		},
		"404": {
			rule: "404",
			specific: ""
		},
		product: {
			rule: "singular",
			specific: "product"
		},
		product_archive: {
			rule: "archive",
			specific: "product_cat"
		}
	};
	var isPremium = typeof JLTMACORE !== "undefined" && JLTMACORE.pro_conditions;
	var proSuffix = isPremium ? "" : " (Pro)";
	var freeTemplateTypes = [
		"header",
		"footer",
		"comment"
	];
	function buildConditionRowHTML(index) {
		var woocommerceOptions = "";
		if (typeof masteraddons !== "undefined" && masteraddons.woocommerce_active) woocommerceOptions = "                  <option value=\"product\">Product" + proSuffix + "</option>                  <option value=\"product_archive\">Product Archive" + proSuffix + "</option>                ";
		var lockBadge = isPremium ? "" : "<span class=\"jltma-lock-badge\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"8\" height=\"8\" viewBox=\"0 0 24 24\" fill=\"currentColor\"><path d=\"M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM9 8V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9z\"/></svg></span>";
		var removeTitle = isPremium ? "Remove Condition" : "Pro Feature";
		var removeClass = isPremium ? "jltma-remove-condition" : "jltma-remove-condition jltma-pro-locked";
		return "                <div class=\"jltma-condition-row\" data-index=\"" + index + "\">                  <div class=\"jltma-condition-controls\">                    <div class=\"jltma-condition-field\">                      <select name=\"jltma_condition_type[]\" class=\"jltma-condition-select jltma-condition-type\">                        <option value=\"include\">Include</option>                        <option value=\"exclude\">Exclude" + proSuffix + "</option>                      </select>                    </div>                    <div class=\"jltma-condition-field\">                      <select name=\"jltma_condition_rule[]\" class=\"jltma-condition-select jltma-condition-rule\">                        <option value=\"entire_site\">Entire Site</option>                        <option value=\"front_page\">Front Page" + proSuffix + "</option>                        <option value=\"singular\">Singular" + proSuffix + "</option>                        <option value=\"archive\">Archive" + proSuffix + "</option>                        <option value=\"search\">Search" + proSuffix + "</option>                        <option value=\"404\">404 Page" + proSuffix + "</option>                        " + woocommerceOptions + "                      </select>                    </div>                    <div class=\"jltma-condition-field jltma-condition-specific-field\" style=\"display: none;\">                      <select name=\"jltma_condition_specific[]\" class=\"jltma-condition-select jltma-condition-specific-select\">                        <option value=\"\">All</option>                      </select>                    </div>                    <button type=\"button\" class=\"" + removeClass + "\" title=\"" + removeTitle + "\">                      <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M3 6h18\"/><path d=\"M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6\"/><path d=\"M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2\"/><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"/><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"/></svg>                      " + lockBadge + "                    </button>                  </div>                </div>              ";
	}
	var Master_Header_Footer = {
		Url_Param_Replace: function(url, paramName, paramValue) {
			if (paramValue == null) paramValue = "";
			var pattern = new RegExp("\\b(" + paramName + "=).*?(&|#|$)");
			if (url.search(pattern) >= 0) return url.replace(pattern, "$1" + paramValue + "$2");
			url = url.replace(/[?#]$/, "");
			return url + (url.indexOf("?") > 0 ? "&" : "?") + paramName + "=" + paramValue;
		},
		JLTMA_Template_Editor: function(data) {
			try {
				(function($) {
					$(".jltma_hf_modal-title").val(data.title);
					var typeSelect = $(".jltma_hfc_type");
					typeSelect.val(data.type || "header");
					typeSelect.trigger("change");
					var activation_input = $(".jltma-enable-switcher");
					if (data.activation == "yes") activation_input.prop("checked", true);
					else activation_input.prop("checked", false);
					activation_input.trigger("change");
					$("#jltma-conditions-repeater").empty();
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
						let woocommerceOptions = "";
						if (typeof masteraddons !== "undefined" && masteraddons.woocommerce_active) woocommerceOptions = `
                  <option value="product">Product${proSuffix}</option>
                  <option value="product_archive">Product Archive${proSuffix}</option>
                `;
						const _removeClass = isPremium ? "jltma-remove-condition" : "jltma-remove-condition jltma-pro-locked";
						const _removeTitle = isPremium ? "Remove Condition" : "Pro Feature";
						const _lockBadge = isPremium ? "" : "<span class=\"jltma-lock-badge\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"8\" height=\"8\" viewBox=\"0 0 24 24\" fill=\"currentColor\"><path d=\"M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM9 8V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9z\"/></svg></span>";
						const newRow = `
                <div class="jltma-condition-row" data-index="${index}">
                  <div class="jltma-condition-controls">
                    <div class="jltma-condition-field">
                      <select name="jltma_condition_type[]" class="jltma-condition-select jltma-condition-type">
                        <option value="include" ${condition.type === "include" ? "selected" : ""}>Include</option>
                        <option value="exclude" ${condition.type === "exclude" ? "selected" : ""}>Exclude${proSuffix}</option>
                      </select>
                    </div>
                    <div class="jltma-condition-field">
                      <select name="jltma_condition_rule[]" class="jltma-condition-select jltma-condition-rule">
                        <option value="entire_site" ${condition.rule === "entire_site" ? "selected" : ""}>Entire Site</option>
                        <option value="front_page" ${condition.rule === "front_page" ? "selected" : ""}>Front Page${proSuffix}</option>
                        <option value="singular" ${condition.rule === "singular" ? "selected" : ""}>Singular${proSuffix}</option>
                        <option value="archive" ${condition.rule === "archive" ? "selected" : ""}>Archive${proSuffix}</option>
                        <option value="search" ${condition.rule === "search" ? "selected" : ""}>Search${proSuffix}</option>
                        <option value="404" ${condition.rule === "404" ? "selected" : ""}>404 Page${proSuffix}</option>
                        ${woocommerceOptions}
                      </select>
                    </div>
                    <div class="jltma-condition-field jltma-condition-specific-field" style="${condition.rule === "singular" || condition.rule === "archive" ? "display: block;" : "display: none;"}">
                      <select name="jltma_condition_specific[]" class="jltma-condition-select jltma-condition-specific-select">
                        <option value="">All</option>
                      </select>
                    </div>
                    <button type="button" class="${_removeClass}" title="${_removeTitle}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                      ${_lockBadge}
                    </button>
                  </div>
                </div>
              `;
						$("#jltma-conditions-repeater").append(newRow);
						const addedRow = $("#jltma-conditions-repeater").find(".jltma-condition-row").last();
						const specificSelect = addedRow.find(".jltma-condition-specific-select");
						if (condition.rule === "singular") {
							Master_Header_Footer.Load_Post_Types(specificSelect);
							if (condition.specific) {
								setTimeout(function() {
									specificSelect.val(condition.specific);
								}, 200);
								if (condition.posts && condition.posts.length > 0) setTimeout(function() {
									specificSelect.trigger("change");
									addedRow.data("selected-posts", condition.posts);
									setTimeout(function() {
										Master_Header_Footer.populatePostSelection(addedRow, condition.posts);
									}, 800);
								}, 300);
							}
						} else if (condition.rule === "archive") {
							Master_Header_Footer.Load_Archive_Types(specificSelect);
							if (condition.specific) setTimeout(function() {
								specificSelect.val(condition.specific);
							}, 200);
						}
						maxConditionIndex = Math.max(maxConditionIndex, index + 1);
					});
					if (typeof window.masterHeaderFooterConditionIndex !== "undefined") window.masterHeaderFooterConditionIndex = maxConditionIndex + 1;
					$(".jltma_hf_modal-jltma_hf_conditions").val(data.jltma_hf_conditions);
					$(".jltma_hf_modal-jltma_hfc_singular").val(data.jltma_hfc_singular);
					$(".jltma_hf_modal-jltma_hfc_singular_id").val(data.jltma_hfc_singular_id);
					$(".jltma_hf_conditions").val(data.jltma_hf_conditions);
					$(".jltma_hf_modal-jltma_hfc_post_types_id").val(data.jltma_hfc_post_types_id);
					$(".jltma-enable-switcher, .jltma_hfc_type").trigger("change");
				})(jQuery);
			} catch (e) {}
		},
		Modal_Singular_List: function() {
			$(".jltma_hf_modal-jltma_hfc_singular_id").select2({
				ajax: {
					url: window.masteraddons.resturl + "select2/singular_list",
					type: "get",
					dataType: "json",
					headers: { "X-WP-Nonce": window.masteraddons.rest_nonce },
					data: function(params) {
						return { s: params.term };
					}
				},
				cache: true,
				placeholder: "Search for posts/pages...",
				dropdownParent: $("#jltma_hf_modal_body")
			});
			this.Setup_Condition_Search();
		},
		Setup_Condition_Search: function() {},
		Modal_Submit: function() {
			try {
				(function($) {
					$(document).on("keydown", ".jltma_hf_modal-title", function(e) {
						if (e.key === "Enter" || e.keyCode === 13) {
							e.preventDefault();
							return false;
						}
					});
					$("#jltma_hf_modal_form").on("submit", function(e) {
						e.preventDefault();
						$(".jltma-validation-message").remove();
						var validationResult = Master_Header_Footer.Validate_Conditions();
						if (!validationResult.valid) {
							var validationHtml = "<div class=\"jltma-validation-message jltma-validation-message--error\">" + validationResult.message + "</div>";
							$(".jltma-tab-conditions .jltma-conditions-section").prepend(validationHtml);
							if (!$(".jltma-tab-conditions").hasClass("active")) {
								$(".jltma-tab-button").removeClass("active");
								$(".jltma-tab-content").removeClass("active");
								$(".jltma-tab-button[data-tab=\"conditions\"]").addClass("active");
								$(".jltma-tab-conditions").addClass("active");
							}
							$(".jltma-tab-conditions").scrollTop(0);
							return false;
						}
						var modal = $("#jltma_hf_modal");
						modal.addClass("loading");
						var form_data = $(this).serialize(), id = $(this).attr("data-jltma-hf-id"), jltma_hfc_nonce = $(this).attr("data-nonce"), open_editor = $(this).attr("data-open-editor"), admin_url = $(this).attr("data-editor-url");
						$.ajax({
							url: window.masteraddons.resturl + "ma-template/update/" + id,
							data: form_data,
							type: "get",
							dataType: "json",
							headers: { "X-WP-Nonce": jltma_hfc_nonce },
							success: function(output) {
								setTimeout(function() {
									modal.removeClass("loading");
								}, 1500);
								JLTMA_Toaster.success("Template saved successfully!");
								var row = $("#post-" + output.data.id);
								if (row.length > 0) {
									row.find(".column-type").html(output.data.type_html);
									row.find(".column-condition").html(output.data.cond_text);
									row.find(".row-title").html(output.data.title).attr("aria-label", output.data.title);
								}
								$("#jltma_hf_modal").removeClass("jltma-modal-open");
								if (open_editor == "1") window.location.href = admin_url + "?post=" + output.data.id + "&action=elementor";
								else if (id == "0") location.reload();
							},
							error: function(xhr, status, error) {
								modal.removeClass("loading");
								var errorMessage = "An error occurred while saving the template.";
								if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
								else if (xhr.responseText) try {
									var response = JSON.parse(xhr.responseText);
									if (response.message) errorMessage = response.message;
								} catch (e) {}
								$(".jltma-validation-message").remove();
								var errorHtml = "<div class=\"jltma-validation-message jltma-validation-message--error\">" + errorMessage + "</div>";
								$(".jltma-tab-conditions .jltma-conditions-section").prepend(errorHtml);
								if (!$(".jltma-tab-conditions").hasClass("active")) {
									$(".jltma-tab-button").removeClass("active");
									$(".jltma-tab-content").removeClass("active");
									$(".jltma-tab-button[data-tab=\"conditions\"]").addClass("active");
									$(".jltma-tab-conditions").addClass("active");
								}
								console.error("Template save error:", error);
								JLTMA_Toaster.error("Failed to save template. Please try again.");
							}
						});
					});
				})(jQuery);
			} catch (e) {}
		},
		Open_Editor: function() {
			try {
				(function($) {
					$(".jltma-btn-editor").on("click", function(e) {
						var form = $("#jltma_hf_modal_form");
						form.attr("data-open-editor", "1");
						form.trigger("submit");
					});
				})(jQuery);
			} catch (e) {}
		},
		Choose_Template_Singular_Condition: function() {
			try {
				(function($) {
					$(".jltma_hf_modal-jltma_hfc_singular").on("change", function() {
						var jltma_hfc_singular = $(this).val();
						var inputs = $(".jltma_hf_modal-jltma_hfc_singular_id-container");
						if (jltma_hfc_singular == "selective") inputs.show();
						else inputs.hide();
					});
				})(jQuery);
			} catch (e) {}
		},
		Choose_Template_Post_Types_Condition: function() {
			try {
				(function($) {
					$(".jltma_hf_modal-jltma_hf_conditions").on("change", function() {
						var jltma_hfc_singular = $(this).val();
						var inputs = $(".jltma_hf_modal-jltma_hfc_post_types_id-container");
						if (jltma_hfc_singular == "post_types") inputs.show();
						else inputs.hide();
					});
				})(jQuery);
			} catch (e) {}
		},
		Choose_Template_Conditions: function() {
			try {
				(function($) {
					$(".jltma_hf_modal-jltma_hf_conditions").unbind().on("change", function() {
						var jltma_hf_conditions = $(this).val(), inputs = $(".jltma_hf_modal-jltma_hfc_singular-container");
						if (jltma_hf_conditions == "singular") inputs.show();
						else if (jltma_hf_conditions == "jltma-hfc-single-pro" || jltma_hf_conditions == "post_types_pro" || jltma_hf_conditions == "jltma-hfc-archive-pro" || jltma_hf_conditions == "search_pro" || jltma_hf_conditions == "404_pro" || jltma_hf_conditions == "product_pro" || jltma_hf_conditions == "product_archive_pro") {
							$(".jltma-hfc-popup-upgade").remove();
							$(".jltma_hf_modal-jltma_hf_conditions").after("<div class=\"jltma-hfc-popup-upgade\"> " + masteraddons.upgrade_pro + "</div>");
						} else {
							inputs.hide();
							$(".jltma-hfc-popup-upgade").hide();
						}
					});
				})(jQuery);
			} catch (e) {}
		},
		Choose_Template_Type: function() {
			try {
				(function($) {
					function toggleProTypeLock(type) {
						var isProType = !isPremium && freeTemplateTypes.indexOf(type) === -1;
						var $activation = $(".jtlma-mega-switcher");
						var $footer = $(".jltma-modal-footer");
						if (isProType) {
							$activation.addClass("jltma-pro-type-locked");
							$activation.find(".jltma-enable-switcher").prop("disabled", true).prop("checked", false);
							$footer.hide();
							var noticeHTML = "<span class=\"jltma-badge-pro\">Pro</span> <span>This template type requires Pro. <a href=\"#\" class=\"jltma-pro-type-upgrade\">Upgrade Now</a></span>";
							if (!$("#jltma-pro-type-notice-general").length) $activation.after("<div id=\"jltma-pro-type-notice-general\" class=\"jltma-pro-type-notice\">" + noticeHTML + "</div>");
							if (!$("#jltma-pro-type-notice-conditions").length) $(".jltma-conditions-section").append("<div id=\"jltma-pro-type-notice-conditions\" class=\"jltma-pro-type-notice\">" + noticeHTML + "</div>");
						} else {
							$activation.removeClass("jltma-pro-type-locked");
							$activation.find(".jltma-enable-switcher").prop("disabled", false);
							$footer.show();
							$("#jltma-pro-type-notice-general, #jltma-pro-type-notice-conditions").remove();
						}
					}
					$(".jltma_hfc_type").on("change", function() {
						var type = $(this).val(), label = $(".jltma-hfc-hide-item-label"), inputs = $(".jltma_hf_options_container");
						if (type == "section" || type == "comment") {
							inputs.hide();
							label.hide();
							$(".jltma-modal-footer").show();
							$(".jltma-tab-button[data-tab=\"conditions\"]").show();
						} else {
							label.show();
							inputs.show();
							$(".jltma-modal-footer").show();
							$(".jltma-tab-button[data-tab=\"conditions\"]").show();
						}
						toggleProTypeLock(type);
						Master_Header_Footer.Auto_Populate_Condition(type);
					});
					$(document).on("click", ".jltma-pro-type-upgrade", function(e) {
						e.preventDefault();
						$(".jltma-upgrade-popup").fadeIn(200);
					});
				})(jQuery);
			} catch (e) {}
		},
		Auto_Populate_Condition: function(templateType) {
			var mapping = TEMPLATE_TYPE_CONDITIONS[templateType];
			if (!mapping) return;
			var repeater = jQuery("#jltma-conditions-repeater");
			var modalId = jQuery("#jltma_hf_modal form").attr("data-jltma-hf-id");
			if (modalId && parseInt(modalId, 10) > 0) return;
			window.masterHeaderFooterConditionIndex = window.masterHeaderFooterConditionIndex || 1;
			repeater.empty();
			var newRow = buildConditionRowHTML(window.masterHeaderFooterConditionIndex);
			repeater.append(newRow);
			window.masterHeaderFooterConditionIndex++;
			var rowElement = repeater.find(".jltma-condition-row").last();
			rowElement.find(".jltma-condition-type").val("include");
			rowElement.find(".jltma-condition-rule").val(mapping.rule);
			var specificField = rowElement.find(".jltma-condition-specific-field");
			var specificSelect = rowElement.find(".jltma-condition-specific-select");
			if (mapping.rule === "singular") {
				specificField.show();
				specificSelect.empty().append("<option value=\"\">All</option>");
				Master_Header_Footer.Load_Post_Types(specificSelect);
				if (mapping.specific) setTimeout(function() {
					specificSelect.val(mapping.specific);
				}, 200);
			} else if (mapping.rule === "archive") {
				specificField.show();
				specificSelect.empty().append("<option value=\"\">All</option>");
				Master_Header_Footer.Load_Archive_Types(specificSelect);
				if (mapping.specific) setTimeout(function() {
					specificSelect.val(mapping.specific);
				}, 200);
			} else specificField.hide();
			repeater.show();
		},
		Conditions_Repeater: function() {
			try {
				(function($) {
					window.masterHeaderFooterConditionIndex = window.masterHeaderFooterConditionIndex || 1;
					$(document).on("click", "#jltma-add-condition", function(e) {
						if (!isPremium) {
							e.preventDefault();
							$(".jltma-upgrade-popup").fadeIn(200);
							return;
						}
						const repeater = $("#jltma-conditions-repeater");
						const newRow = buildConditionRowHTML(window.masterHeaderFooterConditionIndex);
						repeater.append(newRow);
						window.masterHeaderFooterConditionIndex++;
						repeater.show();
						repeater.find(".jltma-condition-row").last().find(".jltma-condition-rule").trigger("change");
					});
					$(document).on("click", ".jltma-remove-condition", function(e) {
						if (!isPremium) {
							e.preventDefault();
							$(".jltma-upgrade-popup").fadeIn(200);
							return;
						}
						const row = $(this).closest(".jltma-condition-row");
						const repeater = $("#jltma-conditions-repeater");
						row.remove();
						if (repeater.find(".jltma-condition-row").length === 0) repeater.hide();
					});
					$(document).on("change", ".jltma-condition-rule", function() {
						const row = $(this).closest(".jltma-condition-row");
						const specificField = row.find(".jltma-condition-specific-field");
						const specificSelect = row.find(".jltma-condition-specific-select");
						const value = $(this).val();
						$(".jltma-validation-message").remove();
						row.find(".jltma-condition-type, .jltma-condition-rule, .jltma-condition-specific-select").removeClass("jltma-field-error");
						row.find(".jltma-condition-sub-select").each(function() {
							if ($(this).hasClass("select2-hidden-accessible")) $(this).select2("destroy");
						});
						row.find(".jltma-condition-search-input").remove();
						row.find(".jltma-condition-sub-select").remove();
						row.find(".select2-hidden-accessible").each(function() {
							$(this).select2("destroy");
						});
						specificSelect.empty().append("<option value=\"\">All</option>");
						if (!isPremium && value !== "entire_site") specificField.hide();
						else if (value === "singular") {
							specificField.show();
							Master_Header_Footer.Load_Post_Types(specificSelect);
						} else if (value === "archive") {
							specificField.show();
							Master_Header_Footer.Load_Archive_Types(specificSelect);
						} else specificField.hide();
					});
					$(document).on("change", ".jltma-condition-specific-select", function() {
						const row = $(this).closest(".jltma-condition-row");
						const ruleSelect = row.find(".jltma-condition-rule");
						const value = $(this).val();
						const selectElement = $(this);
						row.find(".jltma-condition-sub-select").each(function() {
							if ($(this).hasClass("select2-hidden-accessible")) $(this).select2("destroy");
						});
						row.find(".jltma-condition-search-input").remove();
						row.find(".jltma-condition-sub-select").remove();
						if (ruleSelect.val() === "singular" && value && value !== "") {
							const postTypeLabel = selectElement.find("option:selected").text();
							const postSelect = $(`
                  <select name="jltma_condition_posts[${$("#jltma-conditions-repeater .jltma-condition-row").index(row)}][]" 
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
									dropdownParent: $("#jltma_hf_modal_body"),
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
									Master_Header_Footer.populatePostSelection(row, savedPosts);
								}, 300);
							}, 100);
						}
					});
					$(document).on("change", ".jltma-condition-type, .jltma-condition-specific-select", function() {
						$(".jltma-validation-message").remove();
						$(this).removeClass("jltma-field-error");
					});
				})(jQuery);
			} catch (e) {}
		},
		Load_Post_Types: function(selectElement) {
			try {
				$.ajax({
					url: window.masteraddons.resturl + "ma-template/post-types",
					type: "GET",
					headers: { "X-WP-Nonce": window.masteraddons.rest_nonce },
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
		Load_Archive_Types: function(selectElement) {
			try {
				$.ajax({
					url: window.masteraddons.resturl + "ma-template/archive-types",
					type: "GET",
					headers: { "X-WP-Nonce": window.masteraddons.rest_nonce },
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
		},
		Validate_Conditions: function() {
			try {
				var hasInclude = false;
				var hasValidConditions = false;
				var conditionRows = $("#jltma-conditions-repeater .jltma-condition-row");
				var errors = [];
				if (conditionRows.length === 0) return {
					valid: true,
					message: "No conditions set - template will not display anywhere until conditions are added."
				};
				conditionRows.each(function(index) {
					var row = $(this);
					var type = row.find(".jltma-condition-type").val();
					var rule = row.find(".jltma-condition-rule").val();
					var specific = row.find(".jltma-condition-specific-select").val();
					var specificVisible = row.find(".jltma-condition-specific-select").is(":visible");
					row.find(".jltma-condition-type, .jltma-condition-rule, .jltma-condition-specific-select").removeClass("jltma-field-error");
					if (!type) {
						row.find(".jltma-condition-type").addClass("jltma-field-error");
						errors.push("Condition type is required for row " + (index + 1));
					}
					if (!rule) {
						row.find(".jltma-condition-rule").addClass("jltma-field-error");
						errors.push("Condition rule is required for row " + (index + 1));
					}
					if (specificVisible && rule && (rule === "singular" || rule === "archive") && !specific) {
						row.find(".jltma-condition-specific-select").addClass("jltma-field-error");
						errors.push("Specific selection is required for " + rule + " condition in row " + (index + 1));
					}
					if (type && rule) {
						hasValidConditions = true;
						if (type === "include") hasInclude = true;
					}
				});
				if (errors.length > 0) return {
					valid: false,
					message: errors[0]
				};
				if (!hasValidConditions) return {
					valid: false,
					message: "Please complete all condition fields."
				};
				if (hasValidConditions && !hasInclude) return {
					valid: false,
					message: "At least one \"Include\" condition is required. Templates must include at least one location where they should be displayed."
				};
				var includeEntireSite = false;
				var hasOtherIncludes = false;
				conditionRows.each(function() {
					var type = $(this).find(".jltma-condition-type").val();
					var rule = $(this).find(".jltma-condition-rule").val();
					if (type === "include") if (rule === "entire_site") includeEntireSite = true;
					else hasOtherIncludes = true;
				});
				if (includeEntireSite && hasOtherIncludes) return {
					valid: false,
					message: "You cannot use \"Entire Site\" with other include conditions. \"Entire Site\" includes everything already."
				};
				return { valid: true };
			} catch (e) {
				console.error("Validation error:", e);
				return {
					valid: false,
					message: "Validation error occurred."
				};
			}
		},
		Modal_Add_Edit: function() {
			try {
				(function($) {
					$(document).on("click", ".row-actions .edit a, .page-title-action:not(.jltma-cpt-btn), .column-title .row-title, .jltma-theme-builder-edit-cond", function(e) {
						e.preventDefault();
						var id = 0, modal = $("#jltma_hf_modal"), jltma_hfc_nonce = $("#jltma_hf_modal_form").attr("data-nonce"), parent = $(this).parents(".column-title");
						modal.addClass("loading");
						modal.addClass("jltma-modal-open");
						var $clicked = $(this).closest(".jltma-theme-builder-edit-cond");
						if ($clicked.length > 0) id = $clicked.attr("id");
						else if (parent.length > 0) id = parent.find(".hidden").attr("id").split("_")[1];
						else id = 0;
						if (id > 0) $.ajax({
							url: window.masteraddons.resturl + "ma-template/get/" + id,
							type: "get",
							headers: { "X-WP-Nonce": jltma_hfc_nonce },
							dataType: "json",
							success: function(data) {
								Master_Header_Footer.JLTMA_Template_Editor(data);
								modal.removeClass("loading");
							},
							error: function(xhr, status, error) {
								console.error("Error loading template data:", error);
								modal.removeClass("loading");
								Master_Header_Footer.JLTMA_Template_Editor({
									title: "",
									type: "header",
									jltma_hf_conditions: "entire_site",
									jltma_hfc_singular: "all",
									activation: ""
								});
							}
						});
						else modal.removeClass("loading");
						modal.find("form").attr("data-jltma-hf-id", id);
						let $select = $(".jltma_hfc_type");
						let activeTabTitle = $(".master_type_filter_tab_container.nav-tab-wrapper .nav-tab-active").text().trim();
						if ($select.length && activeTabTitle) $select.find("option").each(function() {
							let optionText = $(this).text().trim();
							if (optionText.toLowerCase() === activeTabTitle.toLowerCase()) {
								if (!$select.val() || $select.val().toLowerCase() !== optionText.toLowerCase()) {
									$(this).prop("selected", true);
									$select.trigger("change");
								}
								return false;
							}
						});
						let $conditionsBtn = $(".jltma-tab-button");
						if (["search", "404 page"].includes(activeTabTitle.toLowerCase())) $conditionsBtn.remove();
						let $ruleSelect = $(".jltma-condition-select.jltma-condition-rule");
						function updateSelectOptions() {
							const activeTabTitle = $(".master_type_filter_tab_container .nav-tab-active").text().trim().toLowerCase();
							let visibleOptions = [];
							switch (activeTabTitle) {
								case "all":
									visibleOptions = ["entire"];
									var data = {
										title: "",
										type: "header",
										jltma_hf_conditions: "entire_site",
										jltma_hfc_singular: "all",
										activation: ""
									};
									Master_Header_Footer.JLTMA_Template_Editor(data);
									break;
								case "header":
									visibleOptions = ["entire"];
									var data = {
										title: "",
										type: "header",
										jltma_hf_conditions: "entire_site",
										jltma_hfc_singular: "all",
										activation: ""
									};
									Master_Header_Footer.JLTMA_Template_Editor(data);
									break;
								case "footer":
									visibleOptions = ["entire"];
									var data = {
										title: "",
										type: "footer",
										jltma_hf_conditions: "entire_site",
										jltma_hfc_singular: "all",
										activation: ""
									};
									Master_Header_Footer.JLTMA_Template_Editor(data);
									break;
								case "single":
									visibleOptions = ["singular"];
									data = {
										title: "",
										type: "single",
										jltma_hf_conditions: "singular",
										jltma_hfc_singular: "all",
										activation: ""
									};
									Master_Header_Footer.JLTMA_Template_Editor(data);
									break;
								case "archive":
									visibleOptions = ["archive"];
									data = {
										title: "",
										type: "archive",
										jltma_hf_conditions: "archive",
										jltma_hfc_singular: "all",
										activation: ""
									};
									Master_Header_Footer.JLTMA_Template_Editor(data);
									break;
								case "comment":
									visibleOptions = ["singular"];
									data = {
										title: "",
										type: "comment",
										jltma_hf_conditions: "singular",
										jltma_hfc_singular: "all",
										activation: ""
									};
									Master_Header_Footer.JLTMA_Template_Editor(data);
									break;
								default:
									visibleOptions = $ruleSelect.find("option").map(function() {
										return $(this).attr("value").toLowerCase();
									}).get();
									break;
							}
							$ruleSelect.find("option").each(function() {
								const optionValue = $(this).attr("value").toLowerCase();
								if (visibleOptions.includes(optionValue)) {
									$(this).show();
									if (!$ruleSelect.val() || !visibleOptions.includes($ruleSelect.val().toLowerCase())) $ruleSelect.val($(this).val());
								} else $(this).hide();
							});
						}
						$(document).ready(function() {
							updateSelectOptions();
						});
					});
					$(".jltma-tab-button").on("click", function(e) {
						e.preventDefault();
						var targetTab = $(this).data("tab");
						$(".jltma-tab-button").removeClass("active");
						$(".jltma-tab-content").removeClass("active");
						$(this).addClass("active");
						$(".jltma-tab-" + targetTab).addClass("active");
					});
				})(jQuery);
			} catch (e) {}
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
							headers: { "X-WP-Nonce": window.masteraddons.rest_nonce },
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
		}
	};
	jQuery(document).ready(function($) {
		"use strict";
		$(document).on("click", ".jltma-pop-close, .close-btn, .jltma-modal-backdrop", function(e) {
			if ($(e.target).hasClass("jltma-modal-backdrop") || $(e.target).hasClass("close-btn") || $(e.target).closest(".jltma-pop-close").length) {
				$("#jltma_hf_modal").removeClass("jltma-modal-open");
				e.preventDefault();
			}
		});
		var $themeAddNewBtn = $(".wrap .page-title-action").first();
		if ($themeAddNewBtn.length) {
			$themeAddNewBtn.text("Add New Template");
			if (!$(".jltma-theme-builder-tutorial").length) $themeAddNewBtn.after("<a href=\"https://www.youtube.com/watch?v=KmlHyER6uEQ\" target=\"_blank\" class=\"jltma-cpt-btn jltma-cpt-btn-youtube jltma-theme-builder-tutorial\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"#ff0000\"><path d=\"M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.546 12 3.546 12 3.546s-7.505 0-9.377.504A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.504 9.376.504 9.376.504s7.505 0 9.377-.504a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z\"/></svg> Video Tutorial</a>");
		}
		$(document).on("click", ".wrap .page-title-action:not(.jltma-cpt-btn)", function(e) {
			$("#jltma_hf_modal").toggleClass("jltma-modal-open");
			e.preventDefault();
		});
		Master_Header_Footer.Modal_Add_Edit();
		Master_Header_Footer.Choose_Template_Type();
		Master_Header_Footer.Choose_Template_Conditions();
		Master_Header_Footer.Choose_Template_Singular_Condition();
		Master_Header_Footer.Choose_Template_Post_Types_Condition();
		Master_Header_Footer.Open_Editor();
		Master_Header_Footer.Modal_Submit();
		Master_Header_Footer.Modal_Singular_List();
		Master_Header_Footer.Conditions_Repeater();
		$(document).on("click", ".jltma-copy-shortcode", function(e) {
			e.preventDefault();
			var shortcode = $(this).data("shortcode");
			var button = $(this);
			var originalIcon = button.find(".dashicons");
			if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(shortcode).then(function() {
				originalIcon.removeClass("dashicons-clipboard").addClass("dashicons-yes-alt");
				button.css("background", "#46b450");
				JLTMA_Toaster.success("Shortcode copied to clipboard!", 2e3);
				setTimeout(function() {
					originalIcon.removeClass("dashicons-yes-alt").addClass("dashicons-clipboard");
					button.css("background", "#f9f9f9");
				}, 2e3);
			}).catch(function() {
				copyToClipboardFallback(shortcode, button, originalIcon);
			});
			else copyToClipboardFallback(shortcode, button, originalIcon);
		});
		function copyToClipboardFallback(text, button, originalIcon) {
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
					JLTMA_Toaster.success("Shortcode copied to clipboard!", 2e3);
					setTimeout(function() {
						originalIcon.removeClass("dashicons-yes-alt").addClass("dashicons-clipboard");
						button.css("background", "#f9f9f9");
					}, 2e3);
				} else JLTMA_Toaster.error("Failed to copy shortcode. Please copy manually.", 3e3);
			} catch (err) {
				JLTMA_Toaster.error("Copy not supported. Please copy manually.", 3e3);
			}
			textarea.remove();
		}
		var tab_container = $(".wp-header-end"), tabs = "", proBadge = isPremium ? "" : " <span class=\"jltma-badge-pro\">Pro</span>", proTypes = [
			"single",
			"archive",
			"search",
			"404",
			"product",
			"product_archive"
		], filter_types = [
			{
				key: "all",
				label: "All"
			},
			{
				key: "header",
				label: "Header"
			},
			{
				key: "footer",
				label: "Footer"
			},
			{
				key: "comment",
				label: "Comment"
			},
			{
				key: "single",
				label: "Single" + proBadge
			},
			{
				key: "archive",
				label: "Archive" + proBadge
			},
			{
				key: "search",
				label: "Search" + proBadge
			},
			{
				key: "404",
				label: "404 Page" + proBadge
			}
		];
		if (typeof masteraddons !== "undefined" && masteraddons.woocommerce_active) {
			filter_types.push({
				key: "product",
				label: "Product" + proBadge
			});
			filter_types.push({
				key: "product_archive",
				label: "Product Archive" + proBadge
			});
		}
		var s = new URL(window.location.href).searchParams.get("master_template_type_filter");
		s = s == null ? "all" : s;
		$.each(filter_types, function(index, item) {
			var url = Master_Header_Footer.Url_Param_Replace(window.location.href, "master_template_type_filter", item.key);
			var jlma_class = s == item.key ? "master_type_filter_active nav-tab-active" : " ";
			var isProTab = !isPremium && proTypes.indexOf(item.key) !== -1;
			tabs += `
                <a href="${isProTab ? "#" : url}" class="${jlma_class} master_type_filter_tab_item nav-tab${isProTab ? " jltma-pro-tab" : ""}"${isProTab ? " data-pro=\"1\"" : ""}>${item.label}</a>
            `;
			tabs += "\n";
		});
		tab_container.after("<div class=\"master_type_filter_tab_container nav-tab-wrapper\">" + tabs + "</div><br/>");
		if (!isPremium) $(document).on("click", ".master_type_filter_tab_item[data-pro]", function(e) {
			e.preventDefault();
			$(".jltma-upgrade-popup").fadeIn(200);
		});
	});
})(jQuery);
//#endregion
})();
