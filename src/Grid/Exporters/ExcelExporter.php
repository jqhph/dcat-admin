<?php

namespace Dcat\Admin\Grid\Exporters;

use Dcat\EasyExcel\Excel;
use Dcat\Admin\Grid;

class ExcelExporter extends AbstractExporter
{
    /**
     * {@inheritdoc}
     */
    public function export()
    {
        if (! class_exists(Excel::class)) {
            throw new \Exception('To use exporter, please install [dcat/easy-excel] first.');
        }

        $filename = $this->getFilename().'.'.$this->extension;

        $exporter = Excel::export();

        if ($this->scope === Grid\Exporter::SCOPE_ALL) {
            $exporter->chunk(function (int $times) {
                return $this->buildData($times);
            });
        } else {
            $exporter->data($this->buildData());
        }

        $exporter->headings($this->titles)->download($filename);

        exit;
    }

}
