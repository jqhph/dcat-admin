<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property string $parentColumn
 * @property string $titleColumn
 * @property string $orderColumn
 * @property array  $sortable
 */
trait ModelTree
{
    use SortableTrait;

    /**
     * @var array
     */
    protected static $branchOrder = [];

    /**
     * @var \Closure[]
     */
    protected $queryCallbacks = [];

    /**
     * Get children of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, $this->getParentColumn());
    }

    /**
     * Get parent of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, $this->getParentColumn());
    }

    /**
     * @return string
     */
    public function getParentColumn()
    {
        return empty($this->parentColumn) ? 'parent_id' : $this->parentColumn;
    }

    /**
     * Set parent column.
     *
     * @param string $column
     */
    public function setParentColumn(string $column)
    {
        $this->parentColumn = $column;
    }

    /**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        return empty($this->titleColumn) ? 'title' : $this->titleColumn;
    }

    /**
     * Set title column.
     *
     * @param string $column
     */
    public function setTitleColumn(string $column)
    {
        $this->titleColumn = $column;
    }

    /**
     * Get order column name.
     *
     * @return string
     */
    public function getOrderColumn()
    {
        return empty($this->orderColumn) ? 'order' : $this->orderColumn;
    }

    /**
     * Set order column.
     *
     * @param string $column
     */
    public function setOrderColumn(string $column)
    {
        $this->orderColumn = $column;
    }


    /**
     * Set query callback to model.
     *
     * @param \Closure|null $query
     *
     * @return $this
     */
    public function withQuery(\Closure $query = null)
    {
        $this->queryCallbacks[] = $query;

        return $this;
    }

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree(array $nodes = null)
    {
        if ($nodes === null) {
            $nodes = $this->allNodes();
        }

        return Helper::buildNestedArray(
            $nodes,
            0,
            $this->getKeyName(),
            $this->getParentColumn()
        );
    }

    /**
     * Get all elements.
     *
     * @return mixed
     */
    public function allNodes()
    {
        $orderColumn = DB::getQueryGrammar()->wrap($this->getOrderColumn());
        $byOrder = 'ROOT ASC, '.$orderColumn;

        return $this->callQueryCallbacks(new static())
            ->selectRaw('*, '.$orderColumn.' ROOT')
            ->orderByRaw($byOrder)
            ->get()
            ->toArray();
    }

    /**
     * @param $this $model
     *
     * @return $this|Builder
     */
    protected function callQueryCallbacks($model)
    {
        foreach ($this->queryCallbacks as $callback) {
            if ($callback) {
                $model = $callback($model);
            }
        }

        return $model;
    }

    /**
     * Set the order of branches in the tree.
     *
     * @param array $order
     *
     * @return void
     */
    protected static function setBranchOrder(array $order)
    {
        static::$branchOrder = array_flip(Arr::flatten($order));

        static::$branchOrder = array_map(function ($item) {
            return ++$item;
        }, static::$branchOrder);
    }

    /**
     * Save tree order from a tree like array.
     *
     * @param array $tree
     * @param int   $parentId
     */
    public static function saveOrder($tree = [], $parentId = 0)
    {
        if (empty(static::$branchOrder)) {
            static::setBranchOrder($tree);
        }

        foreach ($tree as $branch) {
            $node = static::find($branch['id']);

            $node->{$node->getParentColumn()} = $parentId;
            $node->{$node->getOrderColumn()} = static::$branchOrder[$branch['id']];
            $node->save();

            if (isset($branch['children'])) {
                static::saveOrder($branch['children'], $branch['id']);
            }
        }
    }

    protected function determineOrderColumnName()
    {
        return $this->getOrderColumn();
    }

    public function moveOrderDown()
    {
        $orderColumnName = $this->determineOrderColumnName();
        $parentColumnName = $this->getParentColumn();

        $swapWithModel = $this->buildSortQuery()->limit(1)
            ->ordered()
            ->where($orderColumnName, '>', $this->$orderColumnName)
            ->where($parentColumnName, $this->$parentColumnName)
            ->first();

        if (! $swapWithModel) {
            return $this;
        }

        return $this->swapOrderWithModel($swapWithModel);
    }

    public function moveOrderUp()
    {
        $orderColumnName = $this->determineOrderColumnName();
        $parentColumnName = $this->getParentColumn();

        $swapWithModel = $this->buildSortQuery()->limit(1)
            ->ordered('desc')
            ->where($orderColumnName, '<', $this->$orderColumnName)
            ->where($parentColumnName, $this->$parentColumnName)
            ->first();

        if (! $swapWithModel) {
            return $this;
        }

        return $this->swapOrderWithModel($swapWithModel);
    }

    public function moveToStart()
    {
        $parentColumnName = $this->getParentColumn();

        $firstModel = $this->buildSortQuery()->limit(1)
            ->ordered()
            ->where($parentColumnName, $this->$parentColumnName)
            ->first();

        if ($firstModel->id === $this->id) {
            return $this;
        }

        $orderColumnName = $this->determineOrderColumnName();

        $this->$orderColumnName = $firstModel->$orderColumnName;
        $this->save();

        $this->buildSortQuery()->where($this->getKeyName(), '!=', $this->id)->increment($orderColumnName);

        return $this;
    }

    public function getHighestOrderNumber(): int
    {
        $parentColumnName = $this->getParentColumn();

        return (int) $this->buildSortQuery()
            ->where($parentColumnName, $this->$parentColumnName)
            ->max($this->determineOrderColumnName());
    }

    /**
     * Get options for Select field in form.
     *
     * @param \Closure|null $closure
     * @param string        $rootText
     *
     * @return array
     */
    public static function selectOptions(\Closure $closure = null, $rootText = null)
    {
        $rootText = $rootText ?: admin_trans_label('root');

        $options = (new static())->withQuery($closure)->buildSelectOptions();

        return collect($options)->prepend($rootText, 0)->all();
    }

    /**
     * Build options of select field in form.
     *
     * @param array  $nodes
     * @param int    $parentId
     * @param string $prefix
     *
     * @return array
     */
    protected function buildSelectOptions(array $nodes = [], $parentId = 0, $prefix = '')
    {
        $prefix = $prefix ?: str_repeat('&nbsp;', 6);

        $options = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        $titleColumn = $this->getTitleColumn();
        $parentColumn = $this->getParentColumn();

        foreach ($nodes as $node) {
            $node[$titleColumn] = $prefix.'&nbsp;'.$node[$titleColumn];
            if ($node[$parentColumn] == $parentId) {
                $children = $this->buildSelectOptions($nodes, $node[$this->getKeyName()], $prefix.$prefix);

                $options[$node[$this->getKeyName()]] = $node[$titleColumn];

                if ($children) {
                    $options += $children;
                }
            }
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $this->where($this->getParentColumn(), $this->getKey())->delete();

        return parent::delete();
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        if (! trait_exists('\Spatie\EloquentSortable\SortableTrait')) {
            throw new \Exception('To use ModelTree, please install package [spatie/eloquent-sortable] first.');
        }

        static::saving(function (Model $branch) {
            $parentColumn = $branch->getParentColumn();

            if (Request::has($parentColumn) && Request::input($parentColumn) == $branch->getKey()) {
                throw new \Exception(trans('admin.parent_select_error'));
            }

            if (Request::has('_order')) {
                $order = Request::input('_order');

                Request::offsetUnset('_order');

                Tree::make(new static())->saveOrder($order);

                return false;
            }

            return $branch;
        });
    }
}
