/*=========================================================================================
	File Name: input-groups.js
	Description: Input Groups js
	----------------------------------------------------------------------------------------
	Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: PIXINVENT
	Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

(function (window, document, $) {
  'use strict';
  var $html = $('html');

  // Default Spin
  $(".touchspin").TouchSpin({
    buttondown_class: "btn btn-primary",
    buttonup_class: "btn btn-primary",
  });

  // Icon Change
  $(".touchspin-icon").TouchSpin({
    buttondown_txt: '<i class="feather icon-chevron-down"></i>',
    buttonup_txt: '<i class="feather icon-chevron-up"></i>'
  });

  // Min - Max

  var touchspinValue = $(".touchspin-min-max"),
    counterMin = 15,
    counterMax = 21;
  if (touchspinValue.length > 0) {
    touchspinValue.TouchSpin({
      min: counterMin,
      max: counterMax
    }).on('touchspin.on.startdownspin', function () {
      var $this = $(this);
      $('.bootstrap-touchspin-up').removeClass("disabled-max-min");
      if ($this.val() == counterMin) {
        $(this).siblings().find('.bootstrap-touchspin-down').addClass("disabled-max-min");
      }
    }).on('touchspin.on.startupspin', function () {
      var $this = $(this);
      $('.bootstrap-touchspin-down').removeClass("disabled-max-min");
      if ($this.val() == counterMax) {
        $(this).siblings().find('.bootstrap-touchspin-up').addClass("disabled-max-min");
      }
    });
  }


  // Step
  $(".touchspin-step").TouchSpin({
    step: 5
  });

  // Color Options
  $(".touchspin-color").each(function (index) {
    var down = "btn btn-primary",
      up = "btn btn-primary",
      $this = $(this);
    if ($this.data('bts-button-down-class')) {
      down = $this.data('bts-button-down-class');
    }
    if ($this.data('bts-button-up-class')) {
      up = $this.data('bts-button-up-class');
    }
    $this.TouchSpin({
      mousewheel: false,
      buttondown_class: down,
      buttonup_class: up,
      buttondown_txt: '<i class="feather icon-minus"></i>',
      buttonup_txt: '<i class="feather icon-plus"></i>'
    });
  });

})(window, document, jQuery);
