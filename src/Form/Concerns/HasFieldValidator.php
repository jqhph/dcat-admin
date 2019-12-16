<?php

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @property Form $form
 */
trait HasFieldValidator
{
    /**
     * The validation rules for creation.
     *
     * @var array|\Closure
     */
    protected $creationRules = [];

    /**
     * The validation rules for updates.
     *
     * @var array|\Closure
     */
    protected $updateRules = [];

    /**
     * Validation rules.
     *
     * @var array|\Closure
     */
    protected $rules = [];

    /**
     * @var \Closure
     */
    protected $validator;

    /**
     * Validation messages.
     *
     * @var array
     */
    protected $validationMessages = [];

    /**
     * Set the update validation rules for the field.
     *
     * @param array|callable|string $rules
     * @param array                 $messages
     *
     * @return $this
     */
    public function updateRules($rules = null, $messages = [])
    {
        $this->updateRules = $this->mergeRules($rules, $this->updateRules);

        $this->setValidationMessages('update', $messages);

        return $this;
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param array|callable|string $rules
     * @param array                 $messages
     *
     * @return $this
     */
    public function creationRules($rules = null, $messages = [])
    {
        $this->creationRules = $this->mergeRules($rules, $this->creationRules);

        $this->setValidationMessages('creation', $messages);

        return $this;
    }

    /**
     * Get or set rules.
     *
     * @param null  $rules
     * @param array $messages
     *
     * @return $this
     */
    public function rules($rules = null, $messages = [])
    {
        if ($rules instanceof \Closure) {
            $this->rules = $rules;
        }

        $originalRules = is_array($this->rules) ? $this->rules : [];

        if (is_array($rules)) {
            $this->rules = array_merge($originalRules, $rules);
        } elseif (is_string($rules)) {
            $this->rules = array_merge($originalRules, array_filter(explode('|', $rules)));
        }

        $this->setValidationMessages('default', $messages);

        return $this;
    }

    /**
     * Get field validation rules.
     *
     * @return string
     */
    protected function getRules()
    {
        if (request()->isMethod('POST')) {
            $rules = $this->creationRules ?: $this->rules;
        } elseif (request()->isMethod('PUT')) {
            $rules = $this->updateRules ?: $this->rules;
        } else {
            $rules = $this->rules;
        }

        if ($rules instanceof \Closure) {
            $rules = $rules->call($this, $this->form);
        }

        if (is_string($rules)) {
            $rules = array_filter(explode('|', $rules));
        }

        if (! $this->form) {
            return $rules;
        }

        if (method_exists($this->form, 'key') || ! $id = $this->form->key()) {
            return $rules;
        }

        if (is_array($rules)) {
            foreach ($rules as &$rule) {
                if (is_string($rule)) {
                    $rule = str_replace('{{id}}', $id, $rule);
                }
            }
        }

        return $rules;
    }

    /**
     * Format validation rules.
     *
     * @param array|string $rules
     *
     * @return array
     */
    protected function formatRules($rules)
    {
        if (is_string($rules)) {
            $rules = array_filter(explode('|', $rules));
        }

        return array_filter((array) $rules);
    }

    /**
     * @param string|array|\Closure $input
     * @param string|array          $original
     *
     * @return array|\Closure
     */
    protected function mergeRules($input, $original)
    {
        if ($input instanceof \Closure) {
            $rules = $input;
        } else {
            if (! empty($original)) {
                $original = $this->formatRules($original);
            }
            $rules = array_merge($original, $this->formatRules($input));
        }

        return $rules;
    }

    /**
     * @param string $rule
     *
     * @return $this
     */
    public function removeUpdateRule($rule)
    {
        $this->deleteRuleByKeyword($this->updateRules, $rule);

        return $this;
    }

    /**
     * @param string $rule
     *
     * @return $this
     */
    public function removeCreationRule($rule)
    {
        $this->deleteRuleByKeyword($this->creationRules, $rule);

        return $this;
    }

    /**
     * Remove a specific rule by keyword.
     *
     * @param string $rule
     *
     * @return $this
     */
    public function removeRule($rule)
    {
        $this->deleteRuleByKeyword($this->rules, $rule);

        return $this;
    }

    /**
     * @param $rules
     * @param $rule
     *
     * @return void
     */
    protected function deleteRuleByKeyword(&$rules, $rule)
    {
        if (is_array($rules)) {
            Helper::deleteByValue($rules, $rule);

            return;
        }

        if (! is_string($rules)) {
            return;
        }

        $pattern = "/{$rule}[^\|]?(\||$)/";

        $rules = preg_replace($pattern, '', $rules, -1);
    }

    /**
     * @param string $rule
     *
     * @return bool
     */
    public function hasUpdateRule($rule)
    {
        return $this->isRuleExists($this->updateRules, $rule);
    }

    /**
     * @param string $rule
     *
     * @return bool
     */
    public function hasCreationRule($rule)
    {
        return $this->isRuleExists($this->creationRules, $rule);
    }

    /**
     * @param string $rule
     *
     * @return bool
     */
    public function hasRule($rule)
    {
        return $this->isRuleExists($this->rules, $rule);
    }

    /**
     * @param $rules
     * @param $rule
     *
     * @return bool
     */
    protected function isRuleExists($rules, $rule)
    {
        if (is_array($rules)) {
            return in_array($rule, $rules);
        }

        if (! is_string($rules)) {
            return false;
        }

        $pattern = "/{$rule}[^\|]?(\||$)/";

        return (bool) preg_match($pattern, $rules);
    }

    /**
     * Set field validator.
     *
     * @param callable $validator
     *
     * @return $this
     */
    public function validator(callable $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return bool|Validator
     */
    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        $rules = $attributes = [];

        if (! $fieldRules = $this->getRules()) {
            return false;
        }

        if (is_string($this->column)) {
            if (! Arr::has($input, $this->column)) {
                return false;
            }

            $input = $this->sanitizeInput($input, $this->column);

            $rules[$this->column] = $fieldRules;
            $attributes[$this->column] = $this->label;
        }

        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                if (! array_key_exists($column, $input)) {
                    continue;
                }
                $input[$column.$key] = Arr::get($input, $column);
                $rules[$column.$key] = $fieldRules;
                $attributes[$column.$key] = "{$this->label}[$column]";
            }
        }

