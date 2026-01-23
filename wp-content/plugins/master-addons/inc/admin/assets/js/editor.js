/*
* Initialize Modules
*/
;(function($, window, document, undefined){

    $( window ).on( 'elementor:init', function() {


		// Add "master-addons" specific css class to elementor body
        $('.elementor-editor-active').addClass('master-addons');


        // Make our custom css visible in the panel's front-end
        if( typeof elementorPro == 'undefined' ) {
            elementor.hooks.addFilter( 'editor/style/styleText', function( css, context ){
                if ( ! context ) {
                    return;
                }

                var model = context.model,
                    customCSS = model.get('settings').get('custom_css');
                var selector = '.elementor-element.elementor-element-' + model.get('id');

                if ('document' === model.get('elType')) {
                    selector = elementor.config.document.settings.cssWrapperSelector;
                }

                if (customCSS) {
                    css += customCSS.replace(/selector/g, selector);
                }

                return css;
            });
        }

        // End of Custom CSS

        var JltmaControlBaseDataView = elementor.modules.controls.BaseData;


        /*!
         * ================== Visual Select Controller ===================
         **/
        var JltmaControlVisualSelectItemView = JltmaControlBaseDataView.extend( {
            onReady: function() {
                this.ui.select.jltmaVisualSelect();
            },
            onBeforeDestroy: function() {
                this.ui.select.jltmaVisualSelect( 'destroy' );
            }
        } );
        elementor.addControlView( 'jltma-visual-select', JltmaControlVisualSelectItemView );



        // Enables the live preview for Animation Tranistions in Elementor Editor
        function jltmaOnGlobalOpenEditorForTranistions ( panel, model, view ) {
            view.listenTo( model.get( 'settings' ), 'change', function( changedModel ){

                // Force to re-render the element if the Entrance Animation enabled for first time
                if( '' !== model.getSetting('ma_el_animation_name') && !view.$el.hasClass('jltma-animated') ){
                    view.render();
                    view.$el.addClass('jltma-animated');
                    view.$el.addClass('jltma-animated-once');
                }

                // Check the changed setting value
                for( settingName in changedModel.changed ) {
                    if ( changedModel.changed.hasOwnProperty( settingName ) ) {

                        // Replay the animation if an animation option changed (except the animation name)
                        if( settingName !== "ma_el_animation_name" && -1 !== settingName.indexOf("ma_el_animation_") ){

                            // Reply the animation
                            view.$el.removeClass( model.getSetting('ma_el_animation_name') );

                            setTimeout( function() {
                                view.$el.addClass( model.getSetting('ma_el_animation_name') );
                            }, ( model.getSetting('ma_el_animation_delay') || 300 ) ); // Animation Delay
                        }
                    }
                }

            }, view );
        }
        elementor.hooks.addAction( 'panel/open_editor/section', jltmaOnGlobalOpenEditorForTranistions );
        elementor.hooks.addAction( 'panel/open_editor/column' , jltmaOnGlobalOpenEditorForTranistions );
        elementor.hooks.addAction( 'panel/open_editor/widget' , jltmaOnGlobalOpenEditorForTranistions );


        // Choose Text Control
        var JLTMA_Choose_Text = elementor.modules.controls.Choose.extend({
            applySavedValue: function applySavedValue() {
                var currentValue = this.getControlValue();

                if (currentValue || _.isString(currentValue)) {
                    this.ui.inputs.filter("[value=\"".concat(currentValue, "\"]")).prop('checked', true);
                } else {
                    this.ui.inputs.filter(':checked').prop('checked', false);
                }
            }
        });
        elementor.hooks.addAction( 'panel/open_editor/widget' , JLTMA_Choose_Text );
        // elementor.hooks.addFilter('elements/widget/behaviors', JLTMA_Choose_Text);
        elementor.addControlView( 'jltma-choose-text', JLTMA_Choose_Text );


        // Query Control

        var JLTMA_ControlQuery = elementor.modules.controls.Select2.extend( {

            cache: null,
            isTitlesReceived: false,

            getSelect2Placeholder: function getSelect2Placeholder() {
                return {
                    id: '',
                    text: 'All',
                };
            },

            getSelect2DefaultOptions: function getSelect2DefaultOptions() {
                var self = this;

                return jQuery.extend( elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply( this, arguments ), {
                    ajax: {
                        transport: function transport( params, success, failure ) {
                            var data = {
                                q           : params.data.q,
                                query_type  : self.model.get('query_type'),
                                object_type : self.model.get('object_type'),
                            };

                            return elementorCommon.ajax.addRequest('jltma_query_control_filter_autocomplete', {
                                data    : data,
                                success : success,
                                error   : failure,
                            });
                        },
                        data: function data( params ) {
                            return {
                                q    : params.term,
                                page : params.page,
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
                var self        = this,
                    ids         = this.getControlValue(),
                    queryType   = this.model.get('query_type');
                    objectType  = this.model.get('object_type');

                if ( ! ids || ! queryType ) return;

                if ( ! _.isArray( ids ) ) {
                    ids = [ ids ];
                }

                elementorCommon.ajax.loadObjects({
                    action  : 'jltma_query_control_value_titles',
                    ids     : ids,
                    data    : {
                        query_type  : queryType,
                        object_type : objectType,
                        unique_id   : '' + self.cid + queryType,
                    },
                    success: function success(data) {
                        self.isTitlesReceived = true;
                        self.model.set('options', data);
                        self.render();
                    },
                    before: function before() {
                        self.addSpinner();
                    },
                });
            },

            addSpinner: function addSpinner() {
                this.ui.select.prop('disabled', true);
                this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner ee-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
            },

            onReady: function onReady() {
                setTimeout( elementor.modules.controls.Select2.prototype.onReady.bind(this) );

                if ( ! this.isTitlesReceived ) {
                    this.getValueTitles();
                }
            }

        } );

        elementor.addControlView( 'jltma_query', JLTMA_ControlQuery );


        /**
         * Disable Pro-Restricted Switcher Controls
         * Handles switcher controls with .jltma-pro-disabled class in description
         * Uses MutationObserver to watch for DOM changes
         */
        function jltmaHandleProRestrictedSwitchers() {
            // Keep track of already processed controls to avoid duplicate processing
            var processedControls = new Set();

            // Function to disable pro-restricted switchers
            function disableProSwitchers() {
                // Find all switcher controls that have .jltma-pro-disabled in their description
                jQuery('.elementor-control-type-switcher').each(function() {
                    var $control = jQuery(this);

                    // Create unique identifier for this control
                    var controlId = $control.attr('data-control-name') || $control.index();

                    // Skip if already processed
                    if (processedControls.has(controlId)) {
                        return;
                    }

                    var $description = $control.find('.elementor-control-field-description');

                    // Check if description contains the pro-disabled class
                    if ($description.find('.jltma-pro-disabled').length > 0) {
                        var $switcherInput = $control.find('input[type="checkbox"]');
                        var $switcherLabel = $control.find('.elementor-switch');
                        
                        // Add disabled class for styling
                        $control.addClass('jltma-switcher-disabled');
                        $switcherLabel.addClass('jltma-switcher-disabled');

                        // Prevent the switcher from being toggled
                        $switcherInput.prop('disabled', true);

                        // Add click handler to show upgrade popup
                        $switcherLabel.off('click.jltma-pro').on('click.jltma-pro', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            jQuery('.jltma-upgrade-popup').fadeIn(200);
                            return false;
                        });

                        // Also prevent label click
                        $control.find('label').off('click.jltma-pro').on('click.jltma-pro', function(e) {
                            if ($switcherLabel.hasClass('jltma-switcher-disabled')) {
                                e.preventDefault();
                                e.stopPropagation();
                                jQuery('.jltma-upgrade-popup').fadeIn(200);
                                return false;
                            }
                        });

                        // Mark as processed
                        processedControls.add(controlId);
                    }
                });
            }

            // Setup MutationObserver to watch for DOM changes
            function setupObserver() {
                // Target the Elementor panel content area
                var targetNode = document.querySelector('#elementor-panel-content-wrapper');

                if (!targetNode) {
                    // If panel not ready, try again in 500ms
                    setTimeout(setupObserver, 500);
                    return;
                }

                // Create observer instance
                var observer = new MutationObserver(function(mutations) {
                    // Check if any mutations added nodes
                    var shouldProcess = false;
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes.length > 0) {
                            shouldProcess = true;
                        }
                    });

                    // If nodes were added, process switchers
                    if (shouldProcess) {
                        disableProSwitchers();
                    }
                });

                // Observer configuration
                var config = {
                    childList: true,      // Watch for child additions/removals
                    subtree: true,        // Watch entire subtree
                    attributes: false     // Don't watch attribute changes
                };

                // Start observing
                observer.observe(targetNode, config);

                // Run initial check
                disableProSwitchers();
            }

            // Also use interval as fallback for dynamic content
            function startIntervalCheck() {
                setInterval(function() {
                    disableProSwitchers();
                }, 1000); // Check every second
            }

            // Initialize when Elementor is ready
            jQuery(window).on('elementor:init', function() {
                setTimeout(function() {
                    setupObserver();
                    startIntervalCheck();
                }, 1000);
            });

            // Fallback: Start even if elementor:init doesn't fire
            setTimeout(function() {
                setupObserver();
                startIntervalCheck();
            }, 2000);
        }

        // Initialize the pro-restricted switcher handler
        jltmaHandleProRestrictedSwitchers();


        /**
         * Pro Widget Promotion Dialog Handler
         * Customizes the Elementor promotion dialog for Master Addons pro widgets
         */
        function jltmaProWidgetPromotionHandler() {
            if (typeof parent.document === 'undefined') {
                return false;
            }

            parent.document.addEventListener('mousedown', function(e) {
                var widgets = parent.document.querySelectorAll('.elementor-element--promotion');

                if (widgets.length > 0) {
                    for (var i = 0; i < widgets.length; i++) {
                        if (widgets[i].contains(e.target)) {
                            var dialog = parent.document.querySelector('#elementor-element--promotion__dialog');
                            var icon = widgets[i].querySelector('.icon > i');

                            if (!dialog || !icon) {
                                break;
                            }

                            var iconClass = icon.classList.toString();

                            // Check if this is a Master Addons widget (has jltma icon class)
                            if (iconClass.indexOf('jltma') >= 0) {
                                // Hide default Elementor Pro button
                                var defaultButton = dialog.querySelector('.dialog-buttons-action');
                                if (defaultButton) {
                                    defaultButton.style.display = 'none';
                                }

                                e.stopImmediatePropagation();

                                // Check if our custom button already exists
                                if (dialog.querySelector('.jltma-dialog-buttons-action') === null) {
                                    // Create custom upgrade button
                                    var button = document.createElement('a');
                                    var buttonText = document.createTextNode('Upgrade Master Addons');
                                    button.setAttribute('href', 'https://master-addons.com/pricing/');
                                    button.setAttribute('target', '_blank');
                                    button.classList.add(
                                        'dialog-button',
                                        'dialog-action',
                                        'dialog-buttons-action',
                                        'elementor-button',
                                        'go-pro',
                                        'elementor-button-success',
                                        'jltma-dialog-buttons-action'
                                    );
                                    button.style.display = "block";
                                    button.style.textAlign = "center";
                                    button.appendChild(buttonText);

                                    // Insert after the hidden default button
                                    if (defaultButton) {
                                        defaultButton.insertAdjacentHTML('afterend', button.outerHTML);
                                    }
                                } else {
                                   // Remove default button
                                    const btnWrapper = document.querySelector('.dialog-buttons-buttons-wrapper > button');
                                    btnWrapper.style.display  = 'none';
                                    
                                    // Show existing custom button
                                    dialog.querySelector('.jltma-dialog-buttons-action').style.display = 'block';
                                }
                            } else {
                                // Not a Master Addons widget - show default button
                                var defaultBtn = dialog.querySelector('.dialog-buttons-action:not(.jltma-dialog-buttons-action)');
                                if (defaultBtn) {
                                    defaultBtn.style.display = '';
                                }

                                // Hide our custom button if it exists
                                var jltmaBtn = dialog.querySelector('.jltma-dialog-buttons-action');
                                if (jltmaBtn !== null) {
                                    jltmaBtn.style.display = 'none';
                                }
                            }

                            // Stop loop
                            break;
                        }
                    }
                }
            });
        }

        // Initialize pro widget promotion handler
        jltmaProWidgetPromotionHandler();


	} );

})(jQuery, window, document);
