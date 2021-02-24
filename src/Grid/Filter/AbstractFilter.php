<?php

namespace Dcat\Admin\Grid\Filter;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Presenter\Checkbox;
use Dcat\Admin\Grid\Filter\Presenter\DateTime;
use Dcat\Admin\Grid\Filter\Presenter\MultipleSelect;
use Dcat\Admin\Grid\Filter\Presenter\Presenter;
use Dcat\Admin\Grid\Filter\Presenter\Radio;
use Dcat\Admin\Grid\Filter\Presenter\Select;
use Dcat\Admin\Grid\Filter\Presenter\Text;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Traits\HasVariables;
use Dcat\Laravel\Database\WhereHasInServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class AbstractFilter.
 *
 * @method Text url()
 * @method Text email()
 * @method Text integer()
 * @method Text decimal($options = [])
 * @method Text currency($options = [])
 * @method Text percentage($options = [])
 * @method Text ip()
 * @method Text mac()
 * @method Text mobile($mask = '19999999999')
 * @method Text inputmask($options = [], $icon = '')
 * @method Text placeholder($placeholder = '')
 */
abstract class AbstractFilter
{
    use HasVariables;

    /**
     * Element id.
     *
     * @var array|string
     */
    protected $id;

    /**
     * Label of presenter.
     *
     * @var string
     */
    protected $label;

    /**
     * @var array|string
     */
    protected $value;

    /**
     * @var array|string
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $column;

    /**
     * Presenter object.
     *
     * @var Presenter
     */
    protected $presenter;

    /**
     * Query for filter.
     *
     * @var string
     */
    protected $query = 'where';

    /**
     * @var Filter
     */
    protected $parent;

    /**
     * @var int
     */
    protected $width = 10;

    /**
     * @var string
     */
    protected $style;

    /**
     * @var string
     */
    protected $view = 'admin::filter.where';

    /**
     * @var Collection
     */
    public $group;

    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * AbstractFilter constructor.
     *
     * @param $column
     * @param string $label
     */
    public function __construct($column, $label = '')
    {
        $this->column = $column;
        $this->label = $this->formatLabel($label);
    }

    /**
     * Setup default presenter.
     *
     * @return void
     */
    protected function setupDefaultPresenter()
    {
        $this->setPresenter(new Text($this->label));
    }

    /**
     * Format label.
     *
     * @param string $label
     *
     * @return string
     */
    protected function formatLabel($label)
    {
        if ($label) {
            return $label;
        }

        $label = admin_trans_field($this->column);

        return str_replace('_', ' ', $label);
    }

