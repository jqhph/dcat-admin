<?php

namespace Dcat\Admin\Models\Repositories;

use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\EloquentRepository;
use Illuminate\Pagination\AbstractPaginator;

class Administrator extends EloquentRepository
{
    public function __construct($relations = [])
    {
        $this->eloquentClass = config('admin.database.users_model');

        parent::__construct($relations);
    }

    public function get(Grid\Model $model)
    {
        $model = parent::get($model);

        $isPaginator = $model instanceof AbstractPaginator;

        $items = collect($isPaginator ? $model->items() : $model)->toArray();

        if (! $items) {
            return $model;
        }

        $roleModel = config('admin.database.roles_model');

        $items = collect($items);

        $roleKeyName = (new $roleModel())->getKeyName();

        $roleIds = $items
            ->pluck('roles')
            ->flatten(1)
            ->keyBy($roleKeyName)
            ->keys()
            ->toArray();

        $permissions = $roleModel::getPermissionId($roleIds);

        if (! $permissions->isEmpty()) {
            $items = $items->map(function ($v) use ($roleKeyName, $permissions) {
                $v['permissions'] = [];

                foreach (array_column($v['roles'], $roleKeyName) as $roleId) {
                    $v['permissions'] = array_merge($v['permissions'], $permissions->get($roleId, []));
                }

                $v['permissions'] = array_unique($v['permissions']);

                return $v;
            });
        }

        if ($isPaginator) {
            $model->setCollection($items);

            return $model;
        }

        return $items;
    }
}
