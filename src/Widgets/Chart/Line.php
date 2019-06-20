<?php

namespace Dcat\Admin\Widgets\Chart;

/**
 * @see https://www.chartjs.org/docs/latest/charts/line.html
 */
class Line extends Chart
{
    use ScaleSetting;

    public $colors = [
        'rgba(64,153,222, 1)', // primary
        'rgba(38,166,154, 1)', // tear
        'rgba(121,134,203, 1)', // purple
        'rgba(33,185,120, 1)', // success
        'rgba(249,144,55, 1)', // orange
        'rgba(41,126,192, 1)', // primary darker
        'rgba(245,87,59, 1)', // red
        'rgba(24,103,192, 1)', // blue
        'rgba(242,203,34, 1)', // yellow
        'rgba(143,193,93, 1)', // green
        'rgba(89,169,248, 1)', // custom
        'rgba(129,199,132, 1)', // silver
        'rgba(228,113,222, 1)', // lime
    ];

    public $backgroundColors = [
        'rgba(64,153,222, 0.1)', // primary
        'rgba(38,166,154, 0.1)', // tear
        'rgba(121,134,203, 0.2)', // purple
        'rgba(33,185,120, 0.12)', // success
        'rgba(249,144,55, 0.1)', // orange
        'rgba(41,126,192, 0.1)', // primary darker
        'rgba(245,87,59, 0.1)', // red
        'rgba(24,103,192, 0.1)', // blue
        'rgba(242,203,34, 0.1)', // yellow
        'rgba(143,193,93, 0.1)', // green
        'rgba(89,169,248, 0.1)', // custom
        'rgba(129,199,132, 0.1)', // silver
        'rgba(228,113,222, 0.1)', // lime
    ];

    protected $fillBackground = false;
    protected $opaque = false;

    public function fillBackground(bool $opaque = false)
    {
        $this->fillBackground = true;
        $this->opaque = $opaque;

        return $this;
    }

    protected function fillColor(array $colors = [])
    {
        $colors = $colors ?: $this->colors;
        $bgColors = $this->opaque ? $colors : $this->backgroundColors;

        foreach ($this->data['datasets'] as &$item) {
            if (empty($item['strokeColor'])) {
                $color = array_shift($colors);
                $bgColor = array_shift($bgColors);

                $item['borderColor'] = $color;
                $item['backgroundColor'] = $this->fillBackground ? $bgColor : 'rgba(255, 255, 255, 0.1)';
            }
        }
    }

    public function showLines(bool $val)
    {
        return $this->options(['showLines' => $val]);
    }

    public function spanGaps(bool $val)
    {
        return $this->options(['spanGaps' => $val]);
    }
}
