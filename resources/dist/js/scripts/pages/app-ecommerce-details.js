//  File Name: app-ecommerce-details.js
//  Description: App Ecommerce Details js.
//  ----------------------------------------------------------------------------------------------
//  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
//  Author: PIXINVENT
//  Author URL: http://www.themeforest.net/user/pixinvent
// ================================================================================================
$(document).ready(function () {
  var mySwiper14 = new Swiper('.swiper-responsive-breakpoints', {
    slidesPerView: 5,
    spaceBetween: 55,
    // init: false,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      1600: {
        slidesPerView: 4,
        spaceBetween: 55,
      },
      1300: {
        slidesPerView: 3,
        spaceBetween: 55,
      },
      900: {
        slidesPerView: 2,
        spaceBetween: 55,
      },
      768: {
        slidesPerView: 1,
        spaceBetween: 55,
      }
    }
  });
  // product color options
  $(".product-color-options li").on("click", function () {
    $this = $(this);
    $this.addClass('selected').siblings().removeClass('selected');
  })
})
