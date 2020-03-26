<?php

namespace Tests\Browser;

use Dcat\Admin\Models\OperationLog;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Grid\BatchActions;
use Tests\Browser\Components\Grid\Actions\Delete;
use Tests\Browser\Components\Grid\RowSelector;
use Tests\TestCase;

/**
 * 操作日志功能测试.
 *
 * @group log
 */
class OperationLogTest extends TestCase
{
    public function testOperationLogIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(test_admin_path('auth/menu'))
                ->assertPathIs(test_admin_path('auth/menu'))
                ->visit(test_admin_path('auth/logs'))
                ->assertSee(__('admin.operation_log'))
                ->assertSee(__('admin.list'))
                ->assertSee(__('admin.refresh'))
                ->assertSee(__('admin.filter'))
                ->assertSee('ID')
                ->assertSee(__('admin.user'))
                ->assertSee(__('admin.method'))
                ->assertSee(__('admin.uri'))
                ->assertSee('IP')
                ->assertSee(__('admin.input'))
                ->assertSee(__('admin.created_at'))
                ->assertSee(__('admin.action'))
                ->waitForText(__('admin.responsive.display'), 2)
                ->assertSee(__('admin.responsive.display_all'));
        });
    }

    public function testGenerateLogs()
    {
        $this->browse(function (Browser $browser) {
            $table = config('admin.database.operation_log_table');

            $browser->visit(test_admin_path('auth/menu'))
                ->assertPathIs(test_admin_path('auth/menu'))
                ->visit(test_admin_path('auth/users'))
                ->assertPathIs(test_admin_path('auth/users'))
                ->visit(test_admin_path('auth/permissions'))
                ->assertPathIs(test_admin_path('auth/permissions'))
                ->visit(test_admin_path('auth/roles'))
                ->assertPathIs(test_admin_path('auth/roles'))
                ->visit(test_admin_path('auth/logs'))
                ->assertPathIs(test_admin_path('auth/logs'));

            $this->seeInDatabase($table, ['path' => trim(test_admin_path('auth/menu'), '/'), 'method' => 'GET'])
                ->seeInDatabase($table, ['path' => trim(test_admin_path('auth/users'), '/'), 'method' => 'GET'])
                ->seeInDatabase($table, ['path' => trim(test_admin_path('auth/permissions'), '/'), 'method' => 'GET'])
                ->seeInDatabase($table, ['path' => trim(test_admin_path('auth/roles'), '/'), 'method' => 'GET']);
        });

        $this->assertSame(4, OperationLog::count());
    }

    public function testDeleteLogs()
    {
        $this->browse(function (Browser $browser) {
            $table = config('admin.database.operation_log_table');

            $this->assertEquals(0, OperationLog::count());

            $browser->visit(test_admin_path('auth/users'));
            $this->seeInDatabase($table, ['path' => trim(test_admin_path('auth/users'), '/'), 'method' => 'GET']);

            $browser->visit(test_admin_path('auth/logs'))
                ->assertPathIs(test_admin_path('auth/logs'))
                ->pause(500);

            $browser->with(new Delete(), function (Browser $browser) {
                $browser->delete(0);
            });

            $this->assertEquals(0, OperationLog::count());
        });
    }

    public function testDeleteMultipleLogs()
    {
        $this->browse(function (Browser $browser) {
            $table = config('admin.database.operation_log_table');

            $browser->visit(test_admin_path('auth/menu'))
                ->visit(test_admin_path('auth/users'))
                ->visit(test_admin_path('auth/permissions'))
                ->visit(test_admin_path('auth/roles'));

            $number = 4;

            $this->seeInDatabase($table, ['path' => trim(test_admin_path('auth/menu'), '/'), 'method' => 'GET'])
                ->seeInDatabase($table, ['path' => trim(test_admin_path('auth/users'), '/'), 'method' => 'GET'])
                ->seeInDatabase($table, ['path' => trim(test_admin_path('auth/permissions'), '/'), 'method' => 'GET'])
                ->seeInDatabase($table, ['path' => trim(test_admin_path('auth/roles'), '/'), 'method' => 'GET'])
                ->assertEquals($number, OperationLog::count());

            $browser->visit(test_admin_path('auth/logs'))
                ->assertPathIs(test_admin_path('auth/logs'));

            $browser->with(new RowSelector(), function (Browser $browser) {
                $browser->selectAll();
            });

            $browser->with(new BatchActions(), function (Browser $browser) use ($number) {
                $browser->shown($number);
                $browser->open();
                $browser->choose(__('admin.delete'));
            });

//            $browser->waitForText(__('admin.delete_confirm'), 3);
//            $browser->script("$('.swal2-confirm').first().click()");
//            $browser->waitForText(__('admin.delete_succeeded'), 3);
//
//            $this->notSeeInDatabase($table, ['path' => trim(test_admin_path('auth/menu'), '/'), 'method' => 'GET'])
//                ->notSeeInDatabase($table, ['path' => trim(test_admin_path('auth/users'), '/'), 'method' => 'GET'])
//                ->notSeeInDatabase($table, ['path' => trim(test_admin_path('auth/permissions'), '/'), 'method' => 'GET'])
//                ->notSeeInDatabase($table, ['path' => trim(test_admin_path('auth/roles'), '/'), 'method' => 'GET'])
//                ->assertEquals(0, OperationLog::count());
        });
    }
}
