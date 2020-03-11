<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form\MenuEditForm;

class MenuEditPage extends Page
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return test_admin_path("auth/menu/{$this->id}/edit");
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSee(__('admin.menu'))
            ->assertSee(__('admin.edit'))
            ->assertSee(__('admin.list'))
            ->assertSee(__('admin.delete'))
            ->assertSee(__('admin.submit'))
            ->assertSee(__('admin.reset'))
            ->assert(new MenuEditForm($this->id));
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
}
