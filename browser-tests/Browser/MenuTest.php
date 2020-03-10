<?php

namespace Tests\Browser;

use Dcat\Admin\Models\Menu;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\MultipleSelect2;
use Tests\Browser\Pages\MenuPage;
use Tests\TestCase;

/**
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
            ];

            $roles = [1];

            $browser->visit(new MenuPage())
                ->select('parent_id', $item['parent_id'])
                ->type('title', $item['title'])
                ->type('uri', $item['uri'])
                ->type('icon', $item['icon'])
                ->click('.row')
                ->within(new MultipleSelect2('select[name="roles[]"]'), function (Browser $browser) use ($item, $roles) {
                    $browser->choose($roles);
                })
                ->pressAndWaitFor('Submit')
                ->waitForText(__('admin.save_succeeded'), 2)
                ->assertPathIs(test_admin_path('auth/menu'));

            $newMenuId = Menu::query()->orderByDesc('id')->first()->id;

            $this->seeInDatabase(config('admin.database.menu_table'), $item)
                ->seeInDatabase(config('admin.database.role_menu_table'), ['role_id' => $roles, 'menu_id' => $newMenuId])
                ->assertEquals(8, Menu::count());
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
            $browser->visit(test_admin_path('auth/menu/1/edit'))
                ->assertSee('Menu')
                ->assertSee('Edit')
                ->type('title', 'blablabla')
                ->press('Submit')
                ->waitForLocation(test_admin_path('auth/menu'), 2);

            $this->seeInDatabase(config('admin.database.menu_table'), ['title' => 'blablabla'])
                ->assertEquals(7, Menu::count());
        });
    }
    //
    //public function testEditMenuParent()
    //{
    //    $this->expectException(\Laravel\BrowserKitTesting\HttpException::class);
    //
    //    $this->visit('admin/auth/menu/5/edit')
    //        ->see('Menu')
    //        ->submitForm('Submit', ['parent_id' => 5]);
    //}
}
