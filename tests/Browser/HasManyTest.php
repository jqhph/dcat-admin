<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form\Field\HasMany;
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
                ->assertPathIs(test_admin_path('tests/painters/create'))
                ->with('form[method="POST"]', function (Browser $browser) {
                    $browser->assertSeeText('Paintings')
                        ->with(new HasMany('paintings'), function (Browser $browser) {
                            // 点击新增
                            $browser->add();
                            // 点击删除
                            $browser->removeLast();
                        });
                });
        });
    }
}
