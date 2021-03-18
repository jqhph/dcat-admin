<?php

namespace Dcat\Admin\Grid\Exporters;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Grid;
use Dcat\EasyExcel\Excel;

class ExcelExporter extends AbstractExporter
{
    public function __construct($titles = [])
    {
        parent::__construct($titles);

        if (! class_exists(Excel::class)) {
            throw new RuntimeException('To use exporter, please install [dcat/easy-excel] first.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function export()
    {
        $filename = $this->getFilename().'.'.$this->extension;

        $exporter = Excel::export();

        if ($this->scope === Grid\Exporter::SCOPE_ALL) {
            $exporter->chunk(function (int $times) {
                return $this->buildData($times);
            });
        } else {
            $exporter->data($this->buildData() ?: [[]]);
        }

        $exporter->headings($this->titles())->download($filename);

        exit;
    }
}
