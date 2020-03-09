<?php

namespace Dcat\Admin\Tests\Browser;

use Laravel\Dusk\Browser;
use Dcat\Admin\Tests\DuskTestCase;

/**
 * @group login
 */
class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin')
                ->assertSee('Dcat Admin');
        });
    }
}
