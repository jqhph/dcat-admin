<?php

namespace Dcat\Admin\Widgets\DataCard;

use Dcat\Admin\Widgets\Sparkline\Line;
use Illuminate\Contracts\Support\Arrayable;

class LineChartCard extends Card
{
    protected $view = 'admin::widgets.data-card.line-chart';

    /**
     * @var Line
     */
    protected $chart;

    public function __construct($title = null, $description = null, $number = null)
    {
        parent::__construct($title, $description, $number);

        $this->setupChart();
    }

    protected function setupChart()
    {
        $this->options['chart'] = $this->chart =
            Line::make()
            ->height('60px')
            ->style('display:block');
    }

    public function chart($builder)
    {
        if ($builder instanceof \Closure) {
            $builder($this->chart);
        } elseif (is_array($builder) || $builder instanceof Arrayable) {
            $this->chart->values($builder);
        }

        return $this;
    }

    protected function script()
    {
        if (! $this->allowBuildFetchingScript()) {
            return;
        }

        $this->setupFetchScript();

        $this->chart->copy($this);
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
        return $this->chart->toJsonResponse(
            true,
            array_merge($this->buildJsonResponseArray(), $data)
        );
    }
}
