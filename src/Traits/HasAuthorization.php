<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

trait HasAuthorization
{
    /**
     * 验证权限.
     *
     * @return bool
     */
    public function passesAuthorization(): bool
    {
        return $this->authorize(Admin::user());
    }

    /**
     * 是否有权限判断.
     *
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * 返回无权限信息.
     *
     * @return mixed
     */
    public function failedAuthorization()
    {
        abort(403, __('admin.deny'));
    }
}
