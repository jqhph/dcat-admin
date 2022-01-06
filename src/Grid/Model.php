<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @mixin Builder
 */
class Model
{
    use Grid\Concerns\HasTree;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var AbstractPaginator
     */
    protected $paginator;

    /**
     * Array of queries of the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $queries;

    /**
     * Sort parameters of the model.
     *
     * @var array
     */
    protected $sort;

    /**
     * @var Collection
     */
    protected $data;

    /**
     * @var callable
     */
    protected $builder;

    /*
     * 20 items per page as default.
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * @var string
     */
    protected $pageName = 'page';

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * If the model use pagination.
     *
     * @var bool
     */
    protected $usePaginate = true;

    /**
     * The query string variable used to store the per-page.
     *
     * @var string
     */
    protected $perPageName = 'per_page';

    /**
     * The query string variable used to store the sort.
     *
     * @var string
     */
    protected $sortName = '_sort';

    /**
     * @var bool
     */
    protected $simple = false;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var array
     */
    protected $eagerLoads = [];

    /**
     * @var array
     */
    protected $constraints = [];

    /**
     * Create a new grid model instance.
     *
     * @param  Repository|\Illuminate\Database\Eloquent\Model  $repository
     * @param  Request  $request
     */
    public function __construct(Request $request, $repository = null)
    {
        if ($repository) {
            $this->repository = Admin::repository($repository);
        }

        $this->request = $request;
        $this->initQueries();
    }

    /**
     * @return void
     */
    protected function initQueries()
    {
        $this->queries = new Collection();
    }

    /**
     * @return Repository|null
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * @return Collection
     */
    public function getQueries()
    {
        return $this->queries = $this->queries->unique();
    }

    /**
     * @param  Collection  $query
     * @return void
     */
    public function setQueries(Collection $query)
    {
        $this->queries = $query;
    }

    /**
     * @return AbstractPaginator|LengthAwarePaginator
     */
    public function paginator(): ?AbstractPaginator
    {
        $this->buildData();

        return $this->paginator;
    }

