(function(){
//#region dev/js/modules/free/globals-flatten.js
(function(root) {
	function indexById(payload) {
		var map = {};
		if (!payload) return map;
		var list = Array.isArray(payload) ? payload : Array.isArray(payload.data) ? payload.data : null;
		if (list) {
			for (var i = 0; i < list.length; i++) {
				var entry = list[i];
				if (entry && entry._id != null) map[entry._id] = entry;
			}
			return map;
		}
		return payload;
	}
	function parseRef(ref) {
		if (typeof ref !== "string") return null;
		var m = ref.match(/globals\/(colors|typography)\?id=([^&]+)/);
		if (!m) return null;
		return {
			kind: m[1],
			id: decodeURIComponent(m[2])
		};
	}
	function bakeOne(settings, controlName, ref, colorMap, typoMap) {
		var parsed = parseRef(ref);
		if (!parsed) return;
		if (parsed.kind === "colors") {
			var c = colorMap[parsed.id];
			if (c && typeof c.color !== "undefined") settings[controlName] = c.color;
			return;
		}
		if (parsed.kind === "typography") {
			var t = typoMap[parsed.id];
			if (!t) return;
			for (var key in t) {
				if (!t.hasOwnProperty(key)) continue;
				if (key.indexOf("typography_") === 0) settings[key] = t[key];
			}
		}
	}
	function bakeSettings(settings, colorMap, typoMap) {
		if (!settings || typeof settings !== "object") return;
		var globals = settings.__globals__;
		if (globals && typeof globals === "object") {
			for (var controlName in globals) {
				if (!globals.hasOwnProperty(controlName)) continue;
				bakeOne(settings, controlName, globals[controlName], colorMap, typoMap);
			}
			delete settings.__globals__;
		}
		for (var prop in settings) {
			if (!settings.hasOwnProperty(prop)) continue;
			var val = settings[prop];
			if (Array.isArray(val)) {
				for (var i = 0; i < val.length; i++) if (val[i] && typeof val[i] === "object" && !Array.isArray(val[i])) bakeSettings(val[i], colorMap, typoMap);
			}
		}
	}
	function walk(node, colorMap, typoMap) {
		if (!node || typeof node !== "object") return;
		bakeSettings(node.settings, colorMap, typoMap);
		if (Array.isArray(node.elements)) for (var i = 0; i < node.elements.length; i++) walk(node.elements[i], colorMap, typoMap);
	}
	function jltmaFlattenGlobals(node, colors, typography) {
		walk(node, indexById(colors), indexById(typography));
		return node;
	}
	root.jltmaFlattenGlobals = jltmaFlattenGlobals;
})(typeof window !== "undefined" ? window : void 0);
//#endregion
})();
