<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\TestCase;

/**
 * 首页功能测试.
 *
 * @group index
 */
class IndexTest extends TestCase
{
    public function testIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('/'))
                ->assertSee('Administrator')
                ->assertSee('Dashboard')
                ->assertSee('Description...')
                ->assertSee('New Users')
                ->assertSee('New Devices')
                ->assertSee('Tickets')
                ->assertSee(__('admin.documentation'))
                ->assertSee(__('admin.extensions'))
                ->assertSee(__('admin.demo'))
                ->assertSee('GITHUB');
        });
    }

    public function testClickMenu()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('/'))
                ->within('.main-menu-content', function (Browser $browser) {
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
                        ->clickLink('Helpers')
                        ->whenTextAvailable('Extensions', 2)
                        ->clickLink('Extensions')
                        ->assertPathIs(test_admin_path('helpers/extensions'))
                        ->clickLink('Scaffold')
                        ->assertPathIs(test_admin_path('helpers/scaffold'))
                        ->clickLink('Icons')
                        ->assertPathIs(test_admin_path('helpers/icons'));
                });
        });
    }
}
