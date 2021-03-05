<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Actions\Action;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Form\AbstractTool;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Concerns;
use Dcat\Admin\Form\Condition;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Traits\HasFormResponse;
use Dcat\Admin\Widgets\DialogForm;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Form.
 *
 * @method Field\Text                   text($column, $label = '')
 * @method Field\Checkbox               checkbox($column, $label = '')
 * @method Field\Radio                  radio($column, $label = '')
 * @method Field\Select                 select($column, $label = '')
 * @method Field\MultipleSelect         multipleSelect($column, $label = '')
 * @method Field\Textarea               textarea($column, $label = '')
 * @method Field\Hidden                 hidden($column, $label = '')
 * @method Field\Id                     id($column, $label = '')
 * @method Field\Ip                     ip($column, $label = '')
 * @method Field\Url                    url($column, $label = '')
 * @method Field\Email                  email($column, $label = '')
 * @method Field\Mobile                 mobile($column, $label = '')
 * @method Field\Slider                 slider($column, $label = '')
 * @method Field\Map                    map($latitude, $longitude, $label = '')
 * @method Field\Editor                 editor($column, $label = '')
 * @method Field\Date                   date($column, $label = '')
 * @method Field\Datetime               datetime($column, $label = '')
 * @method Field\Time                   time($column, $label = '')
 * @method Field\Year                   year($column, $label = '')
 * @method Field\Month                  month($column, $label = '')
 * @method Field\DateRange              dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange          datetimeRange($start, $end, $label = '')
 * @method Field\TimeRange              timeRange($start, $end, $label = '')
 * @method Field\Number                 number($column, $label = '')
 * @method Field\Currency               currency($column, $label = '')
 * @method Field\SwitchField            switch($column, $label = '')
 * @method Field\Display                display($column, $label = '')
 * @method Field\Rate                   rate($column, $label = '')
 * @method Field\Divide                 divider(string $title = null)
 * @method Field\Password               password($column, $label = '')
 * @method Field\Decimal                decimal($column, $label = '')
 * @method Field\Html                   html($html, $label = '')
 * @method Field\Tags                   tags($column, $label = '')
 * @method Field\Icon                   icon($column, $label = '')
 * @method Field\Embeds                 embeds($column, $label = '', Closure $callback = null)
 * @method Field\Captcha                captcha()
 * @method Field\Listbox                listbox($column, $label = '')
 * @method Field\File                   file($column, $label = '')
 * @method Field\Image                  image($column, $label = '')
 * @method Field\MultipleFile           multipleFile($column, $label = '')
 * @method Field\MultipleImage          multipleImage($column, $label = '')
 * @method Field\HasMany                hasMany($column, $labelOrCallback, $callback = null)
 * @method Field\Tree                   tree($column, $label = '')
 * @method Field\Table                  table($column, $labelOrCallback, $callback = null)
 * @method Field\ListField              list($column, $label = '')
 * @method Field\Timezone               timezone($column, $label = '')
 * @method Field\KeyValue               keyValue($column, $label = '')
 * @method Field\Tel                    tel($column, $label = '')
 * @method Field\Markdown               markdown($column, $label = '')
 * @method Field\Range                  range($start, $end, $label = '')
 * @method Field\Color                  color($column, $label = '')
 * @method Field\ArrayField             array($column, $labelOrCallback, $callback = null)
 * @method Field\SelectTable            selectTable($column, $label = '')
 * @method Field\MultipleSelectTable    multipleSelectTable($column, $label = '')
 * @method Field\Button                 button(string $html = null)
 */
class Form implements Renderable
{
    use HasBuilderEvents;
    use HasFormResponse;
    use Concerns\HasEvents;
    use Concerns\HasFiles;
    use Concerns\HandleCascadeFields;
    use Concerns\HasRows;
    use Concerns\HasTabs;
    use Macroable {
            __call as macroCall;
        }

    /**
     * Remove flag in `has many` form.
     */
    const REMOVE_FLAG_NAME = '_remove_';

    const CURRENT_URL_NAME = '_current_';

