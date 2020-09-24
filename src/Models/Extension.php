<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $table = 'admin_extensions';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        parent::__construct($attributes);
    }
}
