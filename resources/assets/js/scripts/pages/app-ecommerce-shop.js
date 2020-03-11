/*=========================================================================================
    File Name: app-ecommerce-shop.js
    Description: Ecommerce Shop
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {
  "use strict";

  // RTL Support
  var direction = 'ltr';
  if ($('html').data('textdirection') == 'rtl') {
    direction = 'rtl';
  }

  var sidebarShop = $(".sidebar-shop"),
    shopOverlay = $(".shop-content-overlay"),
    sidebarToggler = $(".shop-sidebar-toggler"),
    priceFilter = $(".price-options"),
    gridViewBtn = $(".grid-view-btn"),
    listViewBtn = $(".list-view-btn"),
    ecommerceProducts = $("#ecommerce-products"),
    cart = $(".cart"),
    wishlist = $(".wishlist");


  // show sidebar
  sidebarToggler.on("click", function () {
    sidebarShop.toggleClass("show");
    shopOverlay.toggleClass("show");
  });

  // remove sidebar
  $(".shop-content-overlay, .sidebar-close-icon").on("click", function () {
    sidebarShop.removeClass("show");
    shopOverlay.removeClass("show");
  })

  //price slider
  var slider = document.getElementById("price-slider");
  if (slider) {
    noUiSlider.create(slider, {
      start: [51, 5000],
      direction: direction,
      connect: true,
      tooltips: [true, true],
      format: wNumb({
        decimals: 0,
      }),
      range: {
        "min": 51,
        "max": 5000
      }
    });
  }
  // for select in ecommerce header
  if (priceFilter.length > 0) {
    priceFilter.select2({
      minimumResultsForSearch: -1,
      dropdownAutoWidth: true,
      width: 'auto'
    });
  }

  /***** CHANGE VIEW *****/
  // Grid View
  gridViewBtn.on("click", function () {
    ecommerceProducts.removeClass("list-view").addClass("grid-view");
    listViewBtn.removeClass("active");
    gridViewBtn.addClass("active");
  });

  // List View
  listViewBtn.on("click", function () {
    ecommerceProducts.removeClass("grid-view").addClass("list-view");
    gridViewBtn.removeClass("active");
    listViewBtn.addClass("active");
  });

  // For View in cart
  cart.on("click", function () {
    var $this = $(this),
      addToCart = $this.find(".add-to-cart"),
      viewInCart = $this.find(".view-in-cart");
    if (addToCart.is(':visible')) {
      addToCart.addClass("d-none");
      viewInCart.addClass("d-inline-block");
    }
    else {
      var href = viewInCart.attr('href');
      window.location.href = href;
    }
  });

  $(".view-in-cart").on('click', function (e) {
    e.preventDefault();
  });

  // For Wishlist Icon
  wishlist.on("click", function () {
    var $this = $(this)
    $this.find("i").toggleClass("fa-heart-o fa-heart")
    $this.toggleClass("added");
  })

  // Checkout Wizard
  var checkoutWizard = $(".checkout-tab-steps"),
    checkoutValidation = checkoutWizard.show();
  if (checkoutWizard.length > 0) {
    $(checkoutWizard).steps({
      headerTag: "h6",
      bodyTag: "fieldset",
      transitionEffect: "fade",
      titleTemplate: '<span class="step">#index#</span> #title#',
      enablePagination: false,
      onStepChanging: function (event, currentIndex, newIndex) {
        // allows to go back to previous step if form is
        if (currentIndex > newIndex) {
          return true;
        }
        // Needed in some cases if the user went back (clean up)
        if (currentIndex < newIndex) {
          // To remove error styles
          checkoutValidation.find(".body:eq(" + newIndex + ") label.error").remove();
          checkoutValidation.find(".body:eq(" + newIndex + ") .error").removeClass("error");
        }
        // check for valid details and show notification accordingly
        if (currentIndex === 1 && Number($(".form-control.required").val().length) < 1) {
          toastr.warning('Error', 'Please Enter Valid Details', { "positionClass": "toast-bottom-right" });
        }
        checkoutValidation.validate().settings.ignore = ":disabled,:hidden";
        return checkoutValidation.valid();
      },
    });
    // to move to next step on place order and save address click
    $(".place-order, .delivery-address").on("click", function () {
      $(".checkout-tab-steps").steps("next", {});
    });
    // check if user has entered valid cvv
    $(".btn-cvv").on("click", function () {
      if ($(".input-cvv").val().length == 3) {
        toastr.success('Success', 'Payment received Successfully', { "positionClass": "toast-bottom-right" });
      }
      else {
        toastr.warning('Error', 'Please Enter Valid Details', { "positionClass": "toast-bottom-right" });
      }
    })
  }

  // checkout quantity counter
  var quantityCounter = $(".quantity-counter"),
    CounterMin = 1,
    CounterMax = 10;
  if (quantityCounter.length > 0) {
    quantityCounter.TouchSpin({
      min: CounterMin,
      max: CounterMax
    }).on('touchspin.on.startdownspin', function () {
      var $this = $(this);
      $('.bootstrap-touchspin-up').removeClass("disabled-max-min");
      if ($this.val() == 1) {
        $(this).siblings().find('.bootstrap-touchspin-down').addClass("disabled-max-min");
      }
    }).on('touchspin.on.startupspin', function () {
      var $this = $(this);
      $('.bootstrap-touchspin-down').removeClass("disabled-max-min");
      if ($this.val() == 10) {
        $(this).siblings().find('.bootstrap-touchspin-up').addClass("disabled-max-min");
      }
    });
  }

  // remove items from wishlist page
  $(".remove-wishlist , .move-cart").on("click", function () {
    $(this).closest(".ecommerce-card").remove();
  })
})
// on window resize hide sidebar
$(window).on("resize", function () {
  if ($(window).outerWidth() >= 991) {
    $(".sidebar-shop").removeClass("show");
    $(".shop-content-overlay").removeClass("show");
  }
});
