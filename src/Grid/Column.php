<?php

namespace Dcat\Admin\Grid;

use Closure;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasBuilderEvents;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * @method $this editable(bool $refresh = false)
 * @method $this switch(string $color = '', $refresh = false)
 * @method $this switchGroup($columns = [], string $color = '', $refresh = false)
 * @method $this image($server = '', int $width = 200, int $height = 200)
 * @method $this label($style = 'primary', int $max = null)
 * @method $this button($style = 'success');
 * @method $this link($href = '', $target = '_blank');
 * @method $this badge($style = 'primary', int $max = null);
 * @method $this progressBar($style = 'primary', $size = 'sm', $max = 100)
 * @method $this checkbox($options = [], $refresh = false)
 * @method $this radio($options = [], $refresh = false)
 * @method $this expand($callbackOrButton = null)
 * @method $this table($titles = [])
 * @method $this select($options = [], $refresh = false)
 * @method $this modal($title = '', $callback = null)
 * @method $this showTreeInDialog($callbackOrNodes = null)
 * @method $this qrcode($formatter = null, $width = 150, $height = 150)
 * @method $this downloadable($server = '', $disk = null)
 * @method $this copyable()
 * @method $this orderable()
 * @method $this limit(int $limit = 100, string $end = '...')
 * @method $this ascii()
 * @method $this camel()
 * @method $this finish($cap)
 * @method $this lower()
 * @method $this words($words = 100, $end = '...')
 * @method $this upper()
 * @method $this title()
 * @method $this slug($separator = '-')
 * @method $this snake($delimiter = '_')
 * @method $this studly()
 * @method $this substr($start, $length = null)
 * @method $this ucfirst()
 *
 * @mixin Collection
 */
class Column
{
    use HasBuilderEvents;
    use Grid\Column\HasHeader;
    use Grid\Column\HasDisplayers;
    use Macroable {
        __call as __macroCall;
    }

    const SELECT_COLUMN_NAME = '__row_selector__';
    const ACTION_COLUMN_NAME = '__actions__';

    /**
     * Displayers for grid column.
     *
     * @var array
     */
    protected static $displayers = [
        'switch'           => Displayers\SwitchDisplay::class,
        'switchGroup'      => Displayers\SwitchGroup::class,
        'select'           => Displayers\Select::class,
        'image'            => Displayers\Image::class,
        'label'            => Displayers\Label::class,
        'button'           => Displayers\Button::class,
        'link'             => Displayers\Link::class,
        'badge'            => Displayers\Badge::class,
        'progressBar'      => Displayers\ProgressBar::class,
        'radio'            => Displayers\Radio::class,
        'checkbox'         => Displayers\Checkbox::class,
        'table'            => Displayers\Table::class,
        'expand'           => Displayers\Expand::class,
        'modal'            => Displayers\Modal::class,
        'showTreeInDialog' => Displayers\DialogTree::class,
        'qrcode'           => Displayers\QRCode::class,
        'downloadable'     => Displayers\Downloadable::class,
        'copyable'         => Displayers\Copyable::class,
        'orderable'        => Displayers\Orderable::class,
        'limit'            => Displayers\Limit::class,
        'editable'         => Displayers\Editable::class,
    ];

    /**
     * Original grid data.
     *
     * @var Collection
     */
    protected static $originalGridModels;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Name of column.
     *
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * Label of column.
     *
     * @var string
     */
    protected $label;

    /**
     * @var Fluent
     */
    protected $originalModel;

    /**
     * Original value of column.
     *
     * @var mixed
     */
    protected $original;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Sort arguments.
     *
     * @var array
     */
    protected $sort;

    /**
     * @var string
     */
    protected $width;

    /**
     * Attributes of column.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var []Closure
     */
    protected $displayCallbacks = [];

    /**
     * @var array
     */
    protected $titleHtmlAttributes = [];

    /**
     * @var Model
     */
    protected static $model;

    /**
     * @var Grid\Column\Condition
     */
    protected $conditions = [];

    /**
     * @param string $name
     * @param string $label
     */
    public function __construct($name, $label)
    {
        $this->name = $this->formatName($name);

        $this->label = $this->formatLabel($label);

        $this->callResolving();
    }

    protected function formatName($name)
    {
        return $name;
    }

    /**
     * Extend column displayer.
     *
     * @param $name
     * @param $displayer
     */
    public static function extend($name, $displayer)
    {
        static::$displayers[$name] = $displayer;
    }

    /**
     * @return array
     */
    public static function extensions()
    {
        return static::$displayers;
    }

    /**
     * Set grid instance for column.
     *
     * @param Grid $grid
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return Grid
     */
    public function grid()
    {
        return $this->grid;
    }

