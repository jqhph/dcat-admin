/*=========================================================================================
  File Name: app.js
  Description: Template related app JS.
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

(function (window, document, $) {
  "use strict";
  var $html = $("html");
  var $body = $("body");
  var $danger = "#ea5455";
  var $primary = "#7367f0";
  var $textcolor = "#4e5154";

  $(window).on("load", function () {
    var rtl;
    var compactMenu = false; // Set it to true, if you want default menu to be compact

    if ($body.hasClass("menu-collapsed")) {
      compactMenu = true;
    }

    if ($("html").data("textdirection") == "rtl") {
      rtl = true;
    }

    setTimeout(function () {
      $html.removeClass("loading").addClass("loaded");
    }, 1200);

    $.app.menu.init(compactMenu);

    // Navigation configurations
    var config = {
      speed: 300 // set speed to expand / collpase menu
    };
    if ($.app.nav.initialized === false) {
      $.app.nav.init(config);
    }

    Unison.on("change", function (bp) {
      $.app.menu.change();
    });

    // Tooltip Initialization
    $('[data-toggle="tooltip"]').tooltip({
      container: "body"
    });

    // Top Navbars - Hide on Scroll
    if ($(".navbar-hide-on-scroll").length > 0) {
      $(".navbar-hide-on-scroll.fixed-top").headroom({
        offset: 205,
        tolerance: 5,
        classes: {
          // when element is initialised
          initial: "headroom",
          // when scrolling up
          pinned: "headroom--pinned-top",
          // when scrolling down
          unpinned: "headroom--unpinned-top"
        }
      });
      // Bottom Navbars - Hide on Scroll
      $(".navbar-hide-on-scroll.fixed-bottom").headroom({
        offset: 205,
        tolerance: 5,
        classes: {
          // when element is initialised
          initial: "headroom",
          // when scrolling up
          pinned: "headroom--pinned-bottom",
          // when scrolling down
          unpinned: "headroom--unpinned-bottom"
        }
      });
    }

    // Collapsible Card
    $('a[data-action="collapse"]').on("click", function (e) {
      e.preventDefault();
      $(this)
        .closest(".card")
        .children(".card-content")
        .collapse("toggle");
      // Adding bottom padding on card collapse
      $(this)
        .closest(".card")
        .children(".card-header")
        .css("padding-bottom", "1.5rem");
      $(this)
        .closest(".card")
        .find('[data-action="collapse"]')
        .toggleClass("rotate");
    });

    // Toggle fullscreen
    $('a[data-action="expand"]').on("click", function (e) {
      e.preventDefault();
      $(this)
        .closest(".card")
        .find('[data-action="expand"] i')
        .toggleClass("icon-maximize icon-minimize");
      $(this)
        .closest(".card")
        .toggleClass("card-fullscreen");
    });

    //  Notifications & messages scrollable
    $(".scrollable-container").each(function () {
      var scrollable_container = new PerfectScrollbar($(this)[0], {
        wheelPropagation: false
      });
    });

    // Reload Card
    $('a[data-action="reload"]').on("click", function () {
      var block_ele = $(this)
        .closest(".card")
        .find(".card-content");
      var reloadActionOverlay;
      if ($body.hasClass("dark-layout")) {
        var reloadActionOverlay = "#10163a";
      } else {
        var reloadActionOverlay = "#fff";
      }
      // Block Element
      block_ele.block({
        message: '<div class="feather icon-refresh-cw icon-spin font-medium-2 text-primary"></div>',
        timeout: 2000, //unblock after 2 seconds
        overlayCSS: {
          backgroundColor: reloadActionOverlay,
          cursor: "wait"
        },
        css: {
          border: 0,
          padding: 0,
          backgroundColor: "none"
        }
      });
    });

    // Close Card
    $('a[data-action="close"]').on("click", function () {
      $(this).closest(".card").removeClass().slideUp("fast");
    });

    // Match the height of each card in a row
    setTimeout(function () {
      $(".row.match-height").each(function () {
        $(this).find(".card").not(".card .card").matchHeight(); // Not .card .card prevents collapsible cards from taking height
      });
    }, 500);

    $('.card .heading-elements a[data-action="collapse"]').on(
      "click",
      function () {
        var $this = $(this),
          card = $this.closest(".card");
        var cardHeight;

        if (parseInt(card[0].style.height, 10) > 0) {
          cardHeight = card.css("height");
          card.css("height", "").attr("data-height", cardHeight);
        } else {
          if (card.data("height")) {
            cardHeight = card.data("height");
            card.css("height", cardHeight).attr("data-height", "");
          }
        }
      }
    );

    // Add sidebar group active class to active menu
    $(".main-menu-content").find("li.active").parents("li").addClass("sidebar-group-active");

    // Add open class to parent list item if subitem is active except compact menu
    var menuType = $body.data("menu");
    if (menuType != "horizontal-menu" && compactMenu === false) {
      $(".main-menu-content").find("li.active").parents("li").addClass("open");
    }
    if (menuType == "horizontal-menu") {
      $(".main-menu-content").find("li.active").parents("li:not(.nav-item)").addClass("open");
      $(".main-menu-content").find('li.active').closest('li.nav-item').addClass('sidebar-group-active open');
      // $(".main-menu-content")
      //   .find("li.active")
      //   .parents("li")
      //   .addClass("active");
    }

    //card heading actions buttons small screen support
    $(".heading-elements-toggle").on("click", function () {
      $(this)
        .next(".heading-elements")
        .toggleClass("visible");
    });

    //  Dynamic height for the chartjs div for the chart animations to work
    var chartjsDiv = $(".chartjs"),
      canvasHeight = chartjsDiv.children("canvas").attr("height"),
      mainMenu = $(".main-menu");
    chartjsDiv.css("height", canvasHeight);

    if ($body.hasClass("boxed-layout")) {
      if ($body.hasClass("vertical-overlay-menu")) {
        var menuWidth = mainMenu.width();
        var contentPosition = $(".app-content").position().left;
        var menuPositionAdjust = contentPosition - menuWidth;
        if ($body.hasClass("menu-flipped")) {
          mainMenu.css("right", menuPositionAdjust + "px");
        } else {
          mainMenu.css("left", menuPositionAdjust + "px");
        }
      }
    }

    //Custom File Input
    $(".custom-file input").change(function (e) {
      $(this)
        .next(".custom-file-label")
        .html(e.target.files[0].name);
    });

    /* Text Area Counter Set Start */

    $(".char-textarea").on("keyup", function (event) {
      checkTextAreaMaxLength(this, event);
      // to later change text color in dark layout
      $(this).addClass("active");
    });

    /*
    Checks the MaxLength of the Textarea
    -----------------------------------------------------
    @prerequisite:  textBox = textarea dom element
            e = textarea event
                    length = Max length of characters
    */
    function checkTextAreaMaxLength(textBox, e) {
      var maxLength = parseInt($(textBox).data("length")),
        counterValue = $(".counter-value"),
        charTextarea = $(".char-textarea");

      if (!checkSpecialKeys(e)) {
        if (textBox.value.length < maxLength - 1)
          textBox.value = textBox.value.substring(0, maxLength);
      }
      $(".char-count").html(textBox.value.length);

      if (textBox.value.length > maxLength) {
        counterValue.css("background-color", $danger);
        charTextarea.css("color", $danger);
        // to change text color after limit is maxedout out
        charTextarea.addClass("max-limit");
      } else {
        counterValue.css("background-color", $primary);
        charTextarea.css("color", $textcolor);
        charTextarea.removeClass("max-limit");
      }

      return true;
    }
    /*
    Checks if the keyCode pressed is inside special chars
    -------------------------------------------------------
    @prerequisite:  e = e.keyCode object for the key pressed
    */
    function checkSpecialKeys(e) {
      if (
        e.keyCode != 8 &&
        e.keyCode != 46 &&
        e.keyCode != 37 &&
        e.keyCode != 38 &&
        e.keyCode != 39 &&
        e.keyCode != 40
      )
        return false;
      else return true;
    }

    $(".content-overlay").on("click", function () {
      $(".search-list").removeClass("show");
      $(".app-content").removeClass("show-overlay");
      $(".bookmark-wrapper .bookmark-input").removeClass("show");
    });

    // To show shadow in main menu when menu scrolls
    var container = document.getElementsByClassName("main-menu-content");
    if (container.length > 0) {
      container[0].addEventListener("ps-scroll-y", function () {
        if (
          $(this)
          .find(".ps__thumb-y")
          .position().top > 0
        ) {
          $(".shadow-bottom").css("display", "block");
        } else {
          $(".shadow-bottom").css("display", "none");
        }
      });
    }
  });

  // Hide overlay menu on content overlay click on small screens
  $(document).on("click", ".sidenav-overlay", function (e) {
    // Hide menu
    $.app.menu.hide();
    return false;
  });

  // Execute below code only if we find hammer js for touch swipe feature on small screen
  if (typeof Hammer !== "undefined") {
    // Swipe menu gesture
    var swipeInElement = document.querySelector(".drag-target");

    if ($(swipeInElement).length > 0) {
      var swipeInMenu = new Hammer(swipeInElement);

      swipeInMenu.on("panright", function (ev) {
        if ($body.hasClass("vertical-overlay-menu")) {
          $.app.menu.open();
          return false;
        }
      });
    }

    // menu swipe out gesture
    setTimeout(function () {
      var swipeOutElement = document.querySelector(".main-menu");
      var swipeOutMenu;

      if ($(swipeOutElement).length > 0) {
        swipeOutMenu = new Hammer(swipeOutElement);

        swipeOutMenu.get("pan").set({
          direction: Hammer.DIRECTION_ALL,
          threshold: 100
        });

        swipeOutMenu.on("panleft", function (ev) {
          if ($body.hasClass("vertical-overlay-menu")) {
            $.app.menu.hide();
            return false;
          }
        });
      }
    }, 300);

    // menu overlay swipe out gestrue
    var swipeOutOverlayElement = document.querySelector(".sidenav-overlay");

    if ($(swipeOutOverlayElement).length > 0) {
      var swipeOutOverlayMenu = new Hammer(swipeOutOverlayElement);

      swipeOutOverlayMenu.on("panleft", function (ev) {
        if ($body.hasClass("vertical-overlay-menu")) {
          $.app.menu.hide();
          return false;
        }
      });
    }
  }

  $(document).on("click", ".menu-toggle, .modern-nav-toggle", function (e) {
    e.preventDefault();

    // Toggle menu
    $.app.menu.toggle();

    setTimeout(function () {
      $(window).trigger("resize");
    }, 200);

    if ($("#collapse-sidebar-switch").length > 0) {
      setTimeout(function () {
        if ($body.hasClass("menu-expanded") || $body.hasClass("menu-open")) {
          $("#collapse-sidebar-switch").prop("checked", false);
        } else {
          $("#collapse-sidebar-switch").prop("checked", true);
        }
      }, 50);
    }

    // Hides dropdown on click of menu toggle
    // $('[data-toggle="dropdown"]').dropdown('hide');

    // Hides collapse dropdown on click of menu toggle
    if (
      $(".vertical-overlay-menu .navbar-with-menu .navbar-container .navbar-collapse").hasClass("show")
    ) {
      $(".vertical-overlay-menu .navbar-with-menu .navbar-container .navbar-collapse").removeClass("show");
    }

    return false;
  });

  // Add Children Class
  $(".navigation")
    .find("li")
    .has("ul")
    .addClass("has-sub");

  $(".carousel").carousel({
    interval: 2000
  });

  // Page full screen
  $(".nav-link-expand").on("click", function (e) {
    if (typeof screenfull != "undefined") {
      if (screenfull.isEnabled) {
        screenfull.toggle();
      }
    }
  });
  if (typeof screenfull != "undefined") {
    if (screenfull.isEnabled) {
      $(document).on(screenfull.raw.fullscreenchange, function () {
        if (screenfull.isFullscreen) {
          $(".nav-link-expand")
            .find("i")
            .toggleClass("icon-minimize icon-maximize");
          $("html").addClass("full-screen");
        } else {
          $(".nav-link-expand")
            .find("i")
            .toggleClass("icon-maximize icon-minimize");
          $("html").removeClass("full-screen");
        }
      });
    }
  }
  $(document).ready(function () {
    /**********************************
     *   Form Wizard Step Icon
     **********************************/
    $(".step-icon").each(function () {
      var $this = $(this);
      if ($this.siblings("span.step").length > 0) {
        $this.siblings("span.step").empty();
        $(this).appendTo($(this).siblings("span.step"));
      }
    });
  });

  // Update manual scroller when window is resized
  $(window).resize(function () {
    $.app.menu.manualScroller.updateHeight();
  });

  $("#sidebar-page-navigation").on("click", "a.nav-link", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $this = $(this),
      href = $this.attr("href");
    var offset = $(href).offset();
    var scrollto = offset.top - 80; // minus fixed header height
    $("html, body").animate({
        scrollTop: scrollto
      },
      0
    );
    setTimeout(function () {
      $this
        .parent(".nav-item")
        .siblings(".nav-item")
        .children(".nav-link")
        .removeClass("active");
      $this.addClass("active");
    }, 100);
  });

  // main menu internationalization

  // init i18n and load language file
  i18next.use(window.i18nextXHRBackend).init({
      debug: false,
      fallbackLng: "en",
      backend: {
        loadPath: "data/locales/{{lng}}.json"
      },
      returnObjects: true
    },
    function (err, t) {
      // resources have been loaded
      jqueryI18next.init(i18next, $);
    }
  );

  // change language according to data-language of dropdown item
  $(".dropdown-language .dropdown-item").on("click", function () {
    var $this = $(this);
    $this.siblings(".selected").removeClass("selected");
    $this.addClass("selected");
    var selectedLang = $this.text();
    var selectedFlag = $this.find(".flag-icon").attr("class");
    $("#dropdown-flag .selected-language").text(selectedLang);
    $("#dropdown-flag .flag-icon")
      .removeClass()
      .addClass(selectedFlag);
    var currentLanguage = $this.data("language");
    i18next.changeLanguage(currentLanguage, function (err, t) {
      $(".main-menu, .horizontal-menu-wrapper").localize();
    });
  });

  /********************* Bookmark & Search ***********************/
  // This variable is used for mouseenter and mouseleave events of search list
  var $filename = $(".search-input input").data("search"),
    bookmarkWrapper = $(".bookmark-wrapper"),
    bookmarkStar = $(".bookmark-wrapper .bookmark-star"),
    bookmarkInput = $(".bookmark-wrapper .bookmark-input"),
    navLinkSearch = $(".nav-link-search"),
    searchInput = $(".search-input"),
    searchInputInputfield = $(".search-input input"),
    searchList = $(".search-input .search-list"),
    appContent = $(".app-content"),
    bookmarkSearchList = $(".bookmark-input .search-list");

  // Bookmark icon click
  bookmarkStar.on("click", function (e) {
    e.stopPropagation();
    bookmarkInput.toggleClass("show");
    bookmarkInput.find("input").val("");
    bookmarkInput.find("input").blur();
    bookmarkInput.find("input").focus();
    bookmarkWrapper.find(".search-list").addClass("show");

    var arrList = $("ul.nav.navbar-nav.bookmark-icons li"),
      $arrList = "",
      $activeItemClass = "";

    $("ul.search-list li").remove();

    for (var i = 0; i < arrList.length; i++) {
      if (i === 0) {
        $activeItemClass = "current_item";
      } else {
        $activeItemClass = "";
      }
      $arrList +=
        '<li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer ' +
        $activeItemClass +
        '">' +
        '<a class="d-flex align-items-center justify-content-between w-100" href=' +
        arrList[i].firstChild.href +
        ">" +
        '<div class="d-flex justify-content-start align-items-center">' +
        '<span class="mr-75 ' +
        arrList[i].firstChild.firstChild.className +
        '"  data-icon="' +
        arrList[i].firstChild.firstChild.className +
        '"></span>' +
        "<span>" +
        arrList[i].firstChild.dataset.originalTitle +
        "</span>" +
        "</div>" +
        '<span class="float-right bookmark-icon feather icon-star warning"></span>' +
        "</a>" +
        "</li>";
    }
    $("ul.search-list").append($arrList);
  });

  // Navigation Search area Open
  navLinkSearch.on("click", function () {
    var $this = $(this);
    var searchInput = $(this).parent(".nav-search").find(".search-input");
    searchInput.addClass("open");
    searchInputInputfield.focus();
    searchList.find("li").remove();
    bookmarkInput.removeClass("show");
  });

  // Navigation Search area Close
  $(".search-input-close i").on("click", function () {
    var $this = $(this),
      searchInput = $(this).closest(".search-input");
    if (searchInput.hasClass("open")) {
      searchInput.removeClass("open");
      searchInputInputfield.val("");
      searchInputInputfield.blur();
      searchList.removeClass("show");
      appContent.removeClass("show-overlay");
    }
  });

  // Filter
  if ($('.search-list-main').length) {
    var searchListMain = new PerfectScrollbar(".search-list-main", {
      wheelPropagation: false
    });
  }
  if ($('.search-list-bookmark').length) {
    var searchListBookmark = new PerfectScrollbar(".search-list-bookmark", {
      wheelPropagation: false
    });
  }
  // update Perfect Scrollbar on hover
  $(".search-list-main").mouseenter(function () {
    searchListMain.update();
  });

  searchInputInputfield.on("keyup", function (e) {
    $(this).closest(".search-list").addClass("show");
    if (e.keyCode !== 38 && e.keyCode !== 40 && e.keyCode !== 13) {
      if (e.keyCode == 27) {
        appContent.removeClass("show-overlay");
        bookmarkInput.find("input").val("");
        bookmarkInput.find("input").blur();
        searchInputInputfield.val("");
        searchInputInputfield.blur();
        searchInput.removeClass("open");
        if (searchInput.hasClass("show")) {
          $(this).removeClass("show");
          searchInput.removeClass("show");
        }
      }

      // Define variables
      var value = $(this).val().toLowerCase(), //get values of input on keyup
        activeClass = "",
        bookmark = false,
        liList = $("ul.search-list li"); // get all the list items of the search
      liList.remove();
      // To check if current is bookmark input
      if (
        $(this)
        .parent()
        .hasClass("bookmark-input")
      ) {
        bookmark = true;
      }

      // If input value is blank
      if (value != "") {
        appContent.addClass("show-overlay");

        // condition for bookmark and search input click
        if (bookmarkInput.focus()) {
          bookmarkSearchList.addClass("show");
        } else {
          searchList.addClass("show");
          bookmarkSearchList.removeClass("show");
        }
        if (bookmark === false) {
          searchList.addClass("show");
          bookmarkSearchList.removeClass("show");
        }

        var $startList = "",
          $otherList = "",
          $htmlList = "",
          $bookmarkhtmlList = "",
          $pageList = '<li class=" d-flex align-items-center">' +
          '<a href="#" class="pb-25">' +
          '<h6 class="text-primary mb-0">Pages</h6>' +
          '</a>' +
          '</li>',
          $activeItemClass = "",
          $bookmarkIcon = "",
          $defaultList = "",
          a = 0;

        // getting json data from file for search results
        $.getJSON("data/" + $filename + ".json", function (
          data
        ) {
          for (var i = 0; i < data.listItems.length; i++) {
            // if current is bookmark then give class to star icon
            if (bookmark === true) {
              activeClass = ""; // resetting active bookmark class
              var arrList = $("ul.nav.navbar-nav.bookmark-icons li"),
                $arrList = "";
              // Loop to check if current seach value match with the bookmarks already there in navbar
              for (var j = 0; j < arrList.length; j++) {
                if (
                  data.listItems[i].name ===
                  arrList[j].firstChild.dataset.originalTitle
                ) {
                  activeClass = " warning";
                  break;
                } else {
                  activeClass = "";
                }
              }
              $bookmarkIcon =
                '<span class="float-right bookmark-icon feather icon-star' +
                activeClass +
                '"></span>';
            }
            // Search list item start with entered letters and create list
            if (
              data.listItems[i].name.toLowerCase().indexOf(value) == 0 &&
              a < 5
            ) {
              if (a === 0) {
                $activeItemClass = "current_item";
              } else {
                $activeItemClass = "";
              }
              $startList +=
                '<li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer ' +
                $activeItemClass +
                '">' +
                '<a class="d-flex align-items-center justify-content-between w-100" href=' +
                data.listItems[i].url +
                ">" +
                '<div class="d-flex justify-content-start align-items-center">' +
                '<span class="mr-75 ' +
                data.listItems[i].icon +
                '" data-icon="' +
                data.listItems[i].icon +
                '"></span>' +
                "<span>" +
                data.listItems[i].name +
                "</span>" +
                "</div>" +
                $bookmarkIcon +
                "</a>" +
                "</li>";
              a++;
            }
          }
          for (var i = 0; i < data.listItems.length; i++) {
            if (bookmark === true) {
              activeClass = ""; // resetting active bookmark class
              var arrList = $("ul.nav.navbar-nav.bookmark-icons li"),
                $arrList = "";
              // Loop to check if current seach value match with the bookmarks already there in navbar
              for (var j = 0; j < arrList.length; j++) {
                if (
                  data.listItems[i].name ===
                  arrList[j].firstChild.dataset.originalTitle
                ) {
                  activeClass = " warning";
                } else {
                  activeClass = "";
                }
              }
              $bookmarkIcon =
                '<span class="float-right bookmark-icon feather icon-star' +
                activeClass +
                '"></span>';
            }
            // Search list item not start with letters and create list
            if (
              !(data.listItems[i].name.toLowerCase().indexOf(value) == 0) &&
              data.listItems[i].name.toLowerCase().indexOf(value) > -1 &&
              a < 5
            ) {
              if (a === 0) {
                $activeItemClass = "current_item";
              } else {
                $activeItemClass = "";
              }
              $otherList +=
                '<li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer ' +
                $activeItemClass +
                '">' +
                '<a class="d-flex align-items-center justify-content-between w-100" href=' +
                data.listItems[i].url +
                ">" +
                '<div class="d-flex justify-content-start align-items-center">' +
                '<span class="mr-75 ' +
                data.listItems[i].icon +
                '" data-icon="' +
                data.listItems[i].icon +
                '"></span>' +
                "<span>" +
                data.listItems[i].name +
                "</span>" +
                "</div>" +
                $bookmarkIcon +
                "</a>" +
                "</li>";
              a++;
            }
          }
          $defaultList = $(".main-search-list-defaultlist").html();
          if ($startList == "" && $otherList == "") {
            $otherList = $(".main-search-list-defaultlist-other-list").html();
          }
          // concatinating startlist, otherlist, defalutlist with pagelist
          $htmlList = $pageList.concat($startList, $otherList, $defaultList);
          $("ul.search-list").html($htmlList);
          // concatinating otherlist with startlist
          $bookmarkhtmlList = $startList.concat($otherList);
          $("ul.search-list-bookmark").html($bookmarkhtmlList);
        });
      } else {
        if (bookmark === true) {
          var arrList = $("ul.nav.navbar-nav.bookmark-iconss li"),
            $arrList = "";
          for (var i = 0; i < arrList.length; i++) {
            if (i === 0) {
              $activeItemClass = "current_item";
            } else {
              $activeItemClass = "";
            }
            $arrList +=
              '<li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer">' +
              '<a class="d-flex align-items-center justify-content-between w-100" href=' +
              arrList[i].firstChild.href +
              ">" +
              '<div class="d-flex justify-content-start align-items-center">' +
              '<span class="mr-75 ' +
              arrList[i].firstChild.firstChild.className +
              '"  data-icon="' +
              arrList[i].firstChild.firstChild.className +
              '"></span>' +
              "<span>" +
              arrList[i].firstChild.dataset.originalTitle +
              "</span>" +
              "</div>" +
              '<span class="float-right bookmark-icon feather icon-star warning"></span>' +
              "</a>" +
              "</li>";
          }
          $("ul.search-list").append($arrList);
        } else {
          // if search input blank, hide overlay
          if (appContent.hasClass("show-overlay")) {
            appContent.removeClass("show-overlay");
          }
          // If filter box is empty
          if (searchList.hasClass("show")) {
            searchList.removeClass("show");
          }
        }
      }
    }
  });

  // Add class on hover of the list
  $(document).on("mouseenter", ".search-list li", function (e) {
    $(this)
      .siblings()
      .removeClass("current_item");
    $(this).addClass("current_item");
  });
  $(document).on("click", ".search-list li", function (e) {
    e.stopPropagation();
  });

  $("html").on("click", function ($this) {
    if (!$($this.target).hasClass("bookmark-icon")) {
      if (bookmarkSearchList.hasClass("show")) {
        bookmarkSearchList.removeClass("show");
      }
      if (bookmarkInput.hasClass("show")) {
        bookmarkInput.removeClass("show");
      }
    }
  });

  // Prevent closing bookmark dropdown on input textbox click
  $(document).on("click", ".bookmark-input input", function (e) {
    bookmarkInput.addClass("show");
    bookmarkSearchList.addClass("show");
  });

  // Favorite star click
  $(document).on("click", ".bookmark-input .search-list .bookmark-icon", function (e) {
    e.stopPropagation();
    if ($(this).hasClass("warning")) {
      $(this).removeClass("warning");
      var arrList = $("ul.nav.navbar-nav.bookmark-icons li");
      for (var i = 0; i < arrList.length; i++) {
        if (
          arrList[i].firstChild.dataset.originalTitle ==
          $(this).parent()[0].innerText
        ) {
          arrList[i].remove();
        }
      }
      e.preventDefault();
    } else {
      var arrList = $("ul.nav.navbar-nav.bookmark-icons li");
      $(this).addClass("warning");
      e.preventDefault();
      var $url = $(this).parent()[0].href,
        $name = $(this).parent()[0].innerText,
        $icon = $(this).parent()[0].firstChild.firstChild.dataset.icon,
        $listItem = "",
        $listItemDropdown = "";
      $listItem =
        '<li class="nav-item d-none d-lg-block">' +
        '<a class="nav-link" href="' +
        $url +
        '" data-toggle="tooltip" data-placement="top" title="" data-original-title="' +
        $name +
        '">' +
        '<i class="ficon ' +
        $icon +
        '"></i>' +
        "</a>" +
        "</li>";
      $("ul.nav.bookmark-icons").append($listItem);
      $('[data-toggle="tooltip"]').tooltip();
    }
  });

  // If we use up key(38) Down key (40) or Enter key(13)
  $(window).on("keydown", function (e) {
    var $current = $(".search-list li.current_item"),
      $next,
      $prev;
    if (e.keyCode === 40) {
      $next = $current.next();
      $current.removeClass("current_item");
      $current = $next.addClass("current_item");
    } else if (e.keyCode === 38) {
      $prev = $current.prev();
      $current.removeClass("current_item");
      $current = $prev.addClass("current_item");
    }

    if (e.keyCode === 13 && $(".search-list li.current_item").length > 0) {
      var selected_item = $(".search-list li.current_item a");
      window.location = selected_item.attr("href");
      $(selected_item).trigger("click");
    }
  });

  // Waves Effect
  Waves.init();
  Waves.attach(".btn", ["waves-light"]);
})(window, document, jQuery);
