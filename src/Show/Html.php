<?php

namespace Dcat\Admin\Show;

use Dcat\Admin\Support\Helper;

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
        return Helper::render($this->html, [], $this->parent->model());
    }
}