    /**
     * Set original data for column.
     *
     * @param Collection $collection
     */
    public static function setOriginalGridModels(Collection $collection)
    {
        static::$originalGridModels = $collection->map(function ($row) {
            if (is_object($row)) {
                return clone $row;
            }

            return $row;
        });
    }

    /**
     * Set width for column.
     *
     * @param string $width
     *
     * @return $this|string
     */
    public function width(?string $width)
    {
        $this->titleHtmlAttributes['width'] = $width;

        return $this;
    }

    /**
     * @example
     *     $grid->column('...')
     *         ->if(function ($column) {
     *             return $column->getValue() ? true : false;
     *         })
     *         ->display($view)
     *         ->expand(...)
     *         ->else()
     *         ->display('')
     *
     *    $grid->column('...')
     *         ->if()
     *         ->then(function (Column $column) {
     *             $column ->display($view)->expand(...);
     *         })
     *         ->else(function (Column $column) {
     *             $column->emptyString();
     *         })
     *
     *     $grid->column('...')
     *         ->if()
     *         ->display($view)
     *         ->expand(...)
     *         ->else()
     *         ->display('')
     *         ->end()
     *         ->modal()
     *
     * @param \Closure $condition
     *
     * @return Column\Condition
     */
    public function if(\Closure $condition = null)
    {
        $condition = $condition ?: function ($column) {
            return $column->getValue();
        };

        return $this->conditions[] = new Grid\Column\Condition($condition, $this);
    }

    /**
     * Set column attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->htmlAttributes = array_merge($this->htmlAttributes, $attributes);

        return $this;
    }

    /**
     * Get column attributes.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->htmlAttributes;
    }

    /**
     * @return $this
     */
    public function hide()
    {
        $this->grid->hideColumns($this->getName());

        return $this;
    }

    /**
     * Set style of this column.
     *
     * @param string $style
     *
     * @return Column
     */
    public function style($style)
    {
        return $this->setAttributes(compact('style'));
    }

    /**
     * Get name of this column.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array|Model $model
     */
    public function setOriginalModel($model)
    {
        if (is_array($model)) {
            $model = new Fluent($model);
        }

        $this->originalModel = $model;
    }

    /**
     * @return Fluent|Model
     */
    public function getOriginalModel()
    {
        return $this->originalModel;
    }

    /**
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Format label.
     *
     * @param string $label
     *
     * @return mixed
     */
    protected function formatLabel($label)
    {
        return $label ?: str_replace('_', ' ', admin_trans_field($this->name));
    }

    /**
     * Get label of the column.
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Add a display callback.
     *
     * @param \Closure|string $callback
     * @param array           $params
     *
     * @return $this
     */
    public function display($callback, ...$params)
    {
        $this->displayCallbacks[] = [&$callback, &$params];

        return $this;
    }

    /**
     * If has display callbacks.
     *
     * @return bool
     */
    public function hasDisplayCallbacks()
    {
        return ! empty($this->displayCallbacks);
    }

    /**
     * @param array $callbacks
     *
     * @return void
     */
    public function setDisplayCallbacks(array $callbacks)
    {
        $this->displayCallbacks = $callbacks;
    }

    /**
     * @return \Closure[]
     */
    public function getDisplayCallbacks()
    {
        return $this->displayCallbacks;
    }

    /**
     * Call all of the "display" callbacks column.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function callDisplayCallbacks($value)
    {
        foreach ($this->displayCallbacks as $callback) {
            [$callback, $params] = $callback;

            if (! $callback instanceof \Closure) {
                $value = $callback;
                continue;
            }

            $previous = $value;

            $callback = $this->bindOriginalRowModel($callback);
            $value = $callback($value, $this, ...$params);

            if (
                $value instanceof static
                && ($last = array_pop($this->displayCallbacks))
            ) {
                [$last, $params] = $last;
                $last = $this->bindOriginalRowModel($last);
                $value = call_user_func($last, $previous, $this, ...$params);
            }
        }

        return $value;
    }

    /**
     * Set original grid data to column.
     *
     * @param Closure $callback
     *
     * @return Closure
     */
    protected function bindOriginalRowModel(Closure $callback)
    {
        return $callback->bindTo($this->getOriginalModel());
    }

