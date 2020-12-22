<?php

namespace Tests\Browser\Components\Form\Field;

use Dcat\Admin\Form\Field;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;
use Tests\PHPUnit;

class HasMany extends Component
{
    protected $relation;

    public function __construct($relation = null)
    {
        $this->relation = $relation;
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
        $browser->assertVisible('@container')
            ->assertVisible('@add')
            ->assertVisible('@forms');
    }

    /**
     * 读取组件的元素快捷方式.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@container' => '.has-many-'.$this->relation,
            '@add' => '.add',
            '@remove' => '.remove',
            '@forms' => ".has-many-{$this->relation}-forms",
            '@group' => ".has-many-{$this->relation}-forms .fields-group",
        ];
    }

    /**
     * 点击添加按钮.
     *
     * @param Browser $browser
     *
     * @return int
     */
    public function add(Browser $browser)
    {
        $browser->script(
            <<<JS
$('{$this->formatSelector($browser, '@add')}').click();
JS
        );

        // 获取最后一个添加的表单组
        $index = $this->getLastFormGroupIndex($browser);

        $browser->scrollToBottom();

        // 验证表单组是否存在
        $this->withFormGroup($browser, $index);

        return $index;
    }

    /**
     * 获取最后一组新增的表单索引.
     *
     * @param Browser $browser
     *
     * @return int|null
     */
    public function getLastFormGroupIndex(Browser $browser)
    {
        // 获取添加的表单个数
        $length = $browser->script(
            <<<JS
return $('{$this->formatSelector($browser, '@group')}').length;
JS
        );

        return $length[0] ?? null;
    }

    /**
     * @param Browser $browser
     * @param \Closure $callback
     *
     * @return Browser
     */
    public function withLastFormGroup(Browser $browser, \Closure $callback = null)
    {
        return $this->withFormGroup($browser, $this->getLastFormGroupIndex($browser), $callback);
    }

    /**
     * 检测表单组.
     *
     * @param Browser $browser
     * @param \Closure $callback
     *
     * @return Browser
     */
    public function withFormGroup(Browser $browser, $index, ?\Closure $callback = null)
    {
        // 添加的表单组容器选择器
        $groupSelector = $this->formatGroupSelector($browser, $index);

        $browser->assertVisible($groupSelector);
        $browser->assertVisible("{$groupSelector} {$this->formatSelectorWithoutPrefix($browser, '@remove')}");

        return $callback ? $browser->extend($groupSelector, $callback) : $browser;
    }

    /**
     * @param Browser $browser
     * @param int $index
     *
     * @return string
     */
    protected function formatGroupSelector(Browser $browser, $index)
    {
        return "{$this->formatSelectorWithoutPrefix($browser, '@group')}:nth-of-type({$index})";
    }

    /**
     * 移除表单.
     *
     * @param Browser $browser
     * @param int $index
     *
     * @return Browser
     */
    public function remove(Browser $browser, $index)
    {
        $this->withFormGroup($browser, $index, function (Browser $browser) {
            $browser->script(
                <<<JS
$('{$this->formatSelector($browser, $this->elements()['@remove'])}').click();
JS
            );
        });

        return $browser->assertHidden($this->formatGroupSelector($browser, $index));
    }

    /**
     * 移除最后一个表单.
     *
     * @param Browser $browser
     *
     * @return Browser
     */
    public function removeLast(Browser $browser)
    {
        return $this->remove($browser, $this->getLastFormGroupIndex($browser));
    }

    /**
     * 获取hasMany内表单字段值.
     *
     * @param Browser $browser
     * @param string $field
     * @param string $value
     *
     * @return string|null
     */
    public function assertFormGroupInputValue(Browser $browser, $field, $value)
    {
        $input = $browser->script(
                <<<JS
return $('{$browser->resolver->format('.'.Field::FIELD_CLASS_PREFIX.$field)}').val();
JS
        )[0] ?? null;

        PHPUnit::assertEquals($input, $value);
    }
}