    /**
     * 是否使用 simplePaginate方法进行分页.
     *
     * @param  bool  $value
     * @return $this
     */
    public function simple(bool $value = true)
    {
        $this->simple = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaginateMethod()
    {
        return $this->simple ? 'simplePaginate' : 'paginate';
    }

    /**
     * @param  int  $total
     * @param  Collection|array  $data
     * @return LengthAwarePaginator|Paginator
     */
    public function makePaginator($total, $data, string $url = null)
    {
        if ($this->simple) {
            $paginator = new Paginator($data, $this->getPerPage(), $this->getCurrentPage());
        } else {
            $paginator = new LengthAwarePaginator(
                $data,
                $total,
                $this->getPerPage(), // 传入每页显示行数
                $this->getCurrentPage() // 传入当前页码
            );
        }

        return $paginator->setPath(
            $url ?: url()->current()
        );
    }

    /**
     * Get primary key name of model.
     *
     * @return string|array
     */
    public function getKeyName()
    {
        return $this->grid->getKeyName();
    }

    /**
     * Enable or disable pagination.
     *
     * @param  bool  $use
     *
     * @reutrn $this;
     */
    public function usePaginate($use = true)
    {
        $this->usePaginate = $use;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowPagination()
    {
        return $this->usePaginate;
    }

    /**
     * Get the query string variable used to store the per-page.
     *
     * @return string
     */
    public function getPerPageName()
    {
        return $this->grid->makeName($this->perPageName);
    }

    /**
     * @param  int  $perPage
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->grid->makeName($this->pageName);
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function setPageName($name)
    {
        $this->pageName = $name;

        return $this;
    }

    /**
     * Get the query string variable used to store the sort.
     *
     * @return string
     */
    public function getSortName()
    {
        return $this->grid->makeName($this->sortName);
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function setSortName($name)
    {
        $this->sortName = $name;

        return $this;
    }

    /**
     * Set parent grid instance.
     *
     * @param  Grid  $grid
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * Get parent gird instance.
     *
     * @return Grid
     */
    public function grid()
    {
        return $this->grid;
    }

    /**
     * Get filter of Grid.
     *
     * @return Filter
     */
    public function filter()
    {
        return $this->grid->filter();
    }

    /**
     * Get constraints.
     *
     * @return array|bool
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param  array  $constraints
     * @return $this
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * Build.
     *
     * @return Collection
     */
    public function buildData()
    {
        if (is_null($this->data)) {
            $this->setData($this->fetch());
        }

        return $this->data;
    }

    /**
     * @param  Collection|callable|array|AbstractPaginator  $data
     * @return $this
     */
    public function setData($data)
    {
        if (is_callable($data)) {
            $this->builder = $data;

            return $this;
        }

        if ($data instanceof AbstractPaginator) {
            $this->setPaginator($data);

            $data = $data->getCollection();
        } elseif ($data instanceof Collection) {
        } elseif ($data instanceof Arrayable || is_array($data)) {
            $data = collect($data);
        }

        if ($data instanceof Collection) {
            $this->data = $data;
        } else {
            $this->data = collect();
        }

        $this->stdObjToArray($this->data);

        return $this;
    }

    /**
     * Add conditions to grid model.
     *
     * @param  array  $conditions
     * @return $this
     */
    public function addConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            call_user_func_array([$this, key($condition)], current($condition));
        }

        return $this;
    }

    /**
     * @return Collection|array
     *
     * @throws \Exception
     */
    protected function fetch()
    {
        if ($this->paginator) {
            return $this->paginator->getCollection();
        }

        if ($this->builder && is_callable($this->builder)) {
            $results = call_user_func($this->builder, $this);
        } else {
            $results = $this->repository->get($this);
        }

        if (is_array($results) || $results instanceof Collection) {
            return $results;
        }

        if ($results instanceof AbstractPaginator) {
            $this->setPaginator($results);

            return $results->getCollection();
        }

        throw new AdminException('Grid query error');
    }

    /**
     * @param  AbstractPaginator  $paginator
     * @return void
     */
    protected function setPaginator(AbstractPaginator $paginator)
    {
        $this->paginator = $paginator;

        if ($this->simple) {
            if (method_exists($paginator, 'withQueryString')) {
                $paginator->withQueryString();
            } else {
                $paginator->appends(request()->all());
            }
        }

        $paginator->setPageName($this->getPageName());
    }

    /**
     * @param  Collection  $collection
     * @return Collection
     */
    protected function stdObjToArray(Collection $collection)
    {
        return $collection->transform(function ($item) {
            if ($item instanceof \stdClass) {
                return (array) $item;
            }

            return $item;
        });
    }

    /**
     * Get current page.
     *
     * @return int|null
     */
    public function getCurrentPage()
    {
        if (! $this->usePaginate) {
            return;
        }

        return $this->currentPage ?: ($this->currentPage = ($this->request->get($this->getPageName()) ?: 1));
    }

    /**
     * @param  int  $currentPage
     */
    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get items number of per page.
     *
     * @return int|null
     */
    public function getPerPage()
    {
        if (! $this->usePaginate) {
            return;
        }

        $perPage = $this->request->get($this->getPerPageName()) ?: $this->perPage;
        if ($perPage) {
            return (int) $perPage;
        }

        return null;
    }

    /**
     * Find query by method name.
     *
     * @param $method
     * @return Collection
     */
    public function findQueryByMethod($method)
    {
        return $this->queries->where('method', $method);
    }

    /**
     * @param  string|callable  $method
     * @return $this
     */
    public function filterQueryBy($method)
    {
        $this->queries = $this->queries->filter(function ($query, $k) use ($method) {
            if (
                (is_string($method) && $query['method'] === $method)
                || (is_array($method) && in_array($query['method'], $method, true))
            ) {
                return false;
            }

            if (is_callable($method)) {
                return call_user_func($method, $query, $k);
            }

            return true;
        });

        return $this;
    }

    /**
     * Get the grid sort.
     *
     * @return array exp: ['name', 'desc']
     */
    public function getSort()
    {
        if (empty($this->sort)) {
            $this->sort = $this->request->get($this->getSortName());
        }

        if (empty($this->sort['column']) || empty($this->sort['type'])) {
            return [null, null, null];
        }

        return [$this->sort['column'], $this->sort['type'], $this->sort['cast'] ?? null];
    }

    /**
     * @param  string|array  $method
     * @return void
     */
    public function rejectQuery($method)
    {
        $this->queries = $this->queries->reject(function ($query) use ($method) {
            if (is_callable($method)) {
                return call_user_func($method, $query);
            }

            return in_array($query['method'], (array) $method, true);
        });
    }

    /**
     * Reset orderBy query.
     *
     * @return void
     */
    public function resetOrderBy()
    {
        $this->rejectQuery(['orderBy', 'orderByDesc']);
    }

    /**
     * @param  string  $method
     * @param  array  $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {
        return $this->addQuery($method, $arguments);
    }

    /**
     * @param  string  $method
     * @param  array  $arguments
     * @return $this
     */
    public function addQuery(string $method, array $arguments = [])
    {
        $this->queries->push([
            'method'    => $method,
            'arguments' => $arguments,
        ]);

        return $this;
    }

    public function getSortQueries()
    {
        return $this->findQueryByMethod('orderBy')
            ->merge($this->findQueryByMethod('orderByDesc'))
            ->merge($this->findQueryByMethod('latest'))
            ->merge($this->findQueryByMethod('oldest'));
    }

    public function getSortDescMethods()
    {
        return ['orderByDesc', 'latest'];
    }

    /**
     * @param  Builder  $query
     * @param  bool  $fetch
     * @param  string[]  $columns
     * @return Builder|Paginator|Collection
     */
    public function apply($query, bool $fetch = false, $columns = null)
    {
        $this->getQueries()->unique()->each(function ($value) use (&$query, $fetch, $columns) {
            if (! $fetch && in_array($value['method'], ['paginate', 'simplePaginate', 'get'], true)) {
                return;
            }

            if ($columns) {
                if (in_array($value['method'], ['paginate', 'simplePaginate'], true)) {
                    $value['arguments'][1] = $columns;
                } elseif ($value['method'] === 'get') {
                    $value['arguments'] = [$columns];
                }
            }

            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ?? []);
        });

        return $query;
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param  mixed  $relations
     * @return $this|Model
     */
    public function with($relations)
    {
        if (is_array($relations)) {
            if (Arr::isAssoc($relations)) {
                $relations = array_keys($relations);
            }

            $this->eagerLoads = array_merge($this->eagerLoads, $relations);
        }

        if (is_string($relations)) {
            if (Str::contains($relations, '.')) {
                $relations = explode('.', $relations)[0];
            }

            if (Str::contains($relations, ':')) {
                $relations = explode(':', $relations)[0];
            }

            if (in_array($relations, $this->eagerLoads)) {
                return $this;
            }

            $this->eagerLoads[] = $relations;
        }

        return $this->addQuery('with', (array) $relations);
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->data = null;
        $this->model = null;
        $this->initQueries();
    }
}
