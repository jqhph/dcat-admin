<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Exception\Handler;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Row;
use Dcat\Admin\Form\Tab;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Traits\BuilderEvents;
use Dcat\Admin\Widgets\DialogForm;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Fluent;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Spatie\EloquentSortable\Sortable;
use Symfony\Component\HttpFoundation\Response;
use Dcat\Admin\Form\Concerns;

/**
 * Class Form.
 *
 * @method Field\Text           text($column, $label = '')
 * @method Field\Checkbox       checkbox($column, $label = '')
 * @method Field\Radio          radio($column, $label = '')
 * @method Field\Select         select($column, $label = '')
 * @method Field\MultipleSelect multipleSelect($column, $label = '')
 * @method Field\Textarea       textarea($column, $label = '')
 * @method Field\Hidden         hidden($column, $label = '')
 * @method Field\Id             id($column, $label = '')
 * @method Field\Ip             ip($column, $label = '')
 * @method Field\Url            url($column, $label = '')
 * @method Field\Color          color($column, $label = '')
 * @method Field\Email          email($column, $label = '')
 * @method Field\Mobile         mobile($column, $label = '')
 * @method Field\Slider         slider($column, $label = '')
 * @method Field\Map            map($latitude, $longitude, $label = '')
 * @method Field\Editor         editor($column, $label = '')
 * @method Field\Date           date($column, $label = '')
 * @method Field\Datetime       datetime($column, $label = '')
 * @method Field\Time           time($column, $label = '')
 * @method Field\Year           year($column, $label = '')
 * @method Field\Month          month($column, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  datetimeRange($start, $end, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Number         number($column, $label = '')
 * @method Field\Currency       currency($column, $label = '')
 * @method Field\SwitchField    switch($column, $label = '')
 * @method Field\Display        display($column, $label = '')
 * @method Field\Rate           rate($column, $label = '')
 * @method Field\Divide         divider()
 * @method Field\Password       password($column, $label = '')
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($html, $label = '')
 * @method Field\Tags           tags($column, $label = '')
 * @method Field\Icon           icon($column, $label = '')
 * @method Field\Embeds         embeds($column, $label = '')
 * @method Field\Captcha        captcha($column, $label = '')
 * @method Field\Listbox        listbox($column, $label = '')
 * @method Field\SelectResource selectResource($column, $label = '')
 * @method Field\File           file($column, $label = '')
 * @method Field\Image          image($column, $label = '')
 * @method Field\MultipleFile   multipleFile($column, $label = '')
 * @method Field\MultipleImage  multipleImage($column, $label = '')
 * @method Field\Tree           tree($column, $label = '')
 * @method Field\Table          table($column, $callback)
 *
 * @method Field\BootstrapFile          bootstrapFile($column, $label = '')
 * @method Field\BootstrapImage         bootstrapImage($column, $label = '')
 * @method Field\BootstrapMultipleImage bootstrapMultipleImage($column, $label = '')
 * @method Field\BootstrapMultipleFile  bootstrapMultipleFile($column, $label = '')
 */
class Form implements Renderable
{
    use BuilderEvents,
        Concerns\Events,
        Concerns\Files;

    /**
     * Remove flag in `has many` form.
     */
    const REMOVE_FLAG_NAME = '_remove_';

