<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Grid\Events\ApplyFilter;
use Dcat\Admin\Grid\Events\Fetched;
use Dcat\Admin\Grid\Events\Fetching;
use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Between;
use Dcat\Admin\Grid\Filter\Date;
use Dcat\Admin\Grid\Filter\Day;
use Dcat\Admin\Grid\Filter\EndWith;
use Dcat\Admin\Grid\Filter\Equal;
use Dcat\Admin\Grid\Filter\FindInSet;
use Dcat\Admin\Grid\Filter\Group;
use Dcat\Admin\Grid\Filter\Gt;
use Dcat\Admin\Grid\Filter\Hidden;
use Dcat\Admin\Grid\Filter\Ilike;
use Dcat\Admin\Grid\Filter\In;
use Dcat\Admin\Grid\Filter\Layout\Layout;
use Dcat\Admin\Grid\Filter\Like;
use Dcat\Admin\Grid\Filter\Lt;
use Dcat\Admin\Grid\Filter\Month;
use Dcat\Admin\Grid\Filter\Newline;
use Dcat\Admin\Grid\Filter\Ngt;
use Dcat\Admin\Grid\Filter\Nlt;
use Dcat\Admin\Grid\Filter\NotEqual;
use Dcat\Admin\Grid\Filter\NotIn;
use Dcat\Admin\Grid\Filter\Scope;
use Dcat\Admin\Grid\Filter\StartWith;
use Dcat\Admin\Grid\Filter\Where;
use Dcat\Admin\Grid\Filter\WhereBetween;
use Dcat\Admin\Grid\Filter\Year;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Traits\HasVariables;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Filter.
 *
 * @method Equal equal($column, $label = '')
 * @method NotEqual notEqual($column, $label = '')
 * @method Like like($column, $label = '')
 * @method Ilike ilike($column, $label = '')
 * @method StartWith startWith($column, $label = '')
 * @method EndWith endWith($column, $label = '')
 * @method Gt gt($column, $label = '')
 * @method Lt lt($column, $label = '')
 * @method Ngt ngt($column, $label = '')
 * @method Nlt nlt($column, $label = '')
 * @method Between between($column, $label = '')
 * @method In in($column, $label = '')
 * @method NotIn notIn($column, $label = '')
 * @method Where where($colum, $callback, $label = '')
 * @method WhereBetween whereBetween($colum, $callback, $label = '')
 * @method Date date($column, $label = '')
 * @method Day day($column, $label = '')
 * @method Month month($column, $label = '')
 * @method Year year($column, $label = '')
 * @method Hidden hidden($name, $value)
 * @method Group group($column, $builder = null, $label = '')
 * @method Newline newline()
 * @method FindInSet findInSet($column, $label = '')
 */
class Filter implements Renderable
{
    use HasBuilderEvents;
    use Macroable;
    use HasVariables;

    const MODE_RIGHT_SIDE = 'right-side';
    const MODE_PANEL = 'panel';

    /**
     * @var array
     */
    protected static $supports = [];

    /**
     * @var array
     */
    protected static $defaultFilters = [
        'equal'        => Equal::class,
        'notEqual'     => NotEqual::class,
        'ilike'        => Ilike::class,
        'like'         => Like::class,
        'startWith'    => StartWith::class,
        'endWith'      => EndWith::class,
        'gt'           => Gt::class,
        'lt'           => Lt::class,
        'ngt'          => Ngt::class,
        'nlt'          => Nlt::class,
        'between'      => Between::class,
        'group'        => Group::class,
        'where'        => Where::class,
        'whereBetween' => WhereBetween::class,
        'in'           => In::class,
        'notIn'        => NotIn::class,
        'date'         => Date::class,
        'day'          => Day::class,
        'month'        => Month::class,
        'year'         => Year::class,
        'hidden'       => Hidden::class,
        'newline'      => Newline::class,
        'findInSet'    => FindInSet::class,
    ];

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var AbstractFilter[]
     */
    protected $filters = [];

    /**
     * Action of search form.
     *
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $filterID;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var bool
     */
    public $expand;

    /**
     * @var Collection
     */
    protected $scopes;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * Primary key of giving model.
     *
     * @var mixed
     */
    protected $primaryKey;

    /**
     * @var string
     */
    protected $style = 'padding:0';

    /**
     * @var bool
     */
    protected $disableResetButton = false;

    /**
     * @var string
     */
    protected $border = 'border-top:1px solid #f4f4f4;';

    /**
     * @var string
     */
    protected $containerClass = '';

    /**
     * @var bool
     */
    protected $disableCollapse = false;

    /**
     * @var array
     */
    protected $inputs;

    /**
     * @var string
     */
    protected $mode = self::MODE_RIGHT_SIDE;

    protected $conditions;

    /**
     * Create a new filter instance.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        $this->primaryKey = $model->getKeyName();

        $this->filterID = $this->formatFilterId();

        $this->initLayout();

        $this->scopes = new Collection();

        $this->callResolving();
    }

    /**
     * Initialize filter layout.
     */
    protected function initLayout()
    {
        $this->layout = new Filter\Layout\Layout($this);
    }

