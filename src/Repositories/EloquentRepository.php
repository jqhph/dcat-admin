<?php

namespace Dcat\Admin\Repositories;

use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations;
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
     * EloquentRepository constructor.
     *
     * @param EloquentModel|array|string $modelOrRelations $modelOrRelations
     */
    public function __construct($modelOrRelations = [])
    {
        $this->initModel($modelOrRelations);
    }

    /**
     * 初始化模型.
     *
     * @param EloquentModel|Builder|array|string $modelOrRelations
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
            $this->with($modelOrRelations);
        }

        $this->setKeyName($this->eloquent()->getKeyName());

        $this->setIsSoftDeletes(
            in_array(SoftDeletes::class, class_uses($this->eloquent()))
        );
    }

    /**
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return $this->eloquent()->getCreatedAtColumn();
    }

    /**
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return $this->eloquent()->getUpdatedAtColumn();
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
     * @param mixed $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->relations = (array) $relations;

        return $this;
    }

    /**
     * 查询Grid表格数据.
     *
     * @param Grid\Model $model
     *
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

        $model->getQueries()->unique()->each(function ($value) use (&$query) {
            if ($value['method'] == 'paginate') {
                $value['arguments'][1] = $this->getGridColumns();
            } elseif ($value['method'] == 'get') {
                $value['arguments'] = [$this->getGridColumns()];
            }

            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ?? []);
        });

        return $query;
    }

    /**
     * 设置表格数据排序.
     *
     * @param Grid\Model $model
     *
     * @return void
     */
    protected function setSort(Grid\Model $model)
    {
        [$column, $type] = $model->getSort();

        if (empty($column) || empty($type)) {
            return;
        }

        if (Str::contains($column, '.')) {
            $this->setRelationSort($model, $column, $type);
        } else {
            $model->resetOrderBy();

            $model->addQuery('orderBy', [$column, $type]);
        }
    }

    /**
     * 设置关联数据排序.
     *
     * @param Grid\Model $model
     * @param string     $column
     * @param string     $type
     *
     * @return void
     */
    protected function setRelationSort(Grid\Model $model, $column, $type)
    {
        [$relationName, $relationColumn] = explode('.', $column);

        if ($model->getQueries()->contains(function ($query) use ($relationName) {
            return $query['method'] == 'with' && in_array($relationName, $query['arguments']);
        })) {
            $model->addQuery('select', [$this->getGridColumns()]);

            $model->resetOrderBy();

            $model->addQuery('orderBy', [
                $relationColumn,
                $type,
            ]);
        }
    }

    /**
     * 设置分页参数.
     *
     * @param Grid\Model $model
     *
     * @return void
     */
    protected function setPaginate(Grid\Model $model)
    {
        $paginate = $model->findQueryByMethod('paginate');

        $model->rejectQuery(['paginate']);

        if (! $model->allowPagination()) {
            $model->addQuery('get', [$this->getGridColumns()]);
        } else {
            $model->addQuery('paginate', $this->resolvePerPage($model, $paginate));
        }
    }

    /**
     * 获取分页参数.
     *
     * @param Grid\Model $model
     * @param array|null $paginate
     *
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
     * @param Form $form
     *
     * @return array
     */
    public function edit(Form $form): array
    {
        $query = $this->newQuery();

        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }

        $this->model = $query
            ->with($this->getRelations())
            ->findOrFail($form->getKey(), $this->getFormColumns());

        return $this->model->toArray();
    }

    /**
     * 查询详情页面数据.
     *
     * @param Show $show
     *
     * @return array
     */
    public function detail(Show $show): array
    {
        $query = $this->newQuery();

        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }

        $this->model = $query
            ->with($this->getRelations())
            ->findOrFail($show->getKey(), $this->getDetailColumns());

        return $this->model->toArray();
    }

    /**
     * 新增记录.
     *
     * @param Form $form
     *
     * @return mixed
     */
    public function store(Form $form)
    {
        $result = null;

        DB::transaction(function () use ($form, &$result) {
            $model = $this->eloquent();

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

        return $this->eloquent()->getKey();
    }

    /**
     * 查询更新前的行数据.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getDataWhenUpdating(Form $form): array
    {
        return $this->edit($form);
    }

    /**
     * 更新数据.
     *
     * @param Form $form
     *
     * @return bool
     */
    public function update(Form $form)
    {
        /* @var EloquentModel $builder */
        $model = $this->eloquent();

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
        $model = $this->eloquent();

        if (! $model instanceof Sortable) {
            throw new \RuntimeException(
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
        $model = $this->eloquent();

        if (! $model instanceof Sortable) {
            throw new \RuntimeException(
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
     * @param Form $form
     *
     * @return bool
     */
    public function destroy(Form $form, array $deletingData)
    {
        $id = $form->getKey();

        $deletingData = collect($deletingData)->keyBy($this->getKeyName());

        collect(explode(',', $id))->filter()->each(function ($id) use ($form, $deletingData) {
            $data = $deletingData->get($id, []);

            if (! $data) {
                return;
            }

            $model = $this->createDeletingModel($id, $data);

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
     * @param mixed $id
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createDeletingModel($id, $data)
    {
        $model = $this->createEloquent();
        $keyName = $model->getKeyName();

        $model->{$keyName} = $id;

        if ($this->isSoftDeletes) {
            $deletedColumn = $model->getDeletedAtColumn();

            $model->{$deletedColumn} = $data[$deletedColumn] ?? null;
        }

        $model->exists = true;

        return $model;
    }

    /**
     * 查询删除前的行数据.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getDataWhenDeleting(Form $form): array
    {
        $query = $this->newQuery();

        if ($this->isSoftDeletes) {
            $query->withTrashed();
        }

        $id = $form->getKey();

        return $query
            ->with($this->getRelations())
            ->findOrFail(
                collect(explode(',', $id))->filter()->toArray(),
                $this->getFormColumns()
            )
            ->toArray();
    }

    /**
     * 获取父级ID字段名称.
     *
     * @return string
     */
    public function getParentColumn()
    {
        $model = $this->eloquent();

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
        $model = $this->eloquent();

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
        $model = $this->eloquent();

        if (method_exists($model, 'getOrderColumn')) {
            return $model->getOrderColumn();
        }
    }

    /**
     * 保存层级数据排序.
     *
     * @param array $tree
     * @param int   $parentId
     */
    public function saveOrder($tree = [], $parentId = 0)
    {
        $this->eloquent()->saveOrder($tree, $parentId);
    }

    /**
     * 设置数据查询回调.
     *
     * @param \Closure|null $query
     *
     * @return $this
     */
    public function withQuery($queryCallback)
    {
        $this->eloquent()->withQuery($queryCallback);

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

        return $this->eloquent()->toTree();
    }

    /**
     * @return Builder
     */
    protected function newQuery()
    {
        if ($this->queryBuilder) {
            return clone $this->queryBuilder;
        }

        return $this->eloquent()->newQuery();
    }

    /**
     * 获取model对象.
     *
     * @return EloquentModel
     */
    public function eloquent()
    {
        return $this->model ?: ($this->model = $this->createEloquent());
    }

    /**
     * @param array $data
     *
     * @return EloquentModel
     */
    public function createEloquent(array $data = [])
    {
        $model = new $this->eloquentClass();

        if ($data) {
            $model->forceFill($data);
        }

        return $model;
    }

    /**
     * 获取模型的所有关联关系.
     *
     * @return array
     */
    protected function getRelations()
    {
        return $this->relations;
    }

    /**
     * 获取模型关联关系的表单数据.
     *
     * @param EloquentModel $model
     * @param array         $inputs
     *
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
     * @param Form          $form
     * @param EloquentModel $model
     * @param array         $relationsData
     * @param array         $relationKeyMap
     *
     * @throws \Exception
     */
    protected function updateRelation(Form $form, EloquentModel $model, array $relationsData, array $relationKeyMap)
    {
        foreach ($relationsData as $name => $values) {
            $relationName = $relationKeyMap[$name];

            if (! method_exists($model, $relationName)) {
                continue;
            }

            $relation = $model->$relationName();

            $oneToOneRelation = $relation instanceof Relations\HasOne
                || $relation instanceof Relations\MorphOne
                || $relation instanceof Relations\BelongsTo;

            $prepared = $form->prepareUpdate([$name => $values], $oneToOneRelation);

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

                    $related = $model->$name;

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

                    $parent = $model->$name;

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
                    $related = $model->$name;
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

                        if ($related[Form::REMOVE_FLAG_NAME] == 1) {
                            $instance->delete();

                            continue;
                        }

                        Arr::forget($related, Form::REMOVE_FLAG_NAME);

                        $instance->fill($related);

                        $instance->save();
                    }

                    break;
            }
        }
    }
}
