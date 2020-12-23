<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ExtensionHistory extends Model
{
    protected $fillable = ['name', 'type', 'version', 'detail'];

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.extension_histories_table') ?: 'admin_extension_histories');

        parent::__construct($attributes);
    }
}
