(function($) {
    'use strict';

    var MAPopupBuilder = {
        
        init: function() {
            this.bindEvents();
            this.initTriggerFields();
            this.initDisplayConditions();
            this.initTemplates();
            this.initColorPicker();
            this.initTabs();
        },
        
        bindEvents: function() {
            // Save popup
            $('#ma-popup-form').on('submit', this.savePopup);
            
            // Delete popup
            $(document).on('click', '.ma-popup-delete', this.deletePopup);
            
            // Duplicate popup
            $(document).on('click', '.ma-popup-duplicate', this.duplicatePopup);
            
            // Toggle status
            $(document).on('click', '.ma-popup-status', this.toggleStatus);
            
            // Use template
            $(document).on('click', '.ma-use-template', this.useTemplate);
            
            // Category filter
            $(document).on('click', '.ma-category-filter', this.filterTemplates);
        },
        
        initTriggerFields: function() {
            var self = this;
            
            $('#trigger-type').on('change', function() {
                var value = $(this).val();
                
                // Hide all conditional rows
                $('.trigger-scroll-row, .trigger-element-row').hide();
                
                // Show relevant rows
                if (value === 'scroll') {
                    $('.trigger-scroll-row').show();
                } else if (value === 'element-scroll') {
                    $('.trigger-element-row').show();
                }
            }).trigger('change');
        },
        
        initDisplayConditions: function() {
            $('input[name="display_on"]').on('change', function() {
                var value = $(this).val();
                
                if (value === 'specific' || value === 'exclude') {
                    $('.specific-pages-row').show();
                } else {
                    $('.specific-pages-row').hide();
                }
            }).trigger('change');
            
            // Initialize select2 for pages selection
            if ($.fn.select2) {
                $('#specific-pages').select2({
                    placeholder: 'Select pages',
                    allowClear: true
                });
            }
        },
        
        initTemplates: function() {
            // Template category filtering is handled by filterTemplates method
        },
        
        initColorPicker: function() {
            if ($.fn.wpColorPicker) {
                $('.ma-color-picker').wpColorPicker();
            }
        },
        
        initTabs: function() {
            $('.ma-tabs-nav a').on('click', function(e) {
                e.preventDefault();
                
                var target = $(this).attr('href');
                
                // Update nav
                $('.ma-tabs-nav li').removeClass('active');
                $(this).parent().addClass('active');
                
                // Update content
                $('.ma-tab-pane').removeClass('active');
                $(target).addClass('active');
            });
        },
        
        savePopup: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $button = $form.find('button[type="submit"]');
            var originalText = $button.text();
            
            // Show loading state
            $button.text(ma_popup_builder.strings.saving).prop('disabled', true);
            
            // Gather form data
            var formData = new FormData($form[0]);
            formData.append('action', 'ma_popup_save');
            formData.append('ma_popup_nonce', $('#ma_popup_nonce').val());
            
            // Get TinyMCE content if editor exists
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get('popup-content')) {
                formData.set('popup_content', tinyMCE.get('popup-content').getContent());
            }
            
            $.ajax({
                url: ma_popup_builder.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $button.text(ma_popup_builder.strings.saved);
                        
                        // Show success message
                        MAPopupBuilder.showNotice(response.data.message, 'success');
                        
                        // If new popup, redirect to edit page
                        if (!$('input[name="popup_id"]').val() && response.data.popup_id) {
                            window.location.href = 'admin.php?page=ma-popups&action=edit&popup_id=' + response.data.popup_id;
                        }
                    } else {
                        MAPopupBuilder.showNotice(response.data.message || ma_popup_builder.strings.error, 'error');
                    }
                },
                error: function() {
                    MAPopupBuilder.showNotice(ma_popup_builder.strings.error, 'error');
                },
                complete: function() {
                    setTimeout(function() {
                        $button.text(originalText).prop('disabled', false);
                    }, 2000);
                }
            });
        },
        
        deletePopup: function(e) {
            e.preventDefault();
            
            if (!confirm(ma_popup_builder.strings.confirm_delete)) {
                return;
            }
            
            var $link = $(this);
            var popupId = $link.data('id');
            
            $.ajax({
                url: ma_popup_builder.ajax_url,
                type: 'POST',
                data: {
                    action: 'ma_popup_delete',
                    popup_id: popupId,
                    nonce: ma_popup_builder.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $link.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                        });
                        MAPopupBuilder.showNotice(response.data.message, 'success');
                    } else {
                        MAPopupBuilder.showNotice(response.data.message || ma_popup_builder.strings.error, 'error');
                    }
                },
                error: function() {
                    MAPopupBuilder.showNotice(ma_popup_builder.strings.error, 'error');
                }
            });
        },
        
        duplicatePopup: function(e) {
            e.preventDefault();
            
            var $link = $(this);
            var popupId = $link.data('id');
            
            $link.addClass('ma-loading');
            
            $.ajax({
                url: ma_popup_builder.ajax_url,
                type: 'POST',
                data: {
                    action: 'ma_popup_duplicate',
                    popup_id: popupId,
                    nonce: ma_popup_builder.nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        MAPopupBuilder.showNotice(response.data.message || ma_popup_builder.strings.error, 'error');
                    }
                },
                error: function() {
                    MAPopupBuilder.showNotice(ma_popup_builder.strings.error, 'error');
                },
                complete: function() {
                    $link.removeClass('ma-loading');
                }
            });
        },
        
        toggleStatus: function(e) {
            e.preventDefault();
            
            var $link = $(this);
            var popupId = $link.data('id');
            
            $link.addClass('ma-loading');
            
            $.ajax({
                url: ma_popup_builder.ajax_url,
                type: 'POST',
                data: {
                    action: 'ma_popup_toggle_status',
                    popup_id: popupId,
                    nonce: ma_popup_builder.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var newStatus = response.data.new_status;
                        
                        $link.removeClass('ma-status-active ma-status-inactive')
                            .addClass('ma-status-' + newStatus)
                            .text(newStatus === 'active' ? 'Active' : 'Inactive');
                        
                        MAPopupBuilder.showNotice(response.data.message, 'success');
                    } else {
                        MAPopupBuilder.showNotice(response.data.message || ma_popup_builder.strings.error, 'error');
                    }
                },
                error: function() {
                    MAPopupBuilder.showNotice(ma_popup_builder.strings.error, 'error');
                },
                complete: function() {
                    $link.removeClass('ma-loading');
                }
            });
        },
        
        useTemplate: function(e) {
            e.preventDefault();
            
            var templateKey = $(this).data('template');
            
            // Here you would typically load the template data
            // For now, we'll redirect to create new popup with template
            window.location.href = 'admin.php?page=ma-popups&action=new&template=' + templateKey;
        },
        
        filterTemplates: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var category = $button.data('category');
            
            // Update active state
            $('.ma-category-filter').removeClass('active');
            $button.addClass('active');
            
            // Filter templates
            if (category === 'all') {
                $('.ma-template-card').show();
            } else {
                $('.ma-template-card').hide();
                $('.ma-template-card[data-category="' + category + '"]').show();
            }
        },
        
        showNotice: function(message, type) {
            var noticeClass = type === 'success' ? 'ma-notice-success' : 'ma-notice-error';
            var $notice = $('<div class="ma-notice ' + noticeClass + '">' + message + '</div>');
            
            $('.wrap.ma-popup-builder').prepend($notice);
            
            // Auto remove after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        MAPopupBuilder.init();
    });
    
})(jQuery);