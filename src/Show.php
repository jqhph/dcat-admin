<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Show\AbstractTool;
use Dcat\Admin\Show\Divider;
use Dcat\Admin\Show\Field;
use Dcat\Admin\Show\Html;
use Dcat\Admin\Show\Newline;
use Dcat\Admin\Show\Panel;
use Dcat\Admin\Show\Relation;
use Dcat\Admin\Show\Row;
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
    use HasBuilderEvents;
    use Macroable {
            __call as macroCall;
        }

    /**
     * @var string
     */
    protected $view = 'admin::show.container';

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var mixed
     */
    protected $_id;

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
     * @var \Illuminate\Support\Collection
     */
    protected $rows;

    /**
     * Show constructor.
     *
     * @param  mixed  $id  $id
     * @param  Model|Builder|Repository|array|Arrayable  $model
     * @param  \Closure  $builder
     */
    public function __construct($id = null, $model = null, ?\Closure $builder = null)
    {
        switch (func_num_args()) {
            case 1:
            case 2:
                if (is_scalar($id)) {
                    $this->setKey($id);
                } else {
                    $builder = $model;
                    $model = $id;
                }
                break;
            default:
                $this->setKey($id);
        }
        $this->rows = new Collection();
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
                $this->setKey($key);
                $this->setKeyName($model->getKeyName());

                $this->model($model);
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

        if (! $this->model && $this->repository) {
            $this->model($this->repository->detail($this));
        }
    }

    /**
     * Create a show instance.
     *
     * @param  mixed  ...$params
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * @param  string  $value
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
     * @param  mixed  $id
     * @return mixed
     */
    public function setKey($id)
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->_id;
    }

    /**
     * @param  Fluent|\Illuminate\Database\Eloquent\Model|null  $model
     * @return Fluent|$this|\Illuminate\Database\Eloquent\Model
     */
    public function model($model = null)
    {
        if ($model === null) {
            return $this->model;
        }

        if (is_array($model)) {
            $model = new Fluent($model);
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Set a view to render.
     *
     * @param  string  $view
     * @return $this
     */
    public function view($view)
    {
        $this->panel->view($view);

        return $this;
    }

    /**
     * Add variables to show view.
     *
     * @param  array  $variables
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
     * @param  \Closure|array|AbstractTool|Renderable|Htmlable|string  $callback
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
     * @param  string  $name
     * @param  string  $label
     * @return Field
     */
    public function field($name, $label = '')
    {
        return $this->addField($name, $label);
    }

    /**
     * Get fields or add multiple fields.
     *
     * @param  array  $fields
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
     * @param  string  $name
     * @param  string|\Closure  $label
     * @param  null|\Closure  $builder
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
     * @param  string  $name
     * @param  string  $label
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
     * @param  string  $name
     * @param  \Closure  $builder
     * @param  string  $label
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
     * @param  string  $name
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
     * @param  string  $name
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
    public function repository()
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
     * Show the content of html.
     *
     * @param  string  $html
     */
    public function html($html = '')
    {
        $this->fields->push((new Html($html))->setParent($this));
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
     * @param  null|string  $width
     * @param  null|string  $height
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
     * @return string
     */
    public function resource()
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
     * Set resource path.
     *
     * @param  string  $path
     * @return $this
     */
    public function setResource($path)
    {
        if ($path) {
            $this->resource = admin_url($path);
        }

        return $this;
    }

    /**
     * Add field and relation dynamically.
     *
     * @param  string  $method
     * @param  array  $arguments
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
     * @param  array  $arguments
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
     * @param  string  $method
     * @param  array  $arguments
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
    }

    /**
     * Add a row in Show.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function row(Closure $callback)
    {
        $this->rows->push(new Row($callback, $this));

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * Add a model field to show.
     *
     * @param  string  $name
     * @return Field|Collection
     */
    public function __get($name)
    {
        return $this->call($name);
    }
}
