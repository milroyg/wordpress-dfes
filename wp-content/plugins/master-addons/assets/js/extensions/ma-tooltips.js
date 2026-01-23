;(function($, elementor){
    'use strict';
    // make the window global for site js
    var $window = $(window);

    var debounce = function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;
            var callNow = immediate && !timeout;
            clearTimeout(timeout);

            // Set the new timeout
            timeout = setTimeout(function() {
                timeout = null;
                if (!immediate) { func.apply(context, args); }
            }, wait);
            if (callNow) func.apply(context, args);
        };
    };

    var initTooltips = function () {
        if (typeof tippy === 'undefined' || (typeof elementorModules === 'undefined' && typeof elementorFrontend === 'undefined')) {
            setTimeout(initTooltips, 100);
            return;
        }

        var ModuleHandler = elementorModules.frontend.handlers.Base,
            MA_Element_Tooltip;

        MA_Element_Tooltip = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('jltma_element_') !== -1) {
                    this.instance.destroy();
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('jltma_element_' + key);
            },

            run: function () {
                try {
                    var options = this.getDefaultSettings();
                    var widgetID = this.$element.data('id');
                    
                    if (!widgetID || !this.settings('tooltip_enable')) {
                        return;
                    }
                    
                    // Safe DOM element selection with validation
                    var widgetContainer = null;
                    try {
                        widgetContainer = document.querySelector('.elementor-element-' + widgetID);
                        if (!widgetContainer || !widgetContainer.nodeType || widgetContainer.nodeType !== 1) {
                            return;
                        }
                        
                        // Ensure it's not a jQuery object
                        if (widgetContainer.jquery) {
                            widgetContainer = widgetContainer[0];
                            if (!widgetContainer || !widgetContainer.nodeType) {
                                return;
                            }
                        }
                    } catch (error) {
                        return;
                    }

                    // Cleanup existing instance
                    if (this.instance) {
                        try {
                            this.instance.destroy();
                        } catch (error) {
                            // Silent cleanup
                        }
                        this.instance = null;
                    }

                    // Validate and set tooltip content
                    var tooltipText = this.settings('tooltip_text');
                    if (!tooltipText || typeof tooltipText !== 'string' || !tooltipText.trim()) {
                        return;
                    }
                    options.content = tooltipText;

                    options.arrow = !!this.settings('tooltip_arrow');
                    options.followCursor = !!this.settings('tooltip_follow_cursor');

                    if (this.settings('tooltip_placement')) {
                        options.placement = this.settings('tooltip_placement');
                    }

                    if (this.settings('tooltip_trigger')) {
                        if (this.settings('tooltip_custom_trigger')) {
                            try {
                                var customTrigger = document.querySelector(this.settings('tooltip_custom_trigger'));
                                if (customTrigger && customTrigger.nodeType === 1) {
                                    options.triggerTarget = customTrigger;
                                }
                            } catch (error) {
                                // Use default trigger
                            }
                        } else {
                            options.trigger = this.settings('tooltip_trigger');
                        }
                    }

                    if (this.settings('tooltip_duration')) {
                        var duration = parseInt(this.settings('tooltip_duration')) || 300;
                        options.duration = Math.max(100, Math.min(5000, duration));
                    }

                    if (this.settings('tooltip_animation')) {
                        if (this.settings('tooltip_animation') === 'fill') {
                            options.animateFill = true;
                        } else {
                            options.animation = this.settings('tooltip_animation');
                        }
                    }
                    
                    // Safe offset parsing
                    var xOffset = 0, yOffset = 0;
                    try {
                        if (this.settings('tooltip_x_offset') && this.settings('tooltip_x_offset').size !== undefined) {
                            xOffset = parseInt(this.settings('tooltip_x_offset').size) || 0;
                        }
                        if (this.settings('tooltip_y_offset') && this.settings('tooltip_y_offset').size !== undefined) {
                            yOffset = parseInt(this.settings('tooltip_y_offset').size) || 0;
                        }
                    } catch (error) {
                        xOffset = 0;
                        yOffset = 0;
                    }
                    options.offset = [xOffset, yOffset];

                    // Security and performance options
                    options.theme = 'jltma-widget-tippy-' + widgetID;
                    options.allowHTML = false;
                    options.hideOnClick = true;
                    options.appendTo = function() {
                        try {
                            return (typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode()) 
                                ? document.body : 'parent';
                        } catch (error) {
                            return 'parent';
                        }
                    };

                    // Initialize tooltip with error handling
                    try {
                        this.instance = tippy(widgetContainer, options);
                    } catch (error) {
                        // Silent fail for production
                    }
                } catch (error) {
                    // Silent fail for production
                }
            }
        });

        if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
                elementorFrontend.elementsHandler.addHandler(MA_Element_Tooltip, {
                    $element: $scope
                });
            });
        }
    };

    // Initialize for both frontend and editor
    if (typeof elementorFrontend !== 'undefined') {
        $window.on('elementor/frontend/init', initTooltips);
    }
    
    // Initialize immediately for editor context
    $window.ready(initTooltips);
})(jQuery, window.elementorFrontend);
