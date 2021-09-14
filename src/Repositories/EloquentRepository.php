<?php

namespace Dcat\Admin\Repositories;

use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Laravel\Database\SoftDeletes as DcatSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;

class EloquentRepository extends Repository implements TreeRepository
{
    /**
     * @var string
     */
    protected $eloquentClass;

    /**
     * @var EloquentModel
     */
    protected $model;

    /**
     * @var Builder
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $collection;

    /**
     * EloquentRepository constructor.
     *
     * @param  EloquentModel|array|string  $modelOrRelations  $modelOrRelations
     */
    public function __construct($modelOrRelations = [])
    {
        $this->initModel($modelOrRelations);
    }

    /**
     * 初始化模型.
     *
     * @param  EloquentModel|Builder|array|string  $modelOrRelations
     */
    protected function initModel($modelOrRelations)
    {
        if (is_string($modelOrRelations) && class_exists($modelOrRelations)) {
            $this->eloquentClass = $modelOrRelations;
        } elseif ($modelOrRelations instanceof EloquentModel) {
            $this->eloquentClass = get_class($modelOrRelations);
            $this->model = $modelOrRelations;
        } elseif ($modelOrRelations instanceof Builder) {
            $this->model = $modelOrRelations->getModel();
            $this->eloquentClass = get_class($this->model);
            $this->queryBuilder = $modelOrRelations;
        } else {
            $this->setRelations($modelOrRelations);
        }

        $this->setKeyName($this->model()->getKeyName());

        $traits = class_uses($this->model());

        $this->setIsSoftDeletes(
            in_array(SoftDeletes::class, $traits, true)
            || in_array(DcatSoftDeletes::class, $traits, true)
        );
    }

    /**
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return $this->model()->getCreatedAtColumn();
    }

    /**
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return $this->model()->getUpdatedAtColumn();
    }

    /**
     * 获取列表页面查询的字段.
     *
     * @return array
     */
    public function getGridColumns()
    {
        return ['*'];
    }

    /**
     * 获取表单页面查询的字段.
     *
     * @return array
     */
    public function getFormColumns()
    {
        return ['*'];
    }

    /**
     * 获取详情页面查询的字段.
     *
     * @return array
     */
    public function getDetailColumns()
    {
        return ['*'];
    }

    /**
     * 设置关联关系.
     *
     * @param  mixed  $relations
     * @return $this
     */
    public function setRelations($relations)
    {
        $this->relations = (array) $relations;

        return $this;
    }

    /**
     * 查询Grid表格数据.
     *
     * @param  Grid\Model  $model
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection|array
     */
    public function get(Grid\Model $model)
    {
        $this->setSort($model);
        $this->setPaginate($model);

        $query = $this->newQuery();

        if ($this->relations) {
            $query->with($this->relations);
        }

        return $model->apply($query, true, $this->getGridColumns());
    }

    /**
     * 设置表格数据排序.
     *
     * @param  Grid\Model  $model
     * @return void
     */
    protected function setSort(Grid\Model $model)
    {
        [$column, $type, $cast] = $model->getSort();

        if (empty($column) || empty($type)) {
            $orders = $model->getSortQueries();

            $model->resetOrderBy();

            $orders->each(function ($orderBy) use ($model) {
                $column = $orderBy['arguments'][0];
                $type = in_array($orderBy['method'], $model->getSortDescMethods(), true) ? 'desc' : ($orderBy['arguments'][1] ?? 'asc');
                $cast = null;

                $this->addOrderBy($model, $column, $type, $cast);
            });

            return;
        }

        $model->resetOrderBy();

        $this->addOrderBy($model, $column, $type, $cast);
    }

