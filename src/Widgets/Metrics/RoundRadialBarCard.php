<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\JavaScript;
use Illuminate\Contracts\Support\Renderable;

class RoundRadialBarCard extends RadialBarCard
{
    /**
     * @var array
     */
    protected $options = [
        'icon'     => null,
        'title'    => null,
        'header'   => null,
        'content'  => null,
        'footer'   => null,
        'dropdown' => [],
    ];

    /**
     * @var int
     */
    protected $chartHeight = 230;

    /**
     * 内容宽度.
     *
     * @var array
     */
    protected $contentWidth = [5, 7];

    /**
     * @var int
     */
    protected $chartMarginBottom = -40;

    /**
     * 图表默认配置.
     *
     * @return array
     */
    protected function defaultChartOptions()
    {
        $color = Admin::color();

        $colors = [$color->primary(), $color->warning(), $color->danger()];

        return [
            'chart' => [
                'type' => 'radialBar',
            ],
            'colors' => $colors,
            'stroke' => [
                'lineCap' => 'round',
            ],
            'plotOptions' => [
                'radialBar' => [
                    'size' => 115,
                    'hollow' => [
                        'size' => '20%',
                    ],
                    'track' => [
                        'strokeWidth' => '100%',
                        'margin' => 14,
                    ],
                    'dataLabels' => [
                        'name' => [
                            'fontSize' => '14px',
                        ],
                        'value' => [
                            'fontSize' => '12px',
                        ],
                        'total' => [
                            'show' => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * 设置圆圈宽度.
     *
     * @param int $size
     *
     * @return $this
     */
    public function radialBarSize(int $size)
    {
        return $this->chartOption('plotOptions.radialBar.size', $size);
    }

    /**
     * 设置圆圈间距.
     *
     * @param int $margin
     *
     * @return $this
     */
    public function radialBarMargin(int $margin)
    {
        return $this->chartOption('plotOptions.radialBar.track.margin', $margin);
    }

    /**
     * 设置图表统计总数信息
     *
     * @param string $label
     * @param int $number
     *
     * @return $this
     */
    public function chartTotal(string $label, int $number)
    {
        return $this->chartOption('plotOptions.radialBar.dataLabels.total', [
            'show'      => true,
            'label'     => $label,
            'formatter' => JavaScript::make("function () { return {$number}; }"),
        ]);
    }
}
