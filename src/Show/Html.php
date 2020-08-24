<?php

namespace Dcat\Admin\Show;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Show;

class Html extends Field
{
    public $html;

    public function __construct($html, Show $show, string $name = '', string $label = '')
    {
        parent::__construct($name, $label);
        $this->html = $html;
        $this->setParent($show);
    }

    public function render()
    {
        return Helper::render($this->html, [$this->value()], $this->parent->model());
    }
}
