<?php

namespace Dcat\Admin\Tests\Feature;

use Dcat\Admin\Tests\TestCase;

/**
 * @group user-show
 */
class UserShowTest extends TestCase
{
    public function test()
    {
        $this->seedsTable(10);

        $this->visit('admin/tests/users/1');

        $this->assertResponseStatus(200);

        $this->see('Detail')
            ->see('ID')
            ->see('username')
            ->see('email')
            ->see('full name')
            ->see('postcode')
            ->see('tags');

        $this->seeInElement('a[href="http://localhost:8000/admin/tests/users"]', 'List')
            ->seeInElement('a[href="http://localhost:8000/admin/tests/users/1/edit"]', 'Edit');

        $this->assertCount(1, $this->crawler()->filter('hr'));
    }

    protected function seedsTable($count = 100)
    {
        factory(\Dcat\Admin\Tests\Models\User::class, $count)
            ->create()
            ->each(function ($u) {
                $u->profile()->save(factory(\Dcat\Admin\Tests\Models\Profile::class)->make());
                $u->tags()->saveMany(factory(\Dcat\Admin\Tests\Models\Tag::class, 5)->make());
            });
    }
}
