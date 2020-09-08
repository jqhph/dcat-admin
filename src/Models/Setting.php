<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['slug', 'value'];
}
