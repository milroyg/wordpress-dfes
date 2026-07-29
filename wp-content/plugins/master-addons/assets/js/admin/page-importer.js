(function(){
//#region dev/js/admin/page-importer.js
/**
* Master Addons - Page Importer
* Adds "Import Pages" button to WordPress Pages list
* Opens Template Library in a modal popup
*/
(function($) {
	"use strict";
	/**
	* Page Importer Class
	*/
	class JLTMA_PageImporter {
		constructor() {
			this.config = window.JLTMA_PAGE_IMPORTER || {};
			this.modal = null;
			this.modalCreated = false;
			this.currentReactRoot = null;
			this.currentType = null;
			this.init();
		}
		/**
		* Initialize
		*/
		init() {
			$(document).ready(() => {
				this.addImportButton();
				this.attachEvents();
			});
		}
		/**
		* Add "MA Import" button with dropdown beside "Add New" button
		*/
		addImportButton() {
			let $addNewButton = $(".wrap .wp-heading-inline + .page-title-action");
			if ($addNewButton.length === 0) $addNewButton = $(".page-title-action").first();
			if ($addNewButton.length === 0) return;
			const $importWrapper = $("<div>", { class: "jltma-import-wrapper" });
			const $importButton = $("<button>", {
				type: "button",
				class: "jltma-import-pages-btn",
				html: `
                    <img src="${this.config.red_logo_url}" alt="Master Addons" class="jltma-import-icon" />
                    <span>MA Import</span>
                    <span class="jltma-dropdown-icon dashicons dashicons-arrow-down-alt2"></span>
                `
			});
			const $dropdown = $("<div>", {
				class: "jltma-import-dropdown",
				html: `
                    <a href="#" class="jltma-dropdown-item" data-action="template-kits">
                        <span class="dashicons dashicons-admin-page"></span>
                        Template Kits
                    </a>
                    <a href="#" class="jltma-dropdown-item" data-action="import-pages">
                        <span class="dashicons dashicons-welcome-add-page"></span>
                        Import Pages
                    </a>
                `
			});
			$importWrapper.append($importButton, $dropdown);
			$importWrapper.insertAfter($addNewButton);
		}
		/**
		* Create modal structure (fullscreen, no custom header)
		*/
		createModal() {
			const modalHTML = `
                <div id="jltma-page-importer-modal" class="jltma-import-modal" style="display: none;">
                    <div class="jltma-import-backdrop"></div>
                    <div class="jltma-import-modal-content">
                        <div id="ma-el-modal-loading" style="display: none;">
                            <div class="elementor-loader-wrapper">
                                <div class="ma-el-logo-container">
                                    <img src="${this.config.logo_url}" alt="Master Addons" class="ma-el-loading-logo">
                                </div>
                                <div class="elementor-loading-title">${this.config.i18n.loading}</div>
                            </div>
                        </div>
                        <div id="jltma-template-library-root" class="jltma-template-page-importer">
                        </div>
                    </div>
                </div>
            `;
			$("body").append(modalHTML);
			this.modal = $("#jltma-page-importer-modal");
		}
		/**
		* Attach event handlers
		*/
		attachEvents() {
			const self = this;
			$(document).on("click", ".jltma-import-pages-btn", function(e) {
				e.preventDefault();
				e.stopPropagation();
				$(this).closest(".jltma-import-wrapper").toggleClass("is-open");
			});
			$(document).on("click", ".jltma-dropdown-item", function(e) {
				e.preventDefault();
				const action = $(this).data("action");
				$(this).closest(".jltma-import-wrapper").removeClass("is-open");
				if (action === "import-pages") self.openModal("pages");
				else if (action === "template-kits") self.openModal("kits");
			});
			$(document).on("click", function(e) {
				if (!$(e.target).closest(".jltma-import-wrapper").length) $(".jltma-import-wrapper").removeClass("is-open");
			});
			$(document).on("click", ".jltma-import-backdrop", function(e) {
				e.preventDefault();
				self.closeModal();
			});
			$(document).on("keydown", function(e) {
				if (e.key === "Escape") {
					if (self.modal && self.modal.is(":visible")) self.closeModal();
					$(".jltma-import-wrapper").removeClass("is-open");
				}
			});
		}
		/**
		* Open modal and create it if needed (lazy loading)
		* @param {string} type - 'pages' or 'kits'
		*/
		openModal(type = "pages") {
			if (!this.modalCreated) {
				this.createModal();
				this.modalCreated = true;
				this.showModal();
				this.mountReactApp(type);
			} else if (this.currentType !== type) {
				this.showModal();
				this.unmountCurrentApp();
				this.showLoading();
				setTimeout(() => {
					this.mountReactApp(type);
				}, 100);
			} else this.showModal();
			this.currentType = type;
		}
		/**
		* Manually mount the appropriate React app
		* @param {string} type - 'pages' or 'kits'
		*/
		mountReactApp(type = "pages") {
			this.showLoading();
			$("#jltma-template-library-root, #jltma-template-kits-app").attr("id", "jltma-template-library-root").empty();
			if (type === "kits") setTimeout(() => {
				this.mountTemplateKitsApp();
			}, 50);
			else {
				window.JLTMA_IS_PAGE_IMPORTER_MODE = true;
				setTimeout(() => {
					if (typeof window.JLTMAMountTemplateLibrary === "function") {
						this.currentReactRoot = window.JLTMAMountTemplateLibrary();
						this.hideLoading();
					} else {
						const event = new CustomEvent("jltma-mount-template-library");
						document.dispatchEvent(event);
						this.hideLoading();
					}
				}, 50);
			}
		}
		/**
		* Mount Template Kits React app
		*/
		mountTemplateKitsApp() {
			const self = this;
			$("#jltma-template-library-root").attr("id", "jltma-template-kits-app").empty();
			window.JLTMA_IS_PAGE_IMPORTER_MODE = true;
			if (typeof window.JLTMAMountTemplateKits === "function") {
				this.currentReactRoot = window.JLTMAMountTemplateKits();
				setTimeout(() => self.hideLoading(), 300);
			} else {
				const script = document.createElement("script");
				script.src = this.config.template_kits_url;
				script.onload = () => {
					if (typeof window.JLTMAMountTemplateKits === "function") {
						self.currentReactRoot = window.JLTMAMountTemplateKits();
						setTimeout(() => self.hideLoading(), 300);
					}
				};
				document.body.appendChild(script);
			}
		}
		/**
		* Unmount current React app
		*/
		unmountCurrentApp() {
			if (this.currentReactRoot && typeof this.currentReactRoot.unmount === "function") try {
				this.currentReactRoot.unmount();
			} catch (e) {
				console.warn("Error unmounting React app:", e);
			}
			this.currentReactRoot = null;
			$("#jltma-template-library-root, #jltma-template-kits-app").empty();
		}
		/**
		* Show loading state
		*/
		showLoading() {
			$("#ma-el-modal-loading").show();
			$("#jltma-template-library-root, #jltma-template-kits-app").hide();
		}
		/**
		* Hide loading state
		*/
		hideLoading() {
			$("#ma-el-modal-loading").hide();
			$("#jltma-template-library-root, #jltma-template-kits-app").show();
			setTimeout(() => {
				this.injectCloseButton();
			}, 200);
		}
		/**
		* Show the modal with animation
		*/
		showModal() {
			this.modal.css("display", "flex").addClass("show");
			$("body").addClass("jltma-modal-open").css("overflow", "hidden");
		}
		/**
		* Inject close button into Template Library header (beside refresh button)
		*/
		injectCloseButton() {
			const self = this;
			const $refreshBtn = $(".template-library-tabs-wrapper .refresh-btn, .template-kit-upload-wrapper .refresh-btn");
			if ($refreshBtn.length > 0 && !$refreshBtn.parent().find(".jltma-modal-close-btn").length) {
				const $closeBtn = $(`
                    <button class="jltma-modal-close-btn" aria-label="Close" title="Close">
                        <span class="eicon-close"></span>
                    </button>
                `);
				$closeBtn.insertAfter($refreshBtn);
				$closeBtn.on("click", function(e) {
					e.preventDefault();
					self.closeModal();
				});
			}
		}
		/**
		* Close modal
		*/
		closeModal() {
			this.modal.removeClass("show");
			$("body").removeClass("jltma-modal-open").css("overflow", "");
			setTimeout(() => {
				this.modal.css("display", "none");
			}, 200);
		}
	}
	new JLTMA_PageImporter();
})(jQuery);
//#endregion
})();
