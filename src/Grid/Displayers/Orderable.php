<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Orderable extends AbstractDisplayer
{
    protected static $js = [
        '@grid-extension',
    ];

    public function display()
    {
        Admin::script($this->script());

        return <<<EOT

<div class="">
    <a href="javascript:void(0)" class=" font-14 {$this->grid->getRowName()}-orderable" data-id="{$this->getKey()}" data-direction="1">
        <svg style="fill: currentColor" t="1582861402297" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="10589" width="15" height="15"><path d="M877.216 491.808" p-id="10590"></path><path d="M856.224 400.768 535.648 73.888c-5.12-5.248-11.744-8.064-18.656-8.8-1.248-0.16-2.464-0.16-3.68-0.192-1.248 0.032-2.464 0-3.712 0.192-6.912 0.736-13.536 3.584-18.656 8.8L170.368 400.768c-12.096 12.352-12.096 32.288 0 44.64 12.096 12.352 31.648 12.352 43.744 0l267.744-273.024 0 756.96c0 17.44 13.856 31.552 30.944 31.552 0.16 0 0.32-0.096 0.48-0.096 0.16 0 0.32 0.096 0.48 0.096 17.088 0 30.944-14.112 30.944-31.552L544.704 172.384l267.744 273.024c12.096 12.352 31.648 12.352 43.744 0C868.32 433.056 868.32 413.12 856.224 400.768z" p-id="10591"></path></svg>
    </a>&nbsp;
    <a href="javascript:void(0)" class=" font-14 {$this->grid->getRowName()}-orderable" data-id="{$this->getKey()}" data-direction="0">
        <svg style="fill: currentColor" t="1582861442213" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="989" width="15" height="15"><path d="M877.216 533.952" p-id="990"></path><path d="M856.224 624.992 535.648 951.872c-5.12 5.248-11.744 8.064-18.656 8.8-1.248 0.16-2.464 0.16-3.68 0.192-1.248-0.032-2.464 0-3.712-0.192-6.912-0.736-13.536-3.584-18.656-8.8L170.368 624.992c-12.096-12.352-12.096-32.288 0-44.64 12.096-12.352 31.648-12.352 43.744 0l267.744 273.024L481.856 96.448c0-17.44 13.856-31.552 30.944-31.552 0.16 0 0.32 0.096 0.48 0.096 0.16 0 0.32-0.096 0.48-0.096 17.088 0 30.944 14.112 30.944 31.552l0 756.96 267.744-273.024c12.096-12.352 31.648-12.352 43.744 0C868.32 592.704 868.32 612.64 856.224 624.992z" p-id="991"></path></svg>
    </a>
</div>
EOT;
    }

    protected function script()
    {
        return <<<JS
        Dcat.grid.Orderable({
            button: '.{$this->grid->getRowName()}-orderable',
            url: '{$this->resource()}/:key',
        });
JS;
    }
}
