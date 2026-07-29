(function(){
(function($, window, document, undefined) {
	$(window).on("elementor:init", function() {
		$(".elementor-editor-active").addClass("master-addons");
		if (typeof elementorPro == "undefined") elementor.hooks.addFilter("editor/style/styleText", function(css, context) {
			if (!context) return;
			var model = context.model, customCSS = model.get("settings").get("custom_css");
			var selector = ".elementor-element.elementor-element-" + model.get("id");
			if ("document" === model.get("elType")) selector = elementor.config.document.settings.cssWrapperSelector;
			if (customCSS) css += customCSS.replace(/selector/g, selector);
			return css;
		});
		/*!
		* ================== Visual Select Controller ===================
		**/
		var JltmaControlVisualSelectItemView = elementor.modules.controls.BaseData.extend({
			onReady: function() {
				this.ui.select.jltmaVisualSelect();
			},
			onBeforeDestroy: function() {
				this.ui.select.jltmaVisualSelect("destroy");
			}
		});
		elementor.addControlView("jltma-visual-select", JltmaControlVisualSelectItemView);
		function jltmaOnGlobalOpenEditorForTranistions(panel, model, view) {
			view.listenTo(model.get("settings"), "change", function(changedModel) {
				if ("" !== model.getSetting("ma_el_animation_name") && !view.$el.hasClass("jltma-animated")) {
					view.render();
					view.$el.addClass("jltma-animated");
					view.$el.addClass("jltma-animated-once");
				}
				for (settingName in changedModel.changed) if (changedModel.changed.hasOwnProperty(settingName)) {
					if (settingName !== "ma_el_animation_name" && -1 !== settingName.indexOf("ma_el_animation_")) {
						view.$el.removeClass(model.getSetting("ma_el_animation_name"));
						setTimeout(function() {
							view.$el.addClass(model.getSetting("ma_el_animation_name"));
						}, model.getSetting("ma_el_animation_delay") || 300);
					}
				}
			}, view);
		}
		elementor.hooks.addAction("panel/open_editor/section", jltmaOnGlobalOpenEditorForTranistions);
		elementor.hooks.addAction("panel/open_editor/column", jltmaOnGlobalOpenEditorForTranistions);
		elementor.hooks.addAction("panel/open_editor/widget", jltmaOnGlobalOpenEditorForTranistions);
		var JLTMA_Choose_Text = elementor.modules.controls.Choose.extend({ applySavedValue: function applySavedValue() {
			var currentValue = this.getControlValue();
			if (currentValue || _.isString(currentValue)) this.ui.inputs.filter("[value=\"".concat(currentValue, "\"]")).prop("checked", true);
			else this.ui.inputs.filter(":checked").prop("checked", false);
		} });
		elementor.hooks.addAction("panel/open_editor/widget", JLTMA_Choose_Text);
		elementor.addControlView("jltma-choose-text", JLTMA_Choose_Text);
		var JLTMA_ControlQuery = elementor.modules.controls.Select2.extend({
			cache: null,
			isTitlesReceived: false,
			getSelect2Placeholder: function getSelect2Placeholder() {
				return {
					id: "",
					text: "All"
				};
			},
			getSelect2DefaultOptions: function getSelect2DefaultOptions() {
				var self = this;
				return jQuery.extend(elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply(this, arguments), {
					ajax: {
						transport: function transport(params, success, failure) {
							var data = {
								q: params.data.q,
								query_type: self.model.get("query_type"),
								object_type: self.model.get("object_type")
							};
							return elementorCommon.ajax.addRequest("jltma_query_control_filter_autocomplete", {
								data,
								success,
								error: failure
							});
						},
						data: function data(params) {
							return {
								q: params.term,
								page: params.page
							};
						},
						cache: true
					},
					escapeMarkup: function escapeMarkup(markup) {
						return markup;
					},
					minimumInputLength: 1
				});
			},
			getValueTitles: function getValueTitles() {
				var self = this, ids = this.getControlValue(), queryType = this.model.get("query_type");
				objectType = this.model.get("object_type");
				if (!ids || !queryType) return;
				if (!_.isArray(ids)) ids = [ids];
				elementorCommon.ajax.loadObjects({
					action: "jltma_query_control_value_titles",
					ids,
					data: {
						query_type: queryType,
						object_type: objectType,
						unique_id: "" + self.cid + queryType
					},
					success: function success(data) {
						self.isTitlesReceived = true;
						self.model.set("options", data);
						self.render();
					},
					before: function before() {
						self.addSpinner();
					}
				});
			},
			addSpinner: function addSpinner() {
				this.ui.select.prop("disabled", true);
				this.$el.find(".elementor-control-title").after("<span class=\"elementor-control-spinner ee-control-spinner\">&nbsp;<i class=\"fa fa-spinner fa-spin\"></i>&nbsp;</span>");
			},
			onReady: function onReady() {
				setTimeout(elementor.modules.controls.Select2.prototype.onReady.bind(this));
				if (!this.isTitlesReceived) this.getValueTitles();
			}
		});
		elementor.addControlView("jltma_query", JLTMA_ControlQuery);
		/**
		* Disable Pro-Restricted Switcher Controls
		* Handles switcher controls with .jltma-pro-disabled class in description
		* Uses MutationObserver to watch for DOM changes
		*/
		function jltmaHandleProRestrictedSwitchers() {
			var processedControls = /* @__PURE__ */ new Set();
			function disableProSwitchers() {
				jQuery(".elementor-control-type-switcher").each(function() {
					var $control = jQuery(this);
					var controlId = $control.attr("data-control-name") || $control.index();
					if (processedControls.has(controlId)) return;
					if ($control.find(".elementor-control-field-description").find(".jltma-pro-disabled").length > 0) {
						var $switcherInput = $control.find("input[type=\"checkbox\"]");
						var $switcherLabel = $control.find(".elementor-switch");
						$control.addClass("jltma-switcher-disabled");
						$switcherLabel.addClass("jltma-switcher-disabled");
						$switcherInput.prop("disabled", true);
						$switcherLabel.off("click.jltma-pro").on("click.jltma-pro", function(e) {
							e.preventDefault();
							e.stopPropagation();
							jQuery(".jltma-upgrade-popup").fadeIn(200);
							return false;
						});
						$control.find("label").off("click.jltma-pro").on("click.jltma-pro", function(e) {
							if ($switcherLabel.hasClass("jltma-switcher-disabled")) {
								e.preventDefault();
								e.stopPropagation();
								jQuery(".jltma-upgrade-popup").fadeIn(200);
								return false;
							}
						});
						processedControls.add(controlId);
					}
				});
			}
			function setupObserver() {
				var targetNode = document.querySelector("#elementor-panel-content-wrapper");
				if (!targetNode) {
					setTimeout(setupObserver, 500);
					return;
				}
				new MutationObserver(function(mutations) {
					var shouldProcess = false;
					mutations.forEach(function(mutation) {
						if (mutation.addedNodes.length > 0) shouldProcess = true;
					});
					if (shouldProcess) disableProSwitchers();
				}).observe(targetNode, {
					childList: true,
					subtree: true,
					attributes: false
				});
				disableProSwitchers();
			}
			function startIntervalCheck() {
				setInterval(function() {
					disableProSwitchers();
				}, 1e3);
			}
			jQuery(window).on("elementor:init", function() {
				setTimeout(function() {
					setupObserver();
					startIntervalCheck();
				}, 1e3);
			});
			setTimeout(function() {
				setupObserver();
				startIntervalCheck();
			}, 2e3);
		}
		jltmaHandleProRestrictedSwitchers();
		/**
		* Pro Widget Promotion Dialog Handler
		* Customizes the Elementor promotion dialog for Master Addons pro widgets
		*/
		function jltmaProWidgetPromotionHandler() {
			if (typeof parent.document === "undefined") return false;
			parent.document.addEventListener("mousedown", function(e) {
				var widgets = parent.document.querySelectorAll(".elementor-element--promotion");
				if (widgets.length > 0) {
					for (var i = 0; i < widgets.length; i++) if (widgets[i].contains(e.target)) {
						var dialog = parent.document.querySelector("#elementor-element--promotion__dialog");
						var icon = widgets[i].querySelector(".icon > i");
						if (!dialog || !icon) break;
						if (icon.classList.toString().indexOf("jltma") >= 0) {
							var defaultButton = dialog.querySelector(".dialog-buttons-action");
							if (defaultButton) defaultButton.style.display = "none";
							e.stopImmediatePropagation();
							if (dialog.querySelector(".jltma-dialog-buttons-action") === null) {
								var button = document.createElement("a");
								var buttonText = document.createTextNode("Upgrade Master Addons");
								button.setAttribute("href", "https://master-addons.com/pricing/");
								button.setAttribute("target", "_blank");
								button.classList.add("dialog-button", "dialog-action", "dialog-buttons-action", "elementor-button", "go-pro", "elementor-button-success", "jltma-dialog-buttons-action");
								button.style.display = "block";
								button.style.textAlign = "center";
								button.appendChild(buttonText);
								if (defaultButton) defaultButton.insertAdjacentHTML("afterend", button.outerHTML);
							} else {
								const btnWrapper = document.querySelector(".dialog-buttons-buttons-wrapper > button");
								btnWrapper.style.display = "none";
								dialog.querySelector(".jltma-dialog-buttons-action").style.display = "block";
							}
						} else {
							var defaultBtn = dialog.querySelector(".dialog-buttons-action:not(.jltma-dialog-buttons-action)");
							if (defaultBtn) defaultBtn.style.display = "";
							var jltmaBtn = dialog.querySelector(".jltma-dialog-buttons-action");
							if (jltmaBtn !== null) jltmaBtn.style.display = "none";
						}
						break;
					}
				}
			});
		}
		jltmaProWidgetPromotionHandler();
	});
})(jQuery, window, document);
//#endregion
})();
