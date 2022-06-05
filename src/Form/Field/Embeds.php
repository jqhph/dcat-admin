<?php
/*
 * This file is part of the Dcat Admin package.
 *
 * (c) Pian Zhou <pianzhou2021@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Contracts\FieldsCollection;
use Dcat\Admin\Form;
use Dcat\Admin\Form\Concerns\HasFields;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\ResolveField;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
/**
 * Class Embeds.
 *
 * @method Field\Text text($column, $label = '')
 * @method Field\Checkbox checkbox($column, $label = '')
 * @method Field\Radio radio($column, $label = '')
 * @method Field\Select select($column, $label = '')
 * @method Field\MultipleSelect multipleSelect($column, $label = '')
 * @method Field\Textarea textarea($column, $label = '')
 * @method Field\Hidden hidden($column, $label = '')
 * @method Field\Id id($column, $label = '')
 * @method Field\Ip ip($column, $label = '')
 * @method Field\Url url($column, $label = '')
 * @method Field\Email email($column, $label = '')
 * @method Field\Mobile mobile($column, $label = '')
 * @method Field\Slider slider($column, $label = '')
 * @method Field\Map map($latitude, $longitude, $label = '')
 * @method Field\Editor editor($column, $label = '')
 * @method Field\Date date($column, $label = '')
 * @method Field\Datetime datetime($column, $label = '')
 * @method Field\Time time($column, $label = '')
 * @method Field\Year year($column, $label = '')
 * @method Field\Month month($column, $label = '')
 * @method Field\DateRange dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange datetimeRange($start, $end, $label = '')
 * @method Field\TimeRange timeRange($start, $end, $label = '')
 * @method Field\Number number($column, $label = '')
 * @method Field\Currency currency($column, $label = '')
 * @method Field\SwitchField switch($column, $label = '')
 * @method Field\Display display($column, $label = '')
 * @method Field\Rate rate($column, $label = '')
 * @method Field\Divide divider(string $title = null)
 * @method Field\Password password($column, $label = '')
 * @method Field\Decimal decimal($column, $label = '')
 * @method Field\Html html($html, $label = '')
 * @method Field\Tags tags($column, $label = '')
 * @method Field\Icon icon($column, $label = '')
 * @method Field\Embeds embeds($column, $label = '', Closure $callback = null)
 * @method Field\Captcha captcha()
 * @method Field\Listbox listbox($column, $label = '')
 * @method Field\File file($column, $label = '')
 * @method Field\Image image($column, $label = '')
 * @method Field\MultipleFile multipleFile($column, $label = '')
 * @method Field\MultipleImage multipleImage($column, $label = '')
 * @method Field\HasMany hasMany($column, $labelOrCallback, $callback = null)
 * @method Field\Tree tree($column, $label = '')
 * @method Field\Table table($column, $labelOrCallback, $callback = null)
 * @method Field\ListField list($column, $label = '')
 * @method Field\Timezone timezone($column, $label = '')
 * @method Field\KeyValue keyValue($column, $label = '')
 * @method Field\Tel tel($column, $label = '')
 * @method Field\Markdown markdown($column, $label = '')
 * @method Field\Range range($start, $end, $label = '')
 * @method Field\Color color($column, $label = '')
 * @method Field\ArrayField array($column, $labelOrCallback, $callback = null)
 * @method Field\SelectTable selectTable($column, $label = '')
 * @method Field\MultipleSelectTable multipleSelectTable($column, $label = '')
 * @method Field\Button button(string $html = null)
 * @method Field\Autocomplete autocomplete($column, $label = '')
 */
class Embeds extends Field implements FieldsCollection
{
    use ResolveField;
    use HasFields;

    /**
     * Create a new HasMany field instance.
     *
     * @param  string  $column
     * @param  array  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->column = $column;
        $builder    = null;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $builder = $arguments[0];
        }

        if (count($arguments) == 2) {
            [$this->label, $builder] = $arguments;
        }

        if ($builder instanceof \Closure) {
            call_user_func($builder, $this);
        }
    }

    /**
     * @param  Form|WidgetForm  $form
     * @return $this
     */
    public function setForm($form = null)
    {
        $this->form = $form;
        foreach ($this->fields() as $field) {
            $field->setForm($form);
        }

        return $this;
    }

    /**
     * Fill data to the field.
     *
     * @param  array  $data
     * @return void
     */
    public function fill($data)
    {
        parent::fill($data);

        foreach ($this->fields() as $field) {
            $field->fill($data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValidator(array $input)
    {
        if (! Arr::has($input, $this->column)) {
            return false;
        }

        $rules = $attributes = $messages = [];
        /** @var Field $field */
        foreach ($this->mergedFields() as $field) {
            if (! $fieldRules = $field->getRules()) {
                continue;
            }
            File::deleteRules($field, $fieldRules);
            $column = $field->column();
            if (is_array($column)) {
                $rules[current($column)] = $fieldRules;
            } else {
                $rules[$column] = $fieldRules;
            }

            $attributes[]   = $field->label();
            $messages[] = $field->getValidationMessages();
        }
        if (empty($rules)) {
            return false;
        }

        return Validator::make($input, $rules, array_merge($this->getValidationMessages(), $messages), $attributes);
    }

    /**
     * Render the form.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->addVariables(['fields' => $this->fields()]);

        return parent::render();
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return Field
     */
    public function __call($method, $arguments)
    {
        if ($className = Form::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, '');
            $element = new $className($this->column() . '.' . $column, array_slice($arguments, 1));
            $this->pushField($element);
            return $element;
        }

        admin_error('Error', "Field type [$method] does not exist.");

        return new Field\Nullable();
    }
}
