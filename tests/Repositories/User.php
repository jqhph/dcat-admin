<?php

namespace Tests\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class User extends EloquentRepository
{
    protected $eloquentClass = \Tests\Models\User::class;
}
