<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;

abstract class Filter implements Renderable
{
    /**
     * @var string|array
     */
    protected $class;

    /**
     * @var Column
     */
    protected $parent;

    /**
     * @var string
     */
    protected $getColumnName;

    /**
     * @var \Closure[]
     */
    protected $resolvings = [];

    /**
     * @var bool
     */
    protected $display = true;

    /**
     * @param Column $column
     */
    public function setParent(Column $column)
    {
        $this->parent = $column;

        $this->parent->grid()->fetching(function () {
            $this->addResetButton();

            $this->parent->grid()->model()->treeUrlWithoutQuery(
                $this->getQueryName()
            );
        });

        foreach ($this->resolvings as $closure) {
            $closure($this);
        }
    }

    /**
     * @return Column
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * @param \Closure $callback
     *
     * @return $this
     */
    public function resolving(\Closure $callback)
    {
        $this->resolvings[] = $callback;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setColumnName(string $name)
    {
        $this->getColumnName = $name;

        return $this;
    }

    /**
     * Get column name.
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->getColumnName ?: $this->parent->getName();
    }

    /**
     * @return string
     */
    public function getQueryName()
    {
        return $this->parent->grid()->getName().
            '_filter_'.
            $this->getColumnName();
    }

    /**
     * Get filter value of this column.
     *
     * @param string $default
     *
     * @return array|\Illuminate\Http\Request|string
     */
    public function value($default = '')
    {
        return request($this->getQueryName(), $default);
    }

    /**
     * Add reset button.
     */
    protected function addResetButton()
    {
        $value = $this->value();
        if ($value === '' || $value === null) {
            return;
        }

        $style = $this->shouldDisplay() ? 'style=\'margin:3px 14px\'' : '';

        return $this->parent->addHeader(
            "&nbsp;<a class='feather icon-rotate-ccw' href='{$this->urlWithoutFilter()}' {$style}></a>"
        );
    }

    /**
     * Get form action url.
     *
     * @return string
     */
    public function formAction()
    {
        return Helper::fullUrlWithoutQuery([
            $this->getQueryName(),
            $this->getColumnName(),
            $this->parent->grid()->model()->getPageName(),
            '_pjax',
        ]);
    }

    /**
     * @return string
     */
    protected function urlWithoutFilter()
    {
        return Helper::fullUrlWithoutQuery([
            $this->getQueryName(),
        ]);
    }

    /**
     * @param string $key
     *
     * @return array|null|string
     */
    protected function trans($key)
    {
        return __("admin.{$key}");
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function display(bool $value)
    {
        $this->display = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function hide()
    {
        return $this->display(false);
    }

    /**
     * @return bool
     */
    public function shouldDisplay()
    {
        return $this->display;
    }

    /**
     * Add a query binding.
     *
     * @param mixed $value
     * @param Model $model
     */
    public function addBinding($value, Model $model)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        //
    }

    /**
     * @param array ...$params
     *
     * @return static
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }
}
