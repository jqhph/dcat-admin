<?php

namespace Dcat\Admin\Grid;

use Closure;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Traits\BuilderEvents;
use Dcat\Admin\Traits\Definitions;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

/**
 * @method $this switch(string $color = '')
 * @method $this switchGroup($columns = [], string $color = '')
 * @method $this editable($method = null, array $options = []);
 * @method $this image($server = '', int $width = 200, int $height = 200);
 * @method $this label($style = 'success', int $max = null);
 * @method $this button($style = 'success');
 * @method $this link($href = '', $target = '_blank');
 * @method $this badge($style = 'red');
 * @method $this progressBar($style = 'primary', $size = 'sm', $max = 100)
 * @method $this checkbox($options = [])
 * @method $this radio($options = [])
 * @method $this expand($callbackOrButton = null)
 * @method $this table($titles = [])
 * @method $this select($options = [])
 * @method $this modal($title = '', \Closure $callback = null)
 * @method $this tree($callbackOrNodes = null)
 * @method $this qrcode($formatter = null, $width = 150, $height = 150)
 * @method $this downloadable()
 * @method $this copyable()
 *
 * @method $this limit($limit = 100, $end = '...')
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
 */
class Column
{
    use BuilderEvents,
        Definitions,
        Concerns\Displayers;

    const SELECT_COLUMN_NAME = '__row_selector__';

