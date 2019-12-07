<?php

namespace Dcat\Admin\Widgets\Chart;

/**
 * @see https://www.chartjs.org/docs/latest/charts/bubble.html
 */
class Bubble extends Chart
{
    use ScaleSetting;

    public $colors = [
        'rgba(64,153,222, 1)', // primary
        'rgba(121,134,203, 1)', // purple
        'rgba(33,185,120, 1)', // success
        'rgba(24,103,192, 1)', // blue
        'rgba(38,166,154, 1)', // tear
        'rgba(41,126,192, 1)', // primary darker
        'rgba(249,144,55, 1)', // orange
        'rgba(245,87,59, 1)', // red

        'rgba(242,203,34, 1)', // yellow
        'rgba(143,193,93, 1)', // green
        'rgba(89,169,248, 1)', // custom
        'rgba(129,199,132, 1)', // silver
        'rgba(228,113,222, 1)', // lime
    ];

    protected $type = 'bubble';
}