    /**
     * Available fields.
     *
     * @var array
     */
    protected static $availableFields = [
        'button'         => Field\Button::class,
        'checkbox'       => Field\Checkbox::class,
        'color'          => Field\Color::class,
        'currency'       => Field\Currency::class,
        'date'           => Field\Date::class,
        'dateRange'      => Field\DateRange::class,
        'datetime'       => Field\Datetime::class,
        'datetimeRange'  => Field\DatetimeRange::class,
        'decimal'        => Field\Decimal::class,
        'display'        => Field\Display::class,
        'divider'        => Field\Divide::class,
        'embeds'         => Field\Embeds::class,
        'editor'         => Field\Editor::class,
        'email'          => Field\Email::class,
        'hidden'         => Field\Hidden::class,
        'id'             => Field\Id::class,
        'ip'             => Field\Ip::class,
        'map'            => Field\Map::class,
        'mobile'         => Field\Mobile::class,
        'month'          => Field\Month::class,
        'multipleSelect' => Field\MultipleSelect::class,
        'number'         => Field\Number::class,
        'password'       => Field\Password::class,
        'radio'          => Field\Radio::class,
        'rate'           => Field\Rate::class,
        'select'         => Field\Select::class,
        'slider'         => Field\Slider::class,
        'switch'         => Field\SwitchField::class,
        'text'           => Field\Text::class,
        'textarea'       => Field\Textarea::class,
        'time'           => Field\Time::class,
        'timeRange'      => Field\TimeRange::class,
        'url'            => Field\Url::class,
        'year'           => Field\Year::class,
        'html'           => Field\Html::class,
        'tags'           => Field\Tags::class,
        'icon'           => Field\Icon::class,
        'captcha'        => Field\Captcha::class,
        'listbox'        => Field\Listbox::class,
        'selectResource' => Field\SelectResource::class,
        'file'           => Field\File::class,
        'image'          => Field\Image::class,
        'multipleFile'   => Field\MultipleFile::class,
        'multipleImage'  => Field\MultipleImage::class,
        'tree'           => Field\Tree::class,
        'table'          => Field\Table::class,

        'bootstrapFile'          => Field\BootstrapFile::class,
        'bootstrapImage'         => Field\BootstrapImage::class,
        'bootstrapMultipleFile'  => Field\BootstrapMultipleFile::class,
        'bootstrapMultipleImage' => Field\BootstrapMultipleImage::class,
    ];

    /**
     * Collected field assets.
     *
     * @var array
     */
    protected static $collectedAssets = [];

    /**
     * Form field alias.
     *
     * @var array
     */
    public static $fieldAlias = [];

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var bool
     */
    protected $useAjaxSubmit = true;

    /**
     * Model of the form.
     *
     * @var Fluent
     */
    protected $model;

    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Resource path for this form page.
     *
     * @var string
     */
    protected $resource;

    /**
     * Data for save to current model from input.
     *
     * @var array
     */
    protected $updates = [];

    /**
     * Input data.
     *
     * @var array
     */
    protected $inputs = [];

    /**
     * Ignored saving fields.
     *
     * @var array
     */
    protected $ignored = [];

    /**
     * @var Form\Tab
     */
    protected $tab = null;

    /**
     * Field rows in form.
     *
     * @var array
     */
    public $rows = [];

    /**
     * @var bool
     */
    protected $isSoftDeletes = false;

    /**
     * @var MessageBag
     */
    protected $validationMessages;

    /**
     * Create a new form instance.
     *
     * @param Repository $model
     * @param \Closure $callback
     */
    public function __construct(Repository $repository, Closure $callback = null)
    {
        $this->repository = Admin::createRepository($repository);

        $this->builder = new Builder($this);

        if ($callback instanceof Closure) {
            $callback($this);
        }

        $this->isSoftDeletes = $this->repository->isSoftDeletes();

        $this->setModel(new Fluent());

        $this->prepareDialogForm();

        $this->callResolving();
    }

    /**
     * Create a form instance.
     *
     * @param mixed ...$params
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field $field)
    {
        $field->setForm($this);

        $this->builder->fields()->push($field);

        $field::collectAssets();

        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function removeField($column)
    {
        $this->builder->removeField($column);

        return $this;
    }

    /**
     * @return Fluent
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @param Fluent $model
     */
    public function setModel(Fluent $model)
    {
        $this->model = $model;
    }

    /**
     * Get resource id.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->builder()->getResourceId();
    }

    /**
     * Disable submit with ajax.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableAjaxSubmit(bool $disable = true)
    {
        $this->useAjaxSubmit = !$disable;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowAjaxSubmit()
    {
        return $this->useAjaxSubmit === true;
    }

    /**
     * @param \Closure $closure
     * @return $this;
     */
    public function wrap(\Closure $closure)
    {
        $this->builder->wrap($closure);

        return $this;
    }

