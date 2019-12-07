<?php

namespace Dcat\Admin\Widgets\Chart;

/**
 * @see https://www.chartjs.org/docs/latest/charts/bar.html
 */
class Bar extends Chart
{
    use ScaleSetting;

    protected $type = 'bar';

    /**
     * Percent (0-1) of the available width each bar should be within the category width. 1.0 will take the whole category width and put the bars right next to each other.
     *
     * @param number $val default 0.9
     *
     * @return $this
     */
    public function barPercentage($val)
    {
        return $this->options(['barPercentage' => $val]);
    }

    /**
     * Percent (0-1) of the available width each category should be within the sample width.
     *
     * @param number $val default 0.8
     *
     * @return $this
     */
    public function categoryPercentage($val)
    {
        return $this->options(['categoryPercentage' => $val]);
    }

    public function barThickness($val)
    {
        return $this->options(['barThickness' => $val]);
    }

    public function maxBarThickness($val)
    {
        return $this->options(['maxBarThickness' => $val]);
    }

    public function minBarLength($val)
    {
        return $this->options(['minBarLength' => $val]);
    }
}
