jQuery(document).ready(function (e) {
  "use strict";

  // Inject toaster CSS styles into the page
  if (!jQuery('#jltma-toaster-styles').length) {
    var toasterStyles = '<style id="jltma-toaster-styles">' +
      '.jltma-toaster-container {' +
      '  position: fixed;' +
      '  bottom: 40px;' +
      '  left: 50%;' +
      '  transform: translateX(-50%);' +
      '  z-index: 999999;' +
      '  pointer-events: none;' +
      '  display: flex;' +
      '  flex-direction: column;' +
      '  align-items: center;' +
      '  gap: 12px;' +
      '}' +
      '.jltma-toaster {' +
      '  display: flex;' +
      '  align-items: center;' +
      '  gap: 14px;' +
      '  padding: 18px 28px;' +
      '  background: #fff;' +
      '  border-radius: 12px;' +
      '  box-shadow: 0 8px 32px rgba(0,0,0,0.25), 0 2px 8px rgba(0,0,0,0.15);' +
      '  pointer-events: auto;' +
      '  opacity: 0;' +
      '  transform: translateY(30px) scale(0.9);' +
      '  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);' +
      '  min-width: 350px;' +
      '  max-width: 550px;' +
      '  border: 1px solid rgba(0,0,0,0.08);' +
      '}' +
      '.jltma-toaster-show {' +
      '  opacity: 1 !important;' +
      '  transform: translateY(0) scale(1) !important;' +
      '}' +
      '.jltma-toaster .dashicons {' +
      '  font-size: 28px;' +
      '  width: 28px;' +
      '  height: 28px;' +
      '  flex-shrink: 0;' +
      '  display: flex;' +
      '  align-items: center;' +
      '  justify-content: center;' +
      '}' +
      '.jltma-toaster-success {' +
      '  background: linear-gradient(135deg, #5bcc7f 0%, #48bb78 100%);' +
      '  border-left: 5px solid #059669;' +
      '}' +
      '.jltma-toaster-success .dashicons {' +
      '  color: #fff;' +
      '}' +
      '.jltma-toaster-error {' +
      '  background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);' +
      '  border-left: 5px solid #dc2626;' +
      '}' +
      '.jltma-toaster-error .dashicons {' +
      '  color: #fff;' +
      '}' +
      '.jltma-toaster-message {' +
      '  font-size: 15px;' +
      '  font-weight: 600;' +
      '  color: #fff;' +
      '  line-height: 1.5;' +
      '  letter-spacing: 0.01em;' +
      '}' +
      '</style>';
    jQuery('head').append(toasterStyles);
  }

  // Toaster notification function - Define early so it's available everywhere
  window.showToaster = function(message, type) {
    type = type || 'success';
    var toasterClass = type === 'success' ? 'jltma-toaster-success' : 'jltma-toaster-error';
    var iconClass = type === 'success' ? 'dashicons-yes-alt' : 'dashicons-warning';

    var toaster = jQuery('<div class="jltma-toaster ' + toasterClass + '">' +
      '<span class="dashicons ' + iconClass + '"></span>' +
      '<span class="jltma-toaster-message">' + message + '</span>' +
    '</div>');

    var container = jQuery('.jltma-toaster-container');
    if (container.length === 0) {
      container = jQuery('<div class="jltma-toaster-container"></div>').appendTo('body');
    }

    container.append(toaster);

    // Trigger animation
    setTimeout(function() {
      toaster.addClass('jltma-toaster-show');
    }, 10);

    // Remove after 3 seconds
    setTimeout(function() {
      toaster.removeClass('jltma-toaster-show');
      setTimeout(function() {
        toaster.remove();
      }, 300);
    }, 3000);
  };

  var megamenu_enable = "megamenu_enable",
    enable_megamenu = jQuery("#jltma-menu-metabox-input-is-enabled").is(
      ":checked"
    )
      ? 1
      : 0,
    jltma_megamenu_nonce = window.jltma_megamenu_nonce,
    JLTMA_Mega_Menu = {
      Enable_Mega_Menu: function () {
        var $checkbox = jQuery("#jltma-menu-metabox-input-is-enabled");
        var enable_megamenu = $checkbox.is(":checked") ? 1 : 0;
        var $spinner = $checkbox.closest('.master-mega-menu-accordion').find(".spinner");

        // Update UI immediately for better UX
        if (enable_megamenu) {
          e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled");
        } else {
          e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled");
        }

        // Show loading state
        $spinner.addClass("loading").css('visibility', 'visible');

        e.ajax({
          url: ajaxurl,
          type: "post",
          data: {
            action: "jltma_save_megamenu_options",
            is_enabled: enable_megamenu,
          },
          headers: { "X-WP-Nonce": jltma_megamenu_nonce },
          dataType: 'json'
        })
          .done(function (response) {
            $spinner.removeClass("loading").css('visibility', 'hidden');

            // Handle both JSON string and object responses
            if (typeof response === 'string') {
              try {
                response = JSON.parse(response);
              } catch(e) {
                console.error('Failed to parse response:', e);
                if (typeof window.showToaster === 'function') {
                  window.showToaster('Mega Menu settings saved successfully!', 'success');
                }
                return;
              }
            }

            if (response && response.status == "success") {
              // Update body classes based on saved state
              if (enable_megamenu) {
                e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled");
              } else {
                e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled");
              }

              localStorage.setItem("megamenu_enable", enable_megamenu);

              // Show success message via toaster
              var message = response.message || (enable_megamenu ? 'Mega Menu enabled successfully!' : 'Mega Menu disabled successfully!');
              if (typeof window.showToaster === 'function') {
                window.showToaster(message, 'success');
              }
            } else {
              // Show error message
              var errorMsg = response && response.message ? response.message : 'Failed to save Mega Menu settings';
              if (typeof window.showToaster === 'function') {
                window.showToaster(errorMsg, 'error');
              }

              // Revert checkbox state on error
              $checkbox.prop('checked', !enable_megamenu);
              if (!enable_megamenu) {
                e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled");
              } else {
                e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled");
              }
            }
          })
          .fail(function (xhr, status, error) {
            $spinner.removeClass("loading").css('visibility', 'hidden');

            console.error('AJAX Error:', status, error);
            if (typeof window.showToaster === 'function') {
              window.showToaster('Failed to save Mega Menu settings. Please try again.', 'error');
            }

            // Revert checkbox state on error
            $checkbox.prop('checked', !enable_megamenu);
            if (!enable_megamenu) {
              e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled");
            } else {
              e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled");
            }
          });

        return false;
      },

      Menu_Item_Settings_Save: function () {
        var t = e("#jltma-menu-metabox-input-is-enabled:checked").length,
          n = e("#jltma-menu-metabox-input-menu-id").val(),
          i = e(this).parent().find(".spinner"),
          m = {
            is_enabled: t,
            menu_id: n,
          };
        i.addClass("loading"),
          e
            .get(
              masteraddons_megamenu.resturl + "megamenu/save_megamenu_settings",
              m
            )
            .done(function (e) {
              i.removeClass("loading");
            });
      },

      Menu_Item_Save: function () {
        var t = e(this).parent().find(".spinner"),
          n = {
            settings: {
              menu_id: e("#jltma-menu-modal-menu-id").val(),
              menu_has_child: e("#jltma-menu-modal-menu-has-child").val(),
              menu_enable: e("#jltma-menu-item-enable:checked").val(),
              menu_label_enable: e("#mega-menu-hide-item-label:checked").val(),
              menu_transition: e("#mega-menu-transition-effect").val(),
              menu_disable_description: e(
                "#jltma-menu-disable-description:checked"
              ).val(),
              menu_icon: e("#jltma-menu-icon-field").val(),
              menu_trigger_effect: e("#mega-menu-trigger-effect").val(),
              menu_icon_color: e("#jltma-menu-icon-color-field").val(),
              menu_badge_text: e("#jltma-menu-badge-text-field").val(),
              menu_width_type: e("#jltma-megamenu-width-type").val(),
              menu_width_size: e("#jltma-megamenu-width").val(),
              menu_mobile_submenu_content_type: e(
                "#jltma-mobile-submenu-type"
              ).val(),
              menu_badge_color: e("#jltma-menu-badge-color-field").val(),
              // menu_mobile_submenu_content_type:e("#jltma-mobile-submenu-type").val(),
              menu_badge_background: e(
                "#jltma-menu-badge-background-field"
              ).val(),
            },
          };

        t.addClass("loading"),
          e.ajax({
            url:
              masteraddons_megamenu.resturl +
              "megamenu/jltma_save_menuitem_settings",
            type: "get",
            data: n,
            headers: { "X-WP-Nonce": jltma_megamenu_nonce },
            dataType: "json",
            success: function (n) {
              t.removeClass("loading"),
                e("#jltma-menu-item-settings-modal").modal("hide");
            },
          });

        // t.addClass("loading"), e.get(masteraddons_megamenu.resturl + "megamenu/jltma_save_menuitem_settings", n).done(function (n) {
        //     t.removeClass("loading"), e("#jltma-menu-item-settings-modal").modal("hide");
        // });
      },

      Menu_Trigger: function () {
        var t = e("#jltma-menu-modal-menu-id").val(),
          baseUrl = masteraddons_megamenu.resturl +
            "mastermega-content/jltma_content_editor/megamenu/menuitem/" + t,
          separator = baseUrl.indexOf('?') !== -1 ? '&' : '?',
          n = baseUrl + separator + "_wpnonce=" + jltma_megamenu_nonce;
        e("#jltma-menu-builder-iframe").attr("src", n);
      },
    };

  // Need to Work on this section
  e(".jltma-menu-settings-save").on("click", () =>
    JLTMA_Mega_Menu.Menu_Item_Settings_Save(e)
  );

  // e(".jltma-menu-item-save").on("click", ()=>JLTMA_Mega_Menu.Menu_Item_Save(e));

  e(".jltma-menu-item-save").on("click", function () {
    var t = e(this).parent().find(".spinner"),
      n = {
        settings: {
          menu_id: e("#jltma-menu-modal-menu-id").val(),
          menu_has_child: e("#jltma-menu-modal-menu-has-child").val(),
          menu_disable_description: e(
            "#jltma-menu-disable-description:checked"
          ).val(),
          menu_enable: e("#jltma-menu-item-enable:checked").val(),
          menu_icon: e("#jltma-menu-icon-field").val(),
          menu_trigger_effect: e("#mega-menu-trigger-effect").val(),
          menu_icon_color: e("#jltma-menu-icon-color-field").val(),
          menu_label_enable: e("#mega-menu-hide-item-label:checked").val(),
          menu_transition: e("#mega-menu-transition-effect").val(),
          menu_badge_text: e("#jltma-menu-badge-text-field").val(),
          menu_width_type: e("#jltma-megamenu-width-type").val(),
          menu_width_size: e("#jltma-megamenu-width").val(),
          menu_mobile_submenu_content_type: e(
            "#jltma-mobile-submenu-type"
          ).val(),
          menu_badge_color: e("#jltma-menu-badge-color-field").val(),
          // menu_mobile_submenu_content_type:e("#jltma-mobile-submenu-type").val(),
          menu_badge_background: e("#jltma-menu-badge-background-field").val(),
        },
      };

    t.addClass("loading"),
      e.ajax({
        url:
          masteraddons_megamenu.resturl + "megamenu/jltma_save_menuitem_settings",
        type: "get",
        data: n,
        headers: { "X-WP-Nonce": jltma_megamenu_nonce },
        dataType: "json",
        success: function (n) {
          t.removeClass("loading");
          // Show toaster notification
          showToaster('Settings saved successfully!', 'success');
        },
        error: function (xhr, status, error) {
          t.removeClass("loading");
          showToaster('Failed to save settings. Please try again.', 'error');
        }
      });

    // t.addClass("loading"), e.get(masteraddons_megamenu.resturl + "megamenu/jltma_save_menuitem_settings", n).done(function (n) {
    //     t.removeClass("loading"), e("#jltma-menu-item-settings-modal").modal("hide");
    // });
  }),
    e("#jltma-menu-builder-trigger").on("click", () =>
      JLTMA_Mega_Menu.Menu_Trigger(e)
    );

  e("body").on("DOMSubtreeModified", "#menu-to-edit", function () {
    setTimeout(function () {
      e("#menu-to-edit li.menu-item").each(function () {
        var t = e(this);
        t.find(".jltma_menu_trigger").length < 1 &&
          e(".item-title", t).append(
            "<a data-toggle='modal' data-target='#jltma_megamenu_modal' href='#' class='jltma_menu_trigger'>Master Mega</a> "
          );
      });
    }, 200);
  }),
    e("#menu-to-edit").trigger("DOMSubtreeModified"),
    e("#menu-to-edit").on("click", ".jltma_menu_trigger", function (n) {
      n.preventDefault();

      var i = e("#jltma_megamenu_modal"),
        m = e(this).parents("li.menu-item"),
        l = parseInt(m.attr("id").match(/[0-9]+/)[0], 10);

      m.find(".menu-item-title").text(),
        m.attr("class").match(/\menu-item-depth-(\d+)\b/)[1];

      if (
        (e(".jltma_menu_control_nav > li").removeClass("active"),
        e(".jltma-tab-pane").removeClass("active"),
        1 == e(this).parent().find(".is-submenu").is(":hidden"))
      ) {
        var a = 0;
        i.removeClass("jltma-menu-has-child"),
          e("#content_nav").addClass("active"),
          e("#content_tab").addClass("active");
      } else {
        a = 1;
        i.addClass("jltma-menu-has-child"),
          e("#general_nav").addClass("active"),
          e("#general_tab").addClass("active show");
      }

      e("#jltma-menu-modal-menu-id").val(l),
        e("#jltma-menu-modal-menu-has-child").val(a);

      var o = { menu_id: l };

      e.ajax({
        url: masteraddons_megamenu.resturl + "megamenu/get_menuitem_settings",
        type: "get",
        data: o,
        headers: { "X-WP-Nonce": jltma_megamenu_nonce },
        dataType: "json"
      }).done(function (n) {
        e("#jltma-menu-item-enable").prop("checked", !1),
          e("#mega-menu-trigger-effect").val(n.menu_trigger_effect),
          //    e("#jltma-mobile-submenu-type").val(n.menu_mobile_submenu_content_type),
          // Set color picker values - modern color picker compatible
          e("#jltma-menu-icon-color-field").val(n.menu_icon_color || '').trigger('change'),
          // Set icon field - use default if empty
          (function() {
            var defaultIcon = 'fas fa-chevron-down';
            var iconValue = n.menu_icon || defaultIcon;
            var $preview = e("#jltma-menu-icon-preview");

            e("#jltma-menu-icon-field").val(iconValue);
            $preview.attr('class', iconValue);
            $preview.attr('style', 'font-size: 20px; color: #fff; display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px;');

            if (n.menu_icon) {
              e(".jltma-icon-delete-btn").css({'display': 'flex', 'align-items': 'center', 'justify-content': 'center'});
            } else {
              e(".jltma-icon-delete-btn").css('display', 'none');
            }
          })(),
          e("#mega-menu-transition-effect").val(n.menu_transition),
          e("#mega-menu-hide-item-label").prop("checked", !1),
          void 0 !== typeof n.menu_label_enable && 1 == n.menu_label_enable
            ? e("#mega-menu-hide-item-label").prop("checked", !0)
            : e("#mega-menu-hide-item-label").prop("checked", !1),
          e("#jltma-menu-badge-text-field").val(n.menu_badge_text),
          e("#jltma-megamenu-width-type").val(n.menu_width_type || "default"),
          e("#jltma-megamenu-width").val(n.menu_width_size || "1000px"),
          e("#jltma-mobile-submenu-type").val(
            n.menu_mobile_submenu_content_type || "builder_content"
          ),
          e("#jltma-menu-disable-description").prop("checked", !1),
          void 0 !== typeof n.menu_disable_description &&
          1 == n.menu_disable_description
            ? e("#jltma-menu-disable-description").prop("checked", !0)
            : e("#jltma-menu-disable-description").prop("checked", !1),
          // Set color picker values - modern color picker compatible
          e("#jltma-menu-badge-color-field").val(n.menu_badge_color || '#6814cd').trigger('change'),
          e("#jltma-menu-badge-background-field").val(n.menu_badge_background || '#6814cd').trigger('change'),
          void 0 !== typeof n.menu_enable && 1 == n.menu_enable
            ? e("#jltma-menu-item-enable").prop("checked", !0)
            : e("#jltma-menu-item-enable").prop("checked", !1),
          //    void 0!==typeof n.menu_mobile_submenu_content_type&&1==n.menu_mobile_submenu_content_type?e("#menu_mobile_submenu_content_type").prop("checked", !0): e("#menu_mobile_submenu_content_type").prop("checked", !1),
          //    e("#menu_mobile_submenu_content_type input").prop("checked", !1), void 0===typeof n.menu_mobile_submenu_content_type||"builder_content"==n.menu_mobile_submenu_content_type?e("#menu_mobile_submenu_content_type input[value=builder_content]").prop("checked", !0):e("#menu_mobile_submenu_content_type input[value=submenu_list]").prop("checked", !0),
          e("#jltma-menu-item-enable").trigger("change"),
          n.menu_width_type == "custom_width"
            ? e("#jltma-megamenu-width").removeClass("hidden")
            : e("#jltma-megamenu-width").addClass("hidden"),
          setTimeout(function () {
            i.removeClass("jltma-menu-modal-loading");
            i.addClass("show");
          }, 500);
      });
    });

  e("#jltma-menu-item-enable").on("change", function () {
    e(this).is(":checked")
      ? (e("#jltma-menu-builder-trigger").prop("disabled", !1),
        e("#jltma-menu-builder-wrapper").addClass("is_enabled"))
      : (e("#jltma-menu-item-enable").prop("checked", !1),
        e("#jltma-menu-builder-wrapper").removeClass("is_enabled"),
        e("#jltma-menu-builder-trigger").prop("disabled", !0));
  });

  e("#jltma-mega-menu-settings").on(
    "change",
    "#jltma-menu-metabox-input-is-enabled",
    () => JLTMA_Mega_Menu.Enable_Mega_Menu(e)
  );

  e("#jltma-megamenu-width-type").on("change", function () {
    if (this.value == "custom_width") {
      e(this).siblings("#jltma-megamenu-width").removeClass("hidden");
    } else {
      e(this).siblings("#jltma-megamenu-width").addClass("hidden");
    }
  });

  e.ajax({
    url: ajaxurl,
    type: "get",
    data: {
      action: "jltma_get_megamenu_options",
    },
    headers: { "X-WP-Nonce": jltma_megamenu_nonce },
  }).done(function (response) {
    response = jQuery.parseJSON(response);
    var isMegaEnabled = response && response.is_enabled == "1";

    if (isMegaEnabled) {
      e("body").addClass("is_mega_enabled").removeClass("is_mega_disabled");
      localStorage.setItem("megamenu_enable", "1");
      e("#jltma-menu-metabox-input-is-enabled").prop("checked", true);
    } else {
      e("body").removeClass("is_mega_enabled").addClass("is_mega_disabled");
      localStorage.setItem("megamenu_enable", "0");
      e("#jltma-menu-metabox-input-is-enabled").prop("checked", false);
    }
  });

  // Close modal when clicking outside
  jQuery(document).on("click", function (e) {
    if (
      jQuery("#jltma_megamenu_modal").hasClass("show") &&
      !jQuery(e.target).closest(".jltma-modal-dialog, .jltma_menu_trigger")
        .length
    ) {
      jQuery("#jltma_megamenu_modal").removeClass("show");
    }
  });

  jQuery("body").on("click", ".jltma-modal-dialog", function (e) {
    e.stopPropagation();
  });

  // Close button handler for mega menu modal
  jQuery("body").on("click", "#jltma_megamenu_modal .jltma-pop-close, #jltma_megamenu_modal .close-btn", function (e) {
    e.preventDefault();
    jQuery("#jltma_megamenu_modal").removeClass("show");
  });

  // Builder modal trigger
  jQuery("body").on("click", "#jltma-menu-builder-trigger", function (e) {
    jQuery("#jltma-mega-menu-builder-modal").toggleClass("show");
  });

  // Close button handler for builder modal
  jQuery("body").on("click", "#jltma-mega-menu-builder-modal .jltma-pop-close", function (e) {
    e.preventDefault();
    jQuery("#jltma-mega-menu-builder-modal").removeClass("show");
  });

  // ESC key support to close modals
  jQuery(document).on("keydown", function (e) {
    if (e.key === "Escape" || e.keyCode === 27) {
      if (jQuery("#jltma_megamenu_modal").hasClass("show")) {
        jQuery("#jltma_megamenu_modal").removeClass("show");
      }
      if (jQuery("#jltma-mega-menu-builder-modal").hasClass("show")) {
        jQuery("#jltma-mega-menu-builder-modal").removeClass("show");
      }
    }
  });

  // Tabs
  jQuery(".jltma-tab-content > div").hide();
  jQuery(".jltma-tab-content > div:first-of-type").show();
  jQuery(".jltma-tabs a").click(function (e) {
    e.preventDefault();
    var jQuerythis = jQuery(this),
      tabgroup =
        "#" + jQuerythis.parents(".jltma-tabs").data("jltma-tab-content"),
      others = jQuerythis.closest("li").siblings().children("a"),
      target = jQuerythis.attr("href");
    others.removeClass("active");
    jQuerythis.addClass("active");
    jQuery(tabgroup).children("div").hide();
    jQuery(target).show();
    jQuery(target).siblings().hide();
  });

  // Modern Icon Picker Implementation
  function initModernIconPicker() {
    var iconLibraryConfig = masteraddons_megamenu?.iconLibrary || {};
    var allIcons = [];
    var sidebarList = [];

    // Build sidebar list and icons array
    sidebarList.push({
      title: 'All Icons',
      'list-icon': 'dashicons dashicons-marker',
      'library-id': 'all'
    });

    Object.entries(iconLibraryConfig).forEach(function([libraryName, libraryData]) {
      Object.entries(libraryData).forEach(function([categoryName, categoryData]) {
        var iconTitle = categoryName !== '' ? libraryName + ' - ' + categoryName : libraryName;

        // Add to sidebar
        sidebarList.push({
          title: iconTitle,
          'list-icon': categoryData['list-icon'] || '',
          'library-id': categoryData['icon-style'] || 'all'
        });

        // Add icons
        if (categoryData.icons && Array.isArray(categoryData.icons)) {
          categoryData.icons.forEach(function(iconName) {
            var fullClass = categoryData.prefix + iconName;
            allIcons.push({
              class: fullClass,
              name: iconName,
              library: categoryData['icon-style'],
              prefix: categoryData.prefix
            });
          });
        }
      });
    });

    // Delete icon button handler
    jQuery(document).on('click', '.jltma-icon-delete-btn', function(e) {
      e.preventDefault();
      e.stopPropagation();

      console.log('=== DELETE ICON CLICKED ===');

      var $deleteBtn = jQuery(this);
      var $wrapper = $deleteBtn.closest('.jltma-modern-icon-picker-wrapper');
      var $input = $wrapper.find('.icon-picker-input');
      var $preview = jQuery('#jltma-menu-icon-preview'); // Use direct ID selector
      var $button = $wrapper.find('.icon-picker');

      console.log('Found elements:', {
        preview: $preview.length,
        input: $input.length,
        button: $button.length
      });

      console.log('BEFORE - Preview state:', {
        class: $preview.attr('class'),
        style: $preview.attr('style'),
        display: $preview.css('display'),
        element: $preview[0]
      });

      // Clear the input value
      $input.val('');
      console.log('Input cleared, value:', $input.val());

      // Hide icon preview completely - remove all classes and hide element
      $preview.attr('class', ''); // Remove all classes
      $preview.attr('style', 'font-size: 24px; color: #fff; display: none !important;'); // Force hide with important

      // Also try with hide() method as backup
      $preview.hide();

      // Reset icon preview box border to default
      jQuery('#jltma-icon-preview-box').css('border-color', '#2d3748');

      console.log('AFTER - Preview state:', {
        class: $preview.attr('class'),
        style: $preview.attr('style'),
        display: $preview.css('display'),
        element: $preview[0]
      });

      // Hide delete button
      $deleteBtn.hide();

      // Remove has-icon class from button
      $button.removeClass('has-icon');

      console.log('=== DELETE COMPLETE ===');
    });

    // Replace old icon picker button click with modern modal
    jQuery(document).on('click', '.icon-picker', function(e) {
      e.preventDefault();
      e.stopPropagation();

      console.log('Icon picker clicked');

      var $button = jQuery(this);
      var $wrapper = $button.closest('.jltma-modern-icon-picker-wrapper');
      var $input = $wrapper.find('.icon-picker-input');
      var $preview = $wrapper.find('#jltma-menu-icon-preview');
      var $deleteBtn = $wrapper.find('.jltma-icon-delete-btn');
      var currentIcon = $input.val();

      console.log('Icon picker data:', {
        allIconsCount: allIcons.length,
        currentIcon: currentIcon,
        hasIconLibrary: !!masteraddons_megamenu?.iconLibrary
      });

      if (allIcons.length === 0) {
        alert('Icon library not loaded. Please refresh the page.');
        return;
      }

      showModernIconPickerModal(currentIcon, function(selectedIcon) {
        $input.val(selectedIcon);

        // Update icon preview - use direct selector and attr for consistency
        var $iconPreview = jQuery('#jltma-menu-icon-preview');
        $iconPreview.attr('class', selectedIcon);
        $iconPreview.attr('style', 'font-size: 24px; color: #fff; display: block;');

        // Show delete button
        if ($deleteBtn.length > 0) {
          $deleteBtn.css({'display': 'flex', 'align-items': 'center', 'justify-content': 'center'});
        }

        // Update button to show selected state
        $button.addClass('has-icon');

        console.log('Icon selected:', selectedIcon);
      });
    });

    function showModernIconPickerModal(currentIcon, onSelect) {
      var activeLibrary = 'all';
      var searchTerm = '';
      var selectedIcon = currentIcon || '';

      // Auto-select the library of the current icon
      if (currentIcon) {
        var currentIconData = allIcons.find(function(icon) {
          return icon.class === currentIcon;
        });
        if (currentIconData) {
          activeLibrary = currentIconData.library;
        }
      }

      // Create modal HTML
      var modalHtml = '<div class="aim-modal aim-open jltma-megamenu-icon-modal">' +
        '<div class="aim-modal--content">' +
          '<div class="aim-modal--header">' +
            '<div class="aim-modal--header-logo-area">' +
              '<span class="aim-modal--header-logo-title">' +
                '<img src="' + masteraddons_megamenu.pluginUrl + '/assets/images/logo.svg" ' +
                     'alt="Master Addons" style="width: 24px; height: 24px; margin-right: 8px;" />' +
                'Master Addons Icon Picker' +
              '</span>' +
            '</div>' +
            '<div class="aim-modal--header-close-btn">' +
              '<span class="dashicons dashicons-no-alt" title="Close"></span>' +
            '</div>' +
          '</div>' +
          '<div class="aim-modal--body">' +
            '<div class="aim-modal--sidebar">' +
              '<div class="aim-modal--sidebar-tabs">' +
                sidebarList.map(function(item, index) {
                  var isActive = activeLibrary === item['library-id'] ? 'aesthetic-active' : '';
                  var icon = item['list-icon'] ? '<i class="' + item['list-icon'] + '"></i>' : '';
                  return '<div class="aim-modal--sidebar-tab-item ' + isActive + '" data-library-id="' + item['library-id'] + '">' +
                    icon + item.title +
                  '</div>';
                }).join('') +
              '</div>' +
            '</div>' +
            '<div class="aim-modal--icon-preview-wrap">' +
              '<div class="aim-modal--icon-search">' +
                '<input type="text" placeholder="Filter by name..." class="jltma-icon-search-input" />' +
                '<i class="dashicons dashicons-search"></i>' +
              '</div>' +
              '<div class="aim-modal--icon-preview-inner">' +
                '<div id="aim-modal--icon-preview"></div>' +
              '</div>' +
            '</div>' +
          '</div>' +
          '<div class="aim-modal--footer">' +
            '<button class="aim-insert-icon-button" type="button">Insert</button>' +
          '</div>' +
        '</div>' +
      '</div>';

      // Append modal to body
      var $modal = jQuery(modalHtml);
      jQuery('body').append($modal);

      // Render icons
      function renderIcons() {
        var filteredIcons = allIcons.filter(function(icon) {
          var matchesSearch = searchTerm === '' || icon.name.toLowerCase().includes(searchTerm.toLowerCase());
          var matchesLibrary = activeLibrary === 'all' || icon.library === activeLibrary;
          return matchesSearch && matchesLibrary;
        });

        var iconsHtml = filteredIcons.map(function(icon) {
          var isSelected = selectedIcon === icon.class ? 'aesthetic-selected' : '';
          var displayName = icon.name.replace(/-/g, ' ');
          return '<div class="aim-icon-item ' + isSelected + '" data-icon-class="' + icon.class + '">' +
            '<div class="aim-icon-item-inner">' +
              '<i class="' + icon.class + '"></i>' +
              '<div class="aim-icon-item-name" title="' + icon.name + '">' + displayName + '</div>' +
            '</div>' +
          '</div>';
        }).join('');

        $modal.find('#aim-modal--icon-preview').html(iconsHtml);
      }

      renderIcons();

      // Event handlers
      $modal.find('.aim-modal--header-close-btn, .aim-modal').on('click', function(e) {
        if (e.target === this) {
          $modal.remove();
        }
      });

      $modal.find('.aim-modal--content').on('click', function(e) {
        e.stopPropagation();
      });

      $modal.find('.aim-modal--sidebar-tab-item').on('click', function() {
        activeLibrary = jQuery(this).data('library-id');
        $modal.find('.aim-modal--sidebar-tab-item').removeClass('aesthetic-active');
        jQuery(this).addClass('aesthetic-active');
        renderIcons();
      });

      $modal.find('.jltma-icon-search-input').on('input', function() {
        searchTerm = jQuery(this).val();
        renderIcons();
      });

      $modal.on('click', '.aim-icon-item', function() {
        selectedIcon = jQuery(this).data('icon-class');
        $modal.find('.aim-icon-item').removeClass('aesthetic-selected');
        jQuery(this).addClass('aesthetic-selected');
      });

      $modal.find('.aim-insert-icon-button').on('click', function() {
        if (selectedIcon) {
          onSelect(selectedIcon);
        }
        $modal.remove();
      });
    }
  }

  // Initialize modern icon picker
  initModernIconPicker();

  // Modern Color Picker Implementation
  var colorPickerInitialized = false;

  function initModernColorPicker() {
    if (colorPickerInitialized) return;

    // First initialize wpColorPicker on all color inputs
    jQuery('.jltma-menu-wpcolor-picker').each(function() {
      var $input = jQuery(this);
      if (!$input.hasClass('wp-color-picker')) {
        $input.wpColorPicker();
      }
    });

    // Wait a bit for wpColorPicker to initialize, then replace with modern picker
    setTimeout(function() {
      // Replace all wpColorPicker instances with modern color pickers
      jQuery('.jltma-menu-wpcolor-picker').each(function() {
        var $input = jQuery(this);

        // Skip if already converted
        if ($input.attr('type') === 'hidden' && $input.siblings('.jltma-modern-color-picker').length > 0) {
          return;
        }

        var initialColor = $input.val() || '';

        // Create modern color picker HTML - Input first, then preview
        var colorPickerHtml = '<div class="jltma-modern-color-picker">' +
          '<input type="text" class="jltma-color-input" value="' + initialColor + '" placeholder="#000000" />' +
          '<div class="jltma-color-preview" style="background-color: ' + initialColor + ';">' +
            '<div class="jltma-color-preview-inner"></div>' +
          '</div>' +
          '<input type="color" class="jltma-color-native" value="' + initialColor + '" />' +
        '</div>';

        // Store input ID
        var inputId = $input.attr('id');

        // Find and replace wpColorPicker container
        var $container = $input.closest('.wp-picker-container');
        if ($container.length > 0) {
          $container.replaceWith(colorPickerHtml);
          var $modernPicker = jQuery('.jltma-modern-color-picker').last();
          $modernPicker.attr('data-input-id', inputId);

          // Keep the original hidden input
          $input.attr('type', 'hidden').insertAfter($modernPicker);
        }
      });

      colorPickerInitialized = true;
    }, 500);
  }

  // Event handlers for modern color picker
  jQuery(document).on('click', '.jltma-color-preview', function() {
    jQuery(this).siblings('.jltma-color-native').click();
  });

  jQuery(document).on('input change', '.jltma-color-native', function() {
    var color = jQuery(this).val();
    var $picker = jQuery(this).closest('.jltma-modern-color-picker');
    var inputId = $picker.attr('data-input-id');

    $picker.find('.jltma-color-preview').css('background-color', color);
    $picker.find('.jltma-color-input').val(color);
    jQuery('#' + inputId).val(color).trigger('change');
  });

  jQuery(document).on('input change', '.jltma-color-input', function() {
    var color = jQuery(this).val();
    var $picker = jQuery(this).closest('.jltma-modern-color-picker');
    var inputId = $picker.attr('data-input-id');

    if (/^#[0-9A-F]{6}$/i.test(color) || /^#[0-9A-F]{3}$/i.test(color)) {
      $picker.find('.jltma-color-preview').css('background-color', color);
      $picker.find('.jltma-color-native').val(color);
      jQuery('#' + inputId).val(color).trigger('change');
    }
  });

  jQuery(document).on('click', '.jltma-color-clear', function() {
    var $picker = jQuery(this).closest('.jltma-modern-color-picker');
    var inputId = $picker.attr('data-input-id');

    $picker.find('.jltma-color-preview').css('background-color', '');
    $picker.find('.jltma-color-input').val('');
    $picker.find('.jltma-color-native').val('#000000');
    jQuery('#' + inputId).val('').trigger('change');
  });

  // Listen for programmatic changes to hidden color inputs (when loading saved values)
  jQuery(document).on('change', '.jltma-menu-wpcolor-picker', function() {
    var $hiddenInput = jQuery(this);
    var color = $hiddenInput.val();
    var inputId = $hiddenInput.attr('id');

    // Find the corresponding modern color picker
    var $picker = jQuery('.jltma-modern-color-picker[data-input-id="' + inputId + '"]');

    if ($picker.length > 0 && color) {
      $picker.find('.jltma-color-preview').css('background-color', color);
      $picker.find('.jltma-color-input').val(color);
      $picker.find('.jltma-color-native').val(color);
    }
  });

  // Color picker is now handled by inline implementation in modal-options.php
  // Disabled old wpColorPicker replacement to avoid conflicts

  // jQuery(document).on('click', '.jltma_menu_trigger', function() {
  //   setTimeout(function() {
  //     initModernColorPicker();
  //   }, 300);
  // });

  // jQuery(document).ready(function() {
  //   if (jQuery('#jltma-mega-menu-settings').length > 0) {
  //     initModernColorPicker();
  //   }
  // });
});
