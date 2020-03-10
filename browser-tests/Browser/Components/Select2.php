<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Select2 extends BaseComponent
{
    protected $selector;

    public function __construct($selector = null)
    {
        $this->selector = $selector;
    }

    /**
     * 获取组件的 root selector
     *
     * @return string
     */
    public function selector()
    {
        return $this->selector;
    }

    /**
     * 浏览器包含组件的断言
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertHidden($this->selector())
            ->assertVisible('@container');
    }

    /**
     * 读取组件的元素快捷方式
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@container' => '.select2'
        ];
    }

    /**
     * 选中下拉选框
     *
     * @param  Browser  $browser
     * @param  mixed    $value
     *
     * @return void
     */
    public function choose($browser, $value)
    {
        $browser->script(
            <<<JS
$('{$this->selector()}').val('{$value}').change();
JS
        );
    }
}
