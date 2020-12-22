<?php

namespace Tests\Browser\Cases;

use Dcat\Admin\Models\Menu;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form\Field\MultipleSelect2;
use Tests\Browser\Components\Form\Field\Select2;
use Tests\Browser\Components\Form\MenuEditForm;
use Tests\Browser\Pages\MenuEditPage;
use Tests\Browser\Pages\MenuPage;
use Tests\TestCase;

/**
 * 菜单管理功能测试.
 *
 * @group menu
 */
class MenuTest extends TestCase
{
    public function testMenuIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new MenuPage());
        });
    }

    public function testAddMenu()
    {
        $this->browse(function (Browser $browser) {
            $item = [
                'parent_id'   => '0',
                'title'       => 'Test',
                'uri'         => 'test',
                'icon'        => 'fa-user',
                'roles'       => [1],
                'permissions' => [4, 5],
            ];

            $browser
                ->visit(new MenuPage())
                ->newMenu($item)
                ->waitForText(__('admin.save_succeeded'), 2);

            $newMenuId = Menu::query()->orderByDesc('id')->first()->id;

            // 检测是否写入数据库
            $this->assertDatabase($newMenuId, $item);
            $this->assertEquals(8, Menu::count());
        });
    }

    public function testDeleteMenu()
    {
        $this->delete('admin/auth/menu/8');
        $this->assertEquals(7, Menu::count());
    }

    public function testEditMenu()
    {
        $this->browse(function (Browser $browser) {
            $newTitle = 'blablabla';

            $browser->visit(new MenuEditPage(1));

            $browser->script("$(\"input[name='title']\").val(\"{$newTitle}\")");

            $browser->press(__('admin.submit'))
                ->waitForLocation(admin_base_path('auth/menu'), 2);

            $this->seeInDatabase(config('admin.database.menu_table'), ['title' => $newTitle])
                ->assertEquals(7, Menu::count());
        });
    }

    public function testEditMenuParent()
    {
        $this->browse(function (Browser $browser) {
            $id = 5;

            $browser->visit(new MenuEditPage($id))
                ->within(new Select2('select[name="parent_id"]'), function ($browser) use ($id) {
                    $browser->choose($id);
                })
                ->press(__('admin.submit'))
                ->waitForText('500 Internal Server Error', 2);
        });
    }

    public function testQuickEditMenu()
    {
        $this->browse(function (Browser $browser) {
            $id = 5;

            $updates = [
                'title'       => 'balabala',
                'icon'        => 'fa-list',
                'parent_id'   => 0,
                'roles'       => 1,
                'permissions' => [4, 5, 6],
            ];

            $browser->visit(new MenuPage())
                ->within(sprintf('li[data-id="%d"]', $id), function (Browser $browser) {
                    $browser->click('.tree-quick-edit');
                })
                ->whenAvailable('.layui-layer-page', function (Browser $browser) use ($id, $updates) {
                    $browser->whenElementAvailable(new MenuEditForm($id), function (Browser $browser) use ($updates) {
                        // 检测表单
                        $browser->fill($updates);
                    }, 2)
                        ->assertSeeText(__('admin.edit'))
                        ->click('div')
                        ->whenElementAvailable(new MultipleSelect2('select[name="roles[]"]'), function (Browser $browser) {
                            $browser->choose(1);
                        }, 2)
                        ->clickLink(__('admin.submit'));
                }, 2)
                ->waitForText(__('admin.save_succeeded'), 3)
                ->waitForLocation(admin_base_path('auth/menu'), 3)
                ->waitForText('balabala', 2);

            // 检测是否写入数据库
            $this->assertDatabase($id, $updates);
        });
    }

    private function assertDatabase($id, $updates)
    {
        $roles = $updates['roles'];
        $permissions = $updates['permissions'];

        unset($updates['roles'], $updates['permissions']);

        // 检测是否写入数据库
        $this->seeInDatabase(config('admin.database.menu_table'), $updates);

        foreach ((array) $roles as $role) {
            $this->seeInDatabase(
                config('admin.database.role_menu_table'),
                ['role_id' => $role, 'menu_id' => $id]
            );
        }
        foreach ((array) $permissions as $permission) {
            $this->seeInDatabase(
                config('admin.database.permission_menu_table'),
                ['permission_id' => $permission, 'menu_id' => $id]
            );
        }
    }
}