    /**
     * @return Builder
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->builder->getFormId();
    }

    /**
     * @return Repository
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * Generate a edit form.
     *
     * @param $id
     *
     * @return $this
     */
    public function edit($id)
    {
        $this->builder->setMode(Builder::MODE_EDIT);
        $this->builder->setResourceId($id);

        $this->setFieldValue();

        return $this;
    }

    /**
     * Use tab to split form.
     *
     * @param string  $title
     * @param Closure $content
     *
     * @return $this
     */
    public function tab($title, Closure $content, $active = false)
    {
        $this->getTab()->append($title, $content, $active);

        return $this;
    }

    /**
     * Get Tab instance.
     *
     * @return Tab
     */
    public function getTab()
    {
        if (is_null($this->tab)) {
            $this->tab = new Tab($this);
        }

        return $this->tab;
    }

    /**
     * Destroy data entity and remove files.
     *
     * @param $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $this->builder->setResourceId($id);

            $data = $this->repository->getDataWhenDeleting($this);

            $this->setModel(new Fluent($data));

            if (($response = $this->callDeleting()) instanceof Response) {
                return $response;
            }

            $this->repository->destroy($this, $data);

            if (($response = $this->callDeleted()) instanceof Response) {
                return $response;
            }

            $response = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } catch (\Throwable $exception) {
            $response = Handler::handleDestroyException($exception);

            $response = $response ?: [
                'status'  => false,
                'message' => $exception->getMessage() ?: trans('admin.delete_failed'),
            ];
        }

        return response()->json($response);

    }

    /**
     * Store a new record.
     *
     * @param array|null $data
     * @param string|string $redirectTo
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\Http\JsonResponse
     */
    public function store(?array $data = null, $redirectTo = null)
    {
        $data = $data ?: Input::all();

        if (($response = $this->callSubmitted())) {
            return $response;
        }

        if ($response = $this->handleUploadFile($data)) {
            return $response;
        }
        if ($response = $this->handleFileDeleteBeforeCreate($data)) {
            return $response;
        }

        if ($response = $this->handleFileDeleteWhenCreating($data)) {
            return $response;
        }

        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($data)) {
            if (!$this->isAjaxRequest()) {
                return back()->withInput()->withErrors($validationMessages);
            } else {
                return response()->json(['errors' => $validationMessages->getMessages()], 422);
            }
        }

        if (($response = $this->prepare($data))) {
            $this->deleteFilesWhenCreating($data);

            return $response;
        }

        $this->updates = $this->prepareInsert($this->updates);

        $id = $this->repository->store($this);

        $this->builder->setResourceId($id);

        if (($response = $this->callSaved())) {
            return $response;
        }

        if ($response = $this->ajaxResponse(trans('admin.save_succeeded'), $this->getRedirectUrl($id, $redirectTo))) {
            return $response;
        }

