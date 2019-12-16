<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\FirstRowHeader;

trait HasMultipleHeaders
{
    /**
     * Table multiple headers.
     *
     * @var FirstRowHeader[]
     */
    protected $multipleHeaders = [];

    /**
     * Merge cells.
     *
     * @param string $label
     * @param array  $columnNames
     *
     * @return FirstRowHeader
     */
    public function combine(string $label, array $columnNames)
    {
        if (count($columnNames) < 2) {
            throw new \InvalidArgumentException('Invalid column names.');
        }

        $this->withBorder();

        return $this->multipleHeaders[$label] = new FirstRowHeader($this, $label, $columnNames);
    }

    /**
     * @return FirstRowHeader[]
     */
    public function multipleHeaders()
    {
        return $this->multipleHeaders;
    }

    /**
     * Reorder the headers.
     */
    protected function sortHeaders()
    {
        if (! $this->multipleHeaders) {
            return;
        }

        $originalHeaders = $this->multipleHeaders;
        $originalColumns = $this->columns;

        $headersColumns = $this->multipleHeaders = $this->columns = [];

        foreach ($originalHeaders as $header) {
            $headersColumns = array_merge(
                $headersColumns,
                $tmp = $header->columnNames()
            );
            foreach ($tmp as &$name) {
                if ($column = $originalColumns->get($name)) {
                    $this->columns[$name] = $column;
                }
            }
        }

        $before = $after = [];
        $isBefore = true;
        foreach ($originalColumns as $name => $column) {
            if ($isBefore && ! isset($this->columns[$name])) {
                $before[$name] = $column;
                continue;
            }
            $isBefore = false;
            if (! isset($this->columns[$name])) {
                $after[$name] = $column;
            }
        }

        $beforeHeaders = $this->createHeaderWithColumns($before);
        $afterHeaders = $this->createHeaderWithColumns($after);

        $this->columnNames = array_merge(
            array_keys($before),
            array_keys($this->columns),
            array_keys($after)
        );

        $this->columns = collect($this->columns);
        $this->multipleHeaders = array_merge(
            $beforeHeaders,
            array_values($originalHeaders),
            $afterHeaders
        );
    }

    protected function createHeaderWithColumns(array $columns)
    {
        $headers = [];

        /* @var Column $column */
        foreach ($columns as $name => $column) {
            $header = new FirstRowHeader($this, $column->getLabel(), [$name]);
            $prio = $column->getDataPriority();

            if (is_int($prio)) {
                $header->responsive($prio);
            }

            if ($html = $column->renderHeader()) {
                $header->append($html);
            }

            $headers[] = $header;
        }

        return $headers;
    }
}
