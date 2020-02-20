<?php

namespace Dcat\Admin\Layout;

use Illuminate\Contracts\Support\Renderable;

class Row implements Renderable
{
    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * Row constructor.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        if (! empty($content)) {
            $this->column(12, $content);
        }
    }

    /**
     * Add a column.
     *
     * @param int $width
     * @param $content
     */
    public function column($width, $content)
    {
        $width = $width < 1 ? round(12 * $width) : $width;

        $column = new Column($content, $width);

        $this->addColumn($column);
    }

    /**
     * @param Column $column
     */
    protected function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * Build row column.
     *
     * @return string
     */
    public function render()
    {
        $html = $this->startRow();

        foreach ($this->columns as $column) {
            $html .= $column->render();
        }

        return $html.$this->endRow();
    }

    /**
     * Start row.
     *
     * @return string
     */
    protected function startRow()
    {
        return '<div class="row">';
    }

    /**
     * End column.
     *
     * @return string
     */
    protected function endRow()
    {
        return '</div>';
    }
}
