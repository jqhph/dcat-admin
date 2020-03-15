/*=========================================================================================
  File Name: customizer.js
  Description: Template customizer js.
  ----------------------------------------------------------------------------------------
  Item Name:  Vusax - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

(function (window, document, $) {
  'use strict';
  // main menu active gradient colors object
  var themeColor = {
    "theme-primary": "linear-gradient(118deg, #7367f0, rgba(115, 103, 240, 0.7))",
    "theme-success": "linear-gradient(118deg, #28c76f, rgba(40, 199, 111, 0.7))",
    "theme-danger": "linear-gradient(118deg, #ea5455, rgba(234, 84, 85, 0.7))",
    "theme-info": "linear-gradient(118deg, #00cfe8, rgba(0, 207, 232, 0.7))",
    "theme-warning": "linear-gradient(118deg, #ff9f43, rgba(255, 159, 67, 0.7))",
    "theme-dark": "linear-gradient(118deg, #1e1e1e, rgba(30, 30, 30, 0.7))"
  }
  // main menu active box shadow object
  var themeBoxShadow = {
    "theme-primary": "0 0 10px 1px rgba(115, 103, 240, 0.7)",
    "theme-success": "0 0 10px 1px rgba(40, 199, 111, 0.7)",
    "theme-danger": "0 0 10px 1px rgba(234, 84, 85, 0.7)",
    "theme-info": "0 0 10px 1px rgba(0, 207, 232, 0.7)",
    "theme-warning": "0 0 10px 1px rgba(255, 159, 67, 0.7)",
    "theme-dark": "0 0 10px 1px rgba(30, 30, 30, 0.7)"
  }
  // colors for navbar header text of main menu
  var currentColor = {
    "theme-default": "#fff",
    "theme-primary": "#7367f0",
    "theme-success": "#28c76f",
    "theme-danger": "#ea5455",
    "theme-info": "#00cfe8",
    "theme-warning": "#ff9f43",
    "theme-dark": "#adb5bd"
  }
  // Brand Logo Poisitons
  var LogoPosition = {
    "theme-primary": "-65px -54px",
    "theme-success": "-120px -10px",
    "theme-danger": "-10px -10px",
    "theme-info": "-10px -54px",
    "theme-warning": "-120px -54px",
    "theme-dark": "-65px -10px"
  }

  var body = $("body"),
    appContent = $(".app-content"),
    mainMenu = $(".main-menu"),
    menuContent = $(".menu-content"),
    footer = $(".footer"),
    navbar = $(".header-navbar"),
    horizontalNavbar = $(".horizontal-menu-wrapper .header-navbar"),
    navBarShadow = $(".header-navbar-shadow"),
    toggleIcon = $(".toggle-icon"),
    collapseSidebar = $("#collapse-sidebar-switch"),
    customizer = $(".customizer"),
    brandLogo = $(".brand-logo");

  // Customizer toggle & close button click events  [Remove customizer code from production]
  $('.customizer-toggle').on('click', function (e) {
    e.preventDefault();
    $(customizer).toggleClass('open');
  });
  $('.customizer-close').on('click', function () {
    $(customizer).removeClass('open');
  });

  // perfect scrollbar for customizer
  if ($('.customizer-content').length > 0) {
    var customizer_content = new PerfectScrollbar('.customizer-content');
  }

  /***** Theme Colors Options *****/
  $(document).on("click", "#customizer-theme-colors .color-box", function () {
    var $this = $(this);
    $this.siblings().removeClass('selected');
    $this.addClass("selected");
    var selectedColor = $(this).data("color"),
      changeColor = themeColor[selectedColor],
      selectedShadow = themeBoxShadow[selectedColor],
      selectedTextColor = currentColor[selectedColor],
      selectedLogo = LogoPosition[selectedColor];

    // main-menu
    if (body.data('menu') == "horizontal-menu") {
      if (horizontalNavbar.find("li.sidebar-group-active:not(.dropdown-submenu)").length) {
        horizontalNavbar.find("li.sidebar-group-active:not(.dropdown-submenu)  > a").css(
          {
            "background": changeColor,
            "box-shadow": selectedShadow,
            "border-color": selectedTextColor
          }
        );
        horizontalNavbar.find("li.sidebar-group-active:not(.dropdown-submenu)  > ul li.active > a").css(
          {
            "color": selectedTextColor
          }
        );
      }
    }
    else {
      if (mainMenu.find("li.active").length) {
        mainMenu.find("li.active >a").css(
          {
            "background": changeColor,
            "box-shadow": selectedShadow
          }
        );
      }
      else if ($(".main-menu-content").find("li.sidebar-group-active").length) {
        $(".main-menu-content").find("li.sidebar-group-active > a").css(
          {
            "background": changeColor,
            "box-shadow": selectedShadow
          }
        );
      }
      else {
        mainMenu.find(".nav-item.active a").css(
          {
            "background": changeColor,
            "box-shadow": selectedShadow
          }
        );
      }
    }
    // Text with logo
    $(".brand-text").css("color", selectedTextColor);
    // toggle icon
    toggleIcon.removeClass("primary").css("color", selectedTextColor);
    // Changes logo color
    brandLogo.css("background-position", selectedLogo);
  });

  /***** Menu Layout type *****/
  $(".layout-name").on("click", function () {
    var $this = $(this);
    var currentLayout = $this.data("layout");
    body.removeClass("dark-layout semi-dark-layout").addClass(currentLayout);
    if (currentLayout === "") {
      mainMenu.removeClass("menu-dark").addClass("menu-light");
      navbar.removeClass("navbar-dark").addClass("navbar-light");
    }
    else {
      mainMenu.removeClass("menu-light").addClass("menu-dark");

    }
  });

  // checks right radio if layout type matches
  var layout = body.data("layout");
  $(".layout-name[data-layout='" + layout + "']").prop('checked', true);

  /***** Collapse menu switch *****/
  collapseSidebar.on("click", function () {
    $(".modern-nav-toggle").trigger("click");
    mainMenu.trigger('mouseleave');
  });

  // checks if main menu is collapsed by default
  if (body.hasClass("menu-collapsed")) {
    collapseSidebar.prop("checked", true);
  }
  else {
    collapseSidebar.prop("checked", false);
  }

  /***** Navbar Color Options *****/
  $("#customizer-navbar-colors .color-box").on("click", function () {
    var $this = $(this);
    $this.siblings().removeClass('selected');
    $this.addClass("selected");
    var navbarColor = $this.data("navbar-color");
    // changes navbar colors
    if (navbarColor) {
      $(".app-content > .header-navbar")
        .removeClass("bg-primary bg-success bg-danger bg-info bg-warning bg-dark")
        .addClass(navbarColor + " navbar-dark");
    }
    else {
      $(".app-content > .header-navbar")
        .removeClass("bg-primary bg-success bg-danger bg-info bg-warning bg-dark navbar-dark");
    }
    if (body.hasClass("dark-layout")) {
      navbar.addClass("navbar-dark")
    }
  })

  /***** Navbar Type *****/
  if (body.hasClass('horizontal-menu')) {
    // $('.collapse_menu').removeClass('d-none');

    $('#collapse-sidebar').addClass('d-none');

    $('.menu_type').removeClass('d-none');
    $('.navbar_type').addClass('d-none');

    // Hides hidden option in Horizontal layout
    $('.navbar-type #navbar-hidden').closest('fieldset').parent('div').css('display', 'none');
    // On Scroll navbar color on horizontal menu
    $(window).scroll(function () {
      if (body.hasClass('navbar-static')) {
        var scroll = $(window).scrollTop();
        if (scroll > 65) {
          $(".horizontal-menu .header-navbar.navbar-fixed").css({ "background": "#fff", "box-shadow": "0 4px 20px 0 rgba(0,0,0,.05)" });
          $(".horizontal-menu .horizontal-menu-wrapper.header-navbar").css("background", "#fff");
        }
        else {
          $(".horizontal-menu .header-navbar.navbar-fixed").css({ "background": "#f8f8f8", "box-shadow": "none" });
          $(".horizontal-menu .horizontal-menu-wrapper.header-navbar").css("background", "#fff");
        }
      }
    })
  }
  // Hides Navbar
  $("#navbar-hidden").on("click", function () {
    navbar.addClass("d-none");
    navBarShadow.addClass("d-none");
    body.removeClass("navbar-static navbar-floating navbar-sticky").addClass("navbar-hidden");
  });
  // changes to Static navbar
  $("#navbar-static").on("click", function () {
    if (body.hasClass('horizontal-menu')) {
      horizontalNavbar
        .removeClass("d-none floating-nav fixed-top navbar-fixed");
      body.removeClass("navbar-hidden navbar-floating navbar-sticky").addClass("navbar-static");
    }
    else {
      navBarShadow.addClass("d-none");
      navbar
        .removeClass("d-none floating-nav fixed-top")
        .addClass("static-top");
      body.removeClass("navbar-hidden navbar-floating navbar-sticky").addClass("navbar-static");
    }
  });
  // change to floating navbar
  $("#navbar-floating").on("click", function () {
    if (body.hasClass('horizontal-menu')) {
      horizontalNavbar
        .removeClass("d-none fixed-top static-top")
        .addClass("floating-nav");
      body.removeClass("navbar-static navbar-hidden navbar-sticky").addClass("navbar-floating");
    }
    else {
      navBarShadow.removeClass("d-none");
      navbar
        .removeClass("d-none static-top fixed-top")
        .addClass("floating-nav");
      body.removeClass("navbar-static navbar-hidden navbar-sticky").addClass("navbar-floating");
    }
  });
  // changes to Static navbar
  $("#navbar-sticky").on("click", function () {
    if (body.hasClass('horizontal-menu')) {
      horizontalNavbar
        .removeClass("d-none floating-nav static-top navbar-fixed")
        .addClass("fixed-top");
      body.removeClass("navbar-static navbar-floating navbar-hidden").addClass("navbar-sticky");
    }
    else {
      navBarShadow.addClass("d-none");
      navbar
        .removeClass("d-none floating-nav static-top")
        .addClass("fixed-top");
      body.removeClass("navbar-static navbar-floating navbar-hidden").addClass("navbar-fixed");
    }
  });

  /***** Footer Type *****/
  // Hides footer
  $("#footer-hidden").on("click", function () {
    footer.addClass("d-none");
    body.removeClass("footer-static fixed-footer").addClass("footer-hidden");
  });
  // changes to Static footer
  $("#footer-static").on("click", function () {
    body.removeClass("fixed-footer");
    footer.removeClass("d-none").addClass("footer-static");
    body.removeClass("footer-hidden fixed-footer").addClass("footer-static");
  });
  // changes to Sticky footer
  $("#footer-sticky").on("click", function () {
    body.removeClass("footer-static footer-hidden").addClass("fixed-footer");
    footer.removeClass("d-none footer-static");
  });

  /***** Hide Scroll To Top *****/
  $("#hide-scroll-top-switch").on("click", function () {
    var scrollTopBtn = $(".scroll-top")
    if ($(this).prop("checked")) {
      scrollTopBtn.addClass("d-none");
    }
    else {
      scrollTopBtn.removeClass("d-none");
    }
  });
})(window, document, jQuery);
