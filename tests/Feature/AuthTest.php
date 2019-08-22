<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    protected $login = false;

    public function testLoginPage()
    {
        $this->visit('admin/auth/login')
            ->see('login');
    }

    public function testVisitWithoutLogin()
    {
        $this->visit('admin')
            ->dontSeeIsAuthenticated('admin')
            ->seePageIs('admin/auth/login');
    }

    public function testLogin()
    {
        $credentials = ['username' => 'admin', 'password' => 'admin'];

        $this->visit('admin/auth/login')
            ->see('login')
            ->submitForm('Login', $credentials)
            ->see('dashboard')
            ->seeCredentials($credentials, 'admin')
            ->seeIsAuthenticated('admin')
            ->seePageIs('admin')
            ->see('Dashboard')
            ->see('Description...')

            ->see('Environment')
            ->see('PHP version')
            ->see('Laravel version')

            ->see('Extensions')

            ->see('Dependencies')
            ->see('php')
            ->see('laravel/framework');

        $this
            ->see('<span>Admin</span>')
            ->see('<span>Users</span>')
            ->see('<span>Roles</span>')
            ->see('<span>Permission</span>')
            ->see('<span>Operation log</span>')
            ->see('<span>Menu</span>');
    }

    public function testLogout()
    {
        $this->visit('admin/auth/logout')
            ->seePageIs('admin/auth/login')
            ->dontSeeIsAuthenticated('admin');
    }
}
