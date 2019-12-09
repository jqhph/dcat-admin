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
     * @param Column $column
     */
    public function setParent(Column $column)
    {
        $this->parent = $column;
    }

    /**
     * Get column name.
     *
     * @return string
     */
    public function columnName()
    {
        return $this->parent->getName();
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
