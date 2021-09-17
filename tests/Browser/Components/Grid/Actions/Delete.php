<?php

namespace Tests\Browser\Components\Grid\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;

/**
 * 删除动作.
 */
class Delete extends Component
{
    /**
     * 获取组件的 root selector.
     *
     * @return string
     */
    public function selector()
    {
        return '';
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
        return [
            '@item' => 'a[data-action="delete"]:visible',
            '@confirm' => '.swal2-confirm',
            '@cancel' => '.swal2-cancel',
        ];
    }

    /**
     * 选中.
     *
     * @param  Browser  $browser
     * @param  string|array  $value
     * @return Browser
     */
    public function delete(Browser $browser, $value)
    {
        $parent = $this->formatSelector($browser, '@item');

        if (is_numeric($value)) {
            $selector = "$('{$parent}').eq($value)";
        } else {
            $value = admin_url($value);

            $selector = "$('{$parent}[data-url=\"{$value}\"]')";
        }

        $browser->script(
            <<<JS
// 如果开启了 responsive 插件，需要隐藏复制的 table，否则会选中副本的删除按钮
$('.sticky-table-header').hide();
{$selector}.click();
JS
        );

        $this->waitForConfirmDialog($browser);
        $this->clickConfirmButton($browser);
        $this->waitForSucceeded($browser);

        return $browser;
    }

    /**
     * 等待确认弹窗.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return \Laravel\Dusk\Browser
     */
    public function waitForConfirmDialog(Browser $browser)
    {
        return $browser->waitForText(__('admin.delete_confirm'), 1);
    }

    /**
     * 等待成功信息.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return \Laravel\Dusk\Browser
     */
    public function waitForSucceeded(Browser $browser)
    {
        $browser->waitForText(__('admin.delete_succeeded'), 2);

        return $browser;
    }

    /**
     * 点击确认删除按钮.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return \Laravel\Dusk\Browser
     */
    public function clickConfirmButton(Browser $browser)
    {
        $browser->script("$('{$this->formatSelector($browser, '@confirm')}').first().click()");

        return $browser;
    }
}
