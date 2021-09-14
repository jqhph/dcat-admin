<?php

namespace Dcat\Admin\Repositories;

use Dcat\Admin\Contracts\Repository as RepositoryInterface;
use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

abstract class Repository implements RepositoryInterface, TreeRepository
{
    use Macroable;

    /**
     * @var string
     */
    protected $keyName = 'id';

    /**
     * @var bool
     */
    protected $isSoftDeletes = false;

    /**
     * 获取主键名称.
     *
     * @return string|array
     */
    public function getKeyName()
    {
        return $this->keyName ?: 'id';
    }

    /**
     * 设置主键名称.
     *
     * @param  string|array  $keyName
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * 获取创建时间字段.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return 'created_at';
    }

    /**
     * 获取更新时间字段.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return 'updated_at';
    }

    /**
     * 是否使用软删除.
     *
     * @return bool
     */
    public function isSoftDeletes()
    {
        return $this->isSoftDeletes;
    }

    /**
     * @param  bool  $isSoftDeletes
     */
    public function setIsSoftDeletes(?bool $isSoftDeletes)
    {
        $this->isSoftDeletes = $isSoftDeletes;
    }

    /**
     * 获取Grid表格数据.
     *
     * @param  Grid\Model  $model
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection|array
     */
    public function get(Grid\Model $model)
    {
        throw new RuntimeException('This repository does not support "get" method.');
    }

    /**
     * 获取编辑页面数据.
     *
     * @param  Form  $form
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function edit(Form $form)
    {
        throw new RuntimeException('This repository does not support "edit" method.');
    }

    /**
     * 获取详情页面数据.
     *
     * @param  Show  $show
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function detail(Show $show)
    {
        throw new RuntimeException('This repository does not support "detail" method.');
    }

    /**
     * 新增记录.
     *
     * @param  Form  $form
     * @return int|bool|\Dcat\Admin\Http\JsonResponse
     */
    public function store(Form $form)
    {
        throw new RuntimeException('This repository does not support "store" method.');
    }

    /**
     * 查询更新前的行数据.
     *
     * @param  Form  $form
     * @return array|\Illuminate\Contracts\Support\Arrayable
     */
    public function updating(Form $form)
    {
        throw new RuntimeException('This repository does not support "updating" method.');
    }

    /**
     * 更新数据.
     *
     * @param  Form  $form
     * @return bool|\Dcat\Admin\Http\JsonResponse
     */
    public function update(Form $form)
    {
        throw new RuntimeException('This repository does not support "update" method.');
    }

    /**
     * 删除数据.
     *
     * @param  Form  $form
     * @param  array  $deletingData
     * @return bool|int|\Dcat\Admin\Http\JsonResponse
     */
    public function delete(Form $form, array $deletingData)
    {
        throw new RuntimeException('This repository does not support "destroy" method.');
    }

    /**
     * 查询删除前的行数据.
     *
     * @param  Form  $form
     * @return array
     */
    public function deleting(Form $form)
    {
        throw new RuntimeException('This repository does not support "deleting" method.');
    }

    /**
     * 获取主键字段名称.
     *
     * @return string
     */
    public function getPrimaryKeyColumn()
    {
        return $this->getKeyName();
    }

    /**
     *  获取父级ID字段名称.
     *
     * @return string
     */
    public function getParentColumn()
    {
        return 'parent_id';
    }

    /**
     * 获取标题字段名称.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        return 'title';
    }

    /**
     * 获取排序字段名称.
     *
     * @return string
     */
    public function getOrderColumn()
    {
        return 'order';
    }

    /**
     * 保存层级数据排序.
     *
     * @param  array  $tree
     * @param  int  $parentId
     */
    public function saveOrder($tree = [], $parentId = 0)
    {
        throw new RuntimeException('This repository does not support "saveOrder" method.');
    }

    /**
     * 设置数据查询回调.
     *
     * @param  \Closure|null  $query
     * @return $this
     */
    public function withQuery($queryCallback)
    {
        throw new RuntimeException('This repository does not support "withQuery" method.');
    }

    /**
     * 获取层级数据.
     *
     * @return array
     */
    public function toTree()
    {
        throw new RuntimeException('This repository does not support "toTree" method.');
    }

    /**
     * @param  mixed  ...$params
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }
}
