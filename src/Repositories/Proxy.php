<?php

namespace Dcat\Admin\Repositories;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class Proxy implements \Dcat\Admin\Contracts\Repository
{
    protected $repository;

    protected $__listeners = [];

    protected $__caches = [
        'edit'     => [],
        'detail'   => [],
        'updating' => [],
    ];

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;

        $this->__listeners = Repository::getListeners(get_class($repository));
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
        return $this->repository->get($model);
    }

    public function edit(Form $form): array
    {
        $id = $form->key();

        if (array_key_exists($id, $this->__caches['edit'])) {
            return $this->__caches['edit'][$id];
        }

        return $this->__caches['edit'][$id] = $this->repository->edit($form);
    }

    public function detail(Show $show): array
    {
        $id = $show->key();

        if (array_key_exists($id, $this->__caches['detail'])) {
            return $this->__caches['detail'][$id];
        }

        return $this->__caches['detail'][$id] = $this->repository->detail($show);
    }

    public function store(Form $form)
    {
        foreach ($this->__listeners as $listener) {
            $listener->creating($form);
        }

        $newId = $this->repository->store($form);

        foreach ($this->__listeners as $listener) {
            $listener->created($form, $newId);
        }

        return $newId;
    }

    public function getDataWhenUpdating(Form $form): array
    {
        $id = $form->key();

        if (array_key_exists($id, $this->__caches['updating'])) {
            return $this->__caches['updating'][$id];
        }

        return $this->__caches['updating'][$id] = $this->repository->getDataWhenUpdating($form);
    }

    public function update(Form $form)
    {
        $editAttributes = $this->__caches['edit'] ?? [];

        foreach ($this->__listeners as $listener) {
            $listener->updating($form, $editAttributes);
        }

        $result = $this->repository->update($form);

        foreach ($this->__listeners as $listener) {
            $listener->updated($form, $editAttributes, $result);
        }

        return $result;
    }

    public function destroy(Form $form, array $deletingData)
    {
        foreach ($this->__listeners as $listener) {
            $listener->deleting($form, $deletingData);
        }

        $result = $this->repository->destroy($form, $deletingData);

        foreach ($this->__listeners as $listener) {
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

    public function __get($name)
    {
        return $this->repository->$name;
    }

    public function __set($name, $value)
    {
        $this->repository->$name = $value;
    }
}
