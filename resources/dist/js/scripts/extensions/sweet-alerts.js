/*=========================================================================================
	File Name: sweet-alerts.js
	Description: A beautiful replacement for javascript alerts
	----------------------------------------------------------------------------------------
	Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: Pixinvent
	Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {

  // Basic

  $('#basic-alert').on('click', function () {
    Swal.fire({
      title: 'Any fool can use a computer',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  });

  $('#with-title').on('click', function () {
    Swal.fire({
      title: 'The Internet?,',
      text: "That thing is still around?",
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    }
    )
  });

  $('#footer-alert').on('click', function () {
    Swal.fire({
      type: 'error',
      title: 'Oops...',
      text: 'Something went wrong!',
      footer: '<a href>Why do I have this issue?</a>',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  });

  $('#html-alert').on('click', function () {
    Swal.fire({
      title: '<strong>HTML <u>example</u></strong>',
      type: 'info',
      html:
        'You can use <b>bold text</b>, ' +
        '<a href="https://pixinvent.com/" target="_blank">links</a> ' +
        'and other HTML tags',
      showCloseButton: true,
      showCancelButton: true,
      focusConfirm: false,
      confirmButtonText:
        '<i class="fa fa-thumbs-up"></i> Great!',
      confirmButtonAriaLabel: 'Thumbs up, great!',
      cancelButtonText:
        '<i class="fa fa-thumbs-down"></i>',
      cancelButtonAriaLabel: 'Thumbs down',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
      cancelButtonClass: 'btn btn-danger ml-1',
    })
  });

  // Position

  $('#position-top-start').on('click', function () {
    Swal.fire({
      position: 'top-start',
      type: 'success',
      title: 'Your work has been saved',
      showConfirmButton: false,
      timer: 1500,
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })

  $('#position-top-end').on('click', function () {
    Swal.fire({
      position: 'top-end',
      type: 'success',
      title: 'Your work has been saved',
      showConfirmButton: false,
      timer: 1500,
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })

  $('#position-bottom-start').on('click', function () {
    Swal.fire({
      position: 'bottom-start',
      type: 'success',
      title: 'Your work has been saved',
      showConfirmButton: false,
      timer: 1500,
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })
  $('#position-bottom-end').on('click', function () {
    Swal.fire({
      position: 'bottom-end',
      type: 'success',
      title: 'Your work has been saved',
      showConfirmButton: false,
      timer: 1500,
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })

  // Animations

  $("#bounce-in-animation").on('click', function () {
    Swal.fire({
      title: 'Bounce In Animation',
      animation: false,
      customClass: 'animated bounceIn',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })

  })
  $("#fade-in-animation").on('click', function () {
    Swal.fire({
      title: 'Fade In Animation',
      animation: false,
      customClass: 'animated fadeIn',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })
  $("#flip-x-animation").on('click', function () {
    Swal.fire({
      title: 'Flip In Animation',
      animation: false,
      customClass: 'animated flipInX',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })
  $("#tada-animation").on('click', function () {
    Swal.fire({
      title: 'Tada Animation',
      animation: false,
      customClass: 'animated tada',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })
  $("#shake-animation").on('click', function () {
    Swal.fire({
      title: 'Shake Animation',
      animation: false,
      customClass: 'animated shake',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  })

  // type

  $('#type-success').on('click', function () {
    Swal.fire({
      title: "Good job!",
      text: "You clicked the button!",
      type: "success",
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    });
  });

  $('#type-info').on('click', function () {
    Swal.fire({
      title: "Info!",
      text: "You clicked the button!",
      type: "info",
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    });
  });

  $('#type-warning').on('click', function () {
    Swal.fire({
      title: "Warning!",
      text: " You clicked the button!",
      type: "warning",
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    });
  });

  $('#type-error').on('click', function () {
    Swal.fire({
      title: "Error!",
      text: " You clicked the button!",
      type: "error",
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    });
  });

  // options

  $('#custom-icon').on('click', function () {
    Swal.fire({
      title: 'Sweet!',
      text: 'Modal with a custom image.',
      imageUrl: 'images/slider/04.jpg',
      imageWidth: 400,
      imageHeight: 200,
      imageAlt: 'Custom image',
      animation: false,
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    })
  });

  $('#auto-close').on('click', function () {
    var timerInterval
    Swal.fire({
      title: 'Auto close alert!',
      html: 'I will close in <strong></strong> seconds.',
      timer: 2000,
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
      onBeforeOpen: function () {
        Swal.showLoading()
        timerInterval = setInterval(function () {
          Swal.getContent().querySelector('strong')
            .textContent = Swal.getTimerLeft()
        }, 100)
      },
      onClose: function () {
        clearInterval(timerInterval)
      }
    }).then(function (result) {
      if (
        // Read more about handling dismissals
        result.dismiss === Swal.DismissReason.timer
      ) {
        console.log('I was closed by the timer')
      }
    })
  });

  $('#outside-click').on('click', function () {
    Swal.fire({
      title: 'Click outside to close!',
      text: 'This is a cool message!',
      allowOutsideClick: true,
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
    });
  });

  $('#prompt-function').on('click', function () {
    Swal.mixin({
      input: 'text',
      confirmButtonText: 'Next &rarr;',
      showCancelButton: true,
      progressSteps: ['1', '2', '3'],
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
      cancelButtonClass: "btn btn-danger ml-1"
    }).queue([
      {
        title: 'Question 1',
        text: 'Chaining swal2 modals is easy'
      },
      'Question 2',
      'Question 3'
    ]).then(function (result) {
      if (result.value) {
        Swal.fire({
          title: 'All done!',
          html:
            'Your answers: <pre><code>' +
            JSON.stringify(result.value) +
            '</code></pre>',
          confirmButtonText: 'Lovely!'
        })
      }
    })
  });

  $('#ajax-request').on('click', function () {
    Swal.fire({
      title: 'Search for a user',
      input: 'text',
      confirmButtonClass: 'btn btn-primary',
      buttonsStyling: false,
      inputAttributes: {
        autocapitalize: 'off'
      },
      showCancelButton: true,
      confirmButtonText: 'Look up',
      showLoaderOnConfirm: true,
      cancelButtonClass: "btn btn-danger ml-1",
      preConfirm: function (login) {
        return fetch("//api.github.com/users/" + login + "")
          .then(function (response) {
            if (!response.ok) {
              console.log(response)
              throw new Error(response.statusText)
            }
            return response.json()
          })
          .catch(function (error) {
            Swal.showValidationMessage(
              "Request failed:  " + error + ""
            )
          })
      },
      allowOutsideClick: function () {
        !Swal.isLoading()
      }
    }).then(function (result) {
      if (result.value) {
        Swal.fire({
          title: "" + result.value.login + "'s avatar",
          imageUrl: result.value.avatar_url
        })
      }
    })
  });

  // confirm options

  $('#confirm-text').on('click', function () {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
      confirmButtonClass: 'btn btn-primary',
      cancelButtonClass: 'btn btn-danger ml-1',
      buttonsStyling: false,
    }).then(function (result) {
      if (result.value) {
        Swal.fire(
          {
            type: "success",
            title: 'Deleted!',
            text: 'Your file has been deleted.',
            confirmButtonClass: 'btn btn-success',
          }
        )
      }
    })
  });

  $('#confirm-color').on('click', function () {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
      confirmButtonClass: 'btn btn-primary',
      cancelButtonClass: 'btn btn-danger ml-1',
      buttonsStyling: false,
    }).then(function (result) {
      if (result.value) {
        Swal.fire({
          type: "success",
          title: 'Deleted!',
          text: 'Your file has been deleted.',
          confirmButtonClass: 'btn btn-success',
        })
      }
      else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
          title: 'Cancelled',
          text: 'Your imaginary file is safe :)',
          type: 'error',
          confirmButtonClass: 'btn btn-success',
        })
      }
    })
  });

});
