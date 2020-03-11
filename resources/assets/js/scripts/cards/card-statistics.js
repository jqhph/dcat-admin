/*=========================================================================================
    File Name: card-statistics.js
    Description: Card-statistics page content with Apexchart Examples
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(window).on("load", function(){

  var $primary = '#7367F0';
  var $success = '#28C76F';
  var $danger = '#EA5455';
  var $warning = '#FF9F43';
  var $primary_light = '#A9A2F6';
  var $success_light = '#55DD92';
  var $warning_light = '#ffc085';

    // Subscribed Gained Chart
    // ----------------------------------

    var gainedChartoptions = {
        chart: {
            height: 100,
            type: 'area',
            toolbar:{
              show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$primary],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Subscribers',
            data: [28, 40, 36, 52, 38, 60, 55]
        }],

        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: false }
        },
    }

    var gainedChart = new ApexCharts(
        document.querySelector("#line-area-chart-1"),
        gainedChartoptions
    );

    gainedChart.render();



    // Revenue Generated Chart
    // ----------------------------------

    var revenueChartoptions = {
        chart: {
            height: 100,
            type: 'area',
            toolbar:{
              show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$success],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Revenue',
            data: [350, 275, 400, 300, 350, 300, 450]
        }],

        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: false }
        },
    }

    var revenueChart = new ApexCharts(
        document.querySelector("#line-area-chart-2"),
        revenueChartoptions
    );

    revenueChart.render();


    // Quaterly Sales Chart
    // ----------------------------------

    var salesChartoptions = {
        chart: {
            height: 100,
            type: 'area',
            toolbar:{
              show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$danger],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Sales',
            data: [10, 15, 7, 12, 3, 16]
        }],

        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: false }
        },
    }

    var salesChart = new ApexCharts(
        document.querySelector("#line-area-chart-3"),
        salesChartoptions
    );

    salesChart.render();

    // Order Received Chart
    // ----------------------------------

    var orderChartoptions = {
        chart: {
            height: 100,
            type: 'area',
            toolbar:{
              show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$warning],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2.5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.9,
                opacityFrom: 0.7,
                opacityTo: 0.5,
                stops: [0, 80, 100]
            }
        },
        series: [{
            name: 'Orders',
            data: [10, 15, 8, 15, 7, 12, 8]
        }],

        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: false }
        },
    }

    var orderChart = new ApexCharts(
        document.querySelector("#line-area-chart-4"),
        orderChartoptions
    );

    orderChart.render();


    // Site Traffic Chart
    // ----------------------------------

    var trafficChartoptions = {
        chart: {
            height: 100,
            type: 'line',
            dropShadow: {
                enabled: true,
                top: 5,
                left: 0,
                blur: 4,
                opacity: 0.10,
            },
            toolbar:{
              show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$primary],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                gradientToColors: [$primary_light],
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100]
            }
        },
        series: [{
            name: 'Traffic Rate',
            data: [150, 200, 125, 225, 200, 250]
        }],

        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: false }
        },
    }

    var trafficChart = new ApexCharts(
        document.querySelector("#line-area-chart-5"),
        trafficChartoptions
    );

    trafficChart.render();


    // Active Users Chart
    // ----------------------------------

    var userChartoptions = {
        chart: {
            height: 100,
            type: 'line',
            dropShadow: {
                enabled: true,
                top: 5,
                left: 0,
                blur: 4,
                opacity: 0.10,
            },
            toolbar:{
              show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$success],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                gradientToColors: [$success_light],
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100]
            }
        },
        series: [{
            name: 'Active Users',
            data: [750, 1000, 900, 1250, 1000, 1200, 1100]
        }],

        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: false }
        },
    }

    var userChart = new ApexCharts(
        document.querySelector("#line-area-chart-6"),
        userChartoptions
    );

    userChart.render();


    // News Letter Chart
    // ----------------------------------

    var newsletterChartoptions = {
        chart: {
            height: 100,
            type: 'line',
            dropShadow: {
                enabled: true,
                top: 5,
                left: 0,
                blur: 4,
                opacity: 0.10,
            },
            toolbar:{
              show: false,
            },
            sparkline: {
                enabled: true
            },
            grid: {
                show: false,
                padding: {
                    left: 0,
                    right: 0
                }
            },
        },
        colors: [$warning],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 5
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                gradientToColors: [$warning_light],
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100, 100, 100]
            }
        },
        series: [{
            name: 'Newsletter',
            data: [365, 390, 365, 400, 375, 400]
        }],

        xaxis: {
          labels: {
            show: false,
          },
          axisBorder: {
            show: false,
          }
        },
        yaxis: [{
            y: 0,
            offsetX: 0,
            offsetY: 0,
            padding: { left: 0, right: 0 },
        }],
        tooltip: {
            x: { show: false }
        },
    }

    var newsletterChart = new ApexCharts(
        document.querySelector("#line-area-chart-7"),
        newsletterChartoptions
    );

    newsletterChart.render();

});
