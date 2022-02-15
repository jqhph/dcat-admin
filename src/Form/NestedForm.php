<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class NestedForm extends WidgetForm
{
    const DEFAULT_KEY_PREFIX = 'new_';
    const DEFAULT_PARENT_KEY_NAME = '__PARENT_NESTED__';
    const DEFAULT_KEY_NAME = '__NESTED__';

    const REMOVE_FLAG_NAME = '_remove_';

    const REMOVE_FLAG_CLASS = 'form-removed';

    /**
     * @var string
     */
    protected $relationName;

    /**
     * NestedForm key.
     *
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $defaultKey;

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
     * @param  string  $relation
     * @param  null  $key
     */
    public function __construct($relation = null, $key = null)
    {
        parent::__construct();

        $this->relationName = $relation;

        $this->key = $key;

        $this->resetButton(false);
        $this->submitButton(false);
        $this->ajax(false);
        $this->useFormTag(false);
    }

    /**
     * Set Form.
     *
     * @param  Form|WidgetForm  $form
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
    public function form()
    {
        return $this->form;
    }

    public function model()
    {
        return $this->form->model();
    }

    /**
     * Set original values for fields.
     *
     * @param  array  $data
     * @param  string  $relatedKeyName
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
     * @param  array  $input
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
     * @return mixed
     */
    public function getParentKey()
    {
        return $this->form->getKey();
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
     * @param  mixed  $key
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
     * @param  string  $key
     * @return void
     */
    protected function setFieldOriginalValue($key)
    {
        $values = [];
        if (Helper::keyExists($key, $this->original)) {
            $values = $this->original[$key];
        }
        $this->fields->each(function (Field $field) use ($values) {
            $field->setOriginal($values);
        });
    }

    /**
     * Do prepare work before store and update.
     *
     * @param  array  $record
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

            if (($field instanceof Form\Field\Hidden) || ! Helper::equal($field->original(), $value)) {
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
     * @param  array  $data
     * @param  string|array  $columns
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
     * {@inheritDoc}
     */
    public function pushField(Field $field)
    {
        $this->fields->push($field);

        $field->setForm($this->form);
        $field->setParent($this);

        if ($this->layout()->hasColumns()) {
            $this->layout()->addField($field);
        }

        $field->attribute(Field::BUILD_IGNORE, true);

        if ($this->form && method_exists($this->form, 'builder')) {
            $this->form->builder()->pushField((clone $field)->display(false));
        }

        if ($field instanceof Form\Field\HasMany) {
            // HasMany以及array嵌套table，需要保存上级字段名
            $field->setParentRelationName($this->relationName, $this->key);
        }

        $this->callResolvingFieldCallbacks($field);

        $field->setRelation([
            'relation' => $this->relationName,
            'key'      => $this->key,
        ]);

        $field::requireAssets();

        $field->width($this->width['field'], $this->width['label']);

        return $this;
    }

    protected function resolveField($method, $arguments)
    {
        if ($className = Form::findFieldClass($method)) {
            $column = Arr::get($arguments, 0, '');

            /* @var Field $field */
            $field = new $className($column, array_slice($arguments, 1));

            return $this->formatField($field);
        }
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
     * @param  array  $data
     * @return $this
     */
    public function fill($data)
    {
        /* @var Field $field */
        foreach ($this->fields() as $field) {
            $field->fill($data);
        }

        return $this;
    }

    public function getDefaultKey()
    {
        return $this->defaultKey ?: (static::DEFAULT_KEY_PREFIX.static::DEFAULT_KEY_NAME);
    }

    public function setDefaultKey($key)
    {
        $this->defaultKey = $key;

        return $this;
    }

    /**
     * Set `errorKey` `elementName` `elementClass` for fields inside hasmany fields.
     *
     * @param  Field  $field
     * @return Field
     */
    protected function formatField(Field $field)
    {
        $column = $field->column();

        $elementName = $elementClass = $errorKey = [];

        $key = $this->key ?? $this->getDefaultKey();

        if (is_array($column)) {
            foreach ($column as $k => $name) {
                $errorKey[$k] = sprintf('%s.%s.%s', $this->relationName, $key, $name);
                $elementName[$k] = Helper::formatElementName($this->formatName().'.'.$key.'.'.$name);
                $elementClass[$k] = [$this->formatClass(), $this->formatClass($name), $this->formatClass($name, false)];
            }
        } else {
            $errorKey = sprintf('%s.%s.%s', $this->relationName, $key, $column);
            $elementName = Helper::formatElementName($this->formatName().'.'.$key.'.'.$column);
            $elementClass = [$this->formatClass(), $this->formatClass($column), $this->formatClass($column, false)];
        }

        return $field->setErrorKey($errorKey)
            ->setElementName($elementName)
            ->setElementClass($elementClass);
    }

    protected function formatClass($name = null, bool $append = true)
    {
        $class = str_replace('.', '_', $name ?: $this->relationName);

        return $append ? ($class.'_'.$this->key) : $class;
    }

    protected function formatName($name = null)
    {
        return Helper::formatElementName($name ?: $this->relationName);
    }

    /**
     * Add nested-form fields dynamically.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if ($field = $this->resolveField($method, $arguments)) {
            $this->pushField($field);

            return $field;
        }

        return $this;
    }
}
