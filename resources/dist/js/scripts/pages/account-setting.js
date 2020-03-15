/*=========================================================================================
	File Name: account-setting.js
	Description: Account setting.
	----------------------------------------------------------------------------------------
	Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: PIXINVENT
	Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(document).ready(function () {
  // language select
  var languageselect = $("#languageselect2").select2({
    dropdownAutoWidth: true,
    width: '100%'
  });
  // music select
  var musicselect = $("#musicselect2").select2({
    dropdownAutoWidth: true,
    width: '100%'
  });
  // movies select
  var moviesselect = $("#moviesselect2").select2({
    dropdownAutoWidth: true,
    width: '100%'
  });
  // birthdate date
  $('.birthdate-picker').pickadate({
    format: 'mmmm, d, yyyy'
  });
});
