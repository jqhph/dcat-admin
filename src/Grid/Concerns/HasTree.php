<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Collection;

trait HasTree
{
    /**
     * @var string
     */
    protected $parentIdQueryName = '__parent_id__';

    /**
     * @var string
     */
    protected $levelQueryName = '__level__';

    /**
     * @var bool
     */
    protected $showAllChildrenNodes = false;

    /**
     * @var bool
     */
    protected $allowedTreeQuery = true;

    /**
     * @var array
     */
    protected $treeIgnoreQueryNames = [];

    /**
     * 开启树形表格功能
     *
     * @param bool $showAll
     * @param bool $sortable
     *
     * @return void
     */
    public function enableTree(bool $showAll, bool $sortable)
    {
        $this->showAllChildrenNodes = $showAll;

        $this->grid->fetching(function () use ($sortable) {
            $this->sortTree($sortable);
            $this->bindChildrenNodesQuery();

            if ($this->getParentIdFromRequest()) {
                $this->setPageName(
                    $this->getChildrenPageName($this->getParentIdFromRequest())
                );
            }
        });

        $this->collection(function (Collection $collection) {
            if (! $this->getParentIdFromRequest()) {
                return $collection;
            }

            if ($collection->isEmpty()) {
                abort(404);
            }

            $this->buildChildrenNodesPagination();

            return $collection;
        });
    }

    /**
     * 禁止树形表格查询
     *
     * @return $this
     */
    public function disableBindTreeQuery()
    {
        $this->allowedTreeQuery = false;

        return $this->filterQueryBy(function ($query) {
            if (
                $query['method'] === 'where'
                && $query['arguments']
                && $query['arguments'][0] === $this->repository->getParentColumn()
            ) {
                return false;
            }

            return true;
        });
    }

    /**
     * 设置子节点查询链接需要忽略的字段
     *
     * @param string|array $keys
     *
     * @return $this
     */
    public function treeUrlWithoutQuery($keys)
    {
        $this->treeIgnoreQueryNames = array_merge(
            $this->treeIgnoreQueryNames,
            (array) $keys
        );

        return $this;
    }

    public function generateTreeUrl()
    {
        return Helper::urlWithoutQuery(
            $this->grid->filter()->urlWithoutFilters(),
            $this->treeIgnoreQueryNames
        );
    }

    protected function buildChildrenNodesPagination()
    {
        if ($this->grid()->allowPagination()) {
            $nextPage = $this->getCurrentChildrenPage() + 1;

            Admin::html(
                <<<HTML
<next-page class="hidden">{$nextPage}</next-page>
<last-page class="hidden">{$this->paginator()->lastPage()}</last-page>
HTML
            );
        }
    }

    protected function sortTree(bool $sortable)
    {
        if (
            $sortable
            && ! $this->findQueryByMethod('orderBy')
            && ! $this->findQueryByMethod('orderByDesc')
            && ($orderColumn = $this->repository->getOrderColumn())
        ) {
            $this->orderBy($orderColumn)
                ->orderBy($this->repository->getKeyName());
        }
    }

    protected function bindChildrenNodesQuery()
    {
        if (! $this->allowedTreeQuery) {
            return;
        }

        $this->where($this->repository->getParentColumn(), $this->getParentIdFromRequest());
    }

    /**
     * @return mixed
     */
    public function getChildrenQueryNamePrefix()
    {
        return $this->grid->getName();
    }

    /**
     * @param mixed $parentId
     *
     * @return string
     */
    public function getChildrenPageName($parentId)
    {
        return $this->getChildrenQueryNamePrefix().'children_page_'.$parentId;
    }

    /**
     * @return int
     */
    public function getCurrentChildrenPage()
    {
        return $this->request->get(
            $this->getChildrenPageName(
                $this->getParentIdFromRequest()
            )
        ) ?: 1;
    }

    /**
     * @return string
     */
    public function getParentIdQueryName()
    {
        return $this->getChildrenQueryNamePrefix().$this->parentIdQueryName;
    }

    /**
     * @return int
     */
    public function getParentIdFromRequest()
    {
        return $this->request->get(
            $this->getParentIdQueryName()
        ) ?: 0;
    }

    /**
     * @return string
     */
    public function getLevelQueryName()
    {
        return $this->getChildrenQueryNamePrefix().$this->levelQueryName;
    }


    /**
     * @return int
     */
    public function getLevelFromRequest()
    {
        return $this->request->get(
            $this->getLevelQueryName()
        ) ?: 0;
    }

    /**
     * @return bool
     */
    public function showAllChildrenNodes()
    {
        return $this->showAllChildrenNodes;
    }
}
