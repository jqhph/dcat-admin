/*=========================================================================================
    File Name: swiper.js
    Description: swiper plugin
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {
  //initialize swiper when document ready

  // default
  var mySwiper = new Swiper('.swiper-default');

  // navigation
  var mySwiper1 = new Swiper('.swiper-navigations', {
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // pagination
  var mySwiper2 = new Swiper('.swiper-paginations', {
    pagination: {
      el: '.swiper-pagination',
    },
  });

  // progress
  var mySwiper3 = new Swiper('.swiper-progress', {
    pagination: {
      el: '.swiper-pagination',
      type: 'progressbar',
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // multiple
  var mySwiper4 = new Swiper('.swiper-multiple', {
    slidesPerView: 3,
    spaceBetween: 30,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });

  // multi row
  var mySwiper5 = new Swiper('.swiper-multi-row', {
    slidesPerView: 3,
    slidesPerColumn: 2,
    spaceBetween: 30,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });

  // centered slides option-1
  var mySwiperOpt1 = new Swiper('.swiper-centered-slides', {
    slidesPerView: 'auto',
    centeredSlides: true,
    spaceBetween: 30,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // centered slides option-2

  var swiperLength = $(".swiper-slide").length;
  if (swiperLength) {
    swiperLength = Math.floor(swiperLength / 2)
  }

  var mySwiperOpt2 = new Swiper('.swiper-centered-slides-2', {
    slidesPerView: 'auto',
    initialSlide: swiperLength,
    centeredSlides: true,
    spaceBetween: 30,
    slideToClickedSlide: true,
  });
  activeSlide(swiperLength);

  // Active slide change on swipe
  mySwiper.on('slideChange', function () {
    activeSlide(mySwiper.realIndex);
  });

  //add class active content of active slide
  function activeSlide(index) {
    var slideEl = mySwiper.slides[index]
    var slideId = $(slideEl).attr('id');
    $(".wrapper-content").removeClass("active");
    $("[data-faq=" + slideId + "]").addClass('active')
  };

  // fade effect
  var mySwiper7 = new Swiper('.swiper-fade-effect', {
    spaceBetween: 30,
    effect: 'fade',
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // cube effect
  var mySwiper8 = new Swiper('.swiper-cube-effect', {
    effect: 'cube',
    grabCursor: true,
    cubeEffect: {
      shadow: true,
      slideShadows: true,
      shadowOffset: 20,
      shadowScale: 0.94,
    },
    pagination: {
      el: '.swiper-pagination',
    },
  });

  // coverflow effect
  var mySwiper9 = new Swiper('.swiper-coverflow', {
    effect: 'coverflow',
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 'auto',
    coverflowEffect: {
      rotate: 50,
      stretch: 0,
      depth: 100,
      modifier: 1,
      slideShadows: true,
    },
    pagination: {
      el: '.swiper-pagination',
    },
  });

  // autoplay
  var mySwiper10 = new Swiper('.swiper-autoplay', {
    spaceBetween: 30,
    centeredSlides: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // gallery
  var galleryThumbs = new Swiper('.gallery-thumbs', {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesVisibility: true,
    watchSlidesProgress: true,
  });
  var galleryTop = new Swiper('.gallery-top', {
    spaceBetween: 10,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    thumbs: {
      swiper: galleryThumbs
    }
  });

  // parallax
  var mySwiper12 = new Swiper('.swiper-parallax', {
    speed: 600,
    parallax: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // lazy loading
  var mySwiper13 = new Swiper('.swiper-lazy-loading', {
    // Enable lazy loading
    lazy: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // Responsive Breakpoints
  var mySwiper14 = new Swiper('.swiper-responsive-breakpoints', {
    slidesPerView: 5,
    spaceBetween: 50,
    // init: false,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    breakpoints: {
      1024: {
        slidesPerView: 4,
        spaceBetween: 40,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
      640: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      320: {
        slidesPerView: 1,
        spaceBetween: 10,
      }
    }
  });

  // virtual slides
  var appendNumber = 600;
  var prependNumber = 1;
  var mySwiper15 = new Swiper('.swiper-virtual', {

    slidesPerView: 3,
    centeredSlides: true,
    spaceBetween: 30,
    pagination: {
      el: '.swiper-pagination',
      type: 'fraction',
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    virtual: {
      slides: (function () {
        var slides = [];
        for (var i = 0; i < 600; i += 1) {
          slides.push('Slide ' + (i + 1));
        }
        return slides;
      }()),
    },
  });
  $('.slide-1').on('click', function (e) {
    e.preventDefault();
    mySwiper15.slideTo(0, 0);
  });
  $('.slide-250').on('click', function (e) {
    e.preventDefault();
    mySwiper15.slideTo(249, 0);
  });
  $('.slide-500').on('click', function (e) {
    e.preventDefault();
    mySwiper15.slideTo(499, 0);
  });
  $('.prepend-2-slides').on('click', function (e) {
    e.preventDefault();
    mySwiper15.virtual.prependSlide([
      'Slide ' + (--prependNumber),
      'Slide ' + (--prependNumber)
    ]);
  });
  $('.append-slide').on('click', function (e) {
    e.preventDefault();
    mySwiper15.virtual.appendSlide('Slide ' + (++appendNumber));
  });
});
