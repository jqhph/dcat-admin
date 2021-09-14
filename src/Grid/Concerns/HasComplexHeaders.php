<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Exception\InvalidArgumentException;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\ComplexHeader;
use Illuminate\Support\Collection;

trait HasComplexHeaders
{
    /**
     * @var ComplexHeader[]|Collection
     */
    protected $complexHeaders;

    /**
     * Merge cells.
     *
     * @param  string  $column
     * @param  array  $columnNames
     * @param  string  $label
     * @return ComplexHeader
     */
    public function combine(string $column, array $columnNames, string $label = null)
    {
        if (count($columnNames) < 2) {
            throw new InvalidArgumentException('Invalid column names.');
        }

        if (! $this->complexHeaders) {
            $this->complexHeaders = new Collection();
        }

        $this->withBorder();

        return $this->complexHeaders[$column] = new ComplexHeader($this, $column, $columnNames, $label);
    }

    /**
     * @return ComplexHeader[]
     */
    public function getComplexHeaderNames()
    {
        if (! $this->complexHeaders) {
            return [];
        }

        return $this->complexHeaders->map(function ($header) {
            return $header->getName();
        })->toArray();
    }

    /**
     * @return ComplexHeader[]|Collection|null
     */
    public function getComplexHeaders()
    {
        return $this->complexHeaders;
    }

    /**
     * Reorder the headers.
     */
    protected function sortHeaders()
    {
        if (! $this->complexHeaders) {
            return;
        }

        $originalHeaders = $this->complexHeaders->toArray();
        $originalColumns = $this->columns;

        $headersColumns = $this->complexHeaders = $this->columns = [];

        foreach ($originalHeaders as $header) {
            $headersColumns = array_merge(
                $headersColumns,
                $tmp = $header->getColumnNames()->toArray()
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
        $this->complexHeaders = collect(
            array_merge(
                $beforeHeaders,
                array_values($originalHeaders),
                $afterHeaders
            )
        );
    }

    protected function createHeaderWithColumns(array $columns)
    {
        $headers = [];

        /* @var Column $column */
        foreach ($columns as $name => $column) {
            $header = new ComplexHeader($this, $column->getName(), [$name], $column->getLabel());

            if ($html = $column->renderHeader()) {
                $header->append($html);
            }

            $headers[] = $header;
        }

        return $headers;
    }
}
