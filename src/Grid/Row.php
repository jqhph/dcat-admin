<?php

namespace Dcat\Admin\Grid;

use Closure;
use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

class Row
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Row data.
     *
     * @var
     */
    protected $data;

    /**
     * Attributes of row.
     *
     * @var array
     */
    protected $attributes = [];

    public function __construct(Grid $grid, $data)
    {
        $this->grid = $grid;
        $this->data = $data;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->model->getKey();
    }

    /**
     * Get attributes in html format.
     *
     * @return string
     */
    public function getRowAttributes()
    {
        return $this->formatHtmlAttributes($this->attributes);
    }

    /**
     * Get column attributes.
     *
     * @param string $column
     *
     * @return string
     */
    public function getColumnAttributes($column)
    {
        if (
            ($column = $this->grid->getColumns()->get($column))
            && ($attributes = $column->getAttributes())
        ) {
            return $this->formatHtmlAttributes($attributes);
        }
        return '';
    }

    /**
     * Format attributes to html.
     *
     * @param array $attributes
     *
     * @return string
     */
    private function formatHtmlAttributes($attributes = [])
    {
        $attrArr = [];
        foreach ($attributes as $name => $val) {
            $attrArr[] = "$name=\"$val\"";
        }

        return implode(' ', $attrArr);
    }

    /**
     * Set attributes.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Set style of the row.
     *
     * @param array|string $style
     */
    public function style($style)
    {
        if (is_array($style)) {
            $style = implode('', array_map(function ($key, $val) {
                return "$key:$val";
            }, array_keys($style), array_values($style)));
        }

        if (is_string($style)) {
            $this->attributes['style'] = $style;
        }
    }

    /**
     * Get data of this row.
     *
     * @return mixed
     */
    public function model()
    {
        return $this->data;
    }

    /**
     * Getter.
     *
     * @param mixed $attr
     *
     * @return mixed
     */
    public function __get($attr)
    {
        return Arr::get($this->data, $attr);
    }

    /**
     * Get or set value of column in this row.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this|mixed
     */
    public function column($name, $value = null)
    {
        if (is_null($value)) {
            $column = Arr::get($this->data, $name);

            return $this->output($column);
        }

        if ($value instanceof Closure) {
            $value = $value->call($this, $this->column($name));
        }

        Arr::set($this->data, $name, $value);

        return $this;
    }

    /**
     * Output column value.
     *
     * @param mixed $value
     *
     * @return mixed|string
     */
    protected function output($value)
    {
        if ($value instanceof Renderable) {
            $value = $value->render();
        }

        if ($value instanceof Htmlable) {
            $value = $value->toHtml();
        }

        if ($value instanceof Jsonable) {
            $value = $value->toJson();
        }

        if (!is_null($value) && !is_scalar($value)) {
            return sprintf('<pre class="dump">%s</pre>', json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $value;
    }
}
