<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'admin_settings';
    protected $primaryKey = 'slug';
    public $incrementing = false;
    protected $fillable = ['slug', 'value'];
}
