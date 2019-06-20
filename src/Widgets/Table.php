<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Table extends Widget implements Renderable
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
     * @var array
     */
    protected $style = [];

    /**
     * @var int
     */
    protected $level = 0;

    /**
     * Table constructor.
     *
     * @param array $headers
     * @param array $rows
     * @param array $style
     */
    public function __construct($headers = [], $rows = [], $style = [])
    {
        $this->setHeaders($headers);
        $this->setRows($rows);
        $this->setStyle($style);

        $this->class('table '.implode(' ', (array)$this->style), true);
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

    public function level($level)
    {
        $this->level = $level;

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
        $noTrPadding = false;

        if (Arr::isAssoc($rows)) {
            foreach ($rows as $key => $item) {
                if (is_array($item)) {
                    if (Arr::isAssoc($item)) {
                        $borderLeft = $this->level ? 'table-left-border-nofirst' : 'table-left-border';

                        $item = static::make()
                            ->level($this->level + 1)
                            ->setRows($item)
                            ->class('table-no-top-border '.$borderLeft, true)
                            ->render();

                        if (!$noTrPadding) {
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

        $this->rows = $rows;

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
        $this->style = $style;

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
            'style'      => $this->style,
            'attributes' => $this->formatAttributes(),
        ];

        return view($this->view, $vars)->render();
    }
}
