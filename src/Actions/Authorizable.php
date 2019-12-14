<?php

namespace Dcat\Admin\Actions;

use Dcat\Admin\Admin;

trait Authorizable
{
    /**
     * @return bool
     */
    public function passesAuthorization()
    {
        if (method_exists($this, 'authorize')) {
            return $this->authorize(Admin::user()) == true;
        }

        return true;
    }

    /**
     * @return Response
     */
    public function failedAuthorization()
    {
        return $this->response()->error(__('admin.deny'));
    }
}
