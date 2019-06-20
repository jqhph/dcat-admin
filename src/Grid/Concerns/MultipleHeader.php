<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Header;

trait MultipleHeader
{
    /**
     * Table multiple headers.
     *
     * @var Header[]
     */
    protected $mutipleHeaders = [];

    /**
     *
     * @param string $label
     * @param array $columnNames
     * @return Header
     */
    public function addMultipleHeader(string $label, array $columnNames)
    {
        if (!$columnNames || count($columnNames) < 2) {
            throw new \InvalidArgumentException('The number of sub titles must be greater than 2');
        }
        $this->withBorder();

        return $this->mutipleHeaders[$label] = new Header($this, $label, $columnNames);
    }

    /**
     * @return Header[]
     */
    public function getMutipleHeaders()
    {
        return $this->mutipleHeaders;
    }

    /**
     * Reorder the headers.
     */
    protected function sortHeaders()
    {
        if (!$this->mutipleHeaders) {
            return;
        }

        $originalHeaders = $this->mutipleHeaders;
        $originalColumns = $this->columns;

        $headersColumns = $this->mutipleHeaders = $this->columns = [];

        foreach ($originalHeaders as $header) {
            $headersColumns = array_merge(
                $headersColumns,
                $tmp = $header->getColumnNames()
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
            if ($isBefore && !isset($this->columns[$name])) {
                $before[$name] = $column;
                continue;
            }
            $isBefore = false;
            if (!isset($this->columns[$name])) {
                $after[$name] = $column;
            }
        }

        $beforeHeaders = $this->createHeaderWithColumns($before);
        $afterHeaders  = $this->createHeaderWithColumns($after);

        $this->columnNames = array_merge(
            array_keys($before),
            array_keys($this->columns),
            array_keys($after)
        );

        $this->columns = collect($this->columns);
        $this->mutipleHeaders = array_merge(
            $beforeHeaders,
            array_values($originalHeaders),
            $afterHeaders
        );
    }

}
