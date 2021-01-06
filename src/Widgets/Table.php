<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

class Table extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.table';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var int
     */
    protected $depth = 0;

    /**
     * Table constructor.
     *
     * @param array $headers
     * @param mixed $rows
     * @param array $style
     */
    public function __construct($headers = [], $rows = false, $style = [])
    {
        if ($rows === false) {
            $rows = $headers;
            $headers = [];
        }

        $this->class('table default-table');

        $this->setHeaders($headers);
        $this->setRows($rows);
        $this->setStyle($style);
    }

    /**
     * Set table headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders($headers = [])
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param int $depth
     *
     * @return $this
     */
    public function depth(int $depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Set table rows.
     *
     * @param array $rows
     *
     * @return $this
     */
    public function setRows($rows = [])
    {
        if ($rows && ! Arr::isAssoc(Helper::array($rows))) {
            $this->rows = $rows;

            return $this;
        }

        $noTrPadding = false;

        foreach ($rows as $key => $item) {
            if (is_array($item)) {
                if (Arr::isAssoc($item)) {
                    $borderLeft = $this->depth ? 'table-left-border-nofirst' : 'table-left-border';

                    $item = static::make($item)
                        ->depth($this->depth + 1)
                        ->class('table-no-top-border '.$borderLeft, true)
                        ->render();

                    if (! $noTrPadding) {
                        $this->class('table-no-tr-padding', true);
                    }
                    $noTrPadding = true;
                } else {
                    $item = json_encode($item, JSON_UNESCAPED_UNICODE);
                }
            }

            $this->rows[] = [$key, $item];
        }

        return $this;
    }

    /**
     * Set table style.
     *
     * @param array $style
     *
     * @return $this
     */
    public function setStyle($style = [])
    {
        if ($style) {
            $this->class(implode(' ', (array) $style), true);
        }

        return $this;
    }

    /**
     * Render the table.
     *
     * @return string
     */
    public function render()
    {
        $vars = [
            'headers'    => $this->headers,
            'rows'       => $this->rows,
            'attributes' => $this->formatHtmlAttributes(),
        ];

        return view($this->view, $vars)->render();
    }

    /**
     * @return $this
     */
    public function withBorder()
    {
        $this->class('table-bordered', true);

        return $this;
    }
}
