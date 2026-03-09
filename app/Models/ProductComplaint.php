<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComplaint extends Model
{
    protected $guarded = [];
    protected $casts = [
        'complaint_date' => 'date',
        'mfg_date' => 'date',
        'exp_date' => 'date',
    ];
}