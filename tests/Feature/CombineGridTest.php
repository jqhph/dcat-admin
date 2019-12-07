<?php

namespace Tests\Feature;

use Tests\TestCase;

class CombineGridTest extends TestCase
{
    public function test()
    {
        $this->visit('admin/tests/report')
            ->assertResponseStatus(200)
            ->see('报表')
            ->see('avgMonthCost')
            ->see('avgVist')
            ->see('avgCost')
            ->see('top');

        // Column::help
        $this->assertCount(1, $this->crawler()->filter('th a i[class*=fa-question-circle]'));

        $this->assertCount(2, $this->crawler()->filter('thead tr'));

        $firstTr = $this->crawler()->filter('thead tr')->first()->filter('th');

        // cost
        $this->assertEquals('2', (string) $firstTr->eq(2)->attr('rowspan'));

        // avgCost
        $this->assertEquals('3', (string) $firstTr->eq(3)->attr('colspan'));

        // avgVist
        $this->assertEquals('3', (string) $firstTr->eq(4)->attr('colspan'));

        // top
        $this->assertEquals('3', (string) $firstTr->eq(5)->attr('colspan'));
    }
}
