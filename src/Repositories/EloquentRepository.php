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
     * Init model.
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
     * Get columns of the grid.
     *
     * @return array
     */
    public function getGridColumns()
    {
        return ['*'];
    }

    /**
     * Get columns of the form.
     *
     * @return array
     */
    public function getFormColumns()
    {
        return ['*'];
    }

    /**
     * Get columns of the detail view.
     *
     * @return array
     */
    public function getDetailColumns()
    {
        return ['*'];
    }

    /**
     * Set the relationships that should be eager loaded.
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
     * Get the grid data.
     *
     * @param Grid\Model $model
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection|array
     */
    public function get(Grid\Model $model)
    {
        $query = $this->newQuery();

        if ($this->relations) {
            $query->with($this->relations);
        }

        $model->getQueries()->unique()->each(function ($value) use (&$query) {
            if ($value['method'] == 'paginate') {
                $value['arguments'][1] = $this->getGridColumns();
            } elseif ($value['method'] == 'get') {
                $value['arguments'] = $this->getGridColumns();
            }

            $query = call_user_func_array([$query, $value['method']], $value['arguments'] ?? []);
        });

        return $query;
    }

    /**
     * Get data to build edit form.
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
            ->with($this->getRelations($form))
            ->findOrFail($form->key(), $this->getFormColumns());

        return $this->model->toArray();
    }

    /**
     * Get detail data.
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
            ->with($this->getRelations($show))
            ->findOrFail($show->key(), $this->getDetailColumns());

        return $this->model->toArray();
    }

    /**
     * Store a new record.
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
     * Get data before update.
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
     * Update form data.
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

            $model->setAttribute($model->getKeyName(), $form->key());
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
     * Swaps the order of this model with the model 'above' this model.
     *
     * @return bool
     */
    public function moveOrderUp()
    {
        $model = $this->eloquent();
        if ($model instanceof Sortable) {
            $model->moveOrderUp();

            return true;
        }

        return false;
    }

    /**
     * Swaps the order of this model with the model 'below' this model.
     *
     * @return bool
     */
    public function moveOrderDown()
    {
        $model = $this->eloquent();

        if ($model instanceof Sortable) {
            $model->moveOrderDown();

            return true;
        }

        return false;
    }

    /**
     * Destroy data.
     *
     * @param Form $form
     *
     * @return bool
     */
    public function destroy(Form $form, array $deletingData)
    {
        $id = $form->key();

        $deletingData = collect($deletingData)->keyBy($this->getKeyName());

        collect(explode(',', $id))->filter()->each(function ($id) use ($form, $deletingData) {
            $data = $deletingData->get($id, []);

            if (! $data) {
                return;
            }

            $model = $this->createEloquent($data);
            $model->exists = true;

            if ($this->isSoftDeletes && $model->trashed()) {
                $form->deleteFiles($data, true);
                $model->forceDelete();

                return;
            }

            $form->deleteFiles($data);
            $model->delete();
        });

        return true;
    }

    /**
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

        $id = $form->key();

        return $query
            ->with($this->getRelations($form))
            ->findOrFail(
                collect(explode(',', $id))->filter()->toArray(),
                $this->getFormColumns()
            )
            ->toArray();
    }

    /**
     * @return string
     */
    public function getParentColumn()
    {
        return $this->eloquent()->getParentColumn();
    }

    /**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        return $this->eloquent()->getTitleColumn();
    }

    /**
     * Get order column name.
     *
     * @return string
     */
    public function getOrderColumn()
    {
        return $this->eloquent()->getOrderColumn();
    }

    /**
     * Save tree order from a tree like array.
     *
     * @param array $tree
     * @param int   $parentId
     */
    public function saveOrder($tree = [], $parentId = 0)
    {
        $this->eloquent()->saveOrder($tree, $parentId);
    }

    /**
     * Set query callback to model.
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
     * Format data to tree like array.
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
     * Get the eloquent model.
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
     * Get all relations of model from callable.
     *
     * @return array
     */
    protected function getRelations($builder)
    {
        $relations = $columns = [];

        if ($builder instanceof Form) {
            /** @var Form\Field $field */
            foreach ($builder->builder()->fields() as $field) {
                $columns[] = $field->column();
            }
        } elseif ($builder instanceof Show) {
            /** @var Show\Field $field */
            foreach ($builder->fields() as $field) {
                $columns[] = $field->getName();
            }
        }

        $model = $this->eloquent();

        foreach (Arr::flatten($columns) as $column) {
            if (Str::contains($column, '.')) {
                [$relation] = explode('.', $column);

                if (method_exists($model, $relation) &&
                    $model->$relation() instanceof Relations\Relation
                ) {
                    $relations[] = $relation;
                }
            } elseif (method_exists($model, $column) &&
                ! method_exists(EloquentModel::class, $column)
            ) {
                $relations[] = $column;
            }
        }

        return array_unique(array_merge($relations, $this->relations));
    }

    /**
     * Get inputs for relations.
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
     * Update relation data.
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
                    if (! $model->{$relation->getForeignKey()}) {
                        $model->{$relation->getForeignKey()} = $parent->getKey();

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
