<?php

namespace Dcat\Admin\Repositories;

use Dcat\Admin\Form;
use Dcat\Admin\Show;
use Dcat\Admin\Grid;

class Proxy implements \Dcat\Admin\Contracts\Repository
{
    protected $repository;

    protected $listeners = [];

    protected $caches = [];

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;

        $this->listeners = Repository::getListeners(get_class($repository));
    }

    public function getOriginalClassName()
    {
        return get_class($this->repository);
    }

    public function getKeyName()
    {
        return $this->repository->getKeyName();
    }

    public function isSoftDeletes()
    {
        return $this->repository->isSoftDeletes();
    }

    public function getCreatedAtColumn()
    {
        return $this->repository->getCreatedAtColumn();
    }

    public function getUpdatedAtColumn()
    {
        return $this->repository->getUpdatedAtColumn();
    }

    public function get(Grid\Model $model)
    {
        if (isset($this->caches['all'])) {
            return $this->caches['all'];
        }

        return $this->caches['all'] = $this->repository->get($model);
    }

    public function edit(Form $form): array
    {
        if (isset($this->caches['edit'])) {
            return $this->caches['edit'];
        }

        return $this->caches['edit'] = $this->repository->edit($form);
    }

    public function detail(Show $show): array
    {
        if (isset($this->caches['detail'])) {
            return $this->caches['detail'];
        }

        return $this->caches['detail'] = $this->repository->detail($show);
    }

    public function store(Form $form)
    {
        foreach ($this->listeners as $listener) {
            $listener->creating($form);
        }

        $newId = $this->repository->store($form);

        foreach ($this->listeners as $listener) {
            $listener->created($form, $newId);
        }

        return $newId;
    }

    public function getDataWhenUpdating(Form $form): array
    {
        if (isset($this->caches['updating'])) {
            return $this->caches['updating'];
        }

        return $this->caches['updating'] = $this->repository->getDataWhenUpdating($form);
    }

    public function update(Form $form)
    {
        $editAttributes = $this->caches['edit'] ?? [];

        foreach ($this->listeners as $listener) {
            $listener->updating($form, $editAttributes);
        }

        $result = $this->repository->update($form);

        foreach ($this->listeners as $listener) {
            $listener->updated($form, $editAttributes, $result);
        }

        return $result;
    }

    public function destroy(Form $form, array $deletingData)
    {
        foreach ($this->listeners as $listener) {
            $listener->deleting($form, $deletingData);
        }

        $result = $this->repository->destroy($form, $deletingData);

        foreach ($this->listeners as $listener) {
            $listener->deleted($form, $deletingData, $result);
        }

        return $result;
    }

    public function getDataWhenDeleting(Form $form): array
    {
        return $this->repository->getDataWhenDeleting($form);
    }

    public function __call($method, $arguments)
    {
        return $this->repository->$method(...$arguments);
    }
}
