<?php

namespace Dcat\Admin\Widgets\Chart;

use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\HasAjaxRequest;
use Dcat\Admin\Widgets\Color;
use Dcat\Admin\Widgets\Widget;
use Illuminate\Support\Str;

/**
 * @see https://www.chartjs.org/docs/latest/
 *
 * @method $this blue()
 * @method $this green()
 * @method $this orange()
 * @method $this purple()
 */
abstract class Chart extends Widget
{
    use HasAjaxRequest;

    public static $globalSettings = [
        'defaultFontColor'  => '#555',
        'defaultFontFamily' => 'Nunito,system-ui,sans-serif',
    ];

    public $colors = [];

    protected $id = '';

    protected $type = 'line';

    protected $data = [
        'labels'   => [],
        'datasets' => [],
    ];

    protected $options = [];

    protected $width;
    protected $height;

    protected $containerStyle = '';

    /**
     * Chart constructor.
     *
     * @param mixed ...$params
     */
    public function __construct(...$params)
    {
        if (count($params) == 2) {
            [$title, $labels] = $params;

            $title && $this->title($title);
            $labels && $this->labels($labels);
        } elseif (! empty($params[0])) {
            if (is_string($params[0])) {
                $this->title($params[0]);
            } elseif (is_array($params[0])) {
                $this->labels($params[0]);
            }
        }

        $this->setDefaultColors();
    }

    /**
     * Composite the chart.
     *
     * @param Chart $chart
     *
     * @return $this
     */
    public function composite(self $chart)
    {
        $this->data['datasets']
            = array_merge($this->data['datasets'], $chart->datasets());

        return $this;
    }

    /**
     * Set labels.
     *
     * @param $labels
     *
     * @return $this
     */
    public function labels(array $labels)
    {
        $this->data['labels'] = $labels;

        return $this;
    }

    /**
     * Add datasets.
     *
     * @example
     *     $this->add('LiXin', [1, 23, 6, 10, 6]);
     *     $this->add([
     *         ['LiXin', [1, 23, 6, 10, 6]], ['阿翡', [4, 11, 8, 25, 19]]
     *     ]);
     *
     * @param string|array $label
     * @param array        $data
     * @param string|array $fillColor
     *
     * @return $this
     */
    public function add($label, $data = [], $fillColor = null)
    {
        if (is_array($label)) {
            foreach ($label as $item) {
                call_user_func_array([$this, 'add'], $item);
            }

            return $this;
        }

        $item = [
            'label'           => $label,
            'data'            => $data,
            'backgroundColor' => $fillColor,
        ];

        if ($fillColor) {
            if (is_string($fillColor)) {
                $item['backgroundColor'] = $fillColor;
            } elseif (is_array($fillColor)) {
                $item = array_merge($fillColor, $item);
            }
        }

        $this->data['datasets'][] = &$item;

        return $this;
    }