        return $this->redirectAfterStore($id, $redirectTo);
    }

    /**
     * Get ajax response.
     *
     * @param $message
     * @param null $redirect
     * @param bool $status
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function ajaxResponse($message, $redirect = null, bool $status = true)
    {
        if ($this->isAjaxRequest()) {
            return response()->json([
                'status'   => $status,
                'message'  => $message,
                'redirect' => $redirect,
            ]);
        }

        return false;
    }

    /**
     * ajax but not pjax
     *
     * @return bool
     */
    public function isAjaxRequest()
    {
        $request = Request::capture();

        return $request->ajax() && !$request->pjax();
    }

    /**
     * Prepare input data for insert or update.
     *
     * @param array $data
     *
     * @return Response|null
     */
    protected function prepare($data = [])
    {
        $this->inputs = array_merge($this->removeIgnoredFields($data), $this->inputs);

        if (($response = $this->callSaving()) instanceof Response) {
            return $response;
        }

        $this->updates = $this->inputs;
    }

    /**
     * Remove ignored fields from input.
     *
     * @param array $input
     *
     * @return array
     */
    protected function removeIgnoredFields($input)
    {
        Arr::forget($input, $this->ignored);

        return $input;
    }

    /**
     * Get data for insert or update.
     *
     * @return array
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }

    /**
     * Set data for insert or update.
     *
     * @param array $updates
     * @return $this
     */
    public function setUpdates(array $updates)
    {
        $this->updates = array_merge($this->updates, $updates);

        return $this;
    }

    /**
     * Handle update.
     *
     * @param $id
     * @param array|null $data
     * @param string|null $redirectTo
     * @return $this|bool|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|mixed|null
     */
    public function update(
        $id,
        ?array $data = null,
        $redirectTo = null
    )
    {
        $data = $data ?: Input::all();

        $this->builder->setResourceId($id);
        $this->builder->setMode(Builder::MODE_EDIT);

        if (($response = $this->callSubmitted())) {
            return $response;
        }

        if ($uploadFileResponse = $this->handleUploadFile($data)) {
            return $uploadFileResponse;
        }

        $isEditable = $this->isEditable($data);

        $data = $this->handleEditable($data);

        $data = $this->handleFileDelete($data);

        $this->setModel(new Fluent($this->repository->getDataWhenUpdating($this)));
        $this->setFieldOriginalValue();

        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($data)) {
            if (!$isEditable && !$this->isAjaxRequest()) {
                return back()->withInput()->withErrors($validationMessages);
            } else {
                return response()->json([
                    'errors' => $isEditable ? Arr::dot($validationMessages->getMessages()) : $validationMessages->getMessages()
                ], 422);
            }
        }

        if (($response = $this->prepare($data))) {
            return $response;
        }

        $this->updates = $this->prepareUpdate($this->updates);

        $this->repository->update($this);

        if (($result = $this->callSaved())) {
            return $result;
        }

        if ($response = $this->ajaxResponse(trans('admin.update_succeeded'), $this->getRedirectUrl($id, $redirectTo))) {
            return $response;
        }

        return $this->redirectAfterUpdate($id, $redirectTo);
    }

    /**
     * Get RedirectResponse after store.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function redirectAfterStore($id, $redirectTo)
    {
        admin_alert(trans('admin.save_succeeded'));

        return redirect($this->getRedirectUrl($id, $redirectTo));
    }

    /**
     * Get RedirectResponse after update.
     *
     * @param mixed $key
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectAfterUpdate($key, $redirectTo)
    {
        admin_alert(trans('admin.save_succeeded'));

        return redirect($this->getRedirectUrl($key, $redirectTo));
    }

    /**
     * @param $key
     * @param $redirectTo
     * @return string|false
     */
    public function getRedirectUrl($key, $redirectTo = null)
    {
        if ($redirectTo) return $redirectTo;

        $resourcesPath = $this->builder->isCreating() ?
            $this->getResource(0) : $this->getResource(-1);

        if (request('after-save') == 1) {
            // continue editing
            if ($this->builder->isEditing() && $this->isAjaxRequest()) {
                return false;
            }
            return rtrim($resourcesPath, '/')."/{$key}/edit";
        }
        if (request('after-save') == 2) {
            // continue creating
            return rtrim($resourcesPath, '/').'/create';
        }
        if (request('after-save') == 3) {
            // view resource
            return rtrim($resourcesPath, '/')."/{$key}";
        }

        return request(Builder::PREVIOUS_URL_KEY) ?: $resourcesPath;
    }

    /**
     * Check if request is from editable.
     *
     * @param array $input
     *
     * @return bool
     */
    protected function isEditable(array $input = [])
    {
        return array_key_exists('_editable', $input);
    }

    /**
     * Handle editable update.
     *
     * @param array $input
     *
     * @return array
     */
    protected function handleEditable(array $input = [])
    {
        if (array_key_exists('_editable', $input)) {
            $name = $input['name'];
            $value = $input['value'];

            Arr::forget($input, ['pk', 'value', 'name']);
            Arr::set($input, $name, $value);
        }

        return $input;
    }

    /**
     * Prepare input data for update.
     *
     * @param array $updates
     * @param bool  $oneToOneRelation If column is one-to-one relation.
     *
     * @return array
     */
    public function prepareUpdate(array $updates, $oneToOneRelation = false)
    {
        $prepared = [];

        /** @var Field $field */
        foreach ($this->builder->fields() as $field) {
            $columns = $field->column();

            // If column not in input array data, then continue.
            if (!Arr::has($updates, $columns)) {
                continue;
            }

            if ($this->invalidColumn($columns, $oneToOneRelation)) {
                continue;
            }

            $value = $this->getDataByColumn($updates, $columns);

            $value = $field->prepareInputValue($value);

            if (is_array($columns)) {
                foreach ($columns as $name => $column) {
                    Arr::set($prepared, $column, $value[$name]);
                }
            } elseif (is_string($columns)) {
                Arr::set($prepared, $columns, $value);
            }
        }

        return $prepared;
    }

    /**
     * @param string|array $columns
     * @param bool         $oneToOneRelation
     *
     * @return bool
     */
    protected function invalidColumn($columns, $oneToOneRelation = false)
    {
        foreach ((array) $columns as $column) {
            if ((!$oneToOneRelation && Str::contains($column, '.')) ||
                ($oneToOneRelation && !Str::contains($column, '.'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prepare input data for insert.
     *
     * @param $inserts
     *
     * @return array
     */
    public function prepareInsert($inserts)
    {
        if ($this->isHasOneRelation($inserts)) {
            $inserts = Arr::dot($inserts);
        }

        foreach ($inserts as $column => $value) {
            if (is_null($field = $this->getFieldByColumn($column))) {
                unset($inserts[$column]);
                continue;
            }

            $inserts[$column] = $field->prepareInputValue($value);
        }

        $prepared = [];

        foreach ($inserts as $key => $value) {
            Arr::set($prepared, $key, $value);
        }

        return $prepared;
    }

    /**
     * Is input data is has-one relation.
     *
     * @param array $inserts
     *
     * @return bool
     */
    protected function isHasOneRelation($inserts)
    {
        $first = current($inserts);

        if (!is_array($first)) {
            return false;
        }

        if (is_array(current($first))) {
            return false;
        }

        return Arr::isAssoc($first);
    }

    /**
     * Ignore fields to save.
     *
     * @param string|array $fields
     *
     * @return $this
     */
    public function ignore($fields)
    {
        $this->ignored = array_merge($this->ignored, (array) $fields);

        return $this;
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
     * @return Repository
     */
    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * @param array        $data
     * @param string|array $columns
     *
     * @return array|mixed
     */
    protected function getDataByColumn($data, $columns)
    {
        if (is_string($columns)) {
            return Arr::get($data, $columns);
        }

        if (is_array($columns)) {
            $value = [];
            foreach ($columns as $name => $column) {
                if (!Arr::has($data, $column)) {
                    continue;
                }
                $value[$name] = Arr::get($data, $column);
            }

            return $value;
        }
    }

    /**
     * Find field object by column.
     *
     * @param $column
     *
     * @return mixed
     */
    protected function getFieldByColumn($column)
    {
        return $this->builder->fields()->first(
            function (Field $field) use ($column) {
                if (is_array($field->column())) {
                    return in_array($column, $field->column());
                }

                return $field->column() == $column;
            }
        );
    }

    /**
     * Set original data for each field.
     *
     * @return void
     */
    protected function setFieldOriginalValue()
    {
        $values = $this->model->toArray();

        $this->builder->fields()->each(function (Field $field) use ($values) {
            $field->setOriginal($values);
        });
    }

    /**
     * Set all fields value in form.
     *
     * @return void
     */
    protected function setFieldValue()
    {
        $this->setModel(new Fluent($this->repository->edit($this)));

        $this->callEditing();

        $data = $this->model->toArray();

        $this->builder->fields()->each(function (Field $field) use ($data) {
            if (!in_array($field->column(), $this->ignored)) {
                $field->fill($data);
            }
        });
    }

    /**
     * Get validation messages.
     *
     * @param array $input
     *
     * @return MessageBag|bool
     */
    public function validationMessages($input)
    {
        $failedValidators = [];

        /** @var Field $field */
        foreach ($this->builder->fields() as $field) {
            if (!$validator = $field->getValidator($input)) {
                continue;
            }

            if (($validator instanceof Validator) && !$validator->passes()) {
                $failedValidators[] = $validator;
            }
        }

        $message = $this->mergeValidationMessages($failedValidators);

        if ($message->any() && $this->builder->isCreating()) {
            $this->deleteFilesWhenCreating($input);
        }

        return $message->any() ? $message : false;
    }

    /**
     * @param $column
     * @param string|array $messages
     */
    public function addValidationMessages($column, $messages = null)
    {
        if ($column instanceof MessageProvider) {
            return $this->addValidationMessages($column->getMessageBag()->getMessages());
        }

        if (!$this->validationMessages) {
            $this->validationMessages = new MessageBag;
        }

        if (!$column) {
            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $k => &$v) {
                $v = (array)$v;
            }
            $this->validationMessages->merge($column);
        } elseif ($messages) {
            $this->validationMessages->merge([$column => (array)$messages]);
        }

        return $this;
    }


    /**
     * Merge validation messages from input validators.
     *
     * @param \Illuminate\Validation\Validator[] $validators
     *
     * @return MessageBag
     */
    protected function mergeValidationMessages($validators)
    {
        $messageBag = new MessageBag();

        foreach ($validators as $validator) {
            $messageBag = $messageBag->merge($validator->messages());
        }

        if ($this->validationMessages) {
            return $messageBag->merge($this->validationMessages);
        }

        return $messageBag;
    }

    /**
     * Set action for form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->builder->setAction($action);

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->builder->getAction();
    }

    /**
     * Set field and label width in current form.
     *
     * @param int $fieldWidth
     * @param int $labelWidth
     *
     * @return $this
     */
    public function setWidth($fieldWidth = 8, $labelWidth = 2)
    {
        $this->builder->fields()->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field  */
            $field->setWidth($fieldWidth, $labelWidth);
        });

        $this->builder->setWidth($fieldWidth, $labelWidth);

        return $this;
    }

    /**
     * Set view for form.
     *
     * @param string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->builder->setView($view);

        return $this;
    }

    /**
     * Set title for form.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title = '')
    {
        $this->builder->setTitle($title);

        return $this;
    }

    /**
     * Add a row in form.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function row(Closure $callback)
    {
        $this->rows[] = new Row($callback, $this);

        return $this;
    }

    /**
     * Tools setting for form.
     *
     * @param Closure $callback
     */
    public function tools(Closure $callback)
    {
        $callback->call($this, $this->builder->getTools());
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disableHeader(bool $disable = true)
    {
        $this->builder->disableHeader($disable);

        return $this;
    }

    /**
     * @param bool $disable
     * @return $this
     */
    public function disableFooter(bool $disable = true)
    {
        $this->builder->disableFooter($disable);

        return $this;
    }

    /**
     * Disable form submit.
     *
     * @return $this
     */
    public function disableSubmitButton(bool $disable = true)
    {
        $this->builder->getFooter()->disableSubmit($disable);

        return $this;
    }

    /**
     * Disable form reset.
     *
     * @return $this
     */
    public function disableResetButton(bool $disable = true)
    {
        $this->builder->getFooter()->disableReset($disable);

        return $this;
    }

    /**
     * Disable View Checkbox on footer.
     *
     * @return $this
     */
    public function disableViewCheck(bool $disable = true)
    {
        $this->builder->getFooter()->disableViewCheck($disable);

        return $this;
    }

    /**
     * Disable Editing Checkbox on footer.
     *
     * @return $this
     */
    public function disableEditingCheck(bool $disable = true)
    {
        $this->builder->getFooter()->disableEditingCheck($disable);

        return $this;
    }

    /**
     * Disable Creating Checkbox on footer.
     *
     * @return $this
     */
    public function disableCreatingCheck(bool $disable = true)
    {
        $this->builder->getFooter()->disableCreatingCheck($disable);

        return $this;
    }

    /**
     * Disable `view` tool.
     *
     * @return $this
     */
    public function disableViewButton(bool $disable = true)
    {
        $this->builder->getTools()->disableView($disable);

        return $this;
    }

    /**
     * Disable `list` tool.
     *
     * @return $this
     */
    public function disableListButton(bool $disable = true)
    {
        $this->builder->getTools()->disableList($disable);

        return $this;
    }

    /**
     * Disable `delete` tool.
     *
     * @return $this
     */
    public function disableDeleteButton(bool $disable = true)
    {
        $this->builder->getTools()->disableDelete($disable);

        return $this;
    }

    /**
     * Footer setting for form.
     *
     * @param Closure $callback
     */
    public function footer(Closure $callback)
    {
        call_user_func($callback, $this->builder->getFooter());
    }

    /**
     * Get current resource route url.
     *
     * @param int $slice
     *
     * @return string
     */
    public function getResource($slice = -2)
    {
        $path = $this->resource ?: app('request')->getUri();

        $segments = explode('/', trim($path, '/'));

        if ($slice != 0) {
            $segments = array_slice($segments, 0, $slice);
        }

        return implode('/', $segments);
    }

    /**
     * Set resource path.
     *
     * @param string $resource
     * @return $this
     */
    public function resource(string $resource)
    {
        if ($resource) {
            $this->resource = URL::isValidUrl($resource) ? $resource : admin_base_path($resource);
        }

        return $this;
    }

    /**
     * Render the form contents.
     *
     * @return string
     */
    public function render()
    {
        try {
            $this->callComposing();

            return $this->builder->render();
        } catch (\Throwable $e) {
            return Handler::renderException($e);
        }
    }

    /**
     * Get or set input data.
     *
     * @param string $key
     * @param null   $value
     *
     * @return array|mixed
     */
    public function input($key, $value = null)
    {
        if (is_null($value)) {
            return Arr::get($this->inputs, $key);
        }

        return Arr::set($this->inputs, $key, $value);
    }

    /**
     * @param int $width
     * @param Closure $callback
     */
    public function column(int $width, \Closure $callback)
    {
        $layout = $this->builder->layout();

        $layout->column($width, $callback($layout->form()));
    }

    /**
     * @param int $width
     * @return $this
     */
    public function setDefaultColumnWidth(int $width)
    {
        $this->builder->setDefaultColumnWidth($width);

        return $this;
    }

    /**
     * @return $this
     */
    protected function prepareDialogForm()
    {
        DialogForm::prepare($this);

        return $this;
    }

    /**
     * @return bool
     */
    public static function isDialogFormPage()
    {
        return DialogForm::is();
    }

    /**
     * Create a dialog form.
     *
     * @param string|null $title
     * @return DialogForm
     */
    public static function popup(?string $title = null)
    {
        return new DialogForm($title);
    }

    /**
     * Register custom field.
     *
     * @param string $abstract
     * @param string $class
     *
     * @return void
     */
    public static function extend($abstract, $class)
    {
        static::$availableFields[$abstract] = $class;
    }

    /**
     * @return array
     */
    public static function getExtensions()
    {
        return static::$availableFields;
    }

    /**
     * Set form field alias.
     *
     * @param string $field
     * @param string $alias
     *
     * @return void
     */
    public static function alias($field, $alias)
    {
        static::$fieldAlias[$alias] = $field;
    }

    /**
     * Find field class.
     *
     * @param string $method
     *
     * @return bool|mixed
     */
    public static function findFieldClass($method)
    {
        // If alias exists.
        if (isset(static::$fieldAlias[$method])) {
            $method = static::$fieldAlias[$method];
        }

        $class = Arr::get(static::$availableFields, $method);

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @return array|mixed
     */
    public function __get($name)
    {
        return $this->input($name);
    }

    /**
     * Setter.
     *
     * @param string $name
     * @param $value
     */
    public function __set($name, $value)
    {
        return Arr::set($this->inputs, $name, $value);
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field
     */
    public function __call($method, $arguments)
    {
        if ($className = static::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, '');

            $element = new $className($column, array_slice($arguments, 1));

            $this->pushField($element);

            return $element;
        }

        admin_error('Error', "Field type [$method] does not exist.");

        return new Field\Nullable();
    }
}
