<?php

namespace Tests\Browser\Cases;

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
            $browser->visit(admin_base_path('/'))
                ->assertSeeText('Administrator')
                ->assertSeeText('Dashboard')
                ->assertSeeText('Description...')
                ->assertSeeText('New Users')
                ->assertSeeText('New Devices')
                ->assertSeeText('Tickets')
                ->assertSeeText(__('admin.documentation'))
                ->assertSeeText(__('admin.extensions'))
                ->assertSeeText(__('admin.demo'))
                ->assertSeeText('GITHUB');
        });
    }

    public function testClickMenu()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(admin_base_path('/'))
                ->within('.main-menu-content', function (Browser $browser) {
                    $browser
                        ->clickLink('Admin')
                        ->whenTextAvailable('Users', 2)
                        ->clickLink('Users')
                        ->assertPathIs(admin_base_path('auth/users'))
                        ->clickLink('Roles')
                        ->assertPathIs(admin_base_path('auth/roles'))
                        ->clickLink('Permission')
                        ->assertPathIs(admin_base_path('auth/permissions'))
                        ->clickLink('Menu')
                        ->assertPathIs(admin_base_path('auth/menu'))
                        ->clickLink('Operation log')
                        ->assertPathIs(admin_base_path('auth/logs'))
                        ->clickLink('Helpers')
                        ->whenTextAvailable('Extensions', 2)
                        ->clickLink('Extensions')
                        ->assertPathIs(admin_base_path('helpers/extensions'))
                        ->clickLink('Scaffold')
                        ->assertPathIs(admin_base_path('helpers/scaffold'))
                        ->clickLink('Icons')
                        ->assertPathIs(admin_base_path('helpers/icons'));
                });
        });
    }
}
