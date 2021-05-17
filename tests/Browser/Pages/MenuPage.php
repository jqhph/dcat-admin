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
        return admin_base_path('auth/menu');
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSeeText(__('admin.expand'))
            ->assertSeeText(__('admin.collapse'))
            ->assertSeeText(__('admin.save'))
            ->assertSeeText(__('admin.new'))
            ->whenAvailable('@tree', function (Browser $browser) {
                $browser->assertSeeText('Menu')
                    ->assertSeeText('Index')
                    ->assertSeeText('Admin')
                    ->assertSeeText('Users')
                    ->assertSeeText('Roles')
                    ->assertSeeText('Permission')
                    ->assertSeeText('Menu');
            }, 1)
            ->within('@form', function (Browser $browser) {
                $browser->assertSeeText(__('admin.parent_id'))
                    ->assertSeeText(__('admin.title'))
                    ->assertSeeText(__('admin.icon'))
                    ->assertSeeText(__('admin.uri'))
                    ->assertSeeText(__('admin.roles'))
                    ->assertSeeText(__('admin.permission'))
                    ->assertSeeText(__('admin.selectall'))
                    ->assertSeeText(__('admin.expand'))
                    //->assertSelected('parent_id', 0)
                    ->hasInput('title')
                    ->hasInput('icon')
                    ->hasInput('uri')
                    ->assertButtonEnabled(__('admin.submit'))
                    ->assertButtonEnabled(__('admin.reset'));
            });
    }

    /**
     * 创建.
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
