<?php

namespace Tests\Browser\Components\Grid;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;

/**
 * 行选择器.
 */
class RowSelector extends Component
{
    /**
     * 获取组件的 root selector.
     *
     * @return string
     */
    public function selector()
    {
        return '@container';
    }

    /**
     * 浏览器包含组件的断言
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
//        $browser->assertVisible('table:visible thead th .checkbox-grid');
    }

    /**
     * 读取组件的元素快捷方式.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@container' => '#grid-table',
            '@all' => 'input.select-all',
            '@item' => 'input.grid-row-checkbox',
        ];
    }

    /**
     * 选中.
     *
     * @param  Browser  $browser
     * @param  string|array  $value
     * @return Browser
     */
    public function choose(Browser $browser, $value)
    {
        foreach ((array) $value as $v) {
            $browser->script(
                <<<JS
setTimeout(function () {
    $('{$this->formatSelector($browser, '@item')}[data-id="{$v}"]').prop('checked', true);
}, 10)
JS
            );
        }

        return $browser;
    }

    /**
     * 选中所有.
     *
     * @param  Browser  $browser
     * @return Browser
     */
    public function selectAll(Browser $browser)
    {
        $browser->script("Dcat.ready(
            setTimeout(function () {
                $('{$this->formatSelector($browser)} .select-all').first().click()
            }, 10)
        );");

        return $browser;
    }
}
