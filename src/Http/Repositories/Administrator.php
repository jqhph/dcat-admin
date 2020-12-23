<?php

namespace Dcat\Admin\Http\Repositories;

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
        $results = parent::get($model);

        $isPaginator = $results instanceof AbstractPaginator;

        $items = $isPaginator ? $results->getCollection() : $results;
        $items = is_array($items) ? collect($items) : $items;

        if ($items->isEmpty()) {
            return $results;
        }

        $roleModel = config('admin.database.roles_model');

        $roleKeyName = (new $roleModel())->getKeyName();

        $roleIds = $items
            ->pluck('roles')
            ->flatten(1)
            ->pluck($roleKeyName)
            ->toArray();

        $permissions = $roleModel::getPermissionId($roleIds);

        if (! $permissions->isEmpty()) {
            $items = $items->map(function ($v) use ($roleKeyName, $permissions) {
                $v['permissions'] = [];

                foreach ($v['roles']->pluck($roleKeyName) as $roleId) {
                    $v['permissions'] = array_merge($v['permissions'], $permissions->get($roleId, []));
                }

                $v['permissions'] = array_unique($v['permissions']);

                return $v;
            });
        }

        if ($isPaginator) {
            $results->setCollection($items);

            return $results;
        }

        return $items;
    }
}
