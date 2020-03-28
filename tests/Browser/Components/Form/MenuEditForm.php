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
        $browser->assertSeeText(__('admin.submit'))
            ->assertSeeText(__('admin.reset'))
            ->within('@form', function (Browser $browser) {
                $browser
                    ->assertSeeText('ID')
                    ->assertSeeText(__('admin.parent_id'))
                    ->assertSeeText(__('admin.title'))
                    ->assertSeeText(__('admin.icon'))
                    ->assertSeeText(__('admin.uri'))
                    ->assertSeeText(__('admin.roles'))
                    ->assertSeeText(__('admin.permission'))
                    ->assertSeeText(__('admin.created_at'))
                    ->assertSeeText(__('admin.updated_at'))
                    ->assertSeeText(__('admin.selectall'))
                    ->assertSeeText(__('admin.expand'))
                    ->hasInput('title')
                    ->hasInput('icon')
                    ->hasInput('uri')
                    ->is(new Tree('permissions'))
                    ->is(new Select2('select[name="parent_id"]'))
                    ->is(new MultipleSelect2('select[name="roles[]"]'));

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
