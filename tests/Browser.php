<?php

namespace Tests;

use Laravel\Dusk\Browser as BaseBrowser;
use Laravel\Dusk\Component;
use Laravel\Dusk\ElementResolver;

class Browser extends BaseBrowser
{
    /**
     * @var static
     */
    public $parent;

    /**
     * 作用与 with 方法完全相同，不同的在于此方法可以让下层 Broser 对象继承当前 Component 的方法.
     *
     * @param  string|Component  $selector
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($selector, $callback)
    {
        $browser = new static(
            $this->driver, new ElementResolver($this->driver, $this->resolver->format($selector))
        );

        $browser->parent = $this;

        if ($this->page) {
            $browser->onWithoutAssert($this->page);
        }

        if ($selector instanceof Component) {
            $browser->onComponent($selector, $this->resolver);
        }

        call_user_func($callback, $browser);

        return $this;
    }

    public function __call($method, $parameters)
    {
        $parentComponent = $this->parent ? $this->parent->component : null;

        if ($parentComponent && method_exists($parentComponent, $method)) {
            array_unshift($parameters, $this);

            $parentComponent->{$method}(...$parameters);

            return $this;
        }

        return parent::__call($method, $parameters);
    }
}
