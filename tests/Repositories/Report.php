<?php

namespace Tests\Repositories;

use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Faker\Factory;
use Illuminate\Pagination\LengthAwarePaginator;

class Report extends Repository
{
    public function get(Grid\Model $model)
    {
        $items = $this->fetch();

        $paginator = new LengthAwarePaginator(
            $items,
            1000,
            $model->getPerPage(), // 传入每页显示行数
            $model->getCurrentPage() // 传入当前页码
        );

        // 必须设置链接
        $paginator->setPath(\url()->current());

        return $paginator;
    }

    /**
     * 这里生成假数据演示报表功能.
     *
     * @return array
     */
    public function fetch()
    {
        $faker = Factory::create();

        $data = [];

        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'content'        => $faker->text,
                'cost'           => $faker->randomFloat(),
                'avgMonthCost'   => $faker->randomFloat(),
                'avgQuarterCost' => $faker->randomFloat(),
                'avgYearCost'    => $faker->randomFloat(),
                'incrs'          => $faker->numberBetween(1, 999999999),
                'avgMonthVist'   => $faker->numberBetween(1, 999999),
                'avgQuarterVist' => $faker->numberBetween(1, 999999),
                'avgYearVist'    => $faker->numberBetween(1, 999999),
                'avgVists'       => $faker->numberBetween(1, 999999),
                'topCost'        => $faker->numberBetween(1, 999999999),
                'topVist'        => $faker->numberBetween(1, 9999990009),
                'topIncr'        => $faker->numberBetween(1, 99999999),
                'date'           => $faker->date(),
            ];
        }

        return $data;
    }
}
