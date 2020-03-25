<?php

namespace Dcat\Admin\Widgets\Metrics;

/**
 * 趋势图卡片
 *
 * @package Dcat\Admin\Widgets\Metrics
 */
class SparklineCard extends Card
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
            'curve' => 'straight'
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

        // 初始化图表
        $this->setUpChart();
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
<div class="card-content" style="position: relative;width: 100%">
    {$this->renderChart()}
</div>
HTML;
    }
}
