<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Traits\HasVariables;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Field.
 */
class Field implements Renderable
{
    use Macroable;
    use Form\Concerns\HasFieldValidator;
    use HasBuilderEvents;
    use HasVariables;

    const FILE_DELETE_FLAG = '_file_del_';

    const FIELD_CLASS_PREFIX = 'field_';

    const BUILD_IGNORE = 'build-ignore';

    const NORMAL_CLASS = '_normal_';

    /**
     * Element value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Data of all original columns of value.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Field original value.
     *
     * @var mixed
     */
    protected $original;

    /**
     * Field default value.
     *
     * @var mixed
     */
    protected $default;

    /**
     * @var bool
     */
    protected $allowDefaultValueInEditPage = false;

    /**
     * Element label.
     *
     * @var string
     */
    protected $label = '';

    /**
     * Column name.
     *
     * @var string|array
     */
    protected $column = '';

    /**
     * Form element name.
     *
     * @var string|array
     */
    protected $elementName = [];

    /**
     * Form element classes.
     *
     * @var array
     */
    protected $elementClass = [];

    /**
     * Options for specify elements.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Checked for specify elements.
     *
     * @var array
     */
    protected $checked = [];

    /**
     * Css required by this field.
     *
     * @var array
     */
    protected static $css = [];

    /**
     * Js required by this field.
     *
     * @var array
     */
    protected static $js = [];

    /**
     * Script for field.
     *
     * @var string
     */
    protected $script = '';

    /**
     * Element attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Parent form.
     *
     * @var Form|WidgetForm
     */
    protected $form;

    /**
     * @var WidgetForm
     */
    protected $parent;

    /**
     * View for field to render.
     *
     * @var string
     */
    protected $view = '';

    /**
     * Help block.
     *
     * @var array
     */
    protected $help = [];

    /**
     * Key for errors.
     *
     * @var string|array
     */
    protected $errorKey;

    /**
     * Placeholder for this field.
     *
     * @var string|array
     */
    protected $placeholder;

    /**
     * Width for label and field.
     *
     * @var array
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * If the form horizontal layout.
     *
     * @var bool
     */
    protected $horizontal = true;

    /**
     * column data format.
     *
     * @var \Closure
     */
    protected $customFormat = null;

    /**
     * @var bool
     */
    protected $display = true;

    /**
     * @var array
     */
    protected $labelClass = ['text-capitalize'];

    /**
     * @var array
     */
    protected $fieldClass = [];

    /**
     * @var array
     */
    protected $formGroupClass = ['form-field'];

    /**
     * @var \Closure[]
     */
    protected $savingCallbacks = [];

    /**
     * Field constructor.
     *
     * @param string|array $column
     * @param array        $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->column = $column;
        $this->label = $this->formatLabel($arguments);

        $this->callResolving();
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setRelation(array $options = [])
    {
        return $this;
    }

    /**
     * Format the label value.
     *
     * @param array $arguments
     *
     * @return string
     */
    protected function formatLabel($arguments = [])
    {
        if (isset($arguments[0])) {
            return $arguments[0];
        }

        $column = is_array($this->column) ? current($this->column) : $this->column;

        return str_replace('_', ' ', admin_trans_field($column));
    }

    /**
     * Format the name of the field.
     *
     * @param string $column
     *
     * @return array|mixed|string
     */
    protected function formatName($column)
    {
        return Helper::formatElementName($column);
    }

    /**
     * Set form element name.
     *
     * @param string|array $name
     *
     * @return $this
     *
     * @author Edwin Hui
     */
    public function setElementName($name)
    {
        $this->elementName = $name;

        return $this;
    }

    /**
     * Get form element name.
     *
     * @return array|mixed|string
     */
    public function getElementName()
    {
        return $this->elementName ?: $this->formatName($this->column);
    }

    /**
     * Fill data to the field.
     *
     * @param array $data
     *
     * @return void
     */
    final public function fill($data)
    {
        $data = Helper::array($data);

        $this->data($data);

        $this->value = $this->formatFieldData($data);

        $this->callCustomFormatter();
    }

