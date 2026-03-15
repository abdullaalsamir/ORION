<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leadership extends Model
{
    protected $table = 'leadership';

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