; (function ($) {
	// Find all elements with the class 'splw-pro-wrapper' and 'lw-preloader-wrapper' within the body
	jQuery('body').find('.splw-lite-wrapper.lw-preloader-wrapper').each(function () {
		// Get the 'lw_id' and 'parents_siblings_id' values
		var lw_id = $(this).parent('.splw-main-wrapper').attr('id');
		var parents_siblings_id = $('#' + lw_id).find('.lw-preloader').attr('id');

		// Ensure the document is ready before executing the code
		$(document).ready(function () {
			// Animate the identified 'parents_siblings_id' element and then remove it
			$('#' + parents_siblings_id).animate({ opacity: 0 }, 600, function () {
				$(this).remove();
			});
		});
	});

})(jQuery);