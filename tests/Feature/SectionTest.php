<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Section功能测试.
 *
 * @group section
 */
class SectionTest extends TestCase
{
    protected $login = false;

    public function testInjectValues()
    {
        // view
        admin_inject_section('key1', view('admin-tests::test'));

        $this->assertSame(admin_section('key1'), '<h1>Hello world</h1>');

        // string
        admin_inject_section('key2', 'test');

        $this->assertSame(admin_section('key2'), 'test');

        // callable
        admin_inject_section('key3', function () {
            return view('admin-tests::test');
        });

        $this->assertSame(admin_section('key3'), '<h1>Hello world</h1>');
    }

    public function testOptions()
    {
        admin_inject_section('key1', 'value1');

        admin_inject_section('key1', function ($options) {
            return "previous:{$options->previous},name:{$options->name},age:{$options->age}";
        }, false);

        $this->assertSame(
            admin_section('key1', null, ['name' => 'Mike', 'age' => 18]),
            'previous:value1,name:Mike,age:18'
        );
    }

    public function testAppend()
    {
        // 1 append
        admin_inject_section('key1', 'test1,');
        admin_inject_section('key1', 'test2,');
        admin_inject_section('key1', 'test3,');

        $this->assertSame(admin_section('key1'), 'test1,test2,test3,');

        // 2 overwrite
        admin_inject_section('key2', 'test1,');
        admin_inject_section('key2', 'test2,', false);

        $this->assertSame(admin_section('key2'), 'test2,');

        admin_inject_section('key2', 'test3,', false);

        $this->assertSame(admin_section('key2'), 'test3,');

        // 3 overwrite
        admin_inject_section('key3', 'test1,');
        admin_inject_section('key3', 'test2,', false);
        admin_inject_section('key3', function ($options) {
            return $options->previous.'test3,';
        }, false);
        admin_inject_section('key3', function ($options) {
            return $options->previous.'test4,';
        }, false);

        $this->assertSame(admin_section('key3'), 'test2,test3,test4,');
    }

    public function testSort()
    {
        // 值越大排序越靠前
        admin_inject_section('key1', '4,', true, -100);
        admin_inject_section('key1', '2,', true, 2);
        admin_inject_section('key1', '1,', true, 3);
        admin_inject_section('key1', '3,', true, 1);

        $this->assertSame(admin_section('key1'), '1,2,3,4,');
    }

    public function testInjectDefaultSection()
    {
        // step1
        admin_inject_default_section('key', 'Hello');

        $this->assertSame(admin_section('key'), 'Hello');

        // step2
        admin_inject_default_section('key', function ($options) {
            return 'Hello '.$options->var1;
        });

        $this->assertSame(admin_section('key', null, ['var1' => 'world']), 'Hello world');

        // step3
        admin_inject_section('key', '');

        $this->assertSame(admin_section('key'), '');
    }
}
