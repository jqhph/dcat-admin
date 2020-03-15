/*=========================================================================================
	File Name: noui-slider.js
	Description: noUiSlider is a lightweight JavaScript range slider library.
	----------------------------------------------------------------------------------------
	Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: PIXINVENT
	Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {

  // RTL Support
  var direction = 'ltr';
  if($('html').data('textdirection') == 'rtl'){
    direction = 'rtl';
  }

	/********************************************
	*				Slider values				*
	********************************************/

  // Handles
  var handlesSlider = document.getElementById('slider-handles');

  noUiSlider.create(handlesSlider, {
    start: [4000, 8000],
    direction: direction,
    range: {
      'min': [2000],
      'max': [10000]
    }
  });


  // Snapping between steps
  var snapSlider = document.getElementById('slider-snap');

  noUiSlider.create(snapSlider, {
    start: [0, 500],
    direction: direction,
    snap: true,
    connect: true,
    range: {
      'min': 0,
      '10%': 50,
      '20%': 100,
      '30%': 150,
      '40%': 500,
      '50%': 800,
      'max': 1000
    }
  });



	/************************************************
	*				Slider behaviour				*
	************************************************/

  // Tap
  tapSlider = document.getElementById('tap');

  noUiSlider.create(tapSlider, {
    start: 40,
    direction: direction,
    behaviour: 'tap',
    connect: 'upper',
    range: {
      'min': 20,
      'max': 80
    }
  });


  // Drag
  var dragSlider = document.getElementById('drag');

  noUiSlider.create(dragSlider, {
    start: [40, 60],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });


  // Fixed dragging
  dragFixedSlider = document.getElementById('drag-fixed');

  noUiSlider.create(dragFixedSlider, {
    start: [40, 60],
    direction: direction,
    behaviour: 'drag-fixed',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });




  // Hover
  var hoverSlider = document.getElementById('hover'),
    field = document.getElementById('hover-val');

  noUiSlider.create(hoverSlider, {
    start: 20,
    direction: direction,
    behaviour: 'hover-snap',
    range: {
      'min': 0,
      'max': 10
    }
  });

  hoverSlider.noUiSlider.on('hover', function (value) {
    field.innerHTML = value;
  });


  // Combined options
  dragTapSlider = document.getElementById('combined');

  noUiSlider.create(dragTapSlider, {
    start: [40, 60],
    direction: direction,
    behaviour: 'drag-tap',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });



	/****************************************************
	*				Slider Scales / Pips				*
	****************************************************/

  var range_all_sliders = {
    'min': [0],
    '10%': [5, 5],
    '50%': [40, 10],
    'max': [100]
  };

  // Range
  var pipsRange = document.getElementById('pips-range');

  noUiSlider.create(pipsRange, {
    range: range_all_sliders,
    start: 0,
    direction: direction,
    pips: {
      mode: 'range',
      density: 3
    }
  });


  // Steps
  var range_step_sliders = {
    'min': [0],
    '10%': [5, 5],
    '50%': [25, 20],
    'max': [50, 50]
  };
  function filter500(value, type) {
    if (type === 0) {
      return value < 50 ? -1 : 0;
    }
    return value % 50 ? 2 : 1;
  }

  var pipsStepsFilter = document.getElementById('pips-steps-filter');

  noUiSlider.create(pipsStepsFilter, {
    range: range_step_sliders,
    start: 0,
    direction: direction,
    pips: {
      mode: 'steps',
      density: 5,
      filter: filter500,
      format: wNumb({
        decimals: 0,
        prefix: '$'
      })
    }
  });



	/********************************************
	*				Slider Colors				*
	********************************************/

  // Default
  var defaultColorSlider = document.getElementById('default-color-slider');

  noUiSlider.create(defaultColorSlider, {
    start: [45, 55],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });


  // Success
  var successColorSlider = document.getElementById('success-color-slider');

  noUiSlider.create(successColorSlider, {
    start: [40, 60],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });


  // Info
  var infoColorSlider = document.getElementById('info-color-slider');

  noUiSlider.create(infoColorSlider, {
    start: [35, 65],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });


  // Warning
  var warningColorSlider = document.getElementById('warning-color-slider');

  noUiSlider.create(warningColorSlider, {
    start: [45, 55],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });


  // Danger
  var dangerColorSlider = document.getElementById('danger-color-slider');

  noUiSlider.create(dangerColorSlider, {
    start: [40, 60],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  });

  // Colored Connects

  var sliderColoredConnects = document.getElementById('colored-connect');

  noUiSlider.create(sliderColoredConnects, {
    start: [4000, 8000, 12000, 16000],
    direction: direction,
    connect: [false, true, true, true, true],
    range: {
      'min': [2000],
      'max': [20000]
    }
  });

  var connect = sliderColoredConnects.querySelectorAll('.noUi-connect');
  var classes = ['bg-primary', 'bg-success', 'bg-info', 'bg-danger', 'bg-warning'];

  for (var i = 0; i < connect.length; i++) {
    connect[i].classList.add(classes[i]);
  }


	/********************************************
	*				Slider Sizing				*
	********************************************/

  // Extra large options
  var xl_options = {
    start: [45, 55],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  };

  var lg_options = {
    start: [40, 60],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  };

  var default_options = {
    start: [35, 65],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  };

  var sm_options = {
    start: [30, 70],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  };

  var xs_options = {
    start: [25, 75],
    direction: direction,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 20,
      'max': 80
    }
  };

  // Extra Large
  var extraLargeSlider = document.getElementById('extra-large-slider');
  var circleExtraLargeSlider = document.getElementById('circle-extra-large-slider');
  var squareExtraLargeSlider = document.getElementById('square-extra-large-slider');

  noUiSlider.create(extraLargeSlider, xl_options);
  noUiSlider.create(circleExtraLargeSlider, xl_options);
  noUiSlider.create(squareExtraLargeSlider, xl_options);

  // Large
  var largeSlider = document.getElementById('large-slider');
  var circleLargeSlider = document.getElementById('circle-large-slider');
  var squareLargeSlider = document.getElementById('square-large-slider');

  noUiSlider.create(largeSlider, lg_options);
  noUiSlider.create(circleLargeSlider, lg_options);
  noUiSlider.create(squareLargeSlider, lg_options);

  // Default
  var defaultSlider = document.getElementById('default-slider');
  var circleDefaultSlider = document.getElementById('circle-default-slider');
  var squareDefaultSlider = document.getElementById('square-default-slider');

  noUiSlider.create(defaultSlider, default_options);
  noUiSlider.create(circleDefaultSlider, default_options);
  noUiSlider.create(squareDefaultSlider, default_options);

  // Small
  var smallSlider = document.getElementById('small-slider');
  var circleSmallSlider = document.getElementById('circle-small-slider');
  var squareSmallSlider = document.getElementById('square-small-slider');

  noUiSlider.create(smallSlider, sm_options);
  noUiSlider.create(circleSmallSlider, sm_options);
  noUiSlider.create(squareSmallSlider, sm_options);

  // Extra Small
  var extraSmallSlider = document.getElementById('extra-small-slider');
  var circleExtraSmallSlider = document.getElementById('circle-extra-small-slider');
  var squareExtraSmallSlider = document.getElementById('square-extra-small-slider');

  noUiSlider.create(extraSmallSlider, xs_options);
  noUiSlider.create(circleExtraSmallSlider, xs_options);
  noUiSlider.create(squareExtraSmallSlider, xs_options);


	/********************************************
	*				Vertical Slider				*
	********************************************/

  // Default
  var vertical_slider_1 = document.getElementById('slider-vertical-1');

  noUiSlider.create(vertical_slider_1, {
    start: 20,
    direction: direction,
    orientation: 'vertical',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var vertical_slider_2 = document.getElementById('slider-vertical-2');

  noUiSlider.create(vertical_slider_2, {
    start: 50,
    direction: direction,
    orientation: 'vertical',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var vertical_slider_3 = document.getElementById('slider-vertical-3');

  noUiSlider.create(vertical_slider_3, {
    start: 20,
    direction: direction,
    orientation: 'vertical',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var vertical_slider_4 = document.getElementById('slider-vertical-4');

  noUiSlider.create(vertical_slider_4, {
    start: 50,
    direction: direction,
    orientation: 'vertical',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var vertical_slider_5 = document.getElementById('slider-vertical-5');

  noUiSlider.create(vertical_slider_5, {
    start: 20,
    direction: direction,
    orientation: 'vertical',
    range: {
      'min': 0,
      'max': 100
    }
  });


  // Connect to lower
  var connectLowerSlider1 = document.getElementById('connect-lower-1');

  noUiSlider.create(connectLowerSlider1, {
    start: 30,
    direction: direction,
    orientation: 'vertical',
    connect: 'lower',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectLowerSlider2 = document.getElementById('connect-lower-2');

  noUiSlider.create(connectLowerSlider2, {
    start: 40,
    direction: direction,
    orientation: 'vertical',
    connect: 'lower',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectLowerSlider3 = document.getElementById('connect-lower-3');

  noUiSlider.create(connectLowerSlider3, {
    start: 50,
    direction: direction,
    orientation: 'vertical',
    connect: 'lower',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectLowerSlider4 = document.getElementById('connect-lower-4');

  noUiSlider.create(connectLowerSlider4, {
    start: 60,
    direction: direction,
    orientation: 'vertical',
    connect: 'lower',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectLowerSlider5 = document.getElementById('connect-lower-5');

  noUiSlider.create(connectLowerSlider5, {
    start: 70,
    direction: direction,
    orientation: 'vertical',
    connect: 'lower',
    range: {
      'min': 0,
      'max': 100
    }
  });


  // Connect to upper
  var connectUpperSlider1 = document.getElementById('connect-upper-1');

  noUiSlider.create(connectUpperSlider1, {
    start: 30,
    direction: direction,
    orientation: 'vertical',
    connect: 'upper',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectUpperSlider2 = document.getElementById('connect-upper-2');

  noUiSlider.create(connectUpperSlider2, {
    start: 40,
    direction: direction,
    orientation: 'vertical',
    connect: 'upper',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectUpperSlider3 = document.getElementById('connect-upper-3');

  noUiSlider.create(connectUpperSlider3, {
    start: 50,
    direction: direction,
    orientation: 'vertical',
    connect: 'upper',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectUpperSlider4 = document.getElementById('connect-upper-4');

  noUiSlider.create(connectUpperSlider4, {
    start: 60,
    direction: direction,
    orientation: 'vertical',
    connect: 'upper',
    range: {
      'min': 0,
      'max': 100
    }
  });

  var connectUpperSlider5 = document.getElementById('connect-upper-5');

  noUiSlider.create(connectUpperSlider5, {
    start: 70,
    direction: direction,
    orientation: 'vertical',
    connect: 'upper',
    range: {
      'min': 0,
      'max': 100
    }
  });


  // Tooltips
  var tooltipSlider1 = document.getElementById('slider-tooltips-1');

  noUiSlider.create(tooltipSlider1, {
    start: [20, 80],
    direction: direction,
    orientation: 'vertical',
    tooltips: [false, wNumb({
      decimals: 1
    })],
    range: {
      'min': 0,
      'max': 100
    }
  });

  var tooltipSlider2 = document.getElementById('slider-tooltips-2');

  noUiSlider.create(tooltipSlider2, {
    start: [20, 80],
    direction: direction,
    orientation: 'vertical',
    tooltips: [false, wNumb({
      decimals: 1
    })],
    range: {
      'min': 0,
      'max': 100
    }
  });

  var tooltipSlider3 = document.getElementById('slider-tooltips-3');

  noUiSlider.create(tooltipSlider3, {
    start: [20, 80],
    direction: direction,
    orientation: 'vertical',
    tooltips: [false, wNumb({
      decimals: 1
    })],
    range: {
      'min': 0,
      'max': 100
    }
  });

  // Direction top to bottom
  var directionTopBottom1 = document.getElementById('slider-direction-top-bottom-1');

  noUiSlider.create(directionTopBottom1, {
    range: range_all_sliders,
    start: 30,
    direction: direction,
    connect: 'lower',
    orientation: 'vertical',
    pips: {
      mode: 'range',
      density: 5
    }
  });

  var directionTopBottom2 = document.getElementById('slider-direction-top-bottom-2');

  noUiSlider.create(directionTopBottom2, {
    range: range_all_sliders,
    start: 50,
    direction: direction,
    connect: 'lower',
    orientation: 'vertical',
    pips: {
      mode: 'range',
      density: 5
    }
  });

  var directionTopBottom3 = document.getElementById('slider-direction-top-bottom-3');

  noUiSlider.create(directionTopBottom3, {
    range: range_all_sliders,
    start: 70,
    direction: direction,
    connect: 'lower',
    orientation: 'vertical',
    pips: {
      mode: 'range',
      density: 5
    }
  });



  // Limit
  var verticalLimitSlider1 = document.getElementById('vertical-limit-1');

  noUiSlider.create(verticalLimitSlider1, {
    start: [40, 60],
    direction: direction,
    orientation: 'vertical',
    limit: 40,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 0,
      'max': 100
    }
  });

  var verticalLimitSlider2 = document.getElementById('vertical-limit-2');

  noUiSlider.create(verticalLimitSlider2, {
    start: [35, 65],
    direction: direction,
    orientation: 'vertical',
    limit: 40,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 0,
      'max': 100
    }
  });

  var verticalLimitSlider3 = document.getElementById('vertical-limit-3');

  noUiSlider.create(verticalLimitSlider3, {
    start: [30, 70],
    direction: direction,
    orientation: 'vertical',
    limit: 50,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 0,
      'max': 100
    }
  });

  var verticalLimitSlider4 = document.getElementById('vertical-limit-4');

  noUiSlider.create(verticalLimitSlider4, {
    start: [25, 75],
    direction: direction,
    orientation: 'vertical',
    limit: 50,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 0,
      'max': 100
    }
  });

  var verticalLimitSlider5 = document.getElementById('vertical-limit-5');

  noUiSlider.create(verticalLimitSlider5, {
    start: [20, 80],
    direction: direction,
    orientation: 'vertical',
    limit: 70,
    behaviour: 'drag',
    connect: true,
    range: {
      'min': 0,
      'max': 100
    }
  });

  /****************************************************
	*				Horizontal Slider With Time				*
	****************************************************/
  // Create a new date from a string, return as a timestamp.
  function timestamp(str) {
    return new Date(str).getTime();
  }
  1
  function timestamp(str) {
    2
    return new Date(str).getTime();
    3
  }

  // weekdays and months

  var weekdays = [
    "Sunday", "Monday", "Tuesday",
    "Wednesday", "Thursday", "Friday",
    "Saturday"
  ];

  var months = [
    "January", "February", "March",
    "April", "May", "June", "July",
    "August", "September", "October",
    "November", "December"
  ];

  // Append a suffix to dates.
  // Example: 23 => 23rd, 1 => 1st.
  function nth(d) {
    if (d > 3 && d < 21) return 'th';
    switch (d % 10) {
      case 1:
        return "st";
      case 2:
        return "nd";
      case 3:
        return "rd";
      default:
        return "th";
    }
  }

  // Create a string representation of the date.
  function formatDate(date) {
    return weekdays[date.getDay()] + ", " +
      date.getDate() + nth(date.getDate()) + " " +
      months[date.getMonth()] + " " +
      date.getFullYear();
  }
  var date = new Date();

  // set previous month
  var previousMonth = new Date();
  previousMonth.setMonth(previousMonth.getMonth() - 1);

  var dateSlider = document.getElementById('slider-with-date');

  // nouislider settings
  noUiSlider.create(dateSlider, {
    behaviour: 'tap',
    connect: true,
    range: {
      min: timestamp('2016-06-01') + 24 * 60 * 60 * 1000,
      max: timestamp(date)
    },
    step: 1 * 24 * 60 * 60 * 1000,
    start: [timestamp(previousMonth), timestamp(date)],
    direction: direction,
  });

  // get range infos at html
  var dateValues = [
    document.getElementById('event-start'), document.getElementById('event-end')
  ];

  dateSlider.noUiSlider.on('update', function (values, handle) {
    dateValues[handle].innerHTML = formatDate(new Date(+values[handle]));
  });

	/****************************************************
	*				 Slider With Input				*
	****************************************************/

  var select = document.getElementById('slider-select');

  // Append the option elements
  for (var i = -20; i <= 40; i++) {

    var option = document.createElement("option");
    option.text = i;
    option.value = i;

    select.appendChild(option);
  }

  var sliderWithInput = document.getElementById('slider-with-input');

  noUiSlider.create(sliderWithInput, {
    start: [10, 30],
    direction: direction,
    connect: true,
    range: {
      'min': -20,
      'max': 40
    }
  });

  var inputNumber = document.getElementById('slider-input-number');

  sliderWithInput.noUiSlider.on('update', function (values, handle) {

    var value = values[handle];

    if (handle) {
      inputNumber.value = value;
    } else {
      select.value = Math.round(value);
    }
  });

  select.addEventListener('change', function () {
    sliderWithInput.noUiSlider.set([this.value, null]);
  });

  inputNumber.addEventListener('change', function () {
    sliderWithInput.noUiSlider.set([null, this.value]);
  });

});
