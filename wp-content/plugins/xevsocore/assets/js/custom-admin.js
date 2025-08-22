(function($) {
    "use strict";
    wp.data.subscribe(() => {
        var postFormat = wp.data.select('core/editor').getEditedPostAttribute('format');
        $('#xevso_pfg_meta, #xevso_pfv_meta, #xevso_pfa_meta').hide();

        if (postFormat == 'gallery') {
            $('#xevso_pfg_meta').fadeIn();
        };
        if (postFormat == 'video') {
            $('#xevso_pfv_meta').fadeIn();
        };
        if (postFormat == 'audio') {
            $('#xevso_pfa_meta').fadeIn();
        };
    });
})(jQuery);