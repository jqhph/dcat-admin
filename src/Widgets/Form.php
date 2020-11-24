<?php

namespace Dcat\Admin\Widgets;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Form\Concerns\HandleCascadeFields;
use Dcat\Admin\Form\Concerns\HasRows;
use Dcat\Admin\Form\Concerns\HasTabs;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Layout;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasAuthorization;
use Dcat\Admin\Traits\HasFormResponse;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Validation\Validator;

/**
 * Class Form.
 *
 * @method Field\Text                text($column, $label = '')
 * @method Field\Checkbox            checkbox($column, $label = '')
 * @method Field\Radio               radio($column, $label = '')
 * @method Field\Select              select($column, $label = '')
 * @method Field\MultipleSelect      multipleSelect($column, $label = '')
 * @method Field\Textarea            textarea($column, $label = '')
 * @method Field\Hidden              hidden($column, $label = '')
 * @method Field\Id                  id($column, $label = '')
 * @method Field\Ip                  ip($column, $label = '')
 * @method Field\Url                 url($column, $label = '')
 * @method Field\Email               email($column, $label = '')
 * @method Field\Mobile              mobile($column, $label = '')
 * @method Field\Slider              slider($column, $label = '')
 * @method Field\Map                 map($latitude, $longitude, $label = '')
 * @method Field\Editor              editor($column, $label = '')
 * @method Field\Date                date($column, $label = '')
 * @method Field\Datetime            datetime($column, $label = '')
 * @method Field\Time                time($column, $label = '')
 * @method Field\Year                year($column, $label = '')
 * @method Field\Month               month($column, $label = '')
 * @method Field\DateRange           dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange       datetimeRange($start, $end, $label = '')
 * @method Field\TimeRange           timeRange($start, $end, $label = '')
 * @method Field\Number              number($column, $label = '')
 * @method Field\Currency            currency($column, $label = '')
 * @method Field\SwitchField         switch($column, $label = '')
 * @method Field\Display             display($column, $label = '')
 * @method Field\Rate                rate($column, $label = '')
 * @method Field\Divide              divider()
 * @method Field\Password            password($column, $label = '')
 * @method Field\Decimal             decimal($column, $label = '')
 * @method Field\Html                html($html, $label = '')
 * @method Field\Tags                tags($column, $label = '')
 * @method Field\Icon                icon($column, $label = '')
 * @method Field\Embeds              embeds($column, $label = '')
 * @method Field\Captcha             captcha($column, $label = '')
 * @method Field\Listbox             listbox($column, $label = '')
 * @method Field\SelectResource      selectResource($column, $label = '')
 * @method Field\File                file($column, $label = '')
 * @method Field\Image               image($column, $label = '')
 * @method Field\MultipleFile        multipleFile($column, $label = '')
 * @method Field\MultipleImage       multipleImage($column, $label = '')
 * @method Field\HasMany             hasMany($column, \Closure $callback)
 * @method Field\Tree                tree($column, $label = '')
 * @method Field\Table               table($column, $callback)
 * @method Field\ListField           list($column, $label = '')
 * @method Field\Timezone            timezone($column, $label = '')
 * @method Field\KeyValue            keyValue($column, $label = '')
 * @method Field\Tel                 tel($column, $label = '')
 * @method Field\Markdown            markdown($column, $label = '')
 * @method Field\Range               range($start, $end, $label = '')
 * @method Field\Color               color($column, $label = '')
 * @method Field\ArrayField          array($column, $labelOrCallback, $callback = null)
 * @method Field\SelectTable         selectTable($column, $label = '')
 * @method Field\MultipleSelectTable multipleSelectTable($column, $label = '')
 * @method Field\Button              button(string $html = null)
 */
class Form implements Renderable
{
    use HasHtmlAttributes;
    use HasAuthorization;
    use HandleCascadeFields;
    use HasRows;
    use HasTabs;
    use HasFormResponse {
        setCurrentUrl as defaultSetCurrentUrl;
    }
    use Macroable {
        __call as macroCall;
    }

    const REQUEST_NAME = '_form_';
    const CURRENT_URL_NAME = '_current_';
    const LAZY_PAYLOAD_NAME = '_payload_';

    /**
     * @var string
     */
    protected $view = 'admin::widgets.form';

    /**
     * @var Field[]|Collection
     */
    protected $fields;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var bool
     */
    protected $useAjaxSubmit = true;

    /**
     * @var Fluent
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $primaryKey;

    /**
     * Available buttons.
     *
     * @var array
     */
    protected $buttons = ['reset' => true, 'submit' => true];

    /**
     * @var bool
     */
    protected $useFormTag = true;

    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var array
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * @var array
     */
    protected $confirm = [];

    protected $hasColumns = false;

    /**
     * Form constructor.
     *
     * @param array $data
     * @param mixed $key
     */
    public function __construct($data = [], $key = null)
    {
        if ($data) {
            $this->fill($data);
        }
        $this->setKey($key);

        $this->setUp();
    }

    protected function setUp()
    {
        $this->initFields();

        $this->initFormAttributes();

        $this->initCurrentUrl();

        $this->initPayload();
    }

