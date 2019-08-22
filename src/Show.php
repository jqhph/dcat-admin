<?php

namespace Dcat\Admin;

use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Show\Divider;
use Dcat\Admin\Show\Field;
use Dcat\Admin\Show\Newline;
use Dcat\Admin\Show\Panel;
use Dcat\Admin\Show\Relation;
use Dcat\Admin\Traits\BuilderEvents;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Fluent;
use Illuminate\Support\Traits\Macroable;

class Show implements Renderable
{
    use BuilderEvents,
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
    protected $_id;

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
     * @param Model $model
     * @param mixed $builder
     */
    public function __construct(Repository $repository, $builder = null)
    {
        $this->repository = Admin::createRepository($repository);
        $this->builder = $builder;

        $this->initPanel();
        $this->initContents();

        $this->callResolving();
    }

    /**
     * Create a show instance.
     *
     * @param mixed ...$params
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->repository->getKeyName();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param $model
     */
    public function setModel(Fluent $model)
    {
        $this->model = $model;
    }

    /**
     * @return Fluent
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->setModel(new Fluent($this->repository->detail($this)));
        }

        return $this->model;
    }

    /**
     * Set a view to render.
     *
     * @param string $view
     * @param array  $variables
     * @return $this
     */
    public function setView($view, $variables = [])
    {
        if (!empty($variables)) {
            $this->with($variables);
        }

        $this->panel->setView($view);

        return $this;
    }

    /**
     * Add variables to show view.
     *
     * @param array $variables
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
     * Add multiple fields.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields = [])
    {
        if (!Arr::isAssoc($fields)) {
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
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return Collection
     */
    public function getRelations()
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
        $fields = array_keys($this->getModel()->toArray());

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
        $this->panel->getTools()->disableList($disable);

        return $this;
    }

    /**
     * Disable `delete` tool.
     *
     * @return $this
     */
    public function disableDeleteButton(bool $disable = true)
    {
        $this->panel->getTools()->disableDelete($disable);

        return $this;
    }

    /**
     * Disable `edit` tool.
     *
     * @return $this
     */
    public function disableEditButton(bool $disable = true)
    {
        $this->panel->getTools()->disableEdit($disable);

        return $this;
    }

    /**
     * Show quick edit tool.
     *
     * @param null|string $width
     * @param null|string $height
     * @return $this
     */
    public function showQuickEdit(?string $width = null, ?string $height = null)
    {
        $this->panel->getTools()->showQuickEdit($width, $height);

        return $this;
    }

    /**
     * Disable quick edit tool.
     *
     * @return $this
     */
    public function disableQuickEdit()
    {
        $this->panel->getTools()->disableQuickEdit();

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
            $model = $this->getModel();

            if (is_callable($this->builder)) {
                call_user_func($this->builder, $this);
            }

            if ($this->fields->isEmpty()) {
                $this->all();
            }

            if (is_array($this->builder)) {
                $this->fields($this->builder);
            }

            $this->fields->each->setValue($model);
            $this->relations->each->setModel($model);

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
     * @return Field|Collection
     */
    public function __get($name)
    {
        return $this->call($name);
    }
}
