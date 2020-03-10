<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\TestCase;

/**
 * @group auth
 */
class AuthTest extends TestCase
{
    protected $login = false;

    public function testLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('auth/login'))
                ->assertSee('Login');
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
                ->assertSee('Login')
                ->type('username', $credentials['username'])
                ->type('password', $credentials['password'])
                ->press('Login')
                ->assertPathIs(test_admin_path('/'))
                ->assertSee('Administrator')
                ->assertSee('Dashboard')
                ->assertSee('Description...')
                ->assertSee('Environment')
                ->assertSee('PHP version')
                ->assertSee('Laravel version')
                ->assertSee('Extensions')
                ->assertSee('Dependencies')
                ->assertSee('php')
                ->assertSee('laravel/framework');

                //->assertAuthenticated('admin');

            $browser->within('.main-sidebar', function (Browser $browser) {
                $browser->assertSee('Admin')
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