    /**
     * Available fields.
     *
     * @var array
     */
    protected static $availableFields = [
        'button'              => Field\Button::class,
        'checkbox'            => Field\Checkbox::class,
        'currency'            => Field\Currency::class,
        'date'                => Field\Date::class,
        'dateRange'           => Field\DateRange::class,
        'datetime'            => Field\Datetime::class,
        'datetimeRange'       => Field\DatetimeRange::class,
        'decimal'             => Field\Decimal::class,
        'display'             => Field\Display::class,
        'divider'             => Field\Divide::class,
        'embeds'              => Field\Embeds::class,
        'editor'              => Field\Editor::class,
        'email'               => Field\Email::class,
        'hidden'              => Field\Hidden::class,
        'id'                  => Field\Id::class,
        'ip'                  => Field\Ip::class,
        'map'                 => Field\Map::class,
        'mobile'              => Field\Mobile::class,
        'month'               => Field\Month::class,
        'multipleSelect'      => Field\MultipleSelect::class,
        'number'              => Field\Number::class,
        'password'            => Field\Password::class,
        'radio'               => Field\Radio::class,
        'rate'                => Field\Rate::class,
        'select'              => Field\Select::class,
        'slider'              => Field\Slider::class,
        'switch'              => Field\SwitchField::class,
        'text'                => Field\Text::class,
        'textarea'            => Field\Textarea::class,
        'time'                => Field\Time::class,
        'timeRange'           => Field\TimeRange::class,
        'url'                 => Field\Url::class,
        'year'                => Field\Year::class,
        'html'                => Field\Html::class,
        'tags'                => Field\Tags::class,
        'icon'                => Field\Icon::class,
        'captcha'             => Field\Captcha::class,
        'listbox'             => Field\Listbox::class,
        'file'                => Field\File::class,
        'image'               => Field\Image::class,
        'multipleFile'        => Field\MultipleFile::class,
        'multipleImage'       => Field\MultipleImage::class,
        'hasMany'             => Field\HasMany::class,
        'tree'                => Field\Tree::class,
        'table'               => Field\Table::class,
        'list'                => Field\ListField::class,
        'timezone'            => Field\Timezone::class,
        'keyValue'            => Field\KeyValue::class,
        'tel'                 => Field\Tel::class,
        'markdown'            => Field\Markdown::class,
        'range'               => Field\Range::class,
        'color'               => Field\Color::class,
        'array'               => Field\ArrayField::class,
        'selectTable'         => Field\SelectTable::class,
        'multipleSelectTable' => Field\MultipleSelectTable::class,
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
     * @var Closure
     */
    protected $callback;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var bool
     */
    protected $ajax = true;

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
     * @var bool
     */
    protected $isSoftDeletes = false;

    /**
     * @var MessageBag
     */
    protected $validationMessages;

    /**
     * @var Condition[]
     */
    protected $conditions = [];

    /**
     * @var array
     */
    public $context = [];

    /**
     * @var bool
     */
    public $validationErrorToastr = true;

    /**
     * Create a new form instance.
     *
     * @param Repository|Model|\Illuminate\Database\Eloquent\Builder|string $model
     * @param \Closure                                                      $callback
     * @param Request                                                       $request
     */
    public function __construct($repository = null, ?Closure $callback = null, Request $request = null)
    {
        $this->repository = $repository ? Admin::repository($repository) : null;
        $this->callback = $callback;
        $this->request = $request ?: request();
        $this->builder = new Builder($this);
        $this->isSoftDeletes = $repository ? $this->repository->isSoftDeletes() : false;

        $this->model(new Fluent());
        $this->prepareDialogForm();
        $this->callResolving();
    }

    /**
     * Create a form instance.
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
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field $field)
    {
        $field->setForm($this);

        $this->builder->pushField($field);
        $this->builder->layout()->addField($field);

        $width = $this->builder->getWidth();

        $field->width($width['field'], $width['label']);

        $field::requireAssets();

        return $this;
    }

    /**
     * Get specify field.
     *
     * @param string|null $name
     *
     * @return Field|Collection|Field[]|null
     */
    public function field($name = null)
    {
        return $this->builder->field($name);
    }

    /**
     * @return Collection|Field[]
     */
    public function fields()
    {
        return $this->builder->fields();
    }

    /**
     * @param $column
     *
     * @return $this
     */
    public function removeField($column)
    {
        $this->builder->removeField($column);

        return $this;
    }

    /**
     * @param string $title
     * @param string $content
     *
     * @return $this
     */
    public function confirm(?string $title = null, ?string $content = null)
    {
        $this->builder->confirm($title, $content);

        return $this;
    }

    /**
     * @return bool
     */
    public function isCreating()
    {
        return $this->builder->isCreating();
    }

    /**
     * @return bool
     */
    public function isEditing()
    {
        return $this->builder->isEditing();
    }

    /**
     * @return bool
     */
    public function isDeleting()
    {
        return $this->builder->isDeleting();
    }

    /**
     * @param Fluent|array|\Illuminate\Database\Eloquent\Model $model
     *
     * @return Fluent|\Illuminate\Database\Eloquent\Model|void
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
     * 启用或禁用ajax表单提交.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function ajax(bool $value = true)
    {
        $this->ajax = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowAjaxSubmit()
    {
        return $this->ajax === true;
    }

    /**
     * 设置使用 Toastr 展示字段验证信息.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function validationErrorToastr(bool $value = true)
    {
        $this->validationErrorToastr = $value;

        return $this;
    }

    /**
     * @param \Closure $closure
     *
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
    public function getElementId()
    {
        return $this->builder->getElementId();
    }

    /**
     * @return \Dcat\Admin\Form\Layout
     */
    public function layout()
    {
        return $this->builder->layout();
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
        $this->builder->mode(Builder::MODE_EDIT);
        $this->builder->setResourceId($id);

        $this->model($this->repository->edit($this));

        return $this;
    }

    /**
     * Add a fieldset to form.
     *
     * @param string  $title
     * @param Closure $setCallback
     *
     * @return Field\Fieldset
     */
    public function fieldset(string $title, Closure $setCallback)
    {
        $fieldset = new Field\Fieldset();

        $this->html($fieldset->start($title))->plain();

        $setCallback($this);

        $this->html($fieldset->end())->plain();

        return $fieldset;
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
            $this->builder->mode(Builder::MODE_DELETE);

            $data = $this->repository->deleting($this);

            $this->model(new Fluent($data));

            $this->setFieldOriginalValue();

            $this->build();

            if ($response = $this->callDeleting()) {
                return $this->sendResponse($response);
            }

            $result = $this->repository->delete($this, $data);

            // 返回 JsonResponse 对象，直接中断后续逻辑
            if ($result instanceof JsonResponse) {
                return $this->sendResponse($result);
            }

            if ($response = $this->callDeleted($result)) {
                return $this->sendResponse($response);
            }

            $status = $result ? true : false;
            $message = $result ? trans('admin.delete_succeeded') : trans('admin.delete_failed');
        } catch (\Throwable $exception) {
            $response = $this->handleException($exception);

            if ($response instanceof Response) {
                return $response;
            }

            $status = false;
            $message = $exception->getMessage() ?: trans('admin.delete_failed');
        }

        return $this->sendResponse(
            $this->response()
                ->alert()
                ->status($status)
                ->message($message)
        );
    }

