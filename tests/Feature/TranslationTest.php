<?php

namespace Dcat\Admin\Tests\Feature;

use Dcat\Admin\Tests\TestCase;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * @group trans
 */
class TranslationTest extends TestCase
{
    public function testTransField()
    {
        $this->visit('admin/tests/users')
            ->see('Post code');

        $this->registerTranslator();

        $this->assertSame('user', admin_controller_slug());

        $this->assertSame(admin_trans_field('username'), '用户名');
        $this->assertSame(admin_trans_field('profile.postcode'), '邮政编码');

        $this->assertSame(admin_trans_field('value'), 'value');
        $this->assertSame(admin_trans_field('profile.value'), 'value');
    }

    public function testTransLabel()
    {
        $this->visit('admin/tests/users');

        $this->registerTranslator();

        $this->assertSame('user', admin_controller_slug());

        $this->assertSame(admin_trans_label('user'), '用户');
    }

    public function testTransGlobal()
    {
        $this->visit('admin/tests/users');

        $this->registerTranslator();

        $this->assertSame('user', admin_controller_slug());

        $this->assertSame(admin_trans_field('id'), 'ID');
        $this->assertSame(admin_trans_field('profile.address'), '地址');

        $this->assertSame(admin_trans_label('List'), '列表');

        $this->assertSame(admin_trans_label('Create'), 'Create');
    }

    protected function registerTranslator()
    {
        $loader = new FileLoader(app('files'), __DIR__.'/../resources/lang');

        $translator = new Translator($loader, 'en');

        app()->instance('translator', $translator);
    }
}
