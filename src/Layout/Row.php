<?php

namespace Dcat\Admin\Layout;

use Illuminate\Contracts\Support\Renderable;

class Row implements Renderable
{
    /**
     * @var Column[]
     */
    protected $columns = [];

    protected $noGutters = false;

    /**
     * Row constructor.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        if (! empty($content)) {
            if ($content instanceof Column) {
                $this->addColumn($content);
            } else {
                $this->column(12, $content);
            }
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
     * @param bool $value
     *
     * @return $this
     */
    public function noGutters(bool $value = true)
    {
        $this->noGutters = $value;

        return $this;
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
        $noGutters = $this->noGutters ? 'no-gutters' : '';

        return "<div class=\"row {$noGutters}\">";
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
