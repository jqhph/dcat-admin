<?php

namespace Tests\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Tests\Models\User as Model;

class User extends EloquentRepository
{
    protected $eloquentClass = Model::class;
}
