<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Dcat\Admin\Form\Row;

trait HasRows
{
    /**
     * Field rows in form.
     *
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Add a row in form.
     *
     * @param Closure $callback
     * @param array $options
     *
     * @return $this
     */
    public function row(Closure $callback, array $options = [])
    {
        $this->rows[] = new Row($callback, $this, $options);

        return $this;
    }

    /**
     * @return Row[]
     */
    public function rows()
    {
        return $this->rows;
    }
}
