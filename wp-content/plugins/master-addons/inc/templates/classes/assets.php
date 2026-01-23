<?php

namespace MasterAddons\Inc\Templates\Classes;

use MasterAddons\Inc\Templates;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 9/8/19
 */

if (!defined('ABSPATH')) exit; // No access of directly access

if (!class_exists('Master_Addons_Templates_Assets')) {


	class Master_Addons_Templates_Assets
	{


		private static $instance = null;

		public function __construct()
		{

			add_action('elementor/preview/enqueue_styles', array($this, 'enqueue_preview_styles'));

			add_action('elementor/editor/before_enqueue_scripts', array($this, 'editor_scripts'), -1);

			add_action('elementor/editor/after_enqueue_styles', array($this, 'editor_styles'));

			add_action('elementor/editor/footer', array($this, 'load_footer_scripts'));
		}


		public function editor_styles()
		{
			wp_enqueue_style('master-editor-only', JLTMA_URL . '/assets/templates/css/editor.css', [], JLTMA_VER);
		}


		public function enqueue_preview_styles()
		{
			wp_enqueue_style('master-addons-editor-preview', JLTMA_URL . '/assets/templates/css/preview.css', array(), JLTMA_VER, 'all');
		}


		public function editor_scripts()
		{
			wp_enqueue_script('master-addons-editor-js', JLTMA_URL . '/assets/templates/js/editor.js', array('jquery', 'underscore'), JLTMA_VER, true);

			$button = Templates\master_addons_templates()->config->get('master_addons_templates');

			wp_localize_script(
				'master-addons-editor-js',
				'MasterAddonsData',
				apply_filters(
					'master-addons-core/assets/editor/localize',
					array(
						'master_image_dir'      => JLTMA_IMAGE_DIR . 'ma-editor-icon.svg',
						'MasterAddonsEditorBtn' => $button,
						'get_templates_nonce'   => wp_create_nonce('jltma_get_templates_nonce_action'),
						'insert_template_nonce' => wp_create_nonce('jltma_insert_templates_nonce_action'),
						'refresh_cache_nonce'   => wp_create_nonce('master_addons_nonce'),
						'modalRegions'          => $this->get_modal_region(),
						'license'               => array(
							'status'       => Templates\master_addons_templates()->config->get('status'),
							'activateLink' => Templates\master_addons_templates()->config->get('license_page'),
							'proMessage'   => Templates\master_addons_templates()->config->get('pro_message')
						),
					)
				)
			);
		}


		public function get_modal_region()
		{

			return array(
				'modalHeader'  => '.dialog-header',
				'modalContent' => '.dialog-message',
			);
		}


		public function load_footer_scripts()
		{
			

			$scripts = glob(JLTMA_PATH . 'inc/templates/editor/*.php');

			array_map(function ($file) {

				$name = basename($file, '.php');

				ob_start();

				include $file;

				printf('<script type="text/html" id="views-ma-el-%1$s">%2$s</script>', $name, ob_get_clean());
			}, $scripts);
			
			// Add cache refresh functionality for template modal
			$this->add_cache_refresh_script();
		}
		
		
		public function add_cache_refresh_script()
		{
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Global Macy instance
				var macyInstance = null;

				// Wait for Elementor to be available and add cache refresh functionality
				function initCacheRefresh() {
					if (typeof window.elementor === 'undefined') {
						setTimeout(initCacheRefresh, 100);
						return;
					}

					// Initialize Macy.js for template grid
					initMacyLayout();

					// Add tab switching preloader functionality
					initTabPreloader();

					// Add template search functionality
					initTemplateSearch();

					// Initialize filters and keywords
					initFiltersAndKeywords();
					
					// Add banner click tracking
					$(document).on('click', '.ma-el-template-library--banner', function(e) {
						// Track banner click for analytics (optional)
						if (typeof gtag !== 'undefined') {
							gtag('event', 'banner_click', {
								'event_category': 'template_library',
								'event_label': 'template_banner',
								'transport_type': 'beacon'
							});
						}
						
					});
					
					// Add event handler using delegation for dynamically loaded modal content
					$(document).on('click', '#ma-el-template-cache-refresh', function(e) {
						e.preventDefault();
						
						var $button = $(this);
						var $icon = $button.find('i');
						
						// Add updating state
						$button.addClass('updating');
						$icon.removeClass('eicon-sync').addClass('eicon-loading eicon-animation-spin');
						
						// Perform cache refresh
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'jltma_refresh_templates_cache',
								_wpnonce: MasterAddonsData.refresh_cache_nonce
							},
							success: function(response) {
								if (response.success) {
									
									// Update cache count if cache status element exists
									var $cacheStatus = $('#ma-el-template-cache-status');
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
									
									// Force refresh of current tab content by reloading the modal
									setTimeout(function() {
										// Find the currently active tab to refresh
										var $activeTab = $('#ma-el-template-modal-header-tabs input:checked');
										if ($activeTab.length > 0) {
											// Trigger click on active tab to reload content
											$activeTab.trigger('change');
										} else {
											// Fallback: reload entire modal content if possible
											var $modalContent = $('#ma-el-template-library-content');
											if ($modalContent.length > 0) {
												$modalContent.trigger('refresh');
											}
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
				}

				// Initialize Macy.js masonry layout for template grid
				function initMacyLayout() {
					var $container = $('#ma-el-modal-templates-container, .ma-el-templates-list');

					if ($container.length === 0) {
						return;
					}

					// Destroy existing instance if any
					if (macyInstance) {
						macyInstance.remove();
						macyInstance = null;
					}

					// Check if Macy is available
					if (typeof Macy === 'undefined') {
						return;
					}

					// Initialize new Macy instance
					macyInstance = Macy({
						container: $container[0],
						trueOrder: false,
						waitForImages: true,
						margin: 20,
						columns: 3,
						breakAt: {
							1200: 3,
							940: 2,
							520: 1
						}
					});

					// Recalculate after images load
					setTimeout(function() {
						if (macyInstance) {
							macyInstance.recalculate(true);
						}
					}, 500);
				}

				// Tab preloader functionality
				function initTabPreloader() {
					// Add preloader for tab switching
					$(document).on('click', '#ma-el-template-modal-header-tabs label', function(e) {
						var $clickedLabel = $(this);
						var $input = $clickedLabel.find('input');
						
						// Don't show loader if this tab is already active
						if ($input.is(':checked')) {
							return;
						}
						
						// Show loading state
						showTabLoader();
						
						// Set a timeout to ensure loader is shown
						setTimeout(function() {
							$input.prop('checked', true).trigger('change');
						}, 50);
					});
					
					// Monitor for tab content loading completion
					$(document).on('DOMSubtreeModified', '#ma-el-template-library-content', function() {
						hideTabLoader();
					});
					
					// Fallback: Hide loader after reasonable time
					var loaderTimeout;
					$(document).on('change', '#ma-el-template-modal-header-tabs input[type="radio"]', function() {
						clearTimeout(loaderTimeout);
						loaderTimeout = setTimeout(function() {
							hideTabLoader();
						}, 3000); // Hide after 3 seconds max
					});
					
					// Modern browsers: Use MutationObserver instead of deprecated DOMSubtreeModified
					if (typeof MutationObserver !== 'undefined') {
						var observer = new MutationObserver(function(mutations) {
							mutations.forEach(function(mutation) {
								if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
									// Check if template content was added
									var hasTemplateContent = false;
									for (var i = 0; i < mutation.addedNodes.length; i++) {
										var node = mutation.addedNodes[i];
										if (node.nodeType === 1) { // Element node
											if ($(node).find('#ma-el-modal-templates-container').length > 0 ||
												$(node).is('#ma-el-modal-templates-container') ||
												$(node).find('.elementor-template-library-template').length > 0) {
												hasTemplateContent = true;
												break;
											}
										}
									}
									if (hasTemplateContent) {
										setTimeout(function() {
											hideTabLoader();
											// Reinitialize or refresh Macy when new content is loaded
											if (macyInstance) {
												macyInstance.recalculate(true);
											} else {
												initMacyLayout();
											}
										}, 200);
									}
								}
							});
						});
						
						// Start observing when modal is present
						$(document).on('DOMNodeInserted', '#ma-el-template-library-content', function() {
							observer.observe(this, {
								childList: true,
								subtree: true
							});
						});
					}
				}
				
				function showTabLoader() {
					var $modalContent = $('#ma-el-template-library-content');
					if ($modalContent.length > 0) {
						// Add loading overlay
						if ($modalContent.find('.ma-el-tab-loading-overlay').length === 0) {
							var loaderHtml = '<div class="ma-el-tab-loading-overlay">' +
								'<div class="elementor-loader-wrapper">' +
									'<div class="elementor-loader">' +
										'<div class="elementor-loader-box"></div>' +
										'<div class="elementor-loader-box"></div>' +
										'<div class="elementor-loader-box"></div>' +
										'<div class="elementor-loader-box"></div>' +
									'</div>' +
									'<div class="elementor-loading-title">Loading Templates...</div>' +
								'</div>' +
							'</div>';
							$modalContent.append(loaderHtml);
						}
						$modalContent.find('.ma-el-tab-loading-overlay').fadeIn(150);
					}
				}
				
				function hideTabLoader() {
					var $loadingOverlay = $('.ma-el-tab-loading-overlay');
					if ($loadingOverlay.length > 0) {
						$loadingOverlay.fadeOut(150, function() {
							$(this).remove();
						});
					}
				}
				
				// Template search functionality
				function initTemplateSearch() {
					var searchTimeout = null;
					
					// Handle search input
					$(document).on('keyup input', '#ma-el-template-search-input', function(e) {
						var $input = $(this);
						var searchTerm = $input.val().toLowerCase().trim();
						
						// Clear previous timeout
						if (searchTimeout) {
							clearTimeout(searchTimeout);
						}
						
						// Debounce search
						searchTimeout = setTimeout(function() {
							performTemplateSearch(searchTerm);
						}, 300);
					});
					
					// Clear search on escape
					$(document).on('keydown', '#ma-el-template-search-input', function(e) {
						if (e.keyCode === 27) { // Escape key
							$(this).val('');
							performTemplateSearch('');
						}
					});
					
				}
				
				function performTemplateSearch(searchTerm) {
					// Look for templates in both possible containers
					var $templates = $('#ma-el-modal-templates-container .elementor-template-library-template, .ma-el-templates-list .elementor-template-library-template');
					var $noResults = $('.ma-el-no-search-results');
					var $container = $('#ma-el-modal-templates-container');
					var $templatesContainer = $('.ma-el-templates-list');
					
					// Use templates list container if it exists, otherwise fall back to modal container
					if ($templatesContainer.length > 0) {
						$container = $templatesContainer;
					}
					
					if (searchTerm === '') {
						// Show all templates and respect any active keyword filter
						var selectedKeyword = $('.ma-el-keyword-filter.active').data('keyword');
						if (selectedKeyword) {
							performKeywordFilter(selectedKeyword);
						} else {
							$templates.show();
							$noResults.remove();
							refreshMasonryLayout();
						}
						return;
					}
					
					var visibleCount = 0;
					var selectedKeyword = $('.ma-el-keyword-filter.active').data('keyword');
					
					$templates.each(function() {
						var $template = $(this);
						var templateName = ($template.find('.elementor-template-library-template-name').text() || '').toLowerCase();
						var templateTitle = ($template.attr('data-title') || '').toLowerCase();
						var templateKeywords = ($template.attr('data-keywords') || '').toLowerCase();
						
						// Check search match
						var searchMatch = templateName.indexOf(searchTerm) !== -1 || 
							templateTitle.indexOf(searchTerm) !== -1 || 
							templateKeywords.indexOf(searchTerm) !== -1;
						
						// Check keyword filter match if one is selected
						var keywordMatch = !selectedKeyword || templateKeywords.indexOf(selectedKeyword.toLowerCase()) !== -1;
						
						// Show template if both search and keyword filter match
						if (searchMatch && keywordMatch) {
							$template.show();
							visibleCount++;
						} else {
							$template.hide();
						}
					});
					
					// Show no results message if needed
					if (visibleCount === 0) {
						if ($noResults.length === 0) {
							var noResultsHtml = '<div class="ma-el-no-search-results">' +
								'<div class="ma-el-no-results-content">' +
									'<i class="eicon-search"></i>' +
									'<h3>No templates found</h3>' +
									'<p>Try searching with different keywords or check other tabs.</p>' +
								'</div>' +
							'</div>';
							$container.append(noResultsHtml);
						}
					} else {
						$noResults.remove();
					}
					
					// Force masonry layout refresh
					refreshMasonryLayout();
				}
				
				function performKeywordFilter(keyword) {
					var $templates = $('#ma-el-modal-templates-container .elementor-template-library-template, .ma-el-templates-list .elementor-template-library-template');
					var $noResults = $('.ma-el-no-filter-results');
					var $container = $('.ma-el-templates-list').length > 0 ? $('.ma-el-templates-list') : $('#ma-el-modal-templates-container');
					var searchTerm = $('#ma-el-template-search-input').val().toLowerCase().trim();
					
					if (!keyword) {
						// Show all templates but respect search term
						if (searchTerm) {
							performTemplateSearch(searchTerm);
						} else {
							$templates.show();
							$noResults.remove();
							refreshMasonryLayout();
						}
						return;
					}
					
					var visibleCount = 0;
					
					$templates.each(function() {
						var $template = $(this);
						var templateKeywords = ($template.attr('data-keywords') || '').toLowerCase();
						var templateName = ($template.find('.elementor-template-library-template-name').text() || '').toLowerCase();
						var templateTitle = ($template.attr('data-title') || '').toLowerCase();
						
						// Check keyword filter match
						var keywordMatch = templateKeywords.indexOf(keyword.toLowerCase()) !== -1;
						
						// Check search match if there's a search term
						var searchMatch = !searchTerm || 
							templateName.indexOf(searchTerm) !== -1 || 
							templateTitle.indexOf(searchTerm) !== -1 || 
							templateKeywords.indexOf(searchTerm) !== -1;
						
						// Show template if both keyword and search match
						if (keywordMatch && searchMatch) {
							$template.show();
							visibleCount++;
						} else {
							$template.hide();
						}
					});
					
					// Show no results message if needed
					if (visibleCount === 0) {
						if ($noResults.length === 0) {
							var noResultsHtml = '<div class="ma-el-no-filter-results">' +
								'<div class="ma-el-no-results-content">' +
									'<i class="eicon-filter"></i>' +
									'<h3>No templates found</h3>' +
									'<p>Try selecting a different filter or clear the search.</p>' +
								'</div>' +
							'</div>';
							$container.append(noResultsHtml);
						}
					} else {
						$noResults.remove();
					}
					
					// Force masonry layout refresh
					refreshMasonryLayout();
				}
				
				// Initialize Macy.js layout
				(function () {
					if (window.__macyLayoutInit) return;
						window.__macyLayoutInit = true;

					
					const log = (...a) => console.log("[MacyFix]", ...a);
					const wait = (ms) => new Promise((r) => setTimeout(r, ms));

					const findContainer = () =>
						document.querySelector("#ma-el-modal-templates-container") ||
						document.querySelector(".ma-el-templates-list") ||
						document.querySelector(".ma-el-templates-grid");

					const getCols = (w) => (w < 480 ? 1 : w < 768 ? 2 : w < 1200 ? 3 : 5);

					async function waitImages(container) {
						if (!container) return;
						const imgs = Array.from(container.querySelectorAll("img"));
						if (!imgs.length) return;
						await Promise.allSettled(
							imgs.map(
								(img) =>
									new Promise((res) => {
										if (img.complete && img.naturalWidth) return res();
										img.addEventListener("load", res, { once: true });
										img.addEventListener("error", res, { once: true });
									})
							)
						);
					}

					let macyInstance = null;

					async function refreshMacy(delay = 150) {
						await wait(delay);
						const container = findContainer();
						if (!container || typeof Macy === "undefined") {
							console.warn("[MacyFix] Container or Macy missing");
							return;
						}

						await waitImages(container);

						if (macyInstance && typeof macyInstance.remove === "function") {
							macyInstance.remove();
							macyInstance = null;
						}

						const width =
							container.closest(".dialog-widget-content")?.offsetWidth ||
							container.offsetWidth;
						const cols = getCols(width);

						macyInstance = Macy({
							container,
							trueOrder: false,
							waitForImages: true,
							margin: 24,
							columns: cols,
						});

						jQuery(container).trigger("macy:refresh");
						log("Macy refreshed successfully.");
					}

					window.__refreshMacyLayout = refreshMacy;

					
					const tabSelector =
						"#views-ma-el-template-modal-tabs-items, " +
						"#ma-el-template-modal-header-tabs, " +
						".elementor-template-library-menu-item, " +
						".MuiTabs-root [role='tab']";

					jQuery(document)
						.off("click.MacyFix change.MacyFix")
						.on("click.MacyFix change.MacyFix", tabSelector, () => refreshMacy(250));

					jQuery(document).off("ajaxComplete.MacyFix").on("ajaxComplete.MacyFix", () => refreshMacy(300));

					let resizeTimer;
					window.addEventListener("resize", () => {
						clearTimeout(resizeTimer);
						resizeTimer = setTimeout(() => refreshMacy(100), 180);
					});

					if (window.MutationObserver) {
						const tabRoot =
							document.querySelector(".MuiTabs-root") ||
							document.querySelector("#views-ma-el-template-modal-tabs-items");
						if (tabRoot) {
							const obs = new MutationObserver(() => refreshMacy(200));
							obs.observe(tabRoot, { attributes: true, subtree: true });
							log("Tab observer active");
						}
					}

					(async () => {
						let tries = 0;
						const iv = setInterval(async () => {
							const container = findContainer();
							if (container || tries > 20) {
								clearInterval(iv);
								if (container) await refreshMacy(100);
								else console.warn("[MacyFix] Container not found after waiting.");
							}
							tries++;
						}, 200);
					})();
				})();

				function refreshMasonryLayout() {
					// Use Macy.js for layout refresh
					if (macyInstance) {
						// Recalculate the layout
						macyInstance.recalculate(true);

						// Also rerun the layout after images potentially load
						setTimeout(function() {
							macyInstance.recalculate(true);
						}, 500);
					} else {
						// Fallback: Try to initialize Macy if not already done
						initMacyLayout();
					}

					// Trigger window resize for compatibility
					setTimeout(function() {
						$(window).trigger('resize');
					}, 50);
				}
				
				function initFiltersAndKeywords() {
					// Wait for both DOM structure and Marionette views to be available
					function setupFiltersAndKeywords() {
						var $filtersList = $('.ma-el-filters-list');
						var $keywordsList = $('.ma-el-keywords-list');
						var $templatesWrap = $('.ma-el-modal-templates-wrap');
						
						if ($filtersList.length === 0 || $keywordsList.length === 0 || $templatesWrap.length === 0) {
							return false; // Not ready yet
						}
						
						// Check if Marionette has already populated these regions
						if ($filtersList.children().length > 0 || $keywordsList.children().length > 0) {
							return true; // Already populated by Marionette
						}
						
						// Only populate if Marionette hasn't done so already
						// Populate filters list (categories/tags)
						if ($filtersList.children().length === 0) {
							var filtersHtml = '<h4>Categories</h4>' +
								'<div class="ma-el-filter-items">' +
									'<div class="ma-el-filter-item ma-el-category-filter active" data-category="all">' +
										'<span>All Categories</span>' +
									'</div>' +
									'<div class="ma-el-filter-item ma-el-category-filter" data-category="blocks">' +
										'<span>Blocks</span>' +
									'</div>' +
									'<div class="ma-el-filter-item ma-el-category-filter" data-category="pages">' +
										'<span>Pages</span>' +
									'</div>' +
									'<div class="ma-el-filter-item ma-el-category-filter" data-category="sections">' +
										'<span>Sections</span>' +
									'</div>' +
								'</div>';
							$filtersList.html(filtersHtml);
						}
						
						// Populate keywords list (widget types)
						// if ($keywordsList.children().length === 0) {
						// 	var keywordsHtml = '<h4>Widgets</h4>' +
						// 		'<div class="ma-el-keyword-items">' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter active" data-keyword="">' +
						// 				'<span>All Widgets</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="call-to-action">' +
						// 				'<span>Call To Action</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="counter">' +
						// 				'<span>Counter</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="creative-button">' +
						// 				'<span>Creative Button</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="faq">' +
						// 				'<span>FAQ</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="flipbox">' +
						// 				'<span>Flipbox</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="heading">' +
						// 				'<span>Heading</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="hero-image">' +
						// 				'<span>Hero Image</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="hero-slideshow">' +
						// 				'<span>Hero Slideshow</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="infobox">' +
						// 				'<span>Infobox</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="progress-bar">' +
						// 				'<span>Progress Bar</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="team-members">' +
						// 				'<span>Team Members</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="testimonial">' +
						// 				'<span>Testimonial</span>' +
						// 			'</div>' +
						// 			'<div class="ma-el-filter-item ma-el-keyword-filter" data-keyword="text-editor">' +
						// 				'<span>Text Editor</span>' +
						// 			'</div>' +
						// 		'</div>';
						// 	$keywordsList.html(keywordsHtml);
						// }
						
						// Add event handlers for filters
						$(document).off('click', '.ma-el-category-filter').on('click', '.ma-el-category-filter', function(e) {
							e.preventDefault();
							var $this = $(this);
							var category = $this.data('category');
							
							// Update active state
							$('.ma-el-category-filter').removeClass('active');
							$this.addClass('active');
							
							// Apply category filter (placeholder for now)
							performCategoryFilter(category);
						});
						
						// $(document).off('click', '.ma-el-keyword-filter').on('click', '.ma-el-keyword-filter', function(e) {
						// 	e.preventDefault();
						// 	var $this = $(this);
						// 	var keyword = $this.data('keyword');
							
						// 	// Update active state
						// 	$('.ma-el-keyword-filter').removeClass('active');
						// 	$this.addClass('active');
							
						// 	// Apply keyword filter
						// 	performKeywordFilter(keyword);
						// });
						
						return true; // Successfully setup
					}
					
					// Wait longer before trying to setup to allow Marionette to initialize first
					setTimeout(function() {
						if (!setupFiltersAndKeywords()) {
							// If not ready, monitor for DOM changes
							var attempts = 0;
							var maxAttempts = 30;
							
							var setupInterval = setInterval(function() {
								attempts++;
								
								if (setupFiltersAndKeywords() || attempts >= maxAttempts) {
									clearInterval(setupInterval);
								}
							}, 200);
						}
					}, 1000); // Wait 1 second for Marionette to initialize
				}
				
				function performCategoryFilter(category) {
					// Placeholder for category filtering
					// This would filter templates by category (blocks, pages, sections, etc.)
					console.log('Category filter:', category);
					
					// For now, just trigger a layout refresh
					refreshMasonryLayout();
				}
				
				// Initialize when document is ready
				initCacheRefresh();
				
				// Initialize Template Importer lazy loading
				initTemplateImporterLazyLoad();
				
				function initTemplateImporterLazyLoad() {
					var currentPage = 1;
					var isLoading = false;
					var hasMoreTemplates = true;
					var perPage = 10;
					
					// Initialize pagination and lazy loading for template modal
					function initTemplatePagination() {
						// Wait for modal content to be available
						function waitForModalContent() {
							var $modalContainer = $('#ma-el-modal-templates-container');
							var $templatesList = $('.ma-el-templates-list');
							
							if ($modalContainer.length > 0 || $templatesList.length > 0) {
								setupScrollPagination();
								return true;
							}
							return false;
						}
						
						// Try immediately and fallback to polling
						if (!waitForModalContent()) {
							var pollCount = 0;
							var pollInterval = setInterval(function() {
								pollCount++;
								if (waitForModalContent() || pollCount > 20) {
									clearInterval(pollInterval);
								}
							}, 500);
						}
					}
					
					function setupScrollPagination() {
						var $container = $('.ma-el-templates-list').length > 0 ? $('.ma-el-templates-list') : $('#ma-el-modal-templates-container');
						
						// Add loading indicator
						if (!$('.ma-el-template-loading-more').length) {
							var $loadingIndicator = $('<div class="ma-el-template-loading-more" style="display: none; text-align: center; padding: 20px;"><p>Loading More Templates...</p></div>');
							$container.after($loadingIndicator);
						}
						
						// Monitor scroll on modal
						var $modalElement = $('#elementor-template-library-modal');
						if ($modalElement.length === 0) {
							$modalElement = $('.dialog-widget');
						}
						
						if ($modalElement.length > 0) {
							$modalElement.off('scroll.template-lazy').on('scroll.template-lazy', function() {
								if (isLoading || !hasMoreTemplates) {
									return;
								}
								
								var scrollTop = $(this).scrollTop();
								var scrollHeight = this.scrollHeight;
								var clientHeight = $(this).height();
								
								// Load more when 200px from bottom
								if (scrollTop + clientHeight >= scrollHeight - 200) {
									loadMoreTemplates();
								}
							});
						}
					}
					
					function loadMoreTemplates() {
						if (isLoading || !hasMoreTemplates) {
							return;
						}
						
						isLoading = true;
						currentPage++;
						
						$('.ma-el-template-loading-more').show();
						
						// Get current active tab
						var activeTab = getActiveTemplateTab();
						
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'ma_el_get_templates',
								tab: activeTab,
								page: currentPage,
								per_page: perPage,
								_wpnonce: MasterAddonsData.get_templates_nonce
							},
							success: function(response) {
								isLoading = false;
								$('.ma-el-template-loading-more').hide();
								
								if (response.success && response.data && response.data.templates && response.data.templates.length > 0) {
									appendTemplates(response.data.templates);
									
									// Check if we have more templates
									if (response.data.templates.length < perPage) {
										hasMoreTemplates = false;
									}
								} else {
									hasMoreTemplates = false;
								}
							},
							error: function(xhr, status, error) {
								isLoading = false;
								$('.ma-el-template-loading-more').hide();
								console.error('Failed to load more templates:', error);
								
								// Reset page counter for retry
								currentPage--;
							}
						});
					}
					
					function getActiveTemplateTab() {
						var $activeTab = $('#ma-el-template-modal-header-tabs input:checked');
						if ($activeTab.length > 0) {
							return $activeTab.val();
						}
						return 'master_section'; // default tab
					}
					
					function appendTemplates(templates) {
						var $container = $('.ma-el-templates-list').length > 0 ? $('.ma-el-templates-list') : $('#ma-el-modal-templates-container');
						
						templates.forEach(function(template) {
							var templateHtml = generateTemplateHTML(template);
							$container.append(templateHtml);
						});
						
						// Refresh layout for new templates
						refreshMasonryLayout();
					}
					
					function generateTemplateHTML(template) {
						var placeholderImage = MasterAddonsData.master_image_dir ? MasterAddonsData.master_image_dir.replace('ma-editor-icon.svg', 'placeholder.png') : '';
						var thumbnailUrl = template.thumbnail || placeholderImage;
						
						return '<div class="elementor-template-library-template ma-el-template-item" data-template-id="' + template.template_id + '" data-title="' + template.title + '" data-keywords="' + (template.keywords || '') + '">' +
							'<div class="elementor-template-library-template-body">' +
								'<div class="elementor-template-library-template-screenshot">' +
									'<div class="elementor-template-library-template-preview">' +
										'<i class="fa fa-search-plus"></i>' +
									'</div>' +
									'<img src="' + thumbnailUrl + '" alt="' + template.title + '" class="ma-el-template-image" onerror="this.src=\'' + placeholderImage + '\'">' +
									'<div class="elementor-template-library-template-name">' + template.title + '</div>' +
								'</div>' +
							'</div>' +
							'<div class="elementor-template-library-template-controls">' +
								'<button class="elementor-template-library-template-action ma-el-template-insert elementor-button elementor-button-success" data-template-id="' + template.template_id + '">' +
									'<i class="eicon-file-download"></i>' +
									'<span class="elementor-button-title">Insert</span>' +
								'</button>' +
							'</div>' +
						'</div>';
					}
					
					// Reset pagination when tab changes
					$(document).on('change', '#ma-el-template-modal-header-tabs input[type="radio"]', function() {
						currentPage = 1;
						isLoading = false;
						hasMoreTemplates = true;
					});
					
					// Initialize when modal is opened
					$(document).on('DOMNodeInserted', '#elementor-template-library-modal', function() {
						setTimeout(initTemplatePagination, 100);
					});
					
					// Initialize immediately if modal is already open
					if ($('#elementor-template-library-modal').length > 0) {
						setTimeout(initTemplatePagination, 100);
					}
				}
			});
			</script>
			<?php
		}


		public static function get_instance()
		{

			if (self::$instance == null) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}