    /**
     * Displayers for grid column.
     *
     * @var array
     */
    protected static $displayers = [
        'editable'     => Displayers\Editable::class,
        'switch'       => Displayers\SwitchDisplay::class,
        'switchGroup'  => Displayers\SwitchGroup::class,
        'select'       => Displayers\Select::class,
        'image'        => Displayers\Image::class,
        'label'        => Displayers\Label::class,
        'button'       => Displayers\Button::class,
        'link'         => Displayers\Link::class,
        'badge'        => Displayers\Badge::class,
        'progressBar'  => Displayers\ProgressBar::class,
        'radio'        => Displayers\Radio::class,
        'checkbox'     => Displayers\Checkbox::class,
        'table'        => Displayers\Table::class,
        'expand'       => Displayers\Expand::class,
        'modal'        => Displayers\Modal::class,
        'tree'         => Displayers\Tree::class,
        'qrcode'       => Displayers\QRCode::class,
        'downloadable' => Displayers\Downloadable::class,
        'copyable'     => Displayers\Copyable::class,
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
     * Is column sortable.
     *
     * @var bool
     */
    protected $sortable = false;

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
     * @param string $name
     * @param string $label
     */
    public function __construct($name, $label)
    {
        $this->name = $name;

        $this->label = $this->formatLabel($label);

        $this->callResolving();
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
    public static function getExtensions()
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
     * Set original data for column.
     *
     * @param Collection $collection
     */
    public static function setOriginalGridModels(Collection $collection)
    {
        static::$originalGridModels = $collection;
    }

    /**
     * Set width for column.
     *
     * @param string $width
     * @return $this|string
     */
    public function width(?string $width)
    {
        $this->titleHtmlAttributes['width'] = $width;
        return $this;
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
     *
     * @return $this
     */
    public function hide()
    {
        return $this->responsive(0);
    }

    /**
     * responsive
     *
     * data-priority=”1″ 保持可见，但可以在下拉列表筛选隐藏。
     * data-priority=”2″ 480px 分辨率以下可见
     * data-priority=”3″ 640px 以下可见
     * data-priority=”4″ 800px 以下可见
     * data-priority=”5″ 960px 以下可见
     * data-priority=”6″ 1120px 以下可见
     *
     * @param int $priority
     * @return $this
     */
    public function responsive(?int $priority = 1)
    {
        $this->grid->responsive();

        return $this->setHeaderAttributes(['data-priority' => $priority]);
    }

    /**
     * @return int|null
     */
    public function getDataPriority()
    {
        return isset($this->titleHtmlAttributes['data-priority']) ? $this->titleHtmlAttributes['data-priority'] : null;
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
     * @param $label
     *
     * @return mixed
     */
    protected function formatLabel($label)
    {
        if (!$label) $label = admin_trans_field($this->name);

        $label = $label ?: ucfirst($this->name);

        return str_replace(['.', '_'], ' ', $label);
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

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set sort value.
     *
     * @param bool $sort
     *
     * @return Column
     */
    public function sort(bool $sort)
    {
        $this->sortable = $sort;

        return $this;
    }

    /**
     * Mark this column as sortable.
     *
     * @return $this
     */
    public function sortable()
    {
        return $this->sort(true);
    }

    /**
     * Add a display callback.
     *
     * @param $callback
     * @param array $params
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
    protected function hasDisplayCallbacks()
    {
        return !empty($this->displayCallbacks);
    }

    /**
     * Call all of the "display" callbacks column.
     *
     * @param mixed $value
     * @param int   $key
     *
     * @return mixed
     */
    protected function callDisplayCallbacks($value, $key)
    {
        foreach ($this->displayCallbacks as $callback) {
            list($callback, $params) = $callback;

            if (!$callback instanceof \Closure) {
                $value = $callback;
                continue;
            }

            $previous = $value;

            $callback = $this->bindOriginalRowModel($callback, $key);
            $value = $callback($value, $this, ...$params);

            if (($value instanceof static) &&
                ($last = array_pop($this->displayCallbacks))
            ) {
                list($last, $params) = $last;
                $last = $this->bindOriginalRowModel($last, $key);
                $value = call_user_func($last, $previous, $this, ...$params);
            }
        }

        return $value;
    }

    /**
     * Set original grid data to column.
     *
     * @param Closure $callback
     * @param int     $key
     *
     * @return Closure
     */
    protected function bindOriginalRowModel(Closure $callback, $key)
    {
        $rowModel = static::$originalGridModels[$key];

        if (is_array($rowModel)) {
            $rowModel = new Fluent($rowModel);
        }

        return $callback->bindTo($rowModel);
    }

    /**
     * Fill all data to every column.
     *
     * @param array $data
     */
    public function fill(array &$data)
    {
        if (static::hasDefinition($this->name)) {
            $this->useDefinedColumn();
        }

        $i = 0;
        foreach ($data as $key => &$row) {
            $i++;
            if (!isset($row['#'])) {
                $row['#'] = $i;
            }

            $this->original = $value = Arr::get($row, $this->name);

            $this->value = $value = $this->htmlEntityEncode($value);

            Arr::set($row, $this->name, $value);

            if ($this->hasDisplayCallbacks()) {
                $value = $this->callDisplayCallbacks($this->original, $key);
                Arr::set($row, $this->name, $value);
            }
        }

        $this->value = $value ?? null;
    }

    /**
     * Use a defined column.
     *
     * @throws \Exception
     */
    protected function useDefinedColumn()
    {
        $class = static::$definitions[$this->name];

        if ($class instanceof Closure) {
            $this->display($class);
            return;
        }

        if (!class_exists($class) || !is_subclass_of($class, AbstractDisplayer::class)) {
            throw new \Exception("Invalid column definition [$class]");
        }

        $this->displayUsing($class);
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
        if (is_array($item)) {
            array_walk_recursive($item, function (&$value) {
                $value = htmlentities($value);
            });
        } else {
            $item = htmlentities($item);
        }

        return $item;
    }


    /**
     * Create the column sorter.
     *
     * @return string
     */
    public function sorter()
    {
        if (!$this->sortable) {
            return '';
        }

        $icon  = '';
        $color = 'text-70';
        $type  = 'desc';

        if ($this->isSorted()) {
            $type = $this->sort['type'] == 'desc' ? 'asc' : 'desc';
            if ($this->sort['type']) {
                $icon .= $this->sort['type'] == 'desc' ? '-by-attributes-alt' : '-by-attributes';

                $color = 'text-80';
            }
        }

        $url = request()->fullUrlWithQuery([
            $this->grid->model()->getSortName() => ['column' => $this->name, 'type' => $type]
        ]);

        return " <a class=' glyphicon glyphicon-sort{$icon} $color' href='$url'></a>";
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
        return $this->resolveDisplayer($method, $arguments);
    }
}
