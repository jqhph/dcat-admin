/*=========================================================================================
    File Name: dropzone.js
    Description: dropzone
    --------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

Dropzone.options.dpzSingleFile = {
  paramName: "file", // The name that will be used to transfer the file
  maxFiles: 1,
  init: function () {
    this.on("maxfilesexceeded", function (file) {
      this.removeAllFiles();
      this.addFile(file);
    });
  }
};

/********************************************
*               Multiple Files              *
********************************************/
Dropzone.options.dpzMultipleFiles = {
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 0.5, // MB
  clickable: true
}


/********************************************************
*               Use Button To Select Files              *
********************************************************/
new Dropzone(document.body, { // Make the whole body a dropzone
  url: "#", // Set the url
  previewsContainer: "#dpz-btn-select-files", // Define the container to display the previews
  clickable: "#select-files" // Define the element that should be used as click trigger to select files.
});


/****************************************************************
*               Limit File Size and No. Of Files                *
****************************************************************/
Dropzone.options.dpzFileLimits = {
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 0.5, // MB
  maxFiles: 5,
  maxThumbnailFilesize: 1, // MB
}


/********************************************
*               Accepted Files              *
********************************************/
Dropzone.options.dpAcceptFiles = {
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 1, // MB
  acceptedFiles: 'image/*'
}


/************************************************
*               Remove Thumbnail                *
************************************************/
Dropzone.options.dpzRemoveThumb = {
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 1, // MB
  addRemoveLinks: true,
  dictRemoveFile: " Trash"
}

/*****************************************************
*               Remove All Thumbnails                *
*****************************************************/
Dropzone.options.dpzRemoveAllThumb = {
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 1, // MB
  init: function () {

    // Using a closure.
    var _this = this;

    // Setup the observer for the button.
    $("#clear-dropzone").on("click", function () {
      // Using "_this" here, because "this" doesn't point to the dropzone anymore
      _this.removeAllFiles();
      // If you want to cancel uploads as well, you
      // could also call _this.removeAllFiles(true);
    });
  }
}