    /**
     * Initialize the form fields.
     */
    protected function initFields()
    {
        $this->fields = new Collection();
    }

    /**
     * Initialize the form attributes.
     */
    protected function initFormAttributes()
    {
        $this->setHtmlAttribute([
            'method'         => 'POST',
            'action'         => '',
            'class'          => 'form-horizontal',
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
        ]);
    }

    protected function initCurrentUrl()
    {
        if ($this instanceof LazyRenderable) {
            $this->setCurrentUrl($this->getCurrentUrl());
        }
    }

    protected function initPayload()
    {
        if ($payload = \request(static::LAZY_PAYLOAD_NAME)) {
            $this->payload(json_decode($payload, true) ?? []);
        }
    }

    /**
     * Action uri of the form.
     *
     * @param string $action
     *
     * @return $this|string
     */
    public function action($action = null)
    {
        if ($action === null) {
            return $this->getHtmlAttribute('action');
        }

        return $this->setHtmlAttribute('action', admin_url($action));
    }

    /**
     * Method of the form.
     *
     * @param string $method
     *
     * @return $this
     */
    public function method(string $method = 'POST')
    {
        return $this->setHtmlAttribute('method', strtoupper($method));
    }

    /**
     * @param string $title
     * @param string $content
     *
     * @return $this
     */
    public function confirm(?string $title = null, ?string $content = null)
    {
        $this->confirm['title'] = $title;
        $this->confirm['content'] = $content;

        return $this;
    }

    /**
     * Set primary key.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setKey($value)
    {
        $this->primaryKey = $value;

        return $this;
    }

    /**
     * Get primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param array|Arrayable|Closure $data
     *
     * @return Fluent
     */
    public function data()
    {
        if (! $this->data) {
            $this->fill([]);
        }

        return $this->data;
    }

    /**
     * @param array|Arrayable|Closure $data
     *
     * @return $this
     */
    public function fill($data)
    {
        $this->data = new Fluent(Helper::array($data));

        return $this;
    }

    /**
     * @return Fluent
     */
    public function model()
    {
        return $this->data();
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
     * Get specify field.
     *
     * @param string|Field $name
     *
     * @return Field|null
     */
    public function field($name)
    {
        foreach ($this->fields as $field) {
            if (is_array($field->column())) {
                return in_array($name, $field->column(), true) ? $field : null;
            }

            if ($field === $name || $field->column() === $name) {
                return $field;
            }
        }
    }

    /**
     * @return Field[]|Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @param int|float $width
     * @param Closure   $callback
     *
     * @return $this
     */
    public function column($width, \Closure $callback)
    {
        $this->layout()->onlyColumn($width, function () use ($callback) {
            $callback($this);
        });

        $this->hasColumns = true;

        return $this;
    }

    /**
     * @return Layout
     */
    public function layout()
    {
        return $this->layout ?: ($this->layout = new Layout($this));
    }

    /**
     * Validate this form fields.
     *
     * @param Request $request
     *
     * @return bool|MessageBag
     */
    public function validate(Request $request)
    {
        $failedValidators = [];

        /** @var \Dcat\Admin\Form\Field $field */
        foreach ($this->fields() as $field) {
            if (! $validator = $field->getValidator($request->all())) {
                continue;
            }

            if (($validator instanceof Validator) && ! $validator->passes()) {
                $failedValidators[] = $validator;
            }
        }

        $message = $this->mergeValidationMessages($failedValidators);

        return $message->any() ? $message : false;
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

        return $messageBag;
    }

    /**
     * Disable Pjax.
     *
     * @return $this
     */
    public function disablePjax()
    {
        $this->forgetHtmlAttribute('pjax-container');

        return $this;
    }

    /**
     * Disable form tag.
     *
     * @return $this;
     */
    public function disableFormTag()
    {
        $this->useFormTag = false;

        return $this;
    }

    /**
     * Disable reset button.
     *
     * @return $this
     */
    public function disableResetButton()
    {
        $this->buttons['reset'] = false;

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmitButton()
    {
        $this->buttons['submit'] = false;

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
        $this->width = [
            'label' => $labelWidth,
            'field' => $fieldWidth,
        ];

        $this->fields->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field  */
            $field->width($fieldWidth, $labelWidth);
        });

        return $this;
    }

    /**
     * Find field class with given name.
     *
     * @param string $method
     *
     * @return bool|string
     */
    public static function findFieldClass($method)
    {
        $class = Arr::get(\Dcat\Admin\Form::extensions(), $method);

        if (class_exists($class)) {
            return $class;
        }

        return false;
    }