    /**
     * @param \Throwable $e
     *
     * @return mixed
     */
    protected function handleException(\Throwable $e)
    {
        return Admin::handleException($e);
    }

    /**
     * Store a new record.
     *
     * @param array|null    $data
     * @param string|string $redirectTo
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\Http\JsonResponse|Response
     */
    public function store(?array $data = null, $redirectTo = null)
    {
        try {
            if ($data) {
                $this->request->replace($data);
            }

            $data = $data ?: $this->request->all();

            if ($response = $this->beforeStore($data)) {
                return $this->sendResponse($response);
            }

            $this->updates = $this->prepareInsert($this->updates);

            $id = $this->repository->store($this);

            // 返回 JsonResponse 对象，直接中断后续逻辑
            if ($id instanceof JsonResponse) {
                return $this->sendResponse($id);
            }

            $this->builder->setResourceId($id);

            if (($response = $this->callSaved($id))) {
                return $this->sendResponse($response);
            }

            if (! $id) {
                return $this->sendResponse(
                    $this->response()
                        ->error(trans('admin.save_failed'))
                );
            }

            $url = $this->getRedirectUrl($id, $redirectTo);

            return $this->sendResponse(
                $this->response()
                    ->redirectIf($url !== false, $url)
                    ->success(trans('admin.save_succeeded'))
            );
        } catch (\Throwable $e) {
            $response = $this->handleException($e);

            if ($response instanceof Response) {
                return $response;
            }

            return $this->sendResponse(
                $this->response()
                    ->error(trans('admin.save_failed'))
                    ->withExceptionIf($e->getMessage(), $e)
            );
        }
    }

