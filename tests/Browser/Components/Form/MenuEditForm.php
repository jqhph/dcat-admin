<?php

namespace Tests\Browser\Components\Form;

use Dcat\Admin\Models\Menu;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\Form\Field\MultipleSelect2;
use Tests\Browser\Components\Form\Field\Select2;
use Tests\Browser\Components\Form\Field\Tree;

class MenuEditForm extends MenuCreationForm
{
    protected $id;
    protected $selector;

    public function __construct($id = null, $selector = 'form[method="POST"]')
    {
        if ($id && ! is_numeric($id)) {
            $selector = $id;
            $id = null;
        }

        $this->id = $id;
        $this->selector = $selector;
    }

    /**
     * 浏览器包含组件的断言
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSee(__('admin.submit'))
            ->assertSee(__('admin.reset'))
            ->within('@form', function (Browser $browser) {
                $browser
                    ->assertSee('ID')
                    ->assertSee(__('admin.parent_id'))
                    ->assertSee(__('admin.title'))
                    ->assertSee(__('admin.icon'))
                    ->assertSee(__('admin.uri'))
                    ->assertSee(__('admin.roles'))
                    ->assertSee(__('admin.permission'))
                    ->assertSee(__('admin.created_at'))
                    ->assertSee(__('admin.updated_at'))
                    ->assertSee(__('admin.selectall'))
                    ->assertSee(__('admin.expand'))
                    ->hasInput('title')
                    ->hasInput('icon')
                    ->hasInput('uri')
                    ->assert(new Tree('permissions'))
                    ->assert(new Select2('select[name="parent_id"]'))
                    ->assert(new MultipleSelect2('select[name="roles[]"]'));

                if (! $this->id) {
                    return;
                }

                $menu = Menu::find($this->id);
                if ($menu) {
                    $browser->assertSelected('parent_id', $menu->parent_id);
                }
            });
    }
}
