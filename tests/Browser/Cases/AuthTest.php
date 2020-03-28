<?php

namespace Tests\Browser\Cases;

use Laravel\Dusk\Browser;
use Tests\TestCase;

/**
 * 鉴权登陆功能测试.
 *
 * @group auth
 */
class AuthTest extends TestCase
{
    protected $login = false;

    public function testLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('auth/login'))
                ->assertSeeText(__('admin.login'));
        });
    }

    public function testVisitWithoutLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('/'))
                ->assertPathIs(test_admin_path('auth/login'))
                ->assertGuest('admin');
        });
    }

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $credentials = ['username' => 'admin', 'password' => 'admin'];

            $browser->visit(test_admin_path('auth/login'))
                ->assertPathIs(test_admin_path('auth/login'))
                ->assertSeeText(__('admin.login'))
                ->type('username', $credentials['username'])
                ->type('password', $credentials['password'])
                ->press(__('admin.login'))
                ->waitForLocation(test_admin_path('/'), 3)
                ->assertPathIs(test_admin_path('/'))
                ->assertSeeText('Administrator')
                ->assertSeeText('Dashboard')
                ->assertSeeText('Description...')
                ->assertSeeText('New Users')
                ->assertSeeText('New Devices')
                ->assertSeeText('Tickets')
                ->assertSeeText((__('admin.documentation')))
                ->assertSeeText((__('admin.extensions')))
                ->assertSeeText((__('admin.demo')))
                ->assertSeeText('GITHUB');

            $browser->within('.main-menu-content', function (Browser $browser) {
                $browser->assertSeeText('Admin')
                    ->clickLink('Admin')
                    ->waitForText('Users', 1)
                    ->waitForText('Roles', 1)
                    ->waitForText('Permission', 1)
                    ->waitForText('Operation log', 1)
                    ->waitForText('Menu', 1);
            });
        });
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('auth/logout'))
                ->assertPathIs(test_admin_path('auth/login'))
                ->assertGuest('admin');
        });
    }
}
