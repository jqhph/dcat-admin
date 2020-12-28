<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form\Field\HasMany;

class PainterCreatePage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return admin_base_path('tests/painters/create');
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->with('@form', function (Browser $browser) {
                $browser->assertSeeText('Paintings')
                    ->scrollToBottom()
                    ->with(new HasMany('paintings'), function (Browser $browser) {
                        // 点击新增
                        $browser->add();
                        // 点击删除
                        $browser->removeLast();
                    });
            });
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@form' => 'form[method="POST"]',
        ];
    }

    /**
     * 注入表单.
     *
     * @param Browser $browser
     * @param array $input
     *
     * @return Browser
     */
    public function fill(Browser $browser, array $input)
    {
        return $browser->with('@form', function (Browser $browser) use ($input) {
            $inputKeys = [
                'username',
                'bio',
            ];

            foreach ($input as $key => $value) {
                if (in_array($key, $inputKeys, true)) {
                    $browser->type($key, $value);

                    continue;
                }

                if ($key === 'paintings') {
                    $browser->within(new HasMany($key), function (Browser $browser) use ($value) {
                        foreach ($value as $input) {
                            $browser->add();

                            $browser->withLastFormGroup(function (Browser $browser) use ($input) {
                                foreach ($input as $k => $v) {
                                    $browser->fillFieldValue($k, $v);
                                }
                            });
                        }
                    });
                }
            }
        });
    }

    /**
     * 提交表单.
     *
     * @param Browser $browser
     *
     * @return Browser
     */
    public function submit(Browser $browser)
    {
        return $browser->with('@form', function (Browser $browser) {
            $browser->scrollToTop();
            $browser->press(__('admin.submit'));
            $browser->waitForTextInBody(__('admin.save_succeeded'), 2);
            $browser->waitForLocation(admin_base_path('tests/painters'), 1);
        });
    }
}
