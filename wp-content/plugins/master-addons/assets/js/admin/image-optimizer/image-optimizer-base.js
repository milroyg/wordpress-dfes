(function(){
(function($) {
	"use strict";
	var JLTMA_ImageOptimizerBase = {
		settings: {},
		i18n: {},
		isPremium: false,
		/**
		* Initialize base module
		*/
		init: function() {
			var config = typeof jltmaImageOptimizerBase !== "undefined" ? jltmaImageOptimizerBase : {};
			this.settings = config.settings || {};
			this.i18n = config.i18n || {};
			this.isPremium = config.isPremium || false;
			this.initMediaLibraryDisplay();
			this.initTooltips();
		},
		/**
		* Initialize media library display
		* Sets up observation for attachment panels
		*/
		initMediaLibraryDisplay: function() {
			this.observeMediaLibrary();
		},
		/**
		* Initialize tooltips using event delegation (works in all contexts)
		*/
		initTooltips: function() {
			$(document).on("mouseenter", ".jltma-tooltip", function() {
				$(this).find(".jltma-tooltip-text").css({
					"visibility": "visible",
					"opacity": "1"
				});
			}).on("mouseleave", ".jltma-tooltip", function() {
				$(this).find(".jltma-tooltip-text").css({
					"visibility": "hidden",
					"opacity": "0"
				});
			});
		},
		/**
		* Observe media library for DOM changes
		*/
		observeMediaLibrary: function() {
			var self = this;
			self.customizeAllOriginalLinks();
			new MutationObserver(function(mutations) {
				self.customizeAllOriginalLinks();
			}).observe(document.body, {
				childList: true,
				subtree: true
			});
		},
		/**
		* Customize "Original image" links in media modal
		*/
		customizeAllOriginalLinks: function() {
			$("p, span, div").each(function() {
				var $el = $(this);
				if ($el.data("jltma-original-processed")) return;
				if ($el.contents().filter(function() {
					return this.nodeType === 3;
				}).text().indexOf("Original image") === -1) return;
				var $link = $el.find("a").first();
				if (!$link.length) return;
				$el.data("jltma-original-processed", true);
				$link.text("View Original").attr("target", "_blank");
				var $editImage = $el.siblings().filter(function() {
					return $(this).text().trim() === "Edit Image" || $(this).hasClass("edit-attachment");
				}).first();
				if ($editImage.length) {
					$el.remove();
					$editImage.after("<span class=\"jltma-separator\"> | </span>");
					$editImage.next(".jltma-separator").after($link);
				}
			});
		},
		/**
		* Format file size for display
		*/
		formatFileSize: function(bytes) {
			if (!bytes || bytes === 0) return "0 B";
			var k = 1024;
			var sizes = [
				"B",
				"KB",
				"MB",
				"GB"
			];
			var i = Math.floor(Math.log(bytes) / Math.log(k));
			return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
		},
		/**
		* Build upsell card HTML (for free version)
		* Shows potential savings with upgrade prompt
		*/
		buildUpsellCardHTML: function(data) {
			var savingsPercent = data.savingsPercent || 70;
			var originalSize = data.originalSize || 0;
			var potentialSize = Math.round(originalSize * (1 - savingsPercent / 100));
			var pricingUrl = data.pricingUrl || "https://master-addons.com/pricing/";
			var html = "<div class=\"jltma-optimization-card jltma-upsell-card\" style=\"display: block; margin: 8px 0; padding: 10px 12px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fbbf24; border-radius: 8px; font-family: -apple-system, BlinkMacSystemFont, sans-serif;\">";
			if (originalSize > 0) {
				html += "<div style=\"display: flex; align-items: center; gap: 6px; flex-wrap: wrap; font-size: 12px; color: #92400e;\">";
				html += "<svg width=\"14\" height=\"14\" viewBox=\"0 0 24 24\" fill=\"none\" style=\"flex-shrink: 0;\"><circle cx=\"12\" cy=\"12\" r=\"10\" stroke=\"#d97706\" stroke-width=\"2\"/><path d=\"M12 6v6l4 2\" stroke=\"#d97706\" stroke-width=\"2\" stroke-linecap=\"round\"/></svg>";
				html += "<span style=\"font-weight: 600;\">Save ~" + savingsPercent + "%</span>";
				html += "<span style=\"color: #b45309;\">(" + this.formatFileSize(originalSize) + " → ~" + this.formatFileSize(potentialSize) + ")</span>";
				html += "</div>";
			} else {
				html += "<div style=\"font-size: 12px; color: #92400e; font-weight: 600;\">";
				html += "Auto-optimize images on upload";
				html += "</div>";
			}
			html += "<a href=\"" + pricingUrl + "\" target=\"_blank\" style=\"display: inline-flex; align-items: center; gap: 4px; margin-top: 6px; font-size: 11px; font-weight: 600; color: #d97706; text-decoration: none;\">";
			html += "<span class=\"dashicons dashicons-star-filled\" style=\"font-size: 12px; width: 12px; height: 12px;\"></span>";
			html += "Upgrade to Pro";
			html += "</a>";
			html += "</div>";
			return html;
		},
		/**
		* Build optimized card HTML (compact version - matches screenshot)
		*/
		buildOptimizedCardHTML: function(data) {
			var timeDisplay = "";
			if (data.processingTime) timeDisplay = data.processingTime >= 1e3 ? (data.processingTime / 1e3).toFixed(1) + " sec" : data.processingTime + " ms";
			var outputFormat = (data.outputFormat || "webp").toUpperCase();
			var percentage = data.percentage || 0;
			var html = "<div class=\"jltma-optimization-card\" style=\"display: block; clear: both; margin: 16px 0;\">";
			html += "<div style=\"display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #1e1e1e; margin-bottom: 8px;\">";
			html += "<span>MA Optimization</span>";
			html += "<span class=\"jltma-tooltip\" style=\"position: relative; display: inline-flex; align-items: center; vertical-align: middle; margin-left: 2px;\"><span class=\"dashicons dashicons-info-outline\" style=\"font-size: 16px; width: 16px; height: 16px; color: #9ca3af; cursor: help; vertical-align: middle;\"></span><span class=\"jltma-tooltip-text\" style=\"visibility: hidden; opacity: 0; position: absolute; bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%); background: #1e1e1e; color: #fff; padding: 6px 10px; border-radius: 4px; font-size: 12px; font-weight: 400; white-space: nowrap; z-index: 9999999; transition: opacity 0.2s; pointer-events: none;\">Optimized by Master Addons</span></span>";
			html += "</div>";
			html += "<div class=\"jltma-optimized-card\" style=\"padding: 16px 20px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; font-family: -apple-system, BlinkMacSystemFont, sans-serif; text-align: center;\">";
			html += "<div style=\"display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 8px; white-space: nowrap;\">";
			html += "<svg width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" style=\"flex-shrink: 0;\"><circle cx=\"12\" cy=\"12\" r=\"11\" fill=\"#22c55e\"/><path d=\"M8 12l3 3 5-6\" stroke=\"#fff\" stroke-width=\"2.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>";
			html += "<span style=\"font-weight: 700; color: #16a34a; font-size: 16px;\">Saved " + percentage + "%</span>";
			html += "</div>";
			html += "<div style=\"margin-bottom: 6px; white-space: nowrap;\">";
			html += "<span style=\"color: #374151; font-size: 13px;\">(" + data.originalFormatted + " → " + data.optimizedFormatted + ")</span>";
			html += "</div>";
			var metaParts = ["→ " + outputFormat];
			if (timeDisplay) metaParts.push(timeDisplay);
			html += "<div style=\"font-size: 12px; color: #16a34a; white-space: nowrap;\">" + metaParts.join(" · ") + "</div>";
			html += "</div>";
			html += "</div>";
			return html;
		}
	};
	window.JLTMA_ImageOptimizerBase = JLTMA_ImageOptimizerBase;
	$(document).ready(function() {
		JLTMA_ImageOptimizerBase.init();
	});
})(jQuery);
//#endregion
})();
