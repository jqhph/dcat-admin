<?php

namespace Dcat\Admin\Widgets\DataCard;

use Dcat\Admin\Widgets\Chart\Pie;

class PieChartCard extends DoughnutChartCard
{
    protected function setupChart()
    {
        $this->options['chart'] = $this->chart =
            Pie::make()
                ->responsive(false)
                ->height('85px')
                ->width('85px')
                ->setHtmlAttribute('width', '85px')
                ->setHtmlAttribute('height', '85px')
                ->disableLegend();
    }
}
