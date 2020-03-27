<?php

namespace Tests\Browser\Components\Grid;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;

/**
 * 批量操作.
 */
class BatchActions extends Component
{
    protected $gridName;

    protected $prefix;

    public function __construct($gridName = '')
    {
        $this->gridName = $gridName;
        $this->prefix = $gridName ? $gridName.'-' : '';
    }

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
    }

    /**
     * 读取组件的元素快捷方式.
     *
     * @return array
     */
    public function elements()
    {
        $container = ".{$this->prefix}grid-select-all-btn";

        return [
            '@container' => $container,
            '@btn' => '.btn',
            '@menu' => '.dropdown-menu',
            '@item' => '.dropdown-menu .dropdown-item',
        ];
    }

    /**
     * 判断按钮是否已显示.
     *
     * @param Browser $browser
     * @param null $number
     *
     * @return Browser
     */
    public function shown(Browser $browser, $number = null)
    {
        if ($number) {
            $browser->waitForText(str_replace('{n}', $number, __('admin.grid_items_selected')), 1);
        }

        $browser->whenElementAvailable('@btn', 1);

        return $browser;
    }

    /**
     * 显示菜单.
     *
     * @param Browser $browser
     * @param int     $seconds
     *
     * @return Browser
     */
    public function open(Browser $browser)
    {
        $this->shown($browser);

        $browser->script(
            <<<JS
$('{$this->formatSelector($browser)}').addClass('show');
JS
        );

        $browser->whenElementAvailable('@menu', 1);

        return $browser;
    }

    /**
     * 关闭菜单.
     *
     * @param Browser $browser
     * @param int     $seconds
     *
     * @return Browser
     */
    public function close(Browser $browser)
    {
        $this->shown($browser);

        $browser->script(
            <<<JS
$('{$this->formatSelector($browser)}').removeClass('show');
JS
        );

        return $browser;
    }

    /**
     * 点击选项.
     *
     * @param  Browser $browser
     * @param  string  $value
     *
     * @return Browser
     */
    public function choose(Browser $browser, $value)
    {
        $browser->with('@menu', function (Browser $browser) use ($value) {
            $browser->clickLink($value);
        });

        return $browser;
    }
}
