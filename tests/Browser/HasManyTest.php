<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\TestCase;

/**
 * 一对多表单功能测试.
 *
 * @group has-many
 */
class HasManyTest extends TestCase
{
    public function testCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('tests/painters/create'))
                ->assertPathIs(test_admin_path('tests/painters/create'));
        });
    }
}
