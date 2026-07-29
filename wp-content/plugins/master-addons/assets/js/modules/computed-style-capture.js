(function(){
//#region dev/js/modules/free/computed-style-capture.js
var JLTMA_WIDGET_STYLE_MAP = {
	heading: {
		sel: ".elementor-heading-title",
		typo: "typography_",
		color: "title_color"
	},
	"text-editor": {
		sel: ".elementor-widget-container",
		typo: "typography_",
		color: "text_color"
	},
	button: {
		sel: ".elementor-button-text",
		typo: "typography_",
		color: "button_text_color"
	},
	"icon-box": {
		sel: ".elementor-icon-box-title",
		typo: "title_typography_",
		color: "title_color"
	}
};
function pxToObj(v) {
	const n = parseFloat(v);
	if (Number.isNaN(n)) return null;
	return {
		unit: "px",
		size: n,
		sizes: []
	};
}
function firstFamily(ff) {
	return String(ff || "").split(",")[0].replace(/['"]/g, "").trim();
}
function rgbToHex(c) {
	const m = String(c || "").match(/rgba?\(([^)]+)\)/);
	if (!m) return c || "";
	const p = m[1].split(",").map((x) => x.trim());
	const h = (n) => ("0" + parseInt(n, 10).toString(16)).slice(-2);
	return ("#" + h(p[0]) + h(p[1]) + h(p[2])).toUpperCase();
}
function jltmaBakeComputedIntoSettings(settings, widgetType, computed) {
	const map = JLTMA_WIDGET_STYLE_MAP[widgetType];
	if (!map || !computed) return;
	const p = map.typo;
	if (settings.__globals__) {
		[p + "typography", map.color].forEach((k) => {
			if (settings.__globals__[k] === "") delete settings.__globals__[k];
		});
		if (!Object.keys(settings.__globals__).length) delete settings.__globals__;
	}
	const ff = firstFamily(computed.fontFamily);
	if (ff) settings[p + "font_family"] = ff;
	const fs = pxToObj(computed.fontSize);
	if (fs) settings[p + "font_size"] = fs;
	if (computed.fontWeight) settings[p + "font_weight"] = String(computed.fontWeight);
	const lh = pxToObj(computed.lineHeight);
	if (lh) settings[p + "line_height"] = lh;
	if (computed.letterSpacing && computed.letterSpacing !== "normal") {
		const ls = pxToObj(computed.letterSpacing);
		if (ls) settings[p + "letter_spacing"] = ls;
	}
	if (computed.textTransform && computed.textTransform !== "none") settings[p + "text_transform"] = computed.textTransform;
	if (computed.fontStyle && computed.fontStyle !== "normal") settings[p + "font_style"] = computed.fontStyle;
	settings[p + "typography"] = "custom";
	if (map.color) {
		const hex = rgbToHex(computed.color);
		if (hex) settings[map.color] = hex;
	}
}
function jltmaCaptureComputedStyles(model, previewDoc) {
	if (!model || !previewDoc) return model;
	const walk = (node) => {
		if (!node || typeof node !== "object") return;
		const wt = node.widgetType;
		if (node.elType === "widget" && JLTMA_WIDGET_STYLE_MAP[wt] && node.id) {
			const root = previewDoc.querySelector(".elementor-element-" + node.id);
			if (root) {
				const map = JLTMA_WIDGET_STYLE_MAP[wt];
				const el = root.querySelector(map.sel) || root;
				const cs = previewDoc.defaultView.getComputedStyle(el);
				node.settings = node.settings || {};
				jltmaBakeComputedIntoSettings(node.settings, wt, cs);
			}
		}
		(node.elements || []).forEach(walk);
	};
	walk(model);
	return model;
}
if (typeof window !== "undefined") {
	window.jltmaCaptureComputedStyles = jltmaCaptureComputedStyles;
	window.jltmaBakeComputedIntoSettings = jltmaBakeComputedIntoSettings;
}
//#endregion
})();
