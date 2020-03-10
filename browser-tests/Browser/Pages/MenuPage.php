<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

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
        $browser->assertSee('Expand')
            ->assertSee('Collapse')
            ->assertSee('Save')
            ->assertSee('New')
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
                $browser->assertSee('Parent')
                    ->assertSee('Title')
                    ->assertSee('Icon')
                    ->assertSee('URI')
                    ->assertSee('Roles')
                    ->assertSee('Permission')
                    ->assertSee('Select all')
                    ->assertSelected('parent_id', 0)
                    ->hasInput('title')
                    ->hasInput('icon')
                    ->hasInput('uri')
                    ->assertButtonEnabled('Submit')
                    ->assertButtonEnabled('Reset');
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
