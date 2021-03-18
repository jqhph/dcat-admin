<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Events\Fetched;
use Dcat\Admin\Grid\Events\Fetching;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Collection;

/**
 * Trait HasTree.
 *
 *
 * @method \Dcat\Admin\Grid grid()
 */
trait HasTree
{
    /**
     * @var string
     */
    protected $parentIdQueryName = '_parent_id_';

    /**
     * @var string
     */
    protected $depthQueryName = '_depth_';

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
     * @var mixed
     */
    protected $defaultParentId;

    /**
     * 开启树形表格功能.
     *
     * @param bool $showAll
     * @param bool $sortable
     * @param mixed $defaultParentId
     *
     * @return void
     */
    public function enableTree(bool $showAll, bool $sortable, $defaultParentId = null)
    {
        $this->showAllChildrenNodes = $showAll;
        $this->defaultParentId = $defaultParentId;

        $this->grid()->listen(Fetching::class, function () use ($sortable) {
            $this->sortTree($sortable);
            $this->bindChildrenNodesQuery();

            if ($this->getParentIdFromRequest()) {
                $this->setPageName(
                    $this->getChildrenPageName($this->getParentIdFromRequest())
                );
            }

            $this->addIgnoreQueries();
        });

        $this->grid()->listen(Fetched::class, function ($grid, Collection $collection) {
            if (! $this->getParentIdFromRequest()) {
                return;
            }

            if ($collection->isEmpty()) {
                return $grid->show(false);
            }

            $this->buildChildrenNodesPagination();
        });
    }

    /**
     * 设置保存为"前一个页面地址"时需要忽略的参数.
     */
    protected function addIgnoreQueries()
    {
        Admin::addIgnoreQueryName([
            $this->getParentIdQueryName(),
            $this->getDepthQueryName(),
            $this->getChildrenPageName($this->getParentIdFromRequest()),
        ]);
    }

    /**
     * 禁止树形表格查询.
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
                && $query['arguments'][0] === optional($this->repository)->getParentColumn()
            ) {
                return false;
            }

            return true;
        });
    }

    /**
     * 设置子节点查询链接需要忽略的字段.
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
            $this->grid()->filter()->urlWithoutFilters(),
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
            && $this->findQueryByMethod('orderBy')->isEmpty()
            && $this->findQueryByMethod('orderByDesc')->isEmpty()
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
        return $this->getChildrenQueryNamePrefix().'_children_page_'.$parentId;
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
        ) ?: $this->getDefaultParentId();
    }

    /**
     * 移除树相关参数.
     *
     * @param string $url
     *
     * @return string
     */
    public function withoutTreeQuery($url)
    {
        if (! $url) {
            return $url;
        }

        parse_str(explode('?', $url)[1] ?? '', $originalQuery);

        $parentId = $originalQuery[$this->getParentIdQueryName()] ?? 0;

        if (! $parentId) {
            return $url;
        }

        return Helper::urlWithoutQuery($url, [
            $this->getParentIdQueryName(),
            $this->getChildrenPageName($parentId),
            $this->getDepthQueryName(),
        ]);
    }

    /**
     * 获取默认parent_id字段值.
     *
     * @return int|mixed
     */
    public function getDefaultParentId()
    {
        if ($this->defaultParentId !== null) {
            return $this->defaultParentId;
        }

        $repository = $this->grid->model()->repository();

        if ($repository instanceof EloquentRepository) {
            return $repository->model()->getDefaultParentId();
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getDepthQueryName()
    {
        return $this->getChildrenQueryNamePrefix().$this->depthQueryName;
    }

    /**
     * @return int
     */
    public function getDepthFromRequest()
    {
        return $this->request->get(
            $this->getDepthQueryName()
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
