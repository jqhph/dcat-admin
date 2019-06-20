<?php

namespace Dcat\Admin\Models\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class Menu extends EloquentRepository
{
    public function __construct()
    {
        $this->eloquentClass = config('admin.database.menu_model');

        parent::__construct();
    }
}