    /**
     * Before store.
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|Response|void
     */
    protected function beforeStore(array $data)
    {
        $this->inputs = $data;

        $this->build();

        if (($response = $this->callSubmitted())) {
            return $response;
        }

        if ($response = $this->handleUploadFile($this->inputs)) {
            return $response;
        }

        if ($response = $this->deleteFileWhenCreating($this->inputs)) {
            return $response;
        }

        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($this->inputs)) {
            return $this->validationErrorsResponse($validationMessages);
        }

        if (($response = $this->prepare($this->inputs))) {
            return $response;
        }
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
        $this->inputs = $this->removeIgnoredFields($data);

        if ($response = $this->callSaving()) {
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
    public function removeIgnoredFields($input)
    {
        Arr::forget($input, $this->ignored);

        return $input;
    }

    /**
     * Get or set data for insert or update.
     *
     * @param array $updates
     *
     * @return $this|array
     */
    public function updates(array $updates = null)
    {
        if ($updates === null) {
            return $this->updates;
        }

        $this->updates = array_merge($this->updates, $updates);

        return $this;
    }

    /**
     * Handle orderable update.
     *
     * @param int   $id
     * @param array $input
     *
     * @return Response
     */
    protected function handleOrderable(array $input = [])
    {
        if (array_key_exists('_orderable', $input)) {
            $updated = $input['_orderable'] == 1
                ? $this->repository->moveOrderUp()
                : $this->repository->moveOrderDown();

            $message = $updated
                ? __('admin.update_succeeded')
                : __('admin.nothing_updated');

            return $this->sendResponse(
                $this->response()
                    ->status((bool) $updated)
                    ->message($message)
            );
        }
    }

    /**
     * Handle update.
     *
     * @param $id
     * @param array|null  $data
     * @param string|null $redirectTo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse||Response
     */
    public function update(
        $id,
        ?array $data = null,
        $redirectTo = null
    ) {
        try {
            if ($data) {
                $this->request->replace($data);
            }

            $data = $data ?: $this->request->all();

            if ($response = $this->beforeUpdate($id, $data)) {
                return $this->sendResponse($response);
            }

            $this->updates = $this->prepareUpdate($this->updates);

            $updated = $this->repository->update($this);

            // 返回 JsonResponse 对象，直接中断后续逻辑
            if ($updated instanceof JsonResponse) {
                return $this->sendResponse($updated);
            }

            if ($response = $this->callSaved($updated)) {
                return $this->sendResponse($response);
            }

            if (! $updated) {
                return $this->sendResponse(
                    $this->response()
                        ->error(trans('admin.update_failed'))
                );
            }

            $url = $this->getRedirectUrl($id, $redirectTo);

            return $this->sendResponse(
                $this->response()
                    ->success(trans('admin.update_succeeded'))
                    ->redirectIf($url !== false, $url)
                    ->refreshIf($url === false)
            );
        } catch (\Throwable $e) {
            $response = $this->handleException($e);

            if ($response instanceof Response) {
                return $response;
            }

            return $this->sendResponse(
                $this->response()
                    ->error(trans('admin.update_failed'))
                    ->withExceptionIf($e->getMessage(), $e)
            );
        }
    }

    /**
     * Before update.
     *
     * @param array $data
     *
     * @return Response|void
     */
    protected function beforeUpdate($id, array &$data)
    {
        $this->builder->setResourceId($id);
        $this->builder->mode(Builder::MODE_EDIT);

        $this->inputs = $data;

        $this->model($this->repository->updating($this));

        $this->build();

        $this->setFieldOriginalValue();

        if ($response = $this->callSubmitted()) {
            return $response;
        }

        if ($uploadFileResponse = $this->handleUploadFile($this->inputs)) {
            return $uploadFileResponse;
        }

        $isEditable = $this->isEditable($this->inputs);

        $this->inputs = $this->handleEditable($this->inputs);

        $this->inputs = $this->handleFileDelete($this->inputs);

        $this->inputs = $this->handleHasManyValues($this->inputs);

        if ($response = $this->handleOrderable($this->inputs)) {
            return $response;
        }

        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($this->inputs)) {
            return $this->validationErrorsResponse(
                $isEditable ? Arr::dot($validationMessages->toArray()) : $validationMessages
            );
        }

        if ($response = $this->prepare($this->inputs)) {
            return $response;
        }
    }

