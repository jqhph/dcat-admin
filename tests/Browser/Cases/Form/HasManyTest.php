<?php

namespace Tests\Browser\Cases\Form;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\PainterCreatePage;
use Tests\Browser\Pages\PainterEditPage;
use Tests\Models\Painter;
use Tests\Models\Painting;
use Tests\TestCase;

/**
 * 一对多表单功能测试.
 *
 * @group form:has-many
 */
class HasManyTest extends TestCase
{
    /**
     * 测试新增页面.
     *
     * @throws \Throwable
     */
    public function testCreatePage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new PainterCreatePage());
        });
    }

    /**
     * 测试新增记录.
     *
     * @throws \Throwable
     */
    public function testAddNewRecord()
    {
        $this->browse(function (Browser $browser) {
            $data = $this->data();

            $browser
                ->visit(new PainterCreatePage())
                ->fill($data)
                ->submit();

            $paintings = $data['paintings'];
            unset($data['paintings']);

            $painter = Painter::query()->where($data)->first();

            $this->assertTrue($painter instanceof Painter);
            $this->assertEquals($painter->id, 1);

            // painting表数据
            $this->assertEquals(count($paintings), Painting::count());

            foreach ($paintings as $painting) {
                $painting['painter_id'] = $painter->id;

                $this->seeInDatabase((new Painting())->getTable(), $painting);
            }
        });
    }

    /**
     * 测试编辑页面.
     *
     * @throws \Throwable
     */
    public function testEditPage()
    {
        $this->browse(function (Browser $browser) {
            $data = $this->data();

            $painter = $this->save($data);

            $browser->visit(new PainterEditPage($painter));
        });
    }

    /**
     * @param  array  $data
     * @return Painter
     */
    protected function save($data)
    {
        $painter = new Painter();
        $painter->fill($data)->save();

        foreach ($data['paintings'] as $painting) {
            $painting['painter_id'] = $painter->getKey();

            $painter->paintings()->insert($painting);
        }

        return $painter;
    }

    /**
     * @return array
     */
    protected function data()
    {
        return [
            'username' => 'uuu',
            'bio'      => 'bxbxbxbxbxbx',
            'paintings' => [
                [
                    'title' => '蒙娜丽莎',
                    'body' => '(*￣︶￣)',
                    'completed_at' => now(),
                ],
                [
                    'title' => '鸡蛋',
                    'body' => '(*￣︶￣)',
                    'completed_at' => now(),
                ],
            ],
        ];
    }
}
