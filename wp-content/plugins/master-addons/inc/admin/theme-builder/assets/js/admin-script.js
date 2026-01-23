/*
 * Master Header & Footer
 */
(function ($) {
  "use strict";

  // Toaster Notification System
  var JLTMA_Toaster = {
    container: null,

    init: function () {
      // Create container if it doesn't exist
      if (!this.container) {
        this.container = $('<div class="jltma-toaster-container"></div>');
        $("body").append(this.container);
      }
    },

    show: function (message, type = "success", duration = 3000) {
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
      toaster.find(".jltma-toaster-close").on("click", function () {
        JLTMA_Toaster.dismiss(toaster);
      });

      // Auto dismiss after duration
      if (duration > 0) {
        setTimeout(function () {
          JLTMA_Toaster.dismiss(toaster);
        }, duration);
      }

      return toaster;
    },

    dismiss: function (toaster) {
      toaster.addClass("jltma-toaster-exit");
      setTimeout(function () {
        toaster.remove();
      }, 300);
    },

    success: function (message, duration) {
      return this.show(message, "success", duration);
    },

    error: function (message, duration) {
      return this.show(message, "error", duration);
    },

    warning: function (message, duration) {
      return this.show(message, "warning", duration);
    },

    info: function (message, duration) {
      return this.show(message, "info", duration);
    },
  };

  var Master_Header_Footer = {
    Url_Param_Replace: function (url, paramName, paramValue) {
      if (paramValue == null) {
        paramValue = "";
      }
      var pattern = new RegExp("\\b(" + paramName + "=).*?(&|#|$)");
      if (url.search(pattern) >= 0) {
        return url.replace(pattern, "$1" + paramValue + "$2");
      }
      url = url.replace(/[?#]$/, "");
      return (
        url + (url.indexOf("?") > 0 ? "&" : "?") + paramName + "=" + paramValue
      );
    },

    JLTMA_Template_Editor: function (data) {
      try {
        (function ($) {
          // Set the form data
          $(".jltma_hf_modal-title").val(data.title);

          // Set template type - ensure it's set correctly
          var typeSelect = $(".jltma_hfc_type");
          typeSelect.val(data.type || "header"); // Default to header if empty
          // Force trigger change to ensure any dependent logic runs
          typeSelect.trigger("change");

          // Set activation status
          var activation_input = $(".jltma-enable-switcher");
          if (data.activation == "yes") {
            activation_input.prop("checked", true);
          } else {
            activation_input.prop("checked", false);
          }
          // Trigger change event to ensure any dependent styling/behavior is updated
          activation_input.trigger("change");

          // Clear existing conditions repeater
          $("#jltma-conditions-repeater").empty();

          // Populate new conditions repeater
          let conditionData = [];

          // Check if we have new repeater data first
          if (
            data.conditions_data &&
            Array.isArray(data.conditions_data) &&
            data.conditions_data.length > 0
          ) {
            conditionData = data.conditions_data;
          } else {
            // Convert old format to new repeater format
            if (
              data.jltma_hf_conditions &&
              data.jltma_hf_conditions !== "entire_site"
            ) {
              let specificValue = "";
              let posts = [];

              if (data.jltma_hf_conditions === "singular") {
                if (
                  data.jltma_hfc_singular === "selective" &&
                  data.jltma_hfc_singular_id
                ) {
                  specificValue = data.jltma_hfc_singular_id;
                }
              } else if (data.jltma_hf_conditions === "archive") {
                // Handle archive conditions - check for specific archive type
                if (data.jltma_hfc_post_types_id) {
                  specificValue = data.jltma_hfc_post_types_id;
                }
              }

              conditionData.push({
                type: "include",
                rule: data.jltma_hf_conditions,
                specific: specificValue,
                posts: posts,
              });
            } else {
              // Default condition
              conditionData.push({
                type: "include",
                rule: "entire_site",
                specific: "",
                posts: [],
              });
            }
          }

          // Populate repeater with conditions
          // Track the highest index for future additions
          let maxConditionIndex = 0;
          conditionData.forEach(function (condition, index) {
            let woocommerceOptions = "";
            if (
              typeof masteraddons !== "undefined" &&
              masteraddons.woocommerce_active
            ) {
              woocommerceOptions = `
                  <option value="product">Product</option>
                  <option value="product_archive">Product Archive</option>
                `;
            }

            const newRow = `
                <div class="jltma-condition-row" data-index="${index}">
                  <div class="jltma-condition-controls">
                    <div class="jltma-condition-field">
                      <select name="jltma_condition_type[]" class="jltma-condition-select jltma-condition-type">
                        <option value="include" ${
                          condition.type === "include" ? "selected" : ""
                        }>Include</option>
                        <option value="exclude" ${
                          condition.type === "exclude" ? "selected" : ""
                        }>Exclude</option>
                      </select>
                    </div>
                    <div class="jltma-condition-field">
                      <select name="jltma_condition_rule[]" class="jltma-condition-select jltma-condition-rule">
                        <option value="entire_site" ${
                          condition.rule === "entire_site" ? "selected" : ""
                        }>Entire Site</option>
                        <option value="front_page" ${
                          condition.rule === "front_page" ? "selected" : ""
                        }>Front Page</option>
                        <option value="singular" ${
                          condition.rule === "singular" ? "selected" : ""
                        }>Singular</option>
                        <option value="archive" ${
                          condition.rule === "archive" ? "selected" : ""
                        }>Archive</option>
                        <option value="search" ${
                          condition.rule === "search" ? "selected" : ""
                        }>Search</option>
                        <option value="404" ${
                          condition.rule === "404" ? "selected" : ""
                        }>404 Page</option>
                        ${woocommerceOptions}
                      </select>
                    </div>
                    <div class="jltma-condition-field jltma-condition-specific-field" style="${
                      condition.rule === "singular" ||
                      condition.rule === "archive"
                        ? "display: block;"
                        : "display: none;"
                    }">
                      <select name="jltma_condition_specific[]" class="jltma-condition-select jltma-condition-specific-select">
                        <option value="">All</option>
                      </select>
                    </div>
                    <button type="button" class="jltma-remove-condition" title="Remove Condition">
                      <i class="eicon-close"></i>
                    </button>
                  </div>
                </div>
              `;
            $("#jltma-conditions-repeater").append(newRow);

            // Populate specific select if needed
            const addedRow = $("#jltma-conditions-repeater")
              .find(".jltma-condition-row")
              .last();
            const specificSelect = addedRow.find(
              ".jltma-condition-specific-select"
            );

            if (condition.rule === "singular") {
              Master_Header_Footer.Load_Post_Types(specificSelect);
              if (condition.specific) {
                // Set value after a small delay to ensure options are loaded
                setTimeout(function () {
                  specificSelect.val(condition.specific);
                }, 200); // Increase delay to ensure AJAX loads

                // If there are selected posts, create and populate the post selection dropdown
                if (condition.posts && condition.posts.length > 0) {
                  setTimeout(function () {
                    // Trigger the change event to create the post selection dropdown
                    specificSelect.trigger("change");

                    // Store the posts data on the row for later use
                    addedRow.data("selected-posts", condition.posts);

                    // Wait for the dropdown to be created and then populate it
                    setTimeout(function () {
                      Master_Header_Footer.populatePostSelection(
                        addedRow,
                        condition.posts
                      );
                    }, 800); // Increased delay for better reliability
                  }, 300);
                }
              }
            } else if (condition.rule === "archive") {
              Master_Header_Footer.Load_Archive_Types(specificSelect);
              if (condition.specific) {
                // Set value after a small delay to ensure options are loaded
                setTimeout(function () {
                  specificSelect.val(condition.specific);
                }, 200); // Increase delay to ensure AJAX loads
              }
            }

            maxConditionIndex = Math.max(maxConditionIndex, index + 1);
          });

          // Set the global condition index for future additions
          if (typeof window.masterHeaderFooterConditionIndex !== "undefined") {
            window.masterHeaderFooterConditionIndex = maxConditionIndex + 1;
          }

          // Legacy support for old format (hidden fields)
          $(".jltma_hf_modal-jltma_hf_conditions").val(
            data.jltma_hf_conditions
          );
          $(".jltma_hf_modal-jltma_hfc_singular").val(data.jltma_hfc_singular);
          $(".jltma_hf_modal-jltma_hfc_singular_id").val(
            data.jltma_hfc_singular_id
          );
          $(".jltma_hf_conditions").val(data.jltma_hf_conditions);
          $(".jltma_hf_modal-jltma_hfc_post_types_id").val(
            data.jltma_hfc_post_types_id
          );

          // Trigger change events for all form elements
          $(".jltma-enable-switcher, .jltma_hfc_type").trigger("change");
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Modal_Singular_List: function () {
      // Initialize legacy singular list select2 for backward compatibility
      $(".jltma_hf_modal-jltma_hfc_singular_id").select2({
        ajax: {
          url: window.masteraddons.resturl + "select2/singular_list",
          type: "get",
          dataType: "json",
          headers: {
            "X-WP-Nonce": window.masteraddons.rest_nonce,
          },
          data: function (params) {
            var query = {
              s: params.term,
            };
            return query;
          },
        },
        cache: true,
        placeholder: "Search for posts/pages...",
        dropdownParent: $("#jltma_hf_modal_body"),
      });

      // Also setup search functionality for new condition system
      this.Setup_Condition_Search();
    },

    Setup_Condition_Search: function () {
      // Legacy function disabled - post selection now handled by new change event handlers
      // This prevents the post type dropdown from being converted to Select2
      try {
        // Functionality moved to new condition change handlers in Condition_Actions
      } catch (e) {
        console.error("Error in legacy Setup_Condition_Search:", e);
      }
    },

    Modal_Submit: function () {
      try {
        (function ($) {
          $("#jltma_hf_modal_form").on("submit", function (e) {
            e.preventDefault();

            // Clear any existing validation messages
            $(".jltma-validation-message").remove();

            // Validate conditions before submitting
            var validationResult = Master_Header_Footer.Validate_Conditions();
            if (!validationResult.valid) {
              // Show validation message in the UI instead of alert
              var validationHtml =
                '<div class="jltma-validation-message">' +
                validationResult.message +
                "</div>";
              $(".jltma-tab-conditions .jltma-conditions-section").prepend(
                validationHtml
              );

              // Switch to conditions tab if not already active
              if (!$(".jltma-tab-conditions").hasClass("active")) {
                $(".jltma-tab-button").removeClass("active");
                $(".jltma-tab-content").removeClass("active");
                $('.jltma-tab-button[data-tab="conditions"]').addClass(
                  "active"
                );
                $(".jltma-tab-conditions").addClass("active");
              }

              // Scroll to top of conditions section
              $(".jltma-tab-conditions").scrollTop(0);

              return false;
            }

            var modal = $("#jltma_hf_modal");
            modal.addClass("loading");

            var form_data = $(this).serialize(),
              id = $(this).attr("data-jltma-hf-id"),
              jltma_hfc_nonce = $(this).attr("data-nonce"),
              open_editor = $(this).attr("data-open-editor"),
              admin_url = $(this).attr("data-editor-url");

            $.ajax({
              url: window.masteraddons.resturl + "ma-template/update/" + id,
              data: form_data,
              type: "get",
              dataType: "json",
              headers: {
                "X-WP-Nonce": jltma_hfc_nonce,
              },
              success: function (output) {
                setTimeout(function () {
                  modal.removeClass("loading");
                }, 1500);

                // Show toaster notification
                JLTMA_Toaster.success("Template saved successfully!");

                var row = $("#post-" + output.data.id);

                if (row.length > 0) {
                  row.find(".column-type").html(output.data.type_html);

                  row.find(".column-condition").html(output.data.cond_text);

                  row
                    .find(".row-title")
                    .html(output.data.title)
                    .attr("aria-label", output.data.title);
                }

                $("#jltma_hf_modal").removeClass("show");

                if (open_editor == "1") {
                  window.location.href =
                    admin_url + "?post=" + output.data.id + "&action=elementor";
                } else if (id == "0") {
                  location.reload();
                }
              },
              error: function (xhr, status, error) {
                modal.removeClass("loading");

                var errorMessage =
                  "An error occurred while saving the template.";

                // Try to parse backend validation error
                if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                  try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                      errorMessage = response.message;
                    }
                  } catch (e) {
                    // If JSON parsing fails, use default message
                  }
                }

                // Show error message in the UI
                $(".jltma-validation-message").remove();
                var errorHtml =
                  '<div class="jltma-validation-message">' +
                  errorMessage +
                  "</div>";
                $(".jltma-tab-conditions .jltma-conditions-section").prepend(
                  errorHtml
                );

                // Switch to conditions tab if not already active
                if (!$(".jltma-tab-conditions").hasClass("active")) {
                  $(".jltma-tab-button").removeClass("active");
                  $(".jltma-tab-content").removeClass("active");
                  $('.jltma-tab-button[data-tab="conditions"]').addClass(
                    "active"
                  );
                  $(".jltma-tab-conditions").addClass("active");
                }

                console.error("Template save error:", error);

                // Show error toaster notification
                JLTMA_Toaster.error(
                  "Failed to save template. Please try again."
                );
              },
            });
          });
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Open_Editor: function () {
      try {
        (function ($) {
          $(".jltma-btn-editor").on("click", function () {
            var form = $("#jltma_hf_modal_form");
            form.attr("data-open-editor", "1");
            form.trigger("submit");
          });
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Choose_Template_Singular_Condition: function () {
      try {
        (function ($) {
          // Singular Condition
          $(".jltma_hf_modal-jltma_hfc_singular").on("change", function () {
            var jltma_hfc_singular = $(this).val();
            var inputs = $(".jltma_hf_modal-jltma_hfc_singular_id-container");

            if (jltma_hfc_singular == "selective") {
              inputs.show();
            } else {
              inputs.hide();
            }
          });
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Choose_Template_Post_Types_Condition: function () {
      try {
        (function ($) {
          // Post Types Condition
          $(".jltma_hf_modal-jltma_hf_conditions").on("change", function () {
            var jltma_hfc_singular = $(this).val();
            var inputs = $(".jltma_hf_modal-jltma_hfc_post_types_id-container");

            if (jltma_hfc_singular == "post_types") {
              inputs.show();
            } else {
              inputs.hide();
            }
          });
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Choose_Template_Conditions: function () {
      try {
        (function ($) {
          $(".jltma_hf_modal-jltma_hf_conditions")
            .unbind()
            .on("change", function () {
              var jltma_hf_conditions = $(this).val(),
                inputs = $(".jltma_hf_modal-jltma_hfc_singular-container");

              // else if (jltma_hf_conditions == "post_types") {
              //   inputs.find(".jltma_hf_modal-jltma_hfc_post_types_id-container").show();
              // }

              if (jltma_hf_conditions == "singular") {
                inputs.show();
              } else if (
                jltma_hf_conditions == "jltma-hfc-single-pro" ||
                jltma_hf_conditions == "post_types_pro" ||
                jltma_hf_conditions == "jltma-hfc-archive-pro" ||
                jltma_hf_conditions == "search_pro" ||
                jltma_hf_conditions == "404_pro" ||
                jltma_hf_conditions == "product_pro" ||
                jltma_hf_conditions == "product_archive_pro"
              ) {
                $(".jltma-hfc-popup-upgade").remove();
                $(".jltma_hf_modal-jltma_hf_conditions").after(
                  '<div class="jltma-hfc-popup-upgade"> ' +
                    masteraddons.upgrade_pro +
                    "</div>"
                );
              } else {
                inputs.hide();
                $(".jltma-hfc-popup-upgade").hide();
              }
            });
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Choose_Template_Type: function () {
      try {
        (function ($) {
          $(".jltma_hfc_type").on("change", function () {
            var type = $(this).val(),
              label = $(".jltma-hfc-hide-item-label"),
              inputs = $(".jltma_hf_options_container");
            console.log( 'type', type, 'label', label , 'inputs', inputs );
            if (type == "section" || type == "comment") {
              inputs.hide();
              label.hide();
              $('.jltma-modal-footer').show();
              $('.jltma-tab-button[data-tab="conditions"]').show();
            } else if(type == "single_pro" || type == "archive_pro" || type == "search_pro" || type == "404_pro"){
              inputs.hide();
              label.hide();
              $('.jltma-modal-footer').hide();
              $('.jltma-tab-button[data-tab="conditions"]').hide();
            }else {
              label.show();
              inputs.show();
              $('.jltma-modal-footer').show();
              $('.jltma-tab-button[data-tab="conditions"]').show();
            }
          });
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Conditions_Repeater: function () {
      try {
        (function ($) {
          // Use global condition index or start at 1
          window.masterHeaderFooterConditionIndex =
            window.masterHeaderFooterConditionIndex || 1;

          // Add new condition
          $(document).on("click", "#jltma-add-condition", function () {
            const repeater = $("#jltma-conditions-repeater");
            let woocommerceOptions = "";

            // Add WooCommerce options if available
            if (
              typeof masteraddons !== "undefined" &&
              masteraddons.woocommerce_active
            ) {
              woocommerceOptions = `
                  <option value="product">Product</option>
                  <option value="product_archive">Product Archive</option>
                `;
            }

            const newRow = `
                <div class="jltma-condition-row" data-index="${window.masterHeaderFooterConditionIndex}">
                  <div class="jltma-condition-controls">
                    <div class="jltma-condition-field">
                      <select name="jltma_condition_type[]" class="jltma-condition-select jltma-condition-type">
                        <option value="include">Include</option>
                        <option value="exclude">Exclude</option>
                      </select>
                    </div>
                    <div class="jltma-condition-field">
                      <select name="jltma_condition_rule[]" class="jltma-condition-select jltma-condition-rule">
                        <option value="entire_site">Entire Site</option>
                        <option value="front_page">Front Page</option>
                        <option value="singular">Singular</option>
                        <option value="archive">Archive</option>
                        <option value="search">Search</option>
                        <option value="404">404 Page</option>
                        ${woocommerceOptions}
                      </select>
                    </div>
                    <div class="jltma-condition-field jltma-condition-specific-field" style="display: none;">
                      <select name="jltma_condition_specific[]" class="jltma-condition-select jltma-condition-specific-select">
                        <option value="">All</option>
                      </select>
                    </div>
                    <button type="button" class="jltma-remove-condition" title="Remove Condition">
                      <i class="eicon-close"></i>
                    </button>
                  </div>
                </div>
              `;
            repeater.append(newRow);
            window.masterHeaderFooterConditionIndex++;

            // Show repeater if it was hidden
            repeater.show();

            // Trigger change event for the new row to set up dependencies
            const newRowElement = repeater.find(".jltma-condition-row").last();
            newRowElement.find(".jltma-condition-rule").trigger("change");
          });

          // Remove condition
          $(document).on("click", ".jltma-remove-condition", function () {
            const row = $(this).closest(".jltma-condition-row");
            const repeater = $("#jltma-conditions-repeater");

            row.remove();

            // Hide repeater if no conditions remain
            if (repeater.find(".jltma-condition-row").length === 0) {
              repeater.hide();
            }
          });

          // Handle condition rule changes
          $(document).on("change", ".jltma-condition-rule", function () {
            const row = $(this).closest(".jltma-condition-row");
            const specificField = row.find(".jltma-condition-specific-field");
            const specificSelect = row.find(".jltma-condition-specific-select");
            const value = $(this).val();

            // Clear validation messages when conditions change
            $(".jltma-validation-message").remove();

            // Remove error styling from current row
            row
              .find(
                ".jltma-condition-type, .jltma-condition-rule, .jltma-condition-specific-select"
              )
              .removeClass("jltma-field-error");

            // Destroy any existing Select2 instances on sub-selects first
            row.find(".jltma-condition-sub-select").each(function () {
              if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2("destroy");
              }
            });

            // Remove any existing search input and sub-select
            row.find(".jltma-condition-search-input").remove();
            row.find(".jltma-condition-sub-select").remove();

            // Destroy any existing Select2 instances on other elements
            row.find(".select2-hidden-accessible").each(function () {
              $(this).select2("destroy");
            });

            // Clear existing options
            specificSelect.empty().append('<option value="">All</option>');

            if (value === "singular") {
              // Show and populate with post types
              specificField.show();
              Master_Header_Footer.Load_Post_Types(specificSelect);
            } else if (value === "archive") {
              // Show and populate with archive types
              specificField.show();
              Master_Header_Footer.Load_Archive_Types(specificSelect);
            } else {
              specificField.hide();
            }
          });

          // Handle when specific post type is selected for singular conditions
          $(document).on(
            "change",
            ".jltma-condition-specific-select",
            function () {
              const row = $(this).closest(".jltma-condition-row");
              const ruleSelect = row.find(".jltma-condition-rule");
              const value = $(this).val();
              const selectElement = $(this);

              // Destroy any existing Select2 on sub-selects before removing them
              row.find(".jltma-condition-sub-select").each(function () {
                if ($(this).hasClass("select2-hidden-accessible")) {
                  $(this).select2("destroy");
                }
              });

              // Remove any existing search input and sub-select
              row.find(".jltma-condition-search-input").remove();
              row.find(".jltma-condition-sub-select").remove();

              // If a specific post type is selected for singular rule, show post selection dropdown
              if (ruleSelect.val() === "singular" && value && value !== "") {
                const postTypeLabel = selectElement
                  .find("option:selected")
                  .text();

                // Get the row index for proper form naming (use actual position in repeater)
                const rowIndex = $(
                  "#jltma-conditions-repeater .jltma-condition-row"
                ).index(row);

                // Create a Select2 dropdown for posts of this type with unique ID
                const uniqueId =
                  "post-select-" +
                  Date.now() +
                  "-" +
                  Math.random().toString(36).substr(2, 9);
                const postSelect = $(`
                  <select name="jltma_condition_posts[${rowIndex}][]" 
                    id="${uniqueId}"
                    class="jltma-form-control jltma-condition-sub-select" 
                    data-post-type="${value}"
                    multiple>
                  </select>
                `);

                // Insert before the remove button to maintain proper order
                const removeButton = row.find(".jltma-remove-condition");
                removeButton.before(postSelect);

                // Small delay to ensure DOM is ready
                setTimeout(function () {
                  // Initialize Select2 ONLY on the post selection dropdown, not the post type dropdown
                  postSelect.select2({
                    ajax: {
                      url:
                        window.masteraddons.resturl + "select2/singular_list",
                      type: "get",
                      dataType: "json",
                      delay: 250,
                      headers: {
                        "X-WP-Nonce": window.masteraddons.rest_nonce,
                      },
                      data: function (params) {
                        return {
                          s: params.term || "",
                          post_type: value,
                        };
                      },
                      processResults: function (data, params) {
                        const results = [];

                        // Add "All PostType" as first option when no search term
                        if (!params.term || params.term.length === 0) {
                          results.push({
                            id: "",
                            text: `All ${postTypeLabel}`,
                          });
                        }

                        // Add individual posts - data is already an array from the API
                        if (data && Array.isArray(data)) {
                          data.forEach((item) => {
                            results.push({
                              id: item.id,
                              text: item.text,
                            });
                          });
                        }

                        return {
                          results: results,
                        };
                      },
                      error: function (xhr, status, error) {
                        // Handle AJAX error silently or show user-friendly message
                      },
                    },
                    cache: true,
                    placeholder: `All ${postTypeLabel}`,
                    dropdownParent: $("#jltma_hf_modal_body"),
                    allowClear: true,
                    multiple: true,
                    minimumInputLength: 0,
                    escapeMarkup: function (markup) {
                      return markup;
                    },
                    language: {
                      inputTooShort: function () {
                        return `Select specific ${postTypeLabel.toLowerCase()} or leave empty for all`;
                      },
                      noResults: function () {
                        return `No ${postTypeLabel.toLowerCase()} found`;
                      },
                      searching: function () {
                        return `Searching ${postTypeLabel.toLowerCase()}...`;
                      },
                    },
                  });

                  // Check if there are saved posts for this row and restore them
                  const savedPosts = row.data("selected-posts");
                  if (savedPosts && savedPosts.length > 0) {
                    setTimeout(function () {
                      Master_Header_Footer.populatePostSelection(
                        row,
                        savedPosts
                      );
                    }, 300);
                  }
                }, 100);
              }
            }
          );

          // Clear validation messages when any condition field changes
          $(document).on(
            "change",
            ".jltma-condition-type, .jltma-condition-specific-select",
            function () {
              $(".jltma-validation-message").remove();
              $(this).removeClass("jltma-field-error");
            }
          );
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    Load_Post_Types: function (selectElement) {
      try {
        // Don't add "All" here as it's already added by the condition rule handler

        // Load all registered post types via AJAX
        $.ajax({
          url: window.masteraddons.resturl + "ma-template/post-types",
          type: "GET",
          headers: {
            "X-WP-Nonce": window.masteraddons.rest_nonce,
          },
          success: function (postTypes) {
            if (postTypes && Array.isArray(postTypes)) {
              postTypes.forEach(function (postType) {
                selectElement.append(
                  `<option value="${postType.value}">${postType.label}</option>`
                );
              });
            }
          },
          error: function () {
            // Fallback to common post types if AJAX fails
            const fallbackTypes = [
              { value: "post", label: "Post" },
              { value: "page", label: "Page" },
            ];

            if (
              typeof masteraddons !== "undefined" &&
              masteraddons.woocommerce_active
            ) {
              fallbackTypes.push({ value: "product", label: "Product" });
            }

            fallbackTypes.forEach(function (postType) {
              selectElement.append(
                `<option value="${postType.value}">${postType.label}</option>`
              );
            });
          },
        });
      } catch (e) {
        console.error("Error loading post types:", e);
      }
    },

    Load_Archive_Types: function (selectElement) {
      try {
        // Load all registered archive types via AJAX
        $.ajax({
          url: window.masteraddons.resturl + "ma-template/archive-types",
          type: "GET",
          headers: {
            "X-WP-Nonce": window.masteraddons.rest_nonce,
          },
          success: function (archiveTypes) {
            if (archiveTypes && Array.isArray(archiveTypes)) {
              archiveTypes.forEach(function (archiveType) {
                selectElement.append(
                  `<option value="${archiveType.value}">${archiveType.label}</option>`
                );
              });
            }
          },
          error: function () {
            // Fallback to common archive types if AJAX fails
            const fallbackTypes = [
              { value: "author", label: "Author Archive" },
              { value: "date", label: "Date Archive" },
              { value: "category", label: "Category Archive" },
              { value: "tag", label: "Tag Archive" },
            ];

            if (
              typeof masteraddons !== "undefined" &&
              masteraddons.woocommerce_active
            ) {
              fallbackTypes.push({
                value: "product_cat",
                label: "Product Category",
              });
              fallbackTypes.push({
                value: "product_tag",
                label: "Product Tag",
              });
            }

            fallbackTypes.forEach(function (archiveType) {
              selectElement.append(
                `<option value="${archiveType.value}">${archiveType.label}</option>`
              );
            });
          },
        });
      } catch (e) {
        console.error("Error loading archive types:", e);
      }
    },

    Validate_Conditions: function () {
      try {
        var hasInclude = false;
        var hasValidConditions = false;
        var conditionRows = $(
          "#jltma-conditions-repeater .jltma-condition-row"
        );
        var errors = [];

        // Allow empty conditions - templates can be created without conditions initially
        if (conditionRows.length === 0) {
          return {
            valid: true,
            message:
              "No conditions set - template will not display anywhere until conditions are added.",
          };
        }

        conditionRows.each(function (index) {
          var row = $(this);
          var type = row.find(".jltma-condition-type").val();
          var rule = row.find(".jltma-condition-rule").val();
          var specific = row.find(".jltma-condition-specific-select").val();
          var specificVisible = row
            .find(".jltma-condition-specific-select")
            .is(":visible");

          // Remove any existing error styling
          row
            .find(
              ".jltma-condition-type, .jltma-condition-rule, .jltma-condition-specific-select"
            )
            .removeClass("jltma-field-error");

          // Check if required fields are filled
          if (!type) {
            row.find(".jltma-condition-type").addClass("jltma-field-error");
            errors.push("Condition type is required for row " + (index + 1));
          }

          if (!rule) {
            row.find(".jltma-condition-rule").addClass("jltma-field-error");
            errors.push("Condition rule is required for row " + (index + 1));
          }

          // Check if specific field is required but empty
          if (
            specificVisible &&
            rule &&
            (rule === "singular" || rule === "archive") &&
            !specific
          ) {
            row
              .find(".jltma-condition-specific-select")
              .addClass("jltma-field-error");
            errors.push(
              "Specific selection is required for " +
                rule +
                " condition in row " +
                (index + 1)
            );
          }

          if (type && rule) {
            hasValidConditions = true;
            if (type === "include") {
              hasInclude = true;
            }
          }
        });

        if (errors.length > 0) {
          return {
            valid: false,
            message: errors[0], // Show first error
          };
        }

        if (!hasValidConditions) {
          return {
            valid: false,
            message: "Please complete all condition fields.",
          };
        }

        // Only require include conditions if conditions exist
        if (hasValidConditions && !hasInclude) {
          return {
            valid: false,
            message:
              'At least one "Include" condition is required. Templates must include at least one location where they should be displayed.',
          };
        }

        // Additional validation: Check for conflicting conditions
        var includeEntireSite = false;
        var hasOtherIncludes = false;

        conditionRows.each(function () {
          var type = $(this).find(".jltma-condition-type").val();
          var rule = $(this).find(".jltma-condition-rule").val();

          if (type === "include") {
            if (rule === "entire_site") {
              includeEntireSite = true;
            } else {
              hasOtherIncludes = true;
            }
          }
        });

        if (includeEntireSite && hasOtherIncludes) {
          return {
            valid: false,
            message:
              'You cannot use "Entire Site" with other include conditions. "Entire Site" includes everything already.',
          };
        }

        return { valid: true };
      } catch (e) {
        console.error("Validation error:", e);
        return {
          valid: false,
          message: "Validation error occurred.",
        };
      }
    },

    Modal_Add_Edit: function () {
      try {
        (function ($) {
          $(document).on(
            "click",
            ".row-actions .edit a, .page-title-action, .column-title .row-title, .jltma-theme-builder-edit-cond",
            function (e) {
              e.preventDefault();
              var id = 0,
                modal = $("#jltma_hf_modal"),
                jltma_hfc_nonce = $("#jltma_hf_modal_form").attr("data-nonce"),
                parent = $(this).parents(".column-title");

              modal.addClass("loading");
              modal.addClass("show");

              // Check if clicking from different locations
              if ($(this).hasClass("jltma-theme-builder-edit-cond")) {
                // For edit icon in conditions column, get ID directly from the button's id attribute
                id = $(this).attr("id");
              } else if (parent.length > 0) {
                // For clicks from column-title (template name)
                id = parent.find(".hidden").attr("id").split("_")[1];
              } else {
                // For "Add New" button or other cases, ID should be 0 for new template
                id = 0;
              }

              if (id > 0) {
                $.ajax({
                  url: window.masteraddons.resturl + "ma-template/get/" + id,
                  type: "get",
                  headers: { "X-WP-Nonce": jltma_hfc_nonce },
                  dataType: "json",
                  success: function (data) {
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                    modal.removeClass("loading");
                  },
                  error: function (xhr, status, error) {
                    console.error("Error loading template data:", error);
                    modal.removeClass("loading");
                    // Show default empty form on error
                    var data = {
                      title: "",
                      type: "header",
                      jltma_hf_conditions: "entire_site",
                      jltma_hfc_singular: "all",
                      activation: "",
                    };
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                  },
                });
              } else {
                var data = {
                  title: "",
                  type: "header",
                  jltma_hf_conditions: "entire_site",
                  jltma_hfc_singular: "all",
                  activation: "",
                };

                modal.removeClass("loading");
              }

              modal.find("form").attr("data-jltma-hf-id", id);

              // active tab & select value change
              let $select = $(".jltma_hfc_type");

              let activeTabTitle = $(
                ".master_type_filter_tab_container.nav-tab-wrapper .nav-tab-active"
              )
                .text()
                .trim();

              if ($select.length && activeTabTitle) {
                $select.find("option").each(function () {
                  let optionText = $(this).text().trim();
                  if (
                    optionText.toLowerCase() === activeTabTitle.toLowerCase()
                  ) {
                    if (
                      !$select.val() ||
                      $select.val().toLowerCase() !== optionText.toLowerCase()
                    ) {
                      $(this).prop("selected", true);
                      $select.trigger("change");
                    }
                    return false;
                  }
                });
              }

              let $conditionsBtn = $(".jltma-tab-button");

              if (
                ["search", "404 page"].includes(activeTabTitle.toLowerCase())
              ) {
                $conditionsBtn.remove();
              }

              // remove select option
              let $ruleSelect = $(
                ".jltma-condition-select.jltma-condition-rule"
              );

              function updateSelectOptions() {
                const activeTabTitle = $(
                  ".master_type_filter_tab_container .nav-tab-active"
                )
                  .text()
                  .trim()
                  .toLowerCase();

                let visibleOptions = [];
                switch (activeTabTitle) {
                  case "all":
                    visibleOptions = ["entire"];
                     var data = {
                      title: "",
                      type: "header",
                      jltma_hf_conditions: "entire_site",
                      jltma_hfc_singular: "all",
                      activation: "",
                    };
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                    break;
                  case "header":
                    visibleOptions = ["entire"];
                     var data = {
                      title: "",
                      type: "header",
                      jltma_hf_conditions: "entire_site",
                      jltma_hfc_singular: "all",
                      activation: "",
                    };
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                    break;
                  case "footer":
                    visibleOptions = ["entire"];
                     var data = {
                      title: "",
                      type: "footer",
                      jltma_hf_conditions: "entire_site",
                      jltma_hfc_singular: "all",
                      activation: "",
                    };
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                    break;
                  case "single":
                    visibleOptions = ["singular"];
                     data = {
                      title: "",
                      type: "single",
                      jltma_hf_conditions: "singular",
                      jltma_hfc_singular: "all",
                      activation: "",
                    };
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                    break;
                  case "archive":
                    visibleOptions = ["archive"];
                     data = {
                      title: "",
                      type: "archive",
                      jltma_hf_conditions: "archive",
                      jltma_hfc_singular: "all",
                      activation: "",
                    };
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                    break;
                  case "comment":
                    visibleOptions = ["singular"];
                    data = {
                      title: "",
                      type: "comment",
                      jltma_hf_conditions: "singular",
                      jltma_hfc_singular: "all",
                      activation: "",
                    };
                    Master_Header_Footer.JLTMA_Template_Editor(data);
                    break;
                  default:
                    visibleOptions = $ruleSelect
                      .find("option")
                      .map(function () {
                        return $(this).attr("value").toLowerCase();
                      })
                      .get();
                    break;
                  }


                $ruleSelect.find("option").each(function () {
                  const optionValue = $(this).attr("value").toLowerCase();
                  if (visibleOptions.includes(optionValue)) {
                    $(this).show();
                    if (
                      !$ruleSelect.val() ||
                      !visibleOptions.includes($ruleSelect.val().toLowerCase())
                    ) {
                      $ruleSelect.val($(this).val());
                    }
                  } else {
                    $(this).hide();
                  }
                });
              }

              $(document).ready(function () {
                updateSelectOptions();
              });
            }
          );

          // Tab switching functionality
          $(".jltma-tab-button").on("click", function (e) {
            e.preventDefault();
            var targetTab = $(this).data("tab");

            // Remove active class from all tabs and buttons
            $(".jltma-tab-button").removeClass("active");
            $(".jltma-tab-content").removeClass("active");

            // Add active class to clicked button and corresponding tab
            $(this).addClass("active");
            $(".jltma-tab-" + targetTab).addClass("active");
          });

          // Close modal functionality
        })(jQuery);
      } catch (e) {
        //We can also throw from try block and catch it here
        //e.preventDefault();
      }
    },

    // Helper function to populate post selection dropdown
    populatePostSelection: function (row, selectedPosts) {
      try {
        const postSelect = row.find(".jltma-condition-sub-select");

        if (
          postSelect.length &&
          postSelect.hasClass("select2-hidden-accessible")
        ) {
          // Convert selectedPosts to array if it's not already
          let postsArray = Array.isArray(selectedPosts) ? selectedPosts : [];

          // Convert string numbers to integers
          postsArray = postsArray
            .map((id) => parseInt(id, 10))
            .filter((id) => !isNaN(id));

          if (postsArray.length > 0) {
            // For Select2 with AJAX, we need to first add the options for the selected values
            // Get the post titles for the selected IDs via AJAX
            const postType = postSelect.data("post-type");

            $.ajax({
              url: window.masteraddons.resturl + "select2/singular_list",
              type: "get",
              dataType: "json",
              headers: {
                "X-WP-Nonce": window.masteraddons.rest_nonce,
              },
              data: {
                s: "", // Empty search to get all
                post_type: postType,
                ids: postsArray.join(","), // Pass the IDs we need
              },
              success: function (data) {
                if (data && Array.isArray(data)) {
                  // Add options for the selected posts, but only if they don't already exist
                  data.forEach(function (item) {
                    if (postsArray.includes(parseInt(item.id))) {
                      // Check if option already exists
                      const existingOption = postSelect.find(
                        `option[value="${item.id}"]`
                      );
                      if (existingOption.length === 0) {
                        const option = new Option(
                          item.text,
                          item.id,
                          true,
                          true
                        );
                        postSelect.append(option);
                      } else {
                        // If option exists, just mark it as selected
                        existingOption.prop("selected", true);
                      }
                    }
                  });

                  // Now set the values
                  postSelect.val(postsArray).trigger("change");
                }
              },
              error: function (xhr, status, error) {
                // Fallback: try to set values anyway
                postSelect.val(postsArray).trigger("change");
              },
            });
          }
        }
      } catch (error) {
        console.warn("Error populating post selection:", error);
      }
    },
  };

  jQuery(document).ready(function ($) {
    "use strict";

    // Modals - Handle close button and backdrop clicks
    $(document).on(
      "click",
      ".jltma-pop-close, .close-btn, .jltma-modal-backdrop",
      function (e) {
        if (
          $(e.target).hasClass("jltma-modal-backdrop") ||
          $(e.target).hasClass("close-btn") ||
          $(e.target).closest(".jltma-pop-close").length
        ) {
          $("#jltma_hf_modal").removeClass("show");
          e.preventDefault();
        }
      }
    );

    // Handle "Add New" button to show modal for new template
    $(".page-title-action").on("click", function (e) {
      $("#jltma_hf_modal").toggleClass("show");
      e.preventDefault();
    });

    Master_Header_Footer.Modal_Add_Edit();
    Master_Header_Footer.Choose_Template_Type();
    Master_Header_Footer.Choose_Template_Conditions();
    Master_Header_Footer.Choose_Template_Singular_Condition();
    Master_Header_Footer.Choose_Template_Post_Types_Condition();
    Master_Header_Footer.Open_Editor();
    Master_Header_Footer.Modal_Submit();
    Master_Header_Footer.Modal_Singular_List();
    Master_Header_Footer.Conditions_Repeater();

    // Copy to clipboard functionality for shortcodes
    $(document).on("click", ".jltma-copy-shortcode", function (e) {
      e.preventDefault();

      var shortcode = $(this).data("shortcode");
      var button = $(this);
      var originalIcon = button.find(".dashicons");

      // Use the modern clipboard API if available, fallback to older method
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard
          .writeText(shortcode)
          .then(function () {
            // Success - show feedback
            originalIcon
              .removeClass("dashicons-clipboard")
              .addClass("dashicons-yes-alt");
            button.css("background", "#46b450");
            JLTMA_Toaster.success("Shortcode copied to clipboard!", 2000);

            // Reset button after 2 seconds
            setTimeout(function () {
              originalIcon
                .removeClass("dashicons-yes-alt")
                .addClass("dashicons-clipboard");
              button.css("background", "#f9f9f9");
            }, 2000);
          })
          .catch(function () {
            // Fallback method
            copyToClipboardFallback(shortcode, button, originalIcon);
          });
      } else {
        // Fallback for older browsers
        copyToClipboardFallback(shortcode, button, originalIcon);
      }
    });

    // Fallback copy method for older browsers
    function copyToClipboardFallback(text, button, originalIcon) {
      // Create a temporary textarea element
      var textarea = $("<textarea>")
        .css({
          position: "fixed",
          top: "0",
          left: "0",
          opacity: "0",
          pointerEvents: "none",
        })
        .val(text);

      $("body").append(textarea);
      textarea[0].select();

      try {
        var success = document.execCommand("copy");
        if (success) {
          // Success - show feedback
          originalIcon
            .removeClass("dashicons-clipboard")
            .addClass("dashicons-yes-alt");
          button.css("background", "#46b450");
          JLTMA_Toaster.success("Shortcode copied to clipboard!", 2000);

          // Reset button after 2 seconds
          setTimeout(function () {
            originalIcon
              .removeClass("dashicons-yes-alt")
              .addClass("dashicons-clipboard");
            button.css("background", "#f9f9f9");
          }, 2000);
        } else {
          JLTMA_Toaster.error(
            "Failed to copy shortcode. Please copy manually.",
            3000
          );
        }
      } catch (err) {
        JLTMA_Toaster.error("Copy not supported. Please copy manually.", 3000);
      }

      textarea.remove();
    }

    var tab_container = $(".wp-header-end"),
      tabs = "",
      filter_types = [
        { key: "all", label: "All" },
        { key: "header", label: "Header" , type:"free" },
        { key: "footer", label: "Footer", type:"free" },
        { key: "comment", label: "Comment", type:"free" },
        { key: "single", label: "Single", type:"pro" },
        { key: "archive", label: "Archive", type:"pro" },
        { key: "search", label: "Search", type:"pro" },
        { key: "404", label: "404 Page", type:"pro" },
      ];

    // Add WooCommerce tabs if WooCommerce is active
    if (
      typeof masteraddons !== "undefined" &&
      masteraddons.woocommerce_active
    ) {
      filter_types.push({ key: "product", label: "Product" });
      filter_types.push({ key: "product_archive", label: "Product Archive" });
    }

    var url = new URL(window.location.href),
      s = url.searchParams.get("master_template_type_filter");

    s = s == null ? "all" : s;

    const proBadge = '<span class="pro-badge theme-builder-nav jltma-pro-disabled">Pro</span>';
    if( window.JLTMACORE.is_premium ){
      $.each(filter_types, function (index, item) {
        var url = Master_Header_Footer.Url_Param_Replace(
          window.location.href,
          "master_template_type_filter",
          item.key
        );
        var jlma_class =
          s == item.key ? "master_type_filter_active nav-tab-active" : " ";
        tabs += `
                  <a href="${url}" class="${jlma_class} master_type_filter_tab_item nav-tab">${item.label}</a>
              `;
        tabs += "\n";
      });
    }else{
      $.each(filter_types, function (index, item) {
        if( "pro" === item.type){
          tabs += `<div class="  master_type_filter_tab_item nav-tab pro">${item.label} ${proBadge}</div>`;
        }else{
          var url = Master_Header_Footer.Url_Param_Replace(
            window.location.href,
            "master_template_type_filter",
            item.key
          );
          var jlma_class =
            s == item.key ? "master_type_filter_active nav-tab-active" : " ";
          tabs += `
                    <a href="${url}" class="${jlma_class} master_type_filter_tab_item nav-tab">${item.label}</a>
                `;
          tabs += "\n";
        }
      });
    }
    tab_container.after(
      '<div class="master_type_filter_tab_container nav-tab-wrapper">' +
        tabs +
        "</div><br/>"
    );

    // Handle pro tab clicks - show upgrade popup
    $(document).on(
      "click",
      ".master_type_filter_tab_container .nav-tab.pro",
      function (event) {
        event.preventDefault();
        $('.jltma-upgrade-popup').fadeIn(200);
      }
    );
  }); //document.ready
})(jQuery);
