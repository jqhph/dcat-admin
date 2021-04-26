<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasDateTimeFormatter;

    const ADMINISTRATOR = 'administrator';

    const ADMINISTRATOR_ID = 1;

    protected $fillable = ['name', 'slug'];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.roles_table'));
    }

    /**
     * A role belongs to many users.
     *
     * @return BelongsToMany
     */
    public function administrators(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.users_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'user_id');
    }

    /**
     * A role belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'permission_id')->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function menus(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_menu_table');

        $relatedModel = config('admin.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'menu_id')->withTimestamps();
    }

    /**
     * Check user has permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function can(?string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function cannot(?string $permission): bool
    {
        return ! $this->can($permission);
    }

    /**
     * Get id of the permission by id.
     *
     * @param array $roleIds
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPermissionId(array $roleIds)
    {
        if (! $roleIds) {
            return collect();
        }
        $related = config('admin.database.role_permissions_table');

        $model = new static();
        $keyName = $model->getKeyName();

        return $model->newQuery()
            ->leftJoin($related, $keyName, '=', 'role_id')
            ->whereIn($keyName, $roleIds)
            ->get(['permission_id', 'role_id'])
            ->groupBy('role_id')
            ->map(function ($v) {
                $v = $v instanceof Arrayable ? $v->toArray() : $v;

                return array_column($v, 'permission_id');
            });
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public static function isAdministrator(?string $slug)
    {
        return $slug === static::ADMINISTRATOR;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->administrators()->detach();

            $model->permissions()->detach();
        });
    }
}
