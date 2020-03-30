<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;

/**
 * 单环形图卡片.
 *
 * Class SingleRound
 */
class SingleRound extends Round
{
    /**
     * 图表下间距.
     *
     * @var int
     */
    protected $chartMarginBottom = 5;

    /**
     * 图表默认配置.
     *
     * @return array
     */
    protected function defaultChartOptions()
    {
        $color = Admin::color();

        $colors = [$color->success()];
        $gradientToColors = [$color->tear1()];
        $strokColor = $color->gray();

        return [
            'chart' => [
                'type' => 'radialBar',
                'sparkline' => [
                    'enabled' => true,
                ],
                'dropShadow' => [
                    'enabled' => true,
                    'blur' => 3,
                    'left' => 1,
                    'top' => 1,
                    'opacity' => 0.1,
                ],
            ],
            'colors' => $colors,
            'plotOptions' => [
                'radialBar' => [
                    'size' => 70,
                    'startAngle' => -180,
                    'endAngle' => 179,
                    'hollow' => [
                        'size' => '74%',
                    ],
                    'track' => [
                        'background' => $strokColor,
                        'strokeWidth' => '50%',
                    ],
                    'dataLabels' => [
                        'name' => [
                            'show' => false,
                        ],
                        'value' => [
                            'offsetY' => 14,
                            'color' => $strokColor,
                            'fontSize' => '2.8rem',
                        ],
                    ],
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'horizontal',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => $gradientToColors,
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'series' => [100],
            'stroke' => [
                'lineCap' => 'round',
            ],
        ];
    }
}
