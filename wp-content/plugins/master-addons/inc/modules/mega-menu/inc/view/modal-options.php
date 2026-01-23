<div class="jltma-modal fade" id="jltma_megamenu_modal">
    <div class="jltma-modal-dialog" role="document">
        <div class="jltma-modal-content">
            <div class="jltma-popup-contents">

                <?php include 'modal-header.php'; ?>

                <?php include 'modal-body.php'; ?>

            </div> <!-- jltma-popup-contents -->
        </div>
    </div>
</div>

<?php include 'modal-iframe.php'; ?>

<script>
    var jltma_megamenu_nonce = `<?php echo esc_attr(wp_create_nonce('wp_rest')); ?>`

    jQuery(document).ready(function ($) {
  // Run after all Select2 fields are initialized
  $('.select2-selection--multiple').each(function () {
    const $selection = $(this);
    const $rendered = $selection.find('.select2-selection__rendered');
    const $searchInline = $selection.find('.select2-search--inline');

    // Move the search box to the end of the list
    if ($searchInline.length && $rendered.length) {
      $searchInline.appendTo($rendered);
    }

    // Ensure the search field is visible and styled
    $searchInline.css({
      display: 'inline-flex',
      flex: '1',
      'min-width': '60px',
      order: '999',
    });

    // Make sure the rendered list behaves like flex layout
    $rendered.css({
      display: 'flex',
      'flex-wrap': 'wrap',
      'align-items': 'center',
      gap: '5px',
      padding: '4px 8px',
    });

    // Prevent selected items from hiding the search input
    $rendered.find('.select2-selection__choice').css({
      position: 'relative',
      'z-index': '1',
      margin: '2px 3px',
    });
  });
});


