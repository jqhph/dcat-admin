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

interface TreeRepository
{
    /**
     * 获取主键字段名称.
     *
     * @return string
     */
    public function getPrimaryKeyColumn();

    /**
     * 获取父级ID字段名称.
     *
     * @return string
     */
    public function getParentColumn();

    /**
     * 获取标题字段名称.
     *
     * @return string
     */
    public function getTitleColumn();

    /**
     * 获取排序字段名称.
     *
     * @return string
     */
    public function getOrderColumn();

    /**
     * 保存层级数据排序.
     *
     * @param  array  $tree
     * @param  int  $parentId
     */
    public function saveOrder($tree = [], $parentId = 0);

    /**
     * 设置数据查询回调.
     *
     * @param  \Closure|null  $query
     * @return $this
     */
    public function withQuery($queryCallback);

    /**
     * 获取层级数据.
     *
     * @return array
     */
    public function toTree();
}
