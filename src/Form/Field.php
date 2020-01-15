<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
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
    use Macroable, Form\Concerns\HasFieldValidator;

    const FILE_DELETE_FLAG = '_file_del_';

    /**
     * Element id.
     *
     * @var array|string
     */
    protected $id;

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
     * @var string
     */
    protected $elementName = [];

    /**
     * Form element classes.
     *
     * @var array
     */
    protected $elementClass = [];

    /**
     * Variables of elements.
     *
     * @var array
     */
    protected $variables = [];

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
    protected $form = null;

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
     * @var mixed
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
    protected $labelClass = [];

    /**
     * @var \Closure
     */
    protected $prepareCallback;

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
        $this->id = $this->formatId($column);
    }

    /**
     * Get the field element id.
     *
     * @return string|array
     */
    public function elementId()
    {
        return $this->id;
    }

    /**
     * Format the field element id.
     *
     * @param string|array $column
     *
     * @return string|array
     */
    protected function formatId($column)
    {
        $random = Str::random(5);

        if (is_array($column)) {
            $id = [];

            foreach (str_replace('.', '-', $column) as $k => $v) {
                $id[$k] = "{$v}-{$random}";
            }

            return $id;
        }

        return 'form-field-'.str_replace('.', '-', $column).'-'.$random;
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
        $column = is_array($this->column) ? current($this->column) : $this->column;

        $label = isset($arguments[0]) ? $arguments[0] : ucfirst(admin_trans_field($column));

        return str_replace(['.', '_'], ' ', $label);
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
        if (is_string($column)) {
            $name = explode('.', $column);

            if (count($name) == 1) {
                return $name[0];
            }

            $html = array_shift($name);
            foreach ($name as $piece) {
                $html .= "[$piece]";
            }

            return $html;
        }

        if (is_array($this->column)) {
            $names = [];
            foreach ($this->column as $key => $name) {
                $names[$key] = $this->formatName($name);
            }

            return $names;
        }

        return '';
    }

    /**
     * Set form element name.
     *
     * @param string $name
     *
     * @return $this|string
     *
     * @author Edwin Hui
     */
    public function elementName($name = null)
    {
        if ($name === null) {
            return $this->elementName ?: $this->formatName($this->column);
        }

        $this->elementName = $name;

        return $this;
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
                $value[$key] = Arr::get($data, $column);
            }

            return $value;
        }

        return Arr::get($data, $this->column, $this->value);
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
     * @return Fluent
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
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        if (is_array($this->options)) {
            $this->options = array_merge($this->options, $options);
        } else {
            $this->options = $options;
        }

        return $this;
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
     * Get or set key for error message.
     *
     * @param string $key
     *
     * @return $this|string
     */
    public function errorKey($key = null)
    {
        if ($key === null) {
            return $this->errorKey ?: $this->column;
        }

        $this->errorKey = $key;

        return $this;
    }

    /**
     * Set or get value of the field.
     *
     * @param null $value
     *
     * @return mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return is_null($this->value) ? $this->default() : $this->value;
        }

        $this->value = $value;

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
            return $this->data ?: ($this->data = new Fluent());
        }

        $this->data = new Fluent($data);

        return $this;
    }

    /**
     * Get or set default value for field.
     *
     * @param $default
     *
     * @return $this
     */
    public function default($default = null)
    {
        if ($default === null) {
            if ($this->default instanceof \Closure) {
                return call_user_func($this->default, $this->form);
            }

            return $this->default;
        }

        $this->default = $default;

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
    public function help($text = '', $icon = 'fa-info-circle')
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

    public function old()
    {
        return old($this->column, $this->value());
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
            $this->labelClass(['asterisk']);
        }

        $this->rules('required');

        return $this->attribute('required', true);
    }

    /**
     * Set the field automatically get focus.
     *
     * @return $this
     */
    public function autofocus()
    {
        return $this->attribute('autofocus', true);
    }

    /**
     * Set the field as readonly mode.
     *
     * @return $this
     */
    public function readOnly()
    {
        return $this->attribute('readonly', true);
    }

    /**
     * Set field as disabled.
     *
     * @return $this
     */
    public function disable()
    {
        return $this->attribute('disabled', true);
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
            return $this->placeholder ?: trans('admin.input').' '.$this->label;
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Prepare for a field value before update or insert.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function prepareToSave($value)
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
        $this->prepareCallback = $closure;

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
        $value = $this->prepareToSave($value);

        if ($handler = $this->prepareCallback) {
            $handler->bindTo($this->data);

            return $handler($value);
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
     * @return $this
     */
    public function disableHorizontal()
    {
        $this->horizontal = false;

        return $this;
    }

    /**
     * @return array
     */
    public function viewElementClasses()
    {
        if ($this->horizontal) {
            return [
                'label'      => "col-sm-{$this->width['label']} {$this->labelClass()}",
                'field'      => "col-sm-{$this->width['field']}",
                'form-group' => 'form-group ',
            ];
        }

        return ['label' => $this->labelClass(), 'field' => '', 'form-group' => ''];
    }

    /**
     * Set form element class.
     *
     * @param string|array $class
     *
     * @return $this|array
     */
    public function elementClass($class = null)
    {
        if ($class === null) {
            if (! $this->elementClass) {
                $name = $this->elementName ?: $this->formatName($this->column);

                $this->elementClass = (array) str_replace(['[', ']'], '_', $name);
            }

            return $this->elementClass;
        }

        $this->elementClass = array_merge($this->elementClass, (array) $class);

        return $this;
    }

    /**
     * Get element class string.
     *
     * @return mixed
     */
    protected function elementClassString()
    {
        $elementClass = $this->elementClass();

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
     * Get element class selector.
     *
     * @return string|array
     */
    protected function elementClassSelector()
    {
        $elementClass = $this->elementClass();

        $formId = $this->formElementId();
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
     * Remove the field in modal.
     *
     * @return $this
     */
    public function hideInModal()
    {
        if (
            $this->form instanceof Form
            && $this->form->inModal()
        ) {
            $this->display(false);
        }

        return $this;
    }

    /**
     * @return string|null
     */
    protected function formElementId()
    {
        return $this->form ? $this->form->elementId() : null;
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
        $delClass = [];

        if (is_string($class) || is_array($class)) {
            $delClass = (array) $class;
        }

        foreach ($delClass as $del) {
            if (($key = array_search($del, $this->elementClass))) {
                unset($this->elementClass[$key]);
            }
        }

        return $this;
    }

    /**
     * Add variables to field view.
     *
     * @param array $variables
     *
     * @return $this
     */
    protected function addVariables(array $variables = [])
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }

    /**
     * @param array|string $labelClass
     * @param bool         $append
     *
     * @return $this|string
     */
    public function labelClass($labelClass = null, bool $append = true)
    {
        if ($labelClass === null) {
            return implode(' ', $this->labelClass);
        }

        $this->labelClass = $append
            ? array_unique(array_merge($this->labelClass, (array) $labelClass))
            : (array) $labelClass;

        return $this;
    }

    /**
     * Get the view variables of this field.
     *
     * @return array
     */
    public function variables()
    {
        return array_merge($this->variables, [
            'id'          => $this->id,
            'name'        => $this->elementName(),
            'help'        => $this->help,
            'class'       => $this->elementClassString(),
            'value'       => $this->value(),
            'label'       => $this->label,
            'viewClass'   => $this->viewElementClasses(),
            'column'      => $this->column,
            'errorKey'    => $this->errorKey(),
            'attributes'  => $this->formatAttributes(),
            'placeholder' => $this->placeholder(),
            'disabled'    => $this->attributes['disabled'] ?? false,
            'formId'      => $this->formElementId(),
        ]);
    }

    /**
     * Get view of this field.
     *
     * @return string
     */
    public function getView()
    {
        return $this->view ?: 'admin::form.'.strtolower(class_basename(static::class));
    }

    /**
     * Set view of current field.
     *
     * @return string
     */
    public function view($view)
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
     * If this field should render.
     *
     * @return bool
     */
    protected function shouldRender()
    {
        return $this->display;
    }

    /**
     * Collect assets required by this field.
     */
    public static function collectAssets()
    {
        static::$js && Admin::js(static::$js);
        static::$css && Admin::css(static::$css);
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

        Admin::script($this->script);

        return view($this->getView(), $this->variables());
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
