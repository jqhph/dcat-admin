<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class DateRange extends Field
{
    protected $format = 'YYYY-MM-DD';

    protected $column = [];

    public function __construct($column, $arguments)
    {
        $this->column['start'] = $column;
        $this->column['end'] = $arguments[0];

        array_shift($arguments);
        $this->label = $this->formatLabel($arguments);

        $this->options(['format' => $this->format]);
    }

    protected function prepareInputValue($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    public function render()
    {
        $this->options['locale'] = config('app.locale');

        $this->addVariables(['options' => $this->options]);

        return parent::render();
    }

    /**
     * {@inheritDoc}
     */
    public function getValidationMessages()
    {
        // Default validation message.
        $messages = parent::getValidationMessages();

        $result = [];
        foreach ($messages as $key => $message) {
            $column = explode('.', $key);
            $rule = array_pop($column);
            $column = implode('.', $column);

            if ($this->column['start'] == $column) {
                $result[$column.'start.'.$rule] = $message;
            } else {
                $result[$key] = $message;
            }
        }

        return $result;
    }
}