    /**
     * @param  Grid\Model  $model
     * @param  string  $column
     * @param  string  $type
     * @param  string  $cast
     *
     * @throws \Exception
     */
    protected function addOrderBy(Grid\Model $model, $column, $type, $cast)
    {
        $explodedCols = explode('.', $column);
        $isRelation = empty($explodedCols[1]) ? false : method_exists($this->model(), $explodedCols[0]);

        if (count($explodedCols) > 1 && $isRelation) {
            $this->setRelationSort($model, $column, $type, $cast);

            return;
        }

        $this->setOrderBy(
            $model,
            str_replace('.', '->', $column),
            $type,
            $cast);
    }

    /**
     * @param  Grid\Model  $model
     * @param $column
     * @param $type
     * @param $cast
     */
    protected function setOrderBy(Grid\Model $model, $column, $type, $cast)
    {
        $isJsonColumn = Str::contains($column, '->');

        if ($isJsonColumn) {
            $explodedCols = explode('->', $column);
            // json字段排序
            $col = $this->wrapMySqlColumn(array_shift($explodedCols));
            $parts = implode('.', $explodedCols);
            $column = "JSON_UNQUOTE(JSON_EXTRACT({$col}, '$.{$parts}'))";
        }

        if (! empty($cast)) {
            $column = $this->wrapMySqlColumn($column);

            $model->addQuery(
                'orderByRaw',
                ["CAST({$column} AS {$cast}) {$type}"]
            );

            return;
        }

        if ($isJsonColumn) {
            $model->addQuery('orderByRaw', ["{$column} {$type}"]);
        } else {
            $model->addQuery('orderBy', [$column, $type]);
        }
    }

    /**
     * @param  string  $column
     * @return string
     */
    protected function wrapMySqlColumn($column)
    {
        if (Str::contains($column, '`')) {
            return $column;
        }

        $columns = explode('.', $column);

        foreach ($columns as &$column) {
            if (! Str::contains($column, '`')) {
                $column = "`{$column}`";
            }
        }

        return implode('.', $columns);
    }

    /**
     * 设置关联数据排序.
     *
     * @param  Grid\Model  $model
     * @param  string  $column
     * @param  string  $type
     * @param  string  $cast
     *
     * @throws \Exception
     */
    protected function setRelationSort(Grid\Model $model, $column, $type, $cast)
    {
        [$relationName, $relationColumn] = explode('.', $column, 2);

        $relation = $this->model()->$relationName();

        $model->addQuery('select', [$this->model()->getTable().'.*']);

        $model->addQuery('join', $this->joinParameters($relation));

        $this->setOrderBy(
            $model,
            $relation->getRelated()->getTable().'.'.str_replace('.', '->', $relationColumn),
            $type,
            $cast
        );
    }

    /**
     * 关联模型 join 连接查询.
     *
     * @param  Relation  $relation
     * @return array
     *
     * @throws \Exception
     */
    protected function joinParameters(Relation $relation)
    {
        $relatedTable = $relation->getRelated()->getTable();

        if ($relation instanceof BelongsTo) {
            $foreignKeyMethod = version_compare(app()->version(), '5.8.0', '<') ? 'getForeignKey' : 'getForeignKeyName';

            return [
                $relatedTable,
                $relation->{$foreignKeyMethod}(),
                '=',
                $relatedTable.'.'.$relation->getRelated()->getKeyName(),
            ];
        }

        if ($relation instanceof HasOne) {
            return [
                $relatedTable,
                $relation->getQualifiedParentKeyName(),
                '=',
                $relation->getQualifiedForeignKeyName(),
            ];
        }

        throw new AdminException('Related sortable only support `HasOne` and `BelongsTo` relation.');
    }

    /**
     * 设置分页参数.
     *
     * @param  Grid\Model  $model
     * @return void
     */
    protected function setPaginate(Grid\Model $model)
    {
        $paginateMethod = $model->getPaginateMethod();

        $paginate = $model->findQueryByMethod($paginateMethod)->first();

        $model->rejectQuery(['paginate', 'simplePaginate']);

        if (! $model->allowPagination()) {
            $model->addQuery('get', [$this->getGridColumns()]);
        } else {
            $model->addQuery($paginateMethod, $this->resolvePerPage($model, $paginate));
        }
    }

