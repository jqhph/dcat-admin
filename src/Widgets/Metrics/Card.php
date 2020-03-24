<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Traits\FromApi;
use Dcat\Admin\Widgets\Widget;
use Illuminate\Support\Str;

class Card extends Widget
{
    use FromApi;

    /**
     * @var string
     */
    protected $view = 'admin::widgets.metrics.card';

    /**
     * @var array
     */
    protected $options = [
        'icon' => 'feather icon-users',
        'content' => '',
        'style' => 'primary',
        'ranges' => [],
        'chartHeight' => 70,
        'chart' => [
            'chart' => [
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
            ],
        ]
    ];

    /**
     * @var Chart
     */
    protected $chart;

    /**
     * @var \Closure
     */
    protected $chartCallback;

    public function __construct($icon = 'feather icon-users', $contents = null)
    {
        $this->icon($icon);
        $this->content($contents);

        $this->init();
    }

    /**
     * 初始化
     */
    public function init()
    {
        $this->id('metric-card-'.Str::random(8));
        $this->class('card');
    }

    /**
     * 设置图表
     */
    public function setUpChart()
    {
        $chart = $this->chart ?: ($this->chart = Chart::make());

        // 设置图表高度
        if (empty($this->options['chart']['chart']['height'])) {
            $this->options['chart']['chart']['height'] = $this->options['chartHeight'];
        }

        // 颜色
        $chart->colors(Admin::color()->get($this->options['style']));
        // 匹配选项
        $chart->options($this->options['chart']);

        if ($callback = $this->chartCallback) {
            $callback($chart);
        }

        $this->chart = $this->variables['chart'] = $chart;
    }

    /**
     * 设置卡片内容.
     *
     * @param string $contents
     *
     * @return $this
     */
    public function content($contents)
    {
        $this->options['content'] = $contents;

        return $this;
    }

    /**
     * 设置图标
     *
     * @param string $icon
     *
     * @return $this
     */
    public function icon(?string $icon)
    {
        $this->options['icon'] = $icon;

        return $this;
    }

    /**
     * 设置主题色.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style)
    {
        $this->options['style'] = $style;

        return $this;
    }

    /**
     * 设置卡片的下拉菜单选项.
     *
     * @param array $items
     *
     * @return $this
     */
    public function dropdown(array $items)
    {
        $this->options['dropdown'] = $items;

        return $this;
    }

    /**
     * 设置最小高度
     *
     * @param string|int $value
     *
     * @return $this
     */
    public function minHeight($value)
    {
        if (is_numeric($value)) {
            $value .= 'px';
        }

        return $this->appendHtmlAttribute('style', "min-height:{$value};");
    }

    /**
     * 设置图表高度
     *
     * @param int $number
     *
     * @return $this
     */
    public function chartHeight(int $number)
    {
        $this->setUpChart();

        $this->options['chartHeight'] = $number;

        return $this;
    }

    /**
     * 设置图表.
     *
     * @param array|\Closure $options
     *
     * @return $this
     */
    public function chart($options = [])
    {
        if ($options instanceof \Closure) {
            $this->chartCallback = $options;
        } else {
            $this->options['chart'] = array_merge(
                $this->options['chart'],
                Helper::array($options)
            );
        }

        $this->setUpChart();

        return $this;
    }

    /**
     * js代码.
     *
     * @return mixed
     */
    public function script()
    {
        if (! $this->allowBuildRequest()) {
            return;
        }

        $id = $this->id();

        // 开启loading效果
        $this->fetching(
            <<<JS
var \$card = $('#{$id}');
\$card.loading();
JS
        );

        $this->fetched(
            <<<'JS'
$card.loading(false);            
$card.find('.metric-content').html(response.content)
JS
        );

        $clickable = "#{$id} .dropdown .select-option";

        $cardRequestScript = '';

        if ($this->chart) {
            $this->chart->merge($this)->click($clickable);
        } else {
            $this->click($clickable);

            $cardRequestScript = $this->buildRequestScript();
        }

        // 按钮显示选中文本
        return <<<JS
$('{$clickable}').click(function () {
    $(this).parents('.dropdown').find('.btn').html($(this).text());
});

{$cardRequestScript}
JS;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->script = $this->script();

        return parent::render(); // TODO: Change the autogenerated stub
    }

    /**
     * 返回卡片数据结果.
     *
     * @return array
     */
    public function result()
    {
        return [
            'status'  => 1,
            'content' => Helper::render($this->options['content']),
            'options' => $this->chart ? $this->chart->getOptions() : [],
        ];
    }
}
