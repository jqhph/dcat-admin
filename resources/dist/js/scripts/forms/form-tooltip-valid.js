/*=========================================================================================
    File Name: form-tooltip-valid.js
    Description: form tooltip validation etc..
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function(window, document, $) {
  'use strict';

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  // Loop over them and prevent submission
  $("button").click(function () {
    var form = $(".needs-validation");
    if (form[0].checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.addClass('was-validated');
  });

})(window, document, jQuery);
