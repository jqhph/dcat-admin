<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Illuminate\Support\Str;

class Button extends Field
{
    protected $class = 'btn-primary';

    public function __construct($label)
    {
        parent::__construct(Str::random(), [$label]);

        $this->addVariables(['buttonClass' => $this->class]);
    }

    public function class(string $class)
    {
        return $this->addVariables(['buttonClass' => $class]);
    }

    public function on($event, $callback)
    {
        $this->script = <<<JS
$('{$this->getElementClassSelector()}').on('$event', function() {
    $callback
});
JS;

        return $this;
    }
}
