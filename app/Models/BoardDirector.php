<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardDirector extends Model
{
    protected $table = 'board_of_directors';

    protected $fillable = [
        'name',
        'slug',
        'designation',
        'description',
        'image_path',
        'order',
        'is_active'
    ];
}