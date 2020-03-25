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
        'icon' => null,
        'title' => null,
        'header' => null,
        'content' => null,
        'dropdown' => [],
    ];

    /**
     * @var string 
     */
    protected $style = 'primary';

    /**
     * @var int 
     */
    protected $chartHeight = 70;

    /**
     * @var array 
     */
    protected $chartOptions = [];

    /**
     * @var Chart
     */
    protected $chart;

    /**
     * @var \Closure
     */
    protected $chartCallback;

    public function __construct($title = null, $icon = null)
    {
        $this->title($title);
        $this->icon($icon);

        if ($options = $this->defaultChartOptions()) {
            $this->chartOptions = $options;
        }

        $this->init();
    }

    /**
     * 初始化
     */
    protected function init()
    {
        $this->id('metric-card-'.Str::random(8));
        $this->class('card');
    }

    /**
     * 图表默认配置
     *
     * @return array
     */
    protected function defaultChartOptions()
    {
        return [];
    }

    /**
     * 启用图表.
     *
     * @return Chart
     */
    public function useChart()
    {
        return $this->chart ?: ($this->chart = Chart::make());
    }

    /**
     * 设置图表.
     */
    protected function setUpChart()
    {
        if (! $chart = $this->chart) {
            return;
        }

        // 设置图表高度
        $this->chartOptions['chart']['height'] = $this->chartHeight;

        // 颜色
        if (empty($this->chartOptions['colors'])) {
            $this->chartOptions['colors'] = (array) Admin::color()->get($this->style);
        }

        // 图表配置选项
        $chart->options($this->chartOptions);

        if ($callback = $this->chartCallback) {
            $callback($chart);
        }
    }

    /**
     * 设置图标.
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
     * 设置卡片标题.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title(?string $title)
    {
        $this->options['title'] = $title;

        return $this;
    }

    /**
     * 设置卡片头内容.
     *
     * @param string $contents
     *
     * @return $this
     */
    public function header($contents)
    {
        $this->options['header'] = $contents;

        return $this;
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
     * 设置主题色.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style)
    {
        $this->style = $style;

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
    public function height($value)
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
        $this->chartHeight = $number;

        $this->useChart();

        return $this;
    }

    /**
     * 设置图表label.
     *
     * @param string|array $label
     *
     * @return $this
     */
    public function chartLabels($label)
    {
        $this->chartOptions['labels'] = (array) $label;

        $this->useChart();

        return $this;
    }

    /**
     * 设置图表颜色
     *
     * @param string|array $colors
     *
     * @return $this
     */
    public function chartColors($colors)
    {
        $this->chartOptions['colors'] = (array) $colors;

        $this->useChart();

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
            $this->chartOptions = array_merge(
                $this->chartOptions,
                Helper::array($options)
            );
        }

        $this->useChart();

        return $this;
    }

    /**
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
$card.find('.metric-header').html(response.header);
$card.find('.metric-content').html(response.content);
JS
        );

        $clickable = "#{$id} .dropdown .select-option";

        $cardRequestScript = '';

        if ($this->chart) {
            // 有图表的情况下，直接使用图表的js代码.
            $this->chart->merge($this)->click($clickable);
        } else {
            // 没有图表，需要构建卡片数据请求js代码.
            $cardRequestScript = $this->click($clickable)->buildRequestScript();
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
    public function renderHeader()
    {
        return Helper::render($this->options['header']);
    }

    /**
     * @return string
     */
    public function renderContent()
    {
        return Helper::render($this->options['content']);
    }

    /**
     * 渲染图表.
     *
     * @return string
     */
    public function renderChart()
    {
        return $this->chart ? $this->chart->render() : '';
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->setUpChart();

        $this->script = $this->script();

        $this->variables['style'] = $this->style;
        $this->variables['header'] = $this->renderHeader();
        $this->variables['content'] = $this->renderContent();

        return parent::render();
    }

    /**
     * 返回卡片数据结果.
     *
     * @return array
     */
    public function result()
    {
        $this->setUpChart();

        return array_merge(
            [
                'status'  => 1,
                'header'  => $this->renderHeader(),
                'content' => $this->renderContent(),
            ],
            (array) optional($this->chart)->result()
        );
    }
}
