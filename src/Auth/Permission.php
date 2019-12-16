<?php

namespace Dcat\Admin\Auth;

use Dcat\Admin\Admin;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Middleware\Pjax;
use Dcat\Admin\Models\Role;
use Illuminate\Contracts\Support\Arrayable;

class Permission
{
    /**
     * Check permission.
     *
     * @param string|array|Arrayable $permission
     *
     * @return true|void
     */
    public static function check($permission)
    {
        if (static::isAdministrator()) {
            return true;
        }

        if (is_array($permission) || $permission instanceof Arrayable) {
            collect($permission)->each(function ($permission) {
                static::check($permission);
            });

            return true;
        }

        if (Admin::user()->cannot($permission)) {
            static::error();
        }
    }

    /**
     * Roles allowed to access.
     *
     * @param string|array|Arrayable $roles
     *
     * @return true|void
     */
    public static function allow($roles)
    {
        if (static::isAdministrator()) {
            return true;
        }

        if (! Admin::user()->inRoles($roles)) {
            static::error();
        }
    }

    /**
     * Don't check permission.
     *
     * @return bool
     */
    public static function free()
    {
        return true;
    }

    /**
     * Roles denied to access.
     *
     * @param string|array|Arrayable $roles
     *
     * @return true|void
     */
    public static function deny($roles)
    {
        if (static::isAdministrator()) {
            return true;
        }

        if (Admin::user()->inRoles($roles)) {
            static::error();
        }
    }

    /**
     * Send error response page.
     */
    public static function error()
    {
        if (! request()->pjax() && request()->ajax()) {
            abort(403, trans('admin.deny'));
        }

        Pjax::respond(
            response((new Content())->withError(trans('admin.deny')))
        );
    }

    /**
     * If current user is administrator.
     *
     * @return mixed
     */
    public static function isAdministrator()
    {
        return Admin::user()->isRole(Role::ADMINISTRATOR);
    }
}
