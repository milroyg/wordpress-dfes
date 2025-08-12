(function($) {
    "use strict";
    $('.xevso-spimg').magnificPopup({
        delegate: 'a',
        type: 'image',
        mainClass: 'mfp-zoom-out', // this class is for CSS animation below
        gallery: { enabled: true },
        zoom: {
            enabled: true,
            duration: 300,
            easing: 'ease-in-out',
            opener: function(openerElement) {
                return openerElement.is('img') ? openerElement : openerElement.find('img');
            }
        }
    });
}(jQuery))