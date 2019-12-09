<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\Selector;

/**
 * @mixin Grid
 */
trait HasSelector
{
    /**
     * @var Selector
     */
    protected $_selector;

    /**
     * @param \Closure $closure
     *
     * @return $this|Selector
     */
    public function selector(\Closure $closure = null)
    {
        if ($closure === null) {
            return $this->_selector;
        }

        $this->_selector = new Selector($this);

        call_user_func($closure, $this->_selector);

        $this->header(function () {
            return $this->renderSelector();
        });

        return $this;
    }

    /**
     * Apply selector query to grid model query.
     *
     * @return $this
     */
    protected function applySelectorQuery()
    {
        if (is_null($this->_selector)) {
            return $this;
        }

        $active = $this->_selector->parseSelected();

        $this->_selector->all()->each(function ($selector, $column) use ($active) {
            if (! array_key_exists($column, $active)) {
                return;
            }

            $values = $active[$column];
            if ($selector['type'] == 'one') {
                $values = current($values);
            }

            if (is_null($selector['query'])) {
                $this->model()->whereIn($column, $values);
            } else {
                call_user_func($selector['query'], $this->model(), $values);
            }
        });

        return $this;
    }

    /**
     * Render grid selector.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function renderSelector()
    {
        return $this->_selector->render();
    }
}
