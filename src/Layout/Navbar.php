<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasBuilderEvents;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

class Navbar implements Renderable
{
    use HasBuilderEvents;

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * Navbar constructor.
     */
    public function __construct()
    {
        $this->elements = [
            'left'  => collect(),
            'right' => collect(),
        ];

        $this->callResolving();
    }

    /**
     * @param  string|\Closure|Renderable|Htmlable  $element
     * @return $this
     */
    public function left($element)
    {
        $this->elements['left']->push($element);

        return $this;
    }

    /**
     * @param  string|\Closure|Renderable|Htmlable  $element
     * @return $this
     */
    public function right($element)
    {
        $this->elements['right']->push($element);

        return $this;
    }

    /**
     * @param  string  $part
     * @return mixed
     */
    public function render($part = 'right')
    {
        $this->callComposing($part);

        if (! isset($this->elements[$part]) || $this->elements[$part]->isEmpty()) {
            return '';
        }

        return $this->elements[$part]->map([Helper::class, 'render'])->implode('');
    }
}
