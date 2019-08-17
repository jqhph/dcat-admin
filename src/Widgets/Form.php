<?php

namespace Dcat\Admin\Widgets;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
 * @method Field\ListField      list($column, $label = '')
 * @method Field\Timezone       timezone($column, $label = '')
 * @method Field\KeyValue       keyValue($column, $label = '')
 *
 * @method Field\BootstrapFile          bootstrapFile($column, $label = '')
 * @method Field\BootstrapImage         bootstrapImage($column, $label = '')
 * @method Field\BootstrapMultipleImage bootstrapMultipleImage($column, $label = '')
 * @method Field\BootstrapMultipleFile  bootstrapMultipleFile($column, $label = '')
 */
class Form implements Renderable
{
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var bool
     */
    protected $useAjaxSubmit = true;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Available buttons.
     *
     * @var array
     */
    protected $buttons = ['reset', 'submit'];

    /**
     * @var bool
     */
    protected $useFormTag = true;

    /**
     * @var string
     */
    protected $formId;

    /**
     * @var array
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * Form constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (!empty($data)) {
            $this->data = $data;
        }

        $this->initFormAttributes();
    }

    /**
     * Initialize the form attributes.
     */
    protected function initFormAttributes()
    {
        $this->attributes = [
            'method'         => 'POST',
            'action'         => '',
            'class'          => 'form-horizontal',
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
        ];
    }

    /**
     * Action uri of the form.
     *
     * @param string $action
     *
     * @return $this
     */
    public function action($action)
    {
        return $this->attribute('action', $action);
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->attributes['action'];
    }

    /**
     * Method of the form.
     *
     * @param string $method
     *
     * @return $this
     */
    public function method($method = 'POST')
    {
        return $this->attribute('method', strtoupper($method));
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
     * Add form attributes.
     *
     * @param string|array $attr
     * @param string       $value
     *
     * @return $this
     */
    public function attribute($attr, $value = '')
    {
        if (is_array($attr)) {
            foreach ($attr as $key => $value) {
                $this->attribute($key, $value);
            }
        } else {
            $this->attributes[$attr] = $value;
        }

        return $this;
    }

    /**
     * Disable Pjax.
     *
     * @return $this
     */
    public function disablePjax()
    {
        Arr::forget($this->attributes, 'pjax-container');

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
        array_delete($this->buttons, 'reset');

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmitButton()
    {
        array_delete($this->buttons, 'submit');

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
    public function setWidth($fieldWidth = 8, $labelWidth = 2)
    {
        $this->width = [
            'label' => $labelWidth,
            'field' => $fieldWidth,
        ];

        collect($this->fields)->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field  */
            $field->setWidth($fieldWidth, $labelWidth);
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
        $class = Arr::get(\Dcat\Admin\Form::getExtensions(), $method);

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
    public function pushField(Field &$field)
    {
        array_push($this->fields, $field);

        $field->setWidth($this->width['field'], $this->width['label']);

        $field::collectAssets();

        return $this;
    }

    /**
     * Get variables for render form.
     *
     * @return array
     */
    protected function getVariables()
    {
        foreach ($this->fields as $field) {
            $field->fill($this->data);
        }

        return [
            'fields'     => $this->fields,
            'attributes' => $this->formatAttribute(),
            'method'     => $this->attributes['method'],
            'buttons'    => $this->buttons,
        ];
    }

    /**
     * Format form attributes form array to html.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function formatAttribute($attributes = [])
    {
        $attributes = $attributes ?: $this->attributes;

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $key => $val) {
            $html[] = "$key=\"$val\"";
        }

        return implode(' ', $html) ?: '';
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
     * @return $this
     */
    public function setFormId($id)
    {
        $this->formId = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->formId ?: ($this->formId = 'form-'.Str::random(8));
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

    protected function setupSubmitScript()
    {
        Admin::script(
            <<<JS
var f = $('#{$this->getFormId()}');

f.find('[type="submit"]').click(function () {
    var t = $(this);
    t.button('loading');
    
    LA.Form({
        \$form: f,
        after: function () {
            t.button('reset');
        }
    });

    return false;
});
JS

        );
    }

    /**
     * Render the form.
     *
     * @return string
     */
    public function render()
    {
        $this->useAjaxSubmit && $this->setupSubmitScript();

        return view('admin::widgets.form', $this->getVariables())->render();
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
}
