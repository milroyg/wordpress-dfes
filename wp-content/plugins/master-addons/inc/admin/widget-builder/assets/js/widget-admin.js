/**
 * Widget Builder Admin JS
 * Handles modal interactions and AJAX calls for Widget Builder list table
 * Based on Popup Builder Admin pattern
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

;(function ($) {
    'use strict';

    // Toaster Notification System
    var JLTMA_Toaster = {
        container: null,

        init: function() {
            // Create container if it doesn't exist
            if (!this.container) {
                this.container = $('<div class="jltma-toaster-container"></div>');
                $('body').append(this.container);
            }
        },

        show: function(message, type = 'success', duration = 3000) {
            this.init();

            // Create toaster element
            var toaster = $(`
                <div class="jltma-toaster ${type}">
                    <span class="jltma-toaster-icon ${type}-icon"></span>
                    <span class="jltma-toaster-content">${message}</span>
                    <button class="jltma-toaster-close"></button>
                    <div class="jltma-toaster-progress"></div>
                </div>
            `);

            // Add to container
            this.container.append(toaster);

            // Handle close button
            toaster.find('.jltma-toaster-close').on('click', function() {
                JLTMA_Toaster.dismiss(toaster);
            });

            // Auto dismiss after duration
            if (duration > 0) {
                setTimeout(function() {
                    JLTMA_Toaster.dismiss(toaster);
                }, duration);
            }

            return toaster;
        },

        dismiss: function(toaster) {
            toaster.addClass('jltma-toaster-exit');
            setTimeout(function() {
                toaster.remove();
            }, 300);
        },

        success: function(message, duration) {
            return this.show(message, 'success', duration);
        },

        error: function(message, duration) {
            return this.show(message, 'error', duration);
        },

        warning: function(message, duration) {
            return this.show(message, 'warning', duration);
        },

        info: function(message, duration) {
            return this.show(message, 'info', duration);
        }
    };

    const WidgetAdmin = {
        init: function () {
            this.bindEvents();
        },

        bindEvents: function () {
            // Modal controls
            $(document).on('click', '.jltma-add-new-widget', this.openModal.bind(this));
            $(document).on('change', '#jltma_widget_category', this.handleCategoryChange.bind(this));
            $(document).on('click', '.jltma-cancel-inline-category', this.hideInlineCategoryAdd.bind(this));
            $(document).on('click', '.jltma-save-inline-category', this.saveInlineCategory.bind(this));
            $(document).on('click', '.jltma-pop-close, .close-btn, .jltma-modal-backdrop', this.closeModal.bind(this));

            // Import Widget controls
            $(document).on('click', '.jltma-import-widget', this.openImportModal.bind(this));
            $(document).on('click', '.jltma-import-close, .jltma-import-backdrop', this.closeImportModal.bind(this));
            $(document).on('change', '#jltma-import-file', this.handleFileSelect.bind(this));

            // ESC key to close import modal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    if ($('#jltma_import_widget_modal').is(':visible')) {
                        $('#jltma_import_widget_modal').fadeOut(300);
                        $('body').removeClass('jltma-modal-open');
                        WidgetAdmin.resetImportModal();
                    }
                }
            });

            // Drag and drop events
            $(document).on('dragover', '#jltma-dropzone', this.handleDragOver.bind(this));
            $(document).on('dragleave', '#jltma-dropzone', this.handleDragLeave.bind(this));
            $(document).on('drop', '#jltma-dropzone', this.handleDrop.bind(this));

            // Allow Enter key to submit inline category
            $(document).on('keypress', '#jltma_new_category_title', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    $('.jltma-save-inline-category').trigger('click');
                }
            });

            // Allow Escape key to cancel inline category
            $(document).on('keydown', '#jltma_new_category_title', function(e) {
                if (e.which === 27) { // Escape key
                    e.preventDefault();
                    $('.jltma-cancel-inline-category').trigger('click');
                }
            });

            // Save widget form
            $(document).on('submit', '#jltma-widget-form', this.saveWidget.bind(this));

            // Also bind to Save Changes button click
            $(document).on('click', '.jltma-save-widget', function(e) {
                e.preventDefault();
                $('#jltma-widget-form').trigger('submit');
            });

            // Delete widget
            $(document).on('click', '.jltma-delete-widget', this.deleteWidget.bind(this));

            // Copy shortcode to clipboard
            $(document).on('click', '.jltma-copy-shortcode-widget', this.copyShortcode.bind(this));

            // Edit Conditions - opens widget modal like Theme Builder
            $(document).on('click', '.jltma-widget-edit-cond', this.openModalFromConditions.bind(this));
        },

        openModal: function(e) {
            e.preventDefault();
            var $target = $(e.currentTarget);
            var widgetId = $target.data('widget-id') || 0;
            var modal = $('#jltma_widget_builder_modal');

            modal.addClass('loading');
            modal.addClass('active');
            $('body').addClass('jltma-modal-open');

            if (widgetId > 0) {
                // Update modal title to "Edit Widget"
                modal.find('.jltma-modal-title').text('Edit Widget');
                // Store widget ID in hidden field immediately
                $('#jltma_widget_id').val(widgetId);
                // Load existing widget data via AJAX
                this.loadWidgetData(widgetId);
            } else {
                // New widget - reset form and title
                modal.find('.jltma-modal-title').text('Create New Widget');
                this.resetForm();
                modal.removeClass('loading');
            }

            modal.find('form').attr('data-widget-id', widgetId);
        },

        closeModal: function(e) {
            if ($(e.target).hasClass('jltma-modal-backdrop') ||
                $(e.target).hasClass('close-btn') ||
                $(e.target).closest('.jltma-pop-close').length) {

                $('#jltma_widget_builder_modal').removeClass('active');
                $('#jltma_category_modal').removeClass('active');
                $('body').removeClass('jltma-modal-open');
                e.preventDefault();
            }
        },

        resetForm: function() {
            var form = $('#jltma-widget-form');
            form[0].reset();
            $('#jltma_widget_id').val('');

            // Reset category dropdown and store initial value
            var categorySelect = $('select#jltma_widget_category');
            var initialValue = categorySelect.val() || 'general';
            categorySelect.data('previous-value', initialValue);

            // Hide inline category field if it's showing
            $('#jltma-inline-category-add').hide();
            $('#jltma_new_category_title').val('');
        },

        loadWidgetData: function(widgetId) {
            var self = this;
            var modal = $('#jltma_widget_builder_modal');

            $.ajax({
                url: jltmaWidgetAdmin.ajax_url,
                type: 'GET',
                data: {
                    action: 'jltma_widget_get_data',
                    widget_id: widgetId,
                    _nonce: jltmaWidgetAdmin.widget_nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#jltma_widget_id').val(widgetId);
                        $('#jltma_widget_title').val(response.data.title);

                        // Set category - use the value directly with specific selector
                        var categoryDropdown = $('select#jltma_widget_category');
                        var categoryValue = response.data.category || 'general';

                        // Set the value
                        categoryDropdown.val(categoryValue);

                        // Store as previous value
                        categoryDropdown.data('previous-value', categoryValue);

                        // Make sure inline category field is hidden
                        $('#jltma-inline-category-add').hide();
                        $('#jltma_new_category_title').val('');
                    }
                    modal.removeClass('loading');
                },
                error: function(xhr, status, error) {
                    modal.removeClass('loading');
                    JLTMA_Toaster.error(jltmaWidgetAdmin.strings.error);
                }
            });
        },

        handleCategoryChange: function(e) {
            var $select = $(e.currentTarget);
            var selectedValue = $select.val();

            if (selectedValue === '__add_new__') {
                // Store the previous value (not the __add_new__ value)
                var previousValue = $select.data('previous-value') || 'general';
                $select.data('previous-value', previousValue);

                // Show inline category add field
                $('#jltma-inline-category-add').slideDown(200);
                $('#jltma_new_category_title').val('').focus();
            } else {
                // Store current selection as previous value for next time
                $select.data('previous-value', selectedValue);
            }
        },

        showInlineCategoryAdd: function(e) {
            e.preventDefault();
            $('#jltma-inline-category-add').slideDown(200);
            $('#jltma_new_category_title').focus();
        },

        hideInlineCategoryAdd: function(e) {
            e.preventDefault();
            var categorySelect = $('select#jltma_widget_category');
            var previousValue = categorySelect.data('previous-value') || 'general';

            // Hide the inline field
            $('#jltma-inline-category-add').slideUp(200);
            $('#jltma_new_category_title').val('');

            // Reset dropdown to previous value
            categorySelect.val(previousValue);
        },

        saveInlineCategory: function(e) {
            e.preventDefault();
            var title = $('#jltma_new_category_title').val().trim();
            var categorySelect = $('select#jltma_widget_category');
            var saveBtn = $('.jltma-save-inline-category');

            if (!title) {
                JLTMA_Toaster.error('Category title is required.');
                return;
            }

            // Disable button and show loading state
            var originalText = saveBtn.text();
            saveBtn.prop('disabled', true).text('Saving...');

            // Generate slug from title
            var slug = title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();

            // Save via REST API
            $.ajax({
                url: jltmaWidgetAdmin.admin_url + 'admin-ajax.php?rest_route=/jltma/v1/categories',
                type: 'POST',
                headers: {
                    'X-WP-Nonce': jltmaWidgetAdmin.widget_nonce
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    name: title,
                    slug: slug
                }),
                success: function(response) {
                    if (response.success && response.data) {
                        var newSlug = response.data.slug;
                        var newTitle = response.data.title;

                        // Check if category already exists in dropdown
                        if (categorySelect.find('option[value="' + newSlug + '"]').length === 0) {
                            // Add to dropdown before the "__add_new__" option
                            categorySelect.find('option[value="__add_new__"]').before(
                                '<option value="' + newSlug + '">' + newTitle + '</option>'
                            );
                        }

                        // Select the new category
                        categorySelect.val(newSlug);
                        categorySelect.data('previous-value', newSlug);

                        // Hide inline add and clear field
                        $('#jltma-inline-category-add').slideUp(200);
                        $('#jltma_new_category_title').val('');

                        // Show success message
                        JLTMA_Toaster.success(jltmaWidgetAdmin.strings.category_added);
                    } else {
                        JLTMA_Toaster.error('Failed to create category. Please try again.');
                        categorySelect.val(categorySelect.data('previous-value') || 'general');
                    }
                    saveBtn.prop('disabled', false).text(originalText);
                },
                error: function(xhr, status, error) {
                    JLTMA_Toaster.error('Failed to create category. Please try again.');
                    categorySelect.val(categorySelect.data('previous-value') || 'general');
                    saveBtn.prop('disabled', false).text(originalText);
                }
            });
        },

        saveWidget: function(e) {
            e.preventDefault();
            var form = $('#jltma-widget-form');
            var submitBtn = $('.jltma-save-widget');
            var widgetId = $('#jltma_widget_id').val();
            var title = $('#jltma_widget_title').val().trim();
            var category = $('select#jltma_widget_category').val();

            if (!title) {
                JLTMA_Toaster.error(jltmaWidgetAdmin.strings.widget_title_required);
                return;
            }

            // Disable submit button
            var originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text(jltmaWidgetAdmin.strings.saving);

            $.ajax({
                url: jltmaWidgetAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'jltma_widget_save_data',
                    widget_id: widgetId,
                    widget_title: title,
                    widget_category: category,
                    _nonce: jltmaWidgetAdmin.widget_nonce
                },
                success: function(response) {
                    if (response.success) {
                        if (widgetId) {
                            // Get the selected category text from dropdown before closing modal
                            var categoryName = $('select#jltma_widget_category option:selected').text();

                            // Editing existing widget - close modal and update display
                            $('#jltma_widget_builder_modal').removeClass('active');
                            $('body').removeClass('jltma-modal-open');

                            // Update the category display in the table column
                            var $row = $('#post-' + widgetId);
                            var $categoryColumn = $row.find('td.column-jltma_widget_category');

                            // Update the entire column content with category name and Edit Conditions link
                            $categoryColumn.html(
                                '<div style="text-align: center;">' +
                                '<span class="jltma-widget-category">' + categoryName + '</span>' +
                                '<br><a href="#" class="jltma-widget-edit-cond" id="' + widgetId + '" style="font-size: 12px; color: #2271b1; text-decoration: none;">' +
                                'Edit Conditions <span class="dashicons dashicons-edit" style="font-size: 12px; vertical-align: middle;"></span>' +
                                '</a>' +
                                '</div>'
                            );

                            // Show success message
                            JLTMA_Toaster.success(jltmaWidgetAdmin.strings.saved);
                        } else {
                            // New widget - redirect to widget editor
                            window.location.href = response.data.edit_url;
                        }
                    } else {
                        JLTMA_Toaster.error(response.data.message || jltmaWidgetAdmin.strings.error);
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    JLTMA_Toaster.error(jltmaWidgetAdmin.strings.error);
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        },

        deleteWidget: function(e) {
            e.preventDefault();
            var widgetId = $(e.currentTarget).data('widget-id');

            if (!confirm(jltmaWidgetAdmin.strings.confirm_delete)) {
                return;
            }

            $.ajax({
                url: jltmaWidgetAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'jltma_widget_delete',
                    widget_id: widgetId,
                    _nonce: jltmaWidgetAdmin.widget_nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        JLTMA_Toaster.error(response.data.message || jltmaWidgetAdmin.strings.error);
                    }
                },
                error: function() {
                    JLTMA_Toaster.error(jltmaWidgetAdmin.strings.error);
                }
            });
        },

        copyShortcode: function(e) {
            e.preventDefault();
            var shortcode = $(e.currentTarget).data('shortcode');
            var button = $(e.currentTarget);

            // Create temporary textarea
            var temp = $('<textarea>');
            $('body').append(temp);
            temp.val(shortcode).select();
            document.execCommand('copy');
            temp.remove();

            // Show feedback
            var originalHtml = button.html();
            button.html('<span class="dashicons dashicons-yes"></span> ' + jltmaWidgetAdmin.strings.copied);

            setTimeout(function() {
                button.html(originalHtml);
            }, 2000);
        },

        openModalFromConditions: function(e) {
            e.preventDefault();
            var widgetId = $(e.currentTarget).attr('id'); // Get ID from element's id attribute like Theme Builder
            var modal = $('#jltma_widget_builder_modal');

            modal.addClass('loading');
            modal.addClass('active');
            $('body').addClass('jltma-modal-open');

            if (widgetId > 0) {
                // Update modal title to "Edit Widget"
                modal.find('.jltma-modal-title').text('Edit Widget');
                // Store widget ID in hidden field
                $('#jltma_widget_id').val(widgetId);
                // Load existing widget data
                this.loadWidgetData(widgetId);
            }

            modal.find('form').attr('data-widget-id', widgetId);
        },

        // Import Widget Functions
        openImportModal: function(e) {
            e.preventDefault();
            $('#jltma_import_widget_modal').fadeIn(300);
            $('body').addClass('jltma-modal-open');
        },

        closeImportModal: function(e) {
            if ($(e.target).hasClass('jltma-import-backdrop') ||
                $(e.target).hasClass('jltma-import-close') ||
                $(e.target).closest('.jltma-import-close').length) {
                $('#jltma_import_widget_modal').fadeOut(300);
                $('body').removeClass('jltma-modal-open');
                this.resetImportModal();
                e.preventDefault();
            }
        },

        resetImportModal: function() {
            $('#jltma-dropzone').removeClass('dragging');
            $('.jltma-dropzone-content').show();
            $('.jltma-importing').hide();
            $('#jltma-import-file').val('');
        },

        handleDragOver: function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#jltma-dropzone').addClass('dragging');
        },

        handleDragLeave: function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#jltma-dropzone').removeClass('dragging');
        },

        handleDrop: function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#jltma-dropzone').removeClass('dragging');

            var files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                this.processImportFile(files[0]);
            }
        },

        handleFileSelect: function(e) {
            var file = e.target.files[0];
            if (file) {
                this.processImportFile(file);
            }
        },

        processImportFile: function(file) {
            var self = this;

            // Validate file type
            if (!file.name.endsWith('.json')) {
                JLTMA_Toaster.error('Please select a valid JSON file.', 5000);
                return;
            }

            // Show loading state
            $('.jltma-dropzone-content').hide();
            $('.jltma-importing').show();

            var reader = new FileReader();

            reader.onload = function(event) {
                try {
                    var jsonData = JSON.parse(event.target.result);

                    // Hook: Validate import data
                    var isValid = true;
                    if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) {
                        isValid = window.jltmaWidgetBuilder.hooks.applyFilters(
                            'jltma_cwb_import_validate',
                            isValid,
                            jsonData
                        );
                    }

                    // Basic validation
                    if (!jsonData.widget || !jsonData.version) {
                        throw new Error('Invalid widget file format');
                    }

                    if (!isValid) {
                        throw new Error('Import validation failed');
                    }

                    var importedWidget = jsonData.widget;

                    // Hook: Before import
                    if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) {
                        window.jltmaWidgetBuilder.hooks.doAction(
                            'jltma_cwb_before_import',
                            importedWidget
                        );
                    }

                    // Hook: Filter imported widget data
                    var processedWidget = importedWidget;
                    if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) {
                        processedWidget = window.jltmaWidgetBuilder.hooks.applyFilters(
                            'jltma_cwb_import_data',
                            importedWidget,
                            jsonData
                        );
                    }

                    // Create widget with imported data via AJAX
                    self.createWidgetFromImport(processedWidget, jsonData);

                } catch (parseError) {
                    console.error('Failed to parse widget file:', parseError);
                    JLTMA_Toaster.error('Failed to import widget. Invalid JSON file format.', 5000);
                    self.resetImportModal();
                    // Keep modal open on error
                }
            };

            reader.onerror = function() {
                console.error('Failed to read file');
                JLTMA_Toaster.error('Failed to read file. Please try again.', 5000);
                self.resetImportModal();
                // Keep modal open on error
            };

            reader.readAsText(file);
        },

        createWidgetFromImport: function(widgetData, originalJsonData) {
            var self = this;
            var CUSTOM_CATEGORY_PREFIX = 'jltma_cwb_custom_';

            // Check if category needs to be created
            var categorySlug = widgetData.category || 'general';
            var isCustomCategory = widgetData.isCustomCategory ||
                                  categorySlug.startsWith(CUSTOM_CATEGORY_PREFIX);

            // Function to actually create the widget - Use REST API directly
            var createWidget = function() {
                console.log('Creating widget via REST API with data:', widgetData);

                $.ajax({
                    url: jltmaWidgetAdmin.rest_url + '/widgets',
                    type: 'POST',
                    headers: {
                        'X-WP-Nonce': jltmaWidgetAdmin.rest_nonce
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(widgetData),
                    success: function(response) {
                        console.log('Widget created successfully:', response);

                        // Hook: After import
                        if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) {
                            window.jltmaWidgetBuilder.hooks.doAction(
                                'jltma_cwb_imported',
                                widgetData,
                                originalJsonData
                            );
                        }

                        // Show success toaster with longer duration
                        JLTMA_Toaster.success('Widget imported successfully! Redirecting...', 3000);

                        // Close modal and redirect after showing success message
                        setTimeout(function() {
                            $('#jltma_import_widget_modal').fadeOut(300);
                            $('body').removeClass('jltma-modal-open');

                            // Redirect to widget editor after modal closes
                            setTimeout(function() {
                                var editUrl = jltmaWidgetAdmin.admin_url + 'admin.php?page=jltma-widget-builder&widget_id=' + response.id;
                                window.location.href = editUrl;
                            }, 300);
                        }, 1500);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to create widget:', error);
                        console.error('XHR Response:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);
                        console.error('Request Data:', widgetData);

                        var errorMessage = 'Failed to import widget. Please try again.';
                        try {
                            var errorData = JSON.parse(xhr.responseText);
                            if (errorData.message) {
                                errorMessage = errorData.message;
                            }
                        } catch(e) {}

                        JLTMA_Toaster.error(errorMessage + ' Check console for details.', 5000);
                        self.resetImportModal();
                    }
                });
            };

            // Check if we need to create category first
            if (isCustomCategory) {
                // Check if category exists via REST API
                $.ajax({
                    url: jltmaWidgetAdmin.rest_url + '/categories',
                    type: 'GET',
                    headers: {
                        'X-WP-Nonce': jltmaWidgetAdmin.rest_nonce
                    },
                    success: function(categories) {
                        var categoryExists = categories.some(function(cat) {
                            return cat.slug === categorySlug;
                        });

                        if (!categoryExists) {
                            // Create category first
                            self.createCategoryIfNotExists(categorySlug, createWidget);
                        } else {
                            // Category exists, create widget
                            createWidget();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.warn('Failed to check categories, proceeding with widget creation:', error);
                        // If can't check categories, try to create widget anyway
                        createWidget();
                    }
                });
            } else {
                // Built-in category, create widget directly
                createWidget();
            }
        },

        createCategoryIfNotExists: function(categorySlug, callback) {
            var CUSTOM_CATEGORY_PREFIX = 'jltma_cwb_custom_';

            // Extract category name from slug
            var categoryName = categorySlug.replace(CUSTOM_CATEGORY_PREFIX, '')
                .split('_')
                .map(function(word) {
                    return word.charAt(0).toUpperCase() + word.slice(1);
                })
                .join(' ');

            // Hook: Before category creation
            var shouldCreate = true;
            if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) {
                shouldCreate = window.jltmaWidgetBuilder.hooks.applyFilters(
                    'jltma_cwb_before_create_category',
                    shouldCreate,
                    categorySlug,
                    categoryName
                );
            }

            if (!shouldCreate) {
                if (callback) callback();
                return;
            }

            $.ajax({
                url: jltmaWidgetAdmin.rest_url + '/categories',
                type: 'POST',
                headers: {
                    'X-WP-Nonce': jltmaWidgetAdmin.rest_nonce
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    name: categoryName,
                    slug: categorySlug,
                    custom_prefix: CUSTOM_CATEGORY_PREFIX,
                    auto_created: true
                }),
                success: function(result) {
                    // Hook: After category creation
                    if (window.jltmaWidgetBuilder && window.jltmaWidgetBuilder.hooks) {
                        window.jltmaWidgetBuilder.hooks.doAction(
                            'jltma_cwb_category_created',
                            result.data,
                            categorySlug,
                            categoryName
                        );
                    }

                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to create category:', categorySlug, error);
                    JLTMA_Toaster.warning('Could not create category "' + categoryName + '". Widget will use "general" category.', 5000);
                    if (callback) callback(); // Continue anyway
                }
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        WidgetAdmin.init();
    });

})(jQuery);