    /**
     * Format field data.
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function formatFieldData($data)
    {
        if (is_array($this->column)) {
            $value = [];

            foreach ($this->column as $key => $column) {
                $value[$key] = $this->getValueFromData($data, $this->normalizeColumn($column));
            }

            return $value;
        }

        return $this->getValueFromData($data, null, $this->value);
    }

    /**
     * @param array $data
     * @param string $column
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getValueFromData($data, $column = null, $default = null)
    {
        $column = $column ?: $this->normalizeColumn();

        if (Arr::has($data, $column)) {
            return Arr::get($data, $column, $default);
        }

        return Arr::get($data, Str::snake($column), $default);
    }

    protected function normalizeColumn(?string $column = null)
    {
        return str_replace('->', '.', $column ?: $this->column);
    }

    /**
     * custom format form column data when edit.
     *
     * @param \Closure $call
     *
     * @return $this
     */
    public function customFormat(\Closure $call)
    {
        $this->customFormat = $call;

        return $this;
    }

    /**
     * Set original value to the field.
     *
     * @param array $data
     *
     * @return void
     */
    final public function setOriginal($data)
    {
        $data = Helper::array($data);

        $this->original = $this->formatFieldData($data);

        $this->callCustomFormatter('original', new Fluent($data));
    }

    /**
     * @param string      $key
     * @param Fluent|null $dataremoveField
     */
    protected function callCustomFormatter($key = 'value', Fluent $data = null)
    {
        if ($this->customFormat) {
            $this->{$key} = $this->customFormat
                ->call(
                    $data ?: $this->data(),
                    $this->{$key},
                    $this->column,
                    $this
                );
        }
    }

