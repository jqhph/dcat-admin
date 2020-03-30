<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\JavaScript;

/**
 * 环形图卡片.
 *
 * Class Round
 */
class Round extends RadialBar
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
     * 卡片高度.
     *
     * @var int
     */
    protected $height = 250;

    /**
     * 图表高度.
     *
     * @var int
     */
    protected $chartHeight = 210;

    /**
     * 内容宽度.
     *
     * @var array
     */
    protected $contentWidth = [5, 7];

    /**
     * 图表上间距.
     *
     * @var int
     */
    protected $chartMarginTop = -10;

    /**
     * 图表下间距.
     *
     * @var int
     */
    protected $chartMarginBottom = -20;

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
                    'size' => 90,
                    'hollow' => [
                        'size' => '20%',
                    ],
                    'track' => [
                        'strokeWidth' => '100%',
                        'margin' => 13,
                    ],
                    'dataLabels' => [
                        'name' => [
                            'fontSize' => '14px',
                            'offsetY' => -2,
                        ],
                        'value' => [
                            'fontSize' => '12px',
                            'offsetY' => 4,
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
    public function chartRadialBarSize(int $size)
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
    public function chartRadialBarMargin(int $margin)
    {
        return $this->chartOption('plotOptions.radialBar.track.margin', $margin);
    }

    /**
     * 设置图表统计总数信息.
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

    /**
     * 设置图标 dataLabel name 的字体尺寸.
     *
     * @param mixed $size
     *
     * @return $this
     */
    public function chartLabelNameFontSize($size)
    {
        return $this->chartOption('plotOptions.radialBar.dataLabels.name.fontSize', $size);
    }

    /**
     * 设置图标 dataLabel name 的Y轴偏移量.
     *
     * @param mixed $size
     *
     * @return $this
     */
    public function chartLabelNameOffsetY(int $offset)
    {
        return $this->chartOption('plotOptions.radialBar.dataLabels.name.offsetY', $offset);
    }

    /**
     * 设置图标 dataLabel value 的字体尺寸.
     *
     * @param mixed $size
     *
     * @return $this
     */
    public function chartLabelValueFontSize($size)
    {
        return $this->chartOption('plotOptions.radialBar.dataLabels.value.fontSize', $size);
    }

    /**
     * 设置图标 dataLabel value 的Y轴偏移量.
     *
     * @param mixed $size
     *
     * @return $this
     */
    public function chartLabelValueOffsetY(int $offset)
    {
        return $this->chartOption('plotOptions.radialBar.dataLabels.value.offsetY', $offset);
    }
}