    /**
     * 获取分页参数.
     *
     * @param  Grid\Model  $model
     * @param  array|null  $paginate
     * @return array
     */
    protected function resolvePerPage(Grid\Model $model, $paginate)
    {
        if ($paginate && is_array($paginate)) {
            if ($perPage = request()->input($model->getPerPageName())) {
                $paginate['arguments'][0] = (int) $perPage;
            }

            return $paginate['arguments'];
        }

        return [
            $model->getPerPage(),
            $this->getGridColumns(),
            $model->getPageName(),
            $model->getCurrentPage(),
        ];
    }

    /**
     * 查询编辑页面数据.
     *
     * @param  Form  $form
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function edit(Form $form)
    {
        $query = $this->newQuery();

        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }

        $this->model = $query
            ->with($this->getRelations())
            ->findOrFail($form->getKey(), $this->getFormColumns());

        return $this->model;
    }

    /**
     * 查询详情页面数据.
     *
     * @param  Show  $show
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function detail(Show $show)
    {
        $query = $this->newQuery();

        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }

        $this->model = $query
            ->with($this->getRelations())
            ->findOrFail($show->getKey(), $this->getDetailColumns());

        return $this->model;
    }

    /**
     * 新增记录.
     *
     * @param  Form  $form
     * @return mixed
     */
    public function store(Form $form)
    {
        $result = null;

        DB::transaction(function () use ($form, &$result) {
            $model = $this->model();

            $updates = $form->updates();

            [$relations, $relationKeyMap] = $this->getRelationInputs($model, $updates);

            if ($relations) {
                $updates = Arr::except($updates, array_keys($relationKeyMap));
            }

            foreach ($updates as $column => $value) {
                $model->setAttribute($column, $value);
            }

            $result = $model->save();

            $this->updateRelation($form, $model, $relations, $relationKeyMap);
        });

        return $this->model()->getKey();
    }

    /**
     * 查询更新前的行数据.
     *
     * @param  Form  $form
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function updating(Form $form)
    {
        return $this->edit($form);
    }

    /**
     * 更新数据.
     *
     * @param  Form  $form
     * @return bool
     */
    public function update(Form $form)
    {
        /* @var EloquentModel $builder */
        $model = $this->model();

        if (! $model->getKey()) {
            $model->exists = true;

            $model->setAttribute($model->getKeyName(), $form->getKey());
        }

        $result = null;

        DB::transaction(function () use ($form, $model, &$result) {
            $updates = $form->updates();

            [$relations, $relationKeyMap] = $this->getRelationInputs($model, $updates);

            if ($relations) {
                $updates = Arr::except($updates, array_keys($relationKeyMap));
            }

            foreach ($updates as $column => $value) {
                /* @var EloquentModel $model */
                $model->setAttribute($column, $value);
            }

            $result = $model->update();

            $this->updateRelation($form, $model, $relations, $relationKeyMap);
        });

        return $result;
    }

    /**
     * 数据行排序上移一个单位.
     *
     * @return bool
     */
    public function moveOrderUp()
    {
        $model = $this->model();

        if (! $model instanceof Sortable) {
            throw new RuntimeException(
                sprintf(
                    'The model "%s" must be a type of %s.',
                    get_class($model),
                    Sortable::class
                )
            );
        }

        return $model->moveOrderUp() ? true : false;
    }

    /**
     * 数据行排序下移一个单位.
     *
     * @return bool
     */
    public function moveOrderDown()
    {
        $model = $this->model();

        if (! $model instanceof Sortable) {
            throw new RuntimeException(
                sprintf(
                    'The model "%s" must be a type of %s.',
                    get_class($model),
                    Sortable::class
                )
            );
        }

        return $model->moveOrderDown() ? true : false;
    }