        return Validator::make($input, $rules, $this->getValidationMessages(), $attributes);
    }

    /**
     * Set validation messages for column.
     *
     * @param string $key
     * @param array  $messages
     *
     * @return $this
     */
    public function setValidationMessages($key, array $messages)
    {
        $this->validationMessages[$key] = $messages;

        return $this;
    }

    /**
     * Get validation messages for the field.
     *
     * @return array|mixed
     */
    public function getValidationMessages()
    {
        // Default validation message.
        $messages = $this->validationMessages['default'] ?? [];

        if (request()->isMethod('POST')) {
            $messages = $this->validationMessages['creation'] ?? $messages;
        } elseif (request()->isMethod('PUT')) {
            $messages = $this->validationMessages['update'] ?? $messages;
        }

        $result = [];

        foreach ($messages as $k => $v) {
            if (Str::contains($k, '.')) {
                $result[$k] = $v;
                continue;
            }

            if (is_string($this->column)) {
                $k = $this->column.'.'.$k;

                $result[$k] = $v;
                continue;
            }

            foreach ($this->column as $column) {
                $result[$column.'.'.$k] = $v;
            }
        }

        return $result;
    }

    /**
     * Set error messages for individual form field.
     *
     * @see http://1000hz.github.io/bootstrap-validator/
     *
     * @param string $error
     * @param string $key
     *
     * @return $this
     */
    public function setClientValidationError(string $error, string $key = null)
    {
        $key = $key ? "{$key}-" : '';

        return $this->attribute("data-{$key}error", $error);
    }
}
