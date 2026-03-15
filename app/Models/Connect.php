<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connect extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];
}