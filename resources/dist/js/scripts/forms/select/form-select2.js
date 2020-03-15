/*=========================================================================================
    File Name: form-select2.js
    Description: Select2 is a jQuery-based replacement for select boxes.
    It supports searching, remote data sets, and pagination of results.
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function(window, document, $) {
	'use strict';

  // Basic Select2 select
	$(".select2").select2({
    // the following code is used to disable x-scrollbar when click in select input and
    // take 100% width in responsive also
    dropdownAutoWidth: true,
    width: '100%'
  });

    // Select With Icon
    $(".select2-icons").select2({
        dropdownAutoWidth: true,
        width: '100%',
        minimumResultsForSearch: Infinity,
        templateResult: iconFormat,
        templateSelection: iconFormat,
        escapeMarkup: function(es) { return es; }
    });

    // Format icon
    function iconFormat(icon) {
        var originalOption = icon.element;
        if (!icon.id) { return icon.text; }
        var $icon = "<i class='" + $(icon.element).data('icon') + "'></i>" + icon.text;

        return $icon;
    }


    // Limiting the number of selections
    $(".max-length").select2({
      dropdownAutoWidth: true,
      width: '100%',
      maximumSelectionLength: 2,
      placeholder: "Select maximum 2 items"
    });


    // Programmatic access
    var $select = $(".js-example-programmatic").select2({
      dropdownAutoWidth: true,
      width: '100%'
    });
    var $selectMulti = $(".js-example-programmatic-multi").select2();
    $selectMulti.select2({
      dropdownAutoWidth: true,
      width: '100%',
      placeholder: "Programmatic Events"
    });
    $(".js-programmatic-set-val").on("click", function () { $select.val("CA").trigger("change"); });

    $(".js-programmatic-open").on("click", function () { $select.select2("open"); });
    $(".js-programmatic-close").on("click", function () { $select.select2("close"); });

    $(".js-programmatic-init").on("click", function () { $select.select2(); });
    $(".js-programmatic-destroy").on("click", function () { $select.select2("destroy"); });

    $(".js-programmatic-multi-set-val").on("click", function () { $selectMulti.val(["CA", "AL"]).trigger("change"); });
    $(".js-programmatic-multi-clear").on("click", function () { $selectMulti.val(null).trigger("change"); });

    // Loading array data
    var data = [
        { id: 0, text: 'enhancement' },
        { id: 1, text: 'bug' },
        { id: 2, text: 'duplicate' },
        { id: 3, text: 'invalid' },
        { id: 4, text: 'wontfix' }
    ];

    $(".select2-data-array").select2({
      dropdownAutoWidth: true,
      width: '100%',
      data: data
    });

    // Loading remote data
    $(".select2-data-ajax").select2({
        dropdownAutoWidth: true,
        width: '100%',
        ajax: {
        url: "https://api.github.com/search/repositories",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term, // search term
            page: params.page
          };
        },
        processResults: function (data, params) {
          // parse the results into the format expected by Select2
          // since we are using custom formatting functions we do not need to
          // alter the remote JSON data, except to indicate that infinite
          // scrolling can be used
          params.page = params.page || 1;

          return {
            results: data.items,
            pagination: {
              more: (params.page * 30) < data.total_count
            }
          };
        },
        cache: true
      },
      placeholder: 'Search for a repository',
      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
      minimumInputLength: 1,
      templateResult: formatRepo,
      templateSelection: formatRepoSelection
  });

    function formatRepo (repo) {
      if (repo.loading) return repo.text;

      var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
        "<div class='select2-result-repository__meta'>" +
          "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";

      if (repo.description) {
        markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
      }

      markup += "<div class='select2-result-repository__statistics'>" +
        "<div class='select2-result-repository__forks'><i class='icon-code-fork mr-0'></i> " + repo.forks_count + " Forks</div>" +
        "<div class='select2-result-repository__stargazers'><i class='icon-star5 mr-0'></i> " + repo.stargazers_count + " Stars</div>" +
        "<div class='select2-result-repository__watchers'><i class='icon-eye mr-0'></i> " + repo.watchers_count + " Watchers</div>" +
      "</div>" +
      "</div></div>";

      return markup;
    }

    function formatRepoSelection (repo) {
      return repo.full_name || repo.text;
    }


    // Customizing how results are matched
    function matchStart (term, text) {
      if (text.toUpperCase().indexOf(term.toUpperCase()) === 0) {
        return true;
      }

      return false;
    }

    $.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
      $(".select2-customize-result").select2({
        dropdownAutoWidth: true,
        width: '100%',
        placeholder: "Search by 'r'",
        matcher: oldMatcher(matchStart)
      });
    });

    // Theme support
    $(".select2-theme").select2({
      dropdownAutoWidth: true,
      width: '100%',
      placeholder: "Classic Theme",
      theme: "classic"
    });


    // Sizing options

    // Large
    $('.select2-size-lg').select2({
        dropdownAutoWidth: true,
        width: '100%',
        containerCssClass: 'select-lg'
    });

    // Small
    $('.select2-size-sm').select2({
        dropdownAutoWidth: true,
        width: '100%',
        containerCssClass: 'select-sm'
    });

    // Color Options

    // Background Color
    $('.select2-bg').each(function(i, obj) {
      var variation = "",
      textVariation = "",
      textColor = "";
      var color = $(this).data('bgcolor');
      variation = $(this).data('bgcolor-variation');
      textVariation = $(this).data('text-variation');
      textColor = $(this).data('text-color');
      if(textVariation !== ""){
        textVariation = " "+textVariation;
      }
      if(variation !== ""){
        variation = " bg-"+variation;
      }
      var className = "bg-"+color + variation + " " + textColor + textVariation + " border-"+color + ' border-darken-2 ';

      $(this).select2({
        dropdownAutoWidth: true,
        width: '100%',
        containerCssClass: className
      });
    });

    // Border Color
    $('.select2-border').each(function(i, obj) {
      var variation = "",
      textVariation = "",
      textColor = "";
      var color = $(this).data('border-color');
      textVariation = $(this).data('text-variation');
      variation = $(this).data('border-variation');
      textColor = $(this).data('text-color');
      if(textVariation !== ""){
        textVariation = " "+textVariation;
      }
      if(variation !== ""){
        variation = " border-"+variation;
      }

      var className = "border-"+color + " " +variation + " " + textColor + textVariation;

      $(this).select2({
        dropdownAutoWidth: true,
        width: '100%',
        containerCssClass: className
      });
    });

    // Full Background Color
    $('.select2-full-bg').each(function(i, obj) {
      var variation = "",
      textVariation = "",
      textColor = "";
      var color = $(this).data('bgcolor');
      variation = $(this).data('bgcolor-variation');
      textVariation = $(this).data('text-variation');
      textColor = $(this).data('text-color');
      if(variation !== ""){
        variation = " bg-"+variation;
      }
      if(textVariation !== ""){
        textVariation = " "+textVariation;
      }
      var className = "bg-"+color + variation + " " + textColor + textVariation + " border-"+color + ' border-darken-2 ';

      $(this).select2({
        dropdownAutoWidth: true,
        width: '100%',
        containerCssClass: className,
        dropdownCssClass: className
      });
    });

    $('select[data-text-color]').each(function(i, obj) {
      var text = $(this).data('text-color'),textVariation;
      textVariation = $(this).data('text-variation');
      if(textVariation !== ""){
        textVariation = " "+textVariation;
      }
      $(this).next(".select2").find(".select2-selection__rendered").addClass(text+textVariation);
    });

})(window, document, jQuery);
