<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Form\Field\MultipleSelectTable;
use Dcat\Admin\Form\Field\SelectTable;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

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
 * @method Field\Divide                 divider()
 * @method Field\Password               password($column, $label = '')
 * @method Field\Decimal                decimal($column, $label = '')
 * @method Field\Html                   html($html, $label = '')
 * @method Field\Tags                   tags($column, $label = '')
 * @method Field\Icon                   icon($column, $label = '')
 * @method Field\Embeds                 embeds($column, $label = '')
 * @method Field\Captcha                captcha()
 * @method Field\Listbox                listbox($column, $label = '')
 * @method Field\SelectResource         selectResource($column, $label = '')
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
 * @method Field\SelectTable            selectTable($column, $label = '')
 * @method Field\MultipleSelectTable    multipleSelectTable($column, $label = '')
 */
class NestedForm
{
    const DEFAULT_KEY_NAME = '__LA_KEY__';

    const REMOVE_FLAG_NAME = '_remove_';

    const REMOVE_FLAG_CLASS = 'fom-removed';

    /**
     * @var string
     */
    protected $relationName;

    /**
     * NestedForm key.
     *
     * @var
     */
    protected $key;

    /**
     * Fields in form.
     *
     * @var Collection
     */
    protected $fields;

    /**
     * Original data for this field.
     *
     * @var array
     */
    protected $original = [];

    /**
     * @var Form|WidgetForm
     */
    protected $form;

    /**
     * Create a new NestedForm instance.
     *
     * NestedForm constructor.
     *
     * @param string $relation
     * @param null   $key
     */
    public function __construct($relation, $key = null)
    {
        $this->relationName = $relation;

        $this->key = $key;

        $this->fields = new Collection();
    }

    /**
     * Set Form.
     *
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
     * Get form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set original values for fields.
     *
     * @param array  $data
     * @param string $relatedKeyName
     *
     * @return $this
     */
    public function setOriginal($data, $relatedKeyName)
    {
        if (empty($data)) {
            return $this;
        }

        foreach ($data as $value) {
            if (! isset($value[$relatedKeyName])) {
                continue;
            }

            /*
             * like $this->original[30] = [ id = 30, .....]
             */
            $this->original[$value[$relatedKeyName]] = $value;
        }

        return $this;
    }

    /**
     * Prepare for insert or update.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function prepare($input)
    {
        foreach ($input as $key => $record) {
            if (! array_key_exists(static::REMOVE_FLAG_NAME, $record)) {
                continue;
            }

            $this->setFieldOriginalValue($key);

            $input[$key] = $this->prepareRecord($record);
        }

        return $input;
    }

    /**
     * Get key for current form.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key for current form.
     *
     * @param mixed $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Set original data for each field.
     *
     * @param string $key
     *
     * @return void
     */
    protected function setFieldOriginalValue($key)
    {
        $values = [];
        if (array_key_exists($key, $this->original)) {
            $values = $this->original[$key];
        }
        $this->fields->each(function (Field $field) use ($values) {
            $field->setOriginal($values);
        });
    }

    /**
     * Do prepare work before store and update.
     *
     * @param array $record
     *
     * @return array
     */
    protected function prepareRecord($record)
    {
        if ($record[static::REMOVE_FLAG_NAME] == 1) {
            return $record;
        }

        $prepared = [];

        /* @var Field $field */
        foreach ($this->fields as $field) {
            $columns = $field->column();

            $value = $this->fetchColumnValue($record, $columns);

            if ($value === false) {
                continue;
            }

            if (method_exists($field, 'prepare')) {
                $value = $field->prepare($value);
            }

            if (($field instanceof Form\Field\Hidden) || $value != $field->original()) {
                if (is_array($columns)) {
                    foreach ($columns as $name => $column) {
                        Arr::set($prepared, $column, $value[$name]);
                    }
                } elseif (is_string($columns)) {
                    Arr::set($prepared, $columns, $value);
                }
            }
        }

        $prepared[static::REMOVE_FLAG_NAME] = $record[static::REMOVE_FLAG_NAME];

        return $prepared;
    }

    /**
     * Fetch value in input data by column name.
     *
     * @param array        $data
     * @param string|array $columns
     *
     * @return array|mixed
     */
    protected function fetchColumnValue($data, $columns)
    {
        if (is_string($columns)) {
            if (! Arr::has($data, $columns)) {
                return false;
            }

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

        return false;
    }

    /**
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field $field)
    {
        $this->fields->push($field);

        if (method_exists($this->form, 'builder')) {
            $this->form->builder()->fields()->push($field);
            $field->attribute(Builder::BUILD_IGNORE, true);
        }

        $field->setNestedFormRelation($this->relationName, $this->key);

        $field::collectAssets();

        return $this;
    }

    /**
     * Get fields of this form.
     *
     * @return Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Fill data to all fields in form.
     *
     * @param array $data
     *
     * @return $this
     */
    public function fill(array $data)
    {
        /* @var Field $field */
        foreach ($this->fields() as $field) {
            $field->fill($data);
        }

        return $this;
    }

    /**
     * Get the html and script of template.
     *
     * @return array
     */
    public function getTemplateHtmlAndScript()
    {
        $html = '';
        $scripts = [];

        /* @var Field $field */
        foreach ($this->fields() as $field) {

            //when field render, will push $script to Admin
            $html .= $field->render();

            /*
             * Get and remove the last script of Admin::$script stack.
             */
            if ($field->getScript()) {
                $scripts[] = array_pop(Admin::asset()->script);
            }
        }

        return [$html, implode(";\r\n", $scripts)];
    }

    /**
     * Set `errorKey` `elementName` `elementClass` for fields inside hasmany fields.
     *
     * @param Field $field
     *
     * @return Field
     */
    protected function formatField(Field $field)
    {
        $column = $field->column();

        $elementName = $elementClass = $errorKey = [];

        $key = $this->key ?: 'new_'.static::DEFAULT_KEY_NAME;

        if (is_array($column)) {
            foreach ($column as $k => $name) {
                $errorKey[$k] = sprintf('%s.%s.%s', $this->relationName, $key, $name);
                $elementName[$k] = sprintf('%s[%s][%s]', $this->relationName, $key, $name);
                $elementClass[$k] = [$this->relationName, $name];
            }
        } else {
            $errorKey = sprintf('%s.%s.%s', $this->relationName, $key, $column);
            $elementName = sprintf('%s[%s][%s]', $this->relationName, $key, $column);
            $elementClass = [$this->relationName, $column];
        }

        return $field->setErrorKey($errorKey)
            ->setElementName($elementName)
            ->setElementClass($elementClass);
    }

    /**
     * Add nested-form fields dynamically.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($className = Form::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, '');

            /* @var Field $field */
            $field = new $className($column, array_slice($arguments, 1));

            $field->setForm($this->form);

            $field = $this->formatField($field);

            $this->pushField($field);

            return $field;
        }

        return $this;
    }
}
