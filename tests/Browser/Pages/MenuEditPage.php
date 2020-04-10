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
        return admin_base_path("auth/menu/{$this->id}/edit");
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSeeText(__('admin.menu'))
            ->assertSeeText(__('admin.edit'))
            ->assertSeeText(__('admin.list'))
            ->assertSeeText(__('admin.delete'))
            ->scrollToBottom()
            ->assertSeeText(__('admin.submit'))
            ->assertSeeText(__('admin.reset'))
            ->is(new MenuEditForm($this->id));
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
