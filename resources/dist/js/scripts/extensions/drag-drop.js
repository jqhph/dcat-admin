/*=========================================================================================
    File Name: drag-drop.js
    Description: drag & drop elements using dragula js
    --------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


$(document).ready(function () {

  // Draggable Cards
  dragula([document.getElementById('card-drag-area')]);

  // Sortable Lists
  dragula([document.getElementById('basic-list-group')]);
  dragula([document.getElementById('multiple-list-group-a'), document.getElementById('multiple-list-group-b')]);

  // Cloning
  dragula([document.getElementById('chips-list-1'), document.getElementById('chips-list-2')], {
    copy: true
  });

  // With Handles

  dragula([document.getElementById("handle-list-1"), document.getElementById("handle-list-2")], {
    moves: function (el, container, handle) {
      return handle.classList.contains('handle');
    }
  });

});
