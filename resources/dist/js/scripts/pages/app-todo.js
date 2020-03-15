/*=========================================================================================
    File Name: app-todo.js
    Description: app-todo
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(function () {
  "use strict";

  var $curr_title, $curr_desc, $curr_info, $curr_fav, $curr_chipVal;

  // if it is not touch device
  if (!$.app.menu.is_touch_device()) {
    // --------------------------------------------
    // Sidebar menu scrollbar
    // --------------------------------------------
    if ($('.todo-application .sidebar-menu-list').length > 0) {
      var content = new PerfectScrollbar('.sidebar-menu-list', {
        theme: "dark"
      });
    }

    // --------------------------------------------
    // Todo task list scrollbar
    // --------------------------------------------
    if ($('.todo-application .todo-task-list').length > 0) {
      var sidebar_todo = new PerfectScrollbar('.todo-task-list', {
        theme: "dark"
      });
    }
  }

  // if it is a touch device
  else {
    $(".sidebar-menu-list").css("overflow", "scroll");
    $(".todo-task-list").css("overflow", "scroll");
  }

  // Info star click
  $(document).on("click", ".todo-application .todo-item-info i", function (e) {
    $(this).parent('.todo-item-info').toggleClass("success");
    e.stopPropagation();
  });

  // Favorite star click
  $(document).on("click", ".todo-application .todo-item-favorite i", function (e) {
    $(this).parent('.todo-item-favorite').toggleClass("warning");
    e.stopPropagation();
  });

  // Main menu toggle should hide app menu
  $('.menu-toggle').on('click', function (e) {
    $('.app-content .sidebar-left').removeClass('show');
    $('.app-content .app-content-overlay').removeClass('show');
  });

  // On sidebar close click
  $(".todo-application .sidebar-close-icon").on('click', function () {
    $('.sidebar-left').removeClass('show');
    $('.app-content-overlay').removeClass('show');
  });

  // Todo sidebar toggle
  $('.sidebar-toggle').on('click', function (e) {
    e.stopPropagation();
    $('.app-content .sidebar-left').toggleClass('show');
    $('.app-content .app-content-overlay').addClass('show');
  });
  $('.app-content .app-content-overlay').on('click', function (e) {
    $('.app-content .sidebar-left').removeClass('show');
    $('.app-content .app-content-overlay').removeClass('show');
  });

  // Add class active on click of sidebar filters list
  $(".todo-application .list-group-filters a").on('click', function () {
    if ($('.todo-application .list-group-filters a').hasClass('active')) {
      $('.todo-application .list-group-filters a').removeClass('active');
    }
    $(this).addClass("active");
  });

  // For chat sidebar on small screen
  if ($(window).width() > 992) {
    if ($('.todo-application .app-content-overlay').hasClass('show')) {
      $('.todo-application .app-content-overlay').removeClass('show');
    }
  }

  // On add new item, clear modal popup fields
  $(".add-task button").on('click', function (e) {
    $('.modal .new-todo-item-title').val("");
    $('.modal .new-todo-item-desc').val("");
    $('.modal .dropdown-menu input').prop("checked", false);
    if ($('.modal .todo-item-info').hasClass('success')) { $('.modal .todo-item-info').removeClass('success') }
    if ($('.modal .todo-item-favorite').hasClass('warning')) { $('.modal .todo-item-favorite').removeClass('warning') }
  });

  // To add new todo list item
  $(".add-todo-item").on('click', function (e) {
    e.preventDefault();
    var todoInfo = "",
      todoFav = "",
      todoChip = "";

    var todoTitle = $(".new-todo-item-title").val();
    var todoDesc = $(".new-todo-item-desc").val();
    if ($(".modal.show .todo-item-info").hasClass('success')) {
      todoInfo = " success";
    }
    if ($(".modal.show .todo-item-favorite").hasClass('warning')) {
      todoFav = " warning";
    }

    // Chip calculation loop
    var selected = $('.modal .dropdown-menu input:checked');

    selected.each(function () {
      todoChip += '<div class="chip mb-0">' +
        '<div class="chip-body">' +
        '<span class="chip-text" data-value="' + $(this).data('value') + '"><span class="bullet bullet-' + $(this).data('color') + ' bullet-xs"></span> ' + $(this).data('value') + '</span>' +
        '</div>' +
        '</div>';
    });
    // HTML Output
    if (todoTitle != "") {
      $(".todo-task-list-wrapper").append('<li class="todo-item" style="animation-delay: 0s;"  data-toggle="modal" data-target="#editTaskModal">' +
        '<div class="todo-title-wrapper d-flex justify-content-between mb-50">' +
        '<div class="todo-title-area d-flex align-items-center">' +
        '<div class="title-wrapper d-flex">' +
        '<div class="vs-checkbox-con">' +
        '<input type="checkbox" >' +
        '<span class="vs-checkbox vs-checkbox-sm">' +
        '<span class="vs-checkbox--check">' +
        '<i class="vs-icon feather icon-check"></i>' +
        '</span>' +
        '</span>' +
        '</div>' +
        '<h6 class="todo-title mt-50 mx-50">' + todoTitle + '</h6>' +
        '</div>' +
        '<div class="chip-wrapper">' + todoChip + '</div>' +
        '</div>' +
        '<div class="float-right todo-item-action d-flex">' +
        '<a class="todo-item-info' + todoInfo + '"><i class="feather icon-info"></i></a>' +
        '<a class="todo-item-favorite' + todoFav + '"><i class="feather icon-star"></i></a>' +
        '<a class="todo-item-delete"><i class="feather icon-trash"></i></a>' +
        '</div>' +
        '</div>' +
        '<p class="mb-0 todo-desc truncate">' + todoDesc + '</p>' +
        '</li>');
    }

    $('#form-edit-todo .edit-todo-item-title').val(todoTitle);
    $('#form-edit-todo .edit-todo-item-desc').val(todoDesc);
    $('#form-edit-todo .dropdown-menu input').prop("checked", false);
    if ($('#form-edit-todo .edit-todo-item-info').hasClass('success')) { $('#form-edit-todo .edit-todo-item-info').addClass('success') }
    if ($('#form-edit-todo .edit-todo-item-favorite').hasClass('warning')) { $('#form-edit-todo .edit-todo-item-favorite').addClass('warning') }
  });

  // To update todo list item
  $(document).on('click', ".todo-task-list-wrapper .todo-item", function (e) {

    // Saving all values in variable
    $curr_title = $(this).find('.todo-title');  // Set path for Current Title, use this variable when updating title
    $curr_desc = $(this).find('.todo-desc');  // Set path for Current Description, use this variable when updating Description
    $curr_info = $(this).find('.todo-item-info');  // Set path for Current info, use this variable when updating info
    $curr_fav = $(this).find('.todo-item-favorite'); // Set path for Current favorite, use this variable when updating favorite
    $curr_chipVal = $(this).find('.chip-wrapper'); // Set path for Chips, use this variable when updating chip value

    var $title = $(this).find('.todo-title').html();
    var $desc = $(this).find('.todo-desc').html();
    var $info = $(this).find('.todo-item-info');
    var $fav = $(this).find('.todo-item-favorite');
    $('#form-edit-todo .dropdown-menu input').prop("checked", false);


    // Checkbox checked as per chips

    var selected = $(this).find('.chip');
    selected.each(function () {

      var chipVal = $(this).find('.chip-text').data('value');
      $('#form-edit-todo .dropdown-menu input[data-value="' + chipVal + '"]').prop("checked", true);
    });

    // apply all variable values to fields
    $('#form-edit-todo .edit-todo-item-title').val($title);
    $('#form-edit-todo .edit-todo-item-desc').val($desc);

    if ($('#form-edit-todo .todo-item-info').hasClass('success')) { $('#form-edit-todo .todo-item-info').removeClass('success') }
    if ($('#form-edit-todo .edit-todo-item-favorite').hasClass('warning')) { $('#form-edit-todo .edit-todo-item-favorite').removeClass('warning') }

    if ($($info).hasClass('success')) {
      $('#form-edit-todo .todo-item-info').addClass('success');
    }

    if ($($fav).hasClass('warning')) {
      $('#form-edit-todo .edit-todo-item-favorite').addClass('warning');
    }
  });

  // Updating Data Values to Fields
  $('.update-todo-item').on('click', function () {
    var $edit_title = $('#form-edit-todo .edit-todo-item-title').val();
    var $edit_desc = $('#form-edit-todo .edit-todo-item-desc').val();
    var $edit_info = $('#form-edit-todo .todo-item-info i');
    var $edit_fav = $('#form-edit-todo .todo-item-favorite i');

    $($curr_title).text($edit_title);
    $($curr_desc).text($edit_desc);

    if ($($curr_info).hasClass('success')) { $($curr_info).removeClass('success') }
    if ($($curr_fav).hasClass('warning')) { $($curr_fav).removeClass('warning') }

    if ($($edit_info).parent('.todo-item-info').hasClass('success')) {
      $curr_info.addClass('success');
    }

    if ($($edit_fav).parent('.todo-item-favorite').hasClass('warning')) {
      $curr_fav.addClass('warning');
    }

    // Chip calculation loop
    var $edit_selected = $('#form-edit-todo .dropdown-menu input:checked');
    var $edit_todoChip = "";

    $edit_selected.each(function () {
      $edit_todoChip += '<div class="chip mb-0">' +
        '<div class="chip-body">' +
        '<span class="chip-text" data-value="' + $(this).data('value') + '"><span class="bullet bullet-' + $(this).data('color') + ' bullet-xs"></span> ' + $(this).data('value') + '</span>' +
        '</div>' +
        '</div>';
    });

    $curr_chipVal.empty();

    $($curr_chipVal).append($edit_todoChip);


  });


  //EVENT DELETION
  $(document).on('click', '.todo-item-delete', function (e) {
    var item = this;
    e.stopPropagation();
    $(item).closest('.todo-item').remove();
  })

  // Complete task strike through
  $(document).on('click', '.todo-item input', function (event) {
    event.stopPropagation();
    $(this).closest('.todo-item').toggleClass("completed");
  });


  // Filter
  $("#todo-search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    if (value != "") {
      $(".todo-item").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
      var tbl_row = $(".todo-item:visible").length; //here tbl_test is table name

      //Check if table has row or not
      if (tbl_row == 0) {
        if (!$('.no-results').hasClass('show')) {
          $('.no-results').addClass('show');
        }
      }
      else {
        $('.no-results').removeClass('show');

      }
    }
    else {
      // If filter box is empty
      $(".todo-item").show();
      if ($('.no-results').hasClass('show')) {
        $('.no-results').removeClass('show');
      }
    }
  });

});

$(window).on("resize", function () {
  // remove show classes from sidebar and overlay if size is > 992
  if ($(window).width() > 992) {
    if ($('.app-content .app-content-overlay').hasClass('show')) {
      $('.app-content .sidebar-left').removeClass('show');
      $('.app-content .app-content-overlay').removeClass('show');
    }
  }
});
