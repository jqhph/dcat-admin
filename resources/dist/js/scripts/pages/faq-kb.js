/*=========================================================================================
    File Name: app-todo.js
    Description: app-todo
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(function() {
  "use strict";

  // Filter
  $("#searchbar").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    if(value!=""){
      $(".search-content-info .search-content").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
      var search_row = $(".search-content-info .search-content:visible").length;

      //Check if search-content has row or not
      if ( search_row == 0 ){
        $('.search-content-info .no-result').removeClass('no-items');
      }
      else{
        if(!$('.search-content-info .no-result').hasClass('no-items') ){
          $('.search-content-info .no-result').addClass('no-items');
        }
      }
    }
    else {
      // If filter box is empty
      $(".search-content-info .search-content").show();
      if ($('.search-content-info .no-results').hasClass('no-items')) {
        $('.search-content-info .no-results').removeClass('no-items');
      }
    }
  });

});
