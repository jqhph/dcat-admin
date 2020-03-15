/*=========================================================================================
	File Name: toastr.js
	Description: Toastr notifications
	----------------------------------------------------------------------------------------
	Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: Pixinvent
	Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {

  // Success Type
  $('#type-success').on('click', function () {
    toastr.success('Have fun storming the castle!', 'Miracle Max Says');
  });

  // Info Type
  $('#type-info').on('click', function () {
    toastr.info('We do have the Kapua suite available.', 'Turtle Bay Resort');
  });

  // Warning Type
  $('#type-warning').on('click', function () {
    toastr.warning('My name is Inigo Montoya. You killed my father, prepare to die!');
  });

  // Error Type
  $('#type-error').on('click', function () {
    toastr.error('I do not think that word means what you think it means.', 'Inconceivable!');
  });

  // Position Top Left
  $('#position-top-left').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Top Left!', { positionClass: 'toast-top-left', containerId: 'toast-top-left' });
  });

  // Position Top Center
  $('#position-top-center').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Top Center!', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
  });

  // Position Top Right
  $('#position-top-right').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Top Right!', { positionClass: 'toast-top-right', containerId: 'toast-top-right' });
  });

  // Position Top Full Width
  $('#position-top-full').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Top Full Width!', { positionClass: 'toast-top-full-width', });
  });

  // Position Bottom Left
  $('#position-bottom-left').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Bottom Left!', { positionClass: 'toast-bottom-left', containerId: 'toast-bottom-left' });
  });

  // Position Bottom Center
  $('#position-bottom-center').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Bottom Center!', { positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center' });
  });

  // Position Bottom Right
  $('#position-bottom-right').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Bottom Right!', { positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right' });
  });

  // Position Bottom Full Width
  $('#position-bottom-full').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Bottom Full Width!', { positionClass: 'toast-bottom-full-width' });
  });

  // Text Notification
  $('#text-notification').on('click', function () {
    toastr.info('Have fun storming the castle!', 'Miracle Max Says');
  });

  // Close Button
  $('#close-button').on('click', function () {
    toastr.success('Have fun storming the castle!', 'With Close Button', { "closeButton": true });
  });

  // Progress Bar
  $('#progress-bar').on('click', function () {
    toastr.warning('Have fun storming the castle!', 'Progress Bar', { "progressBar": true });
  });

  // Clear Toast Button
  $('#clear-toast-btn').on('click', function () {
    toastr.error('Clear itself?<br /><br /><button type="button" class="btn btn-primary clear">Yes</button>', 'Clear Toast Button');
  });


  // Immediately remove current toasts without using animation
  $('#show-remove-toast').on('click', function () {
    toastr.info('Have fun storming the castle!', 'Miracle Max Says');
  });

  $('#remove-toast').on('click', function () {
    toastr.remove();
  });

  // Remove current toasts using animation
  $('#show-clear-toast').on('click', function () {
    toastr.info('Have fun storming the castle!', 'Miracle Max Says');
  });

  $('#clear-toast').on('click', function () {
    toastr.clear();
  });


  // Fast Duration
  $('#fast-duration').on('click', function () {
    toastr.success('Have fun storming the castle!', 'Fast Duration', { "showDuration": 500 });
  });

  // Slow Duration
  $('#slow-duration').on('click', function () {
    toastr.warning('Have fun storming the castle!', 'Slow Duration', { "hideDuration": 3000 });
  });

  // Timeout
  $('#timeout').on('click', function () {
    toastr.error('I do not think that word means what you think it means.', 'Timeout!', { "timeOut": 5000 });
  });

  // Sticky
  $('#sticky').on('click', function () {
    toastr.info('I do not think that word means what you think it means.', 'Sticky!', { "timeOut": 0 });
  });

  // Slide Down / Slide Up
  $('#slide-toast').on('click', function () {
    toastr.success('I do not think that word means what you think it means.', 'Slide Down / Slide Up!', { "showMethod": "slideDown", "hideMethod": "slideUp", timeOut: 2000 });
  });

  // Fade In / Fade Out
  $('#fade-toast').on('click', function () {
    toastr.success('I do not think that word means what you think it means.', 'Slide Down / Slide Up!', { "showMethod": "fadeIn", "hideMethod": "fadeOut", timeOut: 2000 });
  });
});
