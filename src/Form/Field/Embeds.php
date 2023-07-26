<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Contracts\FieldsCollection;
use Dcat\Admin\Form\EmbeddedForm;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\ResolveField;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Embeds extends Field implements FieldsCollection
{
    use ResolveField;

    /**
     * @var \Closure
     */
    protected $builder = null;

    /**
     * Create a new HasMany field instance.
     *
     * @param  string  $column
     * @param  array  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->column = $column;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        }

        if (count($arguments) == 2) {
            [$this->label, $this->builder] = $arguments;
        }
    }

    /**
     * Prepare input data for insert or update.
     *
     * @param  array  $input
     * @return array
     */
    protected function prepareInputValue($input)
    {
        $form = $this->buildEmbeddedForm();

        return $form->setOriginal($this->original)->prepare($input);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidator(array $input)
    {
        if (! Arr::has($input, $this->column)) {
            return false;
        }

        //$input = Arr::only($input, $this->column);

        $rules = $attributes = $messages = [];

        /** @var Field $field */
        foreach ($this->buildEmbeddedForm()->fields() as $field) {
            if (! $fieldRules = $field->getRules()) {
                continue;
            }

            File::deleteRules($field, $fieldRules);

            $column = $field->column();

            /*
             *
             * For single column field format rules to:
             * [
             *     'extra.name' => 'required'
             *     'extra.email' => 'required'
             * ]
             *
             * For multiple column field with rules like 'required':
             * 'extra' => [
             *     'start' => 'start_at'
             *     'end'   => 'end_at',
             * ]
             *
             * format rules to:
             * [
             *     'extra.start_atstart' => 'required'
             *     'extra.end_atend' => 'required'
             * ]
             */
            if (is_array($column)) {
                foreach ($column as $key => $name) {
                    $rules["{$this->column}.$name$key"] = $fieldRules;
                }

                $this->resetInputKey($input, $column);
            } else {
                $rules["{$this->column}.$column"] = $fieldRules;
            }

            /**
             * For single column field format attributes to:
             * [
             *     'extra.name' => $label
             *     'extra.email' => $label
             * ].
             *
             * For multiple column field with rules like 'required':
             * 'extra' => [
             *     'start' => 'start_at'
             *     'end'   => 'end_at',
             * ]
             *
             * format rules to:
             * [
             *     'extra.start_atstart' => "$label[start_at]"
             *     'extra.end_atend' => "$label[end_at]"
             * ]
             */
            $attributes = array_merge(
                $attributes,
                $this->formatValidationAttribute($input, $field->label(), $column)
            );

            $messages = array_merge(
                $messages,
                $this->formatValidationMessages($input, $field->getValidationMessages())
            );
        }

        if (empty($rules)) {
            return false;
        }

        return Validator::make($input, $rules, array_merge($this->getValidationMessages(), $messages), $attributes);
    }

    /**
     * Format validation messages.
     *
     * @param  array  $input
     * @param  array  $messages
     * @return array
     */
    protected function formatValidationMessages(array $input, array $messages)
    {
        $result = [];
        foreach ($messages as $k => $message) {
            $result[$this->column.'.'.$k] = $message;
        }

        return $result;
    }

    /**
     * Format validation attributes.
     *
     * @param  array  $input
     * @param  string  $label
     * @param  string  $column
     * @return array
     */
    protected function formatValidationAttribute($input, $label, $column)
    {
        $new = $attributes = [];

        if (is_array($column)) {
            foreach ($column as $index => $col) {
                $new[$col.$index] = $col;
            }
        }

        foreach (array_keys(Arr::dot($input)) as $key) {
            if (is_string($column)) {
                if (Str::endsWith($key, ".$column")) {
                    $attributes[$key] = $label;
                }
            } else {
                foreach ($new as $k => $val) {
                    if (Str::endsWith($key, ".$k")) {
                        $attributes[$key] = $label."[$val]";
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * Reset input key for validation.
     *
     * @param  array  $input
     * @param  array  $column  $column is the column name array set
     * @return void.
     */
    public function resetInputKey(array &$input, array $column)
    {
        $column = array_flip($column);

        foreach (Arr::get($input, $this->column) as $key => $value) {
            if (! array_key_exists($key, $column)) {
                continue;
            }

            $newKey = $key.$column[$key];

            /*
             * set new key
             */
            Arr::set($input, "{$this->column}.$newKey", $value);
            /*
             * forget the old key and value
             */
            Arr::forget($input, "{$this->column}.$key");
        }
    }

    /**
     * Get data for Embedded form.
     *
     * Normally, data is obtained from the database.
     *
     * When the data validation errors, data is obtained from session flash.
     *
     * @return array
     */
    protected function getEmbeddedData()
    {
        return Helper::array($this->value);
    }

    /**
     * Build a Embedded Form and fill data.
     *
     * @return EmbeddedForm
     */
    protected function buildEmbeddedForm()
    {
        $form = new EmbeddedForm($this->column);

        $form->setParent($this->form);

        $form->setResolvingFieldCallbacks($this->resolvingFieldCallbacks);

        call_user_func($this->builder, $form);

        $form->fill($this->getEmbeddedData());

        return $form;
    }

    /**
     * Render the form.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->addVariables(['form' => $this->buildEmbeddedForm()]);

        return parent::render();
    }

    /**
     * 根据字段名称查找字段.
     *
     * @param  string  $column
     * @return Field|null
     */
    public function field($name)
    {
        return $this->buildEmbeddedForm()->fields()->first(function (Field $field) use ($name) {
            return $field->column() == $name;
        });
    }

    /**
     * 获取所有字段.
     *
     * @return void
     */
    public function fields()
    {
        return $this->buildEmbeddedForm()->fields();
    }
}
