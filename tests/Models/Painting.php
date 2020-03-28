<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Painting extends Model
{
    protected $table = 'test_paintings';

    protected $fillable = ['title', 'body', 'completed_at'];

    public function painter()
    {
        return $this->belongsTo(Painter::class, 'painter_id');
    }
}
