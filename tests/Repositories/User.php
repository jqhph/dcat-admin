<?php

namespace Dcat\Admin\Tests\Repositories;

use Dcat\Admin\Tests\Models\User as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class User extends EloquentRepository
{
    protected $eloquentClass = Model::class;
}
