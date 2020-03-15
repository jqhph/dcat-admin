$('.toast-toggler').on('click', function () {
  $(this).next('.toast').prependTo('.toast-bs-container .toast-position').toast('show')

  // if ($('.toast-bs-container .toast-position .toast').hasClass('hide')) {
  //   $('.toast-bs-container .toast-position .toast').toast('show')
  // }
});

$('.placement').on('click', function () {
  $('.toast-placement').toast('show');
  $('.toast-placement .toast').toast('show');
});

