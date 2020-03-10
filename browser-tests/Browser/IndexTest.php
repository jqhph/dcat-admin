<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\TestCase;

/**
 * @group index
 */
class IndexTest extends TestCase
{
    public function testIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('/'))
                ->assertSee('Dashboard')
                ->assertSee('Description...')
                ->assertSee('Environment')
                ->assertSee('PHP version')
                ->assertSee('Laravel version')
                ->assertSee('Extensions')
                ->assertSee('Dependencies')
                ->assertSee('php')
                ->assertSee('laravel/framework');
        });
    }

    public function testClickMenu()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('/'))
                ->within('.main-sidebar', function (Browser $browser) {
                    $browser
                        ->clickLink('Admin')
                        ->whenTextAvailable('Users', 2)
                        ->clickLink('Users')
                        ->assertPathIs(test_admin_path('auth/users'))
                        ->clickLink('Roles')
                        ->assertPathIs(test_admin_path('auth/roles'))
                        ->clickLink('Permission')
                        ->assertPathIs(test_admin_path('auth/permissions'))
                        ->clickLink('Menu')
                        ->assertPathIs(test_admin_path('auth/menu'))
                        ->clickLink('Operation log')
                        ->assertPathIs(test_admin_path('auth/logs'))
                        ->clickLink('Extensions')
                        ->assertPathIs(test_admin_path('auth/extensions'))
                        ->clickLink('Scaffold')
                        ->assertPathIs(test_admin_path('auth/scaffold'))
                        ->clickLink('Routes')
                        ->assertPathIs(test_admin_path('auth/routes'))
                        ->clickLink('Icons')
                        ->assertPathIs(test_admin_path('auth/icons'));
                });
        });
    }
}
