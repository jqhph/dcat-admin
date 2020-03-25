<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;

class RadialBarChartCard extends Card
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
    protected $chartHeight = 150;

    /**
     * 内容宽度.
     *
     * @var array
     */
    protected $contentWidth = [2, 10];

    /**
     * @var int
     */
    protected $chartMarginBottom = -10;

    /**
     * 初始化
     */
    public function init()
    {
        parent::init();

        $this->useChart();

        $this->chart->style("margin-bottom: {$this->chartMarginBottom}px;");
    }

    /**
     * 图表默认配置.
     *
     * @return array
     */
    protected function defaultChartOptions()
    {
        $gradientColor = Admin::color()->success();
        $labelColor = '#99a2ac';

        return [
            'chart' => [
                'type' => 'radialBar',
            ],
            'plotOptions' => [
                'radialBar' => [
                    'size' => 150,
                    'startAngle' => -150,
                    'endAngle' => 150,
                    'offsetY' => 20,
                    'hollow' => [
                        'size' => '65%',
                    ],
                    'track' => [
                        'background' => '#fff',
                        'strokeWidth' => '100%',
                    ],
                    'dataLabels' => [
                        'value' => [
                            'offsetY' => 30,
                            'color' => $labelColor,
                            'fontSize' => '2rem'
                        ]
                    ]
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'horizontal',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => [$gradientColor],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100]
                ],
            ],
            'stroke' => [
                'dashArray' => 8
            ],
        ];
    }

    /**
     * 设置卡片底部内容.
     *
     * @param string|\Closure|Renderable $value
     *
     * @return $this
     */
    public function footer($value)
    {
        $this->options['footer'] = $value;

        return $this;
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
     * @return string
     */
    public function renderFooter()
    {
        return Helper::render($this->options['footer']);
    }

    /**
     * @return string
     */
    public function renderContent()
    {
        $content = null;

        if ($this->contentWidth[0]) {
            $content = parent::renderContent();

            $content = <<<HTML
<div class="metric-content col-sm-{$this->contentWidth[0]} d-flex flex-column flex-wrap text-center">
    {$content}
</div>
HTML;
        }


        return <<<HTML
<div class="card-content">
    <div class="card-body pt-0">
        <div class="row">
            {$content}
            
            <div class="col-sm-{$this->contentWidth[1]} d-flex justify-content-center">
                {$this->renderChart()}
            </div>
        </div>
        <div class="metric-footer d-flex justify-content-between">
            {$this->renderFooter()}
        </div>
    </div>
</div>
HTML;
    }
}
