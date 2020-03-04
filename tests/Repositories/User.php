<?php

namespace Dcat\Admin\Tests\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\Models\User as Model;

class User extends EloquentRepository
{
    protected $eloquentClass = Model::class;
}
