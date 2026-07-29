(function(){
//#region dev/js/modules/free/jltma-cross-copy-paste.js
window.XdUtils = window.XdUtils || { extend: function(e, t) {
	var n, i = t || {};
	for (n in e) e.hasOwnProperty(n) && (i[n] = e[n]);
	return i;
} }, window.jltmaXdLocalStorage = window.jltmaXdLocalStorage || (function() {
	var e = "cross-domain-local-message", t = {
		iframeId: "cross-domain-iframe-id",
		iframeUrl: void 0,
		initCallback: function() {}
	}, n = -1, i = null, o = {}, a = !1, s = !0;
	function r(n) {
		var i;
		try {
			i = JSON.parse(n.data);
		} catch (a) {}
		i && i.namespace === e && ("iframe-ready" === i.id ? (s = !0, t.initCallback()) : (function(e) {
			o[e.id] && (o[e.id](e), delete o[e.id]);
		})(i));
	}
	function l(t, a, s, r) {
		n++, o[n] = r;
		var l = {
			namespace: e,
			id: n,
			action: t,
			key: a,
			value: s
		};
		null !== i && i.contentWindow.postMessage(JSON.stringify(l), "*");
	}
	function c(e) {
		t = XdUtils.extend(e, t);
		var n = document.createElement("div");
		window.addEventListener ? window.addEventListener("message", r, !1) : window.attachEvent("onmessage", r), n.innerHTML = "<iframe id=\"" + t.iframeId + "\" src=\"" + t.iframeUrl + "\" style=\"display: none;\"></iframe>", document.body.appendChild(n), i = document.getElementById(t.iframeId);
	}
	function d() {
		return !!a && !!s;
	}
	function m() {
		return "complete" === document.readyState;
	}
	return {
		init: function(e) {
			if (!e.iframeUrl) throw "You must specify iframeUrl [CP]";
			a || (a = !0, m() ? c(e) : document.addEventListener ? document.addEventListener("readystatechange", function() {
				m() && c(e);
			}) : document.attachEvent("readystatechange", function() {
				m() && c(e);
			}));
		},
		setItem: function(e, t, n) {
			d() && l("set", e, t, n);
		},
		getItem: function(e, t) {
			d() && l("get", e, null, t);
		},
		removeItem: function(e, t) {
			d() && l("remove", e, null, t);
		},
		key: function(e, t) {
			d() && l("key", e, null, t);
		},
		getSize: function(e) {
			d() && l("size", null, null, e);
		},
		getLength: function(e) {
			d() && l("length", null, null, e);
		},
		clear: function(e) {
			d() && l("clear", null, null, e);
		},
		wasInit: function() {
			return a;
		}
	};
})(), (function(e) {
	var t = {
		elementTypes: [
			"widget",
			"column",
			"section",
			"container"
		],
		storageKey: jltma_cp_xd.storage_key,
		lastCopiedJson: "",
		lastPastedModel: {},
		sameServer: !1,
		widget: null,
		container: null,
		option: {},
		msg: jltma_cp_xd.message
	};
	function n() {
		elementor.notifications.showToast({ message: elementor.translate(t.msg.paste) });
	}
	function jltmaRelayUrl() {
		if (typeof jltma_cp_xd !== "undefined" && jltma_cp_xd.clipboard_url) return jltma_cp_xd.clipboard_url;
		return (typeof jltma_cp_xd !== "undefined" && jltma_cp_xd.relay_url ? jltma_cp_xd.relay_url : "").replace(/\/+$/, "") + "/wp-json/masteraddons/v1/magic-copy";
	}
	t.elementTypes.forEach(function(i, o) {
		let a = t.elementTypes[o];
		elementor.hooks.addFilter("elements/" + a + "/contextMenuGroups", function(i, o) {
			return i.push({
				name: "jltma_" + a,
				actions: [{
					name: "jltma_copy",
					title: "MA Copy",
					callback: function() {
						var e = {};
						e.elementType = "widget" === a ? o.model.get("widgetType") : null;
						var code = o.model.toJSON();
						try {
							if (window.jltmaCaptureComputedStyles && elementor.$previewContents) code = window.jltmaCaptureComputedStyles(code, elementor.$previewContents[0]);
						} catch (err) {
							console.warn("[MagicCopy] computed-style capture failed; copying raw.", err);
						}
						var store = function(finalCode) {
							e.elementCode = finalCode;
							fetch(jltmaRelayUrl(), {
								method: "POST",
								headers: {
									"Content-Type": "application/json",
									"X-WP-Nonce": typeof jltma_cp_xd !== "undefined" && jltma_cp_xd.rest_nonce ? jltma_cp_xd.rest_nonce : ""
								},
								credentials: "include",
								body: JSON.stringify(e)
							}).then(function(r) {
								if (!r.ok) throw new Error("relay");
								elementor.notifications.showToast({ message: elementor.translate(t.msg.copy) });
							}).catch(function() {
								elementor.notifications.showToast({ message: elementor.translate(t.msg.error) });
							});
						};
						if (!window.jltmaFlattenGlobals) {
							store(code);
							return;
						}
						try {
							var colors = $e.data.get("globals/colors");
							var typography = $e.data.get("globals/typography");
							Promise.all([Promise.resolve(colors), Promise.resolve(typography)]).then(function(res) {
								try {
									code = window.jltmaFlattenGlobals(code, res[0], res[1]);
								} catch (err) {
									console.warn("[MagicCopy] globals flatten failed; copying raw.", err);
								}
								store(code);
							}).catch(function(err) {
								console.warn("[MagicCopy] globals unavailable; copying raw.", err);
								store(code);
							});
						} catch (err) {
							console.warn("[MagicCopy] globals lookup threw; copying raw.", err);
							store(code);
						}
					}
				}, {
					name: "jltma_paste",
					title: "MA Paste",
					callback: function() {
						var jltmaHandlePaste = function(i, o) {
							if (null === i || "" === i || Array.isArray(i) && 0 === i.length) return elementor.notifications.showToast({ message: elementor.translate(t.msg.empty_copy) }), !1;
							let a = o.model.get("elType"), s = i.elementCode.elType, r = i.elementCode;
							if (t.widget = {
								elType: s,
								settings: r.settings
							}, "section" === s || "container" === s) t.widget.elements = r.elements, t.container = elementor.getPreviewContainer();
							else if ("column" === s)
 //!sameElement ? changeElementID(elementCode.elements) : elementCode.elements;
							switch (t.widget.elements = r.elements, a) {
								case "widget":
									t.container = o.getContainer().parent.parent;
									break;
								case "column":
									t.container = o.getContainer().parent;
									break;
								case "section": t.container = o.getContainer();
							}
							else if ("widget" === s) switch (t.widget.widgetType = i.elementType, a) {
								case "widget":
									t.container = o.getContainer().parent, t.option.at = o.getOption("_index") + 1;
									break;
								case "column":
									t.container = o.getContainer();
									break;
								case "section": t.container = o.children.findByIndex(0).getContainer();
							}
							(function(i) {
								var o = JSON.stringify(i), hasMedia = /\.(gif|jpg|jpeg|svg|png|tiff|bmp|pdf)/gi.test(o) || /"id":\s*\d+/.test(o) || /"url":"https?:\/\//i.test(o);
								if (e.isEmptyObject(t.lastPastedModel) || o !== t.lastCopiedJson) if (hasMedia && (t.needsImport || !t.sameServer)) {
									let e = elementor.notifications.getToast(), i = e.getSettings();
									"hide" in i && (i = Object.assign(i.hide, { auto: !1 }), e.setSettings(i)), elementor.notifications.showToast({ message: elementor.translate(t.msg.import_wait) }), elementorCommon.ajax.addRequest("jltma_copy_paste", {
										data: {
											type: "single",
											template: o,
											nonce: jltma_cp_xd.nonce
										},
										success: function(a) {
											var s = a;
											s.hasOwnProperty("data") && (s = s.data), "hide" in i ? (i = Object.assign(i.hide, { auto: !0 }), e.setSettings(i)) : "auto" in i && (i.auto = !0, e.setSettings(i)), t.widget.elType = s.elType, t.widget.settings = s.settings, "widget" === s.elType ? t.widget.widgetType = s.widgetType : t.widget.elements = s.elements, $e.run("document/elements/create", {
												model: t.widget,
												container: t.container,
												options: t.option
											}), n(), t.lastPastedModel = t.widget, t.lastCopiedJson = o;
										},
										error: function(e) {
											elementor.notifications.showToast({ message: elementor.translate(t.msg.error) });
										}
									});
								} else $e.run("document/elements/create", {
									model: t.widget,
									container: t.container,
									options: t.option
								}), n();
								else t.option.clone = !0, $e.run("document/elements/create", {
									model: t.lastPastedModel,
									container: t.container,
									options: t.option
								}), n();
							})(r);
						};
						var jltmaReadAndPaste = function(text) {
							if (text == null || "" === text) {
								elementor.notifications.showToast({ message: elementor.translate(t.msg.empty_copy) });
								return;
							}
							var parsed;
							try {
								parsed = JSON.parse(text);
							} catch (err) {
								elementor.notifications.showToast({ message: elementor.translate(t.msg.empty_copy) });
								return;
							}
							if (!parsed || !parsed.elementCode) {
								elementor.notifications.showToast({ message: elementor.translate(t.msg.empty_copy) });
								return;
							}
							jltmaHandlePaste(parsed, o);
						};
						fetch(jltmaRelayUrl(), {
							method: "GET",
							headers: { "X-WP-Nonce": typeof jltma_cp_xd !== "undefined" && jltma_cp_xd.rest_nonce ? jltma_cp_xd.rest_nonce : "" },
							credentials: "include"
						}).then(function(r) {
							return r.json();
						}).then(function(res) {
							t.needsImport = !!(res && res.needs_import);
							jltmaReadAndPaste(res && res.data ? res.data : "");
						}).catch(function() {
							elementor.notifications.showToast({ message: elementor.translate(t.msg.error) });
						});
					}
				}]
			}), i;
		});
	});
	function jltmaInsertAtRoot(parsed, view) {
		var code = parsed && parsed.elementCode;
		if (!code || !code.elType) {
			elementor.notifications.showToast({ message: elementor.translate(t.msg.empty_copy) });
			return;
		}
		var model = code;
		if ("widget" === code.elType) {
			if (parsed.elementType) code.widgetType = parsed.elementType;
			model = {
				elType: "container",
				settings: {},
				elements: [code]
			};
		} else if ("column" === code.elType) model = {
			elType: "section",
			settings: {},
			elements: [code]
		};
		var options = {};
		if (view && typeof view.getOption === "function") {
			var at = view.getOption("at");
			if (typeof at !== "undefined") options.at = at;
		}
		$e.run("document/elements/create", {
			model,
			container: elementor.getPreviewContainer(),
			options
		});
		n();
	}
	function jltmaInsertParsed(parsed, view) {
		if (!t.needsImport) {
			jltmaInsertAtRoot(parsed, view);
			return;
		}
		elementorCommon.ajax.addRequest("jltma_copy_paste", {
			data: {
				type: "single",
				template: JSON.stringify(parsed.elementCode),
				nonce: jltma_cp_xd.nonce
			},
			success: function(resp) {
				parsed.elementCode = (resp && resp.hasOwnProperty("data") ? resp.data : resp) || parsed.elementCode;
				jltmaInsertAtRoot(parsed, view);
			},
			error: function() {
				jltmaInsertAtRoot(parsed, view);
			}
		});
	}
	function jltmaPasteAtRoot(view) {
		fetch(jltmaRelayUrl(), {
			method: "GET",
			headers: { "X-WP-Nonce": typeof jltma_cp_xd !== "undefined" && jltma_cp_xd.rest_nonce ? jltma_cp_xd.rest_nonce : "" },
			credentials: "include"
		}).then(function(r) {
			return r.json();
		}).then(function(res) {
			t.needsImport = !!(res && res.needs_import);
			var text = res && res.data ? res.data : "";
			if (text == null || "" === text) {
				elementor.notifications.showToast({ message: elementor.translate(t.msg.empty_copy) });
				return;
			}
			var parsed;
			try {
				parsed = JSON.parse(text);
			} catch (err) {
				elementor.notifications.showToast({ message: elementor.translate(t.msg.empty_copy) });
				return;
			}
			jltmaInsertParsed(parsed, view);
		}).catch(function() {
			elementor.notifications.showToast({ message: elementor.translate(t.msg.error) });
		});
	}
	elementor.hooks.addFilter("views/add-section/behaviors", function(behaviors, view) {
		try {
			if (behaviors && behaviors.contextMenu && Array.isArray(behaviors.contextMenu.groups)) behaviors.contextMenu.groups.push({
				name: "jltma_xd_root",
				actions: [{
					name: "jltma_paste_root",
					title: "MA Paste",
					callback: function() {
						jltmaPasteAtRoot(view);
					}
				}]
			});
		} catch (err) {}
		return behaviors;
	});
})(jQuery);
//#endregion
})();
