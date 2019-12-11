<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Middleware\Pjax;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @mixin Builder
 */
class Model
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var EloquentModel
     */
    protected $model;

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
     * @var Collection|LengthAwarePaginator
     */
    protected $data = null;

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
     * Collection callback.
     *
     * @var callable[]
     */
    protected $collectionCallback = [];

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
     * @param Repository|\Illuminate\Database\Eloquent\Model $repository
     * @param Request                                        $request
     */
    public function __construct(Request $request, $repository = null)
    {
        if ($repository) {
            $this->repository = Admin::repository($repository);
        }

        $this->request = $request;
        $this->queries = collect();
    }

    /**
     * @return Repository|null
     */
    public function getRepository()
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
     * @param Collection $query
     *
     * @return void
     */
    public function setQueries(Collection $query)
    {
        $this->queries = $query;
    }

    /**
     * @return LengthAwarePaginator|Collection
     */
    public function paginator()
    {
        return $this->get();
    }

    /**
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->grid->keyName();
    }

    /**
     * Enable or disable pagination.
     *
     * @param bool $use
     */
    public function usePaginate($use = true)
    {
        $this->usePaginate = $use;
    }

    /**
     * Get the query string variable used to store the per-page.
     *
     * @return string
     */
    public function getPerPageName()
    {
        return $this->perPageName;
    }

    /**
     * Set the query string variable used to store the per-page.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setPerPageName($name)
    {
        $this->perPageName = $name;

        return $this;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @param string $pageName
     */
    public function setPageName(string $pageName)
    {
        $this->pageName = $pageName;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * Get the query string variable used to store the sort.
     *
     * @return string
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * Set the query string variable used to store the sort.
     *
     * @param string $name
     *
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
     * @param Grid $grid
     *
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
     * @param array $constraints
     *
     * @return $this
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * Set collection callback.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function collection(callable $callback = null)
    {
        $this->collectionCallback[] = $callback;

        return $this;
    }

    /**
     * Build.
     *
     * @param bool $toArray
     *
     * @return array|Collection|mixed
     */
    public function buildData(bool $toArray = true)
    {
        if (is_null($this->data) || $this->data instanceof \Closure) {
            $this->setData($this->get());
        }

        return $toArray ? $this->data->toArray() : $this->data;
    }

    /**
     * @param Collection|\Closure|array $data
     */
    public function setData($data)
    {
        if ($this->collectionCallback) {
            foreach ($this->collectionCallback as $cb) {
                $data = call_user_func($cb, $data);
            }
        }

        if (
            ($isA = is_array($data))
            || $data instanceof Collection
            || $data instanceof \Closure
            || ($isP = $data instanceof AbstractPaginator)
        ) {
            if ($isA) {
                $data = collect($data);
            } elseif (! empty($isP)) {
                $this->model = $data;
                $this->data = $data->getCollection();

                return;
            }

            $this->data = $data;
        }
    }

    /**
     * Add conditions to grid model.
     *
     * @param array $conditions
     *
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
     * @throws \Exception
     *
     * @return Collection|LengthAwarePaginator
     */
    public function get()
    {
        if (
            $this->model instanceof LengthAwarePaginator
            || $this->model instanceof Collection
        ) {
            return $this->model;
        }

        $this->setSort();
        $this->setPaginate();

        if ($this->data instanceof \Closure) {
            $data = $this->data;
            $this->data = collect();

            return $this->model = $data($this);
        }

        $this->model = $this->repository->get($this);

        if (is_array($this->model)) {
            $this->model = collect($this->model);
        }

        if ($this->model instanceof Collection) {
            return $this->model;
        }

        if ($this->model instanceof LengthAwarePaginator) {
            $this->model->setPageName($this->pageName);

            $this->handleInvalidPage($this->model);

            return $this->model->getCollection();
        }

        throw new \Exception('Grid query error');
    }

    /**
     * If current page is greater than last page, then redirect to last page.
     *
     * @param LengthAwarePaginator $paginator
     *
     * @return void
     */
    protected function handleInvalidPage(LengthAwarePaginator $paginator)
    {
        if (
            $this->usePaginate
            && $paginator->lastPage()
            && $paginator->currentPage() > $paginator->lastPage()
        ) {
            $lastPageUrl = $this->request->fullUrlWithQuery([
                $paginator->getPageName() => $paginator->lastPage(),
            ]);

            Pjax::respond(redirect($lastPageUrl));
        }
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

        return $this->currentPage ?: ($this->currentPage = ($this->request->get($this->pageName) ?: 1));
    }

    /**
     * @param int $currentPage
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

        return $this->request->get($this->perPageName) ?: $this->perPage;
    }

    /**
     * Set the grid paginate.
     *
     * @return void
     */
    protected function setPaginate()
    {
        $paginate = $this->findQueryByMethod('paginate');

        $this->queries = $this->queries->reject(function ($query) {
            return $query['method'] == 'paginate';
        });

        if (! $this->usePaginate) {
            $query = [
                'method'    => 'get',
                'arguments' => [],
            ];
        } else {
            $query = [
                'method'    => 'paginate',
                'arguments' => $this->resolvePerPage($paginate),
            ];
        }

        $this->queries->push($query);
    }

    /**
     * Resolve perPage for pagination.
     *
     * @param array|null $paginate
     *
     * @return array
     */
    protected function resolvePerPage($paginate)
    {
        if ($perPage = app('request')->input($this->perPageName)) {
            if (is_array($paginate)) {
                $paginate['arguments'][0] = (int) $perPage;

                return $paginate['arguments'];
            }

            $this->perPage = (int) $perPage;
        }

        return [$this->perPage, '*', $this->pageName, $this->getCurrentPage()];
    }

    /**
     * Find query by method name.
     *
     * @param $method
     *
     * @return static
     */
    public function findQueryByMethod($method)
    {
        return $this->queries->first(function ($query) use ($method) {
            return $query['method'] == $method;
        });
    }

    /**
     * Get the grid sort.
     *
     * @return array exp: ['name', 'desc']
     */
    public function getSort()
    {
        if (empty($this->sort['column']) || empty($this->sort['type'])) {
            return [null, null];
        }

        return [$this->sort['column'], $this->sort['type']];
    }

    /**
     * Set the grid sort.
     *
     * @return void
     */
    protected function setSort()
    {
        $this->sort = $this->request->get($this->sortName, []);

        if (empty($this->sort['column']) || empty($this->sort['type'])) {
            return;
        }

        if (Str::contains($this->sort['column'], '.')) {
            $this->setRelationSort($this->sort['column']);
        } else {
            $this->resetOrderBy();

            $this->queries->push([
                'method'    => 'orderBy',
                'arguments' => [$this->sort['column'], $this->sort['type']],
            ]);
        }
    }

    /**
     * Set relation sort.
     *
     * @param string $column
     *
     * @return void
     */
    protected function setRelationSort($column)
    {
        [$relationName, $relationColumn] = explode('.', $column);

        if ($this->queries->contains(function ($query) use ($relationName) {
            return $query['method'] == 'with' && in_array($relationName, $query['arguments']);
        })) {
            $this->queries->push([
                'method'    => 'select',
                'arguments' => ['*'],
            ]);

            $this->resetOrderBy();

            $this->queries->push([
                'method'    => 'orderBy',
                'arguments' => [
                    $relationColumn,
                    $this->sort['type'],
                ],
            ]);
        }
    }

    /**
     * @param string|array $method
     *
     * @return void
     */
    public function rejectQueries($method)
    {
        $method = (array) $method;

        $this->queries = $this->queries->reject(function ($query) use ($method) {
            return in_array($query['method'], $method);
        });
    }

    /**
     * Reset orderBy query.
     *
     * @return void
     */
    public function resetOrderBy()
    {
        $this->rejectQueries(['orderBy', 'orderByDesc']);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->queries->push([
            'method'    => $method,
            'arguments' => $arguments,
        ]);

        return $this;
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param mixed $relations
     *
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

        return $this->__call('with', (array) $relations);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $data = $this->buildData();

        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->data = null;
        $this->model = null;
    }
}
