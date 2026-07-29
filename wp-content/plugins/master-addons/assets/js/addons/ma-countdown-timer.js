(function(){
//#region dev/js/addons/free/ma-countdown-timer.js
/**
* Start countdown timer widget script
*/
(function($, elementor) {
	"use strict";
	var JLTMA_CountdownTimer = function($scope, $) {
		var $countdownWidget = $scope.find(".jltma-widget-countdown");
		$.fn.MasterCountDownTimer = function() {
			var $wrapper = $(this).find(".jltma-countdown-wrapper"), data = {
				year: $wrapper.data("countdown-year"),
				month: $wrapper.data("countdown-month"),
				day: $wrapper.data("countdown-day"),
				hour: $wrapper.data("countdown-hour"),
				min: $wrapper.data("countdown-min"),
				sec: $wrapper.data("countdown-sec")
			};
			$wrapper.data("countdown-infinite");
			var targetDate = new Date(data.year, data.month, data.day, data.hour, data.min, data.sec);
			var $year = $wrapper.find(".jltma-countdown-year"), $month = $wrapper.find(".jltma-countdown-month"), $day = $wrapper.find(".jltma-countdown-day"), $hour = $wrapper.find(".jltma-countdown-hour"), $min = $wrapper.find(".jltma-countdown-min"), $sec = $wrapper.find(".jltma-countdown-sec");
			var countdownInterval = setInterval(function() {
				var currentTime = /* @__PURE__ */ new Date();
				var diffTime = (Date.parse(targetDate) - Date.parse(currentTime)) / 1e3;
				if (diffTime <= 0) {
					$year.text(0);
					$month.text(0);
					$day.text(0);
					$hour.text(0);
					$min.text(0);
					$sec.text(0);
					clearInterval(countdownInterval);
					return;
				}
				var totalSeconds = diffTime;
				var years = Math.floor(totalSeconds / 31536e3);
				totalSeconds %= 31536e3;
				var months = Math.floor(totalSeconds / 2592e3);
				totalSeconds %= 2592e3;
				var days = Math.floor(totalSeconds / 86400);
				totalSeconds %= 86400;
				var hours = Math.floor(totalSeconds / 3600);
				totalSeconds %= 3600;
				var minutes = Math.floor(totalSeconds / 60);
				var seconds = Math.floor(totalSeconds % 60);
				$year.text(years);
				$month.text(months);
				$day.text(days);
				$hour.text(hours);
				$min.text(minutes);
				$sec.text(seconds);
			}, 1e3);
		};
		$countdownWidget.each(function() {
			$(this).MasterCountDownTimer();
		});
	};
	$(window).on("elementor/frontend/init", function() {
		elementorFrontend.hooks.addAction("frontend/element_ready/ma-el-countdown-timer.default", JLTMA_CountdownTimer);
	});
})(jQuery, window.elementorFrontend);
/**
* End countdown timer widget script
*/
//#endregion
})();
