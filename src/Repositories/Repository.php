<?php

namespace Dcat\Admin\Repositories;

use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Contracts\Repository as RepositoryInterface;
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
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName ?: 'id';
    }

    /**
     * @param string $keyName
     */
    public function setKeyName(?string $keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return 'created_at';
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return 'updated_at';
    }

    /**
     * @return bool
     */
    public function isSoftDeletes()
    {
        return $this->isSoftDeletes;
    }

    /**
     * @param bool $isSoftDeletes
     */
    public function setIsSoftDeletes(?bool $isSoftDeletes)
    {
        $this->isSoftDeletes = $isSoftDeletes;
    }

    /**
     * Get the grid data.
     *
     * @param Grid\Model $model
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection|array
     */
    public function get(Grid\Model $model)
    {
        throw new RuntimeException('This repository does not support "get" method.');
    }

    /**
     * Get data to build edit form.
     *
     * @param Form $form
     *
     * @return array
     */
    public function edit(Form $form): array
    {
        throw new RuntimeException('This repository does not support "edit" method.');
    }

    /**
     * Get detail data.
     *
     * @param Show $show
     *
     * @return array
     */
    public function detail(Show $show): array
    {
        throw new RuntimeException('This repository does not support "detail" method.');
    }

    /**
     * Store a new record.
     *
     * @param Form $form
     *
     * @return mixed
     */
    public function store(Form $form)
    {
        throw new RuntimeException('This repository does not support "store" method.');
    }

    /**
     * Get data before update.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getDataWhenUpdating(Form $form): array
    {
        throw new RuntimeException('This repository does not support "getDataWhenUpdating" method.');
    }

    /**
     * Update form data.
     *
     * @param Form $form
     *
     * @return bool
     */
    public function update(Form $form)
    {
        throw new RuntimeException('This repository does not support "update" method.');
    }

    /**
     * Destroy data.
     *
     * @param Form  $form
     * @param array $deletingData
     *
     * @return mixed
     */
    public function destroy(Form $form, array $deletingData)
    {
        throw new RuntimeException('This repository does not support "destroy" method.');
    }

    /**
     * Get data before destroy.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getDataWhenDeleting(Form $form): array
    {
        throw new RuntimeException('This repository does not support "getDataWhenDeleting" method.');
    }

    /**
     * @return string
     */
    public function getPrimaryKeyColumn()
    {
        return $this->getKeyName();
    }

    /**
     * @return string
     */
    public function getParentColumn()
    {
        return 'parent_id';
    }

    /**
     * Get title column.
     *
     * @return string
     */
    public function getTitleColumn()
    {
        return 'title';
    }

    /**
     * Get order column name.
     *
     * @return string
     */
    public function getOrderColumn()
    {
        return 'order';
    }

    /**
     * Save tree order from a tree like array.
     *
     * @param array $tree
     * @param int   $parentId
     */
    public function saveOrder($tree = [], $parentId = 0)
    {
        throw new RuntimeException('This repository does not support "saveOrder" method.');
    }

    /**
     * Set query callback to model.
     *
     * @param \Closure|null $query
     *
     * @return $this
     */
    public function withQuery($queryCallback)
    {
        throw new RuntimeException('This repository does not support "withQuery" method.');
    }

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree()
    {
        throw new RuntimeException('This repository does not support "toTree" method.');
    }

    /**
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * Register listeners.
     *
     * @param array|string $repositories
     * @param array|string $listeners
     */
    public static function listen($repositories, $listeners)
    {
        $storage = app('admin.context');

        $array = $storage->get('repository.listeners') ?: [];

        foreach ((array) $repositories as $v) {
            if (! isset($array[$v])) {
                $array[$v] = [];
            }

            $array[$v] = array_merge($array[$v], (array) $listeners);
        }

        $storage['repository.listeners'] = $array;
    }

    /**
     * Get the repository listeners.
     *
     * @param null|string $repository
     *
     * @return RepositoryListener[]
     */
    public static function getListeners(?string $repository)
    {
        if (! $repository) {
            return null;
        }

        $any = $repository !== '*' ? static::getListeners('*') : [];

        $storage = app('admin.context');

        $listeners = $storage->get('repository.listeners') ?: [];
        $resolves = $storage->get('repository.listeners.resolves') ?: [];

        if (isset($resolves[$repository])) {
            return array_merge($resolves[$repository], $any);
        }

        $resolves[$repository] = [];

        if (! isset($listeners[$repository])) {
            return $any;
        }

        foreach ($listeners[$repository] as $class) {
            if (! class_exists($class)) {
                continue;
            }
            $listener = new $class();

            if (! $listener instanceof RepositoryListener) {
                continue;
            }

            $resolves[$repository][] = $listener;
        }

        $storage['repository.listeners.resolves'] = $resolves;

        return array_merge($resolves[$repository], $any);
    }
}
