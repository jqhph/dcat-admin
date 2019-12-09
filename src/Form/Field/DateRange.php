<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;

class DateRange extends Field
{
    protected $format = 'YYYY-MM-DD';

    /**
     * Column name.
     *
     * @var string
     */
    protected $column = [];

    public function __construct($column, $arguments)
    {
        $this->column['start'] = $column;
        $this->column['end'] = $arguments[0];

        array_shift($arguments);
        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($this->column);

        $this->options(['format' => $this->format]);
    }

    protected function prepareToSave($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    public function render()
    {
        $this->options['locale'] = config('app.locale');

        $startOptions = json_encode($this->options);
        $endOptions = json_encode($this->options + ['useCurrent' => false]);

        $class = $this->elementClassSelector();

        $this->script = <<<JS
            $('{$class['start']}').datetimepicker($startOptions);
            $('{$class['end']}').datetimepicker($endOptions);
            $("{$class['start']}").on("dp.change", function (e) {
                $('{$class['end']}').data("DateTimePicker").minDate(e.date);
            });
            $("{$class['end']}").on("dp.change", function (e) {
                $('{$class['start']}').data("DateTimePicker").maxDate(e.date);
            });
JS;

        return parent::render();
    }

    /**
     * Get validation messages for the field.
     *
     * @return array|mixed
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

    public static function collectAssets()
    {
        Admin::collectComponentAssets('moment');
        Admin::collectComponentAssets('bootstrap-datetimepicker');
    }
}
