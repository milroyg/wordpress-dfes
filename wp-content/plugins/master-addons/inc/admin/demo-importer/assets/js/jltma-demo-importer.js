jQuery(document).ready(function($) {
    $(document).on('click', '.template-import-btn', function(e) {
        e.preventDefault();
        var button = $(this);
        var templateId = button.data('template-id');
        var originalText = button.text();

        button.prop('disabled', true).text('Importing widgets...');

        // FIRST: Import widgets
        $.ajax({
            url: jltmaDemoImporter.ajax_url,
            type: 'POST',
            data: {
                action: 'jltma_import_demo_widgets',
                template_id: templateId,
                _nonce: jltmaDemoImporter.nonce
            },
            success: function(widgetResponse) {
                if (widgetResponse.success) {
                    button.text('Importing template...');

                    // SECOND: Import template using jltma_import_template (like library mode)
                    $.ajax({
                        url: jltmaDemoImporter.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'jltma_import_template',
                            template_id: templateId,
                            tab: 'demo', 
                            _wpnonce: jltmaDemoImporter.template_nonce
                        },
                        success: function(templateResponse) {
                            console.log('Template import response:', templateResponse);

                            if (templateResponse.success) {
                                var importedWidgets = widgetResponse.data.imported || 0;
                                var message = 'Successfully imported ' + importedWidgets + ' widget(s)';

                                if (templateResponse.data && templateResponse.data.page_id) {
                                    message += ' and created template!';
                                } else {
                                    message += ' and imported template to library!';
                                }

                                alert(message);
                                button.text('Imported').addClass('imported').css('background', '#4CAF50');

                                // Optionally redirect to edit page
                                if (templateResponse.data && templateResponse.data.edit_url && confirm('Open imported template in Elementor?')) {
                                    window.open(templateResponse.data.edit_url, '_blank');
                                }
                            } else {
                                alert('Widgets imported but template failed: ' + (templateResponse.data.message || 'Unknown error'));
                                button.prop('disabled', false).text(originalText);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Template import error:', error);
                            alert('Widgets imported but template failed to import.');
                            button.prop('disabled', false).text(originalText);
                        }
                    });
                } else {
                    alert('Failed to import widgets: ' + (widgetResponse.data.message || 'Unknown error'));
                    button.prop('disabled', false).text(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Widget import error:', error);
                alert('Failed to import widgets. Check console for details.');
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Import all templates
    $(document).on('click', '.jltma_demo-import', function(e) {
        e.preventDefault();
        var button = $(this);
        var originalText = button.text();

        if (!confirm('This will import all widgets and templates. Continue?')) {
            return;
        }

        console.log('Starting bulk import');
        button.prop('disabled', true).text('Importing all widgets first...');

        // FIRST: Import ALL widgets at once
        $.ajax({
            url: jltmaDemoImporter.ajax_url,
            type: 'POST',
            data: {
                action: 'jltma_import_demo_widgets',
                template_id: 'all',
                _nonce: jltmaDemoImporter.nonce
            },
            success: function(widgetResponse) {
                console.log('All widgets import response:', widgetResponse);

                if (widgetResponse.success) {
                    var widgetCount = widgetResponse.data.imported || 0;
                    button.text('Imported ' + widgetCount + ' widgets. Now importing templates...');

                    // SECOND: Import templates after all widgets are done
                    var templateIds = [];
                    $('.template-import-btn').each(function() {
                        templateIds.push($(this).data('template-id'));
                    });

                    importNextTemplate(templateIds, 0, button, originalText, widgetCount);
                } else {
                    alert('Failed to import widgets: ' + (widgetResponse.data.message || 'Unknown error'));
                    button.prop('disabled', false).text(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Bulk widget import error:', error);
                alert('Failed to import widgets. Check console for details.');
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Function to import templates sequentially (after widgets are done)
    function importNextTemplate(templateIds, index, button, originalText, widgetCount) {
        if (index >= templateIds.length) {
            alert('Successfully imported ' + widgetCount + ' widgets and ' + templateIds.length + ' templates!');
            button.prop('disabled', false).text('All Imported').addClass('imported').css('background', '#4CAF50');

            // Mark all individual buttons as imported
            $('.template-import-btn').each(function() {
                $(this).text('Imported').addClass('imported').css('background', '#4CAF50');
            });
            return;
        }

        var templateId = templateIds[index];
        button.text('Importing template ' + (index + 1) + ' of ' + templateIds.length + '...');
        console.log('Importing template ID:', templateId);

        $.ajax({
            url: jltmaDemoImporter.ajax_url,
            type: 'POST',
            data: {
                action: 'jltma_import_template',
                template_id: templateId,
                tab: 'demo', 
                _wpnonce: jltmaDemoImporter.template_nonce
            },
            success: function(response) {
                console.log('Template ' + templateId + ' import response:', response);
                // Continue with next template regardless of success
                importNextTemplate(templateIds, index + 1, button, originalText, widgetCount);
            },
            error: function(xhr, status, error) {
                console.error('Template ' + templateId + ' import error:', error);
                // Continue even if fails
                importNextTemplate(templateIds, index + 1, button, originalText, widgetCount);
            }
        });
    }
});