    /**
     * Set the column width.
     *
     * @param int|string $width
     *
     * @return $this
     */
    public function width($width)
    {
        if (is_numeric($width)) {
            $this->width = $width;
        } else {
            $this->style = "width:$width;padding-left:10px;padding-right:10px";
            $this->width = ' ';
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->parent->grid()->makeName($this->originalColumn());
    }

    /**
     * Format name.
     *
     * @param string $column
     *
     * @return string
     */
    protected function formatName($column)
    {
        $columns = explode('.', $column);

        if (count($columns) == 1) {
            $name = $columns[0];
        } else {
            $name = array_shift($columns);
            foreach ($columns as $column) {
                $name .= "[$column]";
            }
        }

        return $this->parent->grid()->makeName($name);
    }

    /**
     * Format id.
     *
     * @param string|array $columns
     *
     * @return array|string
     */
    protected function formatId($columns)
    {
        if (is_array($columns)) {
            foreach ($columns as &$column) {
                $column = $this->formatId($column);
            }

            return $columns;
        }

        return $this->parent->grid()->makeName('filter-column-'.str_replace('.', '-', $columns));
    }

    /**
     * @param Filter $filter
     */
    public function setParent(Filter $filter)
    {
        $this->parent = $filter;

        $this->id = $this->formatId($this->column);
    }

    /**
     * @return Filter
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * Get siblings of current filter.
     *
     * @param null $index
     *
     * @return AbstractFilter[]|mixed
     */
    public function siblings($index = null)
    {
        if (! is_null($index)) {
            return Arr::get($this->parent->filters(), $index);
        }

        return $this->parent->filters();
    }

    /**
     * Get previous filter.
     *
     * @param int $step
     *
     * @return AbstractFilter[]|mixed
     */
    public function previous($step = 1)
    {
        return $this->siblings(
            array_search($this, $this->parent->filters()) - $step
        );
    }

    /**
     * Get next filter.
     *
     * @param int $step
     *
     * @return AbstractFilter[]|mixed
     */
    public function next($step = 1)
    {
        return $this->siblings(
            array_search($this, $this->parent->filters()) + $step
        );
    }

    /**
     * Get query condition from filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|null
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if ($value === null) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, $this->value);
    }

    /**
     * Ignore this query filter.
     *
     * @return $this
     */
    public function ignore()
    {
        $this->ignore = true;

        return $this;
    }

    /**
     * Select filter.
     *
     * @param array $options
     *
     * @return Select
     */
    public function select($options = [])
    {
        return $this->setPresenter(new Select($options));
    }

    /**
     * @param array $options
     *
     * @return MultipleSelect
     */
    public function multipleSelect($options = [])
    {
        return $this->setPresenter(new MultipleSelect($options));
    }

    /**
     * @param LazyRenderable $table
     *
     * @return Filter\Presenter\SelectTable
     */
    public function selectTable(LazyRenderable $table)
    {
        return $this->setPresenter(new Filter\Presenter\SelectTable($table));
    }

    /**
     * @param LazyRenderable $table
     *
     * @return Filter\Presenter\MultipleSelectTable
     */
    public function multipleSelectTable(LazyRenderable $table)
    {
        return $this->setPresenter(new Filter\Presenter\MultipleSelectTable($table));
    }

    /**
     * @param array $options
     *
     * @return Radio
     */
    public function radio($options = [])
    {
        return $this->setPresenter(new Radio($options));
    }

    /**
     * @param array $options
     *
     * @return Checkbox
     */
    public function checkbox($options = [])
    {
        return $this->setPresenter(new Checkbox($options));
    }

    /**
     * Datetime filter.
     *
     * @param array $options
     *
     * @return DateTime
     */
    public function datetime($options = [])
    {
        return $this->setPresenter(new DateTime($options));
    }

    /**
     * Date filter.
     *
     * @return DateTime
     */
    public function date()
    {
        return $this->datetime(['format' => 'YYYY-MM-DD']);
    }

    /**
     * Time filter.
     *
     * @return DateTime
     */
    public function time()
    {
        return $this->datetime(['format' => 'HH:mm:ss']);
    }

    /**
     * Day filter.
     *
     * @return DateTime
     */
    public function day()
    {
        return $this->datetime(['format' => 'DD']);
    }

    /**
     * Month filter.
     *
     * @return DateTime
     */
    public function month()
    {
        return $this->datetime(['format' => 'YYYY-MM']);
    }

    /**
     * Year filter.
     *
     * @return DateTime
     */
    public function year()
    {
        return $this->datetime(['format' => 'YYYY']);
    }

    /**
     * Set presenter object of filter.
     *
     * @param Presenter $presenter
     *
     * @return mixed
     */
    public function setPresenter(Presenter $presenter)
    {
        $presenter->setParent($this);

        $presenter::requireAssets();

        return $this->presenter = $presenter;
    }

    /**
     * Get presenter object of filter.
     *
     * @return Presenter
     */
    protected function presenter()
    {
        if (! $this->presenter) {
            $this->setupDefaultPresenter();
        }

        return $this->presenter;
    }

    /**
     * Set default value for filter.
     *
     * @param null $default
     *
     * @return $this
     */
    public function default($default = null)
    {
        if ($default) {
            $this->defaultValue = $default;
        }

        return $this;
    }

    public function getDefault()
    {
        return $this->defaultValue;
    }

    /**
     * Get element id.
     *
     * @return array|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set element id.
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $this->formatId($id);

        return $this;
    }

    /**
     * Get column name of current filter.
     *
     * @return string
     */
    public function column()
    {
        return $this->formatColumnClass($this->column);
    }

    public function originalColumn()
    {
        return $this->column;
    }

    /**
     * @param string $column
     *
     * @return string
     */
    public function formatColumnClass($column)
    {
        return $this->parent->grid()->makeName(str_replace('.', '-', $column));
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get value of current filter.
     *
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Build conditions of filter.
     *
     * @return mixed
     */
    protected function buildCondition(...$params)
    {
        if ($this->ignore) {
            return;
        }

        $column = explode('.', $this->column);

        if (count($column) == 1) {
            return [$this->query => &$params];
        }

        return $this->buildRelationQuery(...$params);
    }

    /**
     * @param mixed ...$params
     *
     * @return array
     */
    protected function buildRelationQuery(...$params)
    {
        $column = explode('.', $this->column);

        $params[0] = array_pop($column);

        // 增加对whereHasIn的支持
        $method = class_exists(WhereHasInServiceProvider::class) ? 'whereHasIn' : 'whereHas';

        return [$method => [implode('.', $column), function ($q) use ($params) {
            call_user_func_array([$q, $this->query], $params);
        }]];
    }

    /**
     * Variables for filter view.
     *
     * @return array
     */
    protected function defaultVariables()
    {
        return array_merge([
            'id'    => $this->id,
            'name'  => $this->formatName($this->column),
            'label' => $this->label,
            'value' => $this->normalizeValue(),
            'width' => $this->width,
            'style' => $this->style,
        ], $this->presenter()->variables());
    }

    protected function normalizeValue()
    {
        if ($this->value === '' || $this->value === null) {
            $this->value = Arr::get($this->parent->inputs(), $this->column);
        }

        return $this->value === '' || $this->value === null ? $this->defaultValue : $this->value;
    }

    /**
     * Render this filter.
     *
     * @return string
     */
    public function render()
    {
        $variables = $this->variables();

        $variables['presenter'] = $this->renderPresenter();

        return Admin::view($this->view, $variables);
    }

    /**
     * @return string
     *
     * @throws \Throwable
     */
    protected function renderPresenter()
    {
        return function () {
            return Admin::view($this->presenter->view(), $this->variables());
        };
    }

    /**
     * Render this filter.
     *
     * @return \Illuminate\View\View|string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param $method
     * @param $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        if (method_exists($this->presenter(), $method)) {
            return $this->presenter()->{$method}(...$params);
        }

        throw new RuntimeException(sprintf(
            'Call to undefined method %s::%s()', static::class, $method
        ));
    }
}
