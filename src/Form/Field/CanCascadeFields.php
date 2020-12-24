<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\RuntimeException;
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

        ($this->parent ?: $this->form)->cascadeGroup($closure, [
            'column' => $this->column(),
            'index'  => count($this->conditions) - 1,
            'class'  => $this->getCascadeClass($value, $operator),
        ]);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function getCascadeClass($value, string $operator)
    {
        if (is_array($value)) {
            $value = implode('-', $value);
        }

        $map = [
            '=' => '0',
            '>' => '1',
            '<' => '2',
            '!=' => '3',
            'in' => '4',
            'notIn' => '5',
            '>=' => '6',
            '<=' => '7',
            'has' => '8',
        ];

        return sprintf('cascade-%s-%s-%s', str_replace(' ', '-', $this->getElementClassString()), $value, $map[$operator]);
    }

    protected function addCascadeScript()
    {
        if (! $script = $this->getCascadeScript()) {
            return;
        }

        Admin::script(
            <<<JS
Dcat.init('{$this->getElementClassSelector()}', function (\$this) {
    {$script}
});
JS
        );
    }

    /**
     * Add cascade scripts to contents.
     *
     * @return string
     */
    protected function getCascadeScript()
    {
        if (empty($this->conditions)) {
            return;
        }

        $cascadeGroups = collect($this->conditions)->map(function ($condition) {
            return [
                'class'    => $this->getCascadeClass($condition['value'], $condition['operator']),
                'operator' => $condition['operator'],
                'value'    => $condition['value'],
            ];
        })->toJson();

        return <<<JS
(function () {
    var compare = function (a, b, o) {
        if (! $.isArray(b)) {
            return operator_table[o](a, b)
        }
        
        if (o === '!=') {
            var result = true;
            for (var i in b) {
                if (! operator_table[o](a, b[i])) {
                    result = false;
                    
                    break;
                }
            }
            return result;
        }
        
        for (var i in b) {
            if (operator_table[o](a, b[i])) {
                return true;
            }
        }
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

    \$this.on(event, function (e) {
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
                throw new RuntimeException('Invalid form field type');
        }
    }
}
