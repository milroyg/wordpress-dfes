(function(){
//#region dev/js/addons/free/ma-carousel.js
/**
* Master Addons - Carousel Helper
*
* Shared carousel functionality used by multiple widgets
*/
var $ = jQuery;
function MA_Carousel($swiper, settings) {
	var $slides = $swiper.find(".jltma-swiper__slide"), elementorBreakpoints = elementorFrontend.config.breakpoints, swiperInstance = $swiper.data("swiper"), swiperArgs = {
		autoHeight: settings.element.autoHeight || false,
		direction: settings.element.direction || settings.default.direction,
		effect: settings.element.effect || settings.default.effect,
		slidesPerView: settings.default.slidesPerView,
		slidesPerColumn: settings.default.slidesPerColumn,
		slidesPerColumnFill: "row",
		slidesPerGroup: settings.default.slidesPerGroup,
		spaceBetween: settings.default.spaceBetween,
		pagination: {},
		navigation: {},
		autoplay: settings.element.autoplay || false,
		grabCursor: true,
		watchSlidesProgress: true,
		watchSlidesVisibility: true
	};
	if (settings.default.breakpoints) {
		swiperArgs.breakpoints = {};
		swiperArgs.breakpoints[elementorBreakpoints.md] = settings.default.breakpoints.tablet;
		swiperArgs.breakpoints[elementorBreakpoints.lg] = settings.default.breakpoints.desktop;
	}
	if (!elementorFrontend.isEditMode()) {
		if (!settings.element.freeMode) {
			swiperArgs.observer = true;
			swiperArgs.observeParents = true;
			swiperArgs.observeSlideChildren = true;
		}
	} else {
		swiperArgs.observer = true;
		swiperArgs.observeParents = true;
		swiperArgs.observeSlideChildren = true;
	}
	MA_Carousel.init = function() {
		if (swiperInstance) {
			MA_Carousel.destroy();
			return;
		}
		if (swiperArgs.breakpoints) {
			if (settings.element.breakpoints.desktop.slidesPerView) swiperArgs.breakpoints[elementorBreakpoints.lg].slidesPerView = settings.stretch ? Math.min($slides.length, +settings.element.breakpoints.desktop.slidesPerView || 3) : +settings.element.breakpoints.desktop.slidesPerView || 3;
			if (settings.element.breakpoints.tablet.slidesPerView) swiperArgs.breakpoints[elementorBreakpoints.md].slidesPerView = settings.stretch ? Math.min($slides.length, +settings.element.breakpoints.tablet.slidesPerView || 2) : +settings.element.breakpoints.tablet.slidesPerView || 2;
		}
		if (settings.element.slidesPerView) swiperArgs.slidesPerView = settings.stretch ? Math.min($slides.length, +settings.element.slidesPerView || 1) : +settings.element.slidesPerView || 1;
		if (swiperArgs.breakpoints) {
			if (settings.element.breakpoints.desktop.slidesPerGroup) swiperArgs.breakpoints[elementorBreakpoints.lg].slidesPerGroup = Math.min($slides.length, +settings.element.breakpoints.desktop.slidesPerGroup || 3);
			if (settings.element.breakpoints.tablet.slidesPerGroup) swiperArgs.breakpoints[elementorBreakpoints.md].slidesPerGroup = Math.min($slides.length, +settings.element.breakpoints.tablet.slidesPerGroup || 2);
		}
		if (settings.element.slidesPerGroup) swiperArgs.slidesPerGroup = Math.min($slides.length, +settings.element.slidesPerGroup || 1);
		if (swiperArgs.breakpoints) {
			if (settings.element.breakpoints.desktop.slidesPerColumn) swiperArgs.breakpoints[elementorBreakpoints.lg].slidesPerColumn = settings.element.breakpoints.desktop.slidesPerColumn;
			if (settings.element.breakpoints.tablet.slidesPerColumn) swiperArgs.breakpoints[elementorBreakpoints.md].slidesPerColumn = settings.element.breakpoints.tablet.slidesPerColumn;
		}
		if (settings.element.slidesPerColumn) swiperArgs.slidesPerColumn = settings.element.slidesPerColumn;
		if (swiperArgs.breakpoints) {
			swiperArgs.breakpoints[elementorBreakpoints.lg].spaceBetween = settings.element.breakpoints.desktop.spaceBetween || 0;
			swiperArgs.breakpoints[elementorBreakpoints.md].spaceBetween = settings.element.breakpoints.tablet.spaceBetween || 0;
		}
		if (settings.element.spaceBetween) swiperArgs.spaceBetween = settings.element.spaceBetween || 0;
		if (settings.element.slidesPerColumnFill) swiperArgs.slidesPerColumnFill = settings.element.slidesPerColumnFill;
		if (settings.element.arrows) {
			swiperArgs.navigation.disabledClass = "jltma-swiper__button--disabled";
			var $prevButton = settings.scope.find(settings.element.arrowPrev), $nextButton = settings.scope.find(settings.element.arrowNext);
			if ($prevButton.length && $nextButton.length) {
				var arrowPrev = settings.element.arrowPrev + "-" + settings.id, arrowNext = settings.element.arrowNext + "-" + settings.id;
				$prevButton.addClass(arrowPrev.replace(".", ""));
				$nextButton.addClass(arrowNext.replace(".", ""));
				swiperArgs.navigation.prevEl = arrowPrev;
				swiperArgs.navigation.nextEl = arrowNext;
			}
		}
		if (settings.element.pagination) {
			swiperArgs.pagination.el = ".jltma-swiper__pagination-" + settings.id;
			swiperArgs.pagination.type = settings.element.paginationType;
			if (settings.element.paginationClickable) swiperArgs.pagination.clickable = true;
		}
		if (settings.element.loop) swiperArgs.loop = true;
		if (swiperArgs.autoplay && (settings.element.autoplaySpeed || settings.element.disableOnInteraction)) {
			swiperArgs.autoplay = {};
			if (settings.element.autoplaySpeed) swiperArgs.autoplay.delay = settings.element.autoplaySpeed;
			if (settings.element.autoplaySpeed) swiperArgs.autoplay.disableOnInteraction = settings.element.disableOnInteraction;
		}
		if (settings.element.speed) swiperArgs.speed = settings.element.speed;
		if (settings.element.resistance) swiperArgs.resistanceRatio = 1 - settings.element.resistance;
		if (settings.element.freeMode) {
			swiperArgs.freeMode = true;
			swiperArgs.freeModeSticky = settings.element.freeModeSticky;
			swiperArgs.freeModeMomentum = settings.element.freeModeMomentum;
			swiperArgs.freeModeMomentumBounce = settings.element.freeModeMomentumBounce;
			if (settings.element.freeModeMomentumRatio) swiperArgs.freeModeMomentumRatio = settings.element.freeModeMomentumRatio;
			if (settings.element.freeModeMomentumVelocityRatio) swiperArgs.freeModeMomentumVelocityRatio = settings.element.freeModeMomentumVelocityRatio;
			if (settings.element.freeModeMomentumBounceRatio) swiperArgs.freeModeMomentumBounceRatio = settings.element.freeModeMomentumBounceRatio;
		}
		return swiperArgs;
	};
	MA_Carousel.onAfterInit = function($swiper, swiper, settings) {
		if ("undefined" == typeof settings || "undefined" == typeof swiper) return;
		if (settings.element.stopOnHover) {
			$swiper.on("mouseover", function() {
				swiper.autoplay.stop();
			});
			$swiper.on("mouseout", function() {
				swiper.autoplay.start();
			});
		}
		if (settings.element.slideChangeTriggerResize) swiper.on("slideChange", function() {
			$(window).trigger("resize");
		});
		$swiper.data("swiper", swiper);
	};
	return MA_Carousel.init();
}
//#endregion
})();
