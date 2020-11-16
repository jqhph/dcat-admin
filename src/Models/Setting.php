<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'slug';
    public $incrementing = false;
    protected $fillable = ['slug', 'value'];

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.settings_table') ?: 'admin_settings');

        parent::__construct($attributes);
    }
}
