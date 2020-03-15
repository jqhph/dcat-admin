// =========================================================================================
//     File Name: invoice.js
//     Description: Invoice print js
//     --------------------------------------------------------------------------------------
//     Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
//     Author: PIXINVENT
//     Author URL: http://www.themeforest.net/user/pixinvent
// ==========================================================================================

$(document).ready(function () {
  // print invoice with button
  $(".btn-print").click(function () {
    window.print();
  });
});
