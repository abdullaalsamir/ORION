<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuarterlyReports extends Model
{
    protected $table = 'quarterly_reports';
    protected $fillable = ['title', 'filename', 'description', 'publication_date', 'order', 'is_active'];
    protected $casts = ['publication_date' => 'date'];
}