    /**
     * Add a form field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field $field)
    {
        $this->fields->push($field);
        if ($this->layout) {
            $this->layout->addField($field);
        }

        $field->setForm($this);
        $field->width($this->width['field'], $this->width['label']);

        $this->setFileUploadUrl($field);

        $field::collectAssets();

        return $this;
    }

    protected function setFileUploadUrl(Field $field)
    {
        if ($field instanceof Field\File && method_exists($this, 'form')) {
            $formData = [static::REQUEST_NAME => get_called_class()];

            $field->url(route(admin_api_route('form.upload')));
            $field->deleteUrl(route(admin_api_route('form.destroy-file'), $formData));
            $field->withFormData($formData);
        }
    }

    /**
     * Get variables for render form.
     *
     * @return array
     */
    protected function variables()
    {
        $this->setHtmlAttribute('id', $this->getElementId());

        $this->fillFields($this->model()->toArray());

        return array_merge([
            'start'   => $this->open(),
            'end'     => $this->close(),
            'fields'  => $this->fields,
            'method'  => $this->getHtmlAttribute('method'),
            'buttons' => $this->buttons,
            'rows'    => $this->rows(),
            'layout'  => $this->layout,
        ], $this->variables);
    }

    public function addVariables(array $variables)
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }

    public function fillFields(array $data)
    {
        foreach ($this->fields as $field) {
            $field->fill($data);
        }
    }

    /**
     * @return string
     */
    protected function open()
    {
        return <<<HTML
<form {$this->formatHtmlAttributes()}>
HTML;
    }

    /**
     * @return string
     */
    protected function close()
    {
        return '</form>';
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field\File) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setFormId($id)
    {
        $this->elementId = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getElementId()
    {
        return $this->elementId ?: ($this->elementId = 'form-'.Str::random(8));
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentUrl($url)
    {
        if ($this instanceof LazyRenderable) {
            $this->payload([static::CURRENT_URL_NAME => $url]);
        }

        return $this->defaultSetCurrentUrl($url);
    }

    /**
     * Disable submit with ajax.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableAjaxSubmit(bool $disable = true)
    {
        $this->useAjaxSubmit = ! $disable;

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
     * @return void
     */
    protected function setUpSubmitScript()
    {
        $confirm = json_encode($this->confirm);

        Admin::script(
            <<<JS
$('#{$this->getElementId()}').form({
    validate: true,
    confirm: {$confirm},
    success: function (data) {
        {$this->buildSuccessScript()}
        {$this->addSavedScript()}
    },
    error: function (response) {
        {$this->buildErrorScript()}
        {$this->addErrorScript()}
    }
});
JS
        );
    }

    /**
     * @return string|void
     *
     * @deprecated 将在2.0版本中废弃，请用 addSavedScript 代替
     */
    protected function buildSuccessScript()
    {
    }

    /**
     * @return string|void
     */
    protected function addSavedScript()
    {
    }

    /**
     * @return string|void
     *
     * @deprecated 将在2.0版本中废弃，请用 addErrorScript 代替
     */
    protected function buildErrorScript()
    {
    }

    /**
     * @return string|void
     */
    protected function addErrorScript()
    {
    }

    /**
     * @param array $input
     *
     * @return array
     */
    public function sanitize(array $input)
    {
        Arr::forget($input, [static::REQUEST_NAME, '_token', static::CURRENT_URL_NAME]);

        return $this->prepareInput($input);
    }

    public function prepareInput(array $input)
    {
        Helper::prepareHasOneRelation($this->fields, $input);

        foreach ($input as $column => $value) {
            $field = $this->field($column);

            if (! $field instanceof Field) {
                unset($input[$column]);

                continue;
            }

            $input[$column] = $field->prepare($value);
        }

        $prepared = [];

        foreach ($input as $key => $value) {
            Arr::set($prepared, $key, $value);
        }

        return $prepared;
    }

    protected function prepareForm()
    {
        if (method_exists($this, 'form')) {
            $this->form();
        }

        if (! $this->data && method_exists($this, 'default')) {
            $data = $this->default();

            if (is_array($data)) {
                $this->fill($data);
            }
        }
    }

    protected function prepareHandler()
    {
        if (method_exists($this, 'handle')) {
            $addHiddenFields = function () {
                $this->method('POST');
                $this->action(route(admin_api_route('form')));
                $this->hidden(static::REQUEST_NAME)->default(get_called_class());
                $this->hidden(static::CURRENT_URL_NAME)->default($this->getCurrentUrl());

                if (! empty($this->payload) && is_array($this->payload)) {
                    $this->hidden(static::LAZY_PAYLOAD_NAME)->default(json_encode($this->payload));
                }
            };

            $this->hasColumns ? $this->column(1, $addHiddenFields) : $addHiddenFields();
        }
    }

    /**
     * Render the form.
     *
     * @return string
     */
    public function render()
    {
        $this->prepareForm();

        $this->prepareHandler();

        if ($this->allowAjaxSubmit()) {
            $this->setUpSubmitScript();
        }

        $tabObj = $this->getTab();

        if (! $tabObj->isEmpty()) {
            $tabObj->addScript();
        }

        $this->addVariables(['tabObj' => $tabObj]);

        return view($this->view, $this->variables())->render();
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Field|null
     */
    public function __call($method, $arguments)
    {
        if ($className = static::findFieldClass($method)) {
            $name = Arr::get($arguments, 0, '');

            $element = new $className($name, array_slice($arguments, 1));

            $this->pushField($element);

            return $element;
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $arguments);
        }

        throw new \BadMethodCallException("Field [{$method}] does not exist.");
    }

    /**
     * Output as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }
}
