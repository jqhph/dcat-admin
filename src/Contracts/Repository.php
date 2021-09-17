<?php
/*
 * This file is part of the dcat-admin.
 *
 * (c) jqh <841324345@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Dcat\Admin\Contracts;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Support\Collection;

interface Repository
{
    /**
     * 获取主键名称.
     *
     * @return string|array
     */
    public function getKeyName();

    /**
     * 获取创建时间字段.
     *
     * @return string
     */
    public function getCreatedAtColumn();

    /**
     * 获取更新时间字段.
     *
     * @return string
     */
    public function getUpdatedAtColumn();

    /**
     * 是否使用软删除.
     *
     * @return bool
     */
    public function isSoftDeletes();

    /**
     * 获取Grid表格数据.
     *
     * @param  Grid\Model  $model
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection|array
     */
    public function get(Grid\Model $model);

    /**
     * 获取编辑页面数据.
     *
     * @param  Form  $form
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function edit(Form $form);

    /**
     * 获取详情页面数据.
     *
     * @param  Show  $show
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function detail(Show $show);

    /**
     * 新增记录.
     *
     * @param  Form  $form
     * @return int|bool|\Dcat\Admin\Http\JsonResponse
     */
    public function store(Form $form);

    /**
     * 查询更新前的行数据.
     *
     * @param  Form  $form
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function updating(Form $form);

    /**
     * 更新数据.
     *
     * @param  Form  $form
     * @return bool|\Dcat\Admin\Http\JsonResponse
     */
    public function update(Form $form);

    /**
     * 删除数据.
     *
     * @param  Form  $form
     * @param  array  $deletingData
     * @return mixed|\Dcat\Admin\Http\JsonResponse
     */
    public function delete(Form $form, array $deletingData);

    /**
     * 查询删除前的行数据.
     *
     * @param  Form  $form
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function deleting(Form $form);
}
