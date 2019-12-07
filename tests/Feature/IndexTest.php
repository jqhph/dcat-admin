<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        $this->visit('admin/')
            ->see('Dashboard')
            ->see('Description...')

            ->see('Environment')
            ->see('PHP version')
            ->see('Laravel version')

            ->see('Extensions')

            ->see('Dependencies')
            ->see('php')
            ->see('laravel/framework');
    }

    public function testClickMenu()
    {
        $this->visit('admin/')
            ->click('Users')
            ->seePageis('admin/auth/users')
            ->click('Roles')
            ->seePageis('admin/auth/roles')
            ->click('Permission')
            ->seePageis('admin/auth/permissions')
            ->click('Menu')
            ->seePageis('admin/auth/menu')
            ->click('Operation log')
            ->seePageis('admin/auth/logs')
            ->click('Extensions')
            ->seePageis('admin/helpers/extensions')
            ->click('Scaffold')
            ->seePageis('admin/helpers/scaffold')
            ->click('Routes')
            ->seePageis('admin/helpers/routes')
            ->click('Icons')
            ->seePageis('admin/helpers/icons');
    }
}
