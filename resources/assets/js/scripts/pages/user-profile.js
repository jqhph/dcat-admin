/*=========================================================================================
    File Name: user-profile.js
    Description: User Profile jQuery Plugin Intialization
    --------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


$(document).ready(function(){

  /************************************
  *     Block Examples      *
  ************************************/
    $('.block-element').on('click', function() {
        var block_ele = $(this);
        $(block_ele).block({
            message: '<div class="spinner-border text-primary"></div>',
            timeout: 2000, //unblock after 2 seconds
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: 0,
                backgroundColor: 'transparent'
            }
        });
    });

    // profile-header-nav toggle
  $('.navbar-toggler').on('click',function(){
    $('.navbar-collapse').toggleClass('show');
    $('.navbar-toggler-icon i').toggleClass('icon-x icon-align-justify');
  });

});
