<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Str;

class Navbar extends Widget
{
    const DROPDOWN_FLAG_KEY = '__dropdown__';

    protected $view = 'admin::widgets.navbar';

    protected $id;

    protected $title;

    /**
     * @var \Closure
     */
    protected $builder;

    /**
     * @var array
     */
    protected $items = [
        'right' => [],
        'left'  => [],
    ];

    protected $active;

    protected $click = false;

    /**
     * Navbar constructor.
     */
    public function __construct(?string $title = '#', $items = [])
    {
        $this->title($title);
        $this->add($items);

        $this->class('navbar navbar-default');
        $this->id = 'navbar-'.Str::random(8);
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    public function noShadow()
    {
        return $this->class('no-shadow', true);
    }

    public function margin($value)
    {
        return $this->style('margin:'.$value);
    }

    public function add($items, bool $right = false)
    {
        if ($right) {
            $this->items['right'] = array_merge($this->items['right'], Helper::array($items));
        } else {
            $this->items['left'] = array_merge($this->items['left'], Helper::array($items));
        }

        return $this;
    }

    public function left($items)
    {
        return $this->add($items, false);
    }

    public function right($items)
    {
        return $this->add($items, true);
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

    public function map(\Closure $closure)
    {
        $this->builder = $closure;

        return $this;
    }

    public function dropdown(
        ?string $text,
        array $options,
        \Closure $closure = null,
        bool $right = false
    ) {
        $dropdown = Dropdown::make($options)
            ->button($text)
            ->buttonClass('')
            ->template('%s<ul class="dropdown-menu">%s</ul>');

        if ($closure) {
            $closure($dropdown);
        }

        $key = $right ? 'right' : 'left';

        $this->items[$key][self::DROPDOWN_FLAG_KEY] = $dropdown;

        return $this;
    }

    public function variables()
    {
        foreach ($this->items['left'] as $k => &$item) {
            $item = $this->formatItem($k, $item);
        }
        foreach ($this->items['right'] as $k => &$item) {
            $item = $this->formatItem($k, $item);
        }

        if ($this->click) {
            Admin::script(
                <<<JS
$('#{$this->id} li.nav-li').click(function () {
    $('#{$this->id} li.nav-li').removeClass('active');
    $(this).addClass('active');
});
JS
            );
        }

        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'items'      => $this->items,
            'attributes' => $this->formatHtmlAttributes(),
            'actives'    => $this->actives,
        ];
    }

    protected function formatItem($k, $item)
    {
        if ($k === self::DROPDOWN_FLAG_KEY) {
            return $item;
        }

        if ($builder = $this->builder) {
            $item = $builder($item, $k);
        }

        if (strpos($item, '</li>')) {
            return $item;
        }

        $active = $this->active === $k ? 'active' : '';

        $item = strpos($item, '</a>') ? $item : "<a href='javascript:void(0)'>$item</a>";

        return "<li class='nav-li $active'>$item</li>";
    }
}
