<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form\MenuCreationForm;

class MenuPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return test_admin_path('auth/menu');
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSee(__('admin.expand'))
            ->assertSee(__('admin.collapse'))
            ->assertSee(__('admin.save'))
            ->assertSee(__('admin.new'))
            ->whenAvailable('@tree', function (Browser $browser) {
                $browser->assertSee('Menu')
                    ->assertSee('Index')
                    ->assertSee('Admin')
                    ->assertSee('Users')
                    ->assertSee('Roles')
                    ->assertSee('Permission')
                    ->assertSee('Menu')
                    ->assertSee('Operation log');
            }, 1)
            ->within('@form', function (Browser $browser) {
                $browser->assertSee(__('admin.parent_id'))
                    ->assertSee(__('admin.title'))
                    ->assertSee(__('admin.icon'))
                    ->assertSee(__('admin.uri'))
                    ->assertSee(__('admin.roles'))
                    ->assertSee(__('admin.permission'))
                    ->assertSee(__('admin.selectall'))
                    ->assertSee(__('admin.expand'))
                    ->assertSelected('parent_id', 0)
                    ->hasInput('title')
                    ->hasInput('icon')
                    ->hasInput('uri')
                    ->assertButtonEnabled(__('admin.submit'))
                    ->assertButtonEnabled(__('admin.reset'));
            });
    }

    /**
     * 创建
     *
     * @param Browser $browser
     * @param array $input
     *
     * @return Browser
     */
    public function newMenu(Browser $browser, array $input)
    {
        return $browser->within(new MenuCreationForm(), function (Browser $browser) use ($input) {
            $browser->fill($input);

            $browser->pressAndWaitFor(__('admin.submit'), 2);
            $browser->waitForLocation($this->url(), 2);
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
            '@tree' => '.dd',
            '@form' => 'form[method="POST"]',
        ];
    }
}
