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
     * @return string
     */
    public function getPrimaryKeyColumn();

    /**
     * @return string
     */
    public function getParentColumn();

    /**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn();

    /**
     * Get order column name.
     *
     * @return string
     */
    public function getOrderColumn();

    /**
     * Save tree order from a tree like array.
     *
     * @param array $tree
     * @param int   $parentId
     */
    public function saveOrder($tree = [], $parentId = 0);

    /**
     * Set query callback to model.
     *
     * @param \Closure|null $query
     *
     * @return $this
     */
    public function withQuery($queryCallback);

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree();
}