    /**
     * Fill all data to every column.
     *
     * @param \Illuminate\Support\Collection $data
     */
    public function fill($data)
    {
        $i = 0;

        $data->transform(function ($row, $key) use (&$i) {
            $this->setOriginalModel(static::$originalGridModels[$key]);

            $this->originalModel['_index'] = $row['_index'] = $i;

            $row = $this->convertModelToArray($row);

            $i++;
            if (! isset($row['#'])) {
                $row['#'] = $i;
            }

            $this->original = Arr::get($this->originalModel, $this->name);

            $this->value = $value = $this->htmlEntityEncode($original = Arr::get($row, $this->name));

            if ($original === null) {
                $original = (string) $original;
            }

            $this->processConditions();

            if ($this->hasDisplayCallbacks()) {
                $value = $this->callDisplayCallbacks($this->original);
            }

            if ($original !== $value) {
                Helper::arraySet($row, $this->name, $value);
            }

            $this->value = $value ?? null;

            return $row;
        });
    }

    /**
     * 把模型转化为数组.
     *
     * @param array|Model $row
     *
     * @return mixed
     */
    protected function convertModelToArray(&$row)
    {
        if (is_array($row)) {
            return $row;
        }

        $array = $row->toArray();

        return Helper::camelArray($array);
    }

    /**
     * @return void
     */
    protected function processConditions()
    {
        foreach ($this->conditions as $condition) {
            $condition->reset();
        }

        foreach ($this->conditions as $condition) {
            $condition->process();
        }
    }

    /**
     * Convert characters to HTML entities recursively.
     *
     * @param array|string $item
     *
     * @return mixed
     */
    protected function htmlEntityEncode($item)
    {
        return Helper::htmlEntityEncode($item);
    }

    /**
     * Determine if this column is currently sorted.
     *
     * @return bool
     */
    protected function isSorted()
    {
        $this->sort = app('request')->get($this->grid->model()->getSortName());

        if (empty($this->sort)) {
            return false;
        }

        return isset($this->sort['column']) && $this->sort['column'] == $this->name;
    }

    /**
     * Find a displayer to display column.
     *
     * @param string $abstract
     * @param array  $arguments
     *
     * @return Column
     */
    protected function resolveDisplayer($abstract, $arguments)
    {
        if (isset(static::$displayers[$abstract])) {
            return $this->callBuiltinDisplayer(static::$displayers[$abstract], $arguments);
        }

        return $this->callSupportDisplayer($abstract, $arguments);
    }

    /**
     * Call Illuminate/Support displayer.
     *
     * @param string $abstract
     * @param array  $arguments
     *
     * @return Column
     */
    protected function callSupportDisplayer($abstract, $arguments)
    {
        return $this->display(function ($value) use ($abstract, $arguments) {
            if (is_array($value) || $value instanceof Arrayable) {
                return call_user_func_array([collect($value), $abstract], $arguments);
            }

            if (is_string($value)) {
                return call_user_func_array([Str::class, $abstract], array_merge([$value], $arguments));
            }

            return $value;
        });
    }

    /**
     * Call Builtin displayer.
     *
     * @param string $abstract
     * @param array  $arguments
     *
     * @return Column
     */
    protected function callBuiltinDisplayer($abstract, $arguments)
    {
        if ($abstract instanceof Closure) {
            return $this->display(function ($value) use ($abstract, $arguments) {
                return $abstract->call($this, ...array_merge([$value], $arguments));
            });
        }

        if (is_subclass_of($abstract, AbstractDisplayer::class)) {
            $grid = $this->grid;
            $column = $this;

            return $this->display(function ($value) use ($abstract, $grid, $column, $arguments) {
                /** @var AbstractDisplayer $displayer */
                $displayer = new $abstract($value, $grid, $column, $this);

                return $displayer->display(...$arguments);
            });
        }

        return $this;
    }

    /**
     * Set column title attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setHeaderAttributes(array $attributes = [])
    {
        $this->titleHtmlAttributes = array_merge($this->titleHtmlAttributes, $attributes);

        return $this;
    }

    /**
     * Set column title default attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function setDefaultHeaderAttribute(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (isset($this->titleHtmlAttributes[$key])) {
                continue;
            }

            $this->titleHtmlAttributes[$key] = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function formatTitleAttributes()
    {
        $attrArr = [];
        foreach ($this->titleHtmlAttributes as $name => $val) {
            $attrArr[] = "$name=\"$val\"";
        }

        return implode(' ', $attrArr);
    }

    /**
     * @param  mixed  $value
     * @param  callable  $callback
     *
     * @return $this|mixed
     */
    public function when($value, $callback)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * Passes through all unknown calls to builtin displayer or supported displayer.
     *
     * Allow fluent calls on the Column object.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if (
            ! isset(static::$displayers[$method])
            && static::hasMacro($method)
        ) {
            return $this->__macroCall($method, $arguments);
        }

        return $this->resolveDisplayer($method, $arguments);
    }
}
