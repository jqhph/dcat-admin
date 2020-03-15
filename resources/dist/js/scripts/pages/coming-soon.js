/*=========================================================================================
    File Name: page-coming-soon.js
    Description: Coming Soon
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

/*******************************
*       js of Countdown        *
********************************/

$(document).ready(function() {

  var todayDate = new Date();
  var releaseDate = new Date(todayDate.setDate(todayDate.getDate()+5));

  var dd = releaseDate.getDate();
  var mm = releaseDate.getMonth() + 1;
  var yy = releaseDate.getFullYear();
  var releaseDate = yy + "/" + mm + "/" + dd;


  $('#clockFlat').countdown(releaseDate).on('update.countdown', function(event) {
    var $this = $(this).html(event.strftime('<div class="clockCard px-1"> <span>%d</span> <br> <p class="bg-amber clockFormat lead px-1 black"> Day%!d </p> </div>'
      + '<div class="clockCard px-1"> <span>%H</span> <br> <p class="bg-amber clockFormat lead px-1 black"> Hour%!H </p> </div>'
      + '<div class="clockCard px-1"> <span>%M</span> <br> <p class="bg-amber clockFormat lead px-1 black"> Minute%!M </p> </div>'
      + '<div class="clockCard px-1"> <span>%S</span> <br> <p class="bg-amber clockFormat lead px-1 black"> Second%!S </p> </div>'))
  });


});
