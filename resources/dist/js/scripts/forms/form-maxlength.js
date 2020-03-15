/*=========================================================================================
	File Name: form-maxlength.js
	Description: Bootstrap-Maxlength uses a Twitter Bootstrap label to show a visual
		feedback to the user about the maximum length of the field where the user is
		inserting text. Uses the HTML5 attribute "maxlength" to work.
	----------------------------------------------------------------------------------------
	Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: Pixinvent
	Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function (window, document, $) {
  'use strict';

  var $danger = "#ea5455";
  var $primary = "#7367f0";
  var $textcolor = "#4e5154";

  $(".char-textarea").on("keyup", function (event) {
    checkTextAreaMaxLength(this, event);
    // to later change text color in dark layout
    $(this).addClass("active")
  });

  /*
  Checks the MaxLength of the Textarea
  -----------------------------------------------------
  @prerequisite:	textBox = textarea dom element
          e = textarea event
                  length = Max length of characters
  */
  function checkTextAreaMaxLength(textBox, e) {

    var maxLength = parseInt($(textBox).data("length"));


    if (!checkSpecialKeys(e)) {
      if (textBox.value.length < maxLength - 1) textBox.value = textBox.value.substring(0, maxLength);
    }
    $(".char-count").html(textBox.value.length);

    if (textBox.value.length > maxLength) {
      $(".counter-value").css("background-color", $danger);
      $(".char-textarea").css("color", $danger);
      // to change text color after limit is maxedout out
      $(".char-textarea").addClass("max-limit")
    }
    else {
      $(".counter-value").css("background-color", $primary);
      $(".char-textarea").css("color", $textcolor);
      $(".char-textarea").removeClass("max-limit")

    }

    return true;
  }
  /*
  Checks if the keyCode pressed is inside special chars
  -------------------------------------------------------
  @prerequisite:	e = e.keyCode object for the key pressed
  */
  function checkSpecialKeys(e) {
    if (e.keyCode != 8 && e.keyCode != 46 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40)
      return false;
    else
      return true;
  }

})(window, document, jQuery);
