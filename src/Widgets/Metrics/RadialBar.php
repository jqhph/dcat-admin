<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;

/**
 * 圆形图卡片.
 *
 * Class RadialBar
 */
class RadialBar extends Card
{
    /**
     * @var string|Renderable|\Closure
     */
    protected $footer;

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
    protected $chartHeight = 150;

    /**
     * 内容宽度.
     *
     * @var array
     */
    protected $contentWidth = [2, 10];

    /**
     * 图表位置是否靠右.
     *
     * @var bool
     */
    protected $chartPullRight = false;

    /**
     * 初始化.
     */
    protected function init()
    {
        parent::init();

        $this->useChart();
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
                    'size' => 200,
                    'startAngle' => -180,
                    'endAngle' => 175,
                    'offsetY' => 0,
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
                            'fontSize' => '2rem',
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
                    'gradientToColors' => [$gradientColor],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'stroke' => [
                'dashArray' => 8,
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
        $this->footer = $value;

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
     * 图表位置靠右.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function chartPullRight(bool $value = true)
    {
        $this->chartPullRight = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function renderFooter()
    {
        return Helper::render($this->footer);
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
<div class="metric-content col-sm-{$this->contentWidth[0]}">
    {$content}
</div>
HTML;
        }

        $justifyClass = $this->chartPullRight ? 'justify-content-between' : 'justify-content-center';

        return <<<HTML
<div class="card-content">
    <div class="row">
        {$content}
        
        <div class="col-sm-{$this->contentWidth[1]} d-flex {$justifyClass}">
            <div></div>
            <div>{$this->renderChart()}</div>
        </div>
    </div>
    <div class="metric-footer">
        {$this->renderFooter()}
    </div>
</div>
HTML;
    }
}
