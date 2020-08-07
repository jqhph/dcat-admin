<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class Button extends Field
{
    protected $class = 'btn-primary';

    public function class(string $class)
    {
        $this->class = $class;

        return $this;
    }

    public function variables()
    {
        $this->addVariables(['buttonClass' => $this->class]);

        return parent::variables();
    }

    public function on($event, $callback)
    {
        $this->script = <<<JS
        $('{$this->getElementClassSelector()}').on('$event', function() {
            $callback
        });
JS;
    }
}
