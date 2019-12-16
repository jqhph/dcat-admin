<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Selector
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array|Collection
     */
    protected $selectors = [];

    /**
     * @var array
     */
    protected $selected;

    /**
     * @var string
     */
    protected $queryName;

    /**
     * Selector constructor.
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
        $this->request = request();
        $this->selectors = new Collection();
        $this->queryName = $grid->getName().'_selector';
    }

    /**
     * @param string         $column
     * @param string|array   $label
     * @param array|\Closure $options
     * @param null|\Closure  $query
     *
     * @return $this
     */
    public function select(string $column, $label, $options = [], ?\Closure $query = null)
    {
        return $this->addSelector($column, $label, $options, $query);
    }

    /**
     * @param string        $column
     * @param string        $label
     * @param array         $options
     * @param null|\Closure $query
     *
     * @return $this
     */
    public function selectOne(string $column, $label, $options = [], ?\Closure $query = null)
    {
        return $this->addSelector($column, $label, $options, $query, 'one');
    }

    /**
     * @param string $column
     * @param string $label
     * @param array  $options
     * @param null   $query
     * @param string $type
     *
     * @return $this
     */
    protected function addSelector(string $column, $label, $options = [], ?\Closure $query = null, $type = 'many')
    {
        if (is_array($label)) {
            if ($options instanceof \Closure) {
                $query = $options;
            }

            $options = $label;
            $label = admin_trans_field($column);
        }

        $this->selectors[$column] = compact(
            'label',
            'options',
            'type',
            'query'
        );

        return $this;
    }

    /**
     * Get all selectors.
     *
     * @return array|Collection
     */
    public function all()
    {
        return $this->selectors;
    }

    /**
     * @return array
     */
    public function parseSelected()
    {
        if (! is_null($this->selected)) {
            return $this->selected;
        }

        $selected = $this->request->get($this->queryName, []);
        if (! is_array($selected)) {
            return [];
        }

        $selected = array_filter($selected, function ($value) {
            return ! is_null($value);
        });

        foreach ($selected as &$value) {
            $value = explode(',', $value);
        }

        return $this->selected = $selected;
    }

    /**
     * @param string $column
     * @param mixed  $value
     * @param bool   $add
     *
     * @return string
     */
    public function url($column, $value = null, $add = false)
    {
        $query = $this->request->query();

        $selected = $this->parseSelected();

        $options = Arr::get($selected, $column, []);

        if (is_null($value)) {
            Arr::forget($query, "{$this->queryName}.{$column}");

            return $this->request->fullUrlWithQuery($query);
        }

        if (in_array($value, $options)) {
            Helper::deleteByValue($options, $value);
        } else {
            if ($add) {
                $options = [];
            }
            array_push($options, $value);
        }

        if (! empty($options)) {
            Arr::set($query, "{$this->queryName}.{$column}", implode(',', $options));
        } else {
            Arr::forget($query, "{$this->queryName}.{$column}");
        }

        return $this->request->fullUrlWithQuery($query);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        return view('admin::grid.selector', [
            'self'     => $this,
            'selected' => $this->parseSelected(),
        ]);
    }
}
