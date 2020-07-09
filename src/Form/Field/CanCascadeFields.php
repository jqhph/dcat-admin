<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Illuminate\Support\Arr;

/**
 * @property Form $form
 */
trait CanCascadeFields
{
    /**
     * @var array
     */
    protected $conditions = [];

    /**
     * @var array
     */
    protected $cascadeGroups = [];

    /**
     * @param $operator
     * @param $value
     * @param $closure
     *
     * @return $this
     */
    public function when($operator, $value, $closure = null)
    {
        if (func_num_args() == 2) {
            $closure = $value;
            $value = $operator;
            $operator = $this->getDefaultOperator();
        }

        $this->formatValues($operator, $value);

        $this->addDependents($operator, $value, $closure);

        return $this;
    }

    protected function getDefaultOperator()
    {
        if ($this instanceof MultipleSelect || $this instanceof Checkbox) {
            return 'in';
        }

        return '=';
    }

    /**
     * @param string $operator
     * @param mixed  $value
     */
    protected function formatValues(string $operator, &$value)
    {
        if (in_array($operator, ['in', 'notIn'])) {
            $value = Arr::wrap($value);
        }

        if (is_array($value)) {
            $value = array_map('strval', $value);
        } else {
            $value = strval($value);
        }
    }

    /**
     * @param string   $operator
     * @param mixed    $value
     * @param \Closure $closure
     */
    protected function addDependents(string $operator, $value, \Closure $closure)
    {
        $this->conditions[] = compact('operator', 'value', 'closure');

        $this->form->cascadeGroup($closure, [
            'column' => $this->column(),
            'index'  => count($this->conditions) - 1,
            'class'  => $this->getCascadeClass($value),
        ]);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function getCascadeClass($value)
    {
        if (is_array($value)) {
            $value = implode('-', $value);
        }

        return sprintf('cascade-%s-%s', $this->getElementClassString(), $value);
    }

    /**
     * Add cascade scripts to contents.
     *
     * @return void
     */
    protected function addCascadeScript()
    {
        if (empty($this->conditions)) {
            return;
        }

        $cascadeGroups = collect($this->conditions)->map(function ($condition) {
            return [
                'class'    => $this->getCascadeClass($condition['value']),
                'operator' => $condition['operator'],
                'value'    => $condition['value'],
            ];
        })->toJson();

        $script = <<<JS
(function () {
    var compare = function (a, b, o) {
        if ($.isArray(b)) {
            for (var i in b) {
                if (operator_table[o](a, b[i])) {
                    return true;
                }
            }
            return false;
        }
        
        return operator_table[o](a, b)
    };
    
    var operator_table = {
        '=': function(a, b) {
            if ($.isArray(a) && $.isArray(b)) {
                return $(a).not(b).length === 0 && $(b).not(a).length === 0;
            }

            return String(a) === String(b);
        },
        '>': function(a, b) {
            return a > b; 
        },
        '<': function(a, b) {
            return a < b; 
        },
        '>=': function(a, b) { return a >= b; },
        '<=': function(a, b) { return a <= b; },
        '!=': function(a, b) {
             return ! operator_table['='](a, b);
        },
        'in': function(a, b) { return Dcat.helpers.inObject(a, String(b), true); },
        'notIn': function(a, b) { return ! Dcat.helpers.inObject(a, String(b), true); },
        'has': function(a, b) { return Dcat.helpers.inObject(b, String(b), true); },
    };
    var cascade_groups = {$cascadeGroups}, event = '{$this->cascadeEvent}';

    $('{$this->getElementClassSelector()}').on(event, function (e) {
        {$this->getFormFrontValue()}

        cascade_groups.forEach(function (event) {
            var group = $('div.cascade-group.'+event.class);
            if (compare(checked, event.value, event.operator)) {
                group.removeClass('d-none');
            } else {
                group.addClass('d-none');
            }
        });
    }).trigger(event);
})();
JS;

        Admin::script($script);
    }

    /**
     * @return string
     */
    protected function getFormFrontValue()
    {
        switch (get_class($this)) {
            case Select::class:
            case MultipleSelect::class:
                return 'var checked = $(this).val();';
            case Radio::class:
                return <<<JS
var checked = $('{$this->getElementClassSelector()}:checked').val();
JS;
            case Checkbox::class:
                return <<<JS
var checked = $('{$this->getElementClassSelector()}:checked').map(function(){
  return $(this).val();
}).get();
JS;
            default:
                throw new \InvalidArgumentException('Invalid form field type');
        }
    }
}
