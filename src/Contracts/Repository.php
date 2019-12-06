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
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn();

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn();

    /**
     * @return bool
     */
    public function isSoftDeletes();

    /**
     * Get the grid data.
     *
     * @param Grid\Model $model
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection|array
     */
    public function get(Grid\Model $model);

    /**
     * Get data to build edit form.
     *
     * @param Form $form
     *
     * @return array
     */
    public function edit(Form $form): array;

    /**
     * Get detail data.
     *
     * @param Show $show
     *
     * @return array
     */
    public function detail(Show $show): array;

    /**
     * Store a new record.
     *
     * @param Form $form
     *
     * @return mixed
     */
    public function store(Form $form);

    /**
     * Get data before update.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getDataWhenUpdating(Form $form): array;

    /**
     * Update form data.
     *
     * @param Form $form
     *
     * @return bool
     */
    public function update(Form $form);

    /**
     * Destroy data.
     *
     * @param Form  $form
     * @param array $deletingData
     *
     * @return mixed
     */
    public function destroy(Form $form, array $deletingData);

    /**
     * Get data before destroy.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getDataWhenDeleting(Form $form): array;
}
