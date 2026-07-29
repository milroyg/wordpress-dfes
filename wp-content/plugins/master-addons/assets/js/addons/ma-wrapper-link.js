(function(){
//#region dev/js/addons/free/ma-wrapper-link.js
/**
* Start wrapper link extension script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_WrapperLink = function($scope, $) {
		$("body").off("click.onWrapperLink", "[data-jltma-wrapper-link]");
		$("body").on("click.onWrapperLink", "[data-jltma-wrapper-link]", function(e) {
			e.preventDefault();
			e.stopPropagation();
			var $wrapper = $(this), data = $wrapper.data("jltma-wrapper-link"), id = $wrapper.data("id"), anchor = document.createElement("a");
			anchor.id = "master-addons-wrapper-link-" + id;
			anchor.href = data.url;
			anchor.target = data.is_external ? "_blank" : "_self";
			anchor.rel = data.nofollow ? "nofollow noreferer" : "";
			anchor.style.display = "none";
			document.body.appendChild(anchor);
			if (data && data.url) if (data.is_external) window.open(data.url, "_blank", data.nofollow ? "noopener,noreferrer" : "noopener");
			else window.location.href = data.url;
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/global", JLTMA_WrapperLink);
	});
})(jQuery, window.elementorFrontend);
/**
* End wrapper link extension script
*/
//#endregion
})();
