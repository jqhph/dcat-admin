<?php

namespace Tests\Browser\Components\Form;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Component;
use Tests\Browser\Components\Form\Field\MultipleSelect2;
use Tests\Browser\Components\Form\Field\Select2;
use Tests\Browser\Components\Form\Field\Tree;

class MenuCreationForm extends Component
{
    protected $selector;

    public function __construct($selector = 'form[method="POST"]')
    {
        $this->selector = $selector;
    }

    /**
     * 获取组件的 css selector.
     *
     * @return string
     */
    public function selector()
    {
        return '@form';
    }

    /**
     * 浏览器包含组件的断言
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSeeText(__('admin.submit'))
            ->assertSeeText(__('admin.reset'))
            ->within('@form', function (Browser $browser) {
                $browser
                    ->assertSeeText(__('admin.parent_id'))
                    ->assertSeeText(__('admin.title'))
                    ->assertSeeText(__('admin.icon'))
                    ->assertSeeText(__('admin.uri'))
                    ->assertSeeText(__('admin.roles'))
                    ->assertSeeText(__('admin.permission'))
                    ->assertSeeText(__('admin.selectall'))
                    ->assertSeeText(__('admin.expand'))
                    ->hasInput('title')
                    ->hasInput('icon')
                    ->hasInput('uri')
                    //->assertSelected('parent_id', 0)
                    ->is(new Tree('permissions'))
                    ->is(new Select2('select[name="parent_id"]'))
                    ->is(new MultipleSelect2('select[name="roles[]"]'));
            });
    }

    /**
     * 注入表单.
     *
     * @param  Browser  $browser
     * @param  array  $input
     * @return Browser
     */
    public function fill(Browser $browser, array $input)
    {
        $inputKeys = [
            'title',
            'icon',
            'uri',
        ];

        $selectKeys = [
            'parent_id',
        ];

        $multipleSelectKeys = [
            'roles',
        ];

        foreach ($input as $key => $value) {
            if (in_array($key, $inputKeys, true)) {
                $browser->type($key, $value);

                continue;
            }

            if (in_array($key, $selectKeys, true)) {
                $selector = sprintf('select[name="%s"]', $key);
                $browser->within(new Select2($selector), function ($browser) use ($value) {
                    $browser->choose($value);
                });

                continue;
            }

            if (in_array($key, $multipleSelectKeys, true)) {
                $selector = sprintf('select[name="%s[]"]', $key);
                $browser->within(new MultipleSelect2($selector), function ($browser) use ($value) {
                    $browser->choose($value);
                });

                continue;
            }

            if ($key === 'permissions') {
                $browser->within(new Tree($key), function ($browser) use ($value) {
                    $browser->expand();

                    $browser->choose($value);
                });
            }
        }

        return $browser;
    }

    /**
     * 读取组件的元素快捷方式.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@form' => $this->selector,
        ];
    }
}
