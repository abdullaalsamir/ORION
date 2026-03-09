<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalJournal extends Model
{
    protected $fillable = ['title', 'filename', 'year', 'order', 'is_active'];
}