    /**
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @param bool $val
     *
     * @return $this
     */
    public function responsive(bool $val = true)
    {
        return $this->options(['responsive' => $val]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/legend.html
     *
     * @param array $opts
     *
     * @return $this
     */
    public function legend(array $opts)
    {
        if (! isset($this->options['legend'])) {
            $this->options['legend'] = [];
        }

        $this->options['legend'] = array_merge($this->options['legend'], $opts);

        return $this;
    }

    /**
     * @return $this
     */
    public function disableLegend()
    {
        return $this->legend(['display' => false]);
    }

    /**
     * @return $this
     */
    public function legendPosition(string $val)
    {
        return $this->legend(['position' => $val]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/tooltip.html
     *
     * @param array $opts
     *
     * @return $this
     */
    public function tooltips(array $opts)
    {
        if (! isset($this->options['tooltips'])) {
            $this->options['tooltips'] = [];
        }

        $this->options['tooltips'] = array_merge($this->options['tooltips'], $opts);

        return $this;
    }

    /**
     * Disable tooltip.
     *
     * @return $this
     */
    public function disableTooltip()
    {
        return $this->tooltips(['enabled' => false]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/title.html
     *
     * @param array $options
     *
     * @return $this
     */
    public function title($options)
    {
        if (is_array($options)) {
            $this->options['title'] = $options;
        } else {
            $this->options['title'] = ['text' => $options, 'display' => true, 'fontSize' => '14'];
        }

        return $this;
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/elements.html
     *
     * @param array $options
     *
     * @return $this
     */
    public function elements(array $options)
    {
        if (! isset($this->options['elements'])) {
            $this->options['elements'] = [];
        }

        $this->options['elements'] = array_merge($this->options['elements'], $options);

        return $this;
    }

    /**
     * @see https://www.chartjs.org/docs/latest/configuration/layout.html
     *
     * @param array $opts
     *
     * @return $this
     */
    public function layout(array $opts)
    {
        if (! isset($this->options['layout'])) {
            $this->options['layout'] = [];
        }

        $this->options['layout'] = array_merge($this->options['layout'], $opts);

        return $this;
    }

    /**
     * The padding to add inside the chart.
     *
     * @param array|int $opts
     *
     * @return Chart
     */
    public function padding($opts)
    {
        return $this->layout(['padding' => $opts]);
    }

    /**
     * @param array $opts
     *
     * @return $this
     */
    public function animation(array $opts)
    {
        if (! isset($this->options['animation'])) {
            $this->options['animation'] = [];
        }

        $this->options['animation'] = array_merge($this->options['animation'], $opts);

        return $this;
    }

    /**
     * Set width of container.
     *
     * @param string $width
     *
     * @return Chart
     */
    public function width($width)
    {
        return $this->setContainerStyle('width:'.$width);
    }

    /**
     * Set height of container.
     *
     * @param string $height
     *
     * @return Chart
     */
    public function height($height)
    {
        return $this->setContainerStyle('height:'.$height);
    }

    /**
     * @param string $style
     * @param bool   $append
     *
     * @return $this
     */
    public function setContainerStyle(string $style, bool $append = true)
    {
        if ($append) {
            $this->containerStyle .= ';'.$style;
        } else {
            $this->containerStyle = $style;
        }

        return $this;
    }

    /**
     * Fill default color.
     *
     * @param array $colors
     *
     * @return void
     */
    protected function fillColor(array $colors = [])
    {
        $colors = $colors ?: $this->colors;

        foreach ($this->data['datasets'] as &$item) {
            if (empty($item['backgroundColor'])) {
                $item['backgroundColor'] = array_shift($colors);
            }
        }
    }

    /**
     * Make element id.
     *
     * @return void
     */
    protected function makeId()
    {
        if ($this->id) {
            return;
        }
        $this->id = 'chart_'.$this->type.Str::random(8);
    }

    public function getId()
    {
        $this->makeId();

        return $this->id;
    }

    /**
     * Setup script.
     *
     * @return string
     */
    protected function script()
    {
        $config = [
            'type'    => $this->type,
            'data'    => &$this->data,
            'options' => &$this->options,
        ];
        $options = json_encode($config);

        // Global configure.
        $globalSettings = '';
        foreach (self::$globalSettings as $k => $v) {
            $globalSettings .= sprintf('Chart.defaults.global.%s="%s";', $k, $v);
        }

        if (! $this->allowBuildRequestScript()) {
            return <<<JS
{$globalSettings}
setTimeout(function(){ new Chart($("#{$this->id}").get(0).getContext("2d"), $options) },60)
JS;
        }

        $this->fetched(
            <<<JS
if (!response.status) {
    return LA.error(response.message || 'Server internal error.');
}        
var id = '{$this->id}', opt = $options, prev = window['obj'+id];
opt.options = $.extend(opt.options, response.options || {});
opt.data.datasets = response.datasets || opt.data.datasets;
if (prev) prev.destroy();

window['obj'+id] = new Chart($("#"+id).get(0).getContext("2d"), opt);
JS
        );

        return $globalSettings.$this->buildRequestScript();
    }

    /**
     * Get datasets.
     *
     * @return array
     */
    public function datasets()
    {
        $this->fillColor();

        return array_map(function ($v) {
            $v['type'] = $v['type'] ?? $this->type;

            return $v;
        }, $this->data['datasets']);
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->makeId();
        $this->fillColor();

        $this->script = $this->script();

        $this->setHtmlAttribute([
            'id' => $this->id,
        ]);

        $this->collectAssets();

        return <<<HTML
<div class="chart" style="{$this->containerStyle}">
    <canvas {$this->formatHtmlAttributes()}>
        Your browser does not support the canvas element.
    </canvas>
</div>
HTML;
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if (isset(Color::$chartTheme[$method])) {
            $this->colors = Color::$chartTheme[$method];

            return $this;
        }

        return parent::__call($method, $parameters); // TODO: Change the autogenerated stub
    }

    /**
     * Return JsonResponse instance.
     *
     * @param bool  $returnOptions
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJsonResponse(bool $returnOptions = true, array $data = [])
    {
        return response()->json(array_merge(
            [
                'status'   => 1,
                'datasets' => $this->datasets(),
                'options'  => $returnOptions ? $this->getOptions() : [],
            ],
            $data
        ));
    }

    /**
     * @return void
     */
    protected function setDefaultColors()
    {
        if (! $this->colors) {
            $this->colors = Color::$chartTheme['blue'];
        }
    }

    /**
     * Collect assets.
     *
     * @return void
     */
    public function collectAssets()
    {
        $this->script && Admin::script($this->script);

        Admin::collectComponentAssets('chartjs');
    }
}
