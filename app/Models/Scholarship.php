<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = ['name', 'degree', 'session', 'medical_college', 'image_path', 'order', 'is_active'];
}