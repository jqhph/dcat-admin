<?php

namespace Tests\Browser\Cases;

use Dcat\Admin\Admin;
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
            $browser->visit(admin_base_path('auth/login'))
                ->assertSeeText(__('admin.login'));
        });
    }

    public function testVisitWithoutLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(admin_base_path('/'))
                ->assertPathIs(admin_base_path('auth/login'))
                ->assertGuest('admin');
        });
    }

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $credentials = ['username' => 'admin', 'password' => 'admin'];

            $browser->visit(admin_base_path('auth/login'))
                ->assertPathIs(admin_base_path('auth/login'))
                ->assertSeeText(__('admin.login'))
                ->type('username', $credentials['username'])
                ->type('password', $credentials['password'])
                ->press(__('admin.login'))
                ->waitForLocation(admin_base_path('/'), 3)
                ->assertPathIs(admin_base_path('/'))
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
                    ->clickLink($this->translateMenuTitle('Admin'));
//                    ->waitForText($this->translateMenuTitle('Users'), 1)
//                    ->waitForText($this->translateMenuTitle('Roles'), 1)
//                    ->waitForText($this->translateMenuTitle('Permission'), 1)
//                    ->waitForText($this->translateMenuTitle('Operation log'), 1)
//                    ->waitForText($this->translateMenuTitle('Menu'), 1);
            });
        });
    }

    /**
     * 翻译菜单标题.
     *
     * @param $title
     *
     * @return string
     */
    protected function translateMenuTitle($title)
    {
        return Admin::menu()->translate($title);
    }

    public function testLogout()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(admin_base_path('auth/logout'))
                ->assertPathIs(admin_base_path('auth/login'))
                ->assertGuest('admin');
        });
    }
}
