<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

trait HasPermissions
{
    protected $allPermissions;

    /**
     * Get all permissions of user.
     *
     * @return mixed
     */
    public function allPermissions(): Collection
    {
        if ($this->allPermissions) {
            return $this->allPermissions;
        }

        return $this->allPermissions =
            $this->roles
            ->pluck('permissions')
            ->flatten()
            ->keyBy($this->getKeyName());
    }

    /**
     * Check if user has permission.
     *
     * @param $ability
     * @return bool
     */
    public function can($ability): bool
    {
        if (! $ability) {
            return false;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        $permissions = $this->allPermissions();

        return $permissions->pluck('slug')->contains($ability) ?:
            $permissions
            ->pluck('id')
            ->contains($ability);
    }

    /**
     * Check if user has no permission.
     *
     * @param $permission
     * @return bool
     */
    public function cannot(string $permission): bool
    {
        return ! $this->can($permission);
    }

    /**
     * Check if user is administrator.
     *
     * @return mixed
     */
    public function isAdministrator(): bool
    {
        $roleModel = config('admin.database.roles_model');

        return $this->isRole($roleModel::ADMINISTRATOR);
    }

    /**
     * Check if user is $role.
     *
     * @param  string  $role
     * @return mixed
     */
    public function isRole(string $role): bool
    {
        /* @var Collection $roles */
        $roles = $this->roles;

        return $roles->pluck('slug')->contains($role) ?:
            $roles->pluck('id')->contains($role);
    }

    /**
     * Check if user in $roles.
     *
     * @param  string|array|Arrayable  $roles
     * @return mixed
     */
    public function inRoles($roles = []): bool
    {
        /* @var Collection $all */
        $all = $this->roles;

        $roles = Helper::array($roles);

        return $all->pluck('slug')->intersect($roles)->isNotEmpty() ?:
            $all->pluck('id')->intersect($roles)->isNotEmpty();
    }

    /**
     * If visible for roles.
     *
     * @param $roles
     * @return bool
     */
    public function visible($roles = []): bool
    {
        if (empty($roles)) {
            return false;
        }

        if ($this->isAdministrator()) {
            return true;
        }

        return $this->inRoles($roles);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            $model->roles()->detach();
        });
    }
}
