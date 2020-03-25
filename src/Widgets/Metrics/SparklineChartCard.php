<?php

namespace Dcat\Admin\Widgets\Metrics;

class SparklineChartCard extends Card
{
    /**
     * 趋势图图表默认配置
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
                ]
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
            'curve' => 'smooth'
        ],
        'fill' => [
            'opacity' => 0.1,
            'type' => 'solid',
        ],
    ];

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
     * 设置线条为直线.
     *
     * @return $this
     */
    public function chartStraight()
    {
        return $this->chartOption('stroke.curve', 'straight');
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
