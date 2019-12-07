<?php

namespace Tests\Feature;

use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    public function testTransField()
    {
        $this->visit('admin/tests/users');

        $this->load();

        $this->assertEquals('user', admin_controller_slug());

        $this->assertEquals(admin_trans_field('username'), '用户名');
        $this->assertEquals(admin_trans_field('profile.postcode'), '邮政编码');

        $this->assertEquals(admin_trans_field('value'), 'value');
        $this->assertEquals(admin_trans_field('profile.value'), 'value');
    }

    public function testTransLabel()
    {
        $this->visit('admin/tests/users');

        $this->load();

        $this->assertEquals('user', admin_controller_slug());

        $this->assertEquals(admin_trans_label('user'), '用户');
    }

    public function testTransGlobal()
    {
        $this->visit('admin/tests/users');

        $this->load();

        $this->assertEquals('user', admin_controller_slug());

        $this->assertEquals(admin_trans_field('id'), 'ID');
        $this->assertEquals(admin_trans_field('profile.address'), '地址');

        $this->assertEquals(admin_trans_label('List'), '列表');

        $this->assertEquals(admin_trans_label('Create'), 'Create');
    }

    protected function load()
    {
        $loader = new FileLoader(app('files'), __DIR__.'/../lang');

        $translator = new Translator($loader, 'en');

        app()->instance('translator', $translator);
    }
}
