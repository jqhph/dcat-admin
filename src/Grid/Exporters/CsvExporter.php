<?php

namespace Dcat\Admin\Grid\Exporters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CsvExporter extends AbstractExporter
{
    /**
     * {@inheritdoc}
     */
    public function export()
    {
        $filename = $this->getFilename().'.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        response()->stream(function () {
            $handle = fopen('php://output', 'w');

            $titles = $this->titles;

            if ($titles) {
                // Add CSV headers
                fputcsv($handle, $titles);
            }

            $records = $this->getData();

            if ($this->builder) {
                $records = $this->builder->call($records);
            }

            if (empty($titles)) {
                $titles = $this->getHeaderRowFromRecords($records);

                // Add CSV headers
                fputcsv($handle, $titles);
            }

            foreach ($records as $record) {
                fputcsv($handle, $this->getFormattedRecord($titles, $record));
            }

            // Close the output stream
            fclose($handle);
        }, 200, $headers)->send();

        exit;
    }

    /**
     * @param array $records
     *
     * @return array
     */
    public function getHeaderRowFromRecords(array $records): array
    {
        $titles = [];

        collect(Arr::dot($records[0] ?? []))->keys()->map(
            function ($key) use(&$titles) {
                if (Str::contains($key, '.')) return;

                $titles[$key] = Str::ucfirst($key);
            }
        );

        return $titles;
    }

    /**
     * @param $titles
     * @param $record
     * @return array
     */
    public function getFormattedRecord($titles, $record)
    {
        $result = [];

        $record = Arr::dot($record);
        foreach ($titles as $k => $label) {
            $result[] = $record[$k] ?? '';
        }

        return $result;
    }
}