    /**
     * @param Form|WidgetForm $form
     *
     * @return $this
     */
    public function setForm($form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @param WidgetForm $form
     *
     * @return $this
     */
    public function setParent($form = null)
    {
        $this->parent = $form;

        return $this;
    }

    /**
     * @return Fluent|\Illuminate\Database\Eloquent\Model
     */
    public function values()
    {
        return $this->form ? $this->form->model() : new Fluent();
    }

    /**
     * Set width for field and label.
     *
     * @param int $field
     * @param int $label
     *
     * @return $this
     */
    public function width($field = 8, $label = 2)
    {
        $this->width = [
            'label' => $label,
            'field' => $field,
        ];

        return $this;
    }

    /**
     * Set the field options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this->data(), $this->value());
        }

        $this->options = array_merge($this->options, Helper::array($options));

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function replaceOptions($options)
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this->data(), $this->value());
        }

        $this->options = $options;

        return $this;
    }

    /**
     * @param array|Arrayable $options
     *
     * @return $this
     */
    public function mergeOptions($options)
    {
        return $this->options($options);
    }

    /**
     * Set the field option checked.
     *
     * @param array $checked
     *
     * @return $this
     */
    public function checked($checked = [])
    {
        if ($checked instanceof Arrayable) {
            $checked = $checked->toArray();
        }

        $this->checked = array_merge($this->checked, (array) $checked);

        return $this;
    }

    /**
     * Set key for error message.
     *
     * @param string|array $key
     *
     * @return $this
     */
    public function setErrorKey($key)
    {
        $this->errorKey = $key;

        return $this;
    }

    /**
     * Get key for error message.
     *
     * @return string
     */
    public function getErrorKey()
    {
        return $this->errorKey ?: $this->column;
    }

    /**
     * Set or get value of the field.
     *
     * @param null $value
     *
     * @return mixed|$this
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            if (
                $this->value === null
                || (is_array($this->value) && empty($this->value))
            ) {
                return $this->default();
            }

            return $this->value;
        }

        $this->value = value($value);

        return $this;
    }

    /**
     * Set or get data.
     *
     * @param array $data
     *
     * @return $this|Fluent
     */
    public function data(array $data = null)
    {
        if (is_null($data)) {
            if (! $this->data || is_array($this->data)) {
                $this->data = new Fluent((array) $this->data);
            }

            return $this->data;
        }

        $this->data = new Fluent($data);

        return $this;
    }

    /**
     * Get or set default value for field.
     *
     * @param mixed $default
     * @param bool  $edit
     *
     * @return $this|mixed
     */
    public function default($default = null, bool $edit = false)
    {
        if ($default === null) {
            if (
                $this->form
                && method_exists($this->form, 'isCreating')
                && ! $this->form->isCreating()
                && ! $this->allowDefaultValueInEditPage
            ) {
                return;
            }

            if ($this->default instanceof \Closure) {
                $this->default->bindTo($this->data());

                return call_user_func($this->default, $this->form);
            }

            return $this->default;
        }

        $this->default = value($default);
        $this->allowDefaultValueInEditPage = $edit;

        return $this;
    }

    /**
     * Set help block for current field.
     *
     * @param string $text
     * @param string $icon
     *
     * @return $this
     */
    public function help($text = '', $icon = 'feather icon-help-circle')
    {
        $this->help = compact('text', 'icon');

        return $this;
    }

    /**
     * Get column of the field.
     *
     * @return string|array
     */
    public function column()
    {
        return $this->column;
    }

    /**
     * Get or set label of the field.
     *
     * @param null $label
     *
     * @return $this|string
     */
    public function label($label = null)
    {
        if ($label == null) {
            return $this->label;
        }

        if ($label instanceof \Closure) {
            $label = $label($this->label);
        }

        $this->label = $label;

        return $this;
    }

    /**
     * Get original value of the field.
     *
     * @return mixed
     */
    public function original()
    {
        return $this->original;
    }

    /**
     * Sanitize input data.
     *
     * @param array  $input
     * @param string $column
     *
     * @return array
     */
    protected function sanitizeInput($input, $column)
    {
        if ($this instanceof Field\MultipleSelect) {
            $value = Arr::get($input, $column);
            Arr::set($input, $column, array_filter($value));
        }

        return $input;
    }

    /**
     * Add html attributes to elements.
     *
     * @param array|string $attribute
     * @param mixed        $value
     *
     * @return $this
     */
    public function attribute($attribute, $value = null)
    {
        if (is_array($attribute)) {
            $this->attributes = array_merge($this->attributes, $attribute);
        } else {
            $this->attributes[$attribute] = (string) $value;
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute(string $key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Specifies a regular expression against which to validate the value of the input.
     *
     * @param string $error
     * @param string $regexp
     *
     * @return $this
     */
    public function pattern($regexp, $error = null)
    {
        if ($error) {
            $this->attribute('data-pattern-error', $error);
        }

        return $this->attribute('pattern', $regexp);
    }

    /**
     * set the input filed required.
     *
     * @param bool $isLabelAsterisked
     *
     * @return $this
     */
    public function required($isLabelAsterisked = true)
    {
        if ($isLabelAsterisked) {
            $this->setLabelClass(['asterisk']);
        }

        $this->rules('required');

        return $this->attribute('required', true);
    }

    /**
     * Set the field automatically get focus.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function autofocus(bool $value = true)
    {
        return $this->attribute('autofocus', $value);
    }

    /**
     * Set the field as readonly mode.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function readOnly(bool $value = true)
    {
        if (! $value) {
            unset($this->attributes['readonly']);

            return $this;
        }

        return $this->attribute('readonly', $value);
    }

    /**
     * Set field as disabled.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function disable(bool $value = true)
    {
        if (! $value) {
            unset($this->attributes['disabled']);

            return $this;
        }

        return $this->attribute('disabled', $value);
    }

    /**
     * Get or set field placeholder.
     *
     * @param string $placeholder
     *
     * @return $this|string
     */
    public function placeholder($placeholder = null)
    {
        if ($placeholder === null) {
            return $this->placeholder ?: $this->defaultPlaceholder();
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return string
     */
    protected function defaultPlaceholder()
    {
        return trans('admin.input').' '.$this->label;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function prepareInputValue($value)
    {
        return $value;
    }

    /**
     * @param \Closure $closure
     *
     * @return $this
     */
    public function saving(\Closure $closure)
    {
        $this->savingCallbacks[] = $closure;

        return $this;
    }

    /**
     * Prepare for a field value before update or insert.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    final public function prepare($value)
    {
        $value = $this->prepareInputValue($value);

        if ($this->savingCallbacks) {
            foreach ($this->savingCallbacks as $callback) {
                $value = $callback->call($this->data(), $value);
            }
        }

        return $value;
    }

    /**
     * Format the field attributes.
     *
     * @return string
     */
    protected function formatAttributes()
    {
        $html = [];

        foreach ($this->attributes as $name => $value) {
            $html[] = $name.'="'.e($value).'"';
        }

        return implode(' ', $html);
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function horizontal(bool $value = true)
    {
        $this->horizontal = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getViewElementClasses()
    {
        if ($this->horizontal) {
            return [
                'label'      => "col-md-{$this->width['label']} {$this->getLabelClass()}",
                'field'      => "col-md-{$this->width['field']} {$this->getFieldClass()}",
                'form-group' => "form-group row {$this->getFormGroupClass()}",
            ];
        }

        return [
            'label'      => $this->getLabelClass(),
            'field'      => $this->getFieldClass(),
            'form-group' => $this->getFormGroupClass(),
        ];
    }

    /**
     * Set element class.
     *
     * @param string|array $class
     *
     * @return $this
     */
    public function setElementClass($class)
    {
        $this->elementClass = array_merge($this->elementClass, (array) $this->normalizeElementClass($class));

        return $this;
    }

    /**
     * Get element class.
     *
     * @return array
     */
    public function getElementClass()
    {
        if (! $this->elementClass) {
            $this->elementClass = $this->getDefaultElementClass();
        }

        return $this->elementClass;
    }

    /**
     * @return array
     */
    protected function getDefaultElementClass()
    {
        return array_merge($this->normalizeElementClass((array) $this->getElementName()), [static::NORMAL_CLASS]);
    }

    /**
     * @param string|array $class
     *
     * @return array|string
     */
    public function normalizeElementClass($class)
    {
        if (is_array($class)) {
            return array_map([$this, 'normalizeElementClass'], $class);
        }

        return static::FIELD_CLASS_PREFIX.str_replace(['[', ']', '->', '.'], '_', $class);
    }

    /**
     * Get element class selector.
     *
     * @return string|array
     */
    public function getElementClassSelector()
    {
        $elementClass = $this->getElementClass();

        $formId = $this->getFormElementId();
        $formId = $formId ? '#'.$formId : '';

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => $class) {
                $classes[$index] = $formId.' .'.(is_array($class) ? implode('.', $class) : $class);
            }

            return $classes;
        }

        return $formId.' .'.implode('.', $elementClass);
    }

    /**
     * Get element class string.
     *
     * @return mixed
     */
    public function getElementClassString()
    {
        $elementClass = $this->getElementClass();

        if (Arr::isAssoc($elementClass)) {
            $classes = [];

            foreach ($elementClass as $index => $class) {
                $classes[$index] = is_array($class) ? implode(' ', $class) : $class;
            }

            return $classes;
        }

        return implode(' ', $elementClass);
    }

    /**
     * @return $this
     */
    public function hideInDialog()
    {
        if (
            $this->form instanceof Form
            && $this->form->inDialog()
        ) {
            $this->display(false);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    protected function getFormElementId()
    {
        return $this->form ? $this->form->getElementId() : null;
    }

    /**
     * Add the element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function addElementClass($class)
    {
        $this->elementClass = array_unique(
            array_merge($this->elementClass, (array) $class)
        );

        return $this;
    }

    /**
     * Remove element class.
     *
     * @param $class
     *
     * @return $this
     */
    public function removeElementClass($class)
    {
        Helper::deleteByValue($this->elementClass, $class);

        return $this;
    }

    /**
     * @param array|string $labelClass
     * @param bool         $append
     *
     * @return $this|string
     */
    public function setLabelClass($labelClass, bool $append = true)
    {
        $this->labelClass = $append
            ? array_unique(array_merge($this->labelClass, (array) $labelClass))
            : (array) $labelClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelClass()
    {
        return implode(' ', $this->labelClass);
    }

    /**
     * @param  mixed  $value
     * @param  callable  $callback
     *
     * @return $this|mixed
     */
    public function when($value, $callback)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * @param string|array $class
     * @param bool         $append
     *
     * @return $this
     */
    public function setFormGroupClass($class, bool $append = true)
    {
        $this->formGroupClass = $append
            ? array_unique(array_merge($this->formGroupClass, (array) $class))
            : (array) $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormGroupClass()
    {
        return implode(' ', $this->formGroupClass);
    }

    /**
     * @param string|array $class
     * @param bool         $append
     *
     * @return $this
     */
    public function setFieldClass($class, bool $append = true)
    {
        $this->fieldClass = $append
            ? array_unique(array_merge($this->fieldClass, (array) $class))
            : (array) $class;

        return $this;
    }

    public function getFieldClass()
    {
        return implode(' ', $this->fieldClass);
    }

    /**
     * Get the view variables of this field.
     *
     * @return array
     */
    public function defaultVariables()
    {
        return [
            'name'        => $this->getElementName(),
            'help'        => $this->help,
            'class'       => $this->getElementClassString(),
            'value'       => $this->value(),
            'label'       => $this->label,
            'viewClass'   => $this->getViewElementClasses(),
            'column'      => $this->column,
            'errorKey'    => $this->getErrorKey(),
            'attributes'  => $this->formatAttributes(),
            'placeholder' => $this->placeholder(),
            'disabled'    => $this->attributes['disabled'] ?? false,
            'formId'      => $this->getFormElementId(),
            'selector'    => $this->getElementClassSelector(),
            'options'     => $this->options,
        ];
    }

    protected function isCreating()
    {
        return request()->isMethod('POST');
    }

    protected function isEditing()
    {
        return request()->isMethod('PUT');
    }

    /**
     * Get view of this field.
     *
     * @return string
     */
    public function view()
    {
        return $this->view ?: 'admin::form.'.strtolower(class_basename(static::class));
    }

    /**
     * Set view of current field.
     *
     * @return string
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get script of current field.
     *
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set script of current field.
     *
     * @return self
     */
    public function script($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * To set this field should render or not.
     *
     * @return self
     */
    public function display(bool $display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * 设置默认属性.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function defaultAttribute(string $attribute, $value)
    {
        if (! array_key_exists($attribute, $this->attributes)) {
            $this->attribute($attribute, $value);
        }

        return $this;
    }

    /**
     * If this field should render.
     *
     * @return bool
     */
    protected function shouldRender()
    {
        return $this->display;
    }

    /**
     * 保存数据为json格式.
     *
     * @param int $option
     *
     * @return $this
     */
    public function saveAsJson($option = 0)
    {
        return $this->saving(function ($value) use ($option) {
            if ($value === null || is_scalar($value)) {
                return $value;
            }

            return json_encode($value, $option);
        });
    }

    /**
     * 保存数据为字符串格式.
     *
     * @return $this
     */
    public function saveAsString()
    {
        return $this->saving(function ($value) {
            if (is_object($value) || is_array($value)) {
                return json_encode($value);
            }

            return (string) $value;
        });
    }

    /**
     * Collect assets required by this field.
     */
    public static function requireAssets()
    {
        static::$js && Admin::js(static::$js);
        static::$css && Admin::css(static::$css);
    }

    /**
     * 设置默认class.
     */
    protected function setDefaultClass()
    {
        if (is_string($class = $this->getElementClassString())) {
            $this->defaultAttribute('class', $class);
        }
    }

    /**
     * Render this filed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        if (! $this->shouldRender()) {
            return '';
        }

        $this->setDefaultClass();

        $this->callComposing();

        $this->withScript();

        return Admin::view($this->view(), $this->variables());
    }

    protected function withScript()
    {
        if ($this->script) {
            Admin::script($this->script);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $view = $this->render();

        return $view instanceof Renderable ? $view->render() : (string) $view;
    }
}
