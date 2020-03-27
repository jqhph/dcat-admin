<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Painter extends Model
{
    protected $table = 'demo_painters';

    public function paintings()
    {
        return $this->hasMany(Painting::class, 'painter_id');
    }
}
