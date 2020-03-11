/*=========================================================================================
    File Name: chart-echart.js
    Description: echarts examples
    ----------------------------------------------------------------------------------------
    Item name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(window).on("load", function(){

    var $dark_green = '#4ea397';
    var $green = '#22c3aa';
    var $light_green = '#7bd9a5';
    var $lighten_green = '#a8e7d2';



    // Bar chart
    // ------------------------------

    var barChart = echarts.init(document.getElementById('bar-chart'));


    // var i;
    function randomize() {
        return Math.round(300 + Math.random() * 700) / 10
    };

    var barChartoption = {
        legend: {},
        tooltip: {},
        dataset: {
            source: [
                ['product', '2015', '2016', '2017'],
                ['Matcha Latte', randomize(), randomize(), randomize()],
                ['Milk Tea', randomize(), randomize(), randomize()],
                ['Cheese Cocoa', randomize(), randomize(), randomize()],
                ['Walnut Brownie', randomize(), randomize(), randomize()],
            ],
        },


        xAxis: {
            type: 'category',
            splitLine: { show: true },
        },
        yAxis: {},
        // Declare several bar series, each will be mapped
        // to a column of dataset.source by default.
        series: [
            {
                type: 'bar',
                itemStyle: {color: $dark_green},
            },
            {
                type: 'bar',
                itemStyle: {color: $green},
            },
            {
                type: 'bar',
                itemStyle: {color: $light_green},
            }
        ]
    };
    barChart.setOption(barChartoption);



    // Line chart
    // ------------------------------

    var lineChart = echarts.init(document.getElementById('line-chart'));

    data = [["2000-06-05",116],["2000-06-06",129],["2000-06-07",135],["2000-06-08",86],["2000-06-09",73],["2000-06-10",85],["2000-06-11",73],["2000-06-12",68],["2000-06-13",92],["2000-06-14",130],["2000-06-15",245],["2000-06-16",139],["2000-06-17",115],["2000-06-18",111],["2000-06-19",309],["2000-06-20",206],["2000-06-21",137],["2000-06-22",128],["2000-06-23",85],["2000-06-24",94],["2000-06-25",71],["2000-06-26",106],["2000-06-27",84],["2000-06-28",93],["2000-06-29",85],["2000-06-30",73],["2000-07-01",83],["2000-07-02",125],["2000-07-03",107],["2000-07-04",82],["2000-07-05",44],["2000-07-06",72],["2000-07-07",106],["2000-07-08",107],["2000-07-09",66],["2000-07-10",91],["2000-07-11",92],["2000-07-12",113],["2000-07-13",107],["2000-07-14",131],["2000-07-15",111],["2000-07-16",64],["2000-07-17",69],["2000-07-18",88],["2000-07-19",77],["2000-07-20",83],["2000-07-21",111],["2000-07-22",57],["2000-07-23",55],["2000-07-24",60]];

    var dateList = data.map(function (item) {
        return item[0];
    });
    var valueList = data.map(function (item) {
        return item[1];
    });

    var lineChartoption = {

        // Make gradient line here
        visualMap: [{
            show: false,
            type: 'continuous',
            seriesIndex: 0,
            min: 0,
            max: 400,
            color: [$dark_green, $lighten_green]
        }],
        tooltip: {
            trigger: 'axis'
        },
        xAxis: [{
            data: dateList,
            splitLine: {show: true}
        }],
        yAxis: [{
            splitLine: {show: false}
        }],
        series: [{
            type: 'line',
            showSymbol: false,
            data: valueList
        }]
    };

    lineChart.setOption(lineChartoption);


    // Pie chart
    // ------------------------------

    var pieChart = echarts.init(document.getElementById('pie-chart'));

    var pieChartoption = {
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: ['Direct interview', 'Email marketing', 'Alliance advertising', 'Video ad', 'Search engine']
        },
        series : [
            {
                name: 'Access source',
                type: 'pie',
                radius : '55%',
                center: ['50%', '60%'],
                color: ['#FF9F43','#28C76F','#EA5455','#87ceeb','#7367F0'],
                data: [
                  {value: 335, name: 'Direct interview'},
                  {value: 310, name: 'Email marketing'},
                  {value: 234, name: 'Alliance advertising'},
                  {value: 135, name: 'Video ad'},
                  {value: 1548, name: 'Search engine'}
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ],
    };

    pieChart.setOption(pieChartoption);


    // Scatter chart
    // ------------------------------

    var scatterChart = echarts.init(document.getElementById('scatter-chart'));

    var data = [
        [[28604,77,17096869,'Australia',1990],[31163,77.4,27662440,'Canada',1990],[1516,68,1154605773,'China',1990],[13670,74.7,10582082,'Cuba',1990],[28599,75,4986705,'Finland',1990],[29476,77.1,56943299,'France',1990],[31476,75.4,78958237,'Germany',1990],[28666,78.1,254830,'Iceland',1990],[1777,57.7,870601776,'India',1990],[29550,79.1,122249285,'Japan',1990],[2076,67.9,20194354,'North Korea',1990],[12087,72,42972254,'South Korea',1990],[24021,75.4,3397534,'New Zealand',1990],[43296,76.8,4240375,'Norway',1990],[10088,70.8,38195258,'Poland',1990],[19349,69.6,147568552,'Russia',1990],[10670,67.3,53994605,'Turkey',1990],[26424,75.7,57110117,'United Kingdom',1990],[37062,75.4,252847810,'United States',1990]],
        [[44056,81.8,23968973,'Australia',2015],[43294,81.7,35939927,'Canada',2015],[13334,76.9,1376048943,'China',2015],[21291,78.5,11389562,'Cuba',2015],[38923,80.8,5503457,'Finland',2015],[37599,81.9,64395345,'France',2015],[44053,81.1,80688545,'Germany',2015],[42182,82.8,329425,'Iceland',2015],[5903,66.8,1311050527,'India',2015],[36162,83.5,126573481,'Japan',2015],[1390,71.4,25155317,'North Korea',2015],[34644,80.7,50293439,'South Korea',2015],[34186,80.6,4528526,'New Zealand',2015],[64304,81.6,5210967,'Norway',2015],[24787,77.3,38611794,'Poland',2015],[23038,73.13,143456918,'Russia',2015],[19360,76.5,78665830,'Turkey',2015],[38225,81.4,64715810,'United Kingdom',2015],[53354,79.1,321773631,'United States',2015]]
    ];

    var scatterChartoption = {

        legend: {
            right: 10,
            data: ['1990', '2015']
        },
        xAxis: {
            splitLine: {
                lineStyle: {
                    type: 'dashed'
                }
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    type: 'dashed'
                }
            },
            scale: true
        },
        series: [{
            name: '1990',
            data: data[0],
            type: 'scatter',
            symbolSize: function (data) {
                return Math.sqrt(data[2]) / 5e2;
            },
            label: {
                emphasis: {
                    show: true,
                    formatter: function (param) {
                        return param.data[3];
                    },
                    position: 'top'
                }
            },
            itemStyle: {
                normal: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(120, 36, 50, 0.5)',
                    shadowOffsetY: 5,
                    color: new echarts.graphic.RadialGradient(0.4, 0.3, 1, [{
                        offset: 0,
                        color: 'rgb(251, 118, 123)'
                    }, {
                        offset: 1,
                        color: 'rgb(204, 46, 72)'
                    }])
                }
            }
        }, {
            name: '2015',
            data: data[1],
            type: 'scatter',
            symbolSize: function (data) {
                return Math.sqrt(data[2]) / 5e2;
            },
            label: {
                emphasis: {
                    show: true,
                    formatter: function (param) {
                        return param.data[3];
                    },
                    position: 'top'
                }
            },
            itemStyle: {
                normal: {
                    shadowBlur: 10,
                    shadowColor: 'rgba(25, 100, 150, 0.5)',
                    shadowOffsetY: 5,
                    color: new echarts.graphic.RadialGradient(0.4, 0.3, 1, [{
                        offset: 0,
                        color: 'rgb(129, 227, 238)'
                    }, {
                        offset: 1,
                        color: 'rgb(25, 183, 207)'
                    }])
                }
            }
        }]
    };

    scatterChart.setOption(scatterChartoption);


    // Polar chart
    // ------------------------------

    var polarChart = echarts.init(document.getElementById('polar-chart'));

    var data = [];

    for (var i = 0; i <= 360; i++) {
        var t = i / 180 * Math.PI;
        var r = Math.sin(2 * t) * Math.cos(2 * t);
        data.push([r, i]);
    }

    var polarChartoption = {
        legend: {
            data: ['line']
        },
        polar: {
            center: ['50%', '54%']
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross'
            }
        },
        angleAxis: {
            type: 'value',
            startAngle: 0
        },
        radiusAxis: {
            min: 0
        },
        series: [{
            coordinateSystem: 'polar',
            name: 'line',
            type: 'line',
            showSymbol: false,
            data: data
        }],
        animationDuration: 2000
    };

    polarChart.setOption(polarChartoption);


    // Radar chart
    // ------------------------------

    var radarChart = echarts.init(document.getElementById('radar-chart'));

    var radarChartoption = {
        tooltip: {},
        radar: {
            indicator: [
                { name: 'Attack', max: 20 },
                { name: 'Defensive', max: 20 },
                { name: 'Speed', max: 20 },
                { name: 'Power', max: 20 },
                { name: 'Endurance', max: 20 },
                { name: 'Agile', max: 20 }
            ]
        },
        series: [{
           name: 'Ability value',
            type: 'radar',
            data: [{ value: [19, 9, 18, 16, 16, 20] }]
        }]
    };

    radarChart.setOption(radarChartoption);

});
