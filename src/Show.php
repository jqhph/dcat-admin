<?php

namespace Dcat\Admin;

use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Show\AbstractTool;
use Dcat\Admin\Show\Divider;
use Dcat\Admin\Show\Field;
use Dcat\Admin\Show\Newline;
use Dcat\Admin\Show\Panel;
use Dcat\Admin\Show\Relation;
use Dcat\Admin\Show\Tools;
use Dcat\Admin\Traits\HasBuilderEvents;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Traits\Macroable;

class Show implements Renderable
{
    use HasBuilderEvents,
        Macroable {
            __call as macroCall;
        }

    /**
     * @var string
     */
    protected $view = 'admin::show';

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var mixed
     */
    protected $__id;

    /**
     * @var string
     */
    protected $keyName = 'id';

    /**
     * @var Fluent
     */
    protected $model;

    /**
     * Show panel builder.
     *
     * @var callable
     */
    protected $builder;

    /**
     * Resource path for this show page.
     *
     * @var string
     */
    protected $resource;

    /**
     * Fields to be show.
     *
     * @var Collection
     */
    protected $fields;

    /**
     * Relations to be show.
     *
     * @var Collection
     */
    protected $relations;

    /**
     * @var Panel
     */
    protected $panel;

    /**
     * Show constructor.
     *
     * @param mixed $id                                $id
     * @param Model|Builder|Repository|array|Arrayable $model
     * @param \Closure                                 $builder
     */
    public function __construct($id = null, $model = null, ?\Closure $builder = null)
    {
        switch (func_num_args()) {
            case 1:
            case 2:
                if (is_scalar($id)) {
                    $this->key($id);
                } else {
                    $builder = $model;
                    $model = $id;
                }
                break;
            default:
                $this->key($id);
        }

        $this->builder = $builder;

        $this->initModel($model);
        $this->initPanel();
        $this->initContents();

        $this->callResolving();
    }

    protected function initModel($model)
    {
        if ($model instanceof Repository || $model instanceof Builder) {
            $this->repository = Admin::repository($model);
        } elseif ($model instanceof Model) {
            if ($key = $model->getKey()) {
                $this->key = $model->getKey();
                $this->keyName = $model->getKeyName();

                $this->model(new Fluent($model->toArray()));
            } else {
                $this->repository = Admin::repository($model);
            }
        } elseif ($model instanceof Arrayable) {
            $this->model(new Fluent($model->toArray()));
        } elseif (is_array($model)) {
            $this->model(new Fluent($model));
        } else {
            $this->model(new Fluent());
        }
    }

    /**
     * Create a show instance.
     *
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setKeyName(string $value)
    {
        $this->keyName = $value;

        return $this;
    }

    /**
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName()
    {
        if (! $this->repository) {
            return $this->keyName;
        }

        return $this->keyName ?: $this->repository->getKeyName();
    }

    /**
     * @param mixed $id
     *
     * @return mixed
     */
    public function key($id = null)
    {
        if ($id === null) {
            return $this->__id;
        }

        $this->__id = $id;

        return $this;
    }

    /**
     * @param Fluent|null $model
     *
     * @return Fluent|$this
     */
    public function model(Fluent $model = null)
    {
        if ($model === null) {
            if (! $this->model) {
                $this->setupModel();
            }

            return $this->model;
        }

        $this->model = $model;

        return $this;
    }

    protected function setupModel()
    {
        if ($this->repository) {
            $this->model(new Fluent($this->repository->detail($this)));
        } else {
            $this->model(new Fluent());
        }
    }

    /**
     * Set a view to render.
     *
     * @param string $view
     * @param array  $variables
     *
     * @return $this
     */
    public function view($view, $variables = [])
    {
        if (! empty($variables)) {
            $this->with($variables);
        }

        $this->panel->view($view);

        return $this;
    }

    /**
     * Add variables to show view.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function with($variables = [])
    {
        $this->panel->with($variables);

        return $this;
    }

    /**
     * @return $this
     */
    public function wrap(\Closure $wrapper)
    {
        $this->panel->wrap($wrapper);

        return $this;
    }

    /**
     * Initialize the contents to show.
     */
    protected function initContents()
    {
        $this->fields = new Collection();
        $this->relations = new Collection();
    }

    /**
     * Initialize panel.
     */
    protected function initPanel()
    {
        $this->panel = new Panel($this);
    }

    /**
     * Get panel instance.
     *
     * @return Panel
     */
    public function panel()
    {
        return $this->panel;
    }

    /**
     * @param \Closure|array|AbstractTool|Renderable|Htmlable|string $callback
     *
     * @return $this|Tools
     */
    public function tools($callback = null)
    {
        if ($callback === null) {
            return $this->panel->tools();
        }

        if ($callback instanceof \Closure) {
            $callback->call($this->model, $this->panel->tools());

            return $this;
        }

        if (! is_array($callback)) {
            $callback = [$callback];
        }

        foreach ($callback as $tool) {
            $this->panel->tools()->append($tool);
        }

        return $this;
    }

    /**
     * Add a model field to show.
     *
     * @param string $name
     * @param string $label
     *
     * @return Field
     */
    public function field($name, $label = '')
    {
        return $this->addField($name, $label);
    }

