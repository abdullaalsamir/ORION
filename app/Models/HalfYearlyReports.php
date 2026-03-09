<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HalfYearlyReports extends Model
{
    protected $table = 'half_yearly_reports';
    protected $fillable = ['title', 'filename', 'description', 'publication_date', 'order', 'is_active'];
    protected $casts = ['publication_date' => 'date'];
}