    /**
     * @return string
     */
    protected function formatFilterId()
    {
        return 'filter-box'.Str::random(8);
    }

    /**
     * Set action of search form.
     *
     * @param  string  $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutInputBorder()
    {
        $this->containerClass = 'input-no-border';

        return $this;
    }

    /**
     * @param  bool  $disabled
     * @return $this
     */
    public function disableCollapse(bool $disabled = true)
    {
        $this->disableCollapse = $disabled;

        return $this;
    }

    /**
     * @param  bool  $disabled
     * @return $this
     */
    public function disableResetButton(bool $disabled = true)
    {
        $this->disableResetButton = $disabled;

        return $this;
    }

    /**
     * Get input data.
     *
     * @param  string  $key
     * @param  null  $value
     * @return array|mixed
     */
    public function input($key = null, $default = null)
    {
        $inputs = $this->inputs();

        if ($key === null) {
            return $inputs;
        }

        return Arr::get($inputs, $key, $default);
    }

    /**
     * Get grid model.
     *
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Get grid.
     *
     * @return \Dcat\Admin\Grid
     */
    public function grid()
    {
        return $this->model->grid();
    }

    /**
     * Set ID of search form.
     *
     * @param  string  $filterID
     * @return $this
     */
    public function setFilterID($filterID)
    {
        $this->filterID = $filterID;

        return $this;
    }

    /**
     * @return $this
     */
    public function panel()
    {
        return $this->mode(static::MODE_PANEL);
    }

    /**
     * @return $this
     */
    public function rightSide()
    {
        return $this->mode(static::MODE_RIGHT_SIDE);
    }

    /**
     * @param  string|null  $mode
     * @return $this|string
     */
    public function mode(string $mode = null)
    {
        if ($mode === null) {
            return $this->mode;
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * Get filter ID.
     *
     * @return string
     */
    public function filterID()
    {
        return $this->filterID;
    }

    /**
     * @return $this
     */
    public function withoutBorder()
    {
        return $this->withBorder('');
    }

    /**
     * @return $this
     */
    public function withBorder($border = null)
    {
        $this->border = is_null($border) ? 'border-top:1px solid #f4f4f4;' : $border;

        return $this;
    }

    /**
     * Remove filter by column.
     *
     * @param  string|array  $column
     */
    public function removeFilter($column)
    {
        $this->filters = array_filter($this->filters, function (AbstractFilter $filter) use (&$column) {
            if (is_array($column)) {
                return ! in_array($filter->column(), $column);
            }

            return $filter->column() != $column;
        });
    }

    /**
     * @return array
     */
    public function inputs()
    {
        if (! is_null($this->inputs)) {
            return $this->inputs;
        }

        $this->inputs = Arr::dot(request()->all());

        $this->inputs = array_filter($this->inputs, function ($input) {
            return $input !== '' && ! is_null($input);
        });

        $this->sanitizeInputs($this->inputs);

        return $this->inputs;
    }

    /**
     * Get all conditions of the filters.
     *
     * @return array
     */
    public function getConditions()
    {
        $inputs = $this->inputs();

        if (empty($inputs)) {
            return [];
        }

        if ($this->conditions !== null) {
            return $this->conditions;
        }

        $params = [];

        foreach ($inputs as $key => $value) {
            Arr::set($params, $key, $value);
        }

        $conditions = [];

        foreach ($this->filters() as $filter) {
            $conditions[] = $filter->condition($params);
        }

        return tap(array_filter($conditions), function ($conditions) {
            if (! empty($conditions)) {
                if ($this->expand === null || $this->mode !== static::MODE_RIGHT_SIDE) {
                    $this->expand();
                }

                $this->grid()->fireOnce(new ApplyFilter([$conditions]));

                $this->grid()->model()->disableBindTreeQuery();
            }

            $this->conditions = $conditions;
        });
    }

    /**
     * @param  array  $inputs
     * @return void
     */
    protected function sanitizeInputs(&$inputs)
    {
        if (! $prefix = $this->grid()->getNamePrefix()) {
            return;
        }

        $inputs = collect($inputs)->filter(function ($input, $key) use ($prefix) {
            return Str::startsWith($key, $prefix);
        })->mapWithKeys(function ($val, $key) use ($prefix) {
            $key = str_replace($prefix, '', $key);

            return [$key => $val];
        })->toArray();
    }

    /**
     * Add a filter to grid.
     *
     * @param  AbstractFilter  $filter
     * @return AbstractFilter
     */
    protected function addFilter(AbstractFilter $filter)
    {
        $this->layout->addFilter($filter);

        $filter->setParent($this);

        return $this->filters[] = $filter;
    }

    /**
     * Use a custom filter.
     *
     * @param  AbstractFilter  $filter
     * @return AbstractFilter
     */
    public function use(AbstractFilter $filter)
    {
        return $this->addFilter($filter);
    }

    /**
     * Get all filters.
     *
     * @return AbstractFilter[]
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * 统计查询条件的数量.
     *
     * @return int
     */
    public function countConditions()
    {
        return $this->mode() === Filter::MODE_RIGHT_SIDE
            ? count($this->getConditions()) : 0;
    }

    /**
     * @param  string  $key
     * @param  string  $label
     * @return Scope
     */
    public function scope($key, $label = '')
    {
        $scope = new Scope($this, $key, $label);

        $this->scopes->push($scope);

        return $scope;
    }

    /**
     * @return string
     */
    public function getScopeQueryName()
    {
        return $this->grid()->makeName('_scope_');
    }

    /**
     * Get all filter scopes.
     *
     * @return Collection
     */
    public function scopes()
    {
        return $this->scopes;
    }

    /**
     * Get current scope.
     *
     * @return Scope|null
     */
    public function getCurrentScope()
    {
        $key = $this->getCurrentScopeName();

        return $this->scopes->first(function ($scope) use ($key) {
            return $scope->key == $key;
        });
    }

    /**
     * Get the name of current scope.
     *
     * @return string
     */
    public function getCurrentScopeName()
    {
        return request($this->getScopeQueryName());
    }

    /**
     * Get scope conditions.
     *
     * @return array
     */
    protected function getScopeConditions()
    {
        if ($scope = $this->getCurrentScope()) {
            return $scope->condition();
        }

        return [];
    }

    /**
     * Expand filter container.
     *
     * @param  bool  $value
     * @return $this
     */
    public function expand(bool $value = true)
    {
        $this->expand = $value;

        return $this;
    }

    /**
     * Execute the filter with conditions.
     *
     * @return Collection|mixed
     */
    public function execute()
    {
        $conditions = array_merge(
            $this->getConditions(),
            $this->getScopeConditions()
        );

        $this->model->addConditions($conditions);

        $this->grid()->fireOnce(new Fetching());

        $data = $this->model->buildData();

        $this->grid()->fireOnce(new Fetched([&$data]));

        return $data;
    }

    /**
     * @param  string  $top
     * @param  string  $right
     * @param  string  $bottom
     * @param  string  $left
     * @return Filter
     */
    public function padding($top = '15px', $right = '15px', $bottom = '5px', $left = '')
    {
        return $this->style("padding:$top $right $bottom $left");
    }

    /**
     * @param  string  $style
     * @return $this
     */
    public function style(?string $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return $this
     */
    public function noPadding()
    {
        return $this->style('padding:0;left:-4px;');
    }

    /**
     * @return $this
     */
    public function hiddenResetButtonText()
    {
        Admin::style(".{$this->containerClass} a.reset .d-none d-sm-inline{display:none}");

        return $this;
    }

    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the string contents of the filter view.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $this->grid()->callBuilder();

        if (empty($this->filters)) {
            return '';
        }

        $this->callComposing();

        if (! $this->view) {
            $this->view = $this->mode === static::MODE_RIGHT_SIDE ? 'admin::filter.right-side-container' : 'admin::filter.container';
        }

        return view($this->view)->with($this->variables())->render();
    }

    protected function defaultVariables()
    {
        return [
            'action'             => $this->action ?: $this->urlWithoutFilters(),
            'layout'             => $this->layout,
            'filterID'           => $this->disableCollapse ? '' : $this->filterID,
            'expand'             => $this->expand,
            'style'              => $this->style,
            'border'             => $this->border,
            'containerClass'     => $this->containerClass,
            'disableResetButton' => $this->disableResetButton,
        ];
    }

    /**
     * Get url without filter queryString.
     *
     * @return string
     */
    public function urlWithoutFilters()
    {
        $filters = collect($this->filters);

        /** @var Collection $columns */
        $columns = $filters->map->getElementName()->flatten();

        $columns->push(
            $this->grid()->model()->getPageName()
        );

        $groupNames = $filters->filter(function ($filter) {
            return $filter instanceof Group;
        })->map(function (AbstractFilter $filter) {
            return "{$filter->getId()}_group";
        });

        return Helper::fullUrlWithoutQuery(
            $columns->merge($groupNames)
        );
    }

    /**
     * Get url without scope queryString.
     *
     * @return string
     */
    public function urlWithoutScopes()
    {
        return Helper::fullUrlWithoutQuery($this->getScopeQueryName());
    }

    /**
     * Generate a filter object and add to grid.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return AbstractFilter|$this
     */
    public function __call($method, $arguments)
    {
        if (! empty(static::$supports[$method])) {
            $class = static::$supports[$method];
            if (! is_subclass_of($class, AbstractFilter::class)) {
                throw new RuntimeException("The class [{$class}] must be a type of ".AbstractFilter::class.'.');
            }

            return $this->addFilter(new $class(...$arguments));
        }

        if (isset(static::$defaultFilters[$method])) {
            return $this->addFilter(new static::$defaultFilters[$method](...$arguments));
        }

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $filterClass
     */
    public static function extend($name, $filterClass)
    {
        static::$supports[$name] = $filterClass;
    }

    /**
     * @return array
     */
    public static function extensions()
    {
        return static::$supports;
    }
}