    /**
     * Get fields or add multiple fields.
     *
     * @param array $fields
     *
     * @return $this|Collection
     */
    public function fields(array $fields = null)
    {
        if ($fields === null) {
            return $this->fields;
        }

        if (! Arr::isAssoc($fields)) {
            $fields = array_combine($fields, $fields);
        }

        foreach ($fields as $field => $label) {
            $this->field($field, $label);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function relations()
    {
        return $this->relations;
    }

    /**
     * Show all fields.
     *
     * @return Show
     */
    public function all()
    {
        $fields = array_keys($this->model()->toArray());

        return $this->fields($fields);
    }

    /**
     * Add a relation to show.
     *
     * @param string          $name
     * @param string|\Closure $label
     * @param null|\Closure   $builder
     *
     * @return Relation
     */
    public function relation($name, $label, $builder = null)
    {
        if (is_null($builder)) {
            $builder = $label;
            $label = '';
        }

        return $this->addRelation($name, $builder, $label);
    }

    /**
     * Add a model field to show.
     *
     * @param string $name
     * @param string $label
     *
     * @return Field
     */
    protected function addField($name, $label = '')
    {
        $field = new Field($name, $label);

        $field->setParent($this);

        $this->overwriteExistingField($name);

        $this->fields->push($field);

        return $field;
    }

    /**
     * Add a relation panel to show.
     *
     * @param string   $name
     * @param \Closure $builder
     * @param string   $label
     *
     * @return Relation
     */
    protected function addRelation($name, $builder, $label = '')
    {
        $relation = new Relation($name, $builder, $label);

        $relation->setParent($this);

        $this->overwriteExistingRelation($name);

        $this->relations->push($relation);

        return $relation;
    }

    /**
     * Overwrite existing field.
     *
     * @param string $name
     */
    protected function overwriteExistingField($name)
    {
        if ($this->fields->isEmpty()) {
            return;
        }

        $this->fields = $this->fields->filter(
            function (Field $field) use ($name) {
                return $field->getName() != $name;
            }
        );
    }

    /**
     * Overwrite existing relation.
     *
     * @param string $name
     */
    protected function overwriteExistingRelation($name)
    {
        if ($this->relations->isEmpty()) {
            return;
        }

        $this->relations = $this->relations->filter(
            function (Relation $relation) use ($name) {
                return $relation->getName() != $name;
            }
        );
    }

    /**
     * @return Repository
     */
    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * Show a divider.
     */
    public function divider()
    {
        $this->fields->push(new Divider());
    }

    /**
     * Show a divider.
     */
    public function newline()
    {
        $this->fields->push(new Newline());
    }

    /**
     * Disable `list` tool.
     *
     * @return $this
     */
    public function disableListButton(bool $disable = true)
    {
        $this->panel->tools()->disableList($disable);

        return $this;
    }

    /**
     * Disable `delete` tool.
     *
     * @return $this
     */
    public function disableDeleteButton(bool $disable = true)
    {
        $this->panel->tools()->disableDelete($disable);

        return $this;
    }

    /**
     * Disable `edit` tool.
     *
     * @return $this
     */
    public function disableEditButton(bool $disable = true)
    {
        $this->panel->tools()->disableEdit($disable);

        return $this;
    }

    /**
     * Show quick edit tool.
     *
     * @param null|string $width
     * @param null|string $height
     *
     * @return $this
     */
    public function showQuickEdit(?string $width = null, ?string $height = null)
    {
        $this->panel->tools()->showQuickEdit($width, $height);

        return $this;
    }

    /**
     * Disable quick edit tool.
     *
     * @return $this
     */
    public function disableQuickEdit()
    {
        $this->panel->tools()->disableQuickEdit();

        return $this;
    }

    /**
     * Set resource path.
     *
     * @param string $resource
     *
     * @return $this
     */
    public function resource(string $resource)
    {
        if ($resource) {
            $this->resource = admin_url($resource);
        }

        return $this;
    }

    /**
     * Get resource path.
     *
     * @return string
     */
    public function getResource()
    {
        if (empty($this->resource)) {
            $path = request()->path();

            $segments = explode('/', $path);
            array_pop($segments);

            $this->resource = url(implode('/', $segments));
        }

        return $this->resource;
    }

    /**
     * Add field and relation dynamically.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field
     */
    public function __call($method, $arguments = [])
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $arguments);
        }

        return $this->call($method, $arguments);
    }

    /**
     * @param $method
     * @param array $arguments
     *
     * @return bool|Show|Field|Relation
     */
    protected function call($method, $arguments = [])
    {
        $label = isset($arguments[0]) ? $arguments[0] : '';

        if ($field = $this->handleRelationField($method, $arguments)) {
            return $field;
        }

        return $this->addField($method, $label);
    }

    /**
     * Handle relation field.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return $this|bool|Relation|Field
     */
    protected function handleRelationField($method, $arguments)
    {
        if (count($arguments) == 1 && $arguments[0] instanceof \Closure) {
            return $this->addRelation($method, $arguments[0]);
        } elseif (count($arguments) == 2 && $arguments[1] instanceof \Closure) {
            return $this->addRelation($method, $arguments[1], $arguments[0]);
        }

        return false;
    }

    /**
     * Render the show panels.
     *
     * @return string
     */
    public function render()
    {
        try {
            $model = $this->model();

            if (is_callable($this->builder)) {
                call_user_func($this->builder, $this);
            }

            if ($this->fields->isEmpty()) {
                $this->all();
            }

            if (is_array($this->builder)) {
                $this->fields($this->builder);
            }

            $this->fields->each->fill($model);
            $this->relations->each->model($model);

            $this->callComposing();

            $data = [
                'panel'     => $this->panel->fill($this->fields),
                'relations' => $this->relations,
            ];

            return view($this->view, $data)->render();
        } catch (\Throwable $e) {
            return Admin::makeExceptionHandler()->renderException($e);
        }
    }

    /**
     * Add a model field to show.
     *
     * @param string $name
     *
     * @return Field|Collection
     */
    public function __get($name)
    {
        return $this->call($name);
    }
}
