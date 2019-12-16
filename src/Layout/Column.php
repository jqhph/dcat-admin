<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Grid;
use Dcat\Admin\Support\Helper;

class Column implements Buildable
{
    /**
     * grid system prefix width.
     *
     * @var array
     */
    protected $width = [];

    /**
     * @var array
     */
    protected $contents = [];

    /**
     * Column constructor.
     *
     * @param $content
     * @param int $width
     */
    public function __construct($content, $width = 12)
    {
        if ($content instanceof \Closure) {
            call_user_func($content, $this);
        } else {
            $this->append($content);
        }

        ///// set width.
        // if null, or $this->width is empty array, set as "md" => "12"
        if (is_null($width) || (is_array($width) && count($width) === 0)) {
            $this->width['md'] = 12;
        }
        // $this->width is number(old version), set as "md" => $width
        elseif (is_numeric($width)) {
            $this->width['md'] = $width;
        } else {
            $this->width = $width;
        }
    }

    /**
     * Append content to column.
     *
     * @param $content
     *
     * @return $this
     */
    public function append($content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Add a row for column.
     *
     * @param $content
     *
     * @return Column
     */
    public function row($content)
    {
        if (! $content instanceof \Closure) {
            $row = new Row($content);
        } else {
            $row = new Row();

            call_user_func($content, $row);
        }

        ob_start();

        $row->build();

        return $this->append(ob_get_clean());
    }

    /**
     * Build column html.
     */
    public function build()
    {
        $this->startColumn();

        foreach ($this->contents as $content) {
            if ($content instanceof Grid) {
                echo $content->render();
            } else {
                echo Helper::render($content);
            }
        }

        $this->endColumn();
    }

    /**
     * Start column.
     */
    protected function startColumn()
    {
        // get class name using width array
        $classnName = collect($this->width)->map(function ($value, $key) {
            return "col-$key-$value";
        })->implode(' ');

        echo "<div class=\"{$classnName}\">";
    }

    /**
     * End column.
     */
    protected function endColumn()
    {
        echo '</div>';
    }
}
