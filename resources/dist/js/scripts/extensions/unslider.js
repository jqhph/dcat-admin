/*=========================================================================================
    File Name: sweet-alerts.js
    Description: A beautiful replacement for javascript alerts
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function(){

    // RTL Support
    var rtl = false;
    if($('html').data('textdirection') == 'rtl'){
        rtl = true;
    }
    if(rtl === true){
        $("#default-slider").attr('dir', 'rtl');
        $("#automatic-slider").attr('dir', 'rtl');
        $("#vertical-slider").attr('dir', 'rtl');
        $("#automcatic-anim-slider").attr('dir', 'rtl');
        $("#infinite-slider").attr('dir', 'rtl');
        $("#manual-slider").attr('dir', 'rtl');
        $("#manual").attr('dir', 'rtl');
    }


    // Default
    $("#default-slider").unslider({
        animation: 'fade'
    });

    // Automatic
    $("#automatic-slider").unslider({
        autoplay: true,
        animation: "fade"
    });

    // Vertical
    $("#vertical-slider").unslider({
        animation: 'vertical',
        autoplay: true,
        infinite: true
    });

    // Automatic Animation
    $("#automcatic-anim-slider").unslider({
        animation: 'fade',
        autoplay: true,
        arrows: false
    });

    // Infinite
    $("#infinite-slider").unslider({
        animation: "fade",
        infinite: true
    });

    // Manual Slider
    $("#manual-slider").unslider({
        keys: false,
        arrows: false,
        nav: false
    });

    $('#manual').on('keyup', function() {
        $('.manual-slider').unslider('animate:' + $(this).val());
    });
});