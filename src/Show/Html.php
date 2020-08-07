<?php

namespace Dcat\Admin\Show;

class Html extends Field
{
    public $html;

    public function __construct($html, string $name = '', string $label = '')
    {
        $this->html = $html;
        parent::__construct($name, $label);
    }

    public function render()
    {
        return $this->html;
    }
}
