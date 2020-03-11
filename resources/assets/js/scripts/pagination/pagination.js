/*=========================================================================================
    File Name: pagination.js
    Description: Provide pagination links for your site or app with the multi-page
                pagination component.
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function (window, document, $) {
  'use strict';
  // default pagination
  $('.page1-links').twbsPagination({
    totalPages: 5,
    visiblePages: 4,
    prev: 'Prev',
    first: null,
    last: null,
    startPage: 2,
    onPageClick: function (event, page) {
      $('#page1-content').text('You are on Page ' + page);
      $(".pagination").find('li').addClass('page-item');
      $(".pagination").find('a').addClass("page-link");
    }
  });

  //Default Pagination with last & first
  $('.firstLast1-links').twbsPagination({
    totalPages: 5,
    visiblePages: 4,
    prev: 'Prev',
    first: 'First',
    last: 'Last',
    startPage: 2,
    onPageClick: function (event, page) {
      $('#firstLast1-content').text('You are on Page ' + page);
      $(".pagination").find('li').addClass('page-item');
      $(".pagination").find('a').addClass("page-link");
    }
  });
  //Set Start Page Of Pagination
  $('.start-links').twbsPagination({
    totalPages: 10,
    visiblePages: 6,
    startPage: 5,
    prev: 'Prev',
    first: 'First',
    last: 'Last',
    onPageClick: function (event, page) {
      $('#start-content').text('Your start Page ' + page);
      $(".pagination").find('li').addClass('page-item');
      $(".pagination").find('a').addClass("page-link");
    }
  });

  // Pagination drop after reload
  $('.url1-links').twbsPagination({
    totalPages: 10,
    visiblePages: 5,
    prev: 'Prev',
    first: 'First',
    last: 'Last',
    href: '?page={{page}}&#url1-content',
    hrefVariable: '{{page}}',
    onPageClick: function (event, page) {
      $('#url1-content').text('You are on Page ' + page);
      $(".pagination").find('li').addClass('page-item');
      $(".pagination").find('a').addClass("page-link");
    }
  });

})(window, document, jQuery);
