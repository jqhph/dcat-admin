<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class NavList extends Widget
{
    protected $items = [];

    /**
     * @var \Closure
     */
    protected $builder;

    protected $active = [];

    protected $click = false;

    public function __construct($items = [])
    {
        $this->add($items);

        $this->class('nav nav-pills nav-stacked');
        $this->id('nav-list-'.Str::random(8));
    }

    public function checked($key)
    {
        $this->active = $key;

        return $this;
    }

    public function click()
    {
        $this->click = true;

        return $this;
    }

    public function add($items)
    {
        if ($items instanceof Arrayable) {
            $items = $items->toArray();
        }

        $this->items = (array) $items;

        return $this;
    }

    public function map(\Closure $closure)
    {
        $this->builder = $closure;

        return $this;
    }

    public function render()
    {
        if ($this->click) {
            Admin::script(
                <<<JS
$('#{$this->id} li').click(function () {
    $('#{$this->id} li').removeClass('active');
    $(this).addClass('active');
});
JS
            );
        }

        $html = '';
        foreach ($this->items as $k => $item) {
            if ($builder = $this->builder) {
                $item = $builder($item, $k);
            }

            $active = $this->active === $k ? 'active' : '';

            $item = strpos($item, '</a>') ? $item : "<a href='javascript:void(0)'>$item</a>";

            $html .= "<li class='$active bg-white'>$item</li>";
        }

        return "<ul {$this->formatHtmlAttributes()}>{$html}</ul>";
    }
}
