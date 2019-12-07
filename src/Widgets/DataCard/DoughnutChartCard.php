<?php

namespace Dcat\Admin\Widgets\DataCard;

use Dcat\Admin\Widgets\Chart\Doughnut;
use Illuminate\Contracts\Support\Arrayable;

class DoughnutChartCard extends Card
{
    protected $view = 'admin::widgets.data-card.chart';

    /**
     * @var Doughnut
     */
    protected $chart;

    /**
     * @var array
     */
    public $dotColors = [];

    /**
     * @var array
     */
    protected $dots = [];

    public function __construct($title = null, $description = null)
    {
        $this->setupChart();
        $this->setupDotColors();

        parent::__construct($title, $description);
    }

    protected function setupChart()
    {
        $this->options['chart'] = $this->chart =
            Doughnut::make()
            ->cutoutPercentage(65)
            ->responsive(false) // 去掉自适应，这里固定大小即可，否则手机端显示会有问题
            ->height('85px')
            ->width('85px')
            ->setHtmlAttribute('width', '85px')
            ->setHtmlAttribute('height', '85px')
            ->disableLegend();
    }

    protected function setupDotColors()
    {
        $this->dotColors = $this->chart->colors;
    }

    /**
     * @param \Closure|array $builder
     * @param array          $data
     *
     * @return $this
     */
    public function chart($builder, array $data = [])
    {
        if ($builder) {
            if ($builder instanceof \Closure) {
                $builder($this->chart);
            } elseif (is_array($builder) || $builder instanceof Arrayable) {
                $this->chart->labels($builder);
            }
        }

        $data && $this->chart->add($data);

        return $this;
    }

    public function orange()
    {
        $this->chart->orange();

        $this->setupDotColors();

        return $this;
    }

    public function green()
    {
        $this->chart->green();

        $this->setupDotColors();

        return $this;
    }

    public function purple()
    {
        $this->chart->purple();

        $this->setupDotColors();

        return $this;
    }

    public function blue()
    {
        $this->chart->blue();

        $this->setupDotColors();

        return $this;
    }

    public function dot($content)
    {
        $this->dots[] = function () use (&$content) {
            $color = array_shift($this->dotColors);

            $content = $this->toString($content);

            $point = "<i class='fa fa-circle' style='font-size:14px;color:$color;'></i>";

            $this->options['content']['left'] .= <<<HTML
<div style='margin:-16px 0 5px;'>

    <span style="font-size:13px;color:#414750" >
    {$point}  &nbsp; $content
    </span>
    
</div>
HTML;
        };

        return $this;
    }

    protected function script()
    {
        $this->buildDots();

        if (! $this->allowBuildFetchingScript()) {
            return;
        }

        $this->setupFetchScript();

        $this->chart->copy($this);
    }

    protected function buildDots()
    {
        foreach ($this->dots as $dot) {
            $dot();
        }
    }

    /**
     * Return JsonResponse instance.
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJsonResponse(array $data = [])
    {
        $this->buildDots();

        return $this->chart->toJsonResponse(
            true,
            array_merge($this->buildJsonResponseArray(), $data)
        );
    }
}
