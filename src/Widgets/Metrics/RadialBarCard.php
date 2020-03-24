<?php

namespace Dcat\Admin\Widgets\Metrics;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\FromApi;
use Illuminate\Contracts\Support\Renderable;

class RadialBarCard extends Card
{
    use FromApi;

    /**
     * @var string
     */
    protected $view = 'admin::widgets.metrics.radial-bar-card';

    /**
     * @var array
     */
    protected $options = [
        'title' => '',
        'content' => '',
        'footer' => '',
        'dropdown' => [],
    ];

    /**
     * @var int
     */
    protected $chartHeight = 200;

    /**
     * @var array
     */
    protected $chartOptions = [];

    public function __construct(?string $title = null, $contents = null)
    {
        $this->title($title);
        $this->content($contents);

        $this->defaultChartOptions();

        $this->init();
    }

    /**
     * 图表默认配置
     */
    protected function defaultChartOptions()
    {
        $gradientColor = Admin::color()->success();

        $this->chartOptions = [
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
                            'color' => '#99a2ac',
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
     * 设置卡片标题.
     *
     * @param string $value
     *
     * @return $this
     */
    public function title(?string $value)
    {
        $this->options['title'] = $value;

        return $this;
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
     * 设置图表label.
     *
     * @param string|array $label
     *
     * @return $this
     */
    public function chartLabels($label)
    {
        $this->chartOptions['labels'] = (array) $label;

        $this->setUpChart();

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

        $this->fetched(
            <<<'JS'
$card.find('.metric-footer').html(response.footer);
JS
        );

        return parent::script();
    }

        /**
     * 返回卡片数据结果.
     *
     * @return array
     */
    public function result()
    {
        return array_merge(
            parent::result(),
            [
                'footer' => Helper::render($this->options['footer']),
            ]
        );
    }
}
