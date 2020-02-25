<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

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
    protected $columnName;

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

        $this->addResetButton();

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
        $this->columnName = $name;

        return $this;
    }

    /**
     * Get column name.
     *
     * @return string
     */
    public function columnName()
    {
        return $this->columnName ?: $this->parent->getName();
    }

    /**
     * @return string
     */
    public function queryName()
    {
        return $this->parent->grid()->getName().
            '_filter_'.
            $this->columnName();
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
        return request($this->queryName(), $default);
    }

    /**
     * Add reset button.
     */
    protected function addResetButton()
    {
        $this->parent->grid()->filtering(function () {
            $value = $this->value();
            if ($value === '' || $value === null) {
                return;
            }

            $style = $this->shouldDisplay() ? 'style=\'margin:3px 12px\'' : '';

            return $this->parent->addHeader(
                "&nbsp;<a class='fa fa-undo' href='{$this->urlWithoutFilter()}' {$style}></a>"
            );
        });
    }

    /**
     * Get form action url.
     *
     * @return string
     */
    public function formAction()
    {
        $request = request();

        $query = $request->query();
        Arr::forget($query, [
            $this->columnName(),
            $this->parent->grid()->model()->getPageName(),
            '_pjax',
        ]);

        $question = $request->getBaseUrl().$request->getPathInfo() == '/' ? '/?' : '?';

        return count($request->query()) > 0
            ? $request->url().$question.http_build_query($query)
            : $request->fullUrl();
    }

    /**
     * @return string
     */
    protected function urlWithoutFilter()
    {
        $query = app('request')->all();
        unset($query[$this->queryName()]);

        return Helper::urlWithQuery(url()->current(), $query);
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
