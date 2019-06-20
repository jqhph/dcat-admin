<?php

namespace Dcat\Admin\Widgets;

class Colors
{
    public static $default = [
        'green' => [
            'rgba(33,185,120, 1)',
            'rgba(33,185,120, 0.1)',
        ],
        'primary' => [
            'rgba(64,153,222, 1)',
            'rgba(64,153,222, 0.1)',
        ],
        'purple' => [
            'rgba(91, 105, 188, 1)',
            'rgba(91,105,188,0.1)',
        ],

        'red' => [
            'rgba(255,91,91, 1)',
            'rgba(255,91,91,0.1)',
        ],

        'custom' => [
            'rgba(89,169,248, 1)',
            'rgba(89,169,248,0.1)',
        ],

        'tear' => [
            'rgba(38,166,154, 1)',
            'rgba(38,166,154,0.1)',
        ],

        'blue' => [
            'rgba(0,126,229, 1)',
            'rgba(0,126,229,0.1)',
        ],
    ];

    public static $charts = [
        'blue' => [
            'rgba(64,153,222,.5)', // primary
            'rgba(64,153,222,.85)', // primary
            '#007ee5', // blue
            '#59a9f8', // custom
            'rgba(121,134,203, 1)', // purple
            '#6474D7', // purple darker
            '#8FC15D', // green
            '#21b978', // success
            '#47C1BF', // tear
            '#F2CB22', // yellow
            '#F99037', // orange
            '#F5573B', // red
            '#9C6ADE', // another purple
            '#ff8acc', // pink
            '#297ec0', // primary darker
            '#483D8B', // blue darker
        ],

       'green' => [ // 绿色系
           'rgba(64,153,222,.5)', // primary
            '#21b978', // success
            '#47C1BF', // tear
            '#8FC15D', // green
        ],

        'orange' => [ // 橙色系
            'rgba(64,153,222,.5)', // primary
            '#F99037', // orange
            '#F5573B', // red
            '#F2CB22', // yellow
        ],

//        'red2' => [ // 红色系
//            '#F99037', // orange
//            '#F5573B', // red
//            '#F2CB22', // yellow
//            '#ff5b5b', // danger
//        ],

        'purple' => [
            'rgba(64,153,222,.5)', // primary
            'rgba(121,134,203, 1)', // purple
            '#6474D7', // purple darker
            '#9C6ADE', // another purple
        ],


    ];
}
