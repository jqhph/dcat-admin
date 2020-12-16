<?php

namespace Dcat\Admin\Widgets\Metrics;

/**
 * 折/曲线图卡片.
 *
 * Class Line
 */
class Line extends Card
{
    /**
     * 图表默认高度.
     *
     * @var int
     */
    protected $chartHeight = 57;

    /**
     * 图表默认配置.
     *
     * @var array
     */
    protected $chartOptions = [
        'chart' => [
            'type' => 'area',
            'toolbar' => [
                'show' => false,
            ],
            'sparkline' => [
                'enabled' => true,
            ],
            'grid' => [
                'show' => false,
                'padding' => [
                    'left' => 0,
                    'right' => 0,
                ],
            ],
        ],
        'tooltip' => [
            'x' => [
                'show' => false,
            ],
        ],
        'xaxis' => [
            'labels' => [
                'show' => false,
            ],
            'axisBorder' => [
                'show' => false,
            ],
        ],
        'yaxis' => [
            'y' => 0,
            'offsetX' => 0,
            'offsetY' => 0,
            'padding' => ['left' => 0, 'right' => 0],
        ],
        'dataLabels' => [
            'enabled' => false,
        ],
        'stroke' => [
            'width' => 2.5,
            'curve' => 'smooth',
        ],
        'fill' => [
            'opacity' => 0.1,
            'type' => 'solid',
        ],
    ];

    /**
     * 初始化.
     */
    protected function init()
    {
        parent::init();

        // 使用图表
        $this->useChart();

        // 兼容图表显示不全问题
        $this->chart->style('margin-right:-6px;');
    }

    /**
     * 设置线条为直线.
     *
     * @return $this
     */
    public function chartStraight()
    {
        return $this->chartOption('stroke.curve', 'straight');
    }

    /**
     * 设置线条为曲线.
     *
     * @return $this
     */
    public function chartSmooth()
    {
        return $this->chartOption('stroke.curve', 'smooth');
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
{$content}
<div class="card-content">
    {$this->renderChart()}
</div>
HTML;
    }
}
