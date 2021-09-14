<?php

namespace Tests\Browser\Components\Form\Field;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;

class Select2 extends Component
{
    protected $selector;

    public function __construct($selector = null)
    {
        $this->selector = $selector;
    }

    /**
     * 获取组件的 root selector.
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
        $browser
            ->assertVisible($this->selector())
            ->assertVisible('@container');
    }

    /**
     * 读取组件的元素快捷方式.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@container' => '.select2',
        ];
    }

    /**
     * 选中下拉选框.
     *
     * @param  Browser  $browser
     * @param  mixed  $value
     * @return Browser
     */
    public function choose(Browser $browser, $value)
    {
        $browser->script(
            <<<JS
$('{$this->formatSelector($browser)}').val('{$value}').change();
JS
        );

        return $browser;
    }
}