    /**
     * @param array $inputs
     *
     * @return array
     */
    protected function handleHasManyValues(array $inputs)
    {
        foreach ($inputs as $column => &$input) {
            $field = $this->builder()->field($column);

            if (is_array($input) && $field instanceof Field\HasMany) {
                $keyName = $field->getKeyName();

                foreach ($input as $k => &$v) {
                    if (! array_key_exists($keyName, $v)) {
                        $v[$keyName] = $k;
                    }

                    if (empty($v[NestedForm::REMOVE_FLAG_NAME])) {
                        $v[NestedForm::REMOVE_FLAG_NAME] = null;
                    }
                }
            }
        }

        return $inputs;
    }

    /**
     * @param $key
     * @param $redirectTo
     *
     * @return string|null
     */
    public function getRedirectUrl($key, $redirectTo = null)
    {
        if ($redirectTo) {
            return $redirectTo;
        }

        $resourcesPath = $this->isCreating() ? $this->resource(0) : $this->resource(-1);

        if ($this->request->get('after-save') == 1) {
            // continue editing
            if ($this->builder->isEditing()) {
                return false;
            }

            return rtrim($resourcesPath, '/')."/{$key}/edit";
        }

        if ($this->request->get('after-save') == 2) {
            // continue creating
            return rtrim($resourcesPath, '/').'/create';
        }

        if ($this->request->get('after-save') == 3) {
            // view resource
            return rtrim($resourcesPath, '/')."/{$key}";
        }

        return $this->request->get(Builder::PREVIOUS_URL_KEY) ?: $this->getCurrentUrl($resourcesPath);
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
     *
     * @return array
     */
    public function prepareUpdate(array $updates)
    {
        $prepared = [];

        /** @var Field $field */
        foreach ($this->builder->fields() as $field) {
            $columns = $field->column();

            // If column not in input array data, then continue.
            if (! Arr::has($updates, $columns)) {
                continue;
            }

            $value = $this->getDataByColumn($updates, $columns);

            $value = $field->prepare($value);

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
     * Prepare input data for insert.
     *
     * @param $inserts
     *
     * @return array
     */
    public function prepareInsert($inserts)
    {
        Helper::prepareHasOneRelation($this->builder->fields(), $inserts);

        foreach ($inserts as $column => $value) {
            if (is_null($field = $this->field($column))) {
                unset($inserts[$column]);
                continue;
            }

            $inserts[$column] = $field->prepare($value);
        }

        $prepared = [];

        foreach ($inserts as $key => $value) {
            Arr::set($prepared, $key, $value);
        }

        return $prepared;
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
        $this->ignored = Arr::flatten(
            array_merge($this->ignored, (array) $fields)
        );

        return $this;
    }

    /**
     * @param $keys
     *
     * @return $this
     */
    public function forgetIgnored($keys)
    {
        Arr::forget($this->ignored, $keys);

        return $this;
    }

    /**
     * Get primary key name of model.
     *
     * @return string
     */
    public function keyName()
    {
        if (! $this->repository) {
            return 'id';
        }

        return $this->repository->getKeyName();
    }

    /**
     * @return string|void
     */
    public function createdAtColumn()
    {
        if (! $this->repository) {
            return;
        }

        return $this->repository->getCreatedAtColumn();
    }

    /**
     * @return string|void
     */
    public function updatedAtColumn()
    {
        if (! $this->repository) {
            return;
        }

        return $this->repository->getUpdatedAtColumn();
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
                if (! Arr::has($data, $column)) {
                    continue;
                }
                $value[$name] = Arr::get($data, $column);
            }

            return $value;
        }
    }

    /**
     * Set original data for each field.
     *
     * @return void
     */
    protected function setFieldOriginalValue()
    {
        $data = $this->model()->toArray();

        $this->builder->fields()->each(function (Field $field) use ($data) {
            $field->setOriginal($data);
        });
    }

    /**
     * @example
     *     $form->if(true)->then(function (Form $form) {
     *          $form->text('name');
     *     });
     *
     *     $form->if(function (Form $form) {
     *         return $form->model()->id > 5;
     *     })->then(function (Form $form) {
     *         $form->text('name');
     *     });
     *
     *     $form->if(true)->now(function (Form $form) {
     *         $form->text('name');
     *     });
     *
     *     $form->if(true)->creating(function (Form $form) {});
     *
     *     $form->if(true)->removeField('name');
     *
     * @param bool|\Closure $condition
     *
     * @return Condition
     */
    public function if($condition)
    {
        return $this->conditions[] = new Condition($condition, $this);
    }

    /**
     * @return void
     */
    protected function rendering()
    {
        $this->build();

        if ($this->isCreating()) {
            $this->callCreating();

            return;
        }

        $this->fillFields($this->model()->toArray());
        $this->callEditing();
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function fillFields(array $data)
    {
        $this->builder->fields()->each(function (Field $field) use ($data) {
            if (! in_array($field->column(), $this->ignored, true)) {
                $field->fill($data);
            }
        });
    }

    /**
     * @return void
     */
    protected function build()
    {
        if ($callback = $this->callback) {
            $callback($this);
        }

        foreach ($this->conditions as $condition) {
            $condition->process();
        }
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
            if (! $validator = $field->getValidator($input)) {
                continue;
            }

            if (($validator instanceof Validator) && ! $validator->passes()) {
                $failedValidators[] = [$field, $validator];
            }
        }

        $message = $this->mergeValidationMessages($failedValidators);

        if ($message->any() && $this->builder->isCreating()) {
            $this->deleteFiles($input, true);
        }

        return $message->any() ? $message : false;
    }

    /**
     * @param string|array|MessageProvider $column
     * @param string|array                 $messages
     *
     * @return $this
     */
    public function responseValidationMessages($column, $messages = null)
    {
        if ($column instanceof MessageProvider) {
            return $this->responseValidationMessages($column->getMessageBag()->getMessages());
        }

        if (! $this->validationMessages) {
            $this->validationMessages = new MessageBag();
        }

        if (! $column) {
            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $k => &$v) {
                $v = (array) $v;
            }
            $this->validationMessages->merge($column);
        } elseif ($messages) {
            $this->validationMessages->merge([$column => (array) $messages]);
        }

        return $this;
    }

