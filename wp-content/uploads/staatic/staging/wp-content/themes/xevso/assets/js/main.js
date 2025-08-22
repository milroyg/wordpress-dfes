(function($) {
    "use strict";
    // Navbar Hover Menu
    $('[data-toggle="tooltip"]').tooltip()
    $('#main-menu').smartmenus();
    $('#footer-menu').smartmenus({
        bottomToTopSubMenus: true,
    });


    jQuery('#xevso-post-gallery').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
    });

    // Footer Widget masonry
    if (typeof imagesLoaded == 'function') {
        $('.masonrys > div').addClass('masonry-item');
        var $boxes = $('.masonry-item');
        $boxes.hide();
        var $container = $('.masonrys');
        $container.imagesLoaded(function() {
            $boxes.fadeIn();
            $container.masonry({
                itemSelector: '.masonry-item',
            });
        });
    }
    // Bottom to top 
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            $('#back-top').fadeIn();
        } else {
            $('#back-top').fadeOut();
        }
    });

    $('#back-top').on('click', function() {
        $("html, body").animate({
            scrollTop: 0
        }, 1000);
        return false;
    });
    // Preloader 
    var winObj = $(window),
        bodyObj = $('body'),
        headerObj = $('header');
    winObj.on('load', function() {
        var $preloader = $('#preloader');
        $preloader.find('.group').fadeOut();
        $preloader.delay(350).fadeOut('slow');
    });
    $('.xevso-product-big-img').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.xevso-product-small-img'
    });
    /*=====================================================================
        04: Sticky menu
    ======================================================================*/
    function menuSticky (){
        var $scroll = $(window).scrollTop();
        if($scroll > 120){
            $('.header-two').addClass('sticky');
        }else{
            $('.header-two').removeClass('sticky');
        }
     }
     menuSticky ()
     $(window).on('scroll',function(){
        menuSticky ()

    });


    $('.xevso-product-small-img').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.xevso-product-big-img',
        dots: true,
        arrows: false,
        focusOnSelect: true,
        centerMode: true,
        centerPadding: '60px',
    });

    $("#ser-input").focus(function() {
        $('.search-full-view').addClass("search-normal-screen");
    });
    $("#search-close").click(function() {
        $('.search-full-view').removeClass("search-normal-screen");
    });
    $('.post-gallery').slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true
    });

  setTimeout(function(){
        $('.loader_bg').fadeToggle();
     }, 3200);

}(jQuery))