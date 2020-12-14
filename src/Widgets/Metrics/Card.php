<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\InteractsWithApi;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Widgets\Widget;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Card extends Widget
{
    use InteractsWithApi;

    /**
     * @var string
     */
    protected $view = 'admin::widgets.metrics.card';

    /**
     * 图标.
     *
     * @var string
     */
    protected $icon;

    /**
     * 卡片标题.
     *
     * @var string
     */
    protected $title;

    /**
     * 卡片子标题.
     *
     * @var string
     */
    protected $subTitle;

    /**
     * 卡片头部内容.
     *
     * @var string|Renderable|\Closure
     */
    protected $header;

    /**
     * 卡片内容.
     *
     * @var string|Renderable|\Closure
     */
    protected $content;

    /**
     * 下拉菜单.
     *
     * @var array
     */
    protected $dropdown = [];

    /**
     * 图标主题色.
     *
     * @var string
     */
    protected $style = 'primary';

    /**
     * 卡片高度.
     *
     * @var int|string
     */
    protected $height = 165;

    /**
     * 图表高度.
     *
     * @var int
     */
    protected $chartHeight;

    /**
     * 图表上间距.
     *
     * @var int
     */
    protected $chartMarginTop;

    /**
     * 图表下间距.
     *
     * @var int
     */
    protected $chartMarginBottom;

    /**
     * 图表右间距.
     *
     * @var int
     */
    protected $chartMarginRight = 1;

    /**
     * 图表配置.
     *
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
     * 初始化.
     */
    protected function init()
    {
        $this->id('metric-card-'.Str::random(8));
        $this->class('card');
    }

    /**
     * 图表默认配置.
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
     * 设置图标.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function icon(?string $icon)
    {
        $this->icon = $icon;

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
        $this->title = $title;

        return $this;
    }

    /**
     * 设置卡片子标题.
     *
     * @param string $title
     *
     * @return $this
     */
    public function subTitle(?string $title)
    {
        $this->subTitle = $title;

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
        $this->header = $contents;

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
        $this->content = $contents;

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
        $this->dropdown = $items;

        return $this;
    }

    /**
     * 设置最小高度.
     *
     * @param string|int $value
     *
     * @return $this
     */
    public function height($value)
    {
        $this->height = $value;

        return $this;
    }

    /**
     * 设置图表配置.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function chartOption($key, $value)
    {
        Arr::set($this->chartOptions, $key, $value);

        $this->useChart();

        return $this;
    }

    /**
     * 设置图表高度.
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
     * 设置图表上间距.
     *
     * @param int $number
     *
     * @return $this
     */
    public function chartMarginTop(int $number)
    {
        $this->chartMarginTop = $number;

        $this->useChart();

        return $this;
    }

    /**
     * 设置图表下间距.
     *
     * @param int $number
     *
     * @return $this
     */
    public function chartMarginBottom(int $number)
    {
        $this->chartMarginBottom = $number;

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
     * 设置图表颜色.
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
     * 设置图表.
     */
    protected function setUpChart()
    {
        if (! $chart = $this->chart) {
            return;
        }

        $this->setUpChartMargin();

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
     * 设置图表间距.
     *
     * @return void
     */
    protected function setUpChartMargin()
    {
        if ($this->chartMarginTop !== null) {
            $this->chart->style("margin-top: {$this->chartMarginTop}px;");
        }

        if ($this->chartMarginBottom !== null) {
            $this->chart->style("margin-bottom: {$this->chartMarginBottom}px;");
        }

        if ($this->chartMarginRight !== null) {
            $this->chart->style("margin-right: {$this->chartMarginRight}px;");
        }
    }

    /**
     * @return mixed
     */
    public function addScript()
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
        return $this->script = <<<JS
$('{$clickable}').on('click', function () {
    $(this).parents('.dropdown').find('.btn').html($(this).text());
});

{$cardRequestScript}
JS;
    }

    /**
     * 渲染卡片头部内容.
     *
     * @return string
     */
    public function renderHeader()
    {
        return Helper::render($this->header);
    }

    /**
     * 渲染卡片主体内容.
     *
     * @return string
     */
    public function renderContent()
    {
        return Helper::render($this->content);
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
     * 设置卡片高度.
     */
    protected function setUpCardHeight()
    {
        if (! $height = $this->height) {
            return;
        }

        if (is_numeric($height)) {
            $height .= 'px';
        }

        $this->appendHtmlAttribute('style', "min-height:{$height};");
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->setUpChart();
        $this->setUpCardHeight();

        $this->addScript();

        $this->variables['icon'] = $this->icon;
        $this->variables['title'] = $this->title;
        $this->variables['subTitle'] = $this->subTitle;
        $this->variables['style'] = $this->style;
        $this->variables['header'] = $this->renderHeader();
        $this->variables['content'] = $this->renderContent();
        $this->variables['dropdown'] = $this->dropdown;

        return parent::render();
    }

    /**
     * 返回API请求结果.
     *
     * @return array
     */
    public function valueResult()
    {
        $this->setUpChart();

        return array_merge(
            [
                'status' => 1,
                'header' => $this->renderHeader(),
                'content' => $this->renderContent(),
            ],
            (array) optional($this->chart)->valueResult()
        );
    }
}