// Inline Icon Picker Handler - Direct implementation to ensure it works
jQuery(document).ready(function($) {

    // IMPORTANT: Re-bind when modal is actually shown
    $(document).on('click', '.jltma_menu_trigger', function() {
        setTimeout(function() {
            $('.jltma-modern-icon-picker-button, .icon-picker').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Get icon library from localized script
                var iconLibrary = (typeof masteraddons_megamenu !== 'undefined' && masteraddons_megamenu.iconLibrary) ? masteraddons_megamenu.iconLibrary : {};
                var allIcons = [];

                // Build icons array
                Object.entries(iconLibrary).forEach(function([libraryName, libraryData]) {
                    Object.entries(libraryData).forEach(function([categoryName, categoryData]) {
                        if (categoryData.icons && Array.isArray(categoryData.icons)) {
                            categoryData.icons.forEach(function(iconName) {
                                allIcons.push({
                                    class: categoryData.prefix + iconName,
                                    name: iconName,
                                    library: categoryData['icon-style'],
                                    prefix: categoryData.prefix
                                });
                            });
                        }
                    });
                });


                if (allIcons.length === 0) {
                    alert('No icons loaded. Please refresh the page.');
                    return;
                }

                // Show icon picker modal
                showIconPickerModal(allIcons, iconLibrary);

                return false;
            });
        }, 500);
    });

    // Icon Picker Modal Function
    function showIconPickerModal(allIcons, iconLibrary) {
        var selectedIcon = $('#jltma-menu-icon-field').val() || '';
        var activeLibrary = 'all';
        var searchTerm = '';

        // Build sidebar list
        var sidebarList = [{title: 'All Icons', 'list-icon': 'eicon eicon-apps', 'library-id': 'all'}];
        Object.entries(iconLibrary).forEach(function([libraryName, libraryData]) {
            Object.entries(libraryData).forEach(function([categoryName, categoryData]) {
                var iconTitle = categoryName !== '' ? libraryName + ' - ' + categoryName : libraryName;
                sidebarList.push({
                    title: iconTitle,
                    'list-icon': categoryData['list-icon'] || '',
                    'library-id': categoryData['icon-style'] || 'all'
                });
            });
        });

        // Create modal HTML matching the exact design from image
        var modalHtml = '<div class="aim-modal aim-open jltma-megamenu-icon-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); z-index: 999999; display: flex; align-items: center; justify-content: center;">' +
            '<div class="aim-modal--content" style="background: #fff; width: 92%; max-width: 1200px; height: 90%; border-radius: 8px; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.5);">' +
                '<div class="aim-modal--header" style="padding: 15px 15px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 12px; background: #fff;">' +
                    '<img src="' + (typeof masteraddons_megamenu !== 'undefined' ? masteraddons_megamenu.pluginUrl : '') + '/assets/images/logo.svg" alt="Master Addons" style="width: 28px; height: 28px;" />' +
                    '<span style="font-size: 16px; font-weight: 600; color: #374151; letter-spacing: 0.5px; text-transform: uppercase; flex: 1;">Master Addons Icon Picker</span>' +
                    '<span class="aim-modal--close" style="cursor: pointer; font-size: 24px; color: #9ca3af; line-height: 1; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.background=\'#f3f4f6\'; this.style.color=\'#ef4444\'" onmouseout="this.style.background=\'\'; this.style.color=\'#9ca3af\'">&times;</span>' +
                '</div>' +
                '<div class="aim-modal--body" style="display: flex; flex: 1; overflow: hidden; background: #fff;">' +
                    '<div class="aim-modal--sidebar" style="width: 280px; border-right: 1px solid #e5e7eb; overflow-y: auto; padding: 24px 0; background: #fafafa;">' +
                        '<div class="aim-modal--sidebar-tabs"></div>' +
                    '</div>' +
                    '<div class="aim-modal--icon-preview-wrap" style="flex: 1; display: flex; flex-direction: column; background: #f9fafb;">' +
                        '<div class="aim-modal--icon-search" style="padding: 5px 5px; background: #fff; border-bottom: 1px solid #e5e7eb;">' +
                            '<div style="position: relative;">' +
                                '<input type="text" placeholder="Filter by name..." class="jltma-icon-search-input" style="width: 100%; padding: 10px 40px 10px 16px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; color: #6b7280; font-style: italic; transition: all 0.2s;" onfocus="this.style.borderColor=\'#6366f1\'; this.style.boxShadow=\'0 0 0 3px rgba(99,102,241,0.1)\'; this.style.fontStyle=\'normal\'" onblur="this.style.borderColor=\'#d1d5db\'; this.style.boxShadow=\'none\'; if(!this.value) this.style.fontStyle=\'italic\'" />' +
                                '<span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 18px;">🔍</span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="aim-modal--icon-preview-inner" style="flex: 1; overflow-y: auto; padding: 24px 30px;">' +
                            '<div id="aim-modal--icon-preview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px;"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="aim-modal--footer" style="padding: 24px 30px; border-top: 1px solid #e5e7eb; text-align: right; background: #fff;">' +
                    '<button class="aim-insert-icon-button" type="button" style="padding: 10px 28px; background: #6366f1; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(99,102,241,0.3); transition: all 0.2s;" onmouseover="this.style.background=\'#4f46e5\'; this.style.boxShadow=\'0 4px 12px rgba(99,102,241,0.4)\'" onmouseout="this.style.background=\'#6366f1\'; this.style.boxShadow=\'0 2px 8px rgba(99,102,241,0.3)\'">INSERT</button>' +
                '</div>' +
            '</div>' +
        '</div>';

        var $modal = $(modalHtml);
        $('body').append($modal);

        // Render sidebar matching exact design with icons
        var sidebarHtml = sidebarList.map(function(item) {
            var isActive = activeLibrary === item['library-id'];
            var iconHtml = item['list-icon'] ? '<i class="' + item['list-icon'] + '" style="font-size: 18px; margin-right: 12px;"></i>' : '<span style="font-size: 18px; margin-right: 12px;">📦</span>';
            var activeStyle = isActive ? 'background: #eef2ff; color: #6366f1; border-left: 3px solid #6366f1;' : 'background: transparent; color: #4b5563; border-left: 3px solid transparent;';
            return '<div class="aim-sidebar-tab" data-library-id="' + item['library-id'] + '" style="' + activeStyle + ' padding: 14px 20px; cursor: pointer; font-size: 14px; transition: all 0.2s; display: flex; align-items: center; font-weight: ' + (isActive ? '600' : '400') + ';" onmouseover="if(!this.classList.contains(\'active\')){this.style.background=\'#f3f4f6\';}" onmouseout="if(!this.classList.contains(\'active\')){this.style.background=\'transparent\';}" class="' + (isActive ? 'active' : '') + '">' +
                iconHtml + '<span>' + item.title + '</span>' +
            '</div>';
        }).join('');
        $modal.find('.aim-modal--sidebar-tabs').html(sidebarHtml);

        // Render icons function matching exact design with icon names
        function renderIcons() {
            var filteredIcons = allIcons.filter(function(icon) {
                var matchesSearch = searchTerm === '' || icon.name.toLowerCase().includes(searchTerm.toLowerCase());
                var matchesLibrary = activeLibrary === 'all' || icon.library === activeLibrary;
                return matchesSearch && matchesLibrary;
            });

            var iconsHtml = filteredIcons.slice(0, 200).map(function(icon) {
                var isSelected = selectedIcon === icon.class;
                var iconName = icon.name.replace(/-/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
                if (iconName.length > 15) iconName = iconName.substring(0, 12) + '...';

                var cardStyle = isSelected ?
                    'background: #fff; border: 2px solid #6366f1; box-shadow: 0 2px 8px rgba(99,102,241,0.2);' :
                    'background: #fff; border: 2px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';

                return '<div class="aim-icon-item" data-icon-class="' + icon.class + '" style="' + cardStyle + ' padding: 10px; border-radius: 8px; cursor: pointer; text-align: center; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; min-height: 60px; height: 60px;" onmouseover="if(!this.dataset.selected){this.style.borderColor=\'#d1d5db\'; this.style.boxShadow=\'0 4px 12px rgba(0,0,0,0.1)\'; this.style.transform=\'translateY(-2px)\';}" onmouseout="if(!this.dataset.selected){this.style.borderColor=\'#e5e7eb\'; this.style.boxShadow=\'0 1px 3px rgba(0,0,0,0.05)\'; this.style.transform=\'translateY(0)\';}" data-selected="' + isSelected + '">' +
                    '<i class="' + icon.class + '" style="font-size: 28px; color: ' + (isSelected ? '#6366f1' : '#6b7280') + '; display: block; line-height: 1; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;"></i>' +
                    '<span style="font-size: 11px; color: #6b7280; font-weight: 500; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; text-align: center;">' + iconName + '</span>' +
                '</div>';
            }).join('');

            $modal.find('#aim-modal--icon-preview').html(iconsHtml || '<p style="text-align: center; color: #9ca3af; padding: 40px; font-size: 14px;">No icons found</p>');
        }

        // IMPORTANT: Stop propagation on modal content to prevent closing parent modal
        $modal.find('.aim-modal--content').on('click', function(e) {
            e.stopPropagation();
        });

        // Close button handler
        $modal.find('.aim-modal--close').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $modal.remove();
        });

        // Click outside to close
        $modal.on('click', function(e) {
            if ($(e.target).hasClass('jltma-megamenu-icon-modal')) {
                e.stopPropagation();
                $modal.remove();
            }
        });

        // Sidebar tab click handler - using event delegation
        $modal.find('.aim-modal--sidebar-tabs').on('click', '.aim-sidebar-tab', function(e) {
            e.preventDefault();
            e.stopPropagation();

            activeLibrary = $(this).data('library-id');

            // Update all tabs - inactive style
            $modal.find('.aim-sidebar-tab').removeClass('active').each(function() {
                $(this).css({
                    'background': 'transparent',
                    'color': '#4b5563',
                    'font-weight': '400',
                    'border-left': '3px solid transparent'
                });
            });

            // Style active tab
            $(this).addClass('active').css({
                'background': '#eef2ff',
                'color': '#6366f1',
                'font-weight': '600',
                'border-left': '3px solid #6366f1'
            });

            renderIcons();
        });

        // Search input handler
        $modal.find('.jltma-icon-search-input').on('input keyup', function(e) {
            e.stopPropagation();
            searchTerm = $(this).val();
            renderIcons();
        });

        // Icon click handler - using event delegation on parent container
        $modal.find('#aim-modal--icon-preview').parent().on('click', '.aim-icon-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $clicked = $(this);
            selectedIcon = $clicked.data('icon-class');

            // Reset all icons
            $modal.find('.aim-icon-item').attr('data-selected', 'false').each(function() {
                $(this).css({
                    'background': '#fff',
                    'border': '2px solid #e5e7eb',
                    'box-shadow': '0 1px 3px rgba(0,0,0,0.05)',
                    'transform': 'translateY(0)'
                }).find('i').css('color', '#6b7280');
            });

            // Highlight selected icon
            $clicked.attr('data-selected', 'true').css({
                'background': '#fff',
                'border': '2px solid #6366f1',
                'box-shadow': '0 2px 8px rgba(99,102,241,0.2)',
                'transform': 'translateY(0)'
            }).find('i').css('color', '#6366f1');
        });

        // Insert Icon button handler
        $modal.find('.aim-insert-icon-button').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (selectedIcon) {
                // Update hidden input
                $('#jltma-menu-icon-field').val(selectedIcon);

                // Update icon preview with proper styling - clear SVG and use icon class
                var $preview = $('#jltma-menu-icon-preview');
                $preview.empty(); // Clear any SVG content
                $preview.attr('class', selectedIcon);
                $preview.attr('style', 'font-size: 20px; color: #fff; display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px;');

                // Update icon preview box border
                $('#jltma-icon-preview-box').css({
                    'border-color': '#6366f1'
                });

                // Show delete button with flex to center icon
                $('.jltma-icon-delete-btn').css({
                    'display': 'flex',
                    'align-items': 'center',
                    'justify-content': 'center'
                });

            } else {
                alert('Please select an icon first');
            }

            $modal.remove();
        });

        // Render icons after all handlers are attached
        renderIcons();
    }

    // Delete icon button handler - using body to ensure it's always attached
    $('body').on('click', '.jltma-icon-delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Reset to default icon (chevron-down)
        var defaultIcon = 'fas fa-chevron-down';

        $('#jltma-menu-icon-field').val(defaultIcon).trigger('change');

        // Reset icon preview to default
        var $preview = $('#jltma-menu-icon-preview');
        $preview.attr('class', defaultIcon);
        $preview.attr('style', 'font-size: 20px; color: #fff; display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px;');

        // Reset icon preview box border to default
        $('#jltma-icon-preview-box').css('border-color', '#2d3748');

        // Hide the delete button (since we're back to default)
        $(this).hide();

        // Return false to prevent any default action
        return false;
    });

    // Color picker inline styling - Compact 48px height
    function initInlineColorPicker() {

        $('.jltma-menu-wpcolor-picker').each(function() {
            var $input = $(this);

            // Skip if already initialized
            if ($input.hasClass('color-picker-initialized') || $input.next().hasClass('jltma-modern-color-picker-inline')) {
                return;
            }

            $input.addClass('color-picker-initialized');
            var color = $input.val() || '';
            var previewBg = color || '#ffffff';

            // Wrap in modern color picker
            var $wrapper = $('<div class="jltma-modern-color-picker-inline" style="display: inline-flex; align-items: center; gap: 8px; padding: 0; background: transparent; border: none; height: auto;"></div>');

            // Color preview box - positioned before input (clickable to open picker)
            var $preview = $('<div class="jltma-color-preview-box" style="width: 40px; height: 36px; border-radius: 4px; border: 2px solid #e2e8f0; cursor: pointer; background-color: ' + previewBg + '; box-shadow: 0 1px 3px rgba(0,0,0,0.1); flex-shrink: 0; position: relative;"></div>');

            // Text input
            var $textInput = $('<input type="text" class="jltma-color-text-input" value="' + color + '" placeholder="Select color" style="min-width: 160px; border: 1px solid #e2e8f0; outline: none; font-size: 13px; padding: 8px 10px; border-radius: 4px; font-family: monospace; text-transform: uppercase; font-weight: 600; color: #374151; transition: all 0.2s; background: #f9fafb;">');

            // Clear button
            var $clearBtn = $('<button type="button" class="jltma-color-clear-btn" style="width: 36px; height: 36px; border-radius: 4px; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #9ca3af; transition: all 0.2s;" title="Clear color">&times;</button>');

            // Hidden native color input
            var $colorInput = $('<input type="color" class="jltma-color-native-input" value="' + (color || '#000000') + '" style="width: 0; height: 0; opacity: 0; position: absolute;">');

            $wrapper.append($preview);
            $wrapper.append($textInput);
            $wrapper.append($clearBtn);
            $wrapper.append($colorInput);

            $input.hide().after($wrapper);

            // Event handlers - Click preview to open color picker
            $preview.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $colorInput.click();
            });

            // Native color picker change
            $colorInput.on('input change', function() {
                var newColor = $(this).val();
                $preview.css('background-color', newColor);
                $textInput.val(newColor);
                $input.val(newColor).trigger('change');
            });

            // Text input change - only update when valid color
            $textInput.on('input keyup', function() {
                var newColor = $(this).val();
                if (/^#[0-9A-F]{6}$/i.test(newColor) || /^#[0-9A-F]{3}$/i.test(newColor)) {
                    $preview.css('background-color', newColor);
                    $colorInput.val(newColor);
                    $input.val(newColor).trigger('change');
                } else if (newColor === '' || newColor === '#') {
                    // Allow clearing
                    $preview.css('background-color', '#ffffff');
                    $input.val('').trigger('change');
                }
            });

            // Clear button click
            $clearBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $textInput.val('');
                $preview.css('background-color', '#ffffff');
                $colorInput.val('#000000');
                $input.val('').trigger('change');
            });

            // Hover effect for clear button
            $clearBtn.on('mouseenter', function() {
                $(this).css({'background': '#fee2e2', 'color': '#ef4444', 'border-color': '#fecaca'});
            });
            $clearBtn.on('mouseleave', function() {
                $(this).css({'background': '#fff', 'color': '#9ca3af', 'border-color': '#e2e8f0'});
            });

            // Focus effect for text input
            $textInput.on('focus', function() {
                $(this).css({'background': '#fff', 'box-shadow': '0 0 0 3px rgba(99,102,241,0.1)', 'border-color': '#6366f1'});
            });
            $textInput.on('blur', function() {
                $(this).css({'background': '#f9fafb', 'box-shadow': 'none', 'border-color': '#e2e8f0'});
            });

        });
    }

    // Initialize when modal content is loaded
    $(document).on('DOMNodeInserted', '#jltma_megamenu_modal', function() {
        setTimeout(function() {
            initInlineColorPicker();
        }, 100);
    });

    // Also initialize when modal trigger is clicked
    $(document).on('click', '.jltma_menu_trigger', function() {
        setTimeout(function() {
            initInlineColorPicker();
        }, 600);
    });
});


</script>
