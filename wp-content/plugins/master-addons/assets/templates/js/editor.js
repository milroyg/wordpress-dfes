! function(e) {
    "use strict";
    var t, a, o, n, i = window.MasterAddonsData || {};
    a = {
        ModalLayoutView: null,
        ModalHeaderView: null,
        ModalHeaderInsertButton: null,
        ModalLoadingView: null,
        ModalBodyView: null,
        ModalErrorView: null,
        LibraryCollection: null,
        KeywordsModel: null,
        ModalCollectionView: null,
        ModalTabsCollection: null,
        ModalTabsCollectionView: null,
        FiltersCollectionView: null,
        FiltersItemView: null,
        ModalTabsItemView: null,
        ModalTemplateItemView: null,
        ModalInsertTemplateBehavior: null,
        ModalTemplateModel: null,
        CategoriesCollection: null,
        ModalPreviewView: null,
        ModalHeaderBack: null,
        ModalHeaderLogo: null,
        MasterProButton: null,
        KeywordsView: null,
        TabModel: null,
        CategoryModel: null,
        init: function() {
            var a = this;
            a.ModalTemplateModel = Backbone.Model.extend({
                defaults: {
                    template_id: 0,
                    name: "",
                    title: "",
                    thumbnail: "",
                    preview: "",
                    source: "",
                    categories: [],
                    keywords: [],
                    liveUrl: "",
                    package: ""
                }
            }), a.ModalHeaderView = Marionette.LayoutView.extend({
                id: "ma-el-template-modal-header",
                template: "#views-ma-el-template-modal-header",
                ui: {
                    closeModal: "#ma-el-template-modal-header-close-modal"
                },
                events: {
                    "click @ui.closeModal": "onCloseModalClick"
                },
                regions: {
                    headerLogo: "#ma-el-template-modal-header-logo-area",
                    headerTabs: "#ma-el-template-modal-header-tabs",
                    headerActions: "#ma-el-template-modal-header-actions"
                },
                onCloseModalClick: function() {
                    // Add elementor-editor-active class to body when closing modal
                    e('body').removeClass('elementor-editor-preview').addClass('elementor-editor-active');
                    t.closeModal()
                }
            }), a.TabModel = Backbone.Model.extend({
                defaults: {
                    slug: "",
                    title: ""
                }
            }), a.LibraryCollection = Backbone.Collection.extend({
                model: a.ModalTemplateModel
            }), a.ModalTabsCollection = Backbone.Collection.extend({
                model: a.TabModel
            }), a.CategoryModel = Backbone.Model.extend({
                defaults: {
                    slug: "",
                    title: ""
                }
            }), a.KeywordsModel = Backbone.Model.extend({
                defaults: {
                    keywords: {}
                }
            }), a.CategoriesCollection = Backbone.Collection.extend({
                model: a.CategoryModel
            }), a.KeywordsView = Marionette.ItemView.extend({
                id: "elementor-template-library-filter",
                template: "#views-ma-el-template-modal-keywords",
                onRender: function() {
                    var self = this;
                    // Add click handler for keywords filter action button
                    setTimeout(function() {
                        jQuery(".ma-el-keywords-filter-action").off("click").on("click", function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            jQuery(".ma-el-keywords-filters-container").toggleClass("active");
                        });
                        
                        // Add click handler for keyword items
                        jQuery(".ma-el-keywords-filter-item").off("click").on("click", function(e) {
                            e.preventDefault();
                            var keywordValue = jQuery(this).data("keyword");
                            var keywordTitle = jQuery(this).text();
                            
                            // Update filter
                            t.setFilter("keyword", keywordValue);
                            
                            // Update selected text
                            jQuery("#ma-el-keywords-selected-filter").text(keywordTitle);
                            
                            // Remove active class
                            jQuery(".ma-el-keywords-filters-container").removeClass("active");
                        });
                        
                        // Close dropdown when clicking outside
                        jQuery(document).off("click.keywordsDropdown").on("click.keywordsDropdown", function(e) {
                            if (!jQuery(e.target).closest(".ma-el-keywords-filters-wrap").length) {
                                jQuery(".ma-el-keywords-filters-container").removeClass("active");
                            }
                        });
                    }, 100);
                }
            }), a.ModalPreviewView = Marionette.ItemView.extend({
                template: "#views-ma-el-template-modal-preview",
                id: "ma-el-item-preview-wrap",
                ui: {
                    iframe: "iframe",
                    notice: ".ma-el-item-notice"
                },
                onRender: function() {
                    if (null !== this.getOption("notice") && this.getOption("notice").length) {
                        var e = ""; - 1 !== this.getOption("notice").indexOf("facebook") ? e += "<p>Please login with your Facebook account in order to get your Facebook Reviews.</p>" : -1 !== this.getOption("notice").indexOf("google") ? e += "<p>You need to add your Google API key from Dashboard -> Master Addons for Elementor -> Google Maps</p>" : -1 !== this.getOption("notice").indexOf("form") && (e += "<p>You need to have <a href='https://wordpress.org/plugins/contact-form-7/' target='_blank'>Contact Form 7 plugin</a> installed and active.</p>"), this.ui.notice.html("<div><p><strong>Important!</strong></p>" + e + "</div>")
                    }
                    this.ui.iframe.attr("src", this.getOption("url"))
                }
            }), a.ModalHeaderBack = Marionette.ItemView.extend({
                template: "#views-ma-el-template-modal-header-back",
                id: "ma-el-template-modal-header-back",
                ui: {
                    button: "button"
                },
                events: {
                    "click @ui.button": "onBackClick"
                },
                onBackClick: function() {
                    t.setPreview("back")
                }
            }), a.ModalHeaderLogo = Marionette.ItemView.extend({
                template: "#views-ma-el-template-modal-header-logo",
                id: "ma-el-template-modal-header-logo"
            }), a.ModalBodyView = Marionette.LayoutView.extend({
                template: "#views-ma-el-template-modal-content",
                id: "ma-el-template-library-content",
                className: function() {
                    return "library-tab-" + t.getTab()
                },
                regions: {
                    contentTemplates: ".ma-el-templates-list",
                    contentFilters: ".ma-el-filters-list",
                    contentKeywords: ".ma-el-keywords-list"
                },
                onRender: function() {
                    // Add search functionality
                    var self = this;
                    setTimeout(function() {
                        jQuery("#ma-el-template-search-input").off("input").on("input", function() {
                            var searchTerm = jQuery(this).val().toLowerCase();
                            // Trigger search filter
                            t.setFilter("search", searchTerm);
                        });
                    }, 100);
                }
            }), a.LibraryLoadingView = Marionette.ItemView.extend({
                id: "ma-el-modal-template-library-loading",
                template: "#views-ma-el-template-modal-loading"
            }), a.LibraryErrorView = Marionette.ItemView.extend({
                id: "ma-el-modal-template-error",
                template: "#views-ma-el-template-modal-error"
            }), a.ModalInsertTemplateBehavior = Marionette.Behavior.extend({
                ui: {
                    insertButton: ".ma-el-template-insert"
                },
                events: {
                    "click @ui.insertButton": "onInsertButtonClick"
                },
                onInsertButtonClick: function() {
                    var a = this.view.model,
                        o = a.attributes.dependencies,
                        n = a.attributes.pro,
                        l = Object.keys(o).length,
                        r = {};
                    // console.log(a);
                    if (t.layout.showLoadingView(), 0 < l)
                        for (var s in o) e.ajax({
                            url: ajaxurl,
                            type: "post",
                            dataType: "json",
                            data: {
                                action  : "jltma_inner_template",
                                security: MasterAddonsData.insert_template_nonce,
                                template: o[s],
                                tab     : t.getTab()
                            }
                        });
                    "valid" !== i.license.status && n ? t.layout.showLicenseError() : elementor.templates.requestTemplateContent(a.get("source"), a.get("template_id"), {
                        data: {
                            tab: t.getTab(),
                            page_settings: !1
                        },
                        success: function(e) {
                            e.license ? (console.log("%c !", "color: #7a7a7a; background-color: #eee;"), t.closeModal(), elementor.channels.data.trigger("$e.run( 'document/import' )", a), null !== t.atIndex && (r.at = t.atIndex), elementor.config.version < "3.0.0" ? elementor.sections.currentView.addChildModel(e.content, r) : elementor.previewView.addChildModel(e.content, r), elementor.channels.data.trigger("template:after:insert", a), t.atIndex = null) : t.layout.showLicenseError()
                            jQuery('body').removeClass('elementor-editor-preview').addClass('elementor-editor-active');
                        },
                        error: function(e) {
                            console.log(e);
                        }
                    })
                }
            }), a.ModalHeaderInsertButton = Marionette.ItemView.extend({
                template: "#views-ma-el-template-modal-insert-button",
                id: "jltma-template-modal-insert-wrapper",
                className: "elementor-template-library-template-action jltma-modal-template-header-item",
                behaviors: {
                    insertTemplate: {
                        behaviorClass: a.ModalInsertTemplateBehavior
                    }
                }
            }), a.MasterProButton = Marionette.ItemView.extend({
                template: "#views-ma-el-template-pro-button",
                id: "ma-el-modal-template-pro-button"
            }), a.ModalTemplateItemView = Marionette.ItemView.extend({
                template: "#views-ma-el-template-modal-item",
                className: function() {
                    var e = " ma-el-modal-template-has-url",
                        t = "";
                    return "" === this.model.get("preview") && (e = " ma-el-modal-template-no-url"), this.model.get("pro") && "valid" != i.license.status && (t = " ma-el-modal-template-pro"), "elementor-template-library-template elementor-template-library-template-remote" + e + t
                },
                ui: function() {
                    return {
                        previewButton: ".elementor-template-library-template-preview"
                    }
                },
                events: function() {
                    return {
                        "click @ui.previewButton": "onPreviewButtonClick"
                    }
                },
                onPreviewButtonClick: function() {
                    "" !== this.model.get("url") && t.setPreview(this.model)
                },
                behaviors: {
                    insertTemplate: {
                        behaviorClass: a.ModalInsertTemplateBehavior
                    }
                }
            }), a.FiltersItemView = Marionette.ItemView.extend({
                template: "#views-ma-el-template-modal-filters-item",
                tagName: "li",
                className: "ma-el-modal-template-filter-item",
                attributes: function() {
                    return {
                        "data-filter": this.model.get("slug")
                    }
                },
                events: function() {
                    return {
                        "click": "onFilterClick"
                    }
                },
                onFilterClick: function(e) {
                    var filterValue = this.model.get("slug");
                    var filterTitle = this.model.get("title");
                    jQuery(".ma-el-library-keywords").val(""), t.setFilter("category", filterValue), t.setFilter("keyword", "");
                    // Update selected filter text
                    jQuery("#ma-el-modal-selected-filter").text(filterTitle);
                    // Remove active class
                    jQuery(".ma-el-modal-filters-container").removeClass("active");
                }
            }), a.ModalTabsItemView = Marionette.ItemView.extend({
                template: "#views-ma-el-template-modal-tabs-item",
                className: function() {
                    return "elementor-template-library-menu-item"
                },
                ui: function() {
                    return {
                        tabsLabels: "label",
                        tabsInput: "input"
                    }
                },
                events: function() {
                    return {
                        "click @ui.tabsLabels": "onTabClick"
                    }
                },
                onRender: function() {
                    this.model.get("slug") === t.getTab() && this.ui.tabsInput.attr("checked", "checked")
                },
                onTabClick: function(e) {
                    var a = jQuery(e.target);
                    t.setTab(a.val()), t.setFilter("keyword", "")
                }
            }), a.FiltersCollectionView = Marionette.CompositeView.extend({
                id: "ma-el-modal-template-library-filters",
                template: "#views-ma-el-template-modal-filters",
                childViewContainer: "#ma-el-modal-filters-container ul",
                getChildView: function(e) {
                    return a.FiltersItemView
                },
                onRender: function() {
                    // Add click handler for filter action button
                    var self = this;
                    setTimeout(function() {
                        jQuery(".ma-el-modal-filter-action").off("click").on("click", function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            jQuery(".ma-el-modal-filters-container").toggleClass("active");
                        });
                        // Close dropdown when clicking outside
                        jQuery(document).off("click.filterDropdown").on("click.filterDropdown", function(e) {
                            if (!jQuery(e.target).closest(".ma-el-modal-filters-wrap").length) {
                                jQuery(".ma-el-modal-filters-container").removeClass("active");
                            }
                        });
                    }, 100);
                }
            }), a.ModalTabsCollectionView = Marionette.CompositeView.extend({
                template: "#views-ma-el-template-modal-tabs",
                childViewContainer: "#views-ma-el-template-modal-tabs-items",
                initialize: function() {
                    this.listenTo(t.channels.layout, "tamplate:cloned", this._renderChildren)
                },
                getChildView: function(e) {
                    return a.ModalTabsItemView
                }
            }), a.ModalCollectionView = Marionette.CompositeView.extend({
                template: "#views-ma-el-template-modal-templates",
                id: "ma-el-modal-template-library-templates",
                childViewContainer: "#ma-el-modal-templates-container",
                initialize: function() {
                    this.listenTo(t.channels.templates, "filter:change", this._renderChildren)
                },
                filter: function(e) {
                    var a = t.getFilter("category"),
                        o = t.getFilter("keyword"),
                        s = t.getFilter("search");
                    
                    // Check category and keyword filters
                    var matchesFilters = !a && !o || (o && !a ? _.contains(e.get("keywords"), o) : a && !o ? _.contains(e.get("categories"), a) : _.contains(e.get("categories"), a) && _.contains(e.get("keywords"), o));
                    
                    // Apply search filter if present
                    if (s && s.length > 0) {
                        var title = (e.get("title") || e.get("name") || "").toLowerCase();
                        var matchesSearch = title.indexOf(s) !== -1;
                        return matchesFilters && matchesSearch;
                    }
                    
                    return matchesFilters;
                },
                getChildView: function(e) {
                    return a.ModalTemplateItemView
                },
                onRenderCollection: function() {
                    var self = this;
                    var e = this.$childViewContainer,
                        n = t.getTab();

                    // Skip masonry - now handled by Macy instance in assets.php
                    // This prevents double initialization and conflicting breakpoints
                    console.log("Skipping Marionette masonry initialization - Macy handles layout");
                    return;

                    // DISABLED CODE BELOW - Macy from assets.php now handles all layout
                    /*
                    // Skip masonry for master_page and local tabs
                    if ("master_page" === n || "local" === n) {
                        return;
                    }

                    // Use a timeout to ensure DOM is ready
                    setTimeout(function() {
                        // Get fresh children after DOM is ready
                        var o = e.children();

                        if (o.length === 0) {
                            console.log("No template items found in container");
                            return;
                        }

                        console.log("Found " + o.length + " template items");

                        // Wait for images to load before applying masonry
                        e.imagesLoaded(function() {
                            a.masonry.init({
                                container: e,
                                items: o
                            });
                        }).fail(function() {
                            // If images fail to load, still apply masonry
                            console.log("Some images failed to load, still applying masonry");
                            a.masonry.init({
                                container: e,
                                items: o
                            });
                        });
                    }, 100);
                    */
                }
            }), a.ModalLoadingView = Marionette.ItemView.extend({
                id: "ma-el-modal-loading",
                template: "#views-ma-el-template-modal-loading"
            }), a.ModalErrorView = Marionette.ItemView.extend({
                id: "ma-el-modal-loading",
                template: "#views-ma-el-template-modal-error"
            }), a.ModalLayoutView = Marionette.LayoutView.extend({
                el: "#ma-el-modal-template",
                regions: i.modalRegions,
                initialize: function() {
                    this.getRegion("modalHeader").show(new a.ModalHeaderView), this.listenTo(t.channels.tabs, "filter:change", this.switchTabs), this.listenTo(t.channels.layout, "preview:change", this.switchPreview)
                },
                switchTabs: function() {
                    this.showLoadingView(), t.setFilter("keyword", ""), t.requestTemplates(t.getTab())
                },
                switchPreview: function() {
                    var e = this.getHeaderView(),
                        o = t.getPreview(),
                        n = t.getFilter("category"),
                        i = t.getFilter("keyword");
                    return "back" === o ? (e.headerLogo.show(new a.ModalHeaderLogo), e.headerTabs.show(new a.ModalTabsCollectionView({
                        collection: t.collections.tabs
                    })), e.headerActions.empty(), t.setTab(t.getTab()), "" != n && (t.setFilter("category", n), jQuery("#ma-el-modal-filters-container").find("input[value='" + n + "']").prop("checked", !0)), void("" != i && t.setFilter("keyword", i))) : "initial" === o ? (e.headerActions.empty(), void e.headerLogo.show(new a.ModalHeaderLogo)) : (this.getRegion("modalContent").show(new a.ModalPreviewView({
                        preview: o.get("preview"),
                        url: o.get("url"),
                        notice: o.get("notice")
                    })), e.headerLogo.empty(), e.headerTabs.show(new a.ModalHeaderBack), void e.headerActions.show(new a.ModalHeaderInsertButton({
                        model: o
                    })))
                },
                getHeaderView: function() {
                    return this.getRegion("modalHeader").currentView
                },
                getContentView: function() {
                    return this.getRegion("modalContent").currentView
                },
                showLoadingView: function() {
                    this.modalContent.show(new a.ModalLoadingView)
                },
                showLicenseError: function() {
                    this.modalContent.show(new a.ModalErrorView)
                },
                showTemplatesView: function(e, o, n) {
                    this.getRegion("modalContent").show(new a.ModalBodyView);
                    var i = this.getContentView(),
                        l = this.getHeaderView(),
                        r = new a.KeywordsModel({
                            keywords: n
                        });
                    t.collections.tabs = new a.ModalTabsCollection(t.getTabs()), l.headerTabs.show(new a.ModalTabsCollectionView({
                        collection: t.collections.tabs
                    })), i.contentTemplates.show(new a.ModalCollectionView({
                        collection: e
                    })), i.contentFilters.show(new a.FiltersCollectionView({
                        collection: o
                    })), i.contentKeywords.show(new a.KeywordsView({
                        model: r
                    }))
                }
            })
        },
        masonry: {
            instance: null,
            init: function(t) {
                // DISABLED: Masonry layout now handled by Macy instance in assets.php
                // This prevents conflicting breakpoints (520: 2 vs 520: 1)
                console.log('Marionette masonry.init() called but disabled - Macy from assets.php handles layout');
                return;

                // ORIGINAL CODE BELOW - DISABLED
                /*
                var self = this;

                if (self.instance && typeof self.instance.remove === 'function') {
                    self.instance.remove();
                    self.instance = null;
                }

                // Check if Macy is available in the global scope
                if (typeof window.Macy === 'undefined') {
                    console.warn('Macy library not loaded, using fallback masonry layout');
                    self.fallbackMasonry(t);
                    return;
                }

                // Get the DOM element from jQuery object
                var containerElement = t.container && t.container.length ? t.container[0] : null;

                if (!containerElement) {
                    console.error('Container element not found for Macy initialization', t.container);
                    return;
                }


                // Initialize Macy with the container element
                try {
                    self.instance = window.Macy({
                        container: containerElement,
                        waitForImages: true,
                        margin: 30,
                        columns: 5,
                        trueOrder: false,
                        breakAt: {
                            1370: 4,
                            940: 3,
                            520: 2,
                            400: 1
                        }
                    });

                    // Force immediate recalculation
                    if (self.instance && typeof self.instance.reInit === 'function') {
                        self.instance.reInit();
                    }

                    setTimeout(function() {
                        if (self.instance && typeof self.instance.recalculate === 'function') {
                            self.instance.recalculate(true);
                        }
                    }, 300);

                    setTimeout(function() {
                        if (self.instance && typeof self.instance.recalculate === 'function') {
                            self.instance.recalculate(true);
                        }
                    }, 600);
                } catch (error) {
                    console.error('Error initializing Macy:', error);
                    self.fallbackMasonry(t);
                }
                */
            },
            fallbackMasonry: function(t) {
                console.log("Hello");
                var self = this;
                self.settings = e.extend(self.getDefaultSettings(), t);
                self.elements = self.getDefaultElements();
                self.runFallback();
            },
            getDefaultSettings: function() {
                return {
                    container: null,
                    items: null,
                    columnsCount: 3,
                    verticalSpaceBetween: 30
                }
            },
            getDefaultElements: function() {
                return {
                    $container: jQuery(this.settings.container),
                    $items: jQuery(this.settings.items)
                }
            },
            runFallback: function() {
                var e = [],
                    t = this.elements.$container.position().top,
                    a = this.settings,
                    o = a.columnsCount;
                t += parseInt(this.elements.$container.css("margin-top"), 10);
                this.elements.$container.height("");
                this.elements.$items.each(function(n) {
                    var i = Math.floor(n / o),
                        l = n % o,
                        r = jQuery(this),
                        s = r.position(),
                        d = r[0].getBoundingClientRect().height + a.verticalSpaceBetween;
                    if (i) {
                        var m = s.top - t - e[l];
                        m -= parseInt(r.css("margin-top"), 10);
                        m *= -1;
                        r.css("margin-top", m + "px");
                        e[l] += d;
                    } else {
                        e.push(d);
                    }
                });
                this.elements.$container.height(Math.max.apply(Math, e));
            }
        }
    }, t = {
        modal: !(n = {
            getDataToSave: function(e) {
                return e.id = window.elementor.config.post_id, e
            },
            init: function() {
                window.elementor.settings.master_template && (window.elementor.settings.master_template.getDataToSave = this.getDataToSave), window.elementor.settings.master_page && (window.elementor.settings.master_page.getDataToSave = this.getDataToSave, window.elementor.settings.master_page.changeCallbacks = {
                    custom_header: function() {
                        this.save(function() {
                            elementor.reloadPreview(), elementor.once("preview:loaded", function() {
                                elementor.getPanelView().setPage("master_page_settings")
                            })
                        })
                    },
                    custom_footer: function() {
                        this.save(function() {
                            elementor.reloadPreview(), elementor.once("preview:loaded", function() {
                                elementor.getPanelView().setPage("master_page_settings")
                            })
                        })
                    }
                })
            }
        }),
        layout: !(o = {
            MasterSearchView: null,
            init: function() {
                this.MasterSearchView = window.elementor.modules.controls.BaseData.extend({
                    onReady: function() {
                        var t = this.model.attributes.action,
                            a = this.model.attributes.query_params;
                        this.ui.select.find("option").each(function(t, a) {
                            e(this).attr("selected", !0)
                        }), this.ui.select.select2({
                            ajax: {
                                url: function() {
                                    var o = "";
                                    return 0 < a.length && e.each(a, function(e, t) {
                                        window.elementor.settings.page.model.attributes[t] && (o += "&" + t + "=" + window.elementor.settings.page.model.attributes[t])
                                    }), ajaxurl + "?action=" + t + o
                                },
                                dataType: "json"
                            },
                            placeholder: "Please enter 3 or more characters",
                            minimumInputLength: 3
                        })
                    },
                    onBeforeDestroy: function() {
                        this.ui.select.data("select2") && this.ui.select.select2("destroy"), this.$el.remove()
                    }
                }), window.elementor.addControlView("master_search", this.MasterSearchView)
            }
        }),
        collections: {},
        tabs: {},
        defaultTab: "",
        channels: {},
        atIndex: null,
        init: function() {
            window.elementor.on("preview:loaded", window._.bind(t.onPreviewLoaded, t)), a.init(), o.init(), n.init()
        },
        onPreviewLoaded: function() {
            let e = setInterval(() => {
                window.elementor.$previewContents.find(".elementor-add-new-section").length && (this.initMasterTempsButton(), clearInterval(e))
            }, 100);
            window.elementor.$previewContents.on("click.addMasterTemplate", ".ma-el-add-section-btn", _.bind(function() {
                // Toggle editor classes on button click
                this.toggleEditorMode();
                // Also show the templates modal
                this.showTemplatesModal();
            }, this)), this.channels = {
                templates: Backbone.Radio.channel("MASTER_EDITOR:templates"),
                tabs: Backbone.Radio.channel("MASTER_EDITOR:tabs"),
                layout: Backbone.Radio.channel("MASTER_EDITOR:layout")
            }, this.tabs = i.tabs, this.defaultTab = i.defaultTab
        },
        initMasterTempsButton: function() {
            var a = window.elementor.$previewContents.find(".elementor-add-new-section"),
                o = '<div class="elementor-add-section-area-button ma-el-add-section-btn"><div class="jltma-editor-icon"></div></div>';

            // Insert button after AI button if it exists, otherwise prepend
            a.length && i.MasterAddonsEditorBtn && a.each(function() {
                var $section = e(this);
                var $aiButton = $section.find('.e-ai-layout-button');
                if ($aiButton.length) {
                    e(o).insertAfter($aiButton);
                } else {
                    e(o).prependTo($section);
                }
            });

            window.elementor.$previewContents.on("click.addMasterTemplate", ".elementor-editor-section-settings .elementor-editor-element-add", function() {
                t.toggleEditorMode();
                var a = e(this).closest(".elementor-top-section"),
                    n = a.data("model-cid");
                elementor.config.version < "3.0.0" ? window.elementor.sections.currentView.collection.length && e.each(window.elementor.sections.currentView.collection.models, function(e, a) {
                    n === a.cid && (t.atIndex = e)
                }) : elementor.previewView.collection.length && e.each(elementor.previewView.collection.models, function(e, a) {
                    n === a.cid && (t.atIndex = e)
                });

                if (i.MasterAddonsEditorBtn) {
                    var $addSection = a.prev(".elementor-add-section").find(".elementor-add-new-section");
                    var $aiButton = $addSection.find('.e-ai-layout-button');
                    if ($aiButton.length) {
                        e(o).insertAfter($aiButton);
                    } else {
                        $addSection.prepend(o);
                    }
                }
            })
        },
        getFilter: function(e) {
            return this.channels.templates.request("filter:" + e)
        },
        setFilter: function(e, t) {
            this.channels.templates.reply("filter:" + e, t), this.channels.templates.trigger("filter:change")
        },
        getTab: function() {
            return this.channels.tabs.request("filter:tabs")
        },
        setTab: function(e, t) {
            this.channels.tabs.reply("filter:tabs", e), t || this.channels.tabs.trigger("filter:change")
        },
        getTabs: function() {
            var e = [];
            return _.each(this.tabs, function(t, a) {
                e.push({
                    slug: a,
                    title: t.title
                })
            }), e
        },
        getPreview: function(e) {
            return this.channels.layout.request("preview")
        },
        setPreview: function(e, t) {
            this.channels.layout.reply("preview", e), t || this.channels.layout.trigger("preview:change")
        },
        getKeywords: function() {
            return _.each(this.keywords, function(e, t) {
                tabs.push({
                    slug: t,
                    title: e
                })
            }), []
        },
        showTemplatesModal: function() {
            jQuery('body').addClass("master-addons-template-popup-loaded");
            var modal = this.getModal();
            modal.show();
            // Prevent outside click from closing the modal
            setTimeout(function() {
                // Remove click events from overlay/mask
                e('.dialog-widget-content').off('click');
                e('.dialog-lightbox-widget').off('click');
                e('.dialog-lightbox-widget-content').off('click');
                // Prevent clicks on overlay from propagating
                e('.dialog-widget-content, .dialog-lightbox-widget').on('click', function(event) {
                    if (event.target === this) {
                        event.stopPropagation();
                        event.preventDefault();
                        return false;
                    }
                });
            }, 100);
            this.layout || (this.layout = new a.ModalLayoutView, this.layout.showLoadingView()), this.setTab(this.defaultTab, !0), this.requestTemplates(this.defaultTab), this.setPreview("initial")
        },
        requestTemplates: function(t) {
            var o = this,
                n = o.tabs[t];
            o.setFilter("category", !1), n.data.templates && n.data.categories ? o.layout.showTemplatesView(n.data.templates, n.data.categories, n.data.keywords) : e.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: {
                    action: "jltma_get_templates",
                    security: MasterAddonsData.get_templates_nonce,
                    tab: t
                },
                success: function(e) {
                    var n = new a.LibraryCollection(e.data.templates),
                        i = new a.CategoriesCollection(e.data.categories);
                    o.tabs[t].data = {
                        templates: n,
                        categories: i,
                        keywords: e.data.keywords
                    }, o.layout.showTemplatesView(n, i, e.data.keywords)
                }
            })
        },
        closeModal: function() {
            this.getModal().hide()
            jQuery('body').removeClass("master-addons-template-popup-loaded");
        },
        getModal: function() {
            return this.modal || (this.modal = elementor.dialogsManager.createWidget("lightbox", {
                id: "ma-el-modal-template",
                className: "elementor-templates-modal",
                closeButton: !1,
                closeOnOutsideClick: !1,
                closeOnEscKey: !1,
                hide: {
                    onOutsideClick: !1,
                    onEscKeyPress: !1
                }
            })), this.modal
        },
        editorCheck: function() {
            return e('body').hasClass('elementor-editor-active') ? true : false;
        },
        toggleEditorMode: function() {
            var body = e('body');

            // When opening template modal, always set to preview mode
            body.removeClass('elementor-editor-active').addClass('elementor-editor-preview');
            body.removeClass('master-addons-template-popup-loaded');
        }
    }, e(window).on("elementor:init", t.init);

    // Template Cache Refresh Handler for Elementor Editor
    e(document).on('click', '#ma-el-template-cache-refresh', function(event) {
        event.preventDefault();

        var $button = e(this);
        var $icon = $button.find('i');

        // Add updating state
        $button.addClass('updating');
        $icon.removeClass('eicon-sync').addClass('eicon-loading eicon-animation-spin');

        // Perform cache refresh
        e.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'jltma_refresh_templates_cache',
                _wpnonce: MasterAddonsData.refresh_cache_nonce
            },
            success: function(response) {
                if (response.success) {
                    console.log('Template cache refreshed successfully');

                    // Update cache count if cache status element exists
                    var $cacheStatus = e('#ma-el-template-cache-status');
                    if ($cacheStatus.length && response.data.total_templates) {
                        var newCount = response.data.total_templates;
                        $cacheStatus.find('.cache-count').text(newCount);
                        $cacheStatus.attr('title', 'Cache Status: ' + newCount + ' templates cached');
                        if (newCount > 0) {
                            $cacheStatus.show();
                        } else {
                            $cacheStatus.hide();
                        }
                    }

                    // Reset button state
                    $button.removeClass('updating');
                    $icon.removeClass('eicon-loading eicon-animation-spin').addClass('eicon-sync');

                    // Force refresh of current tab content by reloading templates
                    setTimeout(function() {
                        if (t && t.getTab) {
                            var currentTab = t.getTab();
                            // Clear cached data for current tab
                            if (t.tabs && t.tabs[currentTab]) {
                                t.tabs[currentTab].data = null;
                            }
                            // Request fresh templates
                            t.requestTemplates(currentTab);
                        }
                    }, 500);

                    // Show success feedback
                    $button.attr('title', 'Cache refreshed successfully!');
                    setTimeout(function() {
                        $button.attr('title', 'Refresh Cache');
                    }, 3000);

                } else {
                    console.error('Failed to refresh template cache:', response.data ? response.data.message : 'Unknown error');
                    $button.removeClass('updating');
                    $icon.removeClass('eicon-loading eicon-animation-spin').addClass('eicon-sync');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error refreshing template cache:', error);
                $button.removeClass('updating');
                $icon.removeClass('eicon-loading eicon-animation-spin').addClass('eicon-sync');
            }
        });
    });
}(jQuery);
