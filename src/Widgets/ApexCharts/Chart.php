<?php

namespace Dcat\Admin\Widgets\ApexCharts;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\FromApi;
use Dcat\Admin\Widgets\Widget;
use Illuminate\Support\Str;

class Chart extends Widget
{
    use FromApi;

    public static $js = '@apex-charts';

    protected $containerSelector;

    protected $options = [];

    protected $built = false;

    protected $scripts = [
        'extend' => 'return options',
    ];

    public function __construct($containerSelector = null, $options = [])
    {
        if ($containerSelector && ! is_string($containerSelector)) {
            $options = $containerSelector;
            $containerSelector = null;
        }

        $this->selector($containerSelector);

        $this->options($options);
    }

    /**
     * 设置或获取图表容器选择器
     *
     * @param string|null $selector
     *
     * @return $this|string|null
     */
    public function selector(?string $selector = null)
    {
        if ($selector === null) {
            return $this->containerSelector;
        }

        $this->containerSelector = $selector;

        if ($selector && ! $this->built) {
            $this->autoRender();
        }

        return $this;
    }

    /**
     * @param string|array $title
     *
     * @return $this
     */
    public function title($title)
    {
        if (is_string($title)) {
            $options = ['text' => $title];
        } else {
            $options = Helper::array($title);
        }

        $this->options['title'] = $options;

        return $this;
    }

    /**
     * @param array $series
     *
     * @return $this
     */
    public function series($series)
    {
        $this->options['series'] = Helper::array($series);

        return $this;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function labels($value)
    {
        $this->options['labels'] = Helper::array($value);

        return $this;
    }

    /**
     * @param string|array $colors
     *
     * @return $this
     */
    public function colors($colors)
    {
        $this->options['colors'] = Helper::array($colors);

        return $this;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function stroke($value)
    {
        $this->options['stroke'] = Helper::array($value);

        return $this;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function xaxis($value)
    {
        $this->options['xaxis'] = Helper::array($value);

        return $this;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function tooltip($value)
    {
        $this->options['tooltip'] = Helper::array($value);

        return $this;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function yaxis($value)
    {
        $this->options['yaxis'] = Helper::array($value);

        return $this;
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    public function fill($value)
    {
        $this->options['fill'] = Helper::array($value);

        return $this;
    }

   /**
     * @param array $value
     *
     * @return $this
     */
    public function chart($value)
    {
        $this->options['chart'] = Helper::array($value);

        return $this;
    }

    /**
     * @param array|bool $value
     *
     * @return $this
     */
    public function dataLabels($value)
    {
        if (is_bool($value)) {
            $value = ['enabled' => $value];
        }

        $this->options['dataLabels'] = Helper::array($value);

        return $this;
    }

    /**
     * @param string|\Closure $script
     *
     * @return $this
     */
    public function extendOptions($script)
    {
        $this->scripts['extend'] = value($script);

        return $this;
    }

    /**
     * @param string $options
     *
     * @return string
     */
    protected function buildDefaultScript($options)
    {
        return <<<JS
(function () {
    var options = {$options}, extend = {$this->buildExtendOptionsScript()};

    var chart = new ApexCharts(
        $("{$this->containerSelector}")[0], 
        $.extend(options, extend(options))
    );
    chart.render();
})();
JS;
    }

    /**
     * @return string
     */
    public function script()
    {
        $options = json_encode($this->options);

        if (! $this->allowBuildRequest()) {
            return $this->buildDefaultScript($options);
        }

        $this->fetched(
            <<<JS
if (! response.status) {
    return Dcat.error(response.message || 'Server internal error.');
}

var container = $('{$this->containerSelector}'), extend = {$this->buildExtendOptionsScript()};

container.html('');

setTimeout(function () {
    var chart = new ApexCharts(container[0], $.extend(response.options, extend(response.options)));
    
    chart.render();
}, 50);
JS
        );

        return $this->buildRequestScript();
    }

    /**
     * @return string
     */
    protected function buildExtendOptionsScript()
    {
        return <<<JS
function (options) {
    {$this->scripts['extend']}
}
JS;
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($this->built) {
            return;
        }
        $this->built = true;

        $hasSelector = $this->containerSelector ? true : false;

        if (! $hasSelector) {
            $id = $this->generateId();

            $this->selector('#'.$id);
        }

        $this->script = $this->script();

        $this->collectAssets();

        if ($hasSelector) {
            return;
        }

        // 没有指定容器选择器，则需自动生成
        $this->setHtmlAttribute([
            'id' => $id,
        ]);

        return <<<HTML
<div {$this->formatHtmlAttributes()}></div>
HTML;
    }

    /**
     * 返回请求结果.
     *
     * @return array
     */
    public function result()
    {
        return [
            'status' => 1,
            'options' => $this->options,
        ];
    }

    protected function generateId()
    {
        return 'apex-chart-'.Str::random(8);
    }
}
