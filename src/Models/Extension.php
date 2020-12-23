<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = ['name', 'is_enabled', 'version', 'options'];

    protected $casts = [
        'options' => 'json',
    ];

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.extensions_table') ?: 'admin_extensions');

        parent::__construct($attributes);
    }
}
