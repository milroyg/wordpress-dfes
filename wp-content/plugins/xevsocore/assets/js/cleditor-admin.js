(function($) {
    "use strict";
    $(document).ready(function() {

        $("#post-formats-select .post-format").on("click", function() {

            if ($(this).attr("id") == "post-format-gallery") {
                $("#xevso_pfg_meta").show();
            } else {
                $("#xevso_pfg_meta").hide();
            }

            if ($(this).attr("id") == "post-format-audio") {
                $("#xevso_pfa_meta").show();
            } else {
                $("#xevso_pfa_meta").hide();
            }

            if ($(this).attr("id") == "post-format-video") {
                $("#xevso_pfv_meta").show();
            } else {
                $("#xevso_pfv_meta").hide();
            }
        });

    });
})(jQuery);