jQuery(document).ready(function($) {
    "use strict";

    var MATemplatesKit = {

        requiredTheme: false,
        requiredPlugins: false,
        lazyImages: [],

        init: function() {
            // Overlay Click
            $(document).on('click', '.ma-templates-kit-grid .image-overlay', function(){
                MATemplatesKit.showImportPage($(this).closest('.grid-item'));
                MATemplatesKit.renderImportPage($(this).closest('.grid-item'));
            });

            // Logo Click
            $('.ma-templates-kit-logo').find('.back-btn').on('click', function(){
                MATemplatesKit.showTemplatesMainGrid();
            });

            // Import Templates Kit
            $('.ma-templates-kit-single').find('.import-kit').on('click', function(){
                if ($('.ma-templates-kit-grid').find('.grid-item[data-kit-id="'+ $(this).attr('data-kit-id') +'"]').data('price') === 'pro') {
                    return false;
                }

                var confirmImport = confirm('For the best results, it is recommended to temporarily deactivate All other Active plugins Except Elementor and Master Addons.\n\nHere's what will be imported: posts, pages, images in the media library, menu items, some basic settings (like which page will be the homepage), and any pre-made headers, footers, and pop-ups if the Template Kit includes them. You can always delete this imported content and return to your old site design. \n\nDon't worry—none of your current data, like images, posts, pages, menus and anything else, won't be deleted.');

                if (confirmImport) {
                    MATemplatesKit.importTemplatesKit($(this).attr('data-kit-id'));
                    $('.ma-import-kit-popup-wrap').fadeIn();

                    // Old Version Check
                    let wooBuilder = $('.grid-item[data-kit-id="'+ $(this).attr('data-kit-id') +'"]').find('.ma-woo-builder-label').length,
                        updateNotice = $('.ma-wp-update-notice').length;

                    if (wooBuilder > 0 && updateNotice > 0) {
                        $('.ma-wp-update-notice').show();
                        $('.progress-wrap').hide();
                    }
                }
            });

            // Close Button Click
            $('.ma-import-kit-popup-wrap').find('.close-btn').on('click', function(){
                $('.ma-import-kit-popup-wrap').fadeOut();
                window.location.reload();
            });

            // Search Templates Kit
            var searchTimeout = null,
                maingGridHtml = $('.ma-templates-kit-grid').html();
            $('.ma-templates-kit-search').find('input').keyup(function(e) {
                if (e.which === 13) {
                    return false;
                }

                var val = $(this).val().toLowerCase();

                if (searchTimeout != null) {
                    clearTimeout(searchTimeout);
                }

                searchTimeout = setTimeout(function() {
                    searchTimeout = null;
                    MATemplatesKit.searchTemplatesKit(val, maingGridHtml);

                    // Final Adjustments
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'ma_search_query_results',
                            search_query: val
                        },
                        success: function(response) {}
                    });
                }, 1000);
            });

            // Price Filter
            $('.ma-templates-kit-price-filter ul li').on('click', function() {
                var price = $(this).text(),
                    price = 'premium' == price.toLowerCase() ? 'pro' : price.toLowerCase();

                MATemplatesKit.fiterFreeProTemplates(price);
                $('.ma-templates-kit-price-filter').children().first().attr('data-price', price);
                $('.ma-templates-kit-price-filter').children().first().text('Price: '+ $(this).text());
            });

            MATemplatesKit.initializeLazyLoading();
        },

        installRequiredTheme: function(kitID) {
            var themeStatus = $('.ma-templates-kit-grid').data('theme-status');

            if ('req-theme-active' === themeStatus) {
                MATemplatesKit.requiredTheme = true;
                return;
            } else if ('req-theme-inactive' === themeStatus) {
                $.post(
                    ajaxurl,
                    {
                        action: 'ma_activate_required_theme',
                        nonce: MATemplatesKitLoc.nonce,
                    }
                );

                MATemplatesKit.requiredTheme = true;
                return;
            }

            wp.updates.installTheme({
                slug: 'hello-elementor',
                success: function() {
                    $.post(
                        ajaxurl,
                        {
                            action: 'ma_activate_required_theme',
                            nonce: MATemplatesKitLoc.nonce,
                        }
                    );

                    MATemplatesKit.requiredTheme = true;
                }
            });
        },

        installRequiredPlugins: function(kitID) {
            MATemplatesKit.installRequiredTheme();

            var kit = $('.grid-item[data-kit-id="'+ kitID +'"]');
                MATemplatesKit.requiredPlugins = kit.data('plugins') !== undefined ? kit.data('plugins') : false;

            // Install Plugins
            if (MATemplatesKit.requiredPlugins) {
                if ('contact-form-7' in MATemplatesKit.requiredPlugins && false === MATemplatesKit.requiredPlugins['contact-form-7']) {
                    MATemplatesKit.installPluginViaAjax('contact-form-7');
                }

                if ('woocommerce' in MATemplatesKit.requiredPlugins && false === MATemplatesKit.requiredPlugins['woocommerce']) {
                    MATemplatesKit.installPluginViaAjax('woocommerce');
                }

                if ('elementor' in MATemplatesKit.requiredPlugins && false === MATemplatesKit.requiredPlugins['elementor']) {
                    MATemplatesKit.installPluginViaAjax('elementor');
                }
            }
        },

        installPluginViaAjax: function(slug) {
            wp.updates.installPlugin({
                slug: slug,
                success: function() {
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'ma_activate_required_plugins',
                            plugin: slug,
                            nonce: MATemplatesKitLoc.nonce,
                        },
                        success: function(response) {
                            MATemplatesKit.requiredPlugins[slug] = true;
                        },
                        error: function(response) {
                            console.log(response);
                            MATemplatesKit.requiredPlugins[slug] = true;
                        }
                    });
                },
                error: function(xhr, ajaxOptions, thrownerror) {
                    console.log(xhr.errorCode)
                    if ('folder_exists' === xhr.errorCode) {
                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: {
                                action: 'ma_activate_required_plugins',
                                plugin: slug,
                                nonce: MATemplatesKitLoc.nonce,
                            },
                            success: function(response) {
                                MATemplatesKit.requiredPlugins[slug] = true;
                            }
                        });
                    }
                },
            });
        },

        maFixCompatibility: function() {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'ma_fix_compatibility',
                    nonce: MATemplatesKitLoc.nonce,
                },
                success: function(response) {
                    console.log('Plugins compatibility fixed successfully!');
                },
                error: function(response) {
                    console.log('No plugins compatibility issues!');
                }
            });
        },

        importTemplatesKit: function(kitID) {
            console.log('Installing Plugins...');
            MATemplatesKit.importProgressBar('plugins');
            MATemplatesKit.installRequiredPlugins(kitID);
            MATemplatesKit.maFixCompatibility();

            var installPlugins = setInterval(function() {

                if (Object.values(MATemplatesKit.requiredPlugins).every(Boolean) && MATemplatesKit.requiredTheme) {
                    // Reset Previous Kit (if any) and then Import New one
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'ma_reset_previous_import',
                            nonce: MATemplatesKitLoc.nonce,
                        },
                        success: function(response) {
                            console.log('Importing Templates Kit: '+ kitID +'...');
                            MATemplatesKit.importProgressBar('content');

                            // Import Kit
                            $.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: {
                                    action: 'ma_import_templates_kit',
                                    nonce: MATemplatesKitLoc.nonce,
                                    ma_templates_kit: kitID,
                                    ma_templates_kit_single: false
                                },
                                success: function(response) {
                                    // needs check to display errors only
                                    if (undefined !== response.success) {
                                        $('.progress-wrap, .ma-import-help').addClass('import-error');
                                        $('.ma-import-help a').attr('href', $('.ma-import-help a').attr('href') + '-xml-'+ response.data['problem'] +'-failed');
                                        $('.progress-wrap').find('strong').html(response.data['error'] +'<br><span>'+ response.data['help'] +'<span>');
                                        $('.ma-import-help a').html('Contact Support <span class="dashicons dashicons-email"></span>');
                                        return false;
                                    }

                                    console.log('Setting up Final Settings...');
                                    MATemplatesKit.importProgressBar('settings');

                                    // Final Adjustments
                                    $.ajax({
                                        type: 'POST',
                                        url: ajaxurl,
                                        data: {
                                            action: 'ma_final_settings_setup',
                                            nonce: MATemplatesKitLoc.nonce,
                                        },
                                        success: function(response) {
                                            setTimeout(function(){
                                                console.log('Import Finished!');
                                                MATemplatesKit.importProgressBar('finish');
                                            }, 1000);
                                        },
                                        error: function(xhr, textStatus, errorThrown) {
                                            console.error('AJAX-error:', textStatus, errorThrown);
                                        }
                                    });
                                },
                                error: function(xhr, textStatus, errorThrown) {
                                    console.error('AJAX-error:', textStatus, errorThrown);
                                }
                            });
                        },
                    });

                    // Clear
                    clearInterval(installPlugins);
                }
            }, 1000);
        },

        importSingleTemplate: function(kitID, templateID) {
            // Import Kit
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'ma_import_templates_kit',
                    nonce: MATemplatesKitLoc.nonce,
                    ma_templates_kit: kitID,
                    ma_templates_kit_single: templateID
                },
                success: function(response) {
                    console.log(response)
                }
            });
        },

        importProgressBar: function(step) {
            if ('plugins' === step) {
                $('.ma-import-kit-popup .progress-wrap strong').html('Step 1: Installing/Activating Plugins<span class="dot-flashing"></span>');
            } else if ('content' === step) {
                $('.ma-import-kit-popup .progress-bar').animate({'width' : '33%'}, 500);
                $('.ma-import-kit-popup .progress-wrap strong').html('Step 2: Importing Demo Content<span class="dot-flashing"></span>');
            } else if ('settings' === step) {
                $('.ma-import-kit-popup .progress-bar').animate({'width' : '66%'}, 500);
                $('.ma-import-kit-popup .progress-wrap strong').html('Step 3: Importing Settings<span class="dot-flashing"></span>');
            } else if ('finish' === step) {
                var href = window.location.href,
                    index = href.indexOf('/wp-admin'),
                    homeUrl = href.substring(0, index);

                $('.ma-import-kit-popup .progress-bar').animate({'width' : '100%'}, 500);
                $('.ma-import-kit-popup .content').children('p').remove();
                $('.ma-import-kit-popup .progress-wrap').before('<p>Your Template Kit has been successfully imported! You can now <strong><a href="'+ homeUrl +'" target="_blank">Visit your Site</a></strong> to see the changes or <strong><a href="admin.php?page=master-addons-settings">configure Master Addons</a></strong> settings.</p>');
                $('.ma-import-kit-popup .progress-wrap strong').html('Step 4: Import Completed Successfully - <a href="'+ homeUrl +'" target="_blank">Visit Site</a>');
                $('.ma-import-kit-popup header h3').text('Import was Successful!');
                $('.ma-import-kit-popup-wrap .close-btn').show();
            }
        },

        showTemplatesMainGrid: function() {
            $(this).hide();
            $('.ma-templates-kit-single').hide();
            $('.ma-templates-kit-page-title').show();
            $('.ma-templates-kit-grid.main-grid').show();
            $('.ma-templates-kit-search').show();
            $('.ma-templates-kit-price-filter').show();
            $('.ma-templates-kit-logo').find('.back-btn').css('display', 'none');
        },

        showImportPage: function(kit) {
            $('.ma-templates-kit-page-title').hide();
            $('.ma-templates-kit-grid.main-grid').hide();
            $('.ma-templates-kit-search').hide();
            $('.ma-templates-kit-price-filter').hide();
            $('.ma-templates-kit-single .action-buttons-wrap').css('margin-left', $('#adminmenuwrap').outerWidth());
            $('.ma-templates-kit-single').show();
            $('.ma-templates-kit-logo').find('.back-btn').css('display', 'flex');
            $('.ma-templates-kit-single .preview-demo').attr('href', 'https://demosites.master-addons.com/'+ kit.data('kit-id') +'?ref=ma-plugin-backend-templates');

            if (true === kit.data('developer')) {
                $('.ma-templates-kit-expert-notice').show();
            } else {
                $('.ma-templates-kit-expert-notice').hide();
            }
        },

        renderImportPage: function(kit) {
            var kitID = kit.data('kit-id'),
                pagesAttr = kit.data('pages') !== undefined ? kit.data('pages') : false,
                pagesArray = pagesAttr ? pagesAttr.split(',') : false,
                singleGrid = $('.ma-templates-kit-grid.single-grid');

            // Reset
            singleGrid.html('');

            // Render
            if (pagesArray) {
                for (var i = 0; i < pagesArray.length - 1; i++) {
                    singleGrid.append('\
                        <div class="grid-item" data-page-id="'+ pagesArray[i] +'">\
                            <a href="https://demosites.master-addons.com/'+ kit.data('kit-id') +'?ref=ma-plugin-backend-templates" target="_blank">\
                            <div class="image-wrap">\
                                <img src="https://master-addons.com/templates-kit/'+ kitID +'/'+ pagesArray[i] +'.jpg">\
                            </div>\
                            <footer><h3>'+ pagesArray[i] +'</h3></footer>\
                            </a>\
                        </div>\
                    ');
                }
            }

            if ($('.ma-templates-kit-grid').find('.grid-item[data-kit-id="'+ kit.data('kit-id') +'"]').data('price') === 'pro') {
                $('.ma-templates-kit-single').find('.import-kit').hide();
                $('.ma-templates-kit-single').find('.get-access').show();
            } else {
                $('.ma-templates-kit-single').find('.get-access').hide();
                $('.ma-templates-kit-single').find('.import-kit').show();

                // Set Kit ID
                $('.ma-templates-kit-single').find('.import-kit').attr('data-kit-id', kit.data('kit-id'));
            }
        },

        setActiveTemplateID: function(template) {
            // Reset
            $('.ma-templates-kit-grid.single-grid').find('.grid-item').removeClass('selected-template');

            // Set ID
            template.addClass('selected-template');
            var id = $('.ma-templates-kit-grid.single-grid').find('.selected-template').data('page-id');

            $('.ma-templates-kit-single').find('.import-template').attr('data-template-id', id);
            $('.ma-templates-kit-single').find('.import-template strong').text(id);

            // Set Preview Link
            $('.ma-templates-kit-single').find('.preview-demo').attr('href', $('.ma-templates-kit-single').find('.preview-demo').attr('href') +'/'+ id);
        },

        initializeLazyLoading: function() {
            var lazyImages = $('img.lazy');

            if ("IntersectionObserver" in window) {
                var lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var lazyImage = $(entry.target);
                            lazyImage.attr('src', lazyImage.data('src'));
                            lazyImage.removeClass('lazy');
                            lazyImageObserver.unobserve(entry.target);
                        }
                    });
                });

                lazyImages.each(function() {
                    lazyImageObserver.observe(this);
                });
            } else {
                // Fallback for browsers that do not support IntersectionObserver
                var lazyLoadThrottleTimeout;
                function lazyLoad() {
                    if (lazyLoadThrottleTimeout) {
                        clearTimeout(lazyLoadThrottleTimeout);
                    }

                    lazyLoadThrottleTimeout = setTimeout(function() {
                        var scrollTop = $(window).scrollTop();
                        lazyImages.each(function() {
                            var img = $(this);
                            if (img.offset().top < (window.innerHeight + scrollTop)) {
                                img.attr('src', img.data('src'));
                                img.removeClass('lazy');
                            }
                        });
                        if (lazyImages.length == 0) {
                            $(document).off("scroll", lazyLoad);
                            $(window).off("resize", lazyLoad);
                            $(window).off("orientationChange", lazyLoad);
                        }
                    }, 20);
                }

                $(document).on("scroll", lazyLoad);
                $(window).on("resize", lazyLoad);
                $(window).on("orientationChange", lazyLoad);
            }
        },

        searchTemplatesKit: function(tag, html) {
            var price = $('.ma-templates-kit-price-filter').children().first().attr('data-price'),
                priceAttr = 'mixed' === price ? '' : '[data-price*="'+ price +'"]';

            if ('' !== tag) {
                $('.main-grid .grid-item').hide();
                $('.main-grid .grid-item[data-tags*="'+ tag +'"]'+ priceAttr).show();
            } else {
                $('.main-grid').html(html);
                $('.main-grid .grid-item'+ priceAttr).show();
            }

            if (!$('.main-grid .grid-item').is(':visible')) {
                $('.ma-templates-kit-page-title').hide();
                $('.ma-templates-kit-not-found').css('display', 'flex');
            } else {
                $('.ma-templates-kit-not-found').hide();
                $('.ma-templates-kit-page-title').show();
            }

            // Reorder Search according to Title match
            $('.main-grid .grid-item:visible').each(function(i){
                if ('' !== tag) {
                    let title = $(this).attr('data-title');

                    if (-1 === title.indexOf(tag)) {
                        $('.main-grid').append($(this).remove());
                    }
                }
            });

            MATemplatesKit.initializeLazyLoading();
        },

        fiterFreeProTemplates: function(price) {
            var tag = $('.ma-templates-kit-search').find('input').val(),
                tagAttr = '' === tag ? '' : '[data-tags*="'+ tag +'"]';

            if ('free' == price) {
                $('.main-grid .grid-item').hide();
                $('.main-grid .grid-item[data-price*="'+ price +'"]'+ tagAttr).show();
            } else if ('pro' == price) {
                $('.main-grid .grid-item').hide();
                $('.main-grid .grid-item[data-price*="'+ price +'"]'+ tagAttr).show();
            } else {
                $('.main-grid .grid-item'+ tagAttr).show();
            }
        }
    }

    MATemplatesKit.init();

}); // end dom ready
