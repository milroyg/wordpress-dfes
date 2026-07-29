(function(){
//#region dev/js/admin/recommended-plugins.js
(function($) {
	$("body").on("click", ".jltma-plugin-action .install-now", function(e) {
		e.preventDefault();
		if (!$(this).hasClass("updating-message")) {
			let plugin = $(this).attr("data-install-url");
			installPlugin($(this), plugin);
		}
	});
	$("body").on("click", ".jltma-plugin-action .activate-now", function() {
		let file = $(this).attr("data-plugin-file");
		activatePlugin($(this), file);
	});
	$("body").on("click", ".jltma-plugin-action .update-now", function() {
		if (!$(this).hasClass("updating-message")) {
			const plugin = $(this).attr("data-plugin");
			updatePlugin($(this), plugin);
		}
	});
	$(".jltma-filter-links").on("click", "a", function(e) {
		e.preventDefault();
		let cls = $(this).data("type");
		$(this).addClass("current").parent().siblings().find("a").removeClass("current");
		$(".jltma-plugins-grid .jltma-plugin-card").each(function(i, el) {
			if (cls == "all") $(this).removeClass("hide");
			else if ($(this).hasClass(cls)) $(this).removeClass("hide");
			else $(this).addClass("hide");
		});
	});
	$(".jltma-search-plugins #search-plugins").on("keyup", function() {
		var value = $(this).val();
		var srch = new RegExp(value, "i");
		$(".jltma-plugins-grid .jltma-plugin-card").each(function() {
			var $this = $(this);
			if (!($this.find(".jltma-plugin-name a, .jltma-plugin-desc").text().search(srch) >= 0)) $this.addClass("hide");
			if ($this.find(".jltma-plugin-name a, .jltma-plugin-desc").text().search(srch) >= 0) $this.removeClass("hide");
		});
	});
})(jQuery);
function activatePlugin(element, file) {
	element.addClass("button-disabled");
	element.attr("disabled", "disabled");
	element.text("Processing...");
	jQuery.ajax({
		url: JLTMACORE.admin_ajax,
		type: "POST",
		data: {
			action: "jltma_recommended_activate_plugin",
			file,
			nonce: JLTMACORE.recommended_nonce
		},
		success: function(response) {
			if (response.success === true) {
				const pluginStatus = jQuery(".jltma-plugin-status .jltma-status-inactive[data-plugin-file='" + file + "']");
				pluginStatus.text("Active");
				pluginStatus.addClass("jltma-status-active");
				pluginStatus.removeClass("jltma-status-inactive");
				element.removeClass("activate-now jltma-btn-success");
				element.addClass("jltma-btn-activated");
				element.text("Activated");
			} else {
				element.removeClass("button-disabled");
				element.prop("disabled", false);
				element.text("Activate");
			}
		}
	});
}
function installPlugin(element, plugin) {
	element.removeClass("jltma-btn-primary");
	element.addClass("updating-message");
	element.text("Installing...");
	jQuery.ajax({
		url: JLTMACORE.admin_ajax,
		type: "POST",
		data: {
			action: "jltma_recommended_upgrade_plugin",
			type: "install",
			plugin,
			nonce: JLTMACORE.recommended_nonce
		},
		success: function(response) {
			if (response.success === true) {
				element.removeClass("updating-message install-now");
				element.addClass("jltma-btn-activated");
				element.attr("disabled", "disabled");
				element.removeAttr("data-install-url");
				element.text("Activated");
				setTimeout(() => {
					const pluginStatus = jQuery(".jltma-plugin-status .jltma-status-not-installed[data-plugin-url='" + plugin + "']");
					pluginStatus.text("Active");
					pluginStatus.addClass("jltma-status-active");
					pluginStatus.removeClass("jltma-status-not-installed");
					pluginStatus.removeAttr("data-plugin-url");
				}, 500);
			} else {
				element.removeClass("updating-message");
				element.addClass("jltma-btn-primary");
				element.text("Install");
			}
		}
	});
}
function updatePlugin(element, plugin) {
	element.addClass("updating-message");
	element.text("Updating...");
	jQuery.ajax({
		url: JLTMACORE.admin_ajax,
		type: "POST",
		data: {
			action: "jltma_recommended_upgrade_plugin",
			type: "update",
			plugin,
			nonce: JLTMACORE.recommended_nonce
		},
		success: function(response) {
			if (response.success === true) {
				element.removeClass("updating-message update-now jltma-btn-warning");
				element.addClass("jltma-btn-activated");
				element.attr("disabled", "disabled");
				element.text("Updated!");
				if (response.data.active === false) {
					const pluginStatus = jQuery(".jltma-plugin-status .jltma-status-inactive[data-plugin-file='" + plugin + "']");
					pluginStatus.text("Active");
					pluginStatus.addClass("jltma-status-active");
					pluginStatus.removeClass("jltma-status-inactive");
					pluginStatus.removeAttr("data-plugin-file");
				}
			} else {
				element.removeClass("updating-message");
				element.text("Update");
			}
		}
	});
}
//#endregion
})();
