(function(){
(function($) {
	"use strict";
	var currentDevice = "";
	var getElementSettings = function($element, setting) {
		var elementSettings = {}, modelCID = $element.data("model-cid");
		if (elementorFrontend.isEditMode() && modelCID) {
			var settings = elementorFrontend.config.elements.data[modelCID], type = settings.attributes.widgetType || settings.attributes.elType, settingsKeys = elementorFrontend.config.elements.keys[type];
			if (!settingsKeys) {
				settingsKeys = elementorFrontend.config.elements.keys[type] = [];
				jQuery.each(settings.controls, function(name, control) {
					if (control.frontend_available) settingsKeys.push(name);
				});
			}
			jQuery.each(settings.getActiveControls(), function(controlKey) {
				if (-1 !== settingsKeys.indexOf(controlKey)) elementSettings[controlKey] = settings.attributes[controlKey];
			});
		} else elementSettings = $element.data("settings") || {};
		return getItems(elementSettings, setting);
	};
	var getItems = function(items, itemKey) {
		if (itemKey) {
			var keyStack = itemKey.split("."), currentKey = keyStack.splice(0, 1);
			if (!keyStack.length) return items[currentKey];
			if (!items[currentKey]) return;
			return this.getItems(items[currentKey], keyStack.join("."));
		}
		return items;
	};
	var getUniqueLoopScopeId = function($scope) {
		if ($scope.data("jltma-template-widget-id")) return $scope.data("jltma-template-widget-id");
		return $scope.data("id");
	};
	function jltMAObserveTarget(target, callback) {
		var options = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
		new IntersectionObserver(function(entries, observer) {
			entries.forEach(function(entry) {
				if (entry.isIntersecting) callback(entry);
			});
		}, options).observe(target);
	}
	function stripTags(text) {
		return text.replace(/<\/?[^>]+(>|$)/g, "");
	}
	var Master_Addons = {
		animatedProgressbar: function(id, type, value, strokeColor, trailColor, strokeWidth, strokeTrailWidth) {
			var triggerClass = ".jltma-progress-bar-" + id;
			if ("line" == type) new ldBar(triggerClass, {
				"type": "stroke",
				"path": "M0 10L100 10",
				"aspect-ratio": "none",
				"stroke": strokeColor,
				"stroke-trail": trailColor,
				"stroke-width": strokeWidth,
				"stroke-trail-width": strokeTrailWidth
			}).set(value);
			if ("line-bubble" == type) {
				new ldBar(triggerClass, {
					"type": "stroke",
					"path": "M0 10L100 10",
					"aspect-ratio": "none",
					"stroke": strokeColor,
					"stroke-trail": trailColor,
					"stroke-width": strokeWidth,
					"stroke-trail-width": strokeTrailWidth
				}).set(value);
				$($(".jltma-progress-bar-" + id).find(".ldBar-label")).animate({ left: value + "%" }, 1e3, "swing");
			}
			if ("circle" == type) new ldBar(triggerClass, {
				"type": "stroke",
				"path": "M50 10A40 40 0 0 1 50 90A40 40 0 0 1 50 10",
				"stroke-dir": "normal",
				"stroke": strokeColor,
				"stroke-trail": trailColor,
				"stroke-width": strokeWidth,
				"stroke-trail-width": strokeTrailWidth
			}).set(value);
			if ("fan" == type) new ldBar(triggerClass, {
				"type": "stroke",
				"path": "M10 90A40 40 0 0 1 90 90",
				"stroke": strokeColor,
				"stroke-trail": trailColor,
				"stroke-width": strokeWidth,
				"stroke-trail-width": strokeTrailWidth
			}).set(value);
		},
		MA_Accordion: function($scope, $) {
			var elementSettings = getElementSettings($scope), $accordionHeader = $scope.find(".jltma-accordion-header"), $accordionType = elementSettings.accordion_type, $accordionSpeed = elementSettings.toggle_speed ? elementSettings.toggle_speed : 300;
			$accordionHeader.each(function() {
				if ($(this).hasClass("active-default")) {
					$(this).addClass("show active");
					$(this).next().slideDown($accordionSpeed);
				}
			});
			$accordionHeader.unbind("click");
			$accordionHeader.click(function(e) {
				e.preventDefault();
				var $this = $(this);
				if ($accordionType === "accordion") if ($this.hasClass("show")) {
					$this.removeClass("show active");
					$this.next().slideUp($accordionSpeed);
				} else {
					$this.parent().parent().find(".jltma-accordion-header").removeClass("show active");
					$this.parent().parent().find(".jltma-accordion-tab-content").slideUp($accordionSpeed);
					$this.toggleClass("show active");
					$this.next().slideDown($accordionSpeed);
				}
				else if ($this.hasClass("show")) {
					$this.removeClass("show active");
					$this.next().slideUp($accordionSpeed);
				} else {
					$this.addClass("show active");
					$this.next().slideDown($accordionSpeed);
				}
			});
		},
		MA_Tabs: function($scope, $) {
			try {
				(function($) {
					var $tabsWrapper = $scope.find("[data-tabs]"), $tabEffect = $tabsWrapper.data("tab-effect");
					$tabsWrapper.each(function() {
						var tab = $(this);
						var isContentActive = false;
						tab.find("[data-tab]").each(function() {
							if ($(this).hasClass("active")) {}
						});
						tab.find(".jltma--advance-tab-content").each(function() {
							if ($(this).hasClass("active")) isContentActive = true;
						});
						if (!isContentActive) tab.find(".jltma--advance-tab-content").eq(0).addClass("active");
						if ($tabEffect == "hover") tab.find("[data-tab]").hover(function() {
							var $data_tab_id = $(this).data("tab-id");
							$(this).siblings().removeClass("active");
							$(this).addClass("active");
							$(this).closest("[data-tabs]").find(".jltma--advance-tab-content").removeClass("active");
							$("#" + $data_tab_id).addClass("active");
						});
						else tab.find("[data-tab]").click(function() {
							var $data_tab_id = $(this).data("tab-id");
							$(this).siblings().removeClass("active");
							$(this).addClass("active");
							$(this).closest("[data-tabs]").find(".jltma--advance-tab-content").removeClass("active");
							$("#" + $data_tab_id).addClass("active");
						});
					});
				})(jQuery);
			} catch (e) {}
		},
		MA_ProgressBar: function($scope, $) {
			var id = $scope.data("id"), $progressBarWrapper = $scope.find(".jltma-progress-bar-" + id), type = $progressBarWrapper.data("type"), value = $progressBarWrapper.data("progress-bar-value"), strokeWidth = $progressBarWrapper.data("progress-bar-stroke-width"), strokeTrailWidth = $progressBarWrapper.data("progress-bar-stroke-trail-width"), color = $progressBarWrapper.data("stroke-color"), trailColor = $progressBarWrapper.data("stroke-trail-color");
			$progressBarWrapper.find("svg").remove();
			$progressBarWrapper.find(".ldBar-label").remove();
			$progressBarWrapper.removeClass("ldBar");
			Master_Addons.animatedProgressbar(id, type, value, color, trailColor, strokeWidth, strokeTrailWidth);
		},
		MA_Image_Hotspot: function($scope, $) {
			getElementSettings($scope);
			var $ma_hotspot = $scope.find(".jltma-hotspots-container");
			if (!$ma_hotspot.length) return;
			var $tooltip = $ma_hotspot.find("> .jltma-tooltip-item"), widgetID = $scope.data("id");
			$tooltip.each(function(index) {
				tippy(this, {
					allowHTML: false,
					theme: "jltma-tippy-" + widgetID
				});
			});
		},
		MA_Pricing_Table: function($scope, $) {
			var $jltma_pricing_table = $scope.find(".jltma-price-table-details ul");
			if (!$jltma_pricing_table.length) return;
			var $tooltip = $jltma_pricing_table.find("> .jltma-tooltip-item"), widgetID = $scope.data("id");
			$tooltip.each(function(index) {
				tippy(this, {
					allowHTML: false,
					theme: "jltma-pricing-table-tippy-" + widgetID,
					appendTo: document.body
				});
			});
		},
		JLTMA_Data_Table: function($scope, $) {
			var a = $scope.find(".jltma-data-table-container"), n = a.data("source"), r = a.data("sourcecsv");
			if (1 == a.data("buttons")) var l = "Bfrtip";
			else l = "frtip";
			if ("custom" == n) {
				var i = $scope.find("table thead tr th").length;
				$scope.find("table tbody tr").each(function() {
					if (e(this).find("td").length < i) {
						var t = i - e(this).find("td").length;
						e(this).append(new Array(++t).join("<td></td>"));
					}
				}), $scope.find(".jltma-data-table").DataTable({
					dom: l,
					paging: a.data("paging"),
					pagingType: "numbers",
					pageLength: a.data("pagelength"),
					info: a.data("info"),
					scrollX: !0,
					searching: a.data("searching"),
					ordering: a.data("ordering"),
					buttons: [
						{
							extend: "csvHtml5",
							text: JLTMA_DATA_TABLE.csvHtml5
						},
						{
							extend: "excelHtml5",
							text: JLTMA_DATA_TABLE.excelHtml5
						},
						{
							extend: "pdfHtml5",
							text: JLTMA_DATA_TABLE.pdfHtml5
						},
						{
							extend: "print",
							text: JLTMA_DATA_TABLE.print
						}
					],
					language: {
						lengthMenu: JLTMA_DATA_TABLE.lengthMenu,
						zeroRecords: JLTMA_DATA_TABLE.zeroRecords,
						info: JLTMA_DATA_TABLE.info,
						infoEmpty: JLTMA_DATA_TABLE.infoEmpty,
						infoFiltered: JLTMA_DATA_TABLE.infoFiltered,
						search: "",
						searchPlaceholder: JLTMA_DATA_TABLE.searchPlaceholder,
						processing: JLTMA_DATA_TABLE.processing
					}
				});
			} else if ("csv" == n) ({ init: function(t) {
				var a = (t = t || {}).csv_path || "", n = $scope.element || $("#table-container"), r = $scope.csv_options || {}, l = $scope.datatables_options || {}, i = $scope.custom_formatting || [], s = {};
				$.each(i, function(e, t) {
					var a = t[0];
					s[a] = t[1];
				});
				var d = $("<table class=\"jltma-data-table cell-border\" style=\"width:100%;visibility:hidden;\">");
				n.empty().append(d), $.when($.get(a)).then(function(t) {
					for (var a = e.csv.toArrays(t, r), n = $("<thead></thead>"), i = a[0], o = $("<tr></tr>"), c = 0; c < i.length; c++) o.append($("<th></th>").text(i[c]));
					n.append(o), d.append(n);
					for (var m = $("<tbody></tbody>"), p = 1; p < a.length; p++) for (var _ = $("<tr></tr>"), g = 0; g < a[p].length; g++) {
						var b = $("<td></td>"), f = s[g];
						f ? b.html(f(a[p][g])) : b.text(a[p][g]), _.append(b), m.append(_);
					}
					d.append(m), d.DataTable(l);
				});
			} }).init({
				csv_path: r,
				element: a,
				datatables_options: {
					dom: l,
					paging: a.data("paging"),
					pagingType: "numbers",
					pageLength: a.data("pagelength"),
					info: a.data("info"),
					scrollX: !0,
					searching: a.data("searching"),
					ordering: a.data("ordering"),
					buttons: [
						{
							extend: "csvHtml5",
							text: JLTMA_DATA_TABLE.csvHtml5
						},
						{
							extend: "excelHtml5",
							text: JLTMA_DATA_TABLE.excelHtml5
						},
						{
							extend: "pdfHtml5",
							text: JLTMA_DATA_TABLE.pdfHtml5
						},
						{
							extend: "print",
							text: JLTMA_DATA_TABLE.print
						}
					],
					language: {
						lengthMenu: JLTMA_DATA_TABLE.lengthMenu,
						zeroRecords: JLTMA_DATA_TABLE.zeroRecords,
						info: JLTMA_DATA_TABLE.info,
						infoEmpty: JLTMA_DATA_TABLE.infoEmpty,
						infoFiltered: JLTMA_DATA_TABLE.infoFiltered,
						search: "",
						searchPlaceholder: JLTMA_DATA_TABLE.searchPlaceholder,
						processing: JLTMA_DATA_TABLE.processing
					}
				}
			});
			$scope.find(".jltma-data-table").css("visibility", "visible");
		},
		JLTMA_Dropdown_Button: function($scope, $) {
			$scope.find(".jltma-dropdown").hover(function() {
				$scope.find(".jltma-dd-menu").addClass("jltma-dd-menu-opened");
			}, function() {
				$scope.find(".jltma-dd-menu").removeClass("jltma-dd-menu-opened");
			});
		},
		JLTMA_WC_Add_To_Cart: function($scope, $) {
			$(document).on("click", ".ajax_add_to_cart", function(e) {
				$(this).append("<i class=\"fa fa-spinner animated rotateIn infinite\"></i>");
			});
			$(".jltma-wc-add-to-cart-btn-custom-js").each(function(index) {
				$($(this).attr("data-jltma-wc-add-to-cart-btn-custom-css")).appendTo("head");
			});
		},
		MA_Offcanvas_Menu: function($scope, $) {
			Master_Addons.MA_Offcanvas_Menu.elementSettings = $scope.data("settings");
			var widgetSelector = "jltma-offcanvas-menu", getID = $scope.data("id"), getElementSettings = $scope.data("settings"), is_esc_close = getElementSettings.esc_close ? getElementSettings.esc_close : "", classes = {
				widget: widgetSelector,
				triggerButton: "jltma-offcanvas__trigger",
				offcanvasContent: "jltma-offcanvas__content",
				offcanvasContentBody: "".concat(widgetSelector, "__body"),
				offcanvasContainer: "".concat(widgetSelector, "__container"),
				offcanvasContainerOverlay: "".concat(widgetSelector, "__container__overlay"),
				offcanvasWrapper: "".concat(widgetSelector, "__wrapper"),
				closeButton: "".concat(widgetSelector, "__close"),
				menuArrow: "".concat(widgetSelector, "__arrow"),
				menuInner: "".concat(widgetSelector, "__menu-inner"),
				itemHasChildrenLink: "menu-item-has-children > a",
				contentClassPart: "jltma-offcanvas-content",
				contentOpenClass: "jltma-offcanvas-content-open",
				customContainer: "".concat(widgetSelector, "__custom-container")
			}, selectors = {
				widget: ".".concat(classes.widget),
				triggerButton: ".".concat(classes.triggerButton),
				offcanvasContent: ".".concat(classes.offcanvasContent),
				offcanvasContentBody: ".".concat(classes.offcanvasContentBody),
				offcanvasContainer: ".".concat(classes.offcanvasContainer),
				offcanvasContainerOverlay: ".".concat(classes.offcanvasContainerOverlay),
				offcanvasWrapper: ".".concat(classes.offcanvasWrapper),
				closeButton: ".".concat(classes.closeButton),
				menuArrow: ".".concat(classes.menuArrow),
				menuParent: ".".concat(classes.menuInner, " .").concat(classes.itemHasChildrenLink),
				contentClassPart: ".".concat(classes.contentClassPart),
				contentOpenClass: ".".concat(classes.contentOpenClass),
				customContainer: ".".concat(classes.customContainer)
			}, elements = {
				$document: jQuery(document),
				$html: jQuery(document).find("html"),
				$body: jQuery(document).find("body"),
				$outsideContainer: jQuery(selectors.offcanvasContainer),
				$containerOverlay: jQuery(selectors.offcanvasContainerOverlay),
				$triggerButton: $scope.find(selectors.triggerButton),
				$offcanvasContent: $scope.find(selectors.offcanvasContent),
				$offcanvasContentBody: $scope.find(selectors.offcanvasContentBody),
				$offcanvasContainer: $scope.find(selectors.offcanvasContainer),
				$offcanvasWrapper: $scope.find(selectors.offcanvasWrapper),
				$closeButton: $scope.find(selectors.closeButton),
				$menuParent: $scope.find(selectors.menuParent)
			};
			Master_Addons.MA_Offcanvas_Menu.resetCanvas = function() {
				var contentId = getID;
				elements.$html.addClass("".concat(classes.offcanvasContent, "-widget"));
				if (!elements.$outsideContainer.length) {
					elements.$body.append("<div class=\"".concat(classes.offcanvasContainerOverlay, "\" />"));
					elements.$body.wrapInner("<div class=\"".concat(classes.offcanvasContainer, "\" />"));
					elements.$offcanvasContent.insertBefore(selectors.offcanvasContainer);
				}
				var $wrapperContent = elements.$offcanvasWrapper.find(selectors.offcanvasContent);
				if ($wrapperContent.length) {
					var $containerContent = elements.$outsideContainer.find("> .".concat(classes.contentClassPart, "-").concat(contentId));
					if ($containerContent.length) $containerContent.remove();
					var $bodyContent = elements.$body.find("> .".concat(classes.contentClassPart, "-").concat(contentId));
					if ($bodyContent.length) $bodyContent.remove();
					if (elements.$html.hasClass(classes.contentOpenClass)) $wrapperContent.addClass("active");
					elements.$body.prepend($wrapperContent);
				}
			};
			Master_Addons.MA_Offcanvas_Menu.offcanvasClose = function() {
				var openId = elements.$html.data("open-id");
				var regex = new RegExp("".concat(classes.contentClassPart, "-.*"));
				var classList = elements.$html.attr("class").split(/\s+/);
				jQuery("".concat(selectors.contentClassPart, "-").concat(openId)).removeClass("active");
				elements.$triggerButton.removeClass("trigger-active");
				classList.forEach(function(className) {
					if (!className.match(regex)) return;
					elements.$html.removeClass(className);
				});
				elements.$html.removeData("open-id");
			};
			Master_Addons.MA_Offcanvas_Menu.containerClick = function(event) {
				if (getID !== elements.$html.data("open-id") || !getElementSettings.overlay_close) return;
				if (!elements.$html.hasClass(classes.contentOpenClass)) return;
				Master_Addons.MA_Offcanvas_Menu.offcanvasClose();
			};
			Master_Addons.MA_Offcanvas_Menu.closeESC = function(event) {
				if (27 !== event.keyCode) return;
				Master_Addons.MA_Offcanvas_Menu.offcanvasClose();
				$(elements.$triggerButton).removeClass("trigger-active");
			};
			Master_Addons.MA_Offcanvas_Menu.addLoaderIcon = function() {
				jQuery(document).find(".jltma-offcanvas__content").addClass("jltma-loading");
			};
			Master_Addons.MA_Offcanvas_Menu.removeLoaderIcon = function() {
				jQuery(document).find(".jltma-offcanvas__content").removeClass("jltma-loading");
			};
			Master_Addons.MA_Offcanvas_Menu.bindEvents = function() {
				elements.$body.on("click", selectors.offcanvasContainerOverlay, Master_Addons.MA_Offcanvas_Menu.containerClick.bind(this));
				if ("yes" === is_esc_close) elements.$document.on("keydown", Master_Addons.MA_Offcanvas_Menu.closeESC.bind(this));
				elements.$triggerButton.on("click", Master_Addons.MA_Offcanvas_Menu.offcanvasContent.bind(this));
				elements.$closeButton.on("click", Master_Addons.MA_Offcanvas_Menu.offcanvasClose.bind(this));
				elements.$menuParent.on("click", Master_Addons.MA_Offcanvas_Menu.onParentClick.bind(this));
				$(elements.$menuParent).on("change", function() {
					Master_Addons.MA_Offcanvas_Menu.onParentClick.bind($(this));
				});
				$("[data-settings=animation_type]").on("click", function() {
					Master_Addons.MA_Offcanvas_Menu.changeControl.bind($(this));
				});
			};
			Master_Addons.MA_Offcanvas_Menu.perfectScrollInit = function() {
				if (!Master_Addons.MA_Offcanvas_Menu.scrollPerfect) {
					Master_Addons.MA_Offcanvas_Menu.scrollPerfect = new PerfectScrollbar(elements.$offcanvasContentBody.get(0), {
						wheelSpeed: .5,
						suppressScrollX: true
					});
					return;
				}
				Master_Addons.MA_Offcanvas_Menu.scrollPerfect.update();
			};
			Master_Addons.MA_Offcanvas_Menu.onEdit = function() {
				if (!Master_Addons.MA_Offcanvas_Menu.isEdit) return;
				if (void 0 === $element.data("opened")) $element.data("opened", "false");
				elementor.channels.editor.on("section:activated", Master_Addons.MA_Offcanvas_Menu.sectionActivated.bind(this));
			};
			Master_Addons.MA_Offcanvas_Menu.sectionActivated = function(sectionName, editor) {
				var elementsData = elementorFrontend.config.elements.data[this.getModelCID()];
				var editedElement = editor.getOption("editedElementView");
				if (this.getModelCID() !== editor.model.cid || elementsData.get("widgetType") !== editedElement.model.get("widgetType")) return;
				if (-1 !== this.sectionsArray.indexOf(sectionName)) {
					if ("true" === $element.data("opened")) {
						var editedModel = editor.getOption("model");
						Master_Addons.MA_Offcanvas_Menu.offcanvasContent(null, editedModel.get("id"));
					}
					$element.data("opened", "true");
				} else Master_Addons.MA_Offcanvas_Menu.offcanvasClose();
			};
			Master_Addons.MA_Offcanvas_Menu.offcanvasContent = function(event) {
				var widgetId = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : null;
				var boxPosition = getElementSettings.canvas_position;
				var offcanvasType = getElementSettings.animation_type;
				var contentId = getID;
				if (null !== widgetId) contentId = widgetId;
				elements.$triggerButton.addClass("trigger-active");
				jQuery("".concat(selectors.contentClassPart, "-").concat(contentId)).addClass("active");
				elements.$html.addClass("".concat(classes.contentOpenClass)).addClass("".concat(classes.contentOpenClass, "-").concat(contentId)).addClass("".concat(classes.contentClassPart, "-").concat(boxPosition)).addClass("".concat(classes.contentClassPart, "-").concat(offcanvasType)).data("open-id", contentId);
			};
			Master_Addons.MA_Offcanvas_Menu.onParentClick = function(event) {
				var $clickedItem = jQuery(event.target);
				var noLinkArray = ["", "#"];
				var $menuParent = $clickedItem.hasClass(classes.menuArrow) ? $clickedItem.parent() : $clickedItem;
				if ($clickedItem.hasClass(classes.menuArrow) || -1 !== noLinkArray.indexOf($clickedItem.attr("href")) || !$menuParent.hasClass("active")) event.preventDefault();
				var $menuParentNext = $menuParent.next();
				$menuParent.removeClass("active");
				$menuParentNext.slideUp("normal");
				if ($menuParentNext.is("ul") && !$menuParentNext.is(":visible")) {
					$menuParent.addClass("active");
					$menuParentNext.slideDown("normal");
				}
			};
			Master_Addons.MA_Offcanvas_Menu.changeControl = function() {
				Master_Addons.MA_Offcanvas_Menu.offcanvasClose();
			};
			Master_Addons.MA_Offcanvas_Menu.onInit = function() {
				Master_Addons.MA_Offcanvas_Menu.resetCanvas();
				Master_Addons.MA_Offcanvas_Menu.bindEvents();
			};
			return Master_Addons.MA_Offcanvas_Menu.onInit();
		},
		MA_Image_Filter_Gallery: function($scope, $) {
			var elementSettings = getElementSettings($scope), $jltma_image_filter_gallery_wrapper = $scope.find(".jltma-image-filter-gallery-wrapper").eq(0), $ma_el_image_filter_gallery_container = $scope.find(".jltma-image-filter-gallery"), $ma_el_image_filter_gallery_nav = $scope.find(".jltma-image-filter-nav"), $ma_el_image_filter_gallery_wrapper = $scope.find(".jltma-image-filter-gallery-wrapper"), $uniqueId = getUniqueLoopScopeId($scope), $maxtilt = elementSettings.ma_el_image_gallery_max_tilt, $perspective = elementSettings.ma_el_image_gallery_perspective, $speed = elementSettings.ma_el_image_gallery_speed, $axis = elementSettings.ma_el_image_gallery_tilt_axis, $glare = elementSettings.ma_el_image_gallery_glare;
			elementSettings.line_location;
			var $ma_el_image_gallery_tooltip = elementSettings.ma_el_image_gallery_tooltip, $container = $(".elementor-element-" + $uniqueId + " .jltma-image-filter-gallery"), layoutMode = $ma_el_image_filter_gallery_wrapper.hasClass("jltma-masonry-yes") ? "masonry" : "fitRows";
			if (!$jltma_image_filter_gallery_wrapper.length) return;
			if ($ma_el_image_gallery_tooltip == "yes") {
				var $img_filter_gallery = $jltma_image_filter_gallery_wrapper.find("ul.jltma-tooltip");
				if (!$img_filter_gallery.length) return;
				var $tooltip = $img_filter_gallery.find("> .jltma-tooltip-item"), widgetID = $scope.data("id");
				$tooltip.each(function(index) {
					tippy(this, {
						allowHTML: false,
						theme: "jltma-image-filter-tippy-" + widgetID
					});
				});
			}
			var optValues = {
				filter: "*",
				itemSelector: ".jltma-image-filter-item",
				percentPosition: true,
				animationOptions: {
					duration: 750,
					easing: "linear",
					queue: false
				}
			};
			var adata = Object.assign({}, optValues);
			if (layoutMode === "fitRows") optValues["layoutMode"] = "fitRows";
			if (layoutMode === "masonry") {
				adata["macolumnWidthsonry"] = ".jltma-image-filter-item";
				adata["horizontalOrder"] = true;
			}
			var $grid = $container.isotope(adata);
			$grid.imagesLoaded().progress(function() {
				$grid.isotope("layout");
				$scope.find(".jltma-image-filter-gallery").css({ "min-height": "300px" });
			});
			if ($.isFunction($.fn.imagesLoaded)) $ma_el_image_filter_gallery_container.imagesLoaded(function() {
				if ($.isFunction($.fn.isotope)) $ma_el_image_filter_gallery_container.isotope(optValues);
			});
			if ($axis === "x") $axis = "y";
			else if ($axis === "y") $axis = "x";
			else $axis = "both";
			if ($glare === "yes") var $max_glare = elementSettings.ma_el_image_gallery_max_glare;
			if ($glare === "yes") $glare = true;
			else $glare = false;
			if ($scope.find(".jltma-tilt-enable")) {
				var tilt_args = {
					maxTilt: $maxtilt,
					perspective: $perspective,
					easing: "linear",
					scale: 1,
					speed: $speed,
					disableAxis: $axis,
					transition: true,
					reset: true,
					glare: $glare,
					maxGlare: $max_glare
				};
				$scope.find(".jltma-tilt").tilt(tilt_args);
			}
			$ma_el_image_filter_gallery_nav.on("click", "li", function() {
				$ma_el_image_filter_gallery_nav.find(".active").removeClass("active");
				$(this).addClass("active");
				if ($.isFunction($.fn.isotope)) {
					var selector = $(this).attr("data-filter");
					$ma_el_image_filter_gallery_container.isotope({ filter: selector });
					return false;
				}
			});
			$("jltma-fancybox").fancybox({
				protect: true,
				animationDuration: 366,
				transitionDuration: 366,
				transitionEffect: "fade",
				animationEffect: "fade",
				preventCaptionOverlap: true,
				infobar: false,
				buttons: [
					"zoom",
					"share",
					"slideShow",
					"fullScreen",
					"download",
					"thumbs",
					"close"
				],
				afterLoad: function(instance, current) {
					var pixelRatio = window.devicePixelRatio || 1;
					if (pixelRatio > 1.5) {
						current.width = current.width / pixelRatio;
						current.height = current.height / pixelRatio;
					}
				}
			});
		},
		MA_Carousel: function($swiper, settings) {
			var $slides = $swiper.find(".jltma-swiper__slide"), elementorBreakpoints = elementorFrontend.config.breakpoints, swiperInstance = $swiper.data("swiper"), swiperArgs = {
				autoHeight: settings.element.autoHeight || false,
				direction: settings.element.direction || settings.default.direction,
				effect: settings.element.effect || settings.default.effect,
				slidesPerView: settings.default.slidesPerView,
				slidesPerColumn: settings.default.slidesPerColumn,
				slidesPerColumnFill: "row",
				slidesPerGroup: settings.default.slidesPerGroup,
				spaceBetween: settings.default.spaceBetween,
				pagination: {},
				navigation: {},
				autoplay: settings.element.autoplay || false,
				grabCursor: true,
				watchSlidesProgress: true,
				watchSlidesVisibility: true
			};
			if (settings.default.breakpoints) {
				swiperArgs.breakpoints = {};
				swiperArgs.breakpoints[elementorBreakpoints.md] = settings.default.breakpoints.tablet;
				swiperArgs.breakpoints[elementorBreakpoints.lg] = settings.default.breakpoints.desktop;
			}
			if (!elementorFrontend.isEditMode()) {
				if (!settings.element.freeMode) {
					swiperArgs.observer = true;
					swiperArgs.observeParents = true;
					swiperArgs.observeSlideChildren = true;
				}
			} else {
				swiperArgs.observer = true;
				swiperArgs.observeParents = true;
				swiperArgs.observeSlideChildren = true;
			}
			Master_Addons.MA_Carousel.init = function() {
				if (swiperInstance) {
					Master_Addons.MA_Carousel.destroy();
					return;
				}
				if (swiperArgs.breakpoints) {
					if (settings.element.breakpoints.desktop.slidesPerView) swiperArgs.breakpoints[elementorBreakpoints.lg].slidesPerView = settings.stretch ? Math.min($slides.length, +settings.element.breakpoints.desktop.slidesPerView || 3) : +settings.element.breakpoints.desktop.slidesPerView || 3;
					if (settings.element.breakpoints.tablet.slidesPerView) swiperArgs.breakpoints[elementorBreakpoints.md].slidesPerView = settings.stretch ? Math.min($slides.length, +settings.element.breakpoints.tablet.slidesPerView || 2) : +settings.element.breakpoints.tablet.slidesPerView || 2;
				}
				if (settings.element.slidesPerView) swiperArgs.slidesPerView = settings.stretch ? Math.min($slides.length, +settings.element.slidesPerView || 1) : +settings.element.slidesPerView || 1;
				if (swiperArgs.breakpoints) {
					if (settings.element.breakpoints.desktop.slidesPerGroup) swiperArgs.breakpoints[elementorBreakpoints.lg].slidesPerGroup = Math.min($slides.length, +settings.element.breakpoints.desktop.slidesPerGroup || 3);
					if (settings.element.breakpoints.tablet.slidesPerGroup) swiperArgs.breakpoints[elementorBreakpoints.md].slidesPerGroup = Math.min($slides.length, +settings.element.breakpoints.tablet.slidesPerGroup || 2);
				}
				if (settings.element.slidesPerGroup) swiperArgs.slidesPerGroup = Math.min($slides.length, +settings.element.slidesPerGroup || 1);
				if (swiperArgs.breakpoints) {
					if (settings.element.breakpoints.desktop.slidesPerColumn) swiperArgs.breakpoints[elementorBreakpoints.lg].slidesPerColumn = settings.element.breakpoints.desktop.slidesPerColumn;
					if (settings.element.breakpoints.tablet.slidesPerColumn) swiperArgs.breakpoints[elementorBreakpoints.md].slidesPerColumn = settings.element.breakpoints.tablet.slidesPerColumn;
				}
				if (settings.element.slidesPerColumn) swiperArgs.slidesPerColumn = settings.element.slidesPerColumn;
				if (swiperArgs.breakpoints) {
					swiperArgs.breakpoints[elementorBreakpoints.lg].spaceBetween = settings.element.breakpoints.desktop.spaceBetween || 0;
					swiperArgs.breakpoints[elementorBreakpoints.md].spaceBetween = settings.element.breakpoints.tablet.spaceBetween || 0;
				}
				if (settings.element.spaceBetween) swiperArgs.spaceBetween = settings.element.spaceBetween || 0;
				if (settings.element.slidesPerColumnFill) swiperArgs.slidesPerColumnFill = settings.element.slidesPerColumnFill;
				if (settings.element.arrows) {
					swiperArgs.navigation.disabledClass = "jltma-swiper__button--disabled";
					var $prevButton = settings.scope.find(settings.element.arrowPrev), $nextButton = settings.scope.find(settings.element.arrowNext);
					if ($prevButton.length && $nextButton.length) {
						var arrowPrev = settings.element.arrowPrev + "-" + settings.id, arrowNext = settings.element.arrowNext + "-" + settings.id;
						$prevButton.addClass(arrowPrev.replace(".", ""));
						$nextButton.addClass(arrowNext.replace(".", ""));
						swiperArgs.navigation.prevEl = arrowPrev;
						swiperArgs.navigation.nextEl = arrowNext;
					}
				}
				if (settings.element.pagination) {
					swiperArgs.pagination.el = ".jltma-swiper__pagination-" + settings.id;
					swiperArgs.pagination.type = settings.element.paginationType;
					if (settings.element.paginationClickable) swiperArgs.pagination.clickable = true;
				}
				if (settings.element.loop) swiperArgs.loop = true;
				if (swiperArgs.autoplay && (settings.element.autoplaySpeed || settings.element.disableOnInteraction)) {
					swiperArgs.autoplay = {};
					if (settings.element.autoplaySpeed) swiperArgs.autoplay.delay = settings.element.autoplaySpeed;
					if (settings.element.autoplaySpeed) swiperArgs.autoplay.disableOnInteraction = settings.element.disableOnInteraction;
				}
				if (settings.element.speed) swiperArgs.speed = settings.element.speed;
				if (settings.element.resistance) swiperArgs.resistanceRatio = 1 - settings.element.resistance;
				if (settings.element.freeMode) {
					swiperArgs.freeMode = true;
					swiperArgs.freeModeSticky = settings.element.freeModeSticky;
					swiperArgs.freeModeMomentum = settings.element.freeModeMomentum;
					swiperArgs.freeModeMomentumBounce = settings.element.freeModeMomentumBounce;
					if (settings.element.freeModeMomentumRatio) swiperArgs.freeModeMomentumRatio = settings.element.freeModeMomentumRatio;
					if (settings.element.freeModeMomentumVelocityRatio) swiperArgs.freeModeMomentumVelocityRatio = settings.element.freeModeMomentumVelocityRatio;
					if (settings.element.freeModeMomentumBounceRatio) swiperArgs.freeModeMomentumBounceRatio = settings.element.freeModeMomentumBounceRatio;
				}
				return swiperArgs;
			};
			Master_Addons.MA_Carousel.onAfterInit = function($swiper, swiper, settings) {
				if ("undefined" == typeof settings || "undefined" == typeof swiper) return;
				if (settings.element.stopOnHover) {
					$swiper.on("mouseover", function() {
						swiper.autoplay.stop();
					});
					$swiper.on("mouseout", function() {
						swiper.autoplay.start();
					});
				}
				if (settings.element.slideChangeTriggerResize) swiper.on("slideChange", function() {
					$(window).trigger("resize");
				});
				$swiper.data("swiper", swiper);
			};
			return Master_Addons.MA_Carousel.init();
		},
		MA_Gallery_Slider: function($scope, $) {
			var elementSettings = getElementSettings($scope), $swiperSlider = $scope.find(".jltma-gallery-slider__slider"), $swiperCarousel = $scope.find(".jltma-gallery-slider__carousel"), uniqueId = getUniqueLoopScopeId($scope);
			$scope.data("id");
			$scope.find(".jltma-gallery-slider__preview");
			$scope.find(".jltma-swiper__wrapper .jltma-gallery__item");
			$scope.find(".jltma-gallery-slider__gallery .jltma-gallery");
			elementSettings.jltma_gallery_slider_thumb_type;
			elementSettings.jltma_gallery_slider_preview_position;
			elementorFrontend.config.is_rtl;
			elementorFrontend.config.is_rtl;
			var hasCarousel = $swiperCarousel.length, swiperSlider = null, swiperCarousel = null, sliderSettings = {
				key: "slider",
				scope: $scope,
				id: uniqueId,
				element: {
					autoHeight: "yes" === elementSettings.jltma_gallery_slider_adaptive_height ? true : false,
					autoplay: "yes" === elementSettings.jltma_gallery_slider_autoplay ? true : false,
					autoplaySpeed: "yes" === elementSettings.jltma_gallery_slider_autoplay && elementSettings.jltma_gallery_slider_autoplay_speed ? elementSettings.jltma_gallery_slider_autoplay_speed.size : false,
					disableOnInteraction: "" !== elementSettings.autoplay_disable_on_interaction,
					stopOnHover: "yes" === elementSettings.jltma_gallery_slider_pause_on_hover,
					loop: "yes" === elementSettings.jltma_gallery_slider_infinite,
					arrows: "" !== elementSettings.jltma_gallery_slider_show_arrows,
					arrowPrev: ".jltma-arrow--prev",
					arrowNext: ".jltma-arrow--next",
					effect: elementSettings.jltma_gallery_slider_effect,
					speed: elementSettings.jltma_gallery_slider_speed ? elementSettings.jltma_gallery_slider_speed : 500,
					resistance: elementSettings.resistance ? elementSettings.resistance.size : .25,
					keyboard: { enabled: true }
				},
				default: {
					effect: "slide",
					direction: "horizontal",
					slidesPerView: 1,
					slidesPerGroup: 1,
					slidesPerColumn: 1,
					spaceBetween: 0
				}
			};
			if (hasCarousel) var carouselSettings = {
				key: "carousel",
				scope: $scope,
				id: uniqueId,
				element: {
					direction: elementSettings.carousel_orientation,
					arrows: "" !== elementSettings.jltma_gallery_slider_thumb_show_arrows,
					arrowPrev: ".jltma-arrow--prev",
					arrowNext: ".jltma-arrow--next",
					autoHeight: false,
					loop: "yes" === elementSettings.jltma_gallery_slider_thumb_infinite ? true : false,
					autoplay: "yes" === elementSettings.jltma_gallery_slider_thumb_autoplay ? true : false,
					autoplaySpeed: "yes" === elementSettings.jltma_gallery_slider_thumb_autoplay && elementSettings.jltma_gallery_slider_thumb_autoplay_speed ? elementSettings.jltma_gallery_slider_thumb_autoplay_speed.size : false,
					stopOnHover: "yes" === elementSettings.jltma_gallery_slider_thumb_pause_on_hover,
					speed: elementSettings.jltma_gallery_slider_thumb_speed ? elementSettings.jltma_gallery_slider_thumb_speed : 500,
					slidesPerView: elementSettings.jltma_gallery_slider_thumb_items_mobile,
					slidesPerColumn: "vertical" === elementSettings.carousel_orientation ? 1 : elementSettings.carousel_slides_per_column_mobile,
					slidesPerGroup: elementSettings.carousel_slides_to_scroll_mobile,
					resistance: elementSettings.carousel_resistance ? elementSettings.carousel_resistance.size : .15,
					spaceBetween: elementSettings.carousel_spacing_mobile ? elementSettings.carousel_spacing_mobile.size : 0,
					breakpoints: {
						tablet: {
							slidesPerView: elementSettings.jltma_gallery_slider_thumb_items_tablet,
							slidesPerColumn: "vertical" === elementSettings.carousel_orientation ? 1 : elementSettings.carousel_slides_per_column_tablet,
							slidesPerGroup: elementSettings.carousel_slides_to_scroll_tablet,
							spaceBetween: elementSettings.carousel_spacing_tablet ? elementSettings.carousel_spacing_tablet.size : 0
						},
						desktop: {
							slidesPerView: elementSettings.jltma_gallery_slider_thumb_items,
							slidesPerColumn: "vertical" === elementSettings.carousel_orientation ? 1 : elementSettings.carousel_slides_per_column,
							slidesPerGroup: elementSettings.carousel_slides_to_scroll,
							spaceBetween: elementSettings.carousel_spacing ? elementSettings.carousel_spacing.size : 0
						}
					}
				},
				default: {
					effect: "slide",
					slidesPerView: 1,
					slidesPerGroup: 1,
					slidesPerColumn: 1,
					spaceBetween: 6,
					breakpoints: {
						tablet: {
							slidesPerView: 2,
							slidesPerGroup: 1,
							slidesPerColumn: 2,
							spaceBetween: 12
						},
						desktop: {
							slidesPerView: 3,
							slidesPerGroup: 1,
							slidesPerColumn: 3,
							spaceBetween: 24
						}
					}
				}
			};
			Master_Addons.MA_Gallery_Slider.init = function() {
				var sliderArgs = Master_Addons.MA_Carousel($swiperSlider, sliderSettings);
				if (hasCarousel) var carouselArgs = Master_Addons.MA_Carousel($swiperCarousel, carouselSettings);
				if ("undefined" === typeof Swiper) {
					const asyncSwiper = elementorFrontend.utils.swiper;
					new asyncSwiper($swiperSlider, sliderArgs).then(function(sliderSwiperInstance) {
						if (!hasCarousel) {
							Master_Addons.MA_Gallery_Slider.initSliders($scope, sliderSwiperInstance, false);
							Master_Addons.MA_Carousel.onAfterInit($swiperSlider, sliderSwiperInstance, sliderSettings);
						} else new asyncSwiper($swiperCarousel, carouselArgs).then(function(carouselSwiperInstance) {
							Master_Addons.MA_Gallery_Slider.initSliders($scope, sliderSwiperInstance, carouselSwiperInstance);
							Master_Addons.MA_Carousel.onAfterInit($swiperSlider, sliderSwiperInstance, sliderSettings);
							Master_Addons.MA_Carousel.onAfterInit($swiperCarousel, carouselSwiperInstance, carouselSettings);
						});
					});
				} else {
					if (hasCarousel) {
						var swiper = new Swiper($swiperSlider[1], { ...carouselArgs });
						var swiperSlider = new Swiper($swiperSlider[0], {
							...sliderArgs,
							thumbs: { swiper }
						});
					} else var swiperSlider = new Swiper($swiperSlider[0], { ...sliderArgs });
					if (hasCarousel) swiperCarousel = new Swiper($swiperCarousel, carouselArgs);
					Master_Addons.MA_Gallery_Slider.initSliders($scope, swiperSlider, swiperCarousel);
					Master_Addons.MA_Carousel.onAfterInit($swiperSlider, swiperSlider, sliderSettings);
					if (hasCarousel) Master_Addons.MA_Carousel.onAfterInit($swiperCarousel, swiperCarousel, carouselSettings);
				}
			};
			Master_Addons.MA_Gallery_Slider.getSlider = function() {
				return $scope.find(".jltma-gallery-slider__slider");
			};
			Master_Addons.MA_Gallery_Slider.getCarousel = function() {
				return $scope.find(".jltma-gallery-slider__carousel");
			};
			Master_Addons.MA_Gallery_Slider.initSliders = function($scope, swiperSlider, swiperCarousel) {
				var data = {
					scope: $scope,
					slider: swiperSlider,
					carousel: swiperCarousel
				};
				Master_Addons.MA_Gallery_Slider.onSlideChange(data);
				Master_Addons.MA_Gallery_Slider.events(data);
			};
			Master_Addons.MA_Gallery_Slider.events = function(data) {
				var $thumbs = data.scope.find(".jltma-gallery__item");
				data.slider.on("slideChange", function(instance) {
					Master_Addons.MA_Gallery_Slider.onSlideChange(data);
				});
				$thumbs.on("click", function() {
					var offset = sliderSettings.element.loop ? 1 : 0;
					event.preventDefault();
					data.slider.slideTo($(this).index() + offset);
				});
			};
			Master_Addons.MA_Gallery_Slider.onSlideChange = function(data) {
				var activeIndex = sliderSettings.element.loop ? data.slider.realIndex : data.slider.activeIndex;
				if (hasCarousel) data.carousel.slideTo(activeIndex);
				var $thumbs = data.scope.find(".jltma-gallery__item");
				$thumbs.removeClass("is--active");
				$thumbs.eq(activeIndex).addClass("is--active");
			};
			Master_Addons.MA_Gallery_Slider.onThumbClicked = function(event) {
				var offset = sliderSettings.element.loop ? 1 : 0;
				event.preventDefault();
				swiperSlider.slideTo($(this).index() + offset, 500, true);
			};
			Master_Addons.onElementRemove($scope, function() {
				$scope.find(".swiper-container").each(function() {
					if ($(this).data("swiper")) $(this).data("swiper").destroy();
				});
			});
			Master_Addons.MA_Gallery_Slider.init();
		},
		onElementRemove: function($element, callback) {
			if (elementorFrontend.isEditMode()) elementor.channels.data.on("element:before:remove", function(model) {
				if ($element.data("id") === model.id) callback();
			});
		},
		MA_Timeline: function($scope, $) {
			var elementSettings = getElementSettings($scope), $timeline = $scope.find(".jltma-timeline"), $swiperSlider = $scope.find(".jltma-timeline-slider"), $timeline_type = elementSettings.ma_el_timeline_type || "custom", $timeline_layout = elementSettings.ma_el_timeline_design_type || "vertical", timelineArgs = {};
			$swiperSlider.length;
			getUniqueLoopScopeId($scope);
			if ($timeline_layout === "horizontal") {
				var $carousel = $scope.find(".jltma-timeline-carousel-slider");
				if (!$carousel.length) return;
				var $carouselContainer = $scope.find(".swiper"), $settings = $carousel.data("settings"), Swiper = elementorFrontend.utils.swiper;
				initSwiper();
				async function initSwiper() {
					await new Swiper($carouselContainer[0], $settings);
					if ($settings.pauseOnHover) $carouselContainer.hover(function() {
						this.swiper.autoplay.stop();
					}, function() {
						this.swiper.autoplay.start();
					});
				}
			}
			if ($timeline_layout === "vertical" || $timeline_type === "post") {
				var $timeline = $scope.find(".jltma-timeline"), timelineArgs = {};
				Master_Addons.MA_Timeline.init = function() {
					if (elementorFrontend.isEditMode()) timelineArgs.scope = window.elementor.$previewContents;
					if ("undefined" !== typeof elementSettings.line_location && elementSettings.line_location.size) timelineArgs.lineLocation = elementSettings.line_location.size;
					$timeline.maTimeline(timelineArgs);
				};
				Master_Addons.MA_Timeline.init();
			}
		},
		MA_NewsTicker: function($scope, $) {
			try {
				var newsTickerWrapper = $scope.find(".jltma-news-ticker");
				newsTickerWrapper.data("tickertype");
				newsTickerWrapper.data("tickerid");
				newsTickerWrapper.data("feedurl");
				newsTickerWrapper.data("feedanimation");
				newsTickerWrapper.data("limitposts");
				var tickerStyleEffect = newsTickerWrapper.data("scroll") || "slide-h", autoplay = newsTickerWrapper.data("autoplay"), timer = newsTickerWrapper.data("timer") || 3e3;
				var swiperContainer = $scope.find(".jltma-ticker-content-inner.swiper")[0];
				if (!swiperContainer) return;
				var swiperOptions = {
					loop: true,
					slidesPerView: 1,
					spaceBetween: 0,
					speed: 500,
					navigation: {
						nextEl: $scope.find(".jltma-ticker-next")[0],
						prevEl: $scope.find(".jltma-ticker-prev")[0]
					}
				};
				if (tickerStyleEffect === "slide-v") swiperOptions.direction = "vertical";
				else if (tickerStyleEffect === "scroll-h") {
					swiperOptions.direction = "horizontal";
					swiperOptions.freeMode = {
						enabled: true,
						momentum: false
					};
					swiperOptions.speed = 5e3;
					swiperOptions.autoplay = {
						delay: 0,
						disableOnInteraction: false,
						pauseOnMouseEnter: true
					};
				} else swiperOptions.direction = "horizontal";
				if (autoplay && tickerStyleEffect !== "scroll-h") swiperOptions.autoplay = {
					delay: timer,
					disableOnInteraction: false,
					pauseOnMouseEnter: true
				};
				new Swiper(swiperContainer, swiperOptions);
			} catch (e) {
				console.log("News Ticker Error:", e);
			}
		},
		MA_Blog: function($scope, $) {
			getElementSettings($scope);
			getUniqueLoopScopeId($scope);
			$scope.data("id");
			$scope.find(".jltma-swiper__container");
			$scope.find(".jltma-grid__item");
			var blogElement = $scope.find(".jltma-blog-wrapper");
			blogElement.data("col");
			var carousel = blogElement.data("carousel");
			blogElement.data("grid");
			$scope.find(".jltma-blog-cats-container li a").click(function(e) {
				e.preventDefault();
				$scope.find(".jltma-blog-cats-container li .active").removeClass("active");
				$(this).addClass("active");
				var selector = $(this).attr("data-filter");
				blogElement.isotope({ filter: selector });
				return false;
			});
			if (blogElement.hasClass("jltma-blog-masonry") && !carousel) blogElement.imagesLoaded(function() {
				blogElement.isotope({
					itemSelector: ".jltma-post-outer-container",
					percentPosition: true,
					animationOptions: {
						duration: 750,
						easing: "linear",
						queue: false
					}
				});
			});
			var $carousel = $scope.find(".jltma-blog-carousel-slider");
			if (!$carousel.length) return;
			var $carouselContainer = $scope.find(".swiper"), $settings = $carousel.data("settings"), Swiper = elementorFrontend.utils.swiper;
			initSwiper();
			async function initSwiper() {
				await new Swiper($carouselContainer[0], $settings);
				if ($settings.pauseOnHover) $carouselContainer.hover(function() {
					this.swiper.autoplay.stop();
				}, function() {
					this.swiper.autoplay.start();
				});
			}
		},
		/**** MA Image Carousel ****/
		MA_Image_Carousel: function($scope, $) {
			var $carousel = $scope.find(".jltma-image-carousel-slider");
			if (!$carousel.length) return;
			var $carouselContainer = $scope.find(".swiper"), $settings = $carousel.data("settings"), Swiper = elementorFrontend.utils.swiper;
			initSwiper();
			async function initSwiper() {
				await new Swiper($carouselContainer[0], $settings);
				if ($settings.pauseOnHover) $carouselContainer.hover(function() {
					this.swiper.autoplay.stop();
				}, function() {
					this.swiper.autoplay.start();
				});
			}
		},
		/**** MA Logo Slider ****/
		MA_Logo_Slider: function($scope, $) {
			var $carousel = $scope.find(".jltma-logo-carousel-slider");
			if (!$carousel.length) return;
			var $carouselContainer = $scope.find(".swiper"), $settings = $carousel.data("settings"), Swiper = elementorFrontend.utils.swiper;
			initSwiper();
			async function initSwiper() {
				await new Swiper($carouselContainer[0], $settings);
				if ($settings.pauseOnHover) $carouselContainer.hover(function() {
					this.swiper.autoplay.stop();
				}, function() {
					this.swiper.autoplay.start();
				});
			}
			/**
			* Icon click for hover
			*/
			$carousel.find(".jltma-logo-slider-figure").on("click", ".item-hover-icon", function() {
				var $this = $(this);
				$this.toggleClass("hide");
				$this.siblings(".jltma-hover-click").toggleClass("show");
			});
			$carousel.find(".jltma-logo-slider-item").each(function(e) {
				var $currentTooltip = $(this).attr("id");
				if ($currentTooltip) {
					var $dataId = $(this).data("id");
					var $tooltipSettings = $(this).data("tooltip-settings");
					var selector = "#" + $currentTooltip;
					var $follow_cursor = $tooltipSettings.follow_cursor;
					var placement_cursor;
					if ($follow_cursor == 1) placement_cursor = { followCursor: true };
					else placement_cursor = {
						placement: $tooltipSettings.placement,
						followCursor: false
					};
					var arrowType = false;
					if ($tooltipSettings.arrow == 1) if ($tooltipSettings.arrow_type == "round") arrowType = tippy.roundArrow;
					else arrowType = true;
					tippy(selector, {
						content: $tooltipSettings.text,
						...placement_cursor,
						animation: $tooltipSettings.animation,
						arrow: arrowType,
						duration: $tooltipSettings.duration,
						delay: $tooltipSettings.delay,
						trigger: $tooltipSettings.trigger,
						offset: [$tooltipSettings.x_offset, $tooltipSettings.y_offset],
						zIndex: 999999,
						allowHTML: false,
						theme: "jltma-tippy-" + $dataId,
						onShow(instance) {
							var tippyPopper = instance.popper;
							$(tippyPopper).addClass($dataId);
						}
					});
				}
			});
		},
		/**** MA Team Slider ****/
		MA_TeamSlider: function($scope, $) {
			if ($scope.find(".jltma-team-carousel-wrapper").eq(0).data("team-preset") == "-content-drawer") try {
				(function($) {
					$(".gridder").gridderExpander({
						scroll: false,
						scrollOffset: 0,
						scrollTo: "panel",
						animationSpeed: 400,
						animationEasing: "easeInOutExpo",
						showNav: true,
						nextText: "<span></span>",
						prevText: "<span></span>",
						closeText: "",
						onStart: function() {},
						onContent: function() {},
						onClosed: function() {}
					});
				})(jQuery);
			} catch (e) {}
			else {
				var $carousel = $scope.find(".jltma-team-carousel-slider");
				if (!$carousel.length) return;
				var $carouselContainer = $scope.find(".swiper"), $settings = $carousel.data("settings"), Swiper = elementorFrontend.utils.swiper;
				async function initSwiper() {
					await new Swiper($carouselContainer[0], $settings);
					if ($settings.pauseOnHover) $carouselContainer.hover(function() {
						this.swiper.autoplay.stop();
					}, function() {
						this.swiper.autoplay.start();
					});
				}
				initSwiper();
			}
		},
		/**** MA Advanced Image ****/
		MA_Advanced_Image: function($scope, $) {
			Master_Addons.MA_Advanced_Image.elementSettings = getElementSettings($scope);
			$scope.find(".jltma-img-dynamic-dropshadow").each(function() {
				var imgFrame, clonedImg, img;
				if (this instanceof jQuery) if (this && this[0]) img = this[0];
				else return;
				else img = this;
				if (!img.classList.contains("jltma-img-has-shadow")) {
					imgFrame = document.createElement("div");
					clonedImg = img.cloneNode();
					clonedImg.classList.add("jltma-img-dynamic-dropshadow-cloned");
					clonedImg.classList.remove("jltma-img-dynamic-dropshadow");
					img.classList.add("jltma-img-has-shadow");
					imgFrame.classList.add("jltma-img-dynamic-dropshadow-frame");
					img.parentNode.appendChild(imgFrame);
					imgFrame.appendChild(img);
					imgFrame.appendChild(clonedImg);
				}
			});
			$scope.find(".jltma-tilt-box").tilt({
				maxTilt: $(this).data("max-tilt"),
				easing: "cubic-bezier(0.23, 1, 0.32, 1)",
				speed: $(this).data("time"),
				perspective: 2e3
			});
		},
		MA_Tooltip: function($scope, $) {
			"use strict";
			if (!$scope || !$scope.length || !$ || typeof getElementSettings !== "function") return;
			if (typeof tippy === "undefined") {
				var retryCount = ($scope.data("ma-tooltip-retry") || 0) + 1;
				if (retryCount <= 10) {
					$scope.data("ma-tooltip-retry", retryCount);
					setTimeout(function() {
						Master_Addons.MA_Tooltip($scope, $);
					}, 100);
				}
				return;
			}
			$scope.removeData("ma-tooltip-retry");
			var elementSettings = getElementSettings($scope), scopeId = $scope.data("id"), currentTooltipElement = null;
			if (!scopeId || typeof scopeId !== "string") return;
			try {
				currentTooltipElement = document.getElementById("jltma-tooltip-" + scopeId);
				if (!currentTooltipElement) {
					var $fallbackElement = $scope.find("#jltma-tooltip-" + scopeId);
					if ($fallbackElement && $fallbackElement.length > 0) {
						var fallbackEl = $fallbackElement[0];
						if (fallbackEl && fallbackEl.nodeType === 1) currentTooltipElement = fallbackEl;
					}
				}
				if (!currentTooltipElement || !currentTooltipElement.nodeType || currentTooltipElement.nodeType !== 1) return;
				if (currentTooltipElement.jquery) {
					currentTooltipElement = currentTooltipElement[0];
					if (!currentTooltipElement || !currentTooltipElement.nodeType) return;
				}
			} catch (error) {
				return;
			}
			var initTooltip = function() {
				try {
					if (currentTooltipElement && currentTooltipElement._maTooltipInitializing) return;
					if (!elementSettings || typeof elementSettings !== "object") return;
					var tooltipText = elementSettings.ma_el_tooltip_text;
					if (!tooltipText || typeof tooltipText !== "string") return;
					if (currentTooltipElement) currentTooltipElement._maTooltipInitializing = true;
					var $jltma_el_tooltip_text = stripTags(tooltipText), $jltma_el_tooltip_direction = elementSettings.ma_el_tooltip_direction || "top", $jltma_tooltip_animation = elementSettings.jltma_tooltip_animation || "shift-away", $jltma_tooltip_arrow = elementSettings.jltma_tooltip_arrow !== false, $jltma_tooltip_duration = parseInt(elementSettings.jltma_tooltip_duration) || 300, $jltma_tooltip_delay = parseInt(elementSettings.jltma_tooltip_delay) || 300, $jltma_tooltip_arrow_type = elementSettings.jltma_tooltip_arrow_type || "sharp", $jltma_tooltip_trigger = elementSettings.jltma_tooltip_trigger || "mouseenter", $jltma_tooltip_custom_trigger = elementSettings.jltma_tooltip_custom_trigger, $animateFill = elementSettings.jltma_tooltip_animation === "fill";
					$jltma_tooltip_duration = Math.max(100, Math.min(5e3, $jltma_tooltip_duration));
					$jltma_tooltip_delay = Math.max(0, Math.min(5e3, $jltma_tooltip_delay));
					var $jltma_tooltip_x_offset = 0, $jltma_tooltip_y_offset = 0;
					try {
						if (elementSettings.jltma_tooltip_x_offset && elementSettings.jltma_tooltip_x_offset.size !== void 0) $jltma_tooltip_x_offset = parseInt(elementSettings.jltma_tooltip_x_offset.size) || 0;
						if (elementSettings.jltma_tooltip_y_offset && elementSettings.jltma_tooltip_y_offset.size !== void 0) $jltma_tooltip_y_offset = parseInt(elementSettings.jltma_tooltip_y_offset.size) || 0;
					} catch (error) {
						$jltma_tooltip_x_offset = 0;
						$jltma_tooltip_y_offset = 0;
					}
					var $jltma_el_tooltip_text_width = 200;
					try {
						if (elementSettings.ma_el_tooltip_text_width && elementSettings.ma_el_tooltip_text_width.size) $jltma_el_tooltip_text_width = parseInt(elementSettings.ma_el_tooltip_text_width.size) || 200;
					} catch (error) {
						$jltma_el_tooltip_text_width = 200;
					}
					if (!currentTooltipElement || !currentTooltipElement.nodeType || currentTooltipElement.nodeType !== 1) return;
					if (!$jltma_el_tooltip_text || !$jltma_el_tooltip_text.trim()) return;
					try {
						var parentElement = currentTooltipElement.parentElement;
						if (parentElement && parentElement.classList && parentElement.classList.contains("jltma-tooltip-element")) return;
					} catch (error) {}
					var tooltipConfig = {
						content: $jltma_el_tooltip_text,
						animation: $jltma_tooltip_animation,
						arrow: $jltma_tooltip_arrow,
						duration: [$jltma_tooltip_duration, $jltma_tooltip_delay],
						trigger: $jltma_tooltip_trigger,
						animateFill: $animateFill,
						flipOnUpdate: true,
						maxWidth: Math.max(50, Math.min(1e3, $jltma_el_tooltip_text_width)),
						zIndex: 999,
						allowHTML: false,
						theme: "jltma-tooltip-tippy-" + scopeId,
						interactive: true,
						hideOnClick: true,
						offset: [Math.max(-500, Math.min(500, $jltma_tooltip_x_offset)), Math.max(-500, Math.min(500, $jltma_tooltip_y_offset))],
						appendTo: function() {
							try {
								return typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode() ? document.body : "parent";
							} catch (error) {
								return "parent";
							}
						},
						onShow: function(instance) {
							try {
								if (instance && instance.popper && typeof jQuery !== "undefined") jQuery(instance.popper).attr("data-tippy-popper-id", scopeId);
							} catch (error) {}
						},
						onCreate: function(instance) {
							try {
								if (instance && instance.reference && !instance.reference.nodeType) {
									if (instance.reference.jquery) instance.reference = instance.reference[0];
								}
							} catch (error) {}
						},
						onDestroy: function() {
							if (currentTooltipElement) currentTooltipElement._tippyInstance = null;
						}
					};
					if ($jltma_tooltip_arrow && $jltma_tooltip_arrow_type === "round") try {
						if (typeof tippy !== "undefined" && tippy.roundArrow) tooltipConfig.arrow = tippy.roundArrow;
					} catch (error) {
						tooltipConfig.arrow = true;
					}
					if (elementSettings.jltma_tooltip_follow_cursor === "yes" || elementSettings.jltma_tooltip_follow_cursor === true) tooltipConfig.followCursor = true;
					else if ($jltma_el_tooltip_direction && typeof $jltma_el_tooltip_direction === "string") {
						if ([
							"top",
							"bottom",
							"left",
							"right",
							"top-start",
							"top-end",
							"bottom-start",
							"bottom-end",
							"left-start",
							"left-end",
							"right-start",
							"right-end",
							"auto"
						].indexOf($jltma_el_tooltip_direction) !== -1) tooltipConfig.placement = $jltma_el_tooltip_direction;
					}
					if ($jltma_tooltip_trigger === "manual" && $jltma_tooltip_custom_trigger && typeof $jltma_tooltip_custom_trigger === "string") try {
						var sanitizedSelector = $jltma_tooltip_custom_trigger.replace(/[<>'"]/g, "");
						var customTriggerEl = document.querySelector(sanitizedSelector);
						if (customTriggerEl && customTriggerEl.nodeType === 1) {
							tooltipConfig.trigger = "manual";
							tooltipConfig.hideOnClick = false;
							var customClickHandler = function() {
								var targetEl = currentTooltipElement;
								if (targetEl && targetEl.jquery) targetEl = targetEl[0];
								var instance = targetEl ? targetEl._tippyInstance : null;
								if (instance && instance.state) if (instance.state.isVisible) instance.hide();
								else {
									instance.show();
									setTimeout(function() {
										if (instance && !instance.state.isDestroyed) instance.hide();
									}, 1500);
								}
							};
							customTriggerEl.addEventListener("click", customClickHandler);
							if (!currentTooltipElement._maTooltipCleanup) currentTooltipElement._maTooltipCleanup = [];
							currentTooltipElement._maTooltipCleanup.push({
								element: customTriggerEl,
								event: "click",
								handler: customClickHandler
							});
						}
					} catch (error) {}
					try {
						[
							"[data-tippy-popper-id=\"" + scopeId + "\"]",
							".tippy-popper[data-tippy-root]",
							".tippy-box[data-theme*=\"" + scopeId + "\"]"
						].forEach(function(selector) {
							try {
								document.querySelectorAll(selector).forEach(function(popper) {
									if (popper && popper.parentNode) popper.parentNode.removeChild(popper);
								});
							} catch (error) {}
						});
						if (currentTooltipElement._tippyInstance) {
							currentTooltipElement._tippyInstance.destroy();
							currentTooltipElement._tippyInstance = null;
						}
						if (currentTooltipElement._tippy) {
							currentTooltipElement._tippy.destroy();
							currentTooltipElement._tippy = null;
						}
						if (currentTooltipElement._maTooltipCleanup) {
							currentTooltipElement._maTooltipCleanup.forEach(function(cleanup) {
								try {
									cleanup.element.removeEventListener(cleanup.event, cleanup.handler);
								} catch (error) {}
							});
							currentTooltipElement._maTooltipCleanup = [];
						}
					} catch (error) {}
					if (!currentTooltipElement || !currentTooltipElement.nodeType || currentTooltipElement.nodeType !== 1) return;
					var sanitizedConfig = {};
					for (var key in tooltipConfig) if (tooltipConfig.hasOwnProperty(key)) sanitizedConfig[key] = tooltipConfig[key];
					if (typeof sanitizedConfig.appendTo === "function") try {
						var appendToResult = sanitizedConfig.appendTo();
						if (appendToResult === "parent") sanitizedConfig.appendTo = "parent";
						else if (appendToResult && appendToResult.nodeType) sanitizedConfig.appendTo = appendToResult;
						else sanitizedConfig.appendTo = "parent";
					} catch (error) {
						sanitizedConfig.appendTo = "parent";
					}
					try {
						var nativeElement = currentTooltipElement;
						if (nativeElement && nativeElement.jquery) nativeElement = nativeElement[0];
						if (!nativeElement || !nativeElement.nodeType || nativeElement.nodeType !== 1) throw new Error("Invalid DOM element for tooltip");
						var tippyInstance = tippy(nativeElement, sanitizedConfig);
						if (tippyInstance && Array.isArray(tippyInstance) && tippyInstance.length > 0) {
							nativeElement._tippyInstance = tippyInstance[0];
							currentTooltipElement._tippyInstance = tippyInstance[0];
							$scope.data("ma-tooltip-active", true);
						}
					} catch (error) {
						return;
					} finally {
						if (currentTooltipElement) currentTooltipElement._maTooltipInitializing = false;
					}
				} catch (error) {
					if (currentTooltipElement) currentTooltipElement._maTooltipInitializing = false;
					return;
				}
			};
			initTooltip();
			if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode()) try {
				$scope.data("ma-tooltip-initialized", true);
				var changeTimeout = null;
				var isChanging = false;
				var handleTooltipChange = function() {
					if (isChanging) return;
					if (changeTimeout) clearTimeout(changeTimeout);
					changeTimeout = setTimeout(function() {
						try {
							isChanging = true;
							elementSettings = getElementSettings($scope);
							document.querySelectorAll(".tippy-popper:not([data-tippy-popper-id])").forEach(function(tooltip) {
								if (tooltip.parentNode) tooltip.parentNode.removeChild(tooltip);
							});
							initTooltip();
							changeTimeout = null;
							setTimeout(function() {
								isChanging = false;
							}, 100);
						} catch (error) {
							isChanging = false;
						}
					}, 200);
				};
				if (typeof elementorModules !== "undefined" && elementorModules.frontend && elementorModules.frontend.handlers && elementorModules.frontend.handlers.Base) {
					var MATooltipEditorHandler = elementorModules.frontend.handlers.Base.extend({
						onElementChange: function(propertyName) {
							if (propertyName && typeof propertyName === "string" && (propertyName.indexOf("ma_el_tooltip") === 0 || propertyName.indexOf("jltma_tooltip") === 0)) handleTooltipChange();
						},
						onDestroy: function() {
							if (changeTimeout) clearTimeout(changeTimeout);
							if (currentTooltipElement && currentTooltipElement._tippyInstance) try {
								currentTooltipElement._tippyInstance.destroy();
							} catch (error) {}
						}
					});
					try {
						elementorFrontend.elementsHandler.addHandler(MATooltipEditorHandler, { $element: $scope });
					} catch (error) {
						$scope.one("remove", function() {
							if (changeTimeout) clearTimeout(changeTimeout);
							if (currentTooltipElement && currentTooltipElement._tippyInstance) try {
								currentTooltipElement._tippyInstance.destroy();
							} catch (error) {}
						});
					}
				}
			} catch (error) {}
			if (typeof window !== "undefined" && window.addEventListener) {
				var cleanupTooltip = function() {
					if (currentTooltipElement) {
						if (currentTooltipElement._tippyInstance) try {
							currentTooltipElement._tippyInstance.destroy();
						} catch (error) {}
						if (currentTooltipElement._maTooltipCleanup) currentTooltipElement._maTooltipCleanup.forEach(function(cleanup) {
							try {
								cleanup.element.removeEventListener(cleanup.event, cleanup.handler);
							} catch (error) {}
						});
					}
				};
				window.addEventListener("beforeunload", cleanupTooltip);
				window.addEventListener("pagehide", cleanupTooltip);
			}
		},
		/**** MA Twitter Slider ****/
		MA_Twitter_Slider: function($scope, $) {
			var $carousel = $scope.find(".jltma-twitter-carousel-slider");
			if (!$carousel.length) return;
			var $carouselContainer = $scope.find(".swiper"), $settings = $carousel.data("settings"), Swiper = elementorFrontend.utils.swiper;
			initSwiper();
			async function initSwiper() {
				await new Swiper($carouselContainer[0], $settings);
				if ($settings.pauseOnHover) $carouselContainer.hover(function() {
					this.swiper.autoplay.stop();
				}, function() {
					this.swiper.autoplay.start();
				});
			}
		},
		MA_ParticlesBG: function($scope, $) {
			function isElementorEditor() {
				return typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode();
			}
			if ($scope.hasClass("jltma-particle-yes") || $scope.attr("data-jltma-particle") || $scope.find(".jltma-particle-wrapper").attr("data-jltma-particles-editor")) {
				let element_type = $scope.data("element_type");
				let sectionID = encodeURIComponent($scope.data("id"));
				let particlesJSON;
				if (!isElementorEditor()) particlesJSON = $scope.attr("data-jltma-particle");
				else particlesJSON = $scope.find(".jltma-particle-wrapper").attr("data-jltma-particles-editor");
				if (("section" === element_type || "column" === element_type || "container" === element_type) && particlesJSON) if (!isElementorEditor()) {
					$scope.prepend("<div class=\"jltma-particle-wrapper\" id=\"jltma-particle-" + sectionID + "\"></div>");
					try {
						let parsedData = JSON.parse(particlesJSON);
						particlesJS("jltma-particle-" + sectionID, parsedData);
						setTimeout(function() {
							window.dispatchEvent(new Event("resize"));
						}, 500);
						setTimeout(function() {
							window.dispatchEvent(new Event("resize"));
						}, 1500);
					} catch (e) {}
				} else if ($scope.hasClass("jltma-particle-yes")) try {
					let parsedData = JSON.parse(particlesJSON);
					particlesJS("jltma-particle-" + sectionID, parsedData);
					$scope.find(".elementor-column").css("z-index", 9);
					setTimeout(function() {
						window.dispatchEvent(new Event("resize"));
					}, 500);
					setTimeout(function() {
						window.dispatchEvent(new Event("resize"));
					}, 1500);
				} catch (e) {}
				else $scope.find(".jltma-particle-wrapper").remove();
			}
		},
		MA_BgSlider: function($scope, $) {
			function isElementorEditor() {
				return typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode();
			}
			if (!isElementorEditor()) {
				if (!$scope.hasClass("has_ma_el_bg_slider")) return;
			} else if (!$scope.find(".ma-el-section-bs").length) return;
			var ma_el_slides = [], ma_el_slides_json = [], ma_el_transition, ma_el_animation, ma_el_custom_overlay, ma_el_overlay, ma_el_cover, ma_el_delay, ma_el_timer;
			var slider_images;
			if (!isElementorEditor()) slider_images = $scope.attr("data-ma-el-bg-slider-images");
			else {
				var slider_wrapper = $scope.find(".ma-el-section-bs-inner");
				if (slider_wrapper.length) slider_images = slider_wrapper.attr("data-ma-el-bg-slider");
			}
			if (!slider_images) return;
			if (!isElementorEditor()) {
				ma_el_transition = $scope.attr("data-ma-el-bg-slider-transition");
				ma_el_animation = $scope.attr("data-ma-el-bg-slider-animation");
				ma_el_custom_overlay = $scope.attr("data-ma-el-bg-custom-overlay");
				ma_el_cover = $scope.attr("data-ma-el-bg-slider-cover");
				ma_el_delay = $scope.attr("data-ma-el-bs-slider-delay");
				ma_el_timer = $scope.attr("data-ma-el-bs-slider-timer");
				if (ma_el_custom_overlay == "yes") ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
				else {
					var overlay_file = $scope.attr("data-ma-el-bg-slider-overlay");
					if (overlay_file) ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/" + overlay_file + ".png";
					else ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
				}
			} else {
				var slider_wrapper = $scope.find(".ma-el-section-bs-inner");
				ma_el_transition = slider_wrapper.attr("data-ma-el-bg-slider-transition");
				ma_el_animation = slider_wrapper.attr("data-ma-el-bg-slider-animation");
				ma_el_custom_overlay = slider_wrapper.attr("data-ma-el-bg-custom-overlay");
				ma_el_cover = slider_wrapper.attr("data-ma-el-bg-slider-cover");
				ma_el_delay = slider_wrapper.attr("data-ma-el-bs-slider-delay");
				ma_el_timer = slider_wrapper.attr("data-ma-el-bs-slider-timer");
				if (ma_el_custom_overlay == "yes") ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
				else {
					var overlay_file = slider_wrapper.attr("data-ma-el-bg-slider-overlay");
					if (overlay_file && overlay_file !== "00.png") ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/" + overlay_file;
					else ma_el_overlay = JLTMA_SCRIPTS.assets_url + "/assets/vendor/vegas/overlays/00.png";
				}
			}
			ma_el_slides = slider_images.split(",");
			jQuery.each(ma_el_slides, function(key, value) {
				var slide = [];
				slide.src = value;
				ma_el_slides_json.push(slide);
			});
			var slider_container;
			if (!$scope.find(".ma-el-section-bs").length) $scope.prepend("<div class=\"ma-el-section-bs\"><div class=\"ma-el-section-bs-inner\"></div></div>");
			slider_container = $scope.find(".ma-el-section-bs-inner");
			slider_container.vegas({
				slides: ma_el_slides_json,
				transition: ma_el_transition,
				animation: ma_el_animation,
				overlay: ma_el_overlay,
				cover: ma_el_cover == "true" ? true : false,
				delay: parseInt(ma_el_delay) || 5e3,
				timer: ma_el_timer == "true" ? true : false,
				init: function() {
					if (ma_el_custom_overlay == "yes") slider_container.children(".vegas-overlay").css("background-image", "");
				}
			});
		},
		MA_AnimatedGradient: function($scope, $) {
			if ($scope.hasClass("ma-el-animated-gradient-yes")) {
				let color = $scope.data("color") || $scope.attr("data-color");
				let angle = $scope.data("angle") || $scope.attr("data-angle");
				let duration = $scope.data("duration") || $scope.attr("data-duration") || "6s";
				let smoothness = parseInt($scope.data("smoothness") || $scope.attr("data-smoothness") || 3);
				let easing = $scope.data("easing") || $scope.attr("data-easing") || "cubic-bezier(0.4, 0.0, 0.2, 1)";
				if (!color || !angle) return;
				let colors = color.split(",");
				if (colors.length < 2) return;
				let animationName = "jltma-animated-gradient-" + Math.random().toString(36).substring(2, 11);
				let keyframes = `@keyframes ${animationName} {`;
				let totalSteps = colors.length;
				for (let i = 0; i <= totalSteps; i++) {
					let percentage = i / totalSteps * 100;
					let currentColorIndex = i % colors.length;
					let nextColorIndex = (i + 1) % colors.length;
					keyframes += `${percentage.toFixed(2)}% { background: linear-gradient(${angle}, ${colors[currentColorIndex].trim()}, ${colors[nextColorIndex].trim()}); }`;
					if (i < totalSteps) {
						let segmentSize = 100 / totalSteps;
						for (let j = 1; j <= smoothness; j++) {
							let interpPercentage = percentage + segmentSize * (j / (smoothness + 1));
							let interpColorIndex = nextColorIndex;
							let interpNextColorIndex = (nextColorIndex + 1) % colors.length;
							keyframes += `${interpPercentage.toFixed(2)}% { background: linear-gradient(${angle}, ${colors[interpColorIndex].trim()}, ${colors[interpNextColorIndex].trim()}); }`;
						}
					}
				}
				keyframes += "}";
				let style = document.createElement("style");
				style.textContent = keyframes;
				document.head.appendChild(style);
				$scope.css({
					"animation": `${animationName} ${duration} ${easing} infinite`,
					"background-size": "400% 400%"
				});
				if ($scope.hasClass("elementor-element-edit-mode")) {
					let editorGradient = $scope.find(".animated-gradient");
					if (editorGradient.length > 0) {
						let editorColor = editorGradient.data("color") || editorGradient.attr("data-color");
						let editorAngle = editorGradient.data("angle") || editorGradient.attr("data-angle");
						let editorDuration = editorGradient.data("duration") || editorGradient.attr("data-duration") || "6s";
						if (editorColor && editorAngle) {
							let editorColors = editorColor.split(",");
							if (editorColors.length >= 2) {
								let editorAnimationName = "jltma-animated-gradient-editor-" + Math.random().toString(36).substring(2, 11);
								let editorKeyframes = `@keyframes ${editorAnimationName} {`;
								let totalSteps = editorColors.length;
								for (let i = 0; i <= totalSteps; i++) {
									let percentage = i / totalSteps * 100;
									let currentColorIndex = i % editorColors.length;
									let nextColorIndex = (i + 1) % editorColors.length;
									editorKeyframes += `${percentage.toFixed(2)}% { background: linear-gradient(${editorAngle}, ${editorColors[currentColorIndex].trim()}, ${editorColors[nextColorIndex].trim()}); }`;
									if (i < totalSteps) {
										let segmentSize = 100 / totalSteps;
										for (let j = 1; j <= smoothness; j++) {
											let interpPercentage = percentage + segmentSize * (j / (smoothness + 1));
											let interpColorIndex = nextColorIndex;
											let interpNextColorIndex = (nextColorIndex + 1) % editorColors.length;
											editorKeyframes += `${interpPercentage.toFixed(2)}% { background: linear-gradient(${editorAngle}, ${editorColors[interpColorIndex].trim()}, ${editorColors[interpNextColorIndex].trim()}); }`;
										}
									}
								}
								editorKeyframes += "}";
								let editorStyle = document.createElement("style");
								editorStyle.textContent = editorKeyframes;
								document.head.appendChild(editorStyle);
								editorGradient.css({
									"animation": `${editorAnimationName} ${editorDuration} ${easing} infinite`,
									"background-size": "400% 400%"
								});
							}
						}
					}
				}
			}
		},
		MA_Image_Comparison: function($scope, $) {
			var $jltma_image_comp_wrap = $scope.find(".jltma-image-comparison").eq(0), $jltma_image_data = $jltma_image_comp_wrap.data("image-comparison-settings");
			$jltma_image_comp_wrap.twentytwenty({
				default_offset_pct: $jltma_image_data.visible_ratio,
				orientation: $jltma_image_data.orientation,
				before_label: $jltma_image_data.before_label,
				after_label: $jltma_image_data.after_label,
				move_slider_on_hover: $jltma_image_data.slider_on_hover,
				move_with_handle_only: $jltma_image_data.slider_with_handle,
				click_to_move: $jltma_image_data.slider_with_click,
				no_overlay: $jltma_image_data.no_overlay
			});
		},
		MA_BarCharts: function BarChart($scope) {
			jltMAObserveTarget($scope[0], function() {
				var $container = $scope.find(".jltma-bar-chart-container"), $chart_canvas = $scope.find("#jltma-bar-chart"), settings = $container.data("settings");
				if ($container.length) new Chart($chart_canvas, settings);
			});
		},
		MA_PieCharts: function($scope, $) {
			jltMAObserveTarget($scope[0], function() {
				$scope.find(".ma-el-piechart .ma-el-percentage").each(function() {
					var track_color = $(this).data("track-color");
					var bar_color = $(this).data("bar-color");
					$(this).easyPieChart({
						animate: 2e3,
						lineWidth: 10,
						barColor: bar_color,
						trackColor: track_color,
						scaleColor: false,
						lineCap: "square",
						size: 220
					});
				});
			});
		},
		ProgressBars: function($scope, $) {
			jltMAObserveTarget($scope[0], function() {
				$scope.find(".jltma-stats-bar-content").each(function() {
					var dataperc = $(this).data("perc");
					$(this).animate({ "width": dataperc + "%" }, dataperc * 20);
				});
			});
		},
		MA_Toggle_Content: function($scope, $) {
			Master_Addons.getElementSettings = getElementSettings($scope);
			var $wrapper = $scope.find(".jltma-toggle-content"), toggleElementArgs = { active: Master_Addons.getElementSettings.jltma_toggle_content_active_index };
			if ("" !== Master_Addons.getElementSettings.jltma_toggle_content_indicator_color) toggleElementArgs.indicatorColor = Master_Addons.getElementSettings.jltma_toggle_content_indicator_color;
			if (Master_Addons.getElementSettings.jltma_toggle_content_indicator_speed.size) toggleElementArgs.speed = Master_Addons.getElementSettings.jltma_toggle_content_indicator_speed.size;
			if (elementorFrontend.isEditMode()) toggleElementArgs.watchControls = true;
			$wrapper.MA_ToggleElement(toggleElementArgs);
		},
		MA_Comment_Form_reCaptcha: function($scope, $) {
			Master_Addons.getElementSettings = getElementSettings($scope);
			var $commentsWrapper = $scope.find(".jltma-comments-wrap");
			$commentsWrapper.data("recaptcha");
			if ($commentsWrapper.data("jltma-comment-settings").reCaptchaprotected == "yes") {}
		},
		MA_Counter_Up: function($scope, $) {
			var $counterup = $scope.find(".jltma-counter-up-number");
			if ($.isFunction($.fn.counterUp)) $counterup.counterUp({
				duration: 2e3,
				delay: 15
			});
		},
		MA_CountdownTimer: function($scope, $) {
			var $countdownWidget = $scope.find(".jltma-widget-countdown");
			$.fn.MasterCountDownTimer = function() {
				var $wrapper = $(this).find(".jltma-countdown-wrapper"), data = {
					year: $wrapper.data("countdown-year"),
					month: $wrapper.data("countdown-month"),
					day: $wrapper.data("countdown-day"),
					hour: $wrapper.data("countdown-hour"),
					min: $wrapper.data("countdown-min"),
					sec: $wrapper.data("countdown-sec")
				};
				$wrapper.data("countdown-infinite");
				var targetDate = new Date(data.year, data.month, data.day, data.hour, data.min, data.sec);
				var $year = $wrapper.find(".jltma-countdown-year"), $month = $wrapper.find(".jltma-countdown-month"), $day = $wrapper.find(".jltma-countdown-day"), $hour = $wrapper.find(".jltma-countdown-hour"), $min = $wrapper.find(".jltma-countdown-min"), $sec = $wrapper.find(".jltma-countdown-sec");
				var countdownInterval = setInterval(function() {
					var currentTime = /* @__PURE__ */ new Date();
					var diffTime = (Date.parse(targetDate) - Date.parse(currentTime)) / 1e3;
					if (diffTime <= 0) {
						$year.text(0);
						$month.text(0);
						$day.text(0);
						$hour.text(0);
						$min.text(0);
						$sec.text(0);
						clearInterval(countdownInterval);
						return;
					}
					var totalSeconds = diffTime;
					var years = Math.floor(totalSeconds / 31536e3);
					totalSeconds %= 31536e3;
					var months = Math.floor(totalSeconds / 2592e3);
					totalSeconds %= 2592e3;
					var days = Math.floor(totalSeconds / 86400);
					totalSeconds %= 86400;
					var hours = Math.floor(totalSeconds / 3600);
					totalSeconds %= 3600;
					var minutes = Math.floor(totalSeconds / 60);
					var seconds = Math.floor(totalSeconds % 60);
					$year.text(years);
					$month.text(months);
					$day.text(days);
					$hour.text(hours);
					$min.text(minutes);
					$sec.text(seconds);
				}, 1e3);
			}, $countdownWidget.each(function() {
				$(this).MasterCountDownTimer();
			});
		},
		/**
		* Fancybox popup
		*/
		MA_Fancybox_Popup: function($scope, $) {
			(function($) {
				if ($.isFunction($.fn.fancybox)) $("[data-fancybox]").fancybox({});
			})(jQuery);
		},
		MA_Reveal: function($scope, $) {
			Master_Addons.MA_Reveal.elementSettings = getElementSettings($scope);
			var rev1;
			Master_Addons.MA_Reveal.revealAction = function() {
				rev1 = new RevealFx(revealistance, { revealSettings: {
					bgcolor: Master_Addons.MA_Reveal.elementSettings.reveal_bgcolor,
					direction: Master_Addons.MA_Reveal.elementSettings.reveal_direction,
					duration: Number(Master_Addons.MA_Reveal.elementSettings.reveal_speed.size) * 100,
					delay: Number(Master_Addons.MA_Reveal.elementSettings.reveal_delay.size) * 100,
					onCover: function(contentEl, revealerEl) {
						contentEl.style.opacity = 1;
					}
				} });
			};
			Master_Addons.MA_Reveal.runReveal = function() {
				rev1.reveal();
			};
			if (Master_Addons.MA_Reveal.elementSettings.enabled_reveal) {
				var revealId = "#reveal-" + $scope.data("id"), revealistance = document.querySelector(revealId);
				if (!jQuery(revealId).hasClass("block-revealer")) Master_Addons.MA_Reveal.revealAction();
				Master_Addons.MA_Reveal.waypointOptions = {
					offset: "100%",
					triggerOnce: true
				};
				jltMAObserveTarget(revealistance, Master_Addons.MA_Reveal.runReveal, Master_Addons.MA_Reveal.waypointOptions);
			}
		},
		MA_Rellax: function($scope, $) {
			var elementSettings = getElementSettings($scope);
			var rellax = null;
			$(window).on("resize", function() {
				if (rellax) {
					rellax.destroy();
					if (rellax) initRellax();
				}
			});
			var initRellax = function() {
				if (elementSettings.enabled_rellax) {
					if (typeof Rellax === "undefined") return;
					currentDevice = elementorFrontend.getCurrentDeviceMode();
					var setting_speed = "speed_rellax";
					var value_speed = 0;
					if (currentDevice != "desktop") setting_speed = "speed_rellax_" + currentDevice;
					var speed_setting = elementSettings[setting_speed];
					if (speed_setting && typeof speed_setting.size !== "undefined") value_speed = speed_setting.size;
					var rellaxId = "#rellax-" + $scope.data("id");
					if ($(rellaxId).length) try {
						rellax = new Rellax(rellaxId, { speed: value_speed });
					} catch (error) {}
				}
			};
			initRellax();
		},
		MA_Rellax_Final: function(panel, model, view) {
			Master_Addons.getElementSettings = getElementSettings($scope);
			var $scope = view.$el;
			$scope.find("#scene");
		},
		MA_Entrance_Animation: function($scope, $) {
			$scope = $scope || $(this);
			var $target = $scope.hasClass("jltma-appear-watch-animation") ? $scope : $scope.find(".jltma-appear-watch-animation"), hasAnimation = $("body").hasClass("jltma-page-animation");
			if (!$target.length) return;
			if (hasAnimation) document.body.addEventListener("JltmaPageAnimationDone", function(event) {
				$target.appearl({
					offset: "200px",
					insetOffset: "0px"
				}).one("appear", function(event, data) {
					this.classList.add("jltma-animated");
					this.classList.add("jltma-animated-once");
				});
			});
			else $target.appearl({
				offset: "200px",
				insetOffset: "0px"
			}).one("appear", function(event, data) {
				this.classList.add("jltma-animated");
				this.classList.add("jltma-animated-once");
			});
		},
		MA_Wrapper_Link: function($scope, $) {
			$("body").off("click.onWrapperLink", "[data-jltma-wrapper-link]");
			$("body").on("click.onWrapperLink", "[data-jltma-wrapper-link]", function(e) {
				e.preventDefault();
				e.stopPropagation();
				var $wrapper = $(this), data = $wrapper.data("jltma-wrapper-link"), id = $wrapper.data("id"), anchor = document.createElement("a");
				anchor.id = "master-addons-wrapper-link-" + id;
				anchor.href = data.url;
				anchor.target = data.is_external ? "_blank" : "_self";
				anchor.rel = data.nofollow ? "nofollow noreferer" : "";
				anchor.style.display = "none";
				document.body.appendChild(anchor);
				document.getElementById(anchor.id);
				if (data && data.url) if (data.is_external) window.open(data.url, "_blank", data.nofollow ? "noopener,noreferrer" : "noopener");
				else window.location.href = data.url;
			});
		},
		/**
		* Restrict Content
		*/
		MA_Restrict_Content_Ajax: function($scope, $) {
			Master_Addons.getElementSettings = getElementSettings($scope);
			var $restrictwrapper = $scope.find(".jltma-restrict-content-wrap").eq(0), $scopeId = $scope.data("id"), $restrict_layout = $restrictwrapper.data("restrict-layout-type"), $restrict_type = $restrictwrapper.data("restrict-type"), $error_message = $restrictwrapper.data("error-message");
			$restrictwrapper.data("rc-ajaxify");
			var $storageID = "ma_el_rc_" + $scopeId, $formID = $scope.find(".jltma-restrict-form").eq(0).data("form-id");
			"" + $scopeId;
			var $popup = $scope.find(".jltma-restrict-content-popup-content"), $content_pass = $restrictwrapper.data("content-pass") ? $restrictwrapper.data("content-pass") : "", $popup_type = $popup.data("popup-type") ? $popup.data("popup-type") : "", $age_wrapper = $scope.find(".jltma-restrict-age-wrapper").eq(0), $restrict_age = {
				min_age: $age_wrapper.data("min-age"),
				age_type: $age_wrapper.data("age-type"),
				age_title: $age_wrapper.data("age-title"),
				age_content: $age_wrapper.data("age-content"),
				age_submit: $("#" + $formID).find("button[name=\"submit\"]").val(),
				checkbox_msg: $age_wrapper.data("checkbox-msg") ? $age_wrapper.data("checkbox-msg") : "",
				empty_bday: $age_wrapper.data("empty-bday") ? $age_wrapper.data("empty-bday") : "",
				non_exist_bday: $age_wrapper.data("non-exist-bday") ? $age_wrapper.data("non-exist-bday") : ""
			};
			if (localStorage.getItem($storageID)) {
				$(".jltma-rc-button").addClass("d-none");
				$("#" + $formID).addClass("d-none");
				$("#jltma-restrict-age-" + $scopeId).removeClass("card");
				$("#jltma-restrict-age-" + $scopeId).removeClass("text-center");
				$("#restrict-content-" + $scopeId).addClass("d-block");
			} else {
				if ($restrict_layout == "popup") var dom_selector = "#jltma-rc-modal-" + $scopeId;
				else var dom_selector = "#jltma-restrict-content-" + $scopeId;
				$(dom_selector).on("click", ".jltma_ra_select", function() {
					var wrap = $(this).closest(".jltma_ra_select_wrap");
					if (!wrap.find(".jltma_ra_options").hasClass("jltma_ra_active")) {
						$(".jltma_ra_options").removeClass("jltma_ra_active");
						wrap.find(".jltma_ra_options").addClass("jltma_ra_active");
						wrap.find(".jltma_ra_options").find("li:contains(\"" + wrap.find(".jltma_ra_select_val").html() + "\")").addClass("jltma_ra_active");
					} else wrap.find(".jltma_ra_options").removeClass("jltma_ra_active");
				});
				$(dom_selector).on("click", ".jltma_ra_options ul li", function() {
					var wrap = $(this).closest(".jltma_ra_select_wrap");
					wrap.find(".jltma_ra_select_val").html($(this).html());
					wrap.find("select").val($(this).attr("data-val"));
					wrap.find(".jltma_ra_options").removeClass("jltma_ra_active");
				});
				$(dom_selector).on("mouseover", ".jltma_ra_options ul li", function() {
					if ($(".jltma_ra_options ul li").hasClass("jltma_ra_active")) $(".jltma_ra_options ul li").removeClass("jltma_ra_active");
				});
				$(document).click(function(e) {
					if ($(e.target).attr("class") != "jltma_ra_select" && !$(".jltma_ra_select").find($(e.target)).length) {
						if ($(".jltma_ra_options.jltma_ra_active").length) $(".jltma_ra_options").removeClass("jltma_ra_active");
					}
				});
				if ($popup_type == "windowload" || $popup_type == "windowloadfullscreen") $("#ma-el-rc-modal-hidden").fancybox().trigger("click");
				else $("[data-fancybox]").fancybox({});
				$(dom_selector).on("submit", "#" + $formID, function(event) {
					event.preventDefault();
					var form = $(this);
					form.find(".jltma_rc_result").remove();
					$.ajax({
						type: "POST",
						url: JLTMA_SCRIPTS.ajaxurl,
						data: {
							action: "jltma_restrict_content",
							nonce: JLTMA_SCRIPTS.nonce,
							fields: form.serialize(),
							restrict_type: $restrict_type,
							error_message: $error_message,
							content_pass: $content_pass,
							restrict_age: $restrict_age
						},
						cache: false,
						success: function(result) {
							try {
								result = jQuery.parseJSON(result);
								if (result["result"] == "success") {
									$("#restrict-content-" + $scopeId).removeClass("d-none").addClass("d-block");
									$("#" + $formID).addClass("d-none");
									$("#jltma-restrict-age-" + $scopeId).removeClass("card");
									$("#jltma-restrict-age-" + $scopeId).removeClass("text-center");
									localStorage.setItem($storageID, true);
									$.fancybox.close();
									$(".jltma-rc-button").addClass("d-none");
								} else if (result["result"] == "validate") $("#" + $formID + " .jltma_rc_submit").after("<div class=\"jltma_rc_result\"><span class=\"eicon-info-circle-o\"></span> " + result["output"] + "</div>");
								else throw 0;
							} catch (err) {
								$("#" + $formID + " .jltma_rc_submit").after("<div class=\"jltma_rc_result\"><span class=\"eicon-loading\"></span> Failed, please try again.</div>");
							}
						}
					});
				});
			}
		},
		MA_Restrict_Content: function($scope, $) {
			try {
				(function($) {
					Master_Addons.getElementSettings = getElementSettings($scope);
					var $restrictwrapper = $scope.find(".jltma-restrict-content-wrap").eq(0);
					$scope.data("id");
					$restrictwrapper.data("restrict-layout-type");
					$restrictwrapper.data("restrict-type");
					$scope.find(".jltma-restrict-content-popup-content");
					$restrictwrapper.data("content-pass");
					var $age_wrapper = $scope.find(".jltma-restrict-age-wrapper").eq(0);
					$age_wrapper.data("min-age");
					$age_wrapper.data("age-type");
					$age_wrapper.data("age-title");
					$age_wrapper.data("age-content");
					$age_wrapper.data("checkbox-msg");
					Master_Addons.MA_Restrict_Content_Ajax($scope, $);
				})(jQuery);
			} catch (e) {}
		},
		MA_Nav_Menu: function($scope, $) {
			Master_Addons.getElementSettings = getElementSettings($scope);
			var $menuContainer = $scope.find(".jltma-nav-menu-element"), $menuID = $menuContainer.data("menu-id"), $menu_type = $menuContainer.data("menu-layout");
			$menuContainer.data("menu-trigger");
			var $menu_offcanvas = $menuContainer.data("menu-offcanvas"), $menu_toggletype = $menuContainer.data("menu-toggletype"), $submenu_animation = $menuContainer.data("menu-animation"), $menu_container_id = $menuContainer.data("menu-container-id"), $sticky_type = $menuContainer.data("sticky-type"), navbar_height = $("#" + $menu_container_id).outerHeight(), menu_container_selector = $("#" + $menu_container_id);
			if ($menu_type == "onepage") {
				$(document).on("click", ".jltma-navbar-nav li a", function(e) {
					if ($(this).attr("href")) {
						var self = $(this), el = self.get(0), href = el.href, hasHash = href.indexOf("#"), enable = self.parents(".jltma-navbar-nav-default").hasClass("jltma-one-page-enabled");
						if (hasHash !== -1 && href.length > 1 && enable && el.pathname == window.location.pathname) {
							e.preventDefault();
							self.parents(".jltma-menu-container").find(".jltma-close").trigger("click");
						}
					}
				});
				$(document).on("click", function(e) {
					$(e.target);
					if ($(".navbar-collapse").hasClass("show") === true) $(".jltma-one-page-enabled").removeClass("show");
				});
			} else {
				"" + $submenu_animation;
				var submenu_selector = $(".jltma-dropdown.jltma-sub-menu");
				$("#" + $menuID + " .jltma-menu-has-children").hover(function() {
					if (submenu_selector.hasClass("fade-up")) submenu_selector.removeClass("fade-up");
					if (submenu_selector.hasClass("fade-down")) submenu_selector.removeClass("fade-down");
					$(".jltma-dropdown.jltma-sub-menu").addClass($submenu_animation);
				});
				if ($sticky_type == "fixed-onscroll") {
					if ($(window).width() > 768) $(function() {
						$(window).scroll(function() {
							if ($(window).scrollTop() >= 10) menu_container_selector.removeClass("" + $menu_container_id).addClass("jltma-on-scroll-fixed");
							else menu_container_selector.removeClass("jltma-on-scroll-fixed").addClass("" + $menu_container_id);
						});
					});
				}
				if ($sticky_type == "sticky-top") {
					if ($(window).width() > 768) $(function() {
						$(window).scroll(function() {
							if ($(window).scrollTop() >= 10) menu_container_selector.removeClass("" + $menu_container_id).addClass("sticky-top");
							else menu_container_selector.removeClass("sticky-top").addClass("" + $menu_container_id);
						});
					});
				}
				if ($sticky_type == "smart-scroll") {
					$("body").css("padding-top", navbar_height + "px");
					menu_container_selector.addClass("jltma-smart-scroll");
					if ($(".jltma-smart-scroll").length > 0) {
						var last_scroll_top = 0;
						$(window).on("scroll", function() {
							var scroll_top = $(this).scrollTop();
							if (scroll_top < last_scroll_top) $(".jltma-smart-scroll").removeClass("scrolled-down").addClass("scrolled-up");
							else $(".jltma-smart-scroll").removeClass("scrolled-up").addClass("scrolled-down");
							last_scroll_top = scroll_top;
						});
					}
				}
				if ($sticky_type == "nav-fixed-top") {
					if ($(window).width() > 768) $(function() {
						$("body").css("padding-top", navbar_height + "px");
						menu_container_selector.addClass("jltma-fixed-top");
					});
				}
				if ($menu_toggletype == "toggle") $("#" + $menuID + " .navbar-nav.toggle .jltma-menu-dropdown-toggle").click(function(e) {
					$(this).parents(".dropdown").toggleClass("open");
					e.stopPropagation();
				});
				if ($menu_offcanvas == "toggle-bar") $(".jltma-nav-panel .navbar-toggler").on("click", function(e) {
					$(".jltma-burger").toggleClass("jltma-close");
				});
				if ($menu_offcanvas == "offcanvas" || $menu_offcanvas == "overlay") {
					$(".jltma-nav-panel .navbar-toggler").on("click", function(e) {
						e.preventDefault();
						e.stopPropagation();
						$($(this).attr("data-trigger")).toggleClass("show");
						$("body").toggleClass("offcanvas-active");
						$(".jltma-nav-panel ").toggleClass("offcanvas-nav");
						if ($menu_offcanvas == "overlay") $(".jltma-nav-panel ").toggleClass("offcanvas-overlay");
					});
					$(document).on("keydown", function(event) {
						if (event.keyCode === 27) {
							$(".mobile-offcanvas").removeClass("show");
							$(".desktop-offcanvas").removeClass("show");
							$("body").removeClass("overlay-active");
						}
					});
					$(".btn-close, .jltma-nav-panel .offcanvas-nav, .jltma-nav-panel.desktop .jltma-close, .jltma-close").click(function(e) {
						$(".jltma-nav-panel ").removeClass("offcanvas-nav");
						$(".mobile-offcanvas").removeClass("show");
						$(".desktop-offcanvas").removeClass("show");
						$("body").removeClass("offcanvas-active");
						if ($menu_offcanvas == "overlay") $(".jltma-nav-panel ").removeClass("offcanvas-overlay");
					});
				}
			}
		},
		initEvents: function($scope, $) {
			$scope.find(".jltma-search-wrapper").eq(0).data("search-type");
			var mainContainer = $scope.find(".jltma-search-main-wrap"), openCtrl = document.getElementById("jltma-btn-search"), closeCtrl = document.getElementById("jltma-btn-search-close"), searchContainer = $scope.find(".jltma-search"), inputSearch = searchContainer.find(".jltma-search__input");
			$(openCtrl).on("click", function() {
				mainContainer.addClass("main-wrap--move");
				searchContainer.addClass("search--open");
				setTimeout(function() {
					inputSearch.focus();
				}, 600);
			});
			$(closeCtrl).on("click", function() {
				mainContainer.removeClass("main-wrap--move");
				searchContainer.removeClass("search--open");
				inputSearch.blur();
				inputSearch.value = "";
			});
			document.addEventListener("keyup", function(ev) {
				if (ev.keyCode == 27) Master_Addons.closeSearch();
			});
		},
		MA_Header_Search: function($scope, $) {
			$("body").addClass("js");
			Master_Addons.initEvents($scope, $);
		}
	};
	function filter_fancy_box(element) {
		$(element).find(".jltma-fancybox").each(function() {
			const rawCaption = $(this).data("caption");
			function decodeEntities(str) {
				if (!str) return "";
				const txt = document.createElement("textarea");
				txt.innerHTML = str;
				return txt.value;
			}
			const caption = decodeEntities(rawCaption);
			const hasDangerousAttr = /\son\w+\s*=/i.test(caption);
			const hasScriptTag = /<\s*script/i.test(caption);
			const hasJsProto = /javascript:/i.test(caption);
			if (caption && (hasDangerousAttr || hasScriptTag || hasJsProto)) {
				$(this).attr("data-caption", "");
				$(this).closest(".elementor-element").remove();
			}
		});
	}
	$(document).ready(function() {
		filter_fancy_box(document.body);
		new MutationObserver((mutations) => {
			mutations.forEach((mutation) => {
				mutation.addedNodes.forEach((node) => {
					if (node.nodeType === 1) filter_fancy_box(node);
				});
			});
		}).observe(document.body, {
			childList: true,
			subtree: true
		});
	});
	$(window).on("elementor/frontend/init", function() {
		if (elementorFrontend.isEditMode()) {}
		elementorFrontend.hooks.addAction("frontend/element_ready/global", Master_Addons.MA_AnimatedGradient);
		elementorFrontend.hooks.addAction("frontend/element_ready/container", Master_Addons.MA_AnimatedGradient);
		elementorFrontend.hooks.addAction("frontend/element_ready/global", Master_Addons.MA_BgSlider);
		elementorFrontend.hooks.addAction("frontend/element_ready/container", Master_Addons.MA_BgSlider);
		elementorFrontend.hooks.addAction("frontend/element_ready/global", Master_Addons.MA_ParticlesBG);
		elementorFrontend.hooks.addAction("frontend/element_ready/container", Master_Addons.MA_ParticlesBG);
		elementorFrontend.hooks.addAction("frontend/element_ready/global", Master_Addons.MA_Reveal);
		elementorFrontend.hooks.addAction("frontend/element_ready/global", Master_Addons.MA_Rellax);
		elementorFrontend.hooks.addAction("frontend/element_ready/global", Master_Addons.MA_Wrapper_Link);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-headlines.default", Master_Addons.MA_Animated_Headlines);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-advanced-accordion.default", Master_Addons.MA_Accordion);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-tabs.default", Master_Addons.MA_Tabs);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-progressbar.default", Master_Addons.MA_ProgressBar);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-team-members-slider.default", Master_Addons.MA_TeamSlider);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-image-carousel.default", Master_Addons.MA_Image_Carousel);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-blog-post.default", Master_Addons.MA_Blog);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-news-ticker.default", Master_Addons.MA_NewsTicker);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-el-countdown-timer.default", Master_Addons.MA_CountdownTimer);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-counter-up.default", Master_Addons.MA_Counter_Up);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-piecharts.default", Master_Addons.MA_PieCharts);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-timeline.default", Master_Addons.MA_Timeline);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-image-filter-gallery.default", Master_Addons.MA_Image_Filter_Gallery);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-gallery-slider.default", Master_Addons.MA_Gallery_Slider);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-el-image-comparison.default", Master_Addons.MA_Image_Comparison);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-el-restrict-content.default", Master_Addons.MA_Restrict_Content);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-search.default", Master_Addons.MA_Header_Search);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-progressbars.default", Master_Addons.ProgressBars);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-instagram-feed.default", Master_Addons.MA_Instagram_Feed);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-toggle-content.default", Master_Addons.MA_Toggle_Content);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-comments.default", Master_Addons.MA_Comment_Form_reCaptcha);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-logo-slider.default", Master_Addons.MA_Logo_Slider);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-twitter-slider.default", Master_Addons.MA_Twitter_Slider);
		elementorFrontend.hooks.addAction("frontend/element_ready/jltma-advanced-image.default", Master_Addons.MA_Advanced_Image);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-tooltip.default", Master_Addons.MA_Tooltip);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-image-hotspot.default", Master_Addons.MA_Image_Hotspot);
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-pricing-table.default", Master_Addons.MA_Pricing_Table);
		if (elementorFrontend.isEditMode()) {
			elementorFrontend.hooks.addAction("frontend/element_ready/ma-headlines.default", Master_Addons.MA_Animated_Headlines);
			elementorFrontend.hooks.addAction("frontend/element_ready/ma-piecharts.default", Master_Addons.MA_PieCharts);
			elementorFrontend.hooks.addAction("frontend/element_ready/ma-progressbars.default", Master_Addons.ProgressBars);
			elementorFrontend.hooks.addAction("frontend/element_ready/ma-progressbar.default", Master_Addons.MA_ProgressBar);
			elementorFrontend.hooks.addAction("frontend/element_ready/ma-news-ticker.default", Master_Addons.MA_NewsTicker);
			elementorFrontend.hooks.addAction("frontend/element_ready/jltma-gallery-slider.default", Master_Addons.MA_Gallery_Slider);
			elementorFrontend.hooks.addAction("frontend/element_ready/jltma-counter-up.default", Master_Addons.MA_Counter_Up);
			elementorFrontend.hooks.addAction("frontend/element_ready/ma-tooltip.default", Master_Addons.MA_Tooltip);
		}
	});
})(jQuery);
//#endregion
})();
