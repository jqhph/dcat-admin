<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class Button extends Field
{
    protected $class = 'btn-primary';

    public function info()
    {
        $this->class = 'btn-info';

        return $this;
    }

    public function on($event, $callback)
    {
        $this->script = <<<JS
        $('{$this->elementClassSelector()}').on('$event', function() {
            $callback
        });
JS;
    }
}
