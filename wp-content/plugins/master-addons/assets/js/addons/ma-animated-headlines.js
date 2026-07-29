(function(){
//#region dev/js/common/utils.js
/**
* Master Addons - Shared Utilities
*
* Common helper functions used across all widget handlers.
*/
/**
* Get element settings from Elementor
*
* @param {jQuery} $element - The jQuery element
* @param {string} setting - Optional specific setting key
* @returns {*} Settings object or specific setting value
*/
function getElementSettings($element, setting) {
	var elementSettings = {}, modelCID = $element.data("model-cid");
	if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode() && modelCID) {
		var settings = elementorFrontend.config.elements.data[modelCID], type = settings.attributes.widgetType || settings.attributes.elType, settingsKeys = elementorFrontend.config.elements.keys[type];
		if (!settingsKeys) {
			settingsKeys = elementorFrontend.config.elements.keys[type] = [];
			jQuery.each(settings.controls, function(name, control) {
				if (control.frontend_available) settingsKeys.push(name);
			});
		}
		jQuery.each(settings.getActiveControls(), function(controlKey) {
			if (-1 !== settingsKeys.indexOf(controlKey)) elementSettings[controlKey] = settings.attributes[controlKey];
		});
	} else elementSettings = $element.data("settings") || {};
	return getItems(elementSettings, setting);
}
/**
* Get nested items from an object
*
* @param {Object} items - The items object
* @param {string} itemKey - Dot-notation key path
* @returns {*} The value at the key path
*/
function getItems(items, itemKey) {
	if (itemKey) {
		var keyStack = itemKey.split("."), currentKey = keyStack.splice(0, 1);
		if (!keyStack.length) return items[currentKey];
		if (!items[currentKey]) return;
		return getItems(items[currentKey], keyStack.join("."));
	}
	return items;
}
/**
* Get unique loop scope ID
*
* @param {jQuery} $scope - The scope element
* @returns {string} The unique ID
*/
function getUniqueLoopScopeId($scope) {
	if ($scope.data("jltma-template-widget-id")) return $scope.data("jltma-template-widget-id");
	return $scope.data("id");
}
/**
* Observe target element with IntersectionObserver
*
* @param {Element} target - The target element to observe
* @param {Function} callback - Callback when element intersects
* @param {Object} options - IntersectionObserver options
*/
function jltMAObserveTarget(target, callback, options = {}) {
	new IntersectionObserver(function(entries, observer) {
		entries.forEach(function(entry) {
			if (entry.isIntersecting) callback(entry);
		});
	}, options).observe(target);
}
/**
* Strip HTML tags from text
*
* @param {string} text - Text to strip
* @returns {string} Text without HTML tags
*/
function stripTags(text) {
	return text.replace(/<\/?[^>]+(>|$)/g, "");
}
/**
* Check if in Elementor edit mode
*
* @returns {boolean} True if in edit mode
*/
function isEditMode() {
	return typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode();
}
//#endregion
//#region dev/js/addons/free/ma-animated-headlines.js
/**
* Start animated headlines widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_AnimatedHeadlines = function($scope, $) {
		var elementSettings = getElementSettings($scope);
		if (!$scope.find(".jltma-animated-headline").eq(0).length) return;
		var animationDelay = elementSettings.anim_delay || 2500, barAnimationDelay = elementSettings.bar_anim_delay || 3800, barWaiting = 400, lettersDelay = elementSettings.letters_anim_delay || 50, typeLettersDelay = elementSettings.type_anim_delay || 150, selectionDuration = elementSettings.type_selection_delay || 500, typeAnimationDelay = selectionDuration + 800, revealDuration = elementSettings.clip_reveal_delay || 600, revealAnimationDelay = elementSettings.clip_anim_duration || 1500;
		function singleLetters($words) {
			$words.each(function() {
				var word = $(this), letters = word.text().trim().split(""), selected = word.hasClass("is-visible");
				for (var i = 0; i < letters.length; i++) {
					if (letters[i] === " ") letters[i] = "\xA0";
					if (word.parents(".rotate-2").length > 0) letters[i] = "<em>" + letters[i] + "</em>";
					letters[i] = selected ? "<i class=\"in\">" + letters[i] + "</i>" : "<i>" + letters[i] + "</i>";
				}
				var newLetters = letters.join("");
				word.html(newLetters).css("opacity", 1);
			});
		}
		function takeNext($word) {
			return !$word.is(":last-child") ? $word.next() : $word.parent().children().eq(0);
		}
		function switchWord($oldWord, $newWord) {
			$oldWord.removeClass("is-visible").addClass("is-hidden");
			$newWord.removeClass("is-hidden").addClass("is-visible");
		}
		function hideLetter($letter, $word, $bool, $duration) {
			$letter.removeClass("in").addClass("out");
			if (!$letter.is(":last-child")) setTimeout(function() {
				hideLetter($letter.next(), $word, $bool, $duration);
			}, $duration);
			else if ($bool) setTimeout(function() {
				hideWord(takeNext($word));
			}, animationDelay);
			if ($letter.is(":last-child") && $("html").hasClass("no-csstransitions")) switchWord($word, takeNext($word));
		}
		function showLetter($letter, $word, $bool, $duration) {
			$letter.addClass("in").removeClass("out");
			if (!$letter.is(":last-child")) setTimeout(function() {
				showLetter($letter.next(), $word, $bool, $duration);
			}, $duration);
			else {
				if ($word.parents(".jltma-animated-headline").hasClass("type")) setTimeout(function() {
					$word.parents(".jltma-words-wrapper").addClass("waiting");
				}, 200);
				if (!$bool) setTimeout(function() {
					hideWord($word);
				}, animationDelay);
			}
		}
		function showWord($word, $duration) {
			if ($word.parents(".jltma-animated-headline").hasClass("type")) {
				showLetter($word.find("i").eq(0), $word, false, $duration);
				$word.addClass("is-visible").removeClass("is-hidden");
			} else if ($word.parents(".jltma-animated-headline").hasClass("clip")) $word.parents(".jltma-words-wrapper").animate({ "width": $word.width() + 10 }, revealDuration, function() {
				setTimeout(function() {
					hideWord($word);
				}, revealAnimationDelay);
			});
		}
		function hideWord($word) {
			var nextWord = takeNext($word);
			if ($word.parents(".jltma-animated-headline").hasClass("type")) {
				var parentSpan = $word.parent(".jltma-words-wrapper");
				parentSpan.addClass("selected").removeClass("waiting");
				setTimeout(function() {
					parentSpan.removeClass("selected");
					$word.removeClass("is-visible").addClass("is-hidden").children("i").removeClass("in").addClass("out");
				}, selectionDuration);
				setTimeout(function() {
					showWord(nextWord, typeLettersDelay);
				}, typeAnimationDelay);
			} else if ($word.parents(".jltma-animated-headline").hasClass("letters")) {
				var bool = $word.children("i").length >= nextWord.children("i").length;
				hideLetter($word.find("i").eq(0), $word, bool, lettersDelay);
				showLetter(nextWord.find("i").eq(0), nextWord, bool, lettersDelay);
			} else if ($word.parents(".jltma-animated-headline").hasClass("clip")) $word.parents(".jltma-words-wrapper").animate({ width: "2px" }, revealDuration, function() {
				switchWord($word, nextWord);
				showWord(nextWord);
			});
			else if ($word.parents(".jltma-animated-headline").hasClass("loading-bar")) {
				$word.parents(".jltma-words-wrapper").removeClass("is-loading");
				switchWord($word, nextWord);
				setTimeout(function() {
					hideWord(nextWord);
				}, barAnimationDelay);
				setTimeout(function() {
					$word.parents(".jltma-words-wrapper").addClass("is-loading");
				}, barWaiting);
			} else {
				switchWord($word, nextWord);
				setTimeout(function() {
					hideWord(nextWord);
				}, animationDelay);
			}
		}
		function animateHeadline($headlines) {
			var duration = animationDelay;
			$headlines.each(function() {
				var headline = $(this);
				if (headline.hasClass("loading-bar")) {
					duration = barAnimationDelay;
					headline.find(".jltma-words-wrapper").each(function() {
						this.style.setProperty("--jltma-bar-duration", (barAnimationDelay - barWaiting) / 1e3 + "s");
					});
					setTimeout(function() {
						headline.find(".jltma-words-wrapper").addClass("is-loading");
					}, barWaiting);
				} else if (headline.hasClass("clip")) {
					var spanWrapper = headline.find(".jltma-words-wrapper"), newWidth = spanWrapper.width() + 10;
					spanWrapper.css("width", newWidth);
				} else if (!headline.hasClass("type")) {
					var words = headline.find(".jltma-words-wrapper b"), width = 0;
					words.each(function() {
						var wordWidth = $(this).width();
						if (wordWidth > width) width = wordWidth;
					});
					headline.find(".jltma-words-wrapper").css("width", width);
				}
				setTimeout(function() {
					hideWord(headline.find(".is-visible").eq(0));
				}, duration);
			});
		}
		singleLetters($scope.find(".jltma-animated-headline.letters b"));
		animateHeadline($scope.find(".jltma-animated-headline"));
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-headlines.default", JLTMA_AnimatedHeadlines);
	});
})(jQuery, window.elementorFrontend);
/**
* End animated headlines widget script
*/
//#endregion
})();
