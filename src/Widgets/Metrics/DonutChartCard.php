<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;

class DonutChartCard extends Card
{
    /**
     * @var int
     */
    protected $chartHeight = 140;

    /**
     * 内容宽度.
     *
     * @var array
     */
    protected $contentWidth = [6, 6];

    /**
     * 趋势图图表默认配置
     *
     * @return  array
     */
    protected function defaultChartOptions()
    {
        $color = Admin::color();

        $colors = [$color->primary(), $color->alpha('blue2', 0.5), $color->orange2()];

        return [
            'chart' => [
                'type' => 'donut',
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'colors' => $colors,
            'legend' => [
                'show' => false,
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'width' => 0,
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '75%'
                    ]
                ]
            ]
        ];
    }

    /**
     * 初始化
     */
    protected function init()
    {
        parent::init();

        // 使用图表s
        $this->useChart();
    }

    /**
     * 设置内容宽度.
     *
     * @param int $left
     * @param int $right
     *
     * @return $this
     */
    public function contentWidth(int $left, int $right)
    {
        $this->contentWidth = [$left, $right];

        return $this;
    }

    /**
     * 渲染内容，加上图表.
     *
     * @return string
     */
    public function renderContent()
    {
        $content = parent::renderContent();

        return <<<HTML
<div class="d-flex row justify-content-between">
    <div class="col-sm-{$this->contentWidth[0]} justify-content-center">
        {$content}
    </div>
    <div class="col-sm-{$this->contentWidth[1]}" style="margin-right: -15px;">
        {$this->renderChart()}
    </div>
</div>
HTML;
    }
}
