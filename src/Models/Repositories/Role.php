<?php

namespace Dcat\Admin\Models\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class Role extends EloquentRepository
{
    public function __construct()
    {
        $this->eloquentClass = config('admin.database.roles_model');

        parent::__construct();
    }
}
