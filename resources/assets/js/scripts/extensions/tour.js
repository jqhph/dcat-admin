/*=========================================================================================
	File Name: tour.js
	Description: tour
	----------------------------------------------------------------------------------------
	Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: Pixinvent
	Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(document).ready(function () {
  displayTour();
  $(window).resize(displayTour)
  var tour = new Shepherd.Tour({
    classes: 'shadow-md bg-purple-dark',
    scrollTo: true
  })

  // tour steps
  tour.addStep('step-1', {
    text: 'Here is page title.',
    attachTo: '.breadcrumbs-top .content-header-title bottom',
    buttons: [

      {
        text: "Skip",
        action: tour.complete
      },
      {
        text: 'Next',
        action: tour.next
      },
    ]
  });

  tour.addStep('step-2', {
    text: 'Check your notifications from here.',
    attachTo: '.dropdown-notification .icon-bell bottom',
    buttons: [

      {
        text: "Skip",
        action: tour.complete
      },

      {
        text: "previous",
        action: tour.back
      },
      {
        text: 'Next',
        action: tour.next
      },
    ]
  });

  tour.addStep('step-3', {
    text: 'Click here for user options.',
    attachTo: '.dropdown-user-link img bottom',
    buttons: [

      {
        text: "Skip",
        action: tour.complete
      },

      {
        text: "previous",
        action: tour.back
      },
      {
        text: 'Next',
        action: tour.next
      },
    ]
  });

  tour.addStep('step-4', {
    text: 'Buy this awesomeness at affordable price!',
    attachTo: '.buy-now bottom',
    buttons: [

      {
        text: "previous",
        action: tour.back
      },

      {
        text: "Finish",
        action: tour.complete
      },
    ]
  });

  // function to remove tour on small screen
  function displayTour() {
    window.resizeEvt;
    if ($(window).width() > 576) {
      $('#tour').on("click", function () {
        clearTimeout(window.resizeEvt);
        tour.start();
      })
    }
    else {
      $('#tour').on("click", function () {
        clearTimeout(window.resizeEvt);
        tour.cancel()
        window.resizeEvt = setTimeout(function () {
          alert("Tour only works for large screens!");
        }, 250);;
      })
    }
  }

});
