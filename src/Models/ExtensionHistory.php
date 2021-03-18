<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ExtensionHistory extends Model
{
    protected $fillable = ['name', 'type', 'version', 'detail'];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.extension_histories_table') ?: 'admin_extension_histories');
    }
}