    /**
     * 删除数据.
     *
     * @param  Form  $form
     * @param  array  $originalData
     * @return bool
     */
    public function delete(Form $form, array $originalData)
    {
        $models = $this->collection->keyBy($this->getKeyName());

        collect(explode(',', $form->getKey()))->filter()->each(function ($id) use ($form, $models) {
            $model = $models->get($id);

            if (! $model) {
                return;
            }

            $data = $model->toArray();

            if ($this->isSoftDeletes && $model->trashed()) {
                $form->deleteFiles($data, true);
                $model->forceDelete();

                return;
            } elseif (! $this->isSoftDeletes) {
                $form->deleteFiles($data);
            }

            $model->delete();
        });

        return true;
    }

    /**
     * 查询删除前的行数据.
     *
     * @param  Form  $form
     * @return array
     */
    public function deleting(Form $form)
    {
        $query = $this->newQuery();

        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }

        $id = $form->getKey();

        $this->collection = $query
            ->with($this->getRelations())
            ->findOrFail(
                collect(explode(',', $id))->filter()->toArray(),
                $this->getFormColumns()
            );

        return $this->collection->toArray();
    }

    /**
     * 获取父级ID字段名称.
     *
     * @return string
     */
    public function getParentColumn()
    {
        $model = $this->model();

        if (method_exists($model, 'getParentColumn')) {
            return $model->getParentColumn();
        }
    }

    /**
     * 获取标题字段名称.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        $model = $this->model();

        if (method_exists($model, 'getTitleColumn')) {
            return $model->getTitleColumn();
        }
    }

    /**
     * 获取排序字段名称.
     *
     * @return string
     */
    public function getOrderColumn()
    {
        $model = $this->model();

        if (method_exists($model, 'getOrderColumn')) {
            return $model->getOrderColumn();
        }
    }

    /**
     * 保存层级数据排序.
     *
     * @param  array  $tree
     * @param  int  $parentId
     */
    public function saveOrder($tree = [], $parentId = 0)
    {
        $this->model()->saveOrder($tree, $parentId);
    }

    /**
     * 设置数据查询回调.
     *
     * @param  \Closure|null  $query
     * @return $this
     */
    public function withQuery($queryCallback)
    {
        $this->model()->withQuery($queryCallback);

        return $this;
    }

    /**
     * 获取层级数据.
     *
     * @return array
     */
    public function toTree()
    {
        if ($this->relations) {
            $this->withQuery(function ($model) {
                return $model->with($this->relations);
            });
        }

        return $this->model()->toTree();
    }

    /**
     * @return Builder
     */
    protected function newQuery()
    {
        if ($this->queryBuilder) {
            return clone $this->queryBuilder;
        }

        return $this->model()->newQuery();
    }

    /**
     * 获取model对象.
     *
     * @return EloquentModel
     */
    public function model()
    {
        return $this->model ?: ($this->model = $this->createModel());
    }

    /**
     * @param  array  $data
     * @return EloquentModel
     */
    public function createModel(array $data = [])
    {
        $model = new $this->eloquentClass();

        if ($data) {
            $model->setRawAttributes($data);
        }

        return $model;
    }

    /**
     * @param  array  $relations
     * @return $this
     */
    public static function with($relations = [])
    {
        return (new static())->setRelations($relations);
    }

    /**
     * 获取模型的所有关联关系.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * 获取模型关联关系的表单数据.
     *
     * @param  EloquentModel  $model
     * @param  array  $inputs
     * @return array
     */
    protected function getRelationInputs($model, $inputs = [])
    {
        $map = [];
        $relations = [];

        foreach ($inputs as $column => $value) {
            $relationColumn = null;

            if (method_exists($model, $column)) {
                $relationColumn = $column;
            } elseif (method_exists($model, $camelColumn = Str::camel($column))) {
                $relationColumn = $camelColumn;
            }

            if (! $relationColumn) {
                continue;
            }

            $relation = call_user_func([$model, $relationColumn]);

            if ($relation instanceof Relations\Relation) {
                $relations[$column] = $value;

                $map[$column] = $relationColumn;
            }
        }

        return [&$relations, $map];
    }

    /**
     * 更新关联关系数据.
     *
     * @param  Form  $form
     * @param  EloquentModel  $model
     * @param  array  $relationsData
     * @param  array  $relationKeyMap
     *
     * @throws \Exception
     */
    protected function updateRelation(Form $form, EloquentModel $model, array $relationsData, array $relationKeyMap)
    {
        foreach ($relationsData as $name => $values) {
            $relationName = $relationKeyMap[$name] ?? $name;

            if (! method_exists($model, $relationName)) {
                continue;
            }

            $relation = $model->$relationName();

            $oneToOneRelation = $relation instanceof Relations\HasOne
                || $relation instanceof Relations\MorphOne
                || $relation instanceof Relations\BelongsTo;

            $prepared = $oneToOneRelation ? $form->prepareUpdate([$name => $values]) : [$name => $values];

            if (empty($prepared)) {
                continue;
            }

            switch (true) {
                case $relation instanceof Relations\BelongsToMany:
                case $relation instanceof Relations\MorphToMany:
                    if (isset($prepared[$name])) {
                        $relation->sync($prepared[$name]);
                    }
                    break;
                case $relation instanceof Relations\HasOne:

                    $related = $model->$relationName;

                    // if related is empty
                    if (is_null($related)) {
                        $related = $relation->getRelated();
                        $qualifiedParentKeyName = $relation->getQualifiedParentKeyName();
                        $localKey = Arr::last(explode('.', $qualifiedParentKeyName));
                        $related->{$relation->getForeignKeyName()} = $model->{$localKey};
                    }

                    foreach ($prepared[$name] as $column => $value) {
                        $related->setAttribute($column, $value);
                    }

                    $related->save();
                    break;
                case $relation instanceof Relations\BelongsTo:
                case $relation instanceof Relations\MorphTo:

                    $parent = $model->$relationName;

                    // if related is empty
                    if (is_null($parent)) {
                        $parent = $relation->getRelated();
                    }

                    foreach ($prepared[$name] as $column => $value) {
                        $parent->setAttribute($column, $value);
                    }

                    $parent->save();

                    // When in creating, associate two models
                    $foreignKeyMethod = version_compare(app()->version(), '5.8.0', '<') ? 'getForeignKey' : 'getForeignKeyName';
                    if (! $model->{$relation->{$foreignKeyMethod}()}) {
                        $model->{$relation->{$foreignKeyMethod}()} = $parent->getKey();

                        $model->save();
                    }

                    break;
                case $relation instanceof Relations\MorphOne:
                    $related = $model->$relationName;
                    if (is_null($related)) {
                        $related = $relation->make();
                    }
                    foreach ($prepared[$name] as $column => $value) {
                        $related->setAttribute($column, $value);
                    }
                    $related->save();
                    break;
                case $relation instanceof Relations\HasMany:
                case $relation instanceof Relations\MorphMany:

                    foreach ($prepared[$name] as $related) {
                        /** @var Relations\Relation $relation */
                        $relation = $model->$relationName();

                        $keyName = $relation->getRelated()->getKeyName();

                        $instance = $relation->findOrNew(Arr::get($related, $keyName));

                        if (Arr::get($related, Form::REMOVE_FLAG_NAME) == 1) {
                            $instance->delete();

                            continue;
                        }

                        Arr::forget($related, Form::REMOVE_FLAG_NAME);

                        $key = Arr::get($related, $relation->getModel()->getKeyName());
                        if ($key === null || $key === '') {
                            Arr::forget($related, $relation->getModel()->getKeyName());
                        }

                        $instance->fill($related);

                        $instance->save();
                    }

                    break;
            }
        }
    }
}
