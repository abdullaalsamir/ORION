<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateGovernance extends Model
{
    protected $table = 'corporate_governance';
    protected $fillable = ['title', 'filename', 'description', 'publication_date', 'order', 'is_active'];
    protected $casts = ['publication_date' => 'date'];
}