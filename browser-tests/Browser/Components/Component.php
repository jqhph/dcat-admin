<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    /**
     * @param Browser $browser
     *
     * @return string
     */
    public function parentSelector(Browser $browser)
    {
        return str_replace($this->selector(), '', $browser->resolver->prefix);
    }

    /**
     * 获取完整的css选择器
     *
     * @param Browser $browser
     * @param string $selector
     *
     * @return string
     */
    public function formatSelector(Browser $browser, $selector = null)
    {
        return $this->parentSelector($browser).' '.($selector ?: $this->selector());
    }
}