    /**
     * Merge validation messages from input validators.
     *
     * @param array $validators
     *
     * @return MessageBag
     */
    protected function mergeValidationMessages($validators)
    {
        $messageBag = new MessageBag();

        foreach ($validators as $value) {
            [$field, $validator] = $value;

            $messageBag = $messageBag->merge($field->formatValidatorMessages($validator->messages()));
        }

        if ($this->validationMessages) {
            return $messageBag->merge($this->validationMessages);
        }

        return $messageBag;
    }

    /**
     * Get or set action for form.
     *
     * @param string|null $action
     *
     * @return $this|string
     */
    public function action($action = null)
    {
        $value = $this->builder->action($action);

        if ($action === null) {
            return $value;
        }

        return $this;
    }

    /**
     * Set field and label width in current form.
     *
     * @param int $fieldWidth
     * @param int $labelWidth
     *
     * @return $this
     */
    public function width($fieldWidth = 8, $labelWidth = 2)
    {
        $this->builder->fields()->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field  */
            $field->width($fieldWidth, $labelWidth);
        });

        $this->builder->width($fieldWidth, $labelWidth);

        return $this;
    }

    /**
     * Set view for form.
     *
     * @param string $view
     *
     * @return $this
     */
    public function view($view)
    {
        $this->builder->view($view);

        return $this;
    }

    /**
     * @param array $vars
     *
     * @return $this
     */
    public function addVariables(array $vars)
    {
        $this->builder->addVariables($vars);

        return $this;
    }

    /**
     * Get or set title for form.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title = null)
    {
        $this->builder->title($title);

        return $this;
    }

    /**
     * Tools setting for form.
     *
     * @param Closure|string|AbstractTool|Renderable|Action|array $callback
     *
     * @return $this;
     */
    public function tools($callback)
    {
        if ($callback instanceof Closure) {
            $callback->call($this, $this->builder->tools());

            return $this;
        }

        if (! is_array($callback)) {
            $callback = [$callback];
        }

        foreach ($callback as $tool) {
            $this->builder->tools()->append($tool);
        }

        return $this;
    }

    /**
     * @param bool $disable
     *
     * @return $this
     */
    public function disableHeader(bool $disable = true)
    {
        $this->builder->disableHeader($disable);

        return $this;
    }

    /**
     * @param bool $disable
     *
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
        $this->builder->footer()->disableSubmit($disable);

        return $this;
    }

    /**
     * Disable form reset.
     *
     * @return $this
     */
    public function disableResetButton(bool $disable = true)
    {
        $this->builder->footer()->disableReset($disable);

        return $this;
    }

    /**
     * Disable View Checkbox on footer.
     *
     * @return $this
     */
    public function disableViewCheck(bool $disable = true)
    {
        $this->builder->footer()->disableViewCheck($disable);

        return $this;
    }

    /**
     * Disable Editing Checkbox on footer.
     *
     * @return $this
     */
    public function disableEditingCheck(bool $disable = true)
    {
        $this->builder->footer()->disableEditingCheck($disable);

        return $this;
    }

    /**
     * Disable Creating Checkbox on footer.
     *
     * @return $this
     */
    public function disableCreatingCheck(bool $disable = true)
    {
        $this->builder->footer()->disableCreatingCheck($disable);

        return $this;
    }

    /**
     * default View Checked on footer.
     *
     * @return $this
     */
    public function defaultViewChecked(bool $checked = true)
    {
        $this->builder->footer()->defaultViewChecked($checked);

        return $this;
    }

    /**
     * default Editing Checked on footer.
     *
     * @return $this
     */
    public function defaultEditingChecked(bool $checked = true)
    {
        $this->builder->footer()->defaultEditingChecked($checked);

        return $this;
    }

    /**
     * default Creating Checked on footer.
     *
     * @return $this
     */
    public function defaultCreatingChecked(bool $checked = true)
    {
        $this->builder->footer()->defaultCreatingChecked($checked);

        return $this;
    }

    /**
     * Disable `view` tool.
     *
     * @return $this
     */
    public function disableViewButton(bool $disable = true)
    {
        $this->builder->tools()->disableView($disable);

        return $this;
    }

    /**
     * Disable `list` tool.
     *
     * @return $this
     */
    public function disableListButton(bool $disable = true)
    {
        $this->builder->tools()->disableList($disable);

        return $this;
    }

    /**
     * Disable `delete` tool.
     *
     * @return $this
     */
    public function disableDeleteButton(bool $disable = true)
    {
        $this->builder->tools()->disableDelete($disable);

        return $this;
    }

    /**
     * Footer setting for form.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function footer(Closure $callback)
    {
        call_user_func($callback, $this->builder->footer());

        return $this;
    }

    /**
     * Get current resource route url.
     *
     * @param int $slice
     *
     * @return string
     */
    public function resource($slice = -2)
    {
        $path = $this->resource ?: $this->request->getUri();

        $segments = explode('/', trim($path, '/'));

        if ($slice != 0) {
            $segments = array_slice($segments, 0, $slice);
        }

        return url(implode('/', $segments));
    }

    /**
     * Set resource path.
     *
     * @param string $resource
     *
     * @return $this
     */
    public function setResource(string $resource)
    {
        if ($resource) {
            $this->resource = admin_url($resource);
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
        $this->rendering();

        $this->callComposing();

        return $this->builder->render();
    }

    /**
     * Get or set input data.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return array|mixed
     */
    public function input($key = null, $value = null)
    {
        if (is_null($key)) {
            return $this->inputs;
        }

        if (is_null($value)) {
            return Arr::get($this->inputs, $key);
        }

        if (is_array($key)) {
            $this->inputs = array_merge($this->inputs, $key);

            return;
        }

        Arr::set($this->inputs, $key, $value);
    }

    /**
     * @param string|array $keys
     *
     * @return void
     */
    public function deleteInput($keys)
    {
        Arr::forget($this->inputs, $keys);
    }

    /**
     * @param int     $width
     * @param Closure $callback
     *
     * @return $this
     */
    public function block(int $width, \Closure $callback)
    {
        $this
            ->builder
            ->layout()
            ->block($width, $callback);

        return $this;
    }

    /**
     * @param int|float $width
     * @param Closure   $callback
     *
     * @return $this
     */
    public function column($width, \Closure $callback)
    {
        $this->builder->layout()->onlyColumn($width, function () use ($callback) {
            $callback($this);
        });

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
     * @param Closure $callback
     *
     * @return bool|void
     */
    public function inDialog(\Closure $callback = null)
    {
        if (! $callback) {
            return DialogForm::is();
        }

        if (DialogForm::is()) {
            $callback($this);
        }
    }

    /**
     * Create a dialog form.
     *
     * @param string|null $title
     *
     * @return DialogForm
     */
    public static function dialog(?string $title = null)
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
    public static function extensions()
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
     * @param mixed  $value
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
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $arguments);
        }

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
