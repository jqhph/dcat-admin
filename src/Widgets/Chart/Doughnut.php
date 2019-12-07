<?php

namespace Dcat\Admin\Widgets\Chart;

use Dcat\Admin\Admin;
use Illuminate\Support\Arr;

/**
 * @see https://www.chartjs.org/docs/latest/charts/doughnut.html
 */
class Doughnut extends Pie
{
    protected $type = 'doughnut